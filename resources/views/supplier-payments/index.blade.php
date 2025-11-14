@extends('layouts.app')

@section('title', 'Supplier Payments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Supplier Payments</h1>
        <a href="{{ route('supplier-payments.create') }}"
            class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border border-primary-700">
            <i class="fas fa-plus mr-2"></i>Record Payment
        </a>
    </div>

    <!-- Filters Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h2>
        <form action="{{ route('supplier-payments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Supplier Filter -->
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Supplier
                </label>
                <select name="supplier_id" id="supplier_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Suppliers</option>
                    @foreach(\App\Models\Supplier::orderBy('company_name')->get() as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->company_name }}
                        </option>
                    @endforeach
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

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Payment #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credit #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Supplier
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Payment Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Method
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Reference
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Processed By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $payment->payment_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('supplier-credits.show', $payment->supplierCredit) }}"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $payment->supplierCredit->credit_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $payment->supplier->company_name }}
                                    </div>
                                    @if($payment->supplier->contact_person)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $payment->supplier->contact_person }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $payment->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $payment->payment_method->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $payment->reference_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $payment->processedBy->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $payment->created_at->format('M d, Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('supplier-payments.show', $payment) }}"
                                    class="text-green-600 dark:text-white border border-green-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-green-600 hover:text-white dark:hover:bg-white dark:hover:text-green-600">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-600 mb-3 block"></i>
                                <p class="text-gray-500 dark:text-gray-400">No supplier payments found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
