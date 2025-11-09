@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Create Supplier Return</h1>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('supplier-returns.store') }}" method="POST" id="supplier-return-form">
                @csrf
                <!-- Section 1: Return Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="return_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Number</label>
                        <input type="text" name="return_number" id="return_number" value="{{ $returnNumber }}" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="grn_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Good Receive Note (GRN)</label>
                        <select name="good_receive_note_id" id="grn_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">Select a GRN</option>
                            @foreach($grns as $grn)
                                <option value="{{ $grn->id }}" data-supplier-id="{{ $grn->supplier->id }}" data-supplier-name="{{ $grn->supplier->company_name }}">#{{ $grn->grn_number }} - {{ $grn->supplier->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                        <input type="text" id="supplier_name" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <input type="hidden" name="supplier_id" id="supplier_id">
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
                            <option>Damaged</option>
                            <option>Wrong Item</option>
                            <option>Defective</option>
                            <option>Overstocked</option>
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Return Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
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
                    <a href="{{ route('supplier-returns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancel</a>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Return</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const grnSelect = document.getElementById('grn_id');
            const supplierNameInput = document.getElementById('supplier_name');
            const supplierIdInput = document.getElementById('supplier_id');
            const returnItemsBody = document.getElementById('return-items-body');
            const addItemBtn = document.getElementById('add-item-btn');
            let returnableStock = [];
            let itemIndex = 0;

            grnSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const supplierId = selectedOption.dataset.supplierId;
                const supplierName = selectedOption.dataset.supplierName;
                const grnId = this.value;

                supplierIdInput.value = supplierId;
                supplierNameInput.value = supplierName;
                returnItemsBody.innerHTML = '';
                updateTotals();

                if (grnId) {
                    fetch(`/good-receive-notes/${grnId}/returnable-stock`)
                        .then(response => response.json())
                        .then(data => {
                            returnableStock = data;
                            addItemBtn.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching returnable stock:', error);
                            addItemBtn.disabled = true;
                        });
                } else {
                    returnableStock = [];
                    addItemBtn.disabled = true;
                }
            });

            addItemBtn.addEventListener('click', function () {
                if (returnableStock.length === 0) return;

                const uniqueId = `new-item-${itemIndex++}`;
                const row = `
                    <tr id="row-${uniqueId}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select name="items[${uniqueId}][stock_id]" class="stock-select w-full border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option value="">Select Product</option>
                                ${returnableStock.map(stock => `<option value="${stock.id}" data-price="${stock.cost_price}" data-tax="${stock.tax}" data-available="${stock.available_quantity}">${stock.product.name} (Batch: ${stock.batch.batch_number})</option>`).join('')}
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 available-qty">0</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" name="items[${uniqueId}][quantity_returned]" class="return-qty w-20 text-center border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" min="1" value="1">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 cost-price">0.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select name="items[${uniqueId}][condition]" class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                <option>Damaged</option>
                                <option>Defective</option>
                                <option>Wrong Item</option>
                                <option>Overstocked</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" name="items[${uniqueId}][notes]" class="w-full border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" class="remove-item-btn text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                `;
                returnItemsBody.insertAdjacentHTML('beforeend', row);
            });

            returnItemsBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('stock-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const row = e.target.closest('tr');
                    const availableQty = selectedOption.dataset.available || 0;
                    const price = selectedOption.dataset.price || 0;

                    row.querySelector('.available-qty').textContent = availableQty;
                    row.querySelector('.cost-price').textContent = parseFloat(price).toFixed(2);
                    row.querySelector('.return-qty').max = availableQty;
                    updateTotals();
                }
            });

            returnItemsBody.addEventListener('input', function (e) {
                if (e.target.classList.contains('return-qty')) {
                    updateTotals();
                }
            });

            returnItemsBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            function updateTotals() {
                let subtotal = 0;
                let totalTax = 0;

                document.querySelectorAll('#return-items-body tr').forEach(row => {
                    const stockSelect = row.querySelector('.stock-select');
                    const qtyInput = row.querySelector('.return-qty');
                    if (!stockSelect || !qtyInput) return;

                    const selectedOption = stockSelect.options[stockSelect.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const taxRate = parseFloat(selectedOption.dataset.tax) || 0;
                    const qty = parseInt(qtyInput.value, 10) || 0;

                    const itemTotal = qty * price;
                    subtotal += itemTotal;
                    totalTax += itemTotal * (taxRate / 100);
                });

                const total = subtotal + totalTax;

                document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('total').textContent = `$${total.toFixed(2)}`;
            }

            // Initial state
            addItemBtn.disabled = true;
        });
    </script>
@endsection
