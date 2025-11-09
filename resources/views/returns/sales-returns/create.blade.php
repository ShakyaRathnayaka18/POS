@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Create Sales Return</h1>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Validation Error!</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('sales-returns.store') }}" method="POST" id="sales-return-form">
                @csrf
                <!-- Hidden fields for calculated totals -->
                <input type="hidden" name="subtotal" id="hidden-subtotal" value="0.00">
                <input type="hidden" name="tax" id="hidden-tax" value="0.00">
                <input type="hidden" name="total" id="hidden-total" value="0.00">
                <input type="hidden" name="refund_amount" id="hidden-refund-amount" value="0.00">

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
                                <option value="{{ $sale->id }}" {{ (request()->get('sale_id') == $sale->id) ? 'selected' : '' }}>
                                    #{{ $sale->sale_number }} - {{ $sale->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Name</label>
                        <input type="text" name="customer_name" id="customer_name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone"
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
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"></textarea>
                    </div>
                    <div>
                        <label for="refund_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refund Method</label>
                        <select name="refund_method" id="refund_method"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option>Cash</option>
                            <option>Credit</option>
                            <option>Bank Transfer</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="Pending">Pending</option>
                            <option value="Processed">Processed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
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
        document.addEventListener('DOMContentLoaded', function () {
            const saleIdSelect = document.getElementById('sale_id');
            const returnItemsBody = document.getElementById('return-items-body');
            const customerNameInput = document.getElementById('customer_name');
            const customerPhoneInput = document.getElementById('customer_phone');

            let itemIndex = 0; // Counter for naming form fields uniquely

            function fetchReturnableItems(saleId) {
                if (!saleId) {
                    returnItemsBody.innerHTML = '';
                    updateTotals();
                    itemIndex = 0;
                    return;
                }

                fetch(`/sales-returns/get-returnable-items/${saleId}`)
                    .then(response => response.json())
                    .then(data => {
                        returnItemsBody.innerHTML = '';
                        itemIndex = 0;
                        if (data.items.length === 0) {
                            returnItemsBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No returnable items for this sale.</td></tr>';
                            updateTotals();
                            return;
                        }

                        customerNameInput.value = data.sale.customer_name || '';
                        customerPhoneInput.value = data.sale.customer_phone || '';

                        data.items.forEach(item => {
                            const row = createReturnItemRow(item, itemIndex);
                            returnItemsBody.insertAdjacentHTML('beforeend', row);
                            itemIndex++;
                        });
                        updateTotals();
                    })
                    .catch(error => {
                        console.error('Error fetching returnable items:', error);
                        returnItemsBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-red-500">Error loading items.</td></tr>';
                    });
            }

            function createReturnItemRow(item, index) {
                const uniqueId = `item-${item.sale_item_id}-${index}`;
                return `
                    <tr id="${uniqueId}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                            ${item.product_name || 'N/A'}
                            <input type="hidden" name="items[${uniqueId}][sale_item_id]" value="${item.sale_item_id}">
                            <input type="hidden" name="items[${uniqueId}][stock_id]" value="${item.stock_id}">
                            <input type="hidden" name="items[${uniqueId}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${uniqueId}][selling_price]" value="${item.selling_price}">
                            <input type="hidden" name="items[${uniqueId}][tax]" value="${item.tax}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ${item.quantity_returnable || 0}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" class="return-qty w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" 
                                   data-price="${item.selling_price || 0}" 
                                   data-tax="${item.tax || 0}" 
                                   name="items[${uniqueId}][quantity_returned]" 
                                   min="0" 
                                   max="${item.quantity_returnable || 0}" 
                                   value="0" 
                                   step="1">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            $${parseFloat(item.selling_price || 0).toFixed(2)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select class="condition-select w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" 
                                    name="items[${uniqueId}][condition]">
                                <option value="Good">Good</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Used">Used</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="restore-checkbox rounded border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600" 
                                   name="items[${uniqueId}][restore_to_stock]" 
                                   value="1" 
                                   checked>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="remove-item-btn text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                `;
            }

            function updateTotals() {
                let subtotal = 0;
                let totalTax = 0;

                document.querySelectorAll('.return-qty').forEach(input => {
                    const qty = parseInt(input.value, 10) || 0;
                    const price = parseFloat(input.dataset.price) || 0;
                    const taxRate = parseFloat(input.dataset.tax) || 0;

                    const itemTotal = qty * price;
                    subtotal += itemTotal;
                    totalTax += itemTotal * (taxRate / 100);
                });

                const total = subtotal + totalTax;

                document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('total').textContent = `$${total.toFixed(2)}`;

                // Update hidden form fields
                document.getElementById('hidden-subtotal').value = subtotal.toFixed(2);
                document.getElementById('hidden-tax').value = totalTax.toFixed(2);
                document.getElementById('hidden-total').value = total.toFixed(2);
                document.getElementById('hidden-refund-amount').value = total.toFixed(2);
            }

            saleIdSelect.addEventListener('change', () => fetchReturnableItems(saleIdSelect.value));

            returnItemsBody.addEventListener('input', function (e) {
                if (e.target.classList.contains('return-qty')) {
                    // Ensure qty doesn't exceed max
                    const max = parseInt(e.target.max) || 0;
                    if (parseInt(e.target.value) > max) {
                        e.target.value = max;
                    }
                    updateTotals();
                }
            });

            returnItemsBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('condition-select')) {
                    const row = e.target.closest('tr');
                    const restoreCheckbox = row.querySelector('.restore-checkbox');
                    if (e.target.value !== 'Good') {
                        restoreCheckbox.checked = false;
                        restoreCheckbox.disabled = true;
                    } else {
                        restoreCheckbox.disabled = false;
                    }
                }
            });

            returnItemsBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            // Initial load if a sale is pre-selected
            if (saleIdSelect.value) {
                fetchReturnableItems(saleIdSelect.value);
            }
        });
    </script>
@endsection