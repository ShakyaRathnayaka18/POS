@extends('layouts.app')

@section('title', 'Supplier Credits')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Supplier Credits</h1>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Outstanding -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-invoice-dollar text-3xl text-orange-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Outstanding</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        LKR {{ number_format($totalOutstanding, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Overdue Credits -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue Credits</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overdueCount }}</p>
                </div>
            </div>
        </div>

        <!-- Due Soon -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-3xl text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Soon (7 days)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dueSoonCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h2>
        <form action="{{ route('supplier-credits.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Supplier Filter -->
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Supplier
                </label>
                <select name="supplier_id" id="supplier_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <!-- Date Range (From) -->
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    From Date
                </label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Date Range (To) -->
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    To Date
                </label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border border-primary-700">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Credits Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credit #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Supplier
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Invoice #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Invoice Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Due Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Original Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Outstanding
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($credits as $credit)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $credit->credit_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $credit->supplier->company_name }}
                                    </div>
                                    @if($credit->supplier->contact_person)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $credit->supplier->contact_person }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $credit->invoice_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $credit->invoice_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $credit->due_date->format('M d, Y') }}
                                </div>
                                @if($credit->isOverdue() && $credit->status->value !== 'paid')
                                    <span class="text-xs text-red-600 dark:text-red-400">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Overdue
                                    </span>
                                @elseif($credit->isDueSoon())
                                    <span class="text-xs text-yellow-600 dark:text-yellow-400">
                                        <i class="fas fa-clock mr-1"></i>Due Soon
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($credit->original_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($credit->outstanding_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $credit->status->badgeColor() }}">
                                    {{ $credit->status->description() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('supplier-credits.show', $credit) }}"
                                    class="text-green-600 dark:text-white border border-green-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-green-600 hover:text-white dark:hover:bg-white dark:hover:text-green-600">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                @if($credit->status->value !== 'paid')
                                    <a href="{{ route('supplier-payments.create', ['credit_id' => $credit->id]) }}"
                                        class="text-blue-600 dark:text-white border border-blue-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-blue-600 hover:text-white dark:hover:bg-white dark:hover:text-blue-600">
                                        <i class="fas fa-money-bill mr-1"></i>Pay
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-600 mb-3 block"></i>
                                <p class="text-gray-500 dark:text-gray-400">No supplier credits found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($credits->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $credits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
