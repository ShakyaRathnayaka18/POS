<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Batch;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleAccountingIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Product $product;

    protected Batch $batch;

    protected Stock $stock;

    protected SaleService $saleService;

    protected function setUp(): void
    {
        parent::setUp();

        // Verify accounts exist (seeded from ChartOfAccountsSeeder)
        $this->assertDatabaseHas('accounts', ['account_code' => '1110']); // Cash in Hand
        $this->assertDatabaseHas('accounts', ['account_code' => '4100']); // Product Sales
        $this->assertDatabaseHas('accounts', ['account_code' => '5100']); // Purchases (COGS)
        $this->assertDatabaseHas('accounts', ['account_code' => '1300']); // Inventory

        // Use REAL seeded user - John Cashier
        $this->user = User::where('name', 'John Cashier')->firstOrFail();

        // Use REAL seeded stock with product and batch
        $this->stock = Stock::with('product', 'batch')
            ->where('available_quantity', '>', 0)
            ->firstOrFail();

        $this->product = $this->stock->product;
        $this->batch = $this->stock->batch;

        // Get SaleService instance
        $this->saleService = app(SaleService::class);
    }

    #[Test]
    public function it_creates_correct_journal_entries_when_sale_is_completed(): void
    {
        // Arrange - Use real stock data
        $quantity = 1; // Use 1 to avoid insufficient stock issues
        $expectedTotal = $this->stock->selling_price * $quantity;

        $saleData = [
            'sale_number' => 'SALE-'.time().'-'.rand(1000, 9999),
            'user_id' => $this->user->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '0771234567',
            'payment_method' => 'Cash',
        ];

        $cartItems = [
            [
                'product_id' => $this->product->id,
                'quantity' => $quantity,
            ],
        ];

        // Act - Create sale using SaleService (proper way)
        $this->actingAs($this->user);
        $sale = $this->saleService->processSale($saleData, $cartItems);

        // Refresh to get latest data
        $sale->refresh();

        // Assert - Sale was created successfully
        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertEquals('Completed', $sale->status);
        $this->assertEquals($expectedTotal, $sale->total);
        $this->assertGreaterThan(0, $sale->items->count());

        // Assert - Journal Entry was created and automatically posted
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'status' => 'posted',
        ]);

        $journalEntry = JournalEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();

        $this->assertNotNull($journalEntry);
        $this->assertEquals('Sale transaction '.$sale->sale_number, $journalEntry->description);
        $this->assertEquals($sale->created_at->format('Y-m-d'), $journalEntry->entry_date->format('Y-m-d'));

        // Assert - 4 Journal Entry Lines exist (Cash Dr, Sales Cr, COGS Dr, Inventory Cr)
        $this->assertCount(4, $journalEntry->lines);

        // Get accounts
        $cashAccount = Account::where('account_code', '1110')->first();
        $salesRevenueAccount = Account::where('account_code', '4100')->first();
        $cogsAccount = Account::where('account_code', '5100')->first();
        $inventoryAccount = Account::where('account_code', '1300')->first();

        // Calculate expected values based on real stock data
        $expectedRevenue = $this->stock->selling_price * $quantity;
        $expectedCOGS = $this->stock->cost_price * $quantity;

        // Assert - Cash account is debited with sale total
        $cashLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $cashAccount->id)
            ->first();

        $this->assertNotNull($cashLine);
        $this->assertEquals($expectedRevenue, $cashLine->debit_amount);
        $this->assertEquals(0.00, $cashLine->credit_amount);
        $this->assertStringContainsString('Cash received from sale', $cashLine->description);

        // Assert - Sales Revenue account is credited with sale total
        $salesLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $salesRevenueAccount->id)
            ->first();

        $this->assertNotNull($salesLine);
        $this->assertEquals(0.00, $salesLine->debit_amount);
        $this->assertEquals($expectedRevenue, $salesLine->credit_amount);
        $this->assertStringContainsString('Sales revenue from', $salesLine->description);

        // Assert - COGS account is debited with calculated cost
        $cogsLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $cogsAccount->id)
            ->first();

        $this->assertNotNull($cogsLine);
        $this->assertEquals($expectedCOGS, $cogsLine->debit_amount);
        $this->assertEquals(0.00, $cogsLine->credit_amount);
        $this->assertStringContainsString('Cost of goods sold', $cogsLine->description);

        // Assert - Inventory account is credited with calculated cost
        $inventoryLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $inventoryAccount->id)
            ->first();

        $this->assertNotNull($inventoryLine);
        $this->assertEquals(0.00, $inventoryLine->debit_amount);
        $this->assertEquals($expectedCOGS, $inventoryLine->credit_amount);
        $this->assertStringContainsString('Inventory reduction', $inventoryLine->description);

        // Assert - Total Debits = Total Credits (balanced entry)
        $totalDebits = $journalEntry->lines->sum('debit_amount');
        $totalCredits = $journalEntry->lines->sum('credit_amount');

        $this->assertEquals($totalDebits, $totalCredits);

        // Expected: Revenue (selling_price * qty) + COGS (cost_price * qty)
        $expectedDebits = ($this->stock->selling_price * $quantity) + ($this->stock->cost_price * $quantity);
        $this->assertEquals($expectedDebits, $totalDebits);
        $this->assertEquals($expectedDebits, $totalCredits);

        // Assert - Stock quantity was reduced
        $initialQuantity = $this->stock->available_quantity;
        $this->stock->refresh();
        $this->assertEquals($initialQuantity - $quantity, $this->stock->available_quantity);
    }

    #[Test]
    public function it_calculates_cogs_correctly_using_stock_cost_price(): void
    {
        // Arrange - Use real stock's cost and selling prices
        $costPrice = $this->stock->cost_price;
        $sellingPrice = $this->stock->selling_price;
        $quantity = 1; // Use 1 to avoid insufficient stock issues

        $saleData = [
            'sale_number' => 'SALE-'.time().'-'.rand(1000, 9999),
            'user_id' => $this->user->id,
            'customer_name' => 'Jane Smith',
            'customer_phone' => '0777654321',
            'payment_method' => 'Cash',
        ];

        $cartItems = [
            [
                'product_id' => $this->product->id,
                'quantity' => $quantity,
            ],
        ];

        // Act
        $this->actingAs($this->user);
        $sale = $this->saleService->processSale($saleData, $cartItems);

        // Assert
        $journalEntry = JournalEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();

        $cogsAccount = Account::where('account_code', '5100')->first();
        $cogsLine = JournalEntryLine::where('journal_entry_id', $journalEntry->id)
            ->where('account_id', $cogsAccount->id)
            ->first();

        // COGS should be calculated from cost_price, NOT selling_price
        $expectedCOGS = $costPrice * $quantity; // 450 * 3 = 1350
        $this->assertEquals($expectedCOGS, $cogsLine->debit_amount);
        $this->assertNotEquals($sellingPrice * $quantity, $cogsLine->debit_amount); // Should NOT use selling price
    }

    #[Test]
    public function it_does_not_create_journal_entry_if_sale_status_is_not_completed(): void
    {
        // Arrange - Create a pending sale directly (bypassing SaleService)
        $sale = Sale::create([
            'sale_number' => 'SALE-PENDING-'.rand(1000, 9999),
            'user_id' => $this->user->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0771111111',
            'subtotal' => 1000.00,
            'tax' => 0.00,
            'total' => 1000.00,
            'payment_method' => 'Cash',
            'status' => 'Pending', // Not completed
        ]);

        // Assert - No journal entry should be created
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
        ]);
    }

    #[Test]
    public function it_uses_correct_account_codes_for_journal_entries(): void
    {
        // Arrange & Act
        $saleData = [
            'sale_number' => 'SALE-'.time().'-'.rand(1000, 9999),
            'user_id' => $this->user->id,
            'customer_name' => 'Account Test',
            'customer_phone' => '0779999999',
            'payment_method' => 'Cash',
        ];

        $cartItems = [
            [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ],
        ];

        $this->actingAs($this->user);
        $sale = $this->saleService->processSale($saleData, $cartItems);

        // Assert - Verify correct account codes are used
        $journalEntry = JournalEntry::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();

        $accountCodes = $journalEntry->lines->map(function ($line) {
            return $line->account->account_code;
        })->toArray();

        // Should have exactly these 4 account codes
        $this->assertContains('1110', $accountCodes); // Cash in Hand
        $this->assertContains('4100', $accountCodes); // Product Sales
        $this->assertContains('5100', $accountCodes); // Purchases (COGS)
        $this->assertContains('1300', $accountCodes); // Inventory
        $this->assertCount(4, $accountCodes);
    }
}
