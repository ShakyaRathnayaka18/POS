<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class JournalEntryService
{
    /**
     * Generate unique journal entry number in format: JE-YYYY-MM-###
     */
    public function generateEntryNumber(): string
    {
        $prefix = 'JE';
        $yearMonth = now()->format('Y-m');

        $lastEntry = JournalEntry::where('entry_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "{$prefix}-{$yearMonth}-{$newNumber}";
    }

    /**
     * Create a new journal entry
     *
     * @param  array  $data  [entry_date, description, lines[], reference_type?, reference_id?]
     *                       - lines: [account_id, debit_amount, credit_amount, description?]
     */
    public function createJournalEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $entryDate = Carbon::parse($data['entry_date']);

            $journalEntry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $entryDate,
                'fiscal_year' => $entryDate->year,
                'fiscal_period' => $entryDate->month,
                'description' => $data['description'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Add journal entry lines
            $totalDebits = 0;
            $totalCredits = 0;
            $lineNumber = 1;

            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $line['account_id'],
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                    'description' => $line['description'] ?? null,
                    'line_number' => $lineNumber++,
                ]);

                $totalDebits += $line['debit_amount'] ?? 0;
                $totalCredits += $line['credit_amount'] ?? 0;
            }

            // Validate balanced entry
            if (round($totalDebits, 2) !== round($totalCredits, 2)) {
                throw new Exception('Journal entry is not balanced. Debits must equal credits.');
            }

            return $journalEntry->fresh(['lines.account']);
        });
    }

    /**
     * Post a journal entry (make it permanent)
     */
    public function postJournalEntry(JournalEntry $journalEntry): JournalEntry
    {
        return DB::transaction(function () use ($journalEntry) {
            if ($journalEntry->status !== 'draft') {
                throw new Exception('Only draft journal entries can be posted.');
            }

            // Validate that debits equal credits
            $totalDebits = $journalEntry->lines->sum('debit_amount');
            $totalCredits = $journalEntry->lines->sum('credit_amount');

            if (round($totalDebits, 2) !== round($totalCredits, 2)) {
                throw new Exception('Journal entry is not balanced. Debits must equal credits.');
            }

            $journalEntry->update([
                'status' => 'posted',
                'approved_by' => auth()->id() ?? null, // Allow auto-posting without user
                'posted_at' => now(),
            ]);

            // Update account balances
            $this->updateAccountBalances($journalEntry);

            return $journalEntry->fresh();
        });
    }

    /**
     * Void a journal entry
     */
    public function voidJournalEntry(JournalEntry $journalEntry): JournalEntry
    {
        return DB::transaction(function () use ($journalEntry) {
            if ($journalEntry->status !== 'posted') {
                throw new Exception('Only posted journal entries can be voided.');
            }

            // Create reversal entry
            $this->createReversalEntry($journalEntry);

            $journalEntry->update([
                'status' => 'void',
                'voided_by' => auth()->id(),
                'voided_at' => now(),
            ]);

            return $journalEntry->fresh();
        });
    }

    /**
     * Create a reversal entry for a voided journal entry
     */
    protected function createReversalEntry(JournalEntry $originalEntry): JournalEntry
    {
        $reversalLines = [];

        foreach ($originalEntry->lines as $line) {
            $reversalLines[] = [
                'account_id' => $line->account_id,
                'debit_amount' => $line->credit_amount, // Swap debits and credits
                'credit_amount' => $line->debit_amount,
                'description' => 'Reversal of '.$originalEntry->entry_number,
            ];
        }

        return $this->createJournalEntry([
            'entry_date' => now(),
            'description' => 'VOID - Reversal of '.$originalEntry->entry_number.': '.$originalEntry->description,
            'lines' => $reversalLines,
            'reference_type' => JournalEntry::class,
            'reference_id' => $originalEntry->id,
        ]);
    }

    /**
     * Update account balances after posting a journal entry
     */
    protected function updateAccountBalances(JournalEntry $journalEntry): void
    {
        foreach ($journalEntry->lines as $line) {
            $balance = AccountBalance::firstOrCreate(
                [
                    'account_id' => $line->account_id,
                    'fiscal_year' => $journalEntry->fiscal_year,
                    'fiscal_period' => $journalEntry->fiscal_period,
                ],
                [
                    'opening_balance' => 0,
                    'debit_total' => 0,
                    'credit_total' => 0,
                    'closing_balance' => 0,
                ]
            );

            $balance->increment('debit_total', $line->debit_amount);
            $balance->increment('credit_total', $line->credit_amount);

            // Calculate closing balance based on account normal balance
            $account = Account::with('accountType')->find($line->account_id);
            $normalBalance = $account->accountType->normal_balance;

            if ($normalBalance === 'debit') {
                $balance->closing_balance = $balance->opening_balance + $balance->debit_total - $balance->credit_total;
            } else {
                $balance->closing_balance = $balance->opening_balance + $balance->credit_total - $balance->debit_total;
            }

            $balance->save();
        }
    }

    /**
     * Get journal entries for a period
     */
    public function getEntriesForPeriod(int $year, int $month, ?string $status = null): mixed
    {
        $query = JournalEntry::with(['lines.account', 'creator'])
            ->where('fiscal_year', $year)
            ->where('fiscal_period', $month);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('entry_date')->orderBy('entry_number')->get();
    }

    /**
     * Get account balance for a specific period
     */
    public function getAccountBalance(int $accountId, int $year, int $month): ?AccountBalance
    {
        return AccountBalance::where('account_id', $accountId)
            ->where('fiscal_year', $year)
            ->where('fiscal_period', $month)
            ->first();
    }
}
