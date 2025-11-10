@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('sales-returns.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4 inline-block">
            &larr; Back to Sales Returns
        </a>
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Sales Return: {{ $salesReturn->return_number }}</h1>
            @php
                $statusColor = [
                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'Approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'Refunded' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'Cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                ][$salesReturn->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-4 py-2 text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">{{ $salesReturn->status }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Return Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Original Sale ID:</span> <a href="#" class="text-indigo-600 dark:text-indigo-400">{{ $salesReturn->sale_id }}</a></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Customer:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->customer_name ?? 'N/A' }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Return Date:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->return_date->format('F d, Y') }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Return Reason:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->return_reason }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Processed By:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->processedBy->name ?? 'N/A' }}</span></div>
                @if($salesReturn->processed_at)
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Processed At:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->processed_at->format('F d, Y H:i') }}</span></div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Financials</h2>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Subtotal:</span> <span class="text-gray-900 dark:text-gray-200">${{ number_format($salesReturn->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Tax:</span> <span class="text-gray-900 dark:text-gray-200">${{ number_format($salesReturn->tax, 2) }}</span></div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                    <span class="font-bold text-gray-900 dark:text-white text-lg">Return Total:</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-white">${{ number_format($salesReturn->total, 2) }}</span>
                </div>
                <div class="flex justify-between text-green-600 dark:text-green-400">
                    <span class="font-bold text-lg">Refund Amount:</span>
                    <span class="font-bold text-lg">${{ number_format($salesReturn->refund_amount, 2) }}</span>
                </div>
                @if($salesReturn->refund_method)
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Refund Method:</span> <span class="text-gray-900 dark:text-gray-200">{{ $salesReturn->refund_method }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Returned Items</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Returned</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Condition</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock Restored</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($salesReturn->items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->product->product_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->quantity_returned }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">${{ number_format($item->selling_price, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">${{ number_format($item->item_total, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->condition }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($item->restore_to_stock)
                                <span class="text-green-600">Yes</span>
                            @else
                                <span class="text-red-600">No</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($salesReturn->status === 'Pending' || $salesReturn->status === 'Approved')
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Process Refund</h2>
        <form action="{{ route('sales-returns.refund', $salesReturn) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="refund_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refund Amount</label>
                    <input type="number" name="refund_amount" id="refund_amount" value="{{ $salesReturn->total }}" max="{{ $salesReturn->total }}" step="0.01" class="w-full form-input mt-1">
                </div>
                <div>
                    <label for="refund_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refund Method</label>
                    <select name="refund_method" id="refund_method" class="w-full form-select mt-1">
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Store Credit</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Process Refund</button>
                </div>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
