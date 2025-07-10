@extends('layouts.app')

@php
    $isEdit = isset($purchaseOrder);
@endphp

@section('title', $isEdit ? 'Edit Purchase Order' : 'Create Purchase Order')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isEdit ? 'Edit Purchase Order' : 'Create Purchase Order' }}</h1>
        <a href="{{ route('purchase-orders.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to POs
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form class="space-y-6" method="POST" action="{{ $isEdit ? route('purchase-orders.update', $purchaseOrder) : route('purchase-orders.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <!-- PO Header -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PO Number</label>
                    <input type="text" name="po_number" value="{{ old('po_number', $purchaseOrder->po_number ?? 'Auto') }}" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier *</label>
                    <select name="supplier_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Date</label>
                    <input type="date" name="expected_date" value="{{ old('expected_date', $purchaseOrder->expected_date ?? '') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <!-- Product Selection -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Products</h3>
                    <button type="button" onclick="addProductRow()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productRows" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <tr>
                                <td class="px-4 py-4">
                                    <select name="products[]" class="product-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" name="quantities[]" min="1" value="1" class="quantity-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" name="unit_prices[]" step="0.01" value="0.00" class="unit-price-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4">
                                    <span class="line-total text-sm font-medium text-gray-900 dark:text-white">$0.00</span>
                                </td>
                                <td class="px-4 py-4">
                                    <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="subtotal font-medium dark:text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                            <input type="number" name="tax" step="0.01" value="0.00" class="tax-input w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Shipping:</span>
                            <input type="number" name="shipping" step="0.01" value="0.00" class="shipping-input w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="dark:text-white">Total:</span>
                            <span class="grand-total dark:text-white">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional notes or instructions..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                <button type="button" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">Save as Draft</button>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">{{ $isEdit ? 'Update PO' : 'Save PO' }}</button>
            </div>
            <input type="hidden" name="subtotal" class="subtotal-input" value="0.00">
            <input type="hidden" name="total" class="total-input" value="0.00">
        </form>
    </div>
</div>

@push('scripts')
<script>
function addProductRow() {
    const tbody = document.getElementById('productRows');
    const newRow = tbody.rows[0].cloneNode(true);
    // Clear input values
    newRow.querySelectorAll('input').forEach(input => {
        if (input.type === 'number') input.value = input.name === 'quantities[]' ? 1 : 0.00;
    });
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    newRow.querySelector('.line-total').textContent = '$0.00';
    tbody.appendChild(newRow);
    attachRowEvents(newRow);
    calculateTotals();
}

function attachRowEvents(row) {
    row.querySelector('.quantity-input').addEventListener('input', calculateTotals);
    row.querySelector('.unit-price-input').addEventListener('input', calculateTotals);
    row.querySelector('.remove-row-btn').addEventListener('click', function() {
        if (document.querySelectorAll('#productRows tr').length > 1) {
            row.remove();
            calculateTotals();
        }
    });
    row.querySelector('.product-select').addEventListener('change', calculateTotals);
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('#productRows tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').textContent = '$' + lineTotal.toFixed(2);
        subtotal += lineTotal;
    });
    document.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
    document.querySelector('.subtotal-input').value = subtotal.toFixed(2);
    const tax = parseFloat(document.querySelector('.tax-input').value) || 0;
    const shipping = parseFloat(document.querySelector('.shipping-input').value) || 0;
    const grandTotal = subtotal + tax + shipping;
    document.querySelector('.grand-total').textContent = '$' + grandTotal.toFixed(2);
    document.querySelector('.total-input').value = grandTotal.toFixed(2);
}

document.querySelectorAll('#productRows tr').forEach(attachRowEvents);
document.querySelector('.tax-input').addEventListener('input', calculateTotals);
document.querySelector('.shipping-input').addEventListener('input', calculateTotals);
window.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endpush
@endsection
