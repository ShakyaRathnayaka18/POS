@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('good-receive-notes.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mb-4 inline-block">
            ← Back to GRNs
        </a>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Create Good Receive Note</h1>
    </div>

    <form action="{{ route('good-receive-notes.store') }}" method="POST" id="grnForm">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">GRN Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="grn_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">GRN Number</label>
                    <input type="text" name="grn_number" id="grn_number" value="{{ $grnNumber }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="received_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Received Date *</label>
                    <input type="date" name="received_date" id="received_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('received_date')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number *</label>
                    <input type="text" name="invoice_number" id="invoice_number" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('invoice_number')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Date *</label>
                    <input type="date" name="invoice_date" id="invoice_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('invoice_date')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                </div>
            </div>
        </div>

        <!-- Payment Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Payment Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Type *</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_type" value="cash" checked
                                class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                                onchange="toggleCreditFields()">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Cash</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_type" value="credit"
                                class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                                onchange="toggleCreditFields()">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Credit</span>
                        </label>
                    </div>
                </div>

                <!-- Credit Terms (hidden by default) -->
                <div id="creditTermsField" class="hidden">
                    <label for="credit_terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Credit Terms *</label>
                    <select name="credit_terms" id="credit_terms"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Credit Terms</option>
                        <option value="due_on_receipt">Due on Receipt (0 days)</option>
                        <option value="net_7">Net 7 Days</option>
                        <option value="net_15">Net 15 Days</option>
                        <option value="net_30">Net 30 Days</option>
                        <option value="net_60">Net 60 Days</option>
                        <option value="net_90">Net 90 Days</option>
                    </select>
                    @error('credit_terms')
                        <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Credit Limit Warning (hidden by default) -->
            <div id="creditWarning" class="hidden mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                            <span id="supplierName"></span> Credit Information
                        </p>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                            <p>Credit Limit: LKR <span id="creditLimit">0.00</span></p>
                            <p>Current Used: LKR <span id="currentUsed">0.00</span></p>
                            <p>Available Credit: LKR <span id="availableCredit" class="font-bold">0.00</span></p>
                            <p class="mt-1">GRN Total: LKR <span id="grnTotal" class="font-bold">0.00</span></p>
                            <p id="creditExceededWarning" class="hidden mt-2 text-red-600 dark:text-red-400 font-semibold">
                                <i class="fas fa-times-circle mr-1"></i>Warning: This purchase exceeds available credit!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Items</h2>
                <button type="button" id="addItemBtn" onclick="addItem()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" disabled>
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>

            <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Subtotal: LKR <span id="subtotalDisplay">0.00</span></p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Tax: LKR <span id="taxDisplay">0.00</span></p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Total: LKR <span id="totalDisplay">0.00</span></p>
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
        fetch(`{{ url('/') }}/suppliers/${supplierId}/products`)
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
        <div class="border border-gray-300 dark:border-gray-600 rounded-md p-4 mb-4 bg-gray-50 dark:bg-gray-700" id="item-${itemIndex}">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold text-gray-900 dark:text-white">Item #${itemIndex + 1}</h3>
                <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product (Vendor Code) *</label>
                    <div class="flex gap-2">
                        <select name="items[${itemIndex}][product_id]" required onchange="handleProductChange(${itemIndex}, this.value); updateCalculations();"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md product-select bg-white dark:bg-gray-600 text-gray-900 dark:text-white" id="product-select-${itemIndex}">
                            <option value="">Select Product</option>
                            ${products.map(p => `<option value="${p.id}" data-vendor-code="${p.vendor_product_code}" data-base-unit="${p.base_unit || 'pcs'}" data-purchase-unit="${p.purchase_unit || ''}" data-conversion-factor="${p.conversion_factor || 1}">${p.product_name} (Vendor: ${p.vendor_product_code}, SKU: ${p.sku})</option>`).join('')}
                        </select>
                        <button type="button" onclick="openCreateProductModal(${itemIndex})"
                            class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors"
                            title="Add New Product">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div id="unit-info-${itemIndex}" class="hidden mt-1 text-xs text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quantity <span id="qty-unit-${itemIndex}" class="text-gray-500"></span> *
                    </label>
                    <input type="number" name="items[${itemIndex}][quantity]" required min="0.0001" step="0.0001" value="1"
                        onchange="updateCalculations(); updateUnitConversionDisplay(${itemIndex});"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-white" id="quantity-${itemIndex}">
                    <div id="converted-qty-${itemIndex}" class="hidden mt-1 text-xs text-green-600 dark:text-green-400"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cost Price <span id="cost-unit-${itemIndex}" class="text-gray-500"></span> *
                    </label>
                    <input type="number" name="items[${itemIndex}][cost_price]" required min="0" step="0.01"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Selling Price <span id="sell-unit-${itemIndex}" class="text-gray-500"></span> *
                    </label>
                    <input type="number" name="items[${itemIndex}][selling_price]" required min="0" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax %</label>
                    <input type="number" name="items[${itemIndex}][tax]" min="0" step="0.01" value="0"
                        onchange="updateCalculations()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch Barcode</label>
                    <input type="text" name="items[${itemIndex}][barcode]" placeholder="Scan or enter barcode"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Unique barcode for this batch</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacture Date</label>
                    <input type="date" name="items[${itemIndex}][manufacture_date]" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Defaults to today</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiry Date</label>
                    <input type="date" name="items[${itemIndex}][expiry_date]"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Leave empty for non-perishable</p>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function handleProductChange(itemIndex, productId) {
    // Product selection handler
    if (!productId) {
        // Reset unit displays
        document.getElementById(`unit-info-${itemIndex}`).classList.add('hidden');
        document.getElementById(`qty-unit-${itemIndex}`).textContent = '';
        document.getElementById(`cost-unit-${itemIndex}`).textContent = '';
        document.getElementById(`sell-unit-${itemIndex}`).textContent = '';
        document.getElementById(`converted-qty-${itemIndex}`).classList.add('hidden');
        return;
    }

    const select = document.getElementById(`product-select-${itemIndex}`);
    const selectedOption = select.options[select.selectedIndex];
    const baseUnit = selectedOption.dataset.baseUnit || 'pcs';
    const purchaseUnit = selectedOption.dataset.purchaseUnit || '';
    const conversionFactor = parseFloat(selectedOption.dataset.conversionFactor) || 1;

    const effectivePurchaseUnit = purchaseUnit || baseUnit;
    const unitInfoEl = document.getElementById(`unit-info-${itemIndex}`);
    const qtyUnitEl = document.getElementById(`qty-unit-${itemIndex}`);
    const costUnitEl = document.getElementById(`cost-unit-${itemIndex}`);
    const sellUnitEl = document.getElementById(`sell-unit-${itemIndex}`);

    // Show purchase unit in labels
    qtyUnitEl.textContent = `(${effectivePurchaseUnit})`;
    costUnitEl.textContent = `(per ${effectivePurchaseUnit})`;
    sellUnitEl.textContent = `(per ${effectivePurchaseUnit})`;

    // Show unit conversion info if different units
    if (purchaseUnit && purchaseUnit !== baseUnit && conversionFactor !== 1) {
        unitInfoEl.innerHTML = `<i class="fas fa-info-circle mr-1"></i>Sold in: ${baseUnit} | 1 ${purchaseUnit} = ${conversionFactor} ${baseUnit}`;
        unitInfoEl.classList.remove('hidden');
    } else {
        unitInfoEl.classList.add('hidden');
    }

    // Update conversion display
    updateUnitConversionDisplay(itemIndex);
}

function updateUnitConversionDisplay(itemIndex) {
    const select = document.getElementById(`product-select-${itemIndex}`);
    if (!select || !select.value) return;

    const selectedOption = select.options[select.selectedIndex];
    const baseUnit = selectedOption.dataset.baseUnit || 'pcs';
    const purchaseUnit = selectedOption.dataset.purchaseUnit || '';
    const conversionFactor = parseFloat(selectedOption.dataset.conversionFactor) || 1;
    const quantity = parseFloat(document.getElementById(`quantity-${itemIndex}`).value) || 0;

    const convertedQtyEl = document.getElementById(`converted-qty-${itemIndex}`);

    if (purchaseUnit && purchaseUnit !== baseUnit && conversionFactor !== 1 && quantity > 0) {
        const convertedQty = quantity * conversionFactor;
        convertedQtyEl.innerHTML = `<i class="fas fa-exchange-alt mr-1"></i>Stock: ${convertedQty.toFixed(4)} ${baseUnit}`;
        convertedQtyEl.classList.remove('hidden');
    } else {
        convertedQtyEl.classList.add('hidden');
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
    document.getElementById('grnTotal').textContent = total.toFixed(2);

    // Check credit limit if payment type is credit
    checkCreditLimit();
}

// Toggle credit fields based on payment type
function toggleCreditFields() {
    const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
    const creditTermsField = document.getElementById('creditTermsField');
    const creditWarning = document.getElementById('creditWarning');
    const creditTermsSelect = document.getElementById('credit_terms');

    if (paymentType === 'credit') {
        creditTermsField.classList.remove('hidden');
        creditTermsSelect.required = true;
        loadSupplierCreditInfo();
    } else {
        creditTermsField.classList.add('hidden');
        creditWarning.classList.add('hidden');
        creditTermsSelect.required = false;
    }
}

// Load supplier credit information
let supplierCreditInfo = null;

function loadSupplierCreditInfo() {
    const supplierId = document.getElementById('supplier_id').value;

    if (!supplierId) {
        alert('Please select a supplier first');
        document.querySelector('input[name="payment_type"][value="cash"]').checked = true;
        toggleCreditFields();
        return;
    }

    // Fetch supplier credit info
    fetch(`{{ url('/') }}/suppliers/${supplierId}/credit-info`)
        .then(response => response.json())
        .then(data => {
            supplierCreditInfo = data;
            document.getElementById('supplierName').textContent = data.company_name + "'s";
            document.getElementById('creditLimit').textContent = parseFloat(data.credit_limit).toFixed(2);
            document.getElementById('currentUsed').textContent = parseFloat(data.current_credit_used).toFixed(2);
            document.getElementById('availableCredit').textContent = parseFloat(data.available_credit).toFixed(2);
            document.getElementById('creditWarning').classList.remove('hidden');
            checkCreditLimit();
        })
        .catch(error => {
            console.error('Error fetching supplier credit info:', error);
            alert('Error loading supplier credit information');
        });
}

// Check if GRN total exceeds available credit
function checkCreditLimit() {
    if (!supplierCreditInfo) return;

    const total = parseFloat(document.getElementById('totalDisplay').textContent);
    const availableCredit = parseFloat(supplierCreditInfo.available_credit);
    const warningElement = document.getElementById('creditExceededWarning');

    if (total > availableCredit) {
        warningElement.classList.remove('hidden');
    } else {
        warningElement.classList.add('hidden');
    }
}

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});

