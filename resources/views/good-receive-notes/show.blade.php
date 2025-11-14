@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('good-receive-notes.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Back to GRNs
        </a>
        <h1 class="text-3xl font-bold text-gray-800">GRN Details: {{ $goodReceiveNote->grn_number }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">GRN Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="font-medium text-gray-700">GRN Number:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->grn_number }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Supplier:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->supplier->company_name }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Contact:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->supplier->phone }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Received Date:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->received_date->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Invoice Number:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->invoice_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Invoice Date:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->invoice_date ? $goodReceiveNote->invoice_date->format('F d, Y') : '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Payment Type:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $goodReceiveNote->payment_type === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($goodReceiveNote->payment_type ?? 'cash') }}
                    </span>
                </div>
                @if($goodReceiveNote->is_credit && $goodReceiveNote->supplierCredit)
                <div>
                    <span class="font-medium text-gray-700">Credit Status:</span>
                    <a href="{{ route('supplier-credits.show', $goodReceiveNote->supplierCredit) }}"
                        class="text-blue-600 hover:text-blue-800 hover:underline">
                        {{ $goodReceiveNote->supplierCredit->credit_number }} -
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $goodReceiveNote->supplierCredit->status->badgeColor() }}">
                            {{ $goodReceiveNote->supplierCredit->status->description() }}
                        </span>
                    </a>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Outstanding:</span>
                    <span class="text-gray-900 font-semibold">LKR {{ number_format($goodReceiveNote->supplierCredit->outstanding_amount, 2) }}</span>
                </div>
                @endif
                <div>
                    <span class="font-medium text-gray-700">Status:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $goodReceiveNote->status === 'Received' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $goodReceiveNote->status }}
                    </span>
                </div>
                @if($goodReceiveNote->notes)
                <div>
                    <span class="font-medium text-gray-700">Notes:</span>
                    <p class="text-gray-900 mt-1">{{ $goodReceiveNote->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Subtotal:</span>
                    <span class="text-gray-900">LKR {{ number_format($goodReceiveNote->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Tax:</span>
                    <span class="text-gray-900">LKR {{ number_format($goodReceiveNote->tax, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Shipping:</span>
                    <span class="text-gray-900">LKR {{ number_format($goodReceiveNote->shipping, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-300">
                    <span class="font-bold text-gray-900 text-lg">Total:</span>
                    <span class="font-bold text-green-600 text-lg">LKR {{ number_format($goodReceiveNote->total, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-gray-200">
                    <span class="font-medium text-gray-700">Batches Created:</span>
                    <span class="text-gray-900">{{ $goodReceiveNote->batches->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Batches</h2>
        @foreach($goodReceiveNote->batches as $batch)
        <div class="mb-6 p-4 border border-gray-200 rounded-lg">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-semibold text-lg">{{ $batch->batch_number }}</h3>
                    <p class="text-sm text-gray-500">
                        Manufacture: {{ $batch->manufacture_date?->format('Y-m-d') ?? 'N/A' }} |
                        Expiry: {{ $batch->expiry_date?->format('Y-m-d') ?? 'N/A' }}
                    </p>
                </div>
                <a href="{{ route('batches.show', $batch) }}" class="text-indigo-600 hover:text-indigo-900">
                    View Batch Details →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cost Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tax %</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Available</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($batch->stocks as $stock)
                        <tr>
                            <td class="px-4 py-2 text-sm">{{ $stock->product->product_name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $stock->product->sku }}</td>
                            <td class="px-4 py-2 text-sm">LKR {{ number_format($stock->cost_price, 2) }}</td>
                            <td class="px-4 py-2 text-sm">LKR {{ number_format($stock->selling_price, 2) }}</td>
                            <td class="px-4 py-2 text-sm">{{ $stock->tax }}%</td>
                            <td class="px-4 py-2 text-sm">{{ $stock->quantity }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="{{ $stock->available_quantity > 0 ? 'text-green-600 font-semibold' : 'text-red-600' }}">
                                    {{ $stock->available_quantity }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
