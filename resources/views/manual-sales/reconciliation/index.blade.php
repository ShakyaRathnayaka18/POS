@extends('layouts.app')

@section('title', 'Manual Sales Reconciliation Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-tasks mr-2"></i> Manual Sales Reconciliation
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Match manual sale items to actual products and convert to regular sales
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-4xl text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Reconciliation</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingManualSales->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-4xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Items</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingManualSales->sum(fn($sale) => $sale->items->count()) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-4xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">LKR {{ number_format($pendingManualSales->sum('total'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Manual Sales Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Pending Manual Sales
                </h2>
            </div>

            @if ($pendingManualSales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Sale #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Cashier</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Payment</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($pendingManualSales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                                        {{ $sale->manual_sale_number }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $sale->created_at->format('M d, Y') }}<br>
                                        <span class="text-xs text-gray-500">{{ $sale->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $sale->user->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        @if ($sale->customer_name)
                                            <div>{{ $sale->customer_name }}</div>
                                            @if ($sale->customer_phone)
                                                <div class="text-xs text-gray-500">{{ $sale->customer_phone }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Walk-in Customer</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-700 dark:text-gray-300">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $sale->items->count() }} item(s)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900 dark:text-white">
                                        LKR {{ number_format($sale->total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                            {{ $sale->payment_method->value === 'cash' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $sale->payment_method->value === 'card' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $sale->payment_method->value === 'credit' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}">
                                            <i class="fas fa-{{ $sale->payment_method->value === 'cash' ? 'money-bill-wave' : ($sale->payment_method->value === 'card' ? 'credit-card' : 'handshake') }} mr-1"></i>
                                            {{ $sale->payment_method->value }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('manual-sales.reconciliation.show', $sale) }}"
                                           class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md font-medium transition-colors">
                                            <i class="fas fa-tasks mr-2"></i> Reconcile
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-6xl text-green-300 dark:text-green-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">All manual sales have been reconciled!</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">No pending manual sales require reconciliation at this time.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
