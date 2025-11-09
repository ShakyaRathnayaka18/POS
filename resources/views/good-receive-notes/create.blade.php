@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('good-receive-notes.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Back to GRNs
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Create Good Receive Note</h1>
    </div>

    <form action="{{ route('good-receive-notes.store') }}" method="POST" id="grnForm">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">GRN Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="grn_number" class="block text-sm font-medium text-gray-700 mb-2">GRN Number</label>
                    <input type="text" name="grn_number" id="grn_number" value="{{ $grnNumber }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700">
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="received_date" class="block text-sm font-medium text-gray-700 mb-2">Received Date *</label>
                    <input type="date" name="received_date" id="received_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('received_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Items</h2>
                <button type="button" id="addItemBtn" onclick="addItem()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" disabled>
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold">Subtotal: $<span id="subtotalDisplay">0.00</span></p>
                    <p class="text-lg font-semibold">Tax: $<span id="taxDisplay">0.00</span></p>
                    <p class="text-2xl font-bold text-green-600">Total: $<span id="totalDisplay">0.00</span></p>
                </div>
                <div class="space-x-4">
                    <a href="{{ route('good-receive-notes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                        Create GRN
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;
let products = [];
let currentSupplierId = null;

// Load products when supplier is selected
document.getElementById('supplier_id').addEventListener('change', function() {
    const supplierId = this.value;
    currentSupplierId = supplierId;
    const addItemBtn = document.getElementById('addItemBtn');

    if (supplierId) {
        addItemBtn.disabled = false;
        // Fetch supplier products via AJAX
        fetch(`/suppliers/${supplierId}/products`)
            .then(response => response.json())
            .then(data => {
                products = data;
                // Clear existing items when supplier changes
                document.getElementById('itemsContainer').innerHTML = '';
                itemIndex = 0;
            })
            .catch(error => {
                console.error('Error fetching supplier products:', error);
                alert('Error loading products for this supplier');
            });
    } else {
        addItemBtn.disabled = true;
        products = [];
        document.getElementById('itemsContainer').innerHTML = '';
        itemIndex = 0;
    }
});

function addItem() {
    if (!currentSupplierId) {
        alert('Please select a supplier first');
        return;
    }

    if (products.length === 0) {
        alert('This supplier has no products assigned. Please link products to this supplier first.');
        return;
    }

    const container = document.getElementById('itemsContainer');
    const itemHtml = `
        <div class="border border-gray-300 rounded-md p-4 mb-4" id="item-${itemIndex}">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold">Item #${itemIndex + 1}</h3>
                <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product (Vendor Code) *</label>
                    <select name="items[${itemIndex}][product_id]" required onchange="handleProductChange(${itemIndex}, this.value); updateCalculations();"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md product-select">
                        <option value="">Select Product</option>
                        ${products.map(p => `<option value="${p.id}" data-vendor-code="${p.vendor_product_code}" data-vendor-cost="${p.vendor_cost_price || ''}">${p.product_name} (Vendor: ${p.vendor_product_code}, SKU: ${p.sku})</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" name="items[${itemIndex}][cost_price]" required min="0" step="0.01"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                    <input type="number" name="items[${itemIndex}][selling_price]" required min="0" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax %</label>
                    <input type="number" name="items[${itemIndex}][tax]" min="0" step="0.01" value="0"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch Barcode</label>
                    <input type="text" name="items[${itemIndex}][barcode]" placeholder="Scan or enter barcode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional: Unique barcode for this batch</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacture Date</label>
                    <input type="date" name="items[${itemIndex}][manufacture_date]" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional: Defaults to today</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="items[${itemIndex}][expiry_date]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional: Leave empty for non-perishable</p>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function handleProductChange(itemIndex, productId) {
    if (!productId) return;

    // Find the product in the products array
    const product = products.find(p => p.id == productId);
    if (!product) return;

    // Get the item container
    const itemContainer = document.getElementById(`item-${itemIndex}`);
    if (!itemContainer) return;

    // Auto-fill cost price if vendor cost price is available
    if (product.vendor_cost_price) {
        const costPriceInput = itemContainer.querySelector('[name*="[cost_price]"]');
        if (costPriceInput && !costPriceInput.value) {
            costPriceInput.value = product.vendor_cost_price;
        }
    }
}

function removeItem(index) {
    document.getElementById(`item-${index}`).remove();
    updateCalculations();
}

function updateCalculations() {
    let subtotal = 0;
    let tax = 0;

    document.querySelectorAll('#itemsContainer > div').forEach(item => {
        const quantity = parseFloat(item.querySelector('[name*="[quantity]"]')?.value || 0);
        const costPrice = parseFloat(item.querySelector('[name*="[cost_price]"]')?.value || 0);
        const taxRate = parseFloat(item.querySelector('[name*="[tax]"]')?.value || 0);

        const itemSubtotal = quantity * costPrice;
        const itemTax = itemSubtotal * (taxRate / 100);

        subtotal += itemSubtotal;
        tax += itemTax;
    });

    const total = subtotal + tax;

    document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
    document.getElementById('taxDisplay').textContent = tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = total.toFixed(2);
}
</script>
@endsection
