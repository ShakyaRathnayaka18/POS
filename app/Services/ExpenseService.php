<?php

namespace App\Services;

use App\Enums\ExpenseStatusEnum;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    /**
     * Generate unique expense number in format: EXP-YYYY-MM-###
     */
    public function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $yearMonth = now()->format('Y-m');

        $lastExpense = Expense::where('expense_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('expense_number', 'desc')
            ->first();

        if ($lastExpense) {
            $lastNumber = (int) substr($lastExpense->expense_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "{$prefix}-{$yearMonth}-{$newNumber}";
    }

    /**
     * Create a new expense
     */
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expenseData = [
                'expense_number' => $this->generateExpenseNumber(),
                'expense_category_id' => $data['expense_category_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'amount' => $data['amount'],
                'expense_date' => $data['expense_date'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'status' => ExpenseStatusEnum::PENDING,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ];

            if (isset($data['receipt'])) {
                $expenseData['receipt_path'] = $this->storeReceipt($data['receipt']);
            }

            return Expense::create($expenseData);
        });
    }

    /**
     * Update an existing expense
     */
    public function updateExpense(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $updateData = [
                'expense_category_id' => $data['expense_category_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'amount' => $data['amount'],
                'expense_date' => $data['expense_date'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            if (isset($data['receipt'])) {
                if ($expense->receipt_path) {
                    Storage::disk('public')->delete($expense->receipt_path);
                }
                $updateData['receipt_path'] = $this->storeReceipt($data['receipt']);
            }

            $expense->update($updateData);

            return $expense->fresh();
        });
    }

    /**
     * Approve an expense
     */
    public function approveExpense(Expense $expense): Expense
    {
        return DB::transaction(function () use ($expense) {
            $expense->update([
                'status' => ExpenseStatusEnum::APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return $expense->fresh();
        });
    }

    /**
     * Reject an expense
     */
    public function rejectExpense(Expense $expense): Expense
    {
        return DB::transaction(function () use ($expense) {
            $expense->update([
                'status' => ExpenseStatusEnum::REJECTED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return $expense->fresh();
        });
    }

    /**
     * Mark an expense as paid
     */
    public function markAsPaid(Expense $expense): Expense
    {
        return DB::transaction(function () use ($expense) {
            $expense->update([
                'status' => ExpenseStatusEnum::PAID,
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            return $expense->fresh();
        });
    }

    /**
     * Store receipt file
     */
    protected function storeReceipt($file): string
    {
        return $file->store('expense-receipts', 'public');
    }

    /**
     * Delete receipt file
     */
    public function deleteReceipt(Expense $expense): void
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
    }

    /**
     * Get expense statistics
     */
    public function getStatistics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Expense::query();

        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        return [
            'total_expenses' => $query->clone()->sum('amount'),
            'pending_approvals' => $query->clone()->where('status', ExpenseStatusEnum::PENDING)->count(),
            'approved_count' => $query->clone()->where('status', ExpenseStatusEnum::APPROVED)->count(),
            'paid_count' => $query->clone()->where('status', ExpenseStatusEnum::PAID)->count(),
            'rejected_count' => $query->clone()->where('status', ExpenseStatusEnum::REJECTED)->count(),
            'paid_this_month' => Expense::where('status', ExpenseStatusEnum::PAID)
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
        ];
    }

    /**
     * Get expenses by category
     */
    public function getExpensesByCategory(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Expense::with('category')
            ->where('status', ExpenseStatusEnum::PAID);

        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        return $query->get()
            ->groupBy('expense_category_id')
            ->map(function ($expenses) {
                return [
                    'category' => $expenses->first()->category->category_name,
                    'total' => $expenses->sum('amount'),
                    'count' => $expenses->count(),
                ];
            })
            ->values()
            ->toArray();
    }
}
