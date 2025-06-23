@extends('layouts.app')

@section('title', 'Stock In Entry')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Stock In Entry</h1>
        <a href="{{ route('stock-in.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to History
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form class="space-y-6">
            <!-- Entry Header -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Entry Date *</label>
                    <input type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Supplier</option>
                        <option>ABC Distributors</option>
                        <option>Tech Solutions Ltd</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference (PO/Invoice)</label>
                    <input type="text" placeholder="PO-2024-001" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <!-- Product Entry -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Products</h3>
                    <button type="button" onclick="addStockRow()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Current Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity In</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Expiry Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody id="stockRows" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <tr>
                                <td class="px-4 py-4">
                                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option>Select Product</option>
                                        <option>Coca Cola 500ml</option>
                                        <option>White Bread</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-900 dark:text-white">45</span>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">$0.00</span>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4">
                                    <button type="button" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Entry Summary -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Items:</span>
                            <span class="font-medium dark:text-white">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Quantity:</span>
                            <span class="font-medium dark:text-white">0</span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="dark:text-white">Total Cost:</span>
                            <span class="dark:text-white">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea rows="3" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                <button type="button" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">Save Stock In</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addStockRow() {
    const tbody = document.getElementById('stockRows');
    const newRow = tbody.rows[0].cloneNode(true);
    // Clear input values
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    tbody.appendChild(newRow);
}
</script>
@endpush
@endsection