// Product creation modal
let currentItemIndexForNewProduct = null;

function openCreateProductModal(itemIndex) {
    currentItemIndexForNewProduct = itemIndex;
    document.getElementById('createProductModal').classList.remove('hidden');
}

function closeCreateProductModal() {
    document.getElementById('createProductModal').classList.add('hidden');
    document.getElementById('createProductForm').reset();
    currentItemIndexForNewProduct = null;
}

function submitProductForm(event) {
    event.preventDefault();

    const form = document.getElementById('createProductForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const errorContainer = document.getElementById('productFormErrors');

    // Add supplier_id to form data
    formData.append('supplier_id', currentSupplierId);

    // Disable submit button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    errorContainer.innerHTML = '';

    fetch('{{ route('products.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add product to products array
            products.push(data.product);

            // Add product to the dropdown
            const select = document.getElementById(`product-select-${currentItemIndexForNewProduct}`);
            const option = document.createElement('option');
            option.value = data.product.id;
            option.setAttribute('data-vendor-code', data.product.vendor_product_code);
            option.textContent = `${data.product.product_name} (Vendor: ${data.product.vendor_product_code}, SKU: ${data.product.sku})`;
            select.appendChild(option);

            // Select the new product
            select.value = data.product.id;
            handleProductChange(currentItemIndexForNewProduct, data.product.id);

            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message);
            } else {
                alert(data.message);
            }

            // Close modal
            closeCreateProductModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.response && error.response.data && error.response.data.errors) {
            // Display validation errors
            const errors = error.response.data.errors;
            let errorHtml = '<div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-4"><ul class="list-disc list-inside">';
            Object.values(errors).forEach(errorArray => {
                errorArray.forEach(errorMsg => {
                    errorHtml += `<li>${errorMsg}</li>`;
                });
            });
            errorHtml += '</ul></div>';
            errorContainer.innerHTML = errorHtml;
        } else {
            errorContainer.innerHTML = '<div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-4">An error occurred. Please try again.</div>';
        }
    })
    .finally(() => {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>Create Product';
    });
}
</script>

<!-- Create Product Modal -->
<div id="createProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
    <div class="relative w-full max-w-2xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-plus-circle mr-2 text-blue-500"></i>Create New Product
            </h3>
            <button type="button" onclick="closeCreateProductModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="createProductForm" onsubmit="submitProductForm(event)" class="p-6">
            <div id="productFormErrors"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Name *</label>
                    <input type="text" name="product_name" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                    <select name="brand_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor Product Code</label>
                    <input type="text" name="vendor_product_code"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Supplier's code for this product</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit</label>
                    <input type="text" name="unit" placeholder="e.g., pcs, kg, ltr"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeCreateProductModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
