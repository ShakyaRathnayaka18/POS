@extends('layouts.app')

@php
    $isEdit = isset($supplier);
@endphp

@section('title', $isEdit ? 'Edit Supplier' : 'Add Supplier')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isEdit ? 'Edit Supplier' : 'Add New Supplier' }}</h1>
        <a href="{{ route('suppliers.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form class="space-y-6" method="POST" action="{{ $isEdit ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Company Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Company Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name *</label>
                        <input type="text" name="company_name" required value="{{ old('company_name', $supplier->company_name ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Type</label>
                        <select name="business_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="" disabled {{ old('business_type', $supplier->business_type ?? '') == '' ? 'selected' : '' }}>Select Type</option>
                            <option value="Agent" {{ old('business_type', $supplier->business_type ?? '') == 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Manufacturer" {{ old('business_type', $supplier->business_type ?? '') == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                            <option value="Wholesaler" {{ old('business_type', $supplier->business_type ?? '') == 'Wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                            <option value="Retailer" {{ old('business_type', $supplier->business_type ?? '') == 'Retailer' ? 'selected' : '' }}>Retailer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax ID</label>
                        <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                        <input type="url" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div> --}}
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person *</label>
                        <input type="text" name="contact_person" required value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                        <input type="email" name="email" required value="{{ old('email', $supplier->email ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                            <input type="tel" name="phone" required value="{{ old('phone', $supplier->phone ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile</label>
                            <input type="tel" name="mobile" value="{{ old('mobile', $supplier->mobile ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            {{-- <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Address</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP/Postal Code</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option>United States</option>
                            <option>Canada</option>
                            <option>United Kingdom</option>
                            <option>Australia</option>
                        </select>
                    </div>
                </div>
            </div> --}}

            <!-- Payment Terms -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Terms</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Terms</label>
                        <select name="payment_terms" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="" disabled {{ old('payment_terms', $supplier->payment_terms ?? '') == '' ? 'selected' : '' }}>Select Terms</option>
                            <option value="Net 30" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                            <option value="Net 15" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'Net 15' ? 'selected' : '' }}>Net 15</option>
                            <option value="Net 7" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'Net 7' ? 'selected' : '' }}>Net 7</option>
                            <option value="Cash on Delivery" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Credit Limit</label>
                        <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option>USD</option>
                            <option>EUR</option>
                            <option>GBP</option>
                            <option>CAD</option>
                        </select>
                    </div> --}}
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                <button type="button" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">{{ $isEdit ? 'Update Supplier' : 'Save Supplier' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
