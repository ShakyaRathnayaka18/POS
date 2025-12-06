@extends('layouts.app')

@section('title', 'Vendor Codes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-black">Vendor Code Management</h1>
        <div class="flex gap-2">
            <button onclick="openModal('syncModal')" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-md transition-colors shadow-md border border-green-400">
                <i class="fas fa-sync mr-2"></i>Auto Sync Codes
            </button>
            <button onclick="openModal('createModal')" class="bg-blue-500 dark:bg-blue-700 hover:bg-blue-600 dark:hover:bg-blue-800 text-black py-2 px-4 rounded-md transition-colors shadow-md border border-blue-300 dark:border-blue-500">
                <i class="fas fa-plus mr-2"></i>Add Vendor Code
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Mappings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMappings }}</p>
                </div>
                <i class="fas fa-link text-blue-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Preferred Vendors</p>
                    <p class="text-2xl font-bold text-green-600">{{ $preferredMappings }}</p>
                </div>
                <i class="fas fa-star text-green-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Products Mapped</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $productsWithMappings }}</p>
                </div>
                <i class="fas fa-box text-purple-500 text-3xl"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Suppliers Active</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $suppliersWithMappings }}</p>
                </div>
                <i class="fas fa-truck text-indigo-500 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('vendor-codes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Vendor code, product, supplier..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                <select name="supplier_id" id="supplier_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
                <select name="product_id" id="product_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="is_preferred" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred</label>
                <select name="is_preferred" id="is_preferred"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All</option>
                    <option value="1" {{ request('is_preferred') == '1' ? 'selected' : '' }}>Preferred Only</option>
                    <option value="0" {{ request('is_preferred') == '0' ? 'selected' : '' }}>Standard Only</option>
                </select>
            </div>
            <div class="flex items-end gap-4">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-black font-bold py-2 px-4 rounded-md transition-colors" style="border: 1px solid #60a5fa !important;">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('vendor-codes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-redo"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Vendor Codes Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendor Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lead Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($vendorCodes as $vendorCode)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $vendorCode->product_name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $vendorCode->internal_sku }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $vendorCode->company_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-mono">
                            {{ $vendorCode->vendor_product_code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $vendorCode->lead_time_days ? $vendorCode->lead_time_days . ' days' : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($vendorCode->is_preferred)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-star mr-1"></i> Preferred
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Standard
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <button onclick="openEditModal({{ json_encode($vendorCode) }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('vendor-codes.destroy', $vendorCode->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vendor code mapping?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No vendor code mappings found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $vendorCodes->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Vendor Code Mapping</h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4" method="POST" action="{{ route('vendor-codes.store') }}">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product *</label>
                    <select name="product_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier *</label>
                    <select name="supplier_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="auto_generate" name="auto_generate" value="1"
                               onchange="toggleAutoGenerate(this.checked)"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="auto_generate" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Auto-generate vendor code
                        </label>
                    </div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor Product Code <span id="vendor_code_required">*</span></label>
                    <input id="vendor_product_code" name="vendor_product_code" type="text" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <div id="vendor_code_preview" class="hidden mt-2 text-sm text-green-600 dark:text-green-400">
                        Preview: <span id="preview_code" class="font-mono font-bold"></span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lead Time (days)</label>
                    <input name="lead_time_days" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex items-center">
                    <input name="is_preferred" type="checkbox" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Mark as Preferred Supplier</label>
                </div>
                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button" onclick="closeModal('createModal')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border" style="border-color: #4ea9dd; border-width: 1px;">Save Mapping</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Vendor Code Mapping</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" class="space-y-4" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
                    <input id="edit_product_name" type="text" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <input id="edit_supplier_name" type="text" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor Product Code *</label>
                    <input id="edit_vendor_product_code" name="vendor_product_code" type="text" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lead Time (days)</label>
                    <input id="edit_lead_time_days" name="lead_time_days" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex items-center">
                    <input id="edit_is_preferred" name="is_preferred" type="checkbox" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Mark as Preferred Supplier</label>
                </div>
                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border" style="border-color: #4ea9dd; border-width: 1px;">Update Mapping</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto Sync Modal -->
<div id="syncModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-sync mr-2 text-green-500"></i>Auto Sync Vendor Codes
                </h3>
                <button onclick="closeModal('syncModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="syncForm" method="POST" action="{{ route('vendor-codes.bulk-sync') }}">
                @csrf

                <!-- Step 1: Select Supplier -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Supplier <span class="text-red-500">*</span>
                    </label>
                    @if($suppliers->count() > 0)
                        <select id="sync_supplier_id" name="supplier_id" required onchange="onSyncSupplierChange()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Choose a supplier...</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    No suppliers found.
                                    <a href="{{ route('suppliers.create') }}" class="font-medium underline hover:text-yellow-800">
                                        Create a supplier first
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Step 2: Select Brand (shown after supplier is selected) -->
                <div id="sync_brand_container" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Brand <span class="text-red-500">*</span>
                    </label>
                    <select id="sync_brand_id" onchange="loadProductsForSync()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Choose a brand...</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 3: Product List with Checkboxes -->
                <div id="sync_products_container" class="hidden">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Products without vendor codes (<span id="product_count">0</span>)
                        </label>
                        <div>
                            <button type="button" onclick="selectAllProducts()" class="text-xs text-blue-600 hover:underline mr-2">Select All</button>
                            <button type="button" onclick="deselectAllProducts()" class="text-xs text-gray-600 hover:underline">Deselect All</button>
                        </div>
                    </div>

                    <div id="sync_products_list" class="max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-gray-50 dark:bg-gray-700">
                        <!-- Products will be loaded here via AJAX -->
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Vendor codes will be auto-generated: {Supplier prefix}-{SKU number}
                    </p>
                </div>

                <!-- Loading indicator -->
                <div id="sync_loading" class="hidden text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Loading products...</p>
                </div>

                <!-- No products message -->
                <div id="sync_no_products" class="hidden text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-green-500"></i>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">All products already have vendor codes for this supplier!</p>
                </div>

                <div class="flex justify-end gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" onclick="closeModal('syncModal')"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="sync_submit_btn" disabled
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-sync mr-2"></i>Sync Selected (<span id="selected_count">0</span>)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditModal(vendorCode) {
        document.getElementById('edit_product_name').value = vendorCode.product_name + ' (' + vendorCode.internal_sku + ')';
        document.getElementById('edit_supplier_name').value = vendorCode.company_name;
        document.getElementById('edit_vendor_product_code').value = vendorCode.vendor_product_code;
        document.getElementById('edit_lead_time_days').value = vendorCode.lead_time_days || '';
        document.getElementById('edit_is_preferred').checked = vendorCode.is_preferred == 1;
        document.getElementById('editForm').action = '{{ url('/vendor-codes') }}/' + vendorCode.id;
        openModal('editModal');
    }

    // Auto-generate vendor code functions
    function toggleAutoGenerate(isChecked) {
        const vendorCodeInput = document.getElementById('vendor_product_code');
        const previewDiv = document.getElementById('vendor_code_preview');
        const requiredSpan = document.getElementById('vendor_code_required');

        if (isChecked) {
            vendorCodeInput.disabled = true;
            vendorCodeInput.required = false;
            vendorCodeInput.value = '';
            vendorCodeInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
            previewDiv.classList.remove('hidden');
            requiredSpan.classList.add('hidden');
            updateVendorCodePreview();
        } else {
            vendorCodeInput.disabled = false;
            vendorCodeInput.required = true;
            vendorCodeInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
            previewDiv.classList.add('hidden');
            requiredSpan.classList.remove('hidden');
        }
    }

    function updateVendorCodePreview() {
        const supplierSelect = document.querySelector('#createModal select[name="supplier_id"]');
        const productSelect = document.querySelector('#createModal select[name="product_id"]');
        const previewCode = document.getElementById('preview_code');

        if (!supplierSelect || !productSelect || !previewCode) return;

        const supplierId = supplierSelect.value;
        const productId = productSelect.value;

        if (supplierId && productId) {
            const supplierOption = supplierSelect.options[supplierSelect.selectedIndex];
            const productOption = productSelect.options[productSelect.selectedIndex];

            const supplierName = supplierOption.textContent.trim();
            const productText = productOption.textContent.trim();

            // Extract SKU from product text (format: "Product Name (SKU-000020)")
            const skuMatch = productText.match(/\(([^)]+)\)$/);
            const sku = skuMatch ? skuMatch[1] : '';

            // Generate preview: first 3 letters of supplier (letters only) + numeric part of SKU
            const prefix = supplierName.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();
            const numericMatch = sku.match(/(\d+)$/);
            const numeric = numericMatch ? numericMatch[1] : '000000';

            previewCode.textContent = prefix + '-' + numeric;
        } else {
            previewCode.textContent = 'Select supplier and product first';
        }
    }

    // Add event listeners for supplier/product changes to update preview
    document.addEventListener('DOMContentLoaded', function() {
        const supplierSelect = document.querySelector('#createModal select[name="supplier_id"]');
        const productSelect = document.querySelector('#createModal select[name="product_id"]');

        if (supplierSelect) {
            supplierSelect.addEventListener('change', function() {
                if (document.getElementById('auto_generate').checked) {
                    updateVendorCodePreview();
                }
            });
        }

        if (productSelect) {
            productSelect.addEventListener('change', function() {
                if (document.getElementById('auto_generate').checked) {
                    updateVendorCodePreview();
                }
            });
        }
    });

    // ===== Auto Sync Functions =====

    function onSyncSupplierChange() {
        const supplierId = document.getElementById('sync_supplier_id').value;
        const brandContainer = document.getElementById('sync_brand_container');
        const brandSelect = document.getElementById('sync_brand_id');

        // Reset brand and products
        brandSelect.value = '';
        document.getElementById('sync_products_container').classList.add('hidden');
        document.getElementById('sync_no_products').classList.add('hidden');
        document.getElementById('sync_products_list').innerHTML = '';
        document.getElementById('sync_submit_btn').disabled = true;

        if (supplierId) {
            brandContainer.classList.remove('hidden');
        } else {
            brandContainer.classList.add('hidden');
        }
    }

    function loadProductsForSync() {
        const supplierId = document.getElementById('sync_supplier_id').value;
        const brandId = document.getElementById('sync_brand_id').value;
        const container = document.getElementById('sync_products_container');
        const list = document.getElementById('sync_products_list');
        const loading = document.getElementById('sync_loading');
        const noProducts = document.getElementById('sync_no_products');
        const submitBtn = document.getElementById('sync_submit_btn');

        // Reset state
        container.classList.add('hidden');
        noProducts.classList.add('hidden');
        submitBtn.disabled = true;

        if (!supplierId || !brandId) {
            return;
        }

        // Show loading
        loading.classList.remove('hidden');

        fetch(`{{ url('vendor-codes/products-without-codes') }}?supplier_id=${supplierId}&brand_id=${brandId}`)
            .then(response => response.json())
            .then(products => {
                loading.classList.add('hidden');

                if (products.length === 0) {
                    noProducts.classList.remove('hidden');
                    return;
                }

                // Build product list with checkboxes
                let html = '';
                products.forEach(product => {
                    html += `
                        <div class="flex items-center py-2 hover:bg-gray-100 dark:hover:bg-gray-600 px-2 rounded">
                            <input type="checkbox" name="product_ids[]" value="${product.id}"
                                id="sync_product_${product.id}" checked onchange="updateSelectedCount()"
                                class="h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                            <label for="sync_product_${product.id}" class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex-1 cursor-pointer">
                                ${product.product_name}
                            </label>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">${product.sku}</span>
                        </div>
                    `;
                });

                list.innerHTML = html;
                document.getElementById('product_count').textContent = products.length;
                container.classList.remove('hidden');
                updateSelectedCount();
            })
            .catch(error => {
                loading.classList.add('hidden');
                console.error('Error loading products:', error);
                list.innerHTML = '<p class="text-red-500 text-center py-4">Error loading products. Please try again.</p>';
                container.classList.remove('hidden');
            });
    }

    function selectAllProducts() {
        document.querySelectorAll('#sync_products_list input[type="checkbox"]').forEach(cb => cb.checked = true);
        updateSelectedCount();
    }

    function deselectAllProducts() {
        document.querySelectorAll('#sync_products_list input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('#sync_products_list input[type="checkbox"]:checked').length;
        document.getElementById('selected_count').textContent = count;
        document.getElementById('sync_submit_btn').disabled = count === 0;
    }

    // Reset sync modal when closed
    function resetSyncModal() {
        const supplierSelect = document.getElementById('sync_supplier_id');
        const brandSelect = document.getElementById('sync_brand_id');
        if (supplierSelect) {
            supplierSelect.value = '';
        }
        if (brandSelect) {
            brandSelect.value = '';
        }
        document.getElementById('sync_brand_container').classList.add('hidden');
        document.getElementById('sync_products_container').classList.add('hidden');
        document.getElementById('sync_loading').classList.add('hidden');
        document.getElementById('sync_no_products').classList.add('hidden');
        document.getElementById('sync_products_list').innerHTML = '';
        document.getElementById('sync_submit_btn').disabled = true;
        document.getElementById('selected_count').textContent = '0';
    }
</script>
@endpush
@endsection