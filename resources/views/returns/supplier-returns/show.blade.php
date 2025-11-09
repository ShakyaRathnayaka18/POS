@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                Supplier Return #{{ $supplierReturn->return_number }}
            </h1>
            <div class="flex items-center">
                <span class="text-sm font-semibold px-3 py-1 rounded-full
                    @if($supplierReturn->status === 'Pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($supplierReturn->status === 'Approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($supplierReturn->status === 'Completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($supplierReturn->status === 'Cancelled') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                    @endif">
                    {{ $supplierReturn->status }}
                </span>
                <a href="{{ route('supplier-returns.index') }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Return Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">GRN Number</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->goodReceiveNote->grn_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->supplier->company_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Return Date</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->return_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Return Reason</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->return_reason }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->notes ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->createdBy->name ?? 'N/A' }}</p>
                </div>
                @if($supplierReturn->approvedBy)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved By</p>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->approvedBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved At</p>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $supplierReturn->approved_at->format('F d, Y H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Return Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Returned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($supplierReturn->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->batch->batch_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->quantity_returned }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${{ number_format($item->item_total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->condition }}</td>
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
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($supplierReturn->subtotal, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($supplierReturn->total, 2) }}</p>
                </div>
            </div>
        </div>

        @if($supplierReturn->status === 'Pending')
            <div class="flex justify-end">
                <form action="{{ route('supplier-returns.cancel', $supplierReturn) }}" method="POST" class="mr-2">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
                </form>
                <form action="{{ route('supplier-returns.approve', $supplierReturn) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Approve</button>
                </form>
            </div>
        @elseif($supplierReturn->status === 'Approved')
            <div class="flex justify-end">
                <form action="{{ route('supplier-returns.complete', $supplierReturn) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Mark as Complete</button>
                </form>
            </div>
        @endif
    </div>
@endsection
