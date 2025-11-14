<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\AccountType;
use App\Models\JournalEntryLine;
use Carbon\Carbon;

class FinancialStatementService
{
    /**
     * Generate Income Statement (Profit & Loss)
     */
    public function generateIncomeStatement(int $year, int $month): array
    {
        $revenueType = AccountType::where('name', 'Revenue')->first();
        $expenseType = AccountType::where('name', 'Expense')->first();

        // Get revenue accounts
        $revenues = $this->getAccountBalances($revenueType->id, $year, $month);
        $totalRevenue = $revenues->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        // Get expense accounts (separating COGS from operating expenses)
        $allExpenses = $this->getAccountBalances($expenseType->id, $year, $month);

        $cogs = $allExpenses->filter(function ($account) {
            return str_starts_with($account->account_code, '5');
        });

        $operatingExpenses = $allExpenses->filter(function ($account) {
            return str_starts_with($account->account_code, '6');
        });

        $totalCOGS = $cogs->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        $totalOperatingExpenses = $operatingExpenses->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        $grossProfit = $totalRevenue - $totalCOGS;
        $netIncome = $grossProfit - $totalOperatingExpenses;

        return [
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'year' => $year,
            'month' => $month,
            'revenue' => [
                'accounts' => $revenues,
                'total' => $totalRevenue,
            ],
            'cogs' => [
                'accounts' => $cogs,
                'total' => $totalCOGS,
            ],
            'gross_profit' => $grossProfit,
            'operating_expenses' => [
                'accounts' => $operatingExpenses,
                'total' => $totalOperatingExpenses,
            ],
            'net_income' => $netIncome,
        ];
    }

    /**
     * Generate Balance Sheet
     */
    public function generateBalanceSheet(int $year, int $month): array
    {
        $assetType = AccountType::where('name', 'Asset')->first();
        $liabilityType = AccountType::where('name', 'Liability')->first();
        $equityType = AccountType::where('name', 'Equity')->first();

        // Get all account balances
        $assets = $this->getAccountBalances($assetType->id, $year, $month);
        $liabilities = $this->getAccountBalances($liabilityType->id, $year, $month);
        $equity = $this->getAccountBalances($equityType->id, $year, $month);

        // Calculate totals
        $totalAssets = $assets->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        $totalLiabilities = $liabilities->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        $totalEquity = $equity->sum(function ($account) {
            return $account->balance->closing_balance ?? 0;
        });

        return [
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'year' => $year,
            'month' => $month,
            'assets' => [
                'accounts' => $assets,
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'accounts' => $liabilities,
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'accounts' => $equity,
                'total' => $totalEquity,
            ],
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
            'balance_check' => $totalAssets === ($totalLiabilities + $totalEquity),
        ];
    }

    /**
     * Generate Trial Balance
     */
    public function generateTrialBalance(int $year, int $month): array
    {
        $accounts = Account::with(['accountType', 'accountBalances' => function ($query) use ($year, $month) {
            $query->where('fiscal_year', $year)
                ->where('fiscal_period', $month);
        }])->where('is_active', true)->get();

        $totalDebits = 0;
        $totalCredits = 0;

        $accountBalances = $accounts->map(function ($account) use (&$totalDebits, &$totalCredits) {
            $balance = $account->accountBalances->first();

            $debitBalance = 0;
            $creditBalance = 0;

            if ($balance) {
                if ($account->accountType->normal_balance === 'debit') {
                    $debitBalance = $balance->closing_balance;
                } else {
                    $creditBalance = $balance->closing_balance;
                }
            }

            $totalDebits += $debitBalance;
            $totalCredits += $creditBalance;

            return [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->accountType->name,
                'debit_balance' => $debitBalance,
                'credit_balance' => $creditBalance,
            ];
        })->filter(function ($item) {
            return $item['debit_balance'] > 0 || $item['credit_balance'] > 0;
        })->values();

        return [
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'year' => $year,
            'month' => $month,
            'accounts' => $accountBalances,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'is_balanced' => round($totalDebits, 2) === round($totalCredits, 2),
        ];
    }

    /**
     * Generate General Ledger for a specific account
     */
    public function generateGeneralLedger(int $accountId, int $year, int $month): array
    {
        $account = Account::with('accountType')->findOrFail($accountId);

        $journalEntryLines = JournalEntryLine::with(['journalEntry' => function ($query) {
            $query->where('status', 'posted');
        }])
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', function ($query) use ($year, $month) {
                $query->where('fiscal_year', $year)
                    ->where('fiscal_period', $month)
                    ->where('status', 'posted');
            })
            ->get()
            ->sortBy(function ($line) {
                return $line->journalEntry->entry_date;
            });

        $balance = AccountBalance::where('account_id', $accountId)
            ->where('fiscal_year', $year)
            ->where('fiscal_period', $month)
            ->first();

        $runningBalance = $balance->opening_balance ?? 0;
        $transactions = [];

        foreach ($journalEntryLines as $line) {
            if ($account->accountType->normal_balance === 'debit') {
                $runningBalance += $line->debit_amount - $line->credit_amount;
            } else {
                $runningBalance += $line->credit_amount - $line->debit_amount;
            }

            $transactions[] = [
                'journal_entry_id' => $line->journalEntry->id,
                'date' => $line->journalEntry->entry_date,
                'entry_number' => $line->journalEntry->entry_number,
                'description' => $line->description ?? $line->journalEntry->description,
                'debit_amount' => $line->debit_amount,
                'credit_amount' => $line->credit_amount,
                'running_balance' => $runningBalance,
            ];
        }

        return [
            'account' => $account,
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'opening_balance' => $balance->opening_balance ?? 0,
            'closing_balance' => $balance->closing_balance ?? 0,
            'total_debits' => $balance->debit_total ?? 0,
            'total_credits' => $balance->credit_total ?? 0,
            'transactions' => $transactions,
        ];
    }

    /**
     * Get account balances for a specific account type
     */
    protected function getAccountBalances(int $accountTypeId, int $year, int $month): mixed
    {
        return Account::with(['accountBalances' => function ($query) use ($year, $month) {
            $query->where('fiscal_year', $year)
                ->where('fiscal_period', $month);
        }])
            ->where('account_type_id', $accountTypeId)
            ->where('is_active', true)
            ->get()
            ->map(function ($account) {
                $account->balance = $account->accountBalances->first();

                return $account;
            });
    }
}
