@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('batches.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Back to Batches
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Batch Details: {{ $batch->batch_number }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Batch Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="font-medium text-gray-700">Batch Number:</span>
                    <span class="text-gray-900">{{ $batch->batch_number }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Barcode:</span>
                    <span class="text-gray-900 font-mono">{{ $batch->barcode ?? 'Not assigned' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">GRN Number:</span>
                    <span class="text-gray-900">{{ $batch->goodReceiveNote->grn_number }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Supplier:</span>
                    <span class="text-gray-900">{{ $batch->goodReceiveNote->supplier->company_name }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Manufacture Date:</span>
                    <span class="text-gray-900">{{ $batch->manufacture_date?->format('Y-m-d') ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Expiry Date:</span>
                    <span class="text-gray-900">{{ $batch->expiry_date?->format('Y-m-d') ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Notes:</span>
                    <span class="text-gray-900">{{ $batch->notes ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Stock Summary</h2>
            <div class="space-y-3">
                <div>
                    <span class="font-medium text-gray-700">Total Quantity:</span>
                    <span class="text-gray-900 text-2xl font-bold">{{ $totalQuantity }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Available Quantity:</span>
                    <span class="text-gray-900 text-2xl font-bold text-green-600">{{ $availableQuantity }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Batch Value:</span>
                    <span class="text-gray-900 text-2xl font-bold">${{ number_format($batchValue, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Stock Items</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($batch->stocks as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $stock->product->product_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $stock->product->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${{ number_format($stock->cost_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${{ number_format($stock->selling_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $stock->tax }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $stock->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="{{ $stock->available_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $stock->available_quantity }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
