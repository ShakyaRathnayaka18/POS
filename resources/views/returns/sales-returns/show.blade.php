@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Sales Return #{{ $salesReturn->return_number }}
            </h1>
            <div class="flex items-center">
                <span class="text-sm font-semibold px-3 py-1 rounded-full
                    @if($salesReturn->status === 'Pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($salesReturn->status === 'Approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($salesReturn->status === 'Refunded') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($salesReturn->status === 'Cancelled') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                    @endif">
                    {{ $salesReturn->status }}
                </span>
                <a href="{{ route('sales-returns.index') }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Return Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sale Number</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->sale->sale_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer Name</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->customer_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Return Date</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->return_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Return Reason</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->return_reason }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->notes ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Processed By</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $salesReturn->processedBy->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Return Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Returned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Restored to Stock</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($salesReturn->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->quantity_returned }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${{ number_format($item->selling_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${{ number_format($item->item_total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->condition }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->restore_to_stock ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Financial Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-start-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($salesReturn->subtotal, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($salesReturn->total, 2) }}</p>
                </div>
                @if($salesReturn->status === 'Refunded')
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Refund Amount</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($salesReturn->refund_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Refund Method</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $salesReturn->refund_method }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($salesReturn->status === 'Pending')
            <div class="flex justify-end">
                <form action="{{ route('sales-returns.cancel', $salesReturn) }}" method="POST" class="mr-2">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
                </form>
                <form action="{{ route('sales-returns.refund', $salesReturn) }}" method="POST">
                    @csrf
                    <div class="flex items-center">
                        <select name="refund_method" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option>Cash</option>
                            <option>Card</option>
                            <option>Store Credit</option>
                        </select>
                        <input type="number" name="refund_amount" value="{{ $salesReturn->total }}" step="0.01" class="ml-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <button type="submit" class="ml-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Process Refund</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
