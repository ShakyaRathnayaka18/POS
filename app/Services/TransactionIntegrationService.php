<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\GoodReceiveNote;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SupplierPayment;
use Carbon\Carbon;

class TransactionIntegrationService
{
    public function __construct(protected JournalEntryService $journalEntryService) {}

    /**
     * Create journal entry for a sale transaction
     *
     * Dr Cash/Accounts Receivable
     *    Cr Sales Revenue
     *    Cr COGS (Dr based on selling price from GRN)
     *    Dr Inventory
     */
    public function createSaleJournalEntry(Sale $sale): void
    {
        // Load items with stock relationship if not already loaded
        if (! $sale->relationLoaded('items')) {
            $sale->load('items.stock');
        }

        $cashAccount = Account::where('account_code', '1110')->first(); // Cash in Hand
        $salesRevenueAccount = Account::where('account_code', '4100')->first(); // Product Sales
        $inventoryAccount = Account::where('account_code', '1300')->first(); // Inventory
        $cogsAccount = Account::where('account_code', '5100')->first(); // Purchases (COGS)

        $lines = [
            // Record revenue
            [
                'account_id' => $cashAccount->id,
                'debit_amount' => $sale->total,
                'credit_amount' => 0,
                'description' => 'Cash received from sale '.$sale->sale_number,
            ],
            [
                'account_id' => $salesRevenueAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $sale->total,
                'description' => 'Sales revenue from '.$sale->sale_number,
            ],
        ];

        // Calculate COGS based on cost price from stock
        $totalCOGS = $sale->items->sum(function ($item) {
            // Get the actual cost from the stock record
            return $item->stock->cost_price * $item->quantity;
        });

        if ($totalCOGS > 0) {
            // Record COGS
            $lines[] = [
                'account_id' => $cogsAccount->id,
                'debit_amount' => $totalCOGS,
                'credit_amount' => 0,
                'description' => 'Cost of goods sold for '.$sale->sale_number,
            ];

            // Reduce inventory
            $lines[] = [
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
     *    Cr Cash
     */
    public function createSupplierPaymentJournalEntry(SupplierPayment $payment): void
    {
        $accountsPayableAccount = Account::where('account_code', '2100')->first();
        $cashAccount = Account::where('account_code', '1110')->first();

        $journalEntry = $this->journalEntryService->createJournalEntry([
            'entry_date' => $payment->payment_date,
            'description' => 'Supplier payment '.$payment->payment_number,
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
                    'account_id' => $cashAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $payment->amount,
                    'description' => 'Cash paid to supplier',
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
        $cashAccount = Account::where('account_code', '1110')->first();

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
                    'account_id' => $cashAccount->id,
                    'debit_amount' => 0,
                    'credit_amount' => $expense->amount,
                    'description' => 'Cash paid for '.$expense->title,
                ],
            ],
        ]);

        // Automatically POST the journal entry
        $this->journalEntryService->postJournalEntry($journalEntry);
    }

    /**
     * Create journal entry for payroll
     *
     * Dr Salaries and Wages Expense
     *    Cr Salaries Payable or Cash
     */
    public function createPayrollJournalEntry(Payroll $payroll): void
    {
        $salariesExpenseAccount = Account::where('account_code', '6100')->first();
        $salariesPayableAccount = Account::where('account_code', '2200')->first();
        $cashAccount = Account::where('account_code', '1110')->first();

        $lines = [
            [
                'account_id' => $salariesExpenseAccount->id,
                'debit_amount' => $payroll->net_salary,
                'credit_amount' => 0,
                'description' => 'Salary for '.$payroll->employee->name.' - '.$payroll->pay_period,
            ],
        ];

        // Determine if paid or accrued
        if ($payroll->status === 'paid') {
            $lines[] = [
                'account_id' => $cashAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $payroll->net_salary,
                'description' => 'Cash paid for salary',
            ];
        } else {
            $lines[] = [
                'account_id' => $salariesPayableAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $payroll->net_salary,
                'description' => 'Accrued salary payable',
            ];
        }

        $this->journalEntryService->createJournalEntry([
            'entry_date' => Carbon::parse($payroll->pay_period),
            'description' => 'Payroll for '.$payroll->employee->name,
            'reference_type' => Payroll::class,
            'reference_id' => $payroll->id,
            'lines' => $lines,
        ]);
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
}
