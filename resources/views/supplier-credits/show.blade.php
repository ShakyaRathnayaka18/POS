@extends('layouts.app')

@section('title', 'Credit Details: ' . $supplierCredit->credit_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('supplier-credits.index') }}"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplierCredit->credit_number }}</h1>
                @if($supplierCredit->invoice_number)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Invoice: {{ $supplierCredit->invoice_number }}
                    </p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            @if($supplierCredit->status->value !== 'paid')
                <a href="{{ route('supplier-payments.create', ['credit_id' => $supplierCredit->id]) }}"
                    class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-money-bill mr-2"></i>Record Payment
                </a>
            @endif
            <button onclick="window.print()"
                class="text-gray-600 dark:text-white border border-gray-600 dark:border-white rounded px-4 py-2 transition-colors duration-200 hover:bg-gray-600 hover:text-white">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Original Amount -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Original Amount</label>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                LKR {{ number_format($supplierCredit->original_amount, 2) }}
            </p>
        </div>

        <!-- Paid Amount -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Paid Amount</label>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                LKR {{ number_format($supplierCredit->paid_amount, 2) }}
            </p>
        </div>

        <!-- Outstanding Amount -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Outstanding Amount</label>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                LKR {{ number_format($supplierCredit->outstanding_amount, 2) }}
            </p>
        </div>

        <!-- Status -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</label>
            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full {{ $supplierCredit->status->badgeColor() }}">
                {{ $supplierCredit->status->description() }}
            </span>
        </div>
    </div>

    <!-- Credit Information Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Credit Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $supplierCredit->supplier->company_name }}
                    </p>
                    @if($supplierCredit->supplier->contact_person)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $supplierCredit->supplier->contact_person }}
                        </p>
                    @endif
                </div>

                <!-- Invoice Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Invoice Number</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->invoice_number ?? '-' }}</p>
                </div>

                <!-- Invoice Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Invoice Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->invoice_date->format('M d, Y') }}</p>
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->due_date->format('M d, Y') }}</p>
                    @if($supplierCredit->isOverdue() && $supplierCredit->status->value !== 'paid')
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>Overdue by {{ now()->diffInDays($supplierCredit->due_date) }} days
                        </p>
                    @elseif($supplierCredit->isDueSoon())
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>Due in {{ $supplierCredit->due_date->diffInDays(now()) }} days
                        </p>
                    @endif
                </div>

                <!-- Credit Terms -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Credit Terms</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->credit_terms->label() }}</p>
                </div>

                <!-- Credit Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Credit Days</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->credit_days }} days</p>
                </div>

                <!-- GRN Reference -->
                @if($supplierCredit->goodReceiveNote)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">GRN Reference</label>
                        <a href="{{ route('good-receive-notes.show', $supplierCredit->goodReceiveNote) }}"
                            class="mt-1 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $supplierCredit->goodReceiveNote->grn_number }}
                        </a>
                    </div>
                @endif

                <!-- Created By -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplierCredit->createdBy->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supplierCredit->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Notes -->
            @if($supplierCredit->notes)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</label>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $supplierCredit->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payments Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Payment History
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    ({{ $supplierCredit->payments->count() }} {{ $supplierCredit->payments->count() === 1 ? 'payment' : 'payments' }})
                </span>
            </h2>
            @if($supplierCredit->status->value !== 'paid')
                <a href="{{ route('supplier-payments.create', ['credit_id' => $supplierCredit->id]) }}"
                    class="bg-primary-600 text-white px-3 py-1 rounded text-sm hover:bg-primary-700">
                    <i class="fas fa-plus mr-1"></i>Add Payment
                </a>
            @endif
        </div>

        @if($supplierCredit->payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Payment #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Method
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Reference
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Processed By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($supplierCredit->payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $payment->payment_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $payment->payment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    LKR {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $payment->payment_method->label() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $payment->reference_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $payment->processedBy->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('supplier-payments.show', $payment) }}"
                                        class="text-green-600 dark:text-white border border-green-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-green-600 hover:text-white dark:hover:bg-white dark:hover:text-green-600">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-file-invoice-dollar text-4xl text-gray-400 dark:text-gray-600 mb-3 block"></i>
                <p class="text-gray-500 dark:text-gray-400 mb-4">No payments recorded yet.</p>
                @if($supplierCredit->status->value !== 'paid')
                    <a href="{{ route('supplier-payments.create', ['credit_id' => $supplierCredit->id]) }}"
                        class="inline-block bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">
                        <i class="fas fa-plus mr-2"></i>Record First Payment
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
