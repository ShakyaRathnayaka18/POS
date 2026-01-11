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
            <div class="text-center mb-4 pb-3 receipt-header">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">H Mart</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">අංක 09, මහනුවර පාර, හසලක</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">☎ 055225706</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white mt-2">බිල්පත</p>
            </div>

            <!-- Sale Information -->
            <div class="mb-3 text-xs info-section">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-900 dark:text-white">දිනය: <strong>{{ $sale->created_at->setTimezone('Asia/Colombo')->format('d/m/Y  h:i:sA') }}</strong></span>
                    <span class="text-gray-900 dark:text-white">අංකය: <strong>{{ $sale->sale_number }}</strong></span>
                </div>
            </div>

            <!-- Cashier & Payment Info -->
            <div class="mb-3 text-xs info-section">
                <div class="flex justify-between">
                    <span class="text-gray-900 dark:text-white">මුදල් අයකැමි: <strong>{{ $sale->user->name ?? 'System' }}</strong></span>
                    <span class="text-gray-900 dark:text-white">ගෙවීමේ ක්‍රමය: <strong>{{ $sale->payment_method }}</strong></span>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t-2 border-dashed border-gray-400 my-3"></div>

            <!-- Column Headers -->
            <div class="text-xs font-bold text-gray-900 dark:text-white mb-2 items-header">
                <div class="flex justify-between">
                    <span style="width: 15%">ප්‍රමාණය</span>
                    <span style="width: 20%">සදහන් මිල</span>
                    <span style="width: 20%; text-align: right">අපේ මිල</span>
                    <span style="width: 20%; text-align: right">එකතුව</span>
                </div>
            </div>

            <!-- Items List -->
            <div class="mb-4 items-list">
                @foreach ($sale->items as $item)
                    <!-- Product Name -->
                    <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                        {{ $item->product->product_name }}
                        @if ($item->product->is_weighted)
                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                ({{ number_format($item->quantity / 1000, 3) }}kg @ LKR {{ number_format($item->price, 2) }}/kg)
                            </span>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="flex justify-between text-xs text-gray-900 dark:text-white mb-3">
                        <span style="width: 15%">
                            @if ($item->product->is_weighted)
                                <strong>{{ number_format($item->quantity / 1000, 3) }}kg</strong>
                            @else
                                <strong>{{ number_format($item->quantity, 0) }}</strong>
                            @endif
                        </span>
                        <span style="width: 20%">
                            @if ((method_exists($item, 'hasDiscount') && $item->hasDiscount()) || (property_exists($item, 'discount_type') && $item->discount_type !== 'none' && $item->discount_amount > 0))
                                <span
                                    class="line-through text-gray-500">{{ number_format($item->price_before_discount, 2) }}</span>
                            @else
                                {{ number_format($item->price, 2) }}
                            @endif
                        </span>
                        <span style="width: 20%; text-align: right">
                            {{ number_format($item->price, 2) }}
                        </span>
                        <span style="width: 20%; text-align: right; font-weight: bold">
                            {{ number_format($item->total, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Divider -->
            <div class="border-t-2 border-gray-400 my-3"></div>

            <!-- Totals Section -->
            <div class="space-y-2 totals-section">
                <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                    <span>මුලු එකතුව</span>
                    <span>{{ number_format($sale->total, 2) }}</span>
                </div>

                <div class="text-center text-xl font-black my-3">
                    **** ගෙවීම ****
                </div>

                <div class="flex justify-between text-sm text-gray-900 dark:text-white">
                    <span>ගෙවූ මුදල</span>
                    <span class="font-bold">{{ number_format($sale->amount_received ?? $sale->total, 2) }}</span>
                </div>

                @if ($sale->total_discount > 0)
                    <div class="flex justify-between text-sm text-gray-900 dark:text-white">
                        <span>ලබාදුන් වට්ටම</span>
                        <span class="font-bold text-green-600">{{ number_format($sale->total_discount, 2) }}</span>
                    </div>
                @endif

                @if ($sale->payment_method->value === 'cash' && $sale->change_amount > 0)
                    <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white">
                        <span>ඉතිරිය</span>
                        <span>{{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                @endif
            </div>

            <!-- Divider -->
            <div class="border-t border-dashed border-gray-400 my-4"></div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-600 dark:text-gray-400 footer-section">
                <p class="mb-3 text-sm font-bold">ස්තූතියි, නැවත එන්න !</p>
                <p class="text-xs">Powered by VertexCore AI</p>
                <p class="text-xs">vertexcoreai.com</p>
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

            /* Hide everything except receipt */
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            /* Receipt Container */
            #receipt {
                position: absolute;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                width: 80mm !important;
                max-width: 80mm !important;
                background: white !important;
                padding: 4mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                font-family: Arial, 'Noto Sans Sinhala', sans-serif !important;
            }

            /* All text black and bold */
            #receipt * {
                color: black !important;
                font-weight: 700 !important;
            }

            /* Store Logo */
            #receipt img {
                width: 45px !important;
                height: auto !important;
                margin: 0 auto 4px !important;
            }

            /* Store Header */
            #receipt .receipt-header {
                border-bottom: 2px solid black !important;
                padding-bottom: 4mm !important;
                margin-bottom: 3mm !important;
            }

            #receipt h1 {
                font-size: 24px !important;
                font-weight: 900 !important;
                margin: 3px 0 !important;
                letter-spacing: 1px !important;
            }

            #receipt .receipt-header p {
                font-size: 11px !important;
                line-height: 1.4 !important;
                margin: 2px 0 !important;
            }

            #receipt .receipt-header p:last-of-type {
                font-size: 12px !important;
                font-weight: 900 !important;
                margin-top: 4px !important;
            }

            /* Sale Info */
            #receipt .sale-info {
                font-size: 11px !important;
                margin-bottom: 2mm !important;
            }

            #receipt .sale-info strong {
                font-weight: 900 !important;
            }

            /* Cashier & Payment Info */
            #receipt .info-section {
                font-size: 10px !important;
                line-height: 1.5 !important;
            }

            /* Dividers */
            #receipt .border-dashed {
                border-style: dashed !important;
                border-color: black !important;
                margin: 2mm 0 !important;
            }

            #receipt .border-t-2 {
                border-top: 1px solid black !important;
            }

            /* Items Header */
            #receipt .items-header {
                font-size: 8px !important;
                font-weight: 900 !important;
                margin-bottom: 2mm !important;
                border-bottom: 1px dashed black !important;
                padding-bottom: 1mm !important;
            }

            /* Items List */
            #receipt .items-list>div:first-child {
                font-size: 10px !important;
                font-weight: 800 !important;
                margin-bottom: 1mm !important;
                line-height: 1.2 !important;
            }

            #receipt .items-list>div:nth-child(2) {
                font-size: 9px !important;
                margin-bottom: 3mm !important;
            }

            #receipt .items-list span {
                display: inline-block;
            }

            /* Totals Section */
            #receipt .totals-section {
                font-size: 10px !important;
            }

            #receipt .totals-section>div:first-child {
                font-size: 14px !important;
                font-weight: 900 !important;
                margin: 2mm 0 !important;
            }

            #receipt .totals-section .text-center {
                font-size: 11px !important;
                font-weight: 900 !important;
                letter-spacing: 2px !important;
                margin: 3mm 0 !important;
            }

            #receipt .totals-section .text-sm {
                font-size: 9px !important;
                margin: 1.5mm 0 !important;
            }

            #receipt .totals-section .text-base {
                font-size: 11px !important;
                font-weight: 900 !important;
                margin: 2mm 0 !important;
            }

            /* Footer */
            #receipt .footer-section {
                font-size: 8px !important;
                line-height: 1.4 !important;
            }

            #receipt .footer-section p {
                margin: 1.5mm 0 !important;
            }

            #receipt .footer-section .font-bold {
                font-weight: 900 !important;
            }

            #receipt .footer-section .text-sm {
                font-size: 10px !important;
                font-weight: 900 !important;
            }

            /* Spacing adjustments */
            #receipt .mb-1 {
                margin-bottom: 1mm !important;
            }

            #receipt .mb-2 {
                margin-bottom: 2mm !important;
            }

            #receipt .mb-3 {
                margin-bottom: 3mm !important;
            }

            #receipt .mb-4 {
                margin-bottom: 4mm !important;
            }

            #receipt .mt-1 {
                margin-top: 1mm !important;
            }

            #receipt .mt-2 {
                margin-top: 2mm !important;
            }

            #receipt .mt-3 {
                margin-top: 3mm !important;
            }

            #receipt .my-3 {
                margin-top: 3mm !important;
                margin-bottom: 3mm !important;
            }

            #receipt .my-4 {
                margin-top: 4mm !important;
                margin-bottom: 4mm !important;
            }

            #receipt .pb-3 {
                padding-bottom: 3mm !important;
            }

            /* Remove dark mode classes */
            #receipt .dark\:text-white,
            #receipt .dark\:text-gray-300,
            #receipt .dark\:text-gray-400,
            #receipt .dark\:bg-gray-800 {
                color: black !important;
                background: white !important;
            }
        }
    </style>
@endsection
