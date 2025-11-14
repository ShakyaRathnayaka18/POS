<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(protected ExpenseService $expenseService) {}

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request): View
    {
        $query = Expense::with(['category', 'creator', 'approver', 'payer']);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);

        $categories = ExpenseCategory::where('is_active', true)->get();
        $statuses = ExpenseStatusEnum::cases();

        $statistics = $this->expenseService->getStatistics();

        return view('expenses.index', compact('expenses', 'categories', 'statuses', 'statistics'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(): View
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $paymentMethods = PaymentMethodEnum::cases();
        $expenseNumber = $this->expenseService->generateExpenseNumber();

        return view('expenses.create', compact('categories', 'paymentMethods', 'expenseNumber'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(StoreExpenseRequest $request)
    {
        try {
            $expense = $this->expenseService->createExpense($request->validated());

            return redirect()
                ->route('expenses.show', $expense)
                ->with('success', 'Expense created successfully! Awaiting approval.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error creating expense: '.$e->getMessage());
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): View
    {
        $expense->load(['category', 'creator', 'approver', 'payer']);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense): View
    {
        if ($expense->status !== ExpenseStatusEnum::PENDING) {
            return back()->with('error', 'Only pending expenses can be edited.');
        }

        $categories = ExpenseCategory::where('is_active', true)->get();
        $paymentMethods = PaymentMethodEnum::cases();

        return view('expenses.edit', compact('expense', 'categories', 'paymentMethods'));
    }

    /**
     * Update the specified expense.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        if ($expense->status !== ExpenseStatusEnum::PENDING) {
            return back()->with('error', 'Only pending expenses can be updated.');
        }

        try {
            $expense = $this->expenseService->updateExpense($expense, $request->validated());

            return redirect()
                ->route('expenses.show', $expense)
                ->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating expense: '.$e->getMessage());
        }
    }

    /**
     * Approve an expense.
     */
    public function approve(Expense $expense)
    {
        if ($expense->status !== ExpenseStatusEnum::PENDING) {
            return back()->with('error', 'Only pending expenses can be approved.');
        }

        try {
            $this->expenseService->approveExpense($expense);

            return back()->with('success', 'Expense approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving expense: '.$e->getMessage());
        }
    }

    /**
     * Reject an expense.
     */
    public function reject(Expense $expense)
    {
        if ($expense->status !== ExpenseStatusEnum::PENDING) {
            return back()->with('error', 'Only pending expenses can be rejected.');
        }

        try {
            $this->expenseService->rejectExpense($expense);

            return back()->with('success', 'Expense rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting expense: '.$e->getMessage());
        }
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid(Expense $expense)
    {
        if ($expense->status !== ExpenseStatusEnum::APPROVED) {
            return back()->with('error', 'Only approved expenses can be marked as paid.');
        }

        try {
            $this->expenseService->markAsPaid($expense);

            return back()->with('success', 'Expense marked as paid successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error marking expense as paid: '.$e->getMessage());
        }
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense)
    {
        if ($expense->status === ExpenseStatusEnum::PAID) {
            return back()->with('error', 'Paid expenses cannot be deleted.');
        }

        try {
            $expense->delete();

            return redirect()
                ->route('expenses.index')
                ->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting expense: '.$e->getMessage());
        }
    }

    /**
     * Store a new expense category (AJAX).
     */
    public function storeCategory(StoreExpenseCategoryRequest $request)
    {
        try {
            $category = ExpenseCategory::create($request->validated());

            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Category created successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all active categories (AJAX).
     */
    public function getCategories()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();

        return response()->json($categories);
    }
}
