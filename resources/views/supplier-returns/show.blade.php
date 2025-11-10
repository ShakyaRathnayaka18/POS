@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('supplier-returns.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4 inline-block">
            &larr; Back to Supplier Returns
        </a>
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Return: {{ $supplierReturn->return_number }}</h1>
            @php
                $statusColor = [
                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'Approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'Cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                ][$supplierReturn->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-4 py-2 text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                {{ $supplierReturn->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Return Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><span class="font-medium text-gray-700 dark:text-gray-300">GRN Number:</span> <a href="{{ route('good-receive-notes.show', $supplierReturn->goodReceiveNote) }}" class="text-indigo-600 dark:text-indigo-400">{{ $supplierReturn->goodReceiveNote->grn_number }}</a></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Supplier:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->supplier->company_name }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Return Date:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->return_date->format('F d, Y') }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Return Reason:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->return_reason }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Created By:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->createdBy->name ?? 'System' }}</span></div>
                @if($supplierReturn->approvedBy)
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Approved By:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->approvedBy->name }}</span></div>
                <div><span class="font-medium text-gray-700 dark:text-gray-300">Approved At:</span> <span class="text-gray-900 dark:text-gray-200">{{ $supplierReturn->approved_at->format('F d, Y H:i') }}</span></div>
                @endif
                @if($supplierReturn->notes)
                <div class="sm:col-span-2"><span class="font-medium text-gray-700 dark:text-gray-300">Notes:</span> <p class="text-gray-900 dark:text-gray-200 mt-1">{{ $supplierReturn->notes }}</p></div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Financial Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Subtotal:</span> <span class="text-gray-900 dark:text-gray-200">${{ number_format($supplierReturn->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Tax:</span> <span class="text-gray-900 dark:text-gray-200">${{ number_format($supplierReturn->tax, 2) }}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-700 dark:text-gray-300">Adjustment:</span> <span class="text-gray-900 dark:text-gray-200">${{ number_format($supplierReturn->adjustment, 2) }}</span></div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                    <span class="font-bold text-gray-900 dark:text-white text-lg">Total:</span>
                    <span class="font-bold text-green-600 dark:text-green-400 text-lg">${{ number_format($supplierReturn->total, 2) }}</span>
                </div>
            </div>
            <div class="mt-6">
                @if($supplierReturn->status === 'Pending')
                    <form action="{{ route('supplier-returns.approve', $supplierReturn) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-2">Approve</button>
                    </form>
                    <form action="{{ route('supplier-returns.cancel', $supplierReturn) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Cancel</button>
                    </form>
                @elseif($supplierReturn->status === 'Approved')
                    <form action="{{ route('supplier-returns.complete', $supplierReturn) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Mark as Completed</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Returned Items</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Batch</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Returned</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cost Price</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tax %</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Condition</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($supplierReturn->items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->product->product_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $item->batch->batch_number }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->quantity_returned }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">${{ number_format($item->cost_price, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->tax }}%</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">${{ number_format($item->item_total, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $item->condition }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
