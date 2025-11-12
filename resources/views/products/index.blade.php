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
            <button onclick="openModal('productModal')"
               class="bg-primary-600 text-[#2f85c3] dark:text-white px-4 py-2 rounded-md hover:bg-primary-700 border"
                style="border-color: #4ea9dd; border-width: 1px;">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
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
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (isset($filters['category_id']) && $filters['category_id'] == $category->id) ? 'selected' : '' }}>{{ $category->cat_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                        <select name="brand_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ (isset($filters['brand_id']) && $filters['brand_id'] == $brand->id) ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Status</option>
                            <option value="in_stock" {{ (isset($filters['status']) && $filters['status'] == 'in_stock') ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ (isset($filters['status']) && $filters['status'] == 'low_stock') ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ (isset($filters['status']) && $filters['status'] == 'out_of_stock') ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border"
                        style="border-color: #4ea9dd; border-width: 1px;">Filter</button>
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
                                Item Code</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Category</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Brand</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                            @if($product->product_image && file_exists(public_path('storage/' . $product->product_image)))
                                                <img class="h-10 w-10 rounded-lg object-cover"
                                                    src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}">
                                            @elseif($product->category && $product->category->icon && file_exists(public_path('images/category-icons/' . $product->category->icon)))
                                                <img class="h-8 w-8 object-contain"
                                                    src="{{ asset('images/category-icons/' . $product->category->icon) }}" alt="{{ $product->category->cat_name }}">
                                            @else
                                                <i class="fas fa-box text-gray-400 dark:text-gray-500 text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->product_name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->description }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->item_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->category->cat_name ?? '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->brand->brand_name ?? '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @php
                                        $latestStock = $product->availableStocks()->latest()->first();
                                        $price = $latestStock ? $latestStock->selling_price : null;
                                    @endphp
                                    @if($price)
                                        ${{ number_format($price, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->initial_stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="edit-btn text-[#2f85c3] dark:text-white border border-[#2f85c3] dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-[#2f85c3] hover:text-white dark:hover:bg-white dark:hover:text-[#2f85c3] font-semibold"
                                        data-product='@json($product)'>Edit</button>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-white border border-red-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-red-600 hover:text-white dark:hover:bg-white dark:hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                        <button
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Showing <span class="font-medium">1</span>
                                to <span class="font-medium">10</span> of <span class="font-medium">97</span> results</p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <button
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Previous</button>
                                <button
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</button>
                                <button
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">2</button>
                                <button
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">3</button>
                                <button
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Next</button>
                            </nav>
                        </div>
                    </div>
                </div>
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU <span
                                        class="text-red-500">*</span></label>
                                <input name="sku" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="e.g. CC500">
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
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                                <select name="brand_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit</label>
                                <select name="unit"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option>Piece</option>
                                    <option>Kg</option>
                                    <option>Liter</option>
                                    <option>Box</option>
                                </select>
                            </div>
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                                    Image</label>
                                <label for="create_product_image"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100">
                                    <div id="create_image_preview_container" class="hidden flex-col items-center justify-center pt-2 pb-2">
                                        <img id="create_image_preview" src="" alt="Preview" class="max-h-28 object-contain rounded-lg">
                                    </div>
                                    <div id="create_upload_placeholder" class="flex flex-col items-center justify-center pt-2 pb-2">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input id="create_product_image" name="product_image" type="file" class="hidden"
                                        accept="image/*" onchange="previewImage('create')">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU <span
                                        class="text-red-500">*</span></label>
                                <input id="edit_sku" name="sku" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="e.g. CC500">
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
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                                <select id="edit_brand_id" name="brand_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit</label>
                                <select id="edit_unit" name="unit"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option>Piece</option>
                                    <option>Kg</option>
                                    <option>Liter</option>
                                    <option>Box</option>
                                </select>
                            </div>
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
                                <div id="edit_current_image_container" class="mb-2 hidden">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Current Image:</p>
                                    <div class="flex items-center justify-center w-full h-32 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                        <img id="edit_current_image" src="" alt="Current Product Image" class="max-h-28 object-contain rounded-lg">
                                    </div>
                                </div>
                                <label for="edit_product_image"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100">
                                    <div id="edit_image_preview_container" class="hidden flex-col items-center justify-center pt-2 pb-2">
                                        <img id="edit_image_preview" src="" alt="New Preview" class="max-h-28 object-contain rounded-lg">
                                    </div>
                                    <div id="edit_upload_placeholder" class="flex flex-col items-center justify-center pt-2 pb-2">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload new image</span> or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input id="edit_product_image" name="product_image" type="file" class="hidden"
                                        accept="image/*" onchange="previewImage('edit')">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeModal('editProductModal')"
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit"
                            class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold flex items-center"><i
                                class="fas fa-save mr-2"></i>Update Product</button>
                    </div>
                </form>
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
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        try {
                            const product = JSON.parse(btn.getAttribute('data-product'));
                            openEditProductModal(product);
                        } catch (e) {
                            console.error('Failed to parse product data:', e);
                            console.error('Data attribute:', btn.getAttribute('data-product'));
                        }
                    });
                });

                // Toastr notifications
                @if(session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                @if(session('error'))
                    toastr.error("{{ session('error') }}");
                @endif
                @if($errors->any())
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

            function previewImage(type) {
                const input = document.getElementById(type + '_product_image');
                const preview = document.getElementById(type + '_image_preview');
                const previewContainer = document.getElementById(type + '_image_preview_container');
                const placeholder = document.getElementById(type + '_upload_placeholder');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        previewContainer.classList.add('flex');
                        placeholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
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
                    document.getElementById('edit_unit').value = product.unit || '';
                    document.getElementById('edit_initial_stock').value = product.initial_stock || '';
                    document.getElementById('edit_minimum_stock').value = product.minimum_stock || '';
                    document.getElementById('edit_maximum_stock').value = product.maximum_stock || '';

                    // Show current product image if exists
                    const currentImageContainer = document.getElementById('edit_current_image_container');
                    const currentImage = document.getElementById('edit_current_image');
                    if (product.product_image) {
                        currentImage.src = '/storage/' + product.product_image;
                        currentImageContainer.classList.remove('hidden');
                    } else {
                        currentImageContainer.classList.add('hidden');
                    }

                    // Reset new image preview
                    document.getElementById('edit_image_preview_container').classList.add('hidden');
                    document.getElementById('edit_upload_placeholder').classList.remove('hidden');
                    document.getElementById('edit_product_image').value = '';

                    form.action = '{{ route("products.update", ":id") }}'.replace(':id', product.id);
                    openModal('editProductModal');
                } catch (error) {
                    // Silent error handling
                }
            }
        </script>
    @endpush
@endsection
