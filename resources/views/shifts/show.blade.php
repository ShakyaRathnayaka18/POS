@extends('layouts.app')

@section('title', 'Shift Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Shift Details</h1>
        <div class="flex gap-2">
            @can('approve shifts')
                @if($shift->status->value === 'completed')
                    <form action="{{ route('shifts.approve', $shift) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-check mr-2"></i>Approve Shift
                        </button>
                    </form>
                @endif
            @endcan
            <a href="{{ auth()->user()->can('view shifts') ? route('shifts.index') : route('shifts.my-shifts') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shift Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Shift Information
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Shift Number</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $shift->shift_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Cashier</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $shift->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Clock In</label>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $shift->clock_in_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Clock Out</label>
                        <p class="text-lg text-gray-900 dark:text-white">
                            {{ $shift->clock_out_at ? $shift->clock_out_at->format('M d, Y h:i A') : 'Active' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $shift->getFormattedDuration() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $shift->status->badgeColor() }} text-white">
                            {{ ucfirst($shift->status->value) }}
                        </span>
                    </div>
                </div>

                @if($shift->notes)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</label>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $shift->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sales List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-receipt mr-2 text-green-500"></i>
                    Sales During Shift ({{ $shift->sales->count() }})
                </h2>
                @if($shift->sales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sale #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Payment</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($shift->sales as $sale)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $sale->sale_number }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sale->created_at->format('h:i A') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sale->customer_name ?? 'Walk-in' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sale->payment_method }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900 dark:text-white">
                                            ${{ number_format($sale->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No sales recorded during this shift</p>
                @endif
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="space-y-6">
            <!-- Sales Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Sales Summary</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Total Sales</span>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                            ${{ number_format($statistics['total_sales'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Transactions</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $statistics['total_sales_count'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Sales/Hour</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($statistics['sales_per_hour'], 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Payment Methods</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>Cash
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($statistics['cash_sales'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-credit-card text-blue-500 mr-2"></i>Card
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($statistics['card_sales'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-file-invoice text-orange-500 mr-2"></i>Credit
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($statistics['credit_sales'], 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cash Drawer -->
            @if($shift->clock_out_at)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Cash Drawer</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Opening Cash</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($shift->opening_cash ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Cash Sales</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($statistics['cash_sales'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Expected Cash</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($shift->expected_cash ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Closing Cash</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($shift->closing_cash ?? 0, 2) }}
                            </span>
                        </div>
                        @if($shift->cash_difference !== null)
                            <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Difference</span>
                                <span class="text-lg font-bold {{ $shift->cash_difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $shift->cash_difference >= 0 ? '+' : '' }}${{ number_format($shift->cash_difference, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
