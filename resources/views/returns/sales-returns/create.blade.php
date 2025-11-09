@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Create Sales Return</h1>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('sales-returns.store') }}" method="POST" id="sales-return-form">
                @csrf
                <!-- Section 1: Return Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="return_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Number</label>
                        <input type="text" name="return_number" id="return_number" value="{{ $returnNumber }}" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="sale_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sale</label>
                        <select name="sale_id" id="sale_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">Select a Sale</option>
                            @foreach($sales as $sale)
                                <option value="{{ $sale->id }}">#{{ $sale->sale_number }} - {{ $sale->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</label>
                        <input type="date" name="return_date" id="return_date" value="{{ now()->toDateString() }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="return_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Reason</label>
                        <select name="return_reason" id="return_reason"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option>Changed Mind</option>
                            <option>Defective</option>
                            <option>Wrong Item</option>
                            <option>Size Issue</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"></textarea>
                    </div>
                </div>

                <!-- Section 2: Items to Return -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Items to Return</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Returnable Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Return Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Restore to Stock</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Remove</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="return-items-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Items will be added here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                     <button type="button" id="add-item-btn" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Item
                    </button>
                </div>

                <!-- Section 3: Totals -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="md:col-start-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal</label>
                        <p id="subtotal" class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">$0.00</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                        <p id="total" class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">$0.00</p>
                    </div>
                </div>

                <!-- Section 4: Actions -->
                <div class="flex justify-end">
                    <a href="{{ route('sales-returns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancel</a>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Return</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('sale_id').addEventListener('change', function () {
            const saleId = this.value;
            const returnItemsBody = document.getElementById('return-items-body');
            returnItemsBody.innerHTML = ''; // Clear existing items

            if (saleId) {
                fetch(`/sales/${saleId}/returnable-items`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            const row = `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${item.product.product_name}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${item.returnable_quantity}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <input type="number" name="items[${item.id}][quantity_returned]" min="0" max="${item.returnable_quantity}" value="0" class="w-20 text-center border-gray-300 rounded-md">
                                        <input type="hidden" name="items[${item.id}][sale_item_id]" value="${item.id}">
                                        <input type="hidden" name="items[${item.id}][stock_id]" value="${item.stock_id}">
                                        <input type="hidden" name="items[${item.id}][product_id]" value="${item.product_id}">
                                        <input type="hidden" name="items[${item.id}][selling_price]" value="${item.selling_price}">
                                        <input type="hidden" name="items[${item.id}][tax]" value="${item.tax}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${item.selling_price}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <select name="items[${item.id}][condition]" class="w-full border-gray-300 rounded-md">
                                            <option>Good</option>
                                            <option>Damaged</option>
                                            <option>Defective</option>
                                            <option>Used</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <input type="checkbox" name="items[${item.id}][restore_to_stock]" value="1" checked class="rounded border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" class="text-red-600 hover:text-red-900">Remove</button>
                                    </td>
                                </tr>
                            `;
                            returnItemsBody.insertAdjacentHTML('beforeend', row);
                        });
                    });
            }
        });
    </script>
@endsection
