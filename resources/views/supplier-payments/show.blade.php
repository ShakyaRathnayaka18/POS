@extends('layouts.app')

@section('title', 'Payment Details: ' . $supplierPayment->payment_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('supplier-payments.index') }}"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplierPayment->payment_number }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Payment Details
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()"
                class="text-gray-600 dark:text-white border border-gray-600 dark:border-white rounded px-4 py-2 transition-colors duration-200 hover:bg-gray-600 hover:text-white">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <!-- Payment Summary Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Payment Amount -->
            <div class="text-center md:text-left">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Amount</label>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                    LKR {{ number_format($supplierPayment->amount, 2) }}
                </p>
            </div>

            <!-- Payment Date -->
            <div class="text-center md:text-left">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Date</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $supplierPayment->payment_date->format('M d, Y') }}
                </p>
            </div>

            <!-- Payment Method -->
            <div class="text-center md:text-left">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Method</label>
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ $supplierPayment->payment_method->label() }}
                </span>
            </div>

            <!-- Reference Number -->
            <div class="text-center md:text-left">
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Reference Number</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $supplierPayment->reference_number ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Payment Information Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $supplierPayment->supplier->company_name }}
                    </p>
                    @if($supplierPayment->supplier->contact_person)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $supplierPayment->supplier->contact_person }}
                        </p>
                    @endif
                    @if($supplierPayment->supplier->phone)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-phone mr-1"></i>{{ $supplierPayment->supplier->phone }}
                        </p>
                    @endif
                </div>

                <!-- Credit Reference -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Credit Reference</label>
                    <a href="{{ route('supplier-credits.show', $supplierPayment->supplierCredit) }}"
                        class="mt-1 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        {{ $supplierPayment->supplierCredit->credit_number }}
                    </a>
                    @if($supplierPayment->supplierCredit->invoice_number)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Invoice: {{ $supplierPayment->supplierCredit->invoice_number }}
                        </p>
                    @endif
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $supplierPayment->payment_date->format('l, F d, Y') }}
                    </p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount Paid</label>
                    <p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">
                        LKR {{ number_format($supplierPayment->amount, 2) }}
                    </p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $supplierPayment->payment_method->label() }}
                    </p>
                </div>

                <!-- Reference Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reference Number</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $supplierPayment->reference_number ?? 'N/A' }}
                    </p>
                </div>

                <!-- Processed By -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Processed By</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierPayment->processedBy->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supplierPayment->created_at->format('M d, Y H:i') }}</p>
                </div>

                <!-- Updated At -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $supplierPayment->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Notes -->
            @if($supplierPayment->notes)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</label>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $supplierPayment->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Credit Status After Payment Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Credit Status After Payment</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Original Credit Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Original Amount</label>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        LKR {{ number_format($supplierPayment->supplierCredit->original_amount, 2) }}
                    </p>
                </div>

                <!-- Total Paid -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Total Paid</label>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                        LKR {{ number_format($supplierPayment->supplierCredit->paid_amount, 2) }}
                    </p>
                </div>

                <!-- Outstanding -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Outstanding</label>
                    <p class="text-lg font-bold text-orange-600 dark:text-orange-400">
                        LKR {{ number_format($supplierPayment->supplierCredit->outstanding_amount, 2) }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Credit Status</label>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $supplierPayment->supplierCredit->status->badgeColor() }}">
                        {{ $supplierPayment->supplierCredit->status->description() }}
                    </span>
                </div>
            </div>

            <!-- Action Button -->
            @if($supplierPayment->supplierCredit->outstanding_amount > 0)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        This credit still has an outstanding balance
                    </p>
                    <a href="{{ route('supplier-payments.create', ['credit_id' => $supplierPayment->supplierCredit->id]) }}"
                        class="inline-block bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                        <i class="fas fa-plus mr-2"></i>Record Another Payment
                    </a>
                </div>
            @else
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center text-green-600 dark:text-green-400">
                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                        <span class="text-lg font-semibold">This credit has been fully paid</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Quick Links</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('supplier-credits.show', $supplierPayment->supplierCredit) }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-file-invoice mr-2"></i>View Credit Details
            </a>
            <a href="{{ route('supplier-payments.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-list mr-2"></i>All Payments
            </a>
            @if($supplierPayment->supplierCredit->goodReceiveNote)
                <a href="{{ route('good-receive-notes.show', $supplierPayment->supplierCredit->goodReceiveNote) }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-box mr-2"></i>View GRN
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
