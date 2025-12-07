<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Account;
use App\Models\Expense;
use App\Models\GoodReceiveNote;
use App\Models\PayrollPeriod;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SupplierPayment;

class TransactionIntegrationService
{
    public function __construct(protected JournalEntryService $journalEntryService) {}

    /**
     * Create journal entry for a sale transaction
     *
     * Dr Cash/Accounts Receivable (total collected)
     *    Cr Sales Revenue (before discount)
     *    Dr Sales Discounts (contra-revenue)
     * Dr COGS (based on cost price from stock)
     *    Cr Inventory
     */
    public function createSaleJournalEntry(Sale $sale): void
    {
        // Load items with stock relationship if not already loaded
        if (! $sale->relationLoaded('items')) {
            $sale->load('items.stock');
        }

        $paymentAccount = $this->mapPaymentMethodToAccount($sale->payment_method);
        $salesRevenueAccount = Account::where('account_code', '4100')->first(); // Product Sales
        $discountAccount = Account::where('account_code', '4200')->first(); // Sales Discounts
        $inventoryAccount = Account::where('account_code', '1300')->first(); // Inventory
        $cogsAccount = Account::where('account_code', '5100')->first(); // Purchases (COGS)

        $lines = [];
        $lineNumber = 1;

        // 1. DR Cash/AR/Bank (Total including tax)
        $lines[] = [
            'line_number' => $lineNumber++,
            'account_id' => $paymentAccount->id,
            'debit_amount' => $sale->total,
            'credit_amount' => 0,
            'description' => 'Payment received from sale '.$sale->sale_number.' via '.$sale->payment_method->label(),
        ];

        // 2. CR Sales Revenue (Subtotal before discounts)
        $lines[] = [
            'line_number' => $lineNumber++,
            'account_id' => $salesRevenueAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $sale->subtotal_before_discount,
            'description' => 'Sales revenue from '.$sale->sale_number,
        ];

        // 3. DR Sales Discounts (if applicable)
        if ($sale->total_discount > 0) {
            $lines[] = [
                'line_number' => $lineNumber++,
                'account_id' => $discountAccount->id,
                'debit_amount' => $sale->total_discount,
                'credit_amount' => 0,
                'description' => 'Discounts applied on sale '.$sale->sale_number,
            ];
        }

        // Calculate COGS based on cost price from stock
        $totalCOGS = $sale->items->sum(function ($item) {
            // Get the actual cost from the stock record
            return $item->stock->cost_price * $item->quantity;
        });

        if ($totalCOGS > 0) {
            // 4. DR COGS
            $lines[] = [
                'line_number' => $lineNumber++,
                'account_id' => $cogsAccount->id,
                'debit_amount' => $totalCOGS,
                'credit_amount' => 0,
                'description' => 'Cost of goods sold for '.$sale->sale_number,
            ];

            // 5. CR Inventory
            $lines[] = [
                'line_number' => $lineNumber++,
                'account_id' => $inventoryAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalCOGS,
                'description' => 'Inventory reduction for '.$sale->sale_number,
            ];
        }

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $sale->created_at,
            'description' => 'Sale transaction '.$sale->sale_number,
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'lines' => $lines,
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for a sale return
     *
     * Dr Sales Returns and Allowances
     *    Cr Cash/Accounts Receivable
     * Dr Inventory
     *    Cr COGS
     */
    public function createSaleReturnJournalEntry(SaleReturn $saleReturn): void
    {
        $cashAccount = Account::where('account_code', '1110')->first();
        $salesReturnsAccount = Account::where('account_code', '4200')->first();
        $inventoryAccount = Account::where('account_code', '1300')->first();
        $cogsAccount = Account::where('account_code', '5100')->first();

        $lines = [
            [
                'account_id' => $salesReturnsAccount->id,
                'debit_amount' => $saleReturn->total_amount,
                'credit_amount' => 0,
                'description' => 'Sale return '.$saleReturn->return_number,
            ],
            [
                'account_id' => $cashAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $saleReturn->total_amount,
                'description' => 'Cash refunded for return '.$saleReturn->return_number,
            ],
        ];

        // Calculate COGS to restore
        $totalCOGS = $saleReturn->items->sum(function ($item) {
            return $item->product->selling_price * $item->quantity;
        });

        if ($totalCOGS > 0) {
            $lines[] = [
                'account_id' => $inventoryAccount->id,
                'debit_amount' => $totalCOGS,
                'credit_amount' => 0,
                'description' => 'Inventory restored for return '.$saleReturn->return_number,
            ];

            $lines[] = [
                'account_id' => $cogsAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalCOGS,
                'description' => 'COGS reversal for return '.$saleReturn->return_number,
            ];
        }

        $this->journalEntryService->createJournalEntry([
            'entry_date' => $saleReturn->return_date,
            'description' => 'Sale return '.$saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'reference_id' => $saleReturn->id,
            'lines' => $lines,
        ]);
    }

    /**
     * Create journal entry for a Good Receive Note (Purchase)
     *
     * Dr Inventory
     *    Cr Accounts Payable (if credit)
     *    Cr Cash (if paid immediately)
     */
    public function createGRNJournalEntry(GoodReceiveNote $grn): void
    {
        $inventoryAccount = Account::where('account_code', '1300')->first();
        $accountsPayableAccount = Account::where('account_code', '2100')->first();
        $cashAccount = Account::where('account_code', '1110')->first();

        $lines = [
            [
                'account_id' => $inventoryAccount->id,
                'debit_amount' => $grn->total_amount,
                'credit_amount' => 0,
                'description' => 'Inventory purchase from GRN '.$grn->grn_number,
            ],
        ];

        // Determine if credit or cash
        if ($grn->is_credit) {
            $lines[] = [
                'account_id' => $accountsPayableAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $grn->total_amount,
                'description' => 'Payable to supplier for GRN '.$grn->grn_number,
            ];
        } else {
            $lines[] = [
                'account_id' => $cashAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $grn->total_amount,
                'description' => 'Cash payment for GRN '.$grn->grn_number,
            ];
        }

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $grn->received_date,
            'description' => 'Goods receipt '.$grn->grn_number,
            'reference_type' => GoodReceiveNote::class,
            'reference_id' => $grn->id,
            'lines' => $lines,
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for supplier payment
     *
     * Dr Accounts Payable
     *    Cr Cash in Hand/Cash in Bank (based on payment method)
     */
    public function createSupplierPaymentJournalEntry(SupplierPayment $payment): void
    {
        $accountsPayableAccount = Account::where('account_code', '2100')->first();

        // Get the appropriate cash/bank account based on payment method
        $paymentAccount = $this->mapPaymentMethodToAccount($payment->payment_method);

        // Generate payment description based on method
        $paymentDescription = match ($payment->payment_method) {
            PaymentMethodEnum::CASH => 'Cash paid to supplier',
            PaymentMethodEnum::BANK_TRANSFER => 'Bank transfer to supplier',
            PaymentMethodEnum::CHECK => 'Check issued to supplier',
            PaymentMethodEnum::CARD => 'Card payment to supplier',
        };

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $payment->payment_date,
            'description' => 'Supplier payment '.$payment->payment_number.' via '.$payment->payment_method->label(),
            'reference_type' => SupplierPayment::class,
            'reference_id' => $payment->id,
            'lines' => [
                [
                    'account_id' => $accountsPayableAccount->id,
                    'debit_amount' => $payment->amount,
                    'credit_amount' => 0,
                    'description' => 'Payment to supplier',
                ],
                [
                    'account_id' => $paymentAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $payment->amount,
                    'description' => $paymentDescription,
                ],
            ],
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for an expense
     *
     * Dr Expense Account (based on category)
     *    Cr Cash/Bank
     */
    public function createExpenseJournalEntry(Expense $expense): void
    {
        // Map expense category to GL account
        $expenseAccount = $this->mapExpenseCategoryToAccount($expense->category->category_name);
        $paymentAccount = $this->mapPaymentMethodToAccount($expense->payment_method);

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $expense->expense_date,
            'description' => 'Expense: '.$expense->title.' ('.$expense->expense_number.')',
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'lines' => [
                [
                    'account_id' => $expenseAccount->id,
                    'debit_amount' => $expense->amount,
                    'credit_amount' => 0,
                    'description' => $expense->description ?? $expense->title,
                ],
                [
                    'account_id' => $paymentAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $expense->amount,
                    'description' => 'Payment for '.$expense->title.' via '.$expense->payment_method->label(),
                ],
            ],
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for payroll accrual (when approved)
     *
     * Dr Salaries and Wages Expense (gross pay)
     * Dr EPF Employer Expense
     * Dr ETF Employer Expense
     *    Cr Salaries Payable (net pay)
     *    Cr EPF Payable (employee + employer)
     *    Cr ETF Payable (employer)
     */
    public function createPayrollAccrualEntry(PayrollPeriod $period): void
    {
        // Load entries if not already loaded
        if (! $period->relationLoaded('payrollEntries')) {
            $period->load('payrollEntries');
        }

        $salariesExpenseAccount = Account::where('account_code', '6100')->first();
        $salariesPayableAccount = Account::where('account_code', '2200')->first();
        $epfPayableAccount = Account::where('account_code', '2210')->first();
        $etfPayableAccount = Account::where('account_code', '2220')->first();

        $totalGrossPay = $period->getTotalGrossPay();
        $totalNetPay = $period->getTotalNetPay();
        $totalEPFEmployee = $period->getTotalEPFEmployee();
        $totalEPFEmployer = $period->getTotalEPFEmployer();
        $totalETF = $period->getTotalETF();

        $lines = [
            // Record salary expense (gross pay)
            [
                'account_id' => $salariesExpenseAccount->id,
                'debit_amount' => $totalGrossPay,
                'credit_amount' => 0,
                'description' => 'Salaries expense for period '.$period->period_start->format('Y-m-d').' to '.$period->period_end->format('Y-m-d'),
            ],
            // Record EPF employer contribution as expense
            [
                'account_id' => $salariesExpenseAccount->id,
                'debit_amount' => $totalEPFEmployer,
                'credit_amount' => 0,
                'description' => 'EPF employer contribution (12%)',
            ],
            // Record ETF employer contribution as expense
            [
                'account_id' => $salariesExpenseAccount->id,
                'debit_amount' => $totalETF,
                'credit_amount' => 0,
                'description' => 'ETF employer contribution (3%)',
            ],
            // Record net salary payable to employees
            [
                'account_id' => $salariesPayableAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalNetPay,
                'description' => 'Net salaries payable to employees',
            ],
            // Record total EPF payable (employee 8% + employer 12%)
            [
                'account_id' => $epfPayableAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalEPFEmployee + $totalEPFEmployer,
                'description' => 'EPF contributions payable (employee 8% + employer 12%)',
            ],
            // Record ETF payable (employer 3%)
            [
                'account_id' => $etfPayableAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalETF,
                'description' => 'ETF contribution payable (employer 3%)',
            ],
        ];

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $period->approved_at ?? now(),
            'description' => 'Payroll accrual for period '.$period->period_start->format('Y-m-d').' to '.$period->period_end->format('Y-m-d'),
            'reference_type' => PayrollPeriod::class,
            'reference_id' => $period->id,
            'lines' => $lines,
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for payroll payment (when marked as paid)
     *
     * Dr Salaries Payable
     *    Cr Cash in Hand
     */
    public function createPayrollPaymentEntry(PayrollPeriod $period): void
    {
        $salariesPayableAccount = Account::where('account_code', '2200')->first();
        $cashAccount = Account::where('account_code', '1110')->first();

        $totalNetPay = $period->getTotalNetPay();

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => now(),
            'description' => 'Payroll payment for period '.$period->period_start->format('Y-m-d').' to '.$period->period_end->format('Y-m-d'),
            'reference_type' => PayrollPeriod::class,
            'reference_id' => $period->id,
            'lines' => [
                [
                    'account_id' => $salariesPayableAccount->id,
                    'debit_amount' => $totalNetPay,
                    'credit_amount' => 0,
                    'description' => 'Payment of accrued salaries',
                ],
                [
                    'account_id' => $cashAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $totalNetPay,
                    'description' => 'Cash paid for salaries',
                ],
            ],
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Map expense category to chart of accounts
     */
    protected function mapExpenseCategoryToAccount(string $categoryName): Account
    {
        // Define category to account mapping
        $mapping = [
            'Rent' => '6200',
            'Utilities' => '6300',
            'Office Supplies' => '6400',
            'Marketing' => '6500',
            'Maintenance' => '6600',
            'Transportation' => '6700',
            'Professional Fees' => '6800',
            'Salaries' => '6100',
        ];

        $accountCode = $mapping[$categoryName] ?? '6900'; // Miscellaneous

        return Account::where('account_code', $accountCode)->first();
    }

    /**
     * Map payment method to appropriate cash/bank/AR GL account
     */
    protected function mapPaymentMethodToAccount(PaymentMethodEnum $paymentMethod): Account
    {
        // Define payment method to account code mapping
        $accountCode = match ($paymentMethod) {
            PaymentMethodEnum::CASH => '1110',           // Cash in Hand (physical cash)
            PaymentMethodEnum::BANK_TRANSFER => '1120', // Cash in Bank (electronic transfers)
            PaymentMethodEnum::CHECK => '1120',         // Cash in Bank (checks deposit to bank)
            PaymentMethodEnum::CARD => '1120',          // Cash in Bank (card settlements)
            PaymentMethodEnum::CREDIT => '1200',        // Accounts Receivable (customer credit)
        };

        return Account::where('account_code', $accountCode)->firstOrFail();
    }
}
