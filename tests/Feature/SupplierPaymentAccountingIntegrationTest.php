<?php

namespace Tests\Feature;

use App\Enums\PaymentMethodEnum;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Supplier;
use App\Models\SupplierCredit;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Services\SupplierPaymentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierPaymentAccountingIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Supplier $supplier;

    protected SupplierCredit $credit;

    protected SupplierPaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        // Verify required accounts exist
        $this->assertDatabaseHas('accounts', ['account_code' => '1110']); // Cash in Hand
        $this->assertDatabaseHas('accounts', ['account_code' => '1120']); // Cash in Bank
        $this->assertDatabaseHas('accounts', ['account_code' => '2100']); // Accounts Payable

        // Use REAL seeded user - John Cashier
        $this->user = User::where('name', 'John Cashier')->firstOrFail();

        // Create a supplier
        $this->supplier = Supplier::factory()->create();

        // Create a supplier credit to pay against
        $this->credit = SupplierCredit::factory()->create([
            'supplier_id' => $this->supplier->id,
            'original_amount' => 1000.00,
            'paid_amount' => 0,
            'outstanding_amount' => 1000.00,
            'status' => 'pending',
        ]);

        // Get SupplierPaymentService instance
        $this->paymentService = app(SupplierPaymentService::class);
    }

    #[Test]
    public function it_creates_correct_journal_entry_for_cash_payment(): void
    {
        // Arrange
        $this->actingAs($this->user);

        $paymentData = [
            'supplier_credit_id' => $this->credit->id,
            'payment_date' => now()->toDateString(),
            'amount' => 500.00,
            'payment_method' => PaymentMethodEnum::CASH->value,
            'notes' => 'Test cash payment',
        ];

        // Act
        $payment = $this->paymentService->processPayment($paymentData);

        // Assert - Verify journal entry created
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => SupplierPayment::class,
            'reference_id' => $payment->id,
            'status' => 'posted',
        ]);

        $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
            ->where('reference_id', $payment->id)
            ->first();

        $lines = $journalEntry->lines;

        // Should have 2 lines: debit AP, credit Cash in Hand
        $this->assertCount(2, $lines);

        // Verify accounts used
        $cashInHandAccount = Account::where('account_code', '1110')->first();
        $accountsPayableAccount = Account::where('account_code', '2100')->first();

        // Check debit line (Accounts Payable)
        $debitLine = $lines->where('account_id', $accountsPayableAccount->id)->first();
        $this->assertNotNull($debitLine);
        $this->assertEquals(500.00, $debitLine->debit_amount);
        $this->assertEquals(0, $debitLine->credit_amount);

        // Check credit line (Cash in Hand for cash payment)
        $creditLine = $lines->where('account_id', $cashInHandAccount->id)->first();
        $this->assertNotNull($creditLine);
        $this->assertEquals(0, $creditLine->debit_amount);
        $this->assertEquals(500.00, $creditLine->credit_amount);

        // Verify journal entry is balanced
        $totalDebits = $lines->sum('debit_amount');
        $totalCredits = $lines->sum('credit_amount');
        $this->assertEquals($totalDebits, $totalCredits);
    }

    #[Test]
    public function it_creates_correct_journal_entry_for_bank_transfer(): void
    {
        // Arrange
        $this->actingAs($this->user);

        $paymentData = [
            'supplier_credit_id' => $this->credit->id,
            'payment_date' => now()->toDateString(),
            'amount' => 500.00,
            'payment_method' => PaymentMethodEnum::BANK_TRANSFER->value,
            'reference_number' => 'TRX123456',
        ];

        // Act
        $payment = $this->paymentService->processPayment($paymentData);

        // Assert - Verify correct account used (Cash in Bank, not Cash in Hand)
        $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
            ->where('reference_id', $payment->id)
            ->first();

        $lines = $journalEntry->lines;

        $cashInBankAccount = Account::where('account_code', '1120')->first();

        // Check credit line uses Cash in Bank (1120)
        $creditLine = $lines->where('account_id', $cashInBankAccount->id)->first();
        $this->assertNotNull($creditLine, 'Bank transfer should credit Cash in Bank account (1120)');
        $this->assertEquals(0, $creditLine->debit_amount);
        $this->assertEquals(500.00, $creditLine->credit_amount);
    }

    #[Test]
    public function it_creates_correct_journal_entry_for_check(): void
    {
        // Arrange
        $this->actingAs($this->user);

        $paymentData = [
            'supplier_credit_id' => $this->credit->id,
            'payment_date' => now()->toDateString(),
            'amount' => 500.00,
            'payment_method' => PaymentMethodEnum::CHECK->value,
            'reference_number' => 'CHK-001',
        ];

        // Act
        $payment = $this->paymentService->processPayment($paymentData);

        // Assert - Verify correct account used (Cash in Bank)
        $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
            ->where('reference_id', $payment->id)
            ->first();

        $lines = $journalEntry->lines;

        $cashInBankAccount = Account::where('account_code', '1120')->first();

        // Check credit line uses Cash in Bank (1120)
        $creditLine = $lines->where('account_id', $cashInBankAccount->id)->first();
        $this->assertNotNull($creditLine, 'Check payment should credit Cash in Bank account (1120)');
        $this->assertEquals(500.00, $creditLine->credit_amount);
    }

    #[Test]
    public function it_creates_correct_journal_entry_for_card_payment(): void
    {
        // Arrange
        $this->actingAs($this->user);

        $paymentData = [
            'supplier_credit_id' => $this->credit->id,
            'payment_date' => now()->toDateString(),
            'amount' => 500.00,
            'payment_method' => PaymentMethodEnum::CARD->value,
            'reference_number' => 'CARD-TRX-789',
        ];

        // Act
        $payment = $this->paymentService->processPayment($paymentData);

        // Assert - Verify correct account used (Cash in Bank)
        $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
            ->where('reference_id', $payment->id)
            ->first();

        $lines = $journalEntry->lines;

        $cashInBankAccount = Account::where('account_code', '1120')->first();

        // Check credit line uses Cash in Bank (1120)
        $creditLine = $lines->where('account_id', $cashInBankAccount->id)->first();
        $this->assertNotNull($creditLine, 'Card payment should credit Cash in Bank account (1120)');
        $this->assertEquals(500.00, $creditLine->credit_amount);
    }

    #[Test]
    public function it_uses_correct_account_codes_for_all_payment_methods(): void
    {
        // Test all payment methods to ensure correct account mapping
        $this->actingAs($this->user);

        $paymentMethods = [
            PaymentMethodEnum::CASH->value => '1110', // Cash in Hand
            PaymentMethodEnum::BANK_TRANSFER->value => '1120', // Cash in Bank
            PaymentMethodEnum::CHECK->value => '1120', // Cash in Bank
            PaymentMethodEnum::CARD->value => '1120', // Cash in Bank
        ];

        foreach ($paymentMethods as $method => $expectedAccountCode) {
            // Create fresh credit for each test
            $credit = SupplierCredit::factory()->create([
                'supplier_id' => $this->supplier->id,
                'original_amount' => 1000.00,
                'paid_amount' => 0,
                'outstanding_amount' => 1000.00,
            ]);

            $paymentData = [
                'supplier_credit_id' => $credit->id,
                'payment_date' => now()->toDateString(),
                'amount' => 100.00,
                'payment_method' => $method,
            ];

            $payment = $this->paymentService->processPayment($paymentData);

            $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
                ->where('reference_id', $payment->id)
                ->first();

            $expectedAccount = Account::where('account_code', $expectedAccountCode)->first();

            $creditLine = $journalEntry->lines->where('credit_amount', '>', 0)->first();

            $this->assertEquals(
                $expectedAccount->id,
                $creditLine->account_id,
                "Payment method '{$method}' should use account code '{$expectedAccountCode}'"
            );
        }
    }

    #[Test]
    public function it_creates_balanced_journal_entries_for_all_payment_methods(): void
    {
        // Verify all payment methods create balanced journal entries
        $this->actingAs($this->user);

        $paymentMethods = [
            PaymentMethodEnum::CASH->value,
            PaymentMethodEnum::BANK_TRANSFER->value,
            PaymentMethodEnum::CHECK->value,
            PaymentMethodEnum::CARD->value,
        ];

        foreach ($paymentMethods as $method) {
            // Create fresh credit for each test
            $credit = SupplierCredit::factory()->create([
                'supplier_id' => $this->supplier->id,
                'original_amount' => 1000.00,
                'paid_amount' => 0,
                'outstanding_amount' => 1000.00,
            ]);

            $paymentData = [
                'supplier_credit_id' => $credit->id,
                'payment_date' => now()->toDateString(),
                'amount' => 250.00,
                'payment_method' => $method,
            ];

            $payment = $this->paymentService->processPayment($paymentData);

            $journalEntry = JournalEntry::where('reference_type', SupplierPayment::class)
                ->where('reference_id', $payment->id)
                ->first();

            $totalDebits = $journalEntry->lines->sum('debit_amount');
            $totalCredits = $journalEntry->lines->sum('credit_amount');

            $this->assertEquals(
                $totalDebits,
                $totalCredits,
                "Payment method '{$method}' should create balanced journal entry"
            );

            $this->assertEquals(250.00, $totalDebits);
            $this->assertEquals(250.00, $totalCredits);
        }
    }
}
