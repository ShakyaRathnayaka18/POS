<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Employee;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PayrollPeriod;
use App\Models\PayrollSettings;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayrollAccountingIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected Employee $employee;

    protected PayrollService $payrollService;

    protected PayrollSettings $payrollSettings;

    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing test data from failed test runs
        \DB::table('payroll_entry_shift')->delete();
        \DB::table('payroll_entries')->delete();
        \DB::table('payroll_periods')->delete();

        // Verify required accounts exist (seeded from ChartOfAccountsSeeder)
        $this->assertDatabaseHas('accounts', ['account_code' => '6100']); // Salaries and Wages
        $this->assertDatabaseHas('accounts', ['account_code' => '2200']); // Salaries Payable
        $this->assertDatabaseHas('accounts', ['account_code' => '2210']); // EPF Payable
        $this->assertDatabaseHas('accounts', ['account_code' => '2220']); // ETF Payable
        $this->assertDatabaseHas('accounts', ['account_code' => '1110']); // Cash in Hand

        // Use REAL seeded super admin user
        $this->admin = User::where('email', 'superadmin@pos.com')->firstOrFail();

        // Create a test employee with known salary
        $this->employee = Employee::factory()->create([
            'user_id' => User::factory()->create([
                'name' => 'Test Employee',
            ])->id,
            'employment_type' => 'salaried',
            'base_salary' => 50000.00, // LKR 50,000 per month
            'status' => 'active',
        ]);

        // Ensure PayrollSettings exists
        $this->payrollSettings = PayrollSettings::firstOrCreate(
            [],
            [
                'epf_employee_percentage' => 8.0,
                'epf_employer_percentage' => 12.0,
                'etf_employer_percentage' => 3.0,
                'daily_hours_threshold' => 8.0,
                'ot_calculation_mode' => 'multiplier',
                'ot_weekday_multiplier' => 1.5,
                'ot_weekend_multiplier' => 2.0,
            ]
        );

        // Get PayrollService instance
        $this->payrollService = app(PayrollService::class);
    }

    #[Test]
    public function it_creates_accrual_journal_entry_when_payroll_approved(): void
    {
        // Arrange - Create and process a payroll period
        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->endOfMonth()->format('Y-m-d'),
            notes: 'Test payroll period'
        );

        // Generate entries for all employees
        $this->payrollService->generateEntries($period->id);

        // Delete all payroll entries except for our test employee
        $period->payrollEntries()->where('employee_id', '!=', $this->employee->id)->delete();

        // Manually set payroll values for our test employee only (simulating calculated payroll)
        $entry = $period->payrollEntries()->where('employee_id', $this->employee->id)->first();
        $entry->update([
            'regular_hours' => 160.0,
            'overtime_hours' => 0,
            'overtime_hours_2x' => 0,
            'base_amount' => 50000.00,
            'overtime_amount' => 0,
            'overtime_amount_2x' => 0,
            'gross_pay' => 50000.00,
            'epf_employee' => 4000.00, // 8% of gross
            'epf_employer' => 6000.00, // 12% of gross
            'etf_employer' => 1500.00, // 3% of gross
            'net_pay' => 46000.00, // gross - epf_employee
        ]);

        // Process period
        $period = $this->payrollService->processPeriod($period->id, $this->admin->id);

        // Act - Approve the payroll (should trigger accrual journal entry)
        $this->actingAs($this->admin);
        $period = $this->payrollService->approvePeriod($period->id, $this->admin->id);

        // Refresh to get latest data
        $period->refresh();

        // Assert - Payroll was approved
        $this->assertEquals('approved', $period->status);

        // Assert - Accrual Journal Entry was created and automatically posted
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => PayrollPeriod::class,
            'reference_id' => $period->id,
            'status' => 'posted',
        ]);

        $journalEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll accrual%')
            ->first();

        $this->assertNotNull($journalEntry);
        $this->assertStringContainsString('Payroll accrual', $journalEntry->description);

        // Assert - 6 Journal Entry Lines exist (3 debits, 3 credits)
        $this->assertCount(6, $journalEntry->lines);

        // Get accounts
        $salariesExpenseAccount = Account::where('account_code', '6100')->first();
        $salariesPayableAccount = Account::where('account_code', '2200')->first();
        $epfPayableAccount = Account::where('account_code', '2210')->first();
        $etfPayableAccount = Account::where('account_code', '2220')->first();

        // Calculate expected values
        $expectedGrossPay = 50000.00;
        $expectedEPFEmployer = 6000.00;
        $expectedETF = 1500.00;
        $expectedNetPay = 46000.00;
        $expectedEPFEmployee = 4000.00;
        $expectedTotalEPF = $expectedEPFEmployee + $expectedEPFEmployer; // 10000.00

        // Assert - Salaries Expense account is debited with gross pay
        $salariesExpenseLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salariesExpenseAccount->id)
            ->where('description', 'like', '%Salaries expense for period%')
            ->first();

        $this->assertNotNull($salariesExpenseLine);
        $this->assertEquals($expectedGrossPay, $salariesExpenseLine->debit_amount);
        $this->assertEquals(0.00, $salariesExpenseLine->credit_amount);

        // Assert - EPF Employer Expense is debited
        $epfEmployerExpenseLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salariesExpenseAccount->id)
            ->where('description', 'like', '%EPF employer%')
            ->first();

        $this->assertNotNull($epfEmployerExpenseLine);
        $this->assertEquals($expectedEPFEmployer, $epfEmployerExpenseLine->debit_amount);
        $this->assertEquals(0.00, $epfEmployerExpenseLine->credit_amount);

        // Assert - ETF Employer Expense is debited
        $etfEmployerExpenseLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salariesExpenseAccount->id)
            ->where('description', 'like', '%ETF employer%')
            ->first();

        $this->assertNotNull($etfEmployerExpenseLine);
        $this->assertEquals($expectedETF, $etfEmployerExpenseLine->debit_amount);
        $this->assertEquals(0.00, $etfEmployerExpenseLine->credit_amount);

        // Assert - Salaries Payable account is credited with net pay
        $salariesPayableLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salariesPayableAccount->id)
            ->first();

        $this->assertNotNull($salariesPayableLine);
        $this->assertEquals(0.00, $salariesPayableLine->debit_amount);
        $this->assertEquals($expectedNetPay, $salariesPayableLine->credit_amount);

        // Assert - EPF Payable account is credited with total EPF (employee + employer)
        $epfPayableLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $epfPayableAccount->id)
            ->first();

        $this->assertNotNull($epfPayableLine);
        $this->assertEquals(0.00, $epfPayableLine->debit_amount);
        $this->assertEquals($expectedTotalEPF, $epfPayableLine->credit_amount);

        // Assert - ETF Payable account is credited with ETF
        $etfPayableLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $etfPayableAccount->id)
            ->first();

        $this->assertNotNull($etfPayableLine);
        $this->assertEquals(0.00, $etfPayableLine->debit_amount);
        $this->assertEquals($expectedETF, $etfPayableLine->credit_amount);

        // Assert - Total Debits = Total Credits (balanced entry)
        $totalDebits = $journalEntry->lines->sum('debit_amount');
        $totalCredits = $journalEntry->lines->sum('credit_amount');

        $this->assertEquals($totalDebits, $totalCredits);

        // Expected: Gross + EPF Employer + ETF = Net + Total EPF + ETF
        $expectedDebits = $expectedGrossPay + $expectedEPFEmployer + $expectedETF;
        $this->assertEquals($expectedDebits, $totalDebits);
        $this->assertEquals($expectedDebits, $totalCredits);
    }

    #[Test]
    public function it_creates_payment_journal_entry_when_payroll_marked_as_paid(): void
    {
        // Arrange - Create, process, and approve payroll (use next month to avoid conflicts)
        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->addMonth()->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->addMonth()->endOfMonth()->format('Y-m-d')
        );

        $this->payrollService->generateEntries($period->id);

        // Delete all payroll entries except for our test employee
        $period->payrollEntries()->where('employee_id', '!=', $this->employee->id)->delete();

        $entry = $period->payrollEntries()->where('employee_id', $this->employee->id)->first();
        $entry->update([
            'gross_pay' => 50000.00,
            'epf_employee' => 4000.00,
            'epf_employer' => 6000.00,
            'etf_employer' => 1500.00,
            'net_pay' => 46000.00,
        ]);

        $period = $this->payrollService->processPeriod($period->id, $this->admin->id);
        $period = $this->payrollService->approvePeriod($period->id, $this->admin->id);

        // Act - Mark as paid (should trigger payment journal entry)
        $this->actingAs($this->admin);
        $period = $this->payrollService->markAsPaid($period->id);

        // Refresh
        $period->refresh();

        // Assert - Payroll was marked as paid
        $this->assertEquals('paid', $period->status);

        // Assert - Payment Journal Entry was created
        $journalEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll payment%')
            ->first();

        $this->assertNotNull($journalEntry);
        $this->assertEquals('posted', $journalEntry->status);

        // Assert - 2 Journal Entry Lines exist (debit Salaries Payable, credit Cash)
        $this->assertCount(2, $journalEntry->lines);

        // Get accounts
        $salariesPayableAccount = Account::where('account_code', '2200')->first();
        $cashAccount = Account::where('account_code', '1110')->first();

        $expectedNetPay = 46000.00;

        // Assert - Salaries Payable is debited
        $salariesPayableLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salariesPayableAccount->id)
            ->first();

        $this->assertNotNull($salariesPayableLine);
        $this->assertEquals($expectedNetPay, $salariesPayableLine->debit_amount);
        $this->assertEquals(0.00, $salariesPayableLine->credit_amount);

        // Assert - Cash is credited
        $cashLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $cashAccount->id)
            ->first();

        $this->assertNotNull($cashLine);
        $this->assertEquals(0.00, $cashLine->debit_amount);
        $this->assertEquals($expectedNetPay, $cashLine->credit_amount);

        // Assert - Balanced
        $totalDebits = $journalEntry->lines->sum('debit_amount');
        $totalCredits = $journalEntry->lines->sum('credit_amount');
        $this->assertEquals($totalDebits, $totalCredits);
        $this->assertEquals($expectedNetPay, $totalDebits);
    }

    #[Test]
    public function it_calculates_epf_and_etf_correctly_using_real_rates(): void
    {
        // Arrange
        $grossPay = 50000.00;

        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->addMonths(2)->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->addMonths(2)->endOfMonth()->format('Y-m-d')
        );

        $this->payrollService->generateEntries($period->id);

        // Delete all payroll entries except for our test employee
        $period->payrollEntries()->where('employee_id', '!=', $this->employee->id)->delete();

        $entry = $period->payrollEntries()->where('employee_id', $this->employee->id)->first();
        $entry->update([
            'gross_pay' => $grossPay,
            'epf_employee' => $grossPay * 0.08, // 8%
            'epf_employer' => $grossPay * 0.12, // 12%
            'etf_employer' => $grossPay * 0.03, // 3%
            'net_pay' => $grossPay - ($grossPay * 0.08),
        ]);

        $period = $this->payrollService->processPeriod($period->id, $this->admin->id);

        // Act
        $this->actingAs($this->admin);
        $period = $this->payrollService->approvePeriod($period->id, $this->admin->id);

        // Assert
        $journalEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll accrual%')
            ->first();

        $epfPayableAccount = Account::where('account_code', '2210')->first();
        $etfPayableAccount = Account::where('account_code', '2220')->first();

        // EPF should be 8% (employee) + 12% (employer) = 20% = 10,000
        $expectedEPF = $grossPay * 0.20;
        $epfLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $epfPayableAccount->id)
            ->first();

        $this->assertEquals($expectedEPF, $epfLine->credit_amount);

        // ETF should be 3% (employer only) = 1,500
        $expectedETF = $grossPay * 0.03;
        $etfLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $etfPayableAccount->id)
            ->first();

        $this->assertEquals($expectedETF, $etfLine->credit_amount);
    }

    #[Test]
    public function it_uses_correct_account_codes_for_payroll_entries(): void
    {
        // Arrange & Act
        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->addMonths(3)->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->addMonths(3)->endOfMonth()->format('Y-m-d')
        );

        $this->payrollService->generateEntries($period->id);

        // Delete all payroll entries except for our test employee
        $period->payrollEntries()->where('employee_id', '!=', $this->employee->id)->delete();

        $entry = $period->payrollEntries()->where('employee_id', $this->employee->id)->first();
        $entry->update([
            'gross_pay' => 50000.00,
            'epf_employee' => 4000.00,
            'epf_employer' => 6000.00,
            'etf_employer' => 1500.00,
            'net_pay' => 46000.00,
        ]);

        $period = $this->payrollService->processPeriod($period->id, $this->admin->id);

        $this->actingAs($this->admin);
        $period = $this->payrollService->approvePeriod($period->id, $this->admin->id);

        // Assert - Verify correct account codes are used in accrual entry
        $journalEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll accrual%')
            ->first();

        $accountCodes = $journalEntry->lines->map(function ($line) {
            return $line->account->account_code;
        })->toArray();

        // Should use exactly these account codes
        $this->assertContains('6100', $accountCodes); // Salaries and Wages (appears 3 times)
        $this->assertContains('2200', $accountCodes); // Salaries Payable
        $this->assertContains('2210', $accountCodes); // EPF Payable
        $this->assertContains('2220', $accountCodes); // ETF Payable
        $this->assertCount(6, $accountCodes); // Total 6 lines
    }

    #[Test]
    public function it_does_not_create_journal_entry_if_payroll_not_approved(): void
    {
        // Arrange - Create a draft payroll period
        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->addMonths(4)->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->addMonths(4)->endOfMonth()->format('Y-m-d')
        );

        // Assert - No journal entry should be created for draft status
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => PayrollPeriod::class,
            'reference_id' => $period->id,
        ]);

        // Process but don't approve
        $this->payrollService->generateEntries($period->id);
        $entry = $period->payrollEntries()->first();
        $entry->update([
            'gross_pay' => 50000.00,
            'net_pay' => 46000.00,
        ]);

        $period = $this->payrollService->processPeriod($period->id, $this->admin->id);

        // Assert - Still no journal entry for processing status
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => PayrollPeriod::class,
            'reference_id' => $period->id,
        ]);
    }

    #[Test]
    public function it_balances_debits_and_credits_for_payroll(): void
    {
        // Arrange
        $period = $this->payrollService->createPayrollPeriod(
            periodStart: now()->addMonths(5)->startOfMonth()->format('Y-m-d'),
            periodEnd: now()->addMonths(5)->endOfMonth()->format('Y-m-d')
        );

        $this->payrollService->generateEntries($period->id);

        // Delete all payroll entries except for our test employee
        $period->payrollEntries()->where('employee_id', '!=', $this->employee->id)->delete();

        $entry = $period->payrollEntries()->where('employee_id', $this->employee->id)->first();
        $entry->update([
            'gross_pay' => 75000.00,
            'epf_employee' => 6000.00,
            'epf_employer' => 9000.00,
            'etf_employer' => 2250.00,
            'net_pay' => 69000.00,
        ]);

        // Manually set period status to processing (bypassing processPeriod to keep our custom values)
        $period->update([
            'status' => 'processing',
            'processed_by' => $this->admin->id,
            'processed_at' => now(),
        ]);

        // Act
        $this->actingAs($this->admin);
        $period = $this->payrollService->approvePeriod($period->id, $this->admin->id);

        // Assert - Accrual entry is balanced
        $accrualEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll accrual%')
            ->first();

        $totalDebits = $accrualEntry->lines->sum('debit_amount');
        $totalCredits = $accrualEntry->lines->sum('credit_amount');

        $this->assertEquals($totalDebits, $totalCredits);

        // Debits: Gross (75000) + EPF Employer (9000) + ETF (2250) = 86250
        $expectedTotal = 86250.00;
        $this->assertEquals($expectedTotal, $totalDebits);
        $this->assertEquals($expectedTotal, $totalCredits);

        // Mark as paid and verify payment entry is balanced
        $period = $this->payrollService->markAsPaid($period->id);

        $paymentEntry = JournalEntry::where('reference_type', PayrollPeriod::class)
            ->where('reference_id', $period->id)
            ->where('description', 'like', '%Payroll payment%')
            ->first();

        $paymentDebits = $paymentEntry->lines->sum('debit_amount');
        $paymentCredits = $paymentEntry->lines->sum('credit_amount');

        $this->assertEquals($paymentDebits, $paymentCredits);
        $this->assertEquals(69000.00, $paymentDebits); // Net pay
    }
}
