@extends('layouts.app')

@section('title', 'Sale Receipt - ' . $sale->sale_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Action Buttons -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('cashier.dashboard') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
            &larr; Back to Cashier
        </a>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-print mr-2"></i>Print Receipt
            </button>
            <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-block">
                <i class="fas fa-list mr-2"></i>View Sales History
            </a>
        </div>
    </div>

    <!-- Receipt -->
    <div id="receipt" class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
        <!-- Store Logo -->
        <div class="text-center mb-2">
            <img src="{{ asset('images/h_mart.png') }}" alt="Store Logo" class="w-24 mx-auto">
        </div>

        <!-- Store Header -->
        <div class="text-center mb-6 border-b-2 border-gray-300 dark:border-gray-600 pb-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">H Mart</h1>
            <p class="text-gray-600 dark:text-gray-400">No 09, Kandy Road, Hasalaka</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Telephone - 055225706</p>
            <!-- <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Shop smart, save big</p> -->
        </div>

        
        <!-- Sale Information -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <p class="text-gray-600 dark:text-gray-400">Invoice No:</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $sale->sale_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-600 dark:text-gray-400">Date:</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div>
                <p class="text-gray-600 dark:text-gray-400">Cashier:</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $sale->user->name ?? 'System' }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-600 dark:text-gray-400">Payment Method:</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $sale->payment_method }}</p>
            </div>
        </div>

        @if($sale->customer_name || $sale->customer_phone)
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg no-print">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Customer Information</h3>
            @if($sale->customer_name)
            <p class="text-sm text-gray-700 dark:text-gray-300">Name: <span class="font-medium">{{ $sale->customer_name }}</span></p>
            @endif
            @if($sale->customer_phone)
            <p class="text-sm text-gray-700 dark:text-gray-300">Phone: <span class="font-medium">{{ $sale->customer_phone }}</span></p>
            @endif
        </div>
        @endif

        <!-- Items Table -->
        <div class="mb-6">
            <table class="w-full">
                <thead class="border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="text-left py-2 text-gray-700 dark:text-gray-300">Product</th>
                        <th class="text-center py-2 text-gray-700 dark:text-gray-300">Qty</th>
                        <th class="text-right py-2 text-gray-700 dark:text-gray-300">Price</th>
                        <th class="text-right py-2 text-gray-700 dark:text-gray-300">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="py-3">{{ $item->product->product_name }}</td>
                        <td class="py-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-3 text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="py-3 text-right font-medium">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-4 space-y-2">
            <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span>Subtotal:</span>
                <span class="font-medium">{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span>Tax:</span>
                <span class="font-medium">{{ number_format($sale->tax, 2) }}</span>
            </div>
            <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white border-t border-gray-300 dark:border-gray-600 pt-2">
                <span>Total:</span>
                <span class="text-green-600 dark:text-green-400">{{ number_format($sale->total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 text-center text-sm text-gray-500 dark:text-gray-400">
            <p class="mb-2">Thank you for shopping at H Mart</p>

            <p class="text-xs mt-4">Powered by VertexCore AI | vertexcoreai.com</p>

        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mt-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #receipt,
        #receipt * {
            visibility: visible;
            font-family: 'Courier New', Courier, monospace; /* Adding monospaced font */
        }

        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            background: white !important;
            color: black !important;
        }

        /* Hide customer information in print */
        .no-print,
        .no-print * {
            display: none !important;
            visibility: hidden !important;
        }

        /* Hide dark mode styles in print */
        .dark\:bg-gray-800,
        .dark\:bg-gray-700,
        .dark\:text-white,
        .dark\:text-gray-300,
        .dark\:text-gray-400,
        .dark\:border-gray-600,
        .dark\:border-gray-700 {
            background: white !important;
            color: black !important;
            border-color: #e5e7eb !important;
        }
    }
</style>
@endsection