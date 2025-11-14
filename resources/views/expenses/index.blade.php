@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses Management</h1>
        <a href="{{ route('expenses.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>Add Expense
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">LKR {{ number_format($statistics['total_expenses'], 2) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Approval</div>
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $statistics['pending_approvals'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistics['approved_count'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid This Month</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">LKR {{ number_format($statistics['paid_this_month'], 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Expense #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($expenses as $expense)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $expense->expense_number }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $expense->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $expense->category->category_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                        LKR {{ number_format($expense->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $expense->expense_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $expense->status->badgeColor() }}">
                            {{ $expense->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No expenses found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="bg-white dark:bg-gray-800 px-4 py-3 rounded-lg">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
