@extends('layouts.app')

@section('title', 'Sale Receipt - ' . $sale->sale_number)

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Action Buttons -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('cashier.dashboard') }}"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                &larr; Back to Cashier
            </a>
            <div class="space-x-2">
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
                <a href="{{ route('sales.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded inline-block">
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
                <p class="text-gray-600 dark:text-gray-800">අංක 09, මහනුවර පාර, හසලක</p>
                <p class="text-sm text-gray-500 dark:text-gray-800 mt-2">දු.ක - 055225706</p>
            </div>


            <!-- Sale Information -->
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-gray-600 dark:text-gray-800">Invoice No:</p>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $sale->sale_number }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 dark:text-white text-sm">

                        <span class="text-gray-700 dark:text-gray-300">දිනය:</span>
                        {{ $sale->created_at->setTimezone('Asia/Colombo')->format('Y-m-d') }}

                        <br>

                        <span class="text-gray-700 dark:text-gray-300">වේලාව:</span>
                        {{ $sale->created_at->setTimezone('Asia/Colombo')->format('h:i:s A') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-800">මුදල් අයකැමි:</p>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $sale->user->name ?? 'System' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 dark:text-gray-800">ගෙවීමේ ක්‍රමය:</p>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $sale->payment_method }}</p>
                </div>
            </div>

            @if ($sale->customer_name || $sale->customer_phone)
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg no-print">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Customer Information</h3>
                    @if ($sale->customer_name)
                        <p class="text-sm text-gray-700 dark:text-gray-300">Name: <span
                                class="font-medium">{{ $sale->customer_name }}</span></p>
                    @endif
                    @if ($sale->customer_phone)
                        <p class="text-sm text-gray-700 dark:text-gray-300">Phone: <span
                                class="font-medium">{{ $sale->customer_phone }}</span></p>
                    @endif
                </div>
            @endif

            <!-- Items Table -->
            <div class="mb-6">
                <table class="w-full items-table">
                    <thead class="border-b-2 border-gray-300 dark:border-gray-600">
                        <tr>
                            <th class="text-left py-2 text-gray-700 dark:text-gray-300" style="width: 40%;">නිෂ්පාදනය</th>
                            <th class="text-center py-2 text-gray-700 dark:text-gray-300" style="width: 12%;">ප්‍රමාණය</th>
                            <th class="text-right py-2 text-gray-700 dark:text-gray-300" style="width: 16%;">මිල</th>
                            <th class="text-right py-2 text-gray-700 dark:text-gray-300" style="width: 16%;">වට්ටම්</th>
                            <th class="text-right py-2 text-gray-700 dark:text-gray-300" style="width: 16%;">එකතුව</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="py-3" style="width: 40%;">{{ $item->product->product_name }}</td>

                                <td class="py-3 text-center" style="width: 12%;">{{ number_format($item->quantity, 0) }}
                                </td>

                                <td class="py-3 text-right" style="width: 16%;">
                                    @if ($item->hasDiscount())
                                        <span
                                            class="line-through text-gray-500 dark:text-gray-400 text-xs">{{ number_format($item->price_before_discount, 2) }}</span><br>
                                        <span
                                            class="text-green-600 dark:text-green-400">{{ number_format($item->price, 2) }}</span>
                                    @else
                                        {{ number_format($item->price, 2) }}
                                    @endif
                                </td>

                                <td class="py-3 text-right text-green-600 dark:text-green-400 text-sm" style="width: 16%;">
                                    @if ($item->hasDiscount())
                                        {{ number_format($item->discount_amount, 2) }}
                                    @else
                                        0.00
                                    @endif
                                </td>

                                <td class="py-3 text-right font-medium" style="width: 16%;">
                                    {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-4 space-y-2">
                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                    <span>උප එකතුව:</span>
                    <span class="font-medium">{{ number_format($sale->subtotal_before_discount, 2) }}</span>
                </div>
                @if ($sale->total_discount > 0)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>ලබාදුන් වට්ටම:</span>
                        <span class="font-medium">{{ number_format($sale->total_discount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                    <span>බදු:</span>
                    <span class="font-medium">{{ number_format($sale->tax, 2) }}</span>
                </div>
                <div
                    class="flex justify-between text-xl font-bold text-gray-900 dark:text-white border-t border-gray-300 dark:border-gray-600 pt-2">
                    <span>මුලු එකතුව:</span>
                    <span class="text-gray-900 dark:text-white font-bold">{{ number_format($sale->total, 2) }}</span>
                </div>

                {{-- 💰 CASH PAYMENT AND CHANGE SECTION 💰 --}}
                @if ($sale->payment_method->value === 'cash')
                    <div class="flex justify-between text-gray-700 dark:text-gray-300 pt-2">
                        {{-- Amount Received (ගෙවූ මුදල) --}}
                        <span>ගෙවූ මුදල:</span>
                        <span class="font-medium">{{ number_format($sale->amount_received, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-blue-600 dark:text-blue-400">
                        {{-- Change (ශේෂය) --}}
                        <span>ශේෂය:</span>
                        <span class="font-bold">{{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                @endif
                {{-- END CASH SECTION --}}
            </div>

            <!-- Footer -->
            <div
                class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 text-center text-sm text-gray-500 dark:text-gray-800">
                <p class="mb-2">ස්තූතියි, නැවත එන්න -H Mart©</p>

                <p class="text-xs mt-4">Powered by VertexCore AI | vertexcoreai.com</p>

            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div
                class="mt-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded">
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
                font-family: 'Courier New', Courier, monospace;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                background: white !important;
            }

            /* Hide customer info */
            .no-print,
            .no-print * {
                display: none !important;
                visibility: hidden !important;
            }

            /* THERMAL PRINTER: Bold text */
            #receipt * {
                color: black !important;
                font-weight: 700 !important;
            }

            #receipt h1,
            #receipt .font-bold,
            #receipt th,
            #receipt .text-xl {
                font-weight: 900 !important;
            }

            /* THERMAL PRINTER: Remove all borders */
            #receipt [class*="border"] {
                border: none !important;
            }

            /* Keep only 3 lines: after header, before items, after total */
            #receipt .text-center.mb-6.border-b-2 {
                border-bottom: 2px solid black !important;
            }

            #receipt thead {
                border-top: 2px solid black !important;
                border-bottom: 2px solid black !important;
            }

            #receipt .border-t-2.pt-4 {
                border-top: 2px solid black !important;
            }

            /* ===== FIX COLUMN ALIGNMENT ===== */
            /* Force table layout to be fixed */
            .items-table {
                table-layout: fixed !important;
                width: 100% !important;
            }

            /* Define exact column widths for headers */
            .items-table thead th:nth-child(1) {
                width: 40% !important;
                text-align: left !important;
            }

            .items-table thead th:nth-child(2) {
                width: 12% !important;
                text-align: center !important;
            }

            .items-table thead th:nth-child(3) {
                width: 16% !important;
                text-align: right !important;
            }

            .items-table thead th:nth-child(4) {
                width: 16% !important;
                text-align: right !important;
            }

            .items-table thead th:nth-child(5) {
                width: 16% !important;
                text-align: right !important;
            }

            /* Define exact column widths for body cells - MUST match headers */
            .items-table tbody td:nth-child(1) {
                width: 40% !important;
                text-align: left !important;
            }

            .items-table tbody td:nth-child(2) {
                width: 12% !important;
                text-align: center !important;
            }

            .items-table tbody td:nth-child(3) {
                width: 16% !important;
                text-align: right !important;
            }

            .items-table tbody td:nth-child(4) {
                width: 16% !important;
                text-align: right !important;
            }

            .items-table tbody td:nth-child(5) {
                width: 16% !important;
                text-align: right !important;
            }

            /* Remove all padding/margin inconsistencies */
            .items-table th,
            .items-table td {
                padding: 4px 2px !important;
                margin: 0 !important;
                word-wrap: break-word !important;
            }
        }
    </style>
@endsection
