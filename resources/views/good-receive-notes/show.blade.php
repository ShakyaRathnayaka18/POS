@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('good-receive-notes.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mb-4 inline-block">
            ← Back to GRNs
        </a>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">GRN Details: {{ $goodReceiveNote->grn_number }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">GRN Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">GRN Number:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->grn_number }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Supplier:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->supplier->company_name }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Contact:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->supplier->phone }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Received Date:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->received_date->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Invoice Number:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->invoice_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Invoice Date:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->invoice_date ? $goodReceiveNote->invoice_date->format('F d, Y') : '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Payment Type:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $goodReceiveNote->payment_type === 'cash' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400' }}">
                        {{ ucfirst($goodReceiveNote->payment_type ?? 'cash') }}
                    </span>
                </div>
                @if($goodReceiveNote->is_credit && $goodReceiveNote->supplierCredit)
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Credit Status:</span>
                    <a href="{{ route('supplier-credits.show', $goodReceiveNote->supplierCredit) }}"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline">
                        {{ $goodReceiveNote->supplierCredit->credit_number }} -
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $goodReceiveNote->supplierCredit->status->badgeColor() }}">
                            {{ $goodReceiveNote->supplierCredit->status->description() }}
                        </span>
                    </a>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Outstanding:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">LKR {{ number_format($goodReceiveNote->supplierCredit->outstanding_amount, 2) }}</span>
                </div>
                @endif
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $goodReceiveNote->status === 'Received' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400' }}">
                        {{ $goodReceiveNote->status }}
                    </span>
                </div>
                @if($goodReceiveNote->notes)
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Notes:</span>
                    <p class="text-gray-900 dark:text-white mt-1">{{ $goodReceiveNote->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Subtotal Before Discount:</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($goodReceiveNote->subtotal_before_discount, 2) }}</span>
                </div>
                @if($goodReceiveNote->discount > 0)
                <div class="flex justify-between bg-green-50 dark:bg-green-900/30 -mx-2 px-2 py-1 rounded">
                    <span class="font-medium text-green-700 dark:text-green-300">Discount:</span>
                    <span class="text-green-700 dark:text-green-300">- LKR {{ number_format($goodReceiveNote->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Subtotal:</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($goodReceiveNote->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Tax:</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($goodReceiveNote->tax, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Shipping:</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($goodReceiveNote->shipping, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                    <span class="font-bold text-gray-900 dark:text-white text-lg">Total:</span>
                    <span class="font-bold text-green-600 dark:text-green-400 text-lg">LKR {{ number_format($goodReceiveNote->total, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Batches Created:</span>
                    <span class="text-gray-900 dark:text-white">{{ $goodReceiveNote->batches->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Batches</h2>
        @foreach($goodReceiveNote->batches as $batch)
        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $batch->batch_number }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Manufacture: {{ $batch->manufacture_date?->format('Y-m-d') ?? 'N/A' }} |
                        Expiry: {{ $batch->expiry_date?->format('Y-m-d') ?? 'N/A' }}
                    </p>
                </div>
                <a href="{{ route('batches.show', $batch) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    View Batch Details →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-600">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cost Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Selling Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tax %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Available</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($batch->stocks as $stock)
                        <tr class="{{ $stock->cost_price == 0 ? 'bg-green-50 dark:bg-green-900/10' : '' }}">
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                {{ $stock->product->product_name }}
                                @if($stock->cost_price == 0)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                        FREE
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $stock->product->sku }}</td>
                            <td class="px-4 py-2 text-sm {{ $stock->cost_price == 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                                LKR {{ number_format($stock->cost_price, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">LKR {{ number_format($stock->selling_price, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $stock->tax }}%</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $stock->quantity }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="{{ $stock->available_quantity > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $stock->available_quantity }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($batch->stocks->where('cost_price', 0)->count() > 0)
                <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded">
                    <p class="text-sm text-green-800 dark:text-green-300">
                        <i class="fas fa-gift mr-1"></i>
                        <strong>Free Items:</strong> {{ $batch->stocks->where('cost_price', 0)->sum('quantity') }} units received as FOC
                    </p>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
