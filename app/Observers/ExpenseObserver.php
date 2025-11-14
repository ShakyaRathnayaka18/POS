<?php

namespace App\Observers;

use App\Enums\ExpenseStatusEnum;
use App\Models\Expense;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
{
    public function __construct(protected TransactionIntegrationService $transactionIntegrationService) {}

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        try {
            // Only create journal entry if expense is paid
            if ($expense->status === ExpenseStatusEnum::PAID) {
                $this->transactionIntegrationService->createExpenseJournalEntry($expense);
            }
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for expense: '.$e->getMessage(), [
                'expense_id' => $expense->id,
                'expense_number' => $expense->expense_number,
            ]);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        try {
            // If expense status changed to paid, create journal entry
            if ($expense->wasChanged('status') && $expense->status === ExpenseStatusEnum::PAID) {
                $this->transactionIntegrationService->createExpenseJournalEntry($expense);
            }
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for updated expense: '.$e->getMessage(), [
                'expense_id' => $expense->id,
                'expense_number' => $expense->expense_number,
            ]);
        }
    }
}
