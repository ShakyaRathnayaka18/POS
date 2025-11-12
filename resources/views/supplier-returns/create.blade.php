@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('supplier-returns.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4 inline-block">
            &larr; Back to Supplier Returns
        </a>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Create Supplier Return</h1>
    </div>

    <form action="{{ route('supplier-returns.store') }}" method="POST" id="returnForm">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Return Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="return_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Number</label>
                    <input type="text" name="return_number" id="return_number" value="{{ $returnNumber }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label for="good_receive_note_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Good Receive Note (GRN) *</label>
                    <select name="good_receive_note_id" id="good_receive_note_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                        <option value="">Select GRN</option>
                        @foreach($grns as $grnOption)
                        <option value="{{ $grnOption->id }}"
                            data-supplier-id="{{ $grnOption->supplier_id }}"
                            data-supplier-name="{{ $grnOption->supplier->company_name }}"
                            {{ ($grn && $grn->id == $grnOption->id) ? 'selected' : '' }}>
                            {{ $grnOption->grn_number }} - {{ $grnOption->supplier->company_name }} ({{ $grnOption->received_date }})
                        </option>
                        @endforeach
                    </select>
                    @error('good_receive_note_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <input type="text" id="supplier_name" value="{{ $grn->supplier->company_name ?? '' }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $grn->supplier_id ?? '' }}">
                </div>

                <div>
                    <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Date *</label>
                    <input type="date" name="return_date" id="return_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                    @error('return_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="return_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Reason *</label>
                    <select name="return_reason" id="return_reason" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                        <option value="Damaged">Damaged</option>
                        <option value="Wrong Item">Wrong Item</option>
                        <option value="Defective">Defective</option>
                        <option value="Overstocked">Overstocked</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Items to Return</h2>
                <button type="button" id="addItemBtn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" disabled>
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center">
                <div class="text-gray-800 dark:text-white">
                    <p class="text-lg font-semibold">Subtotal: LKR <span id="subtotalDisplay">0.00</span></p>
                    <p class="text-lg font-semibold">Tax: LKR <span id="taxDisplay">0.00</span></p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Total: LKR <span id="totalDisplay">0.00</span></p>
                </div>
                <div class="space-x-4">
                    <a href="{{ route('supplier-returns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
                    <button type="submit" class="bg-blue-900 hover:bg-blue-900 text-black py-2 px-6 rounded border border-b-blue-900">Create Return</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let itemIndex = 0;
    let returnableStock = [];
    let currentGrnId = '{{ $grn->id ?? '
    ' }}';

    document.addEventListener('DOMContentLoaded', function() {
        const grnSelect = document.getElementById('good_receive_note_id');

        // If a GRN is pre-selected, load its stock
        if (currentGrnId) {
            loadReturnableStock(currentGrnId);
        }

        grnSelect.addEventListener('change', function() {
            currentGrnId = this.value;

            // Update supplier information
            const selectedOption = this.options[this.selectedIndex];
            if (currentGrnId) {
                const supplierId = selectedOption.getAttribute('data-supplier-id');
                const supplierName = selectedOption.getAttribute('data-supplier-name');
                document.getElementById('supplier_id').value = supplierId;
                document.getElementById('supplier_name').value = supplierName;
            } else {
                document.getElementById('supplier_id').value = '';
                document.getElementById('supplier_name').value = '';
            }

            loadReturnableStock(currentGrnId);
        });

        document.getElementById('addItemBtn').addEventListener('click', addItem);
    });

    function loadReturnableStock(grnId) {
        const addItemBtn = document.getElementById('addItemBtn');
        if (grnId) {
            addItemBtn.disabled = true;
            fetch(`/good-receive-notes/${grnId}/returnable-stock`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    returnableStock = data;
                    addItemBtn.disabled = false;
                    document.getElementById('itemsContainer').innerHTML = '';
                    itemIndex = 0;
                    // You might want to auto-fill supplier info here if not already done
                })
                .catch(error => {
                    console.error('Error fetching returnable stock:', error);
                    alert('Error loading stock for this GRN. Please ensure the GRN exists and has available stock.');
                    returnableStock = [];
                    addItemBtn.disabled = true;
                });
        } else {
            returnableStock = [];
            addItemBtn.disabled = true;
            document.getElementById('itemsContainer').innerHTML = '';
            itemIndex = 0;
        }
    }

    function addItem() {
        if (!currentGrnId) {
            alert('Please select a GRN first');
            return;
        }

        if (returnableStock.length === 0) {
            alert('This GRN has no returnable stock.');
            return;
        }

        const container = document.getElementById('itemsContainer');
        const itemHtml = `
        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 mb-4" id="item-${itemIndex}">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-800 dark:text-white">Item #${itemIndex + 1}</h3>
                <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product *</label>
                    <select name="items[${itemIndex}][stock_id]" required onchange="handleStockChange(${itemIndex}, this.value); updateCalculations();"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md product-select bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                        <option value="">Select Product</option>
                        ${returnableStock.map(s => `<option value="${s.id}" data-cost="${s.cost_price}" data-tax="${s.tax}" data-available="${s.available_quantity}">${s.product.product_name} (Batch: ${s.batch.batch_number}, Available: ${s.available_quantity})</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity to Return *</label>
                    <input type="number" name="items[${itemIndex}][quantity_returned]" required min="1" value="1"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost Price</label>
                    <input type="number" name="items[${itemIndex}][cost_price]" required readonly
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <input type="hidden" name="items[${itemIndex}][tax]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition *</label>
                    <select name="items[${itemIndex}][condition]" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                        <option value="Damaged">Damaged</option>
                        <option value="Defective">Defective</option>
                        <option value="Wrong Item">Wrong Item</option>
                        <option value="Overstocked">Overstocked</option>
                    </select>
                </div>
            </div>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', itemHtml);
        itemIndex++;
    }

    function handleStockChange(index, stockId) {
        if (!stockId) return;

        const stock = returnableStock.find(s => s.id == stockId);
        if (!stock) return;

        const itemContainer = document.getElementById(`item-${index}`);
        if (!itemContainer) return;

        const costPriceInput = itemContainer.querySelector('[name*="[cost_price]"]');
        costPriceInput.value = stock.cost_price;

        const taxInput = itemContainer.querySelector('[name*="[tax]"]');
        taxInput.value = stock.tax;

        const quantityInput = itemContainer.querySelector('[name*="[quantity_returned]"]');
        quantityInput.max = stock.available_quantity;

        updateCalculations();
    }

    function removeItem(index) {
        document.getElementById(`item-${index}`).remove();
        updateCalculations();
    }

    function updateCalculations() {
        let subtotal = 0;
        let tax = 0;

        document.querySelectorAll('#itemsContainer > div').forEach(item => {
            const quantity = parseFloat(item.querySelector('[name*="[quantity_returned]"]')?.value || 0);
            const costPrice = parseFloat(item.querySelector('[name*="[cost_price]"]')?.value || 0);
            const taxRate = parseFloat(item.querySelector('[name*="[tax]"]')?.value || 0);

            const itemSubtotal = quantity * costPrice;
            const itemTax = itemSubtotal * (taxRate / 100);

            subtotal += itemSubtotal;
            tax += itemTax;
        });

        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = tax.toFixed(2);
        document.getElementById('totalDisplay').textContent = (subtotal + tax).toFixed(2);
    }
</script>
@endsection