@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h1>
            <div class="flex space-x-2">
                <button onclick="openModal('bulkProductModal')"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 border border-green-600">
                    <i class="fas fa-boxes mr-2"></i>Bulk Add
                </button>
                <button onclick="openModal('productModal')"
                    class="bg-primary-600 text-[#2f85c3] dark:text-white px-4 py-2 rounded-md hover:bg-primary-700 border"
                    style="border-color: #4ea9dd; border-width: 1px;">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search products..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ isset($filters['category_id']) && $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                    {{ $category->cat_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                        <select name="brand_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ isset($filters['brand_id']) && $filters['brand_id'] == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Status</option>
                            <option value="in_stock"
                                {{ isset($filters['status']) && $filters['status'] == 'in_stock' ? 'selected' : '' }}>In
                                Stock</option>
                            <option value="low_stock"
                                {{ isset($filters['status']) && $filters['status'] == 'low_stock' ? 'selected' : '' }}>Low
                                Stock</option>
                            <option value="out_of_stock"
                                {{ isset($filters['status']) && $filters['status'] == 'out_of_stock' ? 'selected' : '' }}>
                                Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="bg-blue-500 text-black py-3 px-4 rounded-md hover:bg-blue-600 font-medium transition-colors"
                        style="border: 1px solid #60a5fa !important;">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                SKU</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Category</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Brand</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-lg object-cover"
                                            src="{{ $product->product_image ?? 'https://via.placeholder.com/40' }}"
                                            alt="">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->description }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->category->cat_name ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->brand->brand_name ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->initial_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button
                                        class="edit-btn text-[#2f85c3] dark:text-white border border-[#2f85c3] dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-[#2f85c3] hover:text-white dark:hover:bg-white dark:hover:text-[#2f85c3] font-semibold"
                                        data-product='@json($product)'>Edit</button>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-white border border-red-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-red-600 hover:text-white dark:hover:bg-white dark:hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600 sm:px-6">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Product Modal (Create) -->
        <div id="productModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
            <div
                class="relative w-full max-w-2xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-box-open mr-2 text-primary-600"></i> Add New Product
                    </h3>
                    <button onclick="closeModal('productModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form class="px-6 py-6 space-y-6" method="POST" action="{{ route('products.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name
                                    <span class="text-red-500">*</span></label>
                                <input name="product_name" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="e.g. Coca Cola 500ml">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                                <input name="sku" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed"
                                    placeholder="Auto-generated" readonly>
                                <p class="text-xs text-gray-500 mt-1">SKU will be auto-generated</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Short product description..."></textarea>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category
                                    <span class="text-red-500">*</span></label>
                                <select name="category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                                <select name="brand_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Unit Configuration Section -->
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Unit Configuration
                                </h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sales
                                            Unit</label>
                                        <select name="base_unit" id="create_base_unit"
                                            onchange="updateConversionFactor('create')"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                            <option value="pcs">Pieces (pcs)</option>
                                            <option value="g">Grams (g)</option>
                                            <option value="kg">Kilograms (kg)</option>
                                            <option value="ml">Milliliters (ml)</option>
                                            <option value="L">Liters (L)</option>
                                            <option value="box">Box</option>
                                            <option value="pack">Pack</option>
                                            <option value="dozen">Dozen</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Purchase
                                            Unit (GRN)</label>
                                        <select name="purchase_unit" id="create_purchase_unit"
                                            onchange="updateConversionFactor('create')"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                            <option value="">Same as Sales Unit</option>
                                            <option value="pcs">Pieces (pcs)</option>
                                            <option value="g">Grams (g)</option>
                                            <option value="kg">Kilograms (kg)</option>
                                            <option value="ml">Milliliters (ml)</option>
                                            <option value="L">Liters (L)</option>
                                            <option value="box">Box</option>
                                            <option value="pack">Pack</option>
                                            <option value="dozen">Dozen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3" id="create_conversion_container" style="display: none;">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Conversion Factor <span id="create_conversion_hint" class="text-gray-400"></span>
                                    </label>
                                    <input name="conversion_factor" id="create_conversion_factor" type="number"
                                        step="0.0001" min="0.0001" value="1"
                                        class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                </div>
                                <div class="mt-3">
                                    <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="allow_decimal_sales" value="1"
                                            class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        Allow decimal quantities at POS (e.g., 0.5 kg)
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="unit" value="pcs">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Initial
                                        Stock</label>
                                    <input name="initial_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min
                                        Stock</label>
                                    <input name="minimum_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max
                                        Stock</label>
                                    <input name="maximum_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Supplier Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Supplier Information
                            (Optional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                                <select id="create_supplier_id" name="supplier_id" onchange="onProductSupplierChange()"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Supplier</option>
                                    @foreach (\App\Models\Supplier::orderBy('company_name')->get() as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="vendor_code_section" class="hidden">
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" id="auto_generate_vendor_code"
                                        name="auto_generate_vendor_code" value="1"
                                        onchange="toggleProductAutoGenerate(this.checked)"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="auto_generate_vendor_code"
                                        class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Auto-generate vendor code
                                    </label>
                                </div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor
                                    Product Code</label>
                                <input id="create_vendor_product_code" name="vendor_product_code" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter vendor's product code">
                                <div id="product_vendor_code_preview"
                                    class="hidden mt-2 text-sm text-green-600 dark:text-green-400">
                                    Preview: <span id="product_preview_code" class="font-mono font-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeModal('productModal')"
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit"
                            class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold flex items-center"><i
                                class="fas fa-save mr-2"></i>Save Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Modal (Edit) -->
        <div id="editProductModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
            <div
                class="relative w-full max-w-2xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-box-open mr-2 text-primary-600"></i> Edit Product
                    </h3>
                    <button onclick="closeModal('editProductModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form id="editProductForm" class="px-6 py-6 space-y-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name
                                    <span class="text-red-500">*</span></label>
                                <input id="edit_product_name" name="product_name" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="e.g. Coca Cola 500ml">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                                <input id="edit_sku" name="sku" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed"
                                    readonly>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea id="edit_description" name="description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Short product description..."></textarea>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category
                                    <span class="text-red-500">*</span></label>
                                <select id="edit_category_id" name="category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                                <select id="edit_brand_id" name="brand_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Unit Configuration Section -->
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Unit Configuration
                                </h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Sales
                                            Unit</label>
                                        <select name="base_unit" id="edit_base_unit"
                                            onchange="updateConversionFactor('edit')"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                            <option value="pcs">Pieces (pcs)</option>
                                            <option value="g">Grams (g)</option>
                                            <option value="kg">Kilograms (kg)</option>
                                            <option value="ml">Milliliters (ml)</option>
                                            <option value="L">Liters (L)</option>
                                            <option value="box">Box</option>
                                            <option value="pack">Pack</option>
                                            <option value="dozen">Dozen</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Purchase
                                            Unit (GRN)</label>
                                        <select name="purchase_unit" id="edit_purchase_unit"
                                            onchange="updateConversionFactor('edit')"
                                            class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                            <option value="">Same as Sales Unit</option>
                                            <option value="pcs">Pieces (pcs)</option>
                                            <option value="g">Grams (g)</option>
                                            <option value="kg">Kilograms (kg)</option>
                                            <option value="ml">Milliliters (ml)</option>
                                            <option value="L">Liters (L)</option>
                                            <option value="box">Box</option>
                                            <option value="pack">Pack</option>
                                            <option value="dozen">Dozen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3" id="edit_conversion_container" style="display: none;">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Conversion Factor <span id="edit_conversion_hint" class="text-gray-400"></span>
                                    </label>
                                    <input name="conversion_factor" id="edit_conversion_factor" type="number"
                                        step="0.0001" min="0.0001" value="1"
                                        class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                </div>
                                <div class="mt-3">
                                    <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="allow_decimal_sales" id="edit_allow_decimal_sales"
                                            value="1"
                                            class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        Allow decimal quantities at POS (e.g., 0.5 kg)
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="unit" id="edit_unit" value="pcs">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Initial
                                        Stock</label>
                                    <input id="edit_initial_stock" name="initial_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min
                                        Stock</label>
                                    <input id="edit_minimum_stock" name="minimum_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max
                                        Stock</label>
                                    <input id="edit_maximum_stock" name="maximum_stock" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                                    Image</label>
                                <label
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input id="edit_product_image" name="product_image" type="file" class="hidden"
                                        accept="image/*">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeModal('editProductModal')"
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit"
                            class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold flex items-center"><i
                                class="fas fa-save mr-2"></i>Update Product</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Bulk Product Modal -->
        <div id="bulkProductModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
            <div
                class="relative w-full max-w-7xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-boxes mr-2 text-green-600"></i> Bulk Add Products
                    </h3>
                    <button onclick="closeModal('bulkProductModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form id="bulkProductForm" class="flex flex-col flex-grow overflow-hidden" method="POST"
                    action="{{ route('products.bulk_store') }}">
                    @csrf
                    <!-- Common Fields -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 border-b border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category
                                    <span class="text-red-500">*</span></label>
                                <select name="common_category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                                <select name="common_brand_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2 flex items-end">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-1"></i> These settings apply to all products below.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Table Area -->
                    <div class="flex-grow overflow-auto p-4">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">
                                        #</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Product Name <span class="text-red-500">*</span></th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">
                                        Unit</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                                        Stock</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                                        Min</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">
                                        Max</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Description</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">
                                        <button type="button" onclick="handleAddBulkRow()"
                                            class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-plus-circle text-lg"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="bulk-products-body"
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                <!-- Rows will be added here via JS -->
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 flex justify-between items-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Total Items: <span id="bulk-row-count" class="font-bold">0</span>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeModal('bulkProductModal')"
                                class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                            <button type="submit"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold flex items-center">
                                <i class="fas fa-save mr-2"></i>Save All Products
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
        <div
            class="relative w-full max-w-lg mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-file-csv mr-2 text-green-600"></i> Import Products
                </h3>
                <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            {{-- <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Upload CSV File
                        </label>
                        <input type="file" name="file" accept=".csv" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 mt-2">
                            Please upload a CSV file with the correct headers.
                            <a href="{{ route('products.sample-csv') }}"
                                class="text-primary-600 hover:underline">Download Sample CSV</a>
                        </p>
                    </div>
                </div>
                <div
                    class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                    <button type="button" onclick="closeModal('importModal')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Import</button>
                </div>
            </form> --}}
        </div>
    </div>
    </div>

    @push('styles')
        <style>
            #editProductModal:not(.hidden) {
                display: flex !important;
                opacity: 1 !important;
                z-index: 50 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        try {
                            const productData = btn.getAttribute('data-product');
                            const product = JSON.parse(productData);
                            openEditProductModal(product);
                        } catch (e) {
                            console.error('Error parsing product data:', e);
                            alert('Error opening edit modal. Please refresh the page and try again.');
                        }
                    });
                });

                // Toastr notifications
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                @if (session('error'))
                    toastr.error("{{ session('error') }}");
                @endif
                @if ($errors->any())
                    toastr.error(`{!! implode('<br>', $errors->all()) !!}`);
                @endif
            });

            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            function openEditProductModal(product) {
                try {
                    const form = document.getElementById('editProductForm');
                    if (!form) {
                        throw new Error('Edit product form not found');
                    }
                    document.getElementById('edit_product_name').value = product.product_name || '';
                    document.getElementById('edit_sku').value = product.sku || '';
                    document.getElementById('edit_description').value = product.description || '';
                    document.getElementById('edit_category_id').value = product.category_id || '';
                    document.getElementById('edit_brand_id').value = product.brand_id || '';
                    document.getElementById('edit_unit').value = product.unit || 'pcs';
                    document.getElementById('edit_initial_stock').value = product.initial_stock || '';
                    document.getElementById('edit_minimum_stock').value = product.minimum_stock || '';
                    document.getElementById('edit_maximum_stock').value = product.maximum_stock || '';

                    // Unit configuration fields
                    document.getElementById('edit_base_unit').value = product.base_unit || 'pcs';
                    document.getElementById('edit_purchase_unit').value = product.purchase_unit || '';
                    document.getElementById('edit_conversion_factor').value = product.conversion_factor || 1;
                    document.getElementById('edit_allow_decimal_sales').checked = product.allow_decimal_sales || false;

                    // Update conversion factor visibility
                    updateConversionFactor('edit');

                    form.action = '{{ route('products.update', ':id') }}'.replace(':id', product.id);
                    openModal('editProductModal');
                } catch (error) {
                    // Silent error handling
                }
            }

            // Unit conversion factor mapping
            const conversionFactors = {
                'g_kg': 1000, // 1 kg = 1000 g
                'kg_g': 0.001, // 1 g = 0.001 kg
                'ml_L': 1000, // 1 L = 1000 ml
                'L_ml': 0.001, // 1 ml = 0.001 L
                'pcs_dozen': 12, // 1 dozen = 12 pcs
                'dozen_pcs': 0.0833, // 1 pc = 0.0833 dozen
            };

            function updateConversionFactor(prefix) {
                const baseUnit = document.getElementById(prefix + '_base_unit').value;
                const purchaseUnit = document.getElementById(prefix + '_purchase_unit').value;
                const conversionContainer = document.getElementById(prefix + '_conversion_container');
                const conversionInput = document.getElementById(prefix + '_conversion_factor');
                const conversionHint = document.getElementById(prefix + '_conversion_hint');

                if (!purchaseUnit || purchaseUnit === baseUnit) {
                    conversionContainer.style.display = 'none';
                    conversionInput.value = 1;
                    return;
                }

                conversionContainer.style.display = 'block';

                // Check for known conversion
                const key = baseUnit + '_' + purchaseUnit;
                if (conversionFactors[key]) {
                    conversionInput.value = conversionFactors[key];
                    conversionHint.textContent = `(1 ${purchaseUnit} = ${conversionFactors[key]} ${baseUnit})`;
                } else {
                    conversionHint.textContent = `(1 ${purchaseUnit} = ? ${baseUnit})`;
                }
            }

            // Show/hide vendor code section when supplier is selected
            function onProductSupplierChange() {
                const supplierSelect = document.getElementById('create_supplier_id');
                const vendorCodeSection = document.getElementById('vendor_code_section');

                if (supplierSelect.value) {
                    vendorCodeSection.classList.remove('hidden');
                    // Update preview if auto-generate is checked
                    if (document.getElementById('auto_generate_vendor_code').checked) {
                        updateProductVendorCodePreview();
                    }
                } else {
                    vendorCodeSection.classList.add('hidden');
                    // Reset auto-generate state
                    document.getElementById('auto_generate_vendor_code').checked = false;
                    toggleProductAutoGenerate(false);
                }
            }

            // Toggle auto-generate vendor code for product creation
            function toggleProductAutoGenerate(isChecked) {
                const vendorCodeInput = document.getElementById('create_vendor_product_code');
                const previewDiv = document.getElementById('product_vendor_code_preview');

                if (isChecked) {
                    vendorCodeInput.disabled = true;
                    vendorCodeInput.value = '';
                    vendorCodeInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
                    previewDiv.classList.remove('hidden');
                    updateProductVendorCodePreview();
                } else {
                    vendorCodeInput.disabled = false;
                    vendorCodeInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                    previewDiv.classList.add('hidden');
                }
            }

            // Update vendor code preview for product creation
            function updateProductVendorCodePreview() {
                const supplierSelect = document.getElementById('create_supplier_id');
                const skuInput = document.querySelector('#productModal input[name="sku"]');
                const previewCode = document.getElementById('product_preview_code');

                if (!supplierSelect || !previewCode) return;

                const supplierId = supplierSelect.value;
                // SKU is auto-generated, so we'll show a placeholder
                const sku = skuInput ? skuInput.value : '';

                if (supplierId) {
                    const supplierOption = supplierSelect.options[supplierSelect.selectedIndex];
                    const supplierName = supplierOption.textContent.trim();

                    // Generate preview: first 3 letters of supplier (letters only) + placeholder for SKU
                    const prefix = supplierName.replace(/[^A-Za-z]/g, '').substring(0, 3).toUpperCase();

                    if (sku) {
                        const numericMatch = sku.match(/(\d+)$/);
                        const numeric = numericMatch ? numericMatch[1] : '000000';
                        previewCode.textContent = prefix + '-' + numeric;
                    } else {
                        previewCode.textContent = prefix + '-XXXXXX (SKU will be auto-generated)';
                    }
                } else {
                    previewCode.textContent = 'Select supplier first';
                }
            }
        </script>
        <script>
            // Bulk Product Functions
            let bulkRowCount = 0;

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize with 5 empty rows
                for (let i = 0; i < 5; i++) {
                    handleAddBulkRow();
                }
            });

            function handleAddBulkRow() {
                bulkRowCount++;
                const tbody = document.getElementById('bulk-products-body');
                const tr = document.createElement('tr');
                tr.id = `bulk-row-${bulkRowCount}`;

                tr.innerHTML = `
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                        ${bulkRowCount}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="text" name="products[${bulkRowCount}][name]" required
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm"
                            placeholder="Product Name">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <select name="products[${bulkRowCount}][unit]"
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="pcs">pcs</option>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="L">L</option>
                            <option value="ml">ml</option>
                            <option value="box">Box</option>
                            <option value="pack">Pack</option>
                        </select>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="products[${bulkRowCount}][initial_stock]" value="0" min="0"
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="products[${bulkRowCount}][min_stock]" value="0" min="0"
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="products[${bulkRowCount}][max_stock]" value="0" min="0"
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="text" name="products[${bulkRowCount}][description]"
                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm"
                            placeholder="Description">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center">
                        <button type="button" onclick="removeBulkRow(${bulkRowCount})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
                updateBulkRowCount();
            }

            function removeBulkRow(id) {
                const row = document.getElementById(`bulk-row-${id}`);
                if (row) {
                    row.remove();
                    // Don't decrement counter to avoid ID conflicts, just update display
                    updateBulkRowCount();
                }
            }

            function updateBulkRowCount() {
                const count = document.getElementById('bulk-products-body').children.length;
                document.getElementById('bulk-row-count').textContent = count;
            }
        </script>
    @endpush
@endsection
