@extends('layouts.app')

@section('title', 'Record Supplier Payment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Record Supplier Payment</h1>
        <a href="{{ route('supplier-payments.index') }}"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
        </a>
    </div>

    <!-- Credit Information Card (if credit is pre-selected) -->
    @if($credit)
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                        Payment for Credit: {{ $credit->credit_number }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300 font-medium">Supplier:</span>
                            <p class="text-blue-900 dark:text-blue-100">{{ $credit->supplier->company_name }}</p>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300 font-medium">Invoice:</span>
                            <p class="text-blue-900 dark:text-blue-100">{{ $credit->invoice_number ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300 font-medium">Original Amount:</span>
                            <p class="text-blue-900 dark:text-blue-100 font-bold">LKR {{ number_format($credit->original_amount, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300 font-medium">Outstanding:</span>
                            <p class="text-blue-900 dark:text-blue-100 font-bold text-lg">LKR {{ number_format($credit->outstanding_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('supplier-payments.store') }}" method="POST" class="space-y-6" id="paymentForm">
            @csrf

            <!-- Credit Selection Section -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Credit Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Credit Selection -->
                    <div class="md:col-span-2">
                        <label for="supplier_credit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Supplier Credit *
                        </label>
                        <select name="supplier_credit_id" id="supplier_credit_id" required
                            @if($credit) disabled @endif
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            @if($credit)
                                <option value="{{ $credit->id }}" selected>
                                    {{ $credit->credit_number }} - {{ $credit->supplier->company_name }} - Outstanding: LKR {{ number_format($credit->outstanding_amount, 2) }}
                                </option>
                            @else
                                <option value="">Select a credit to pay</option>
                                @foreach(\App\Models\SupplierCredit::with('supplier')->whereNotIn('status', ['paid'])->orderBy('due_date')->get() as $availableCredit)
                                    <option value="{{ $availableCredit->id }}"
                                        data-outstanding="{{ $availableCredit->outstanding_amount }}"
                                        data-supplier="{{ $availableCredit->supplier->company_name }}"
                                        data-invoice="{{ $availableCredit->invoice_number }}">
                                        {{ $availableCredit->credit_number }} - {{ $availableCredit->supplier->company_name }} - Outstanding: LKR {{ number_format($availableCredit->outstanding_amount, 2) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @if($credit)
                            <input type="hidden" name="supplier_credit_id" value="{{ $credit->id }}">
                        @endif
                        @error('supplier_credit_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Outstanding Amount Display -->
                        <div id="outstandingDisplay" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded {{ $credit ? '' : 'hidden' }}">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Outstanding Amount: <span class="font-bold" id="outstandingAmount">LKR {{ $credit ? number_format($credit->outstanding_amount, 2) : '0.00' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Date *
                        </label>
                        <input type="date" name="payment_date" id="payment_date" required
                            value="{{ old('payment_date', date('Y-m-d')) }}"
                            max="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        @error('payment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Amount *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">LKR</span>
                            <input type="number" name="amount" id="amount" required min="0.01" step="0.01"
                                value="{{ old('amount', $credit ? $credit->outstanding_amount : '') }}"
                                class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Enter the payment amount (cannot exceed outstanding balance)
                        </p>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Method *
                        </label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->value }}" {{ old('payment_method') == $method->value ? 'selected' : '' }}>
                                    {{ $method->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reference Number
                        </label>
                        <input type="text" name="reference_number" id="reference_number"
                            value="{{ old('reference_number') }}"
                            placeholder="Check #, Transaction ID, etc."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="pb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes
                </label>
                <textarea name="notes" id="notes" rows="4"
                    placeholder="Add any additional notes about this payment..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                <a href="{{ route('supplier-payments.index') }}"
                    class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                    class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-save mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditSelect = document.getElementById('supplier_credit_id');
    const amountInput = document.getElementById('amount');
    const outstandingDisplay = document.getElementById('outstandingDisplay');
    const outstandingAmountSpan = document.getElementById('outstandingAmount');
    const form = document.getElementById('paymentForm');

    let currentOutstanding = {{ $credit ? $credit->outstanding_amount : 0 }};

    // Update outstanding amount display when credit selection changes
    if (creditSelect && !creditSelect.disabled) {
        creditSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                currentOutstanding = parseFloat(selectedOption.dataset.outstanding);
                outstandingAmountSpan.textContent = 'LKR ' + currentOutstanding.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                outstandingDisplay.classList.remove('hidden');
                amountInput.value = currentOutstanding.toFixed(2);
            } else {
                outstandingDisplay.classList.add('hidden');
                amountInput.value = '';
                currentOutstanding = 0;
            }
        });
    }

    // Validate payment amount doesn't exceed outstanding
    amountInput.addEventListener('blur', function() {
        const amount = parseFloat(this.value);
        if (amount > currentOutstanding) {
            alert(`Payment amount (LKR ${amount.toFixed(2)}) cannot exceed outstanding balance (LKR ${currentOutstanding.toFixed(2)})`);
            this.value = currentOutstanding.toFixed(2);
        }
    });

    // Form validation before submit
    form.addEventListener('submit', function(e) {
        const amount = parseFloat(amountInput.value);

        if (!creditSelect.value) {
            e.preventDefault();
            alert('Please select a credit to pay.');
            creditSelect.focus();
            return false;
        }

        if (amount <= 0) {
            e.preventDefault();
            alert('Payment amount must be greater than zero.');
            amountInput.focus();
            return false;
        }

        if (amount > currentOutstanding) {
            e.preventDefault();
            alert(`Payment amount (LKR ${amount.toFixed(2)}) cannot exceed outstanding balance (LKR ${currentOutstanding.toFixed(2)})`);
            amountInput.focus();
            return false;
        }

        // Confirm submission
        const confirmMsg = `Confirm payment of LKR ${amount.toFixed(2)}?`;
        if (!confirm(confirmMsg)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection
