@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expense Details</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $expense->expense_number }}</p>
        </div>
        <div class="flex gap-2">
            @if($expense->status === App\Enums\ExpenseStatusEnum::PENDING)
                <a href="{{ route('expenses.edit', $expense) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('expenses.index') }}"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Status</h2>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $expense->status->badgeColor() }}">
                    {{ $expense->status->description() }}
                </span>
            </div>
            
            @can('manage expenses')
                <div class="flex gap-2">
                    @if($expense->status === App\Enums\ExpenseStatusEnum::PENDING)
                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Approve this expense?')"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>
                        <form action="{{ route('expenses.reject', $expense) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject this expense?')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </form>
                    @elseif($expense->status === App\Enums\ExpenseStatusEnum::APPROVED)
                        <form action="{{ route('expenses.mark-as-paid', $expense) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Mark this expense as paid?')"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <i class="fas fa-money-bill mr-2"></i>Mark as Paid
                            </button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>
    </div>

    <!-- Expense Information -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Title:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->title }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Category:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->category->category_name }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Amount:</span>
                <span class="text-gray-900 dark:text-white font-semibold ml-2">LKR {{ number_format($expense->amount, 2) }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Expense Date:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->expense_date->format('F d, Y') }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Payment Method:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ ucwords(str_replace('_', ' ', $expense->payment_method->value)) }}</span>
            </div>
            @if($expense->reference_number)
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Reference Number:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->reference_number }}</span>
            </div>
            @endif
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Created By:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->creator->name }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Created At:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $expense->created_at->format('F d, Y H:i') }}</span>
            </div>
        </div>

        @if($expense->description)
        <div class="mt-4">
            <span class="font-medium text-gray-700 dark:text-gray-300">Description:</span>
            <p class="text-gray-900 dark:text-white mt-2">{{ $expense->description }}</p>
        </div>
        @endif

        @if($expense->notes)
        <div class="mt-4">
            <span class="font-medium text-gray-700 dark:text-gray-300">Notes:</span>
            <p class="text-gray-900 dark:text-white mt-2">{{ $expense->notes }}</p>
        </div>
        @endif

        @if($expense->receipt_path)
        <div class="mt-4">
            <span class="font-medium text-gray-700 dark:text-gray-300">Receipt:</span>
            <div class="mt-2">
                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    <i class="fas fa-file mr-2"></i>View Receipt
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Approval/Payment History -->
    @if($expense->status !== App\Enums\ExpenseStatusEnum::PENDING)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">History</h2>
        <div class="space-y-3">
            @if($expense->approved_by)
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $expense->status === App\Enums\ExpenseStatusEnum::REJECTED ? 'Rejected' : 'Approved' }} by {{ $expense->approver->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $expense->approved_at->format('F d, Y H:i') }}
                    </p>
                </div>
            </div>
            @endif

            @if($expense->paid_by)
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-money-bill text-white text-xs"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Paid by {{ $expense->payer->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $expense->paid_at->format('F d, Y H:i') }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Delete Button -->
    @can('manage expenses')
        @if($expense->status !== App\Enums\ExpenseStatusEnum::PAID)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">Danger Zone</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Once deleted, this expense cannot be recovered.</p>
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this expense?')"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete Expense
                </button>
            </form>
        </div>
        @endif
    @endcan
</div>
@endsection
