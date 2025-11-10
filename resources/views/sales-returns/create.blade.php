@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('sales-returns.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4 inline-block">
            &larr; Back to Sales Returns
        </a>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Create Sales Return</h1>
    </div>

    <form action="{{ route('sales-returns.store') }}" method="POST" id="returnForm">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Return Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="return_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Number</label>
                    <input type="text" name="return_number" id="return_number" value="{{ $returnNumber }}" readonly class="w-full form-input bg-gray-100 dark:bg-gray-700">
                </div>

                <div>
                    <label for="sale_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Original Sale *</label>
                    <select name="sale_id" id="sale_id" required class="w-full form-select">
                        <option value="">Select a Sale</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}" @if($sale && $sale->id == $s->id) selected @endif>Sale #{{ $s->id }} - {{ $s->created_at->format('Y-m-d H:i') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Date *</label>
                    <input type="date" name="return_date" id="return_date" value="{{ date('Y-m-d') }}" required class="w-full form-input">
                </div>

                <div>
                    <label for="return_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Reason *</label>
                    <input type="text" name="return_reason" id="return_reason" required class="w-full form-input">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full form-input"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Items to Return</h2>
            <div id="itemsContainer" class="space-y-4">
                <!-- Items will be loaded here -->
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center">
                <div class="text-gray-800 dark:text-white">
                    <p class="text-lg font-semibold">Subtotal: $<span id="subtotalDisplay">0.00</span></p>
                    <p class="text-lg font-semibold">Tax: $<span id="taxDisplay">0.00</span></p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Total: $<span id="totalDisplay">0.00</span></p>
                </div>
                <div class="space-x-4">
                    <a href="{{ route('sales-returns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Create Return</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleSelect = document.getElementById('sale_id');
    
    if (saleSelect.value) {
        loadReturnableItems(saleSelect.value);
    }

    saleSelect.addEventListener('change', function() {
        loadReturnableItems(this.value);
    });
});

function loadReturnableItems(saleId) {
    const itemsContainer = document.getElementById('itemsContainer');
    itemsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Loading items...</p>';

    if (!saleId) {
        itemsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Select a sale to see returnable items.</p>';
        return;
    }

    fetch(`/sales-returns/get-returnable-items/${saleId}`)
        .then(response => response.json())
        .then(data => {
            itemsContainer.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach((item, index) => {
                    const itemHtml = `
                        <div class="border dark:border-gray-700 rounded-md p-4">
                            <input type="hidden" name="items[${index}][sale_item_id]" value="${item.sale_item_id}">
                            <input type="hidden" name="items[${index}][stock_id]" value="${item.stock_id}">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][selling_price]" value="${item.selling_price}">
                            <input type="hidden" name="items[${index}][tax]" value="${item.tax}">

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">${item.product_name} (${item.sku})</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sold: ${item.quantity_sold} @ $${item.selling_price}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Qty to Return (Max: ${item.quantity_returnable})</label>
                                    <input type="number" name="items[${index}][quantity_returned]" min="0" max="${item.quantity_returnable}" value="0" onchange="updateCalculations()" class="w-full form-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condition</label>
                                    <select name="items[${index}][condition]" class="w-full form-select">
                                        <option value="Good">Good</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Defective">Defective</option>
                                        <option value="Used">Used</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                });
            } else {
                itemsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No returnable items for this sale.</p>';
            }
            updateCalculations();
        })
        .catch(error => {
            console.error('Error:', error);
            itemsContainer.innerHTML = '<p class="text-red-500">Error loading items.</p>';
        });
}

function updateCalculations() {
    let subtotal = 0;
    let tax = 0;

    document.querySelectorAll('#itemsContainer > div').forEach(itemEl => {
        const quantity = parseFloat(itemEl.querySelector('[name*="[quantity_returned]"]').value) || 0;
        const price = parseFloat(itemEl.querySelector('[name*="[selling_price]"]').value) || 0;
        const taxRate = parseFloat(itemEl.querySelector('[name*="[tax]"]').value) || 0;
        
        if (quantity > 0) {
            const itemSubtotal = quantity * price;
            subtotal += itemSubtotal;
            tax += itemSubtotal * (taxRate / 100);
        }
    });

    document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
    document.getElementById('taxDisplay').textContent = tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = (subtotal + tax).toFixed(2);
}
</script>
@endsection
