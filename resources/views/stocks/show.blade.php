@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('stocks.index', ['page' => $currentPage ?? 1]) }}"
                class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
                ← Back to Stocks
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Stock Details</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Product Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-gray-700">Product Name:</span>
                        <span class="text-gray-900">{{ $stock->product->product_name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">SKU:</span>
                        <span class="text-gray-900">{{ $stock->product->sku }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Category:</span>
                        <span class="text-gray-900">{{ $stock->product->category->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Brand:</span>
                        <span class="text-gray-900">{{ $stock->product->brand->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Description:</span>
                        <p class="text-gray-900 mt-1">{{ $stock->product->description ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Batch Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Batch Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-gray-700">Batch Number:</span>
                        <span class="text-gray-900">{{ $stock->batch->batch_number }}</span>
                    </div>
                    <div x-data="{ editing: false }">
                        <span class="font-medium text-gray-700">Barcode:</span>
                        <template x-if="!editing">
                            <span>
                                <span class="text-gray-900 font-mono">{{ $stock->batch->barcode ?? 'Not assigned' }}</span>
                                <button @click="editing = true" class="ml-2 text-indigo-600 hover:text-indigo-900 text-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </span>
                        </template>
                        <template x-if="editing">
                            <form action="{{ route('stocks.update-barcode', $stock) }}" method="POST"
                                class="inline-flex items-center gap-2 mt-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="current_page" value="{{ $currentPage ?? 1 }}">
                                <input type="text" name="barcode" value="{{ $stock->batch->barcode }}"
                                    class="border border-gray-300 rounded px-2 py-1 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Enter barcode">
                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">Save</button>
                                <button type="button" @click="editing = false"
                                    class="text-gray-600 hover:text-gray-800 text-sm">Cancel</button>
                            </form>
                        </template>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">GRN Number:</span>
                        <a href="{{ route('good-receive-notes.show', $stock->batch->goodReceiveNote) }}"
                            class="text-indigo-600 hover:text-indigo-900">
                            {{ $stock->batch->goodReceiveNote->grn_number }}
                        </a>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Supplier:</span>
                        <span class="text-gray-900">{{ $stock->batch->goodReceiveNote->supplier->company_name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Manufacture Date:</span>
                        <span class="text-gray-900">{{ $stock->batch->manufacture_date?->format('Y-m-d') ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Expiry Date:</span>
                        @if ($stock->batch->expiry_date)
                            <span
                                class="{{ $stock->batch->expiry_date < now() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                {{ $stock->batch->expiry_date->format('Y-m-d') }}
                                @if ($stock->batch->expiry_date < now())
                                    (Expired)
                                @elseif($stock->batch->expiry_date < now()->addDays(30))
                                    (Expiring Soon)
                                @endif
                            </span>
                        @else
                            <span class="text-gray-900">N/A</span>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('batches.show', $stock->batch) }}"
                            class="text-indigo-600 hover:text-indigo-900 inline-block mt-2">
                            View Full Batch Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Pricing Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Pricing & Financial</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-medium text-gray-700">Cost Price:</span>
                        <span class="text-gray-900 text-lg">LKR {{ number_format($stock->cost_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-medium text-gray-700">Selling Price:</span>
                        <span class="text-gray-900 text-lg">LKR {{ number_format($stock->selling_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-medium text-gray-700">Tax:</span>
                        <span class="text-gray-900 text-lg">{{ $stock->tax }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-medium text-gray-700">Profit Margin:</span>
                        <span class="text-green-600 text-lg font-semibold">
                            LKR {{ number_format($profitMargin, 2) }} ({{ number_format($profitPercentage, 1) }}%)
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 bg-blue-50 px-3 rounded">
                        <span class="font-bold text-gray-900">Total Value:</span>
                        <span class="text-blue-600 text-xl font-bold">LKR {{ number_format($totalValue, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 bg-green-50 px-3 rounded">
                        <span class="font-bold text-gray-900">Potential Revenue:</span>
                        <span class="text-green-600 text-xl font-bold">LKR {{ number_format($potentialRevenue, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quantity Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Quantity & Status</h2>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-700">Total Quantity:</span>
                            <span class="text-gray-900 text-2xl font-bold">{{ number_format($stock->quantity, 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-700">Available Quantity:</span>
                            <span
                                class="text-2xl font-bold {{ $stock->available_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($stock->available_quantity, 0) }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $stock->available_quantity > 0 ? 'bg-green-600' : 'bg-red-600' }} h-2 rounded-full"
                                style="width: {{ $stock->quantity > 0 ? ($stock->available_quantity / $stock->quantity) * 100 : 0 }}%">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $stock->quantity > 0 ? number_format(($stock->available_quantity / $stock->quantity) * 100, 1) : 0 }}%
                            remaining
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700">Sold/Used:</span>
                            <span class="text-gray-900 text-xl font-bold">
                                {{ number_format($stock->quantity - $stock->available_quantity, 0) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <span class="font-medium text-gray-700">Status:</span>
                        @if ($stock->available_quantity == 0)
                            <span
                                class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        @elseif($stock->available_quantity <= $stock->quantity / 2)
                            <span
                                class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Low Stock
                            </span>
                        @else
                            <span
                                class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                In Stock
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
