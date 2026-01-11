@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Stock Management</h1>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Stock Items</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalStocks }}</p>
                    </div>
                    <i class="fas fa-boxes text-blue-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Value</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">LKR
                            {{ number_format($totalValue, 2) }}</p>
                    </div>
                    <i class="fas fa-dollar-sign text-green-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $outOfStock }}</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Low Stock</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $lowStock }}</p>
                    </div>
                    <i class="fas fa-exclamation-circle text-yellow-500 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Category Statistics Card -->
        @if ($categoryStats && $selectedCategory)
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            @if ($selectedCategory->icon)
                                <i class="{{ $selectedCategory->icon }} text-4xl mr-4"></i>
                            @else
                                <i class="fas fa-folder text-blue-500 text-4xl mr-4"></i>
                            @endif
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $selectedCategory->cat_name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Category Statistics</p>
                            </div>
                        </div>
                        <a href="{{ route('stocks.index') }}"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-times mr-2"></i> Clear Filter
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <!-- Total Products -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Products</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $categoryStats['total_products'] }}</p>
                        </div>

                        <!-- Stock Items -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Stock Items</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $categoryStats['total_stocks'] }}
                            </p>
                        </div>

                        <!-- Total Value -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Value</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">LKR
                                {{ number_format($categoryStats['total_value'], 2) }}</p>
                        </div>

                        <!-- In Stock -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">In Stock</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ $categoryStats['in_stock'] }}</p>
                        </div>

                        <!-- Out of Stock -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Out of Stock</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                                {{ $categoryStats['out_of_stock'] }}</p>
                        </div>

                        <!-- Low Stock -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Low Stock</p>
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $categoryStats['low_stock'] }}</p>
                        </div>
                    </div>

                    <!-- Brands Section -->
                    @if (isset($categoryStats['brands']) && $categoryStats['brands']->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                <i class="fas fa-tags mr-2 text-blue-500"></i>Brands in {{ $selectedCategory->cat_name }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($categoryStats['brands'] as $brand)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col">
                                        <div class="flex justify-between items-start mb-2">
                                            <h5 class="font-bold text-gray-900 dark:text-white truncate"
                                                title="{{ $brand->brand_name }}">
                                                {{ $brand->brand_name }}
                                            </h5>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $brand->total_products }} Items
                                            </span>
                                        </div>

                                        <div class="mt-auto space-y-2">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-green-600 dark:text-green-400 font-medium">In
                                                    Stock:</span>
                                                <span
                                                    class="font-bold text-gray-700 dark:text-gray-300">{{ $brand->in_stock }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        @endif


        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('stocks.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-filter mr-1"></i> Category
                    </label>
                    <select name="category_id" id="category_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @if (isset($categories))
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->cat_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Product Filter -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Product
                    </label>
                    <select name="product_id" id="product_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="status" id="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock
                        </option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of
                            Stock</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Product name, SKU, batch..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('stocks.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Stock Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                SKU</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Batch</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Barcode</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Cost Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Selling Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Discount</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Quantity</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Available</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($stocks as $stock)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $stock->product->product_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stock->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $stock->product->sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $stock->batch->batch_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $stock->batch->barcode ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if ($stock->cost_price > 0)
                                        LKR {{ number_format($stock->cost_price, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if ($stock->selling_price > 0)
                                        LKR {{ number_format($stock->selling_price, 2) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if ($stock->discount_price > 0)
                                        <span class="text-red-500">- LKR {{ number_format($stock->discount_price, 2) }}</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">0.00</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ number_format($stock->total_quantity, 0) }}
                                        </div>
                                        @if ($stock->foc_quantity > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <span class="text-green-600 dark:text-green-400">FOC:
                                                    {{ number_format($stock->foc_quantity, 0) }}</span>
                                                @if ($stock->paid_quantity > 0)
                                                    + Paid: {{ number_format($stock->paid_quantity, 0) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold">
                                        <div
                                            class="{{ $stock->total_available_quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($stock->total_available_quantity, 0) }}
                                        </div>
                                        @if ($stock->foc_available_quantity > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <span class="text-green-600 dark:text-green-400">FOC:
                                                    {{ number_format($stock->foc_available_quantity, 0) }}</span>
                                                @if ($stock->paid_available_quantity > 0)
                                                    + Paid: {{ number_format($stock->paid_available_quantity, 0) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($stock->total_available_quantity == 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                            Out of Stock
                                        </span>
                                    @elseif($stock->total_available_quantity <= $stock->total_quantity / 2)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                            Low Stock
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                            In Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                    <button
                                        onclick="openEditModal({{ $stock->id }}, '{{ $stock->cost_price }}', '{{ $stock->selling_price }}', '{{ $stock->discount_price }}', '{{ $stock->batch->barcode }}', '{{ addslashes($stock->product->product_name) }}')"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="{{ route('stocks.show', ['stock' => $stock->id, 'page' => $stocks->currentPage()]) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No stock items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $stocks->links() }}
            </div>
        </div>

        <!-- Edit Stock Modal -->
        <div id="editStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="relative w-full max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-edit mr-2 text-blue-500"></i>Edit Stock
                        </h3>
                        <button type="button" onclick="closeEditModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="editStockForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="current_page" id="current_page"
                            value="{{ $stocks->currentPage() }}">

                        <div class="p-6 space-y-4">
                            <!-- Product Name -->
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md p-3">
                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                    <i class="fas fa-box mr-2"></i>Product: <span id="modalProductName"
                                        class="font-semibold"></span>
                                </p>
                            </div>

                            <!-- Price Fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="cost_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cost Price (LKR) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="cost_price" name="cost_price" required min="0"
                                        step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label for="selling_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Selling Price (LKR) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="selling_price" name="selling_price" required
                                        min="0" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        oninput="calculateFinalPrice()">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="discount_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Discount (LKR)
                                    </label>
                                    <input type="number" id="discount_price" name="discount_price" min="0"
                                        step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        oninput="calculateFinalPrice()">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Final Selling Price
                                    </label>
                                    <div class="px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-bold" id="final_price_display">
                                        LKR 0.00
                                    </div>
                                </div>
                            </div>

                            <!-- Barcode Field -->
                            <div>
                                <label for="barcode"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Batch Barcode
                                </label>
                                <input type="text" id="barcode" name="barcode" maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Leave empty to remove
                                    barcode</p>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fas fa-adjust mr-2"></i>Quantity Adjustment (Optional)
                                </h4>

                                <!-- Adjustment Fields -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="quantity_adjustment"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Adjustment Quantity
                                        </label>
                                        <input type="number" id="quantity_adjustment" name="quantity_adjustment"
                                            step="0.0001" placeholder="Enter positive or negative value"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Positive to increase, negative to decrease
                                        </p>
                                    </div>

                                    <div>
                                        <label for="adjustment_reason"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Reason <span class="text-red-500" id="reason_required"
                                                style="display: none;">*</span>
                                        </label>
                                        <select id="adjustment_reason" name="adjustment_reason"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Select reason...</option>
                                            <option value="Damage">Damage</option>
                                            <option value="Theft/Loss">Theft/Loss</option>
                                            <option value="Recount">Physical Recount</option>
                                            <option value="Return to Supplier">Return to Supplier</option>
                                            <option value="Found Item">Found Item</option>
                                            <option value="Expired">Expired</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Notes Field -->
                                <div class="mt-4">
                                    <label for="adjustment_notes"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Notes (Optional)
                                    </label>
                                    <textarea id="adjustment_notes" name="adjustment_notes" rows="2" maxlength="1000"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="Additional notes about this adjustment..."></textarea>
                                </div>
                            </div>

                            <!-- Info Message -->
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md p-3">
                                <p class="text-xs text-yellow-800 dark:text-yellow-300">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Price/barcode changes will be logged in GRN records. Quantity adjustments will be
                                    tracked separately in the Stock Adjustments panel.
                                </p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="closeEditModal()"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(stockId, costPrice, sellingPrice, discountPrice, barcode, productName) {
            // Check if this is a FOC stock
            if (parseFloat(costPrice) === 0) {
                alert('FOC (Free of Charge) stocks cannot be edited. Only paid stocks can be modified.');
                return;
            }

            // Set form action URL
            const form = document.getElementById('editStockForm');
            form.action = "{{ url('stocks') }}/" + stockId;

            // Populate form fields
            document.getElementById('cost_price').value = costPrice;
            
            // The stored selling_price is the FINAL price (Base - Discount).
            // To show the Base Price in the modal for editing, we add the discount back.
            const storedSellingPrice = parseFloat(sellingPrice) || 0;
            const storedDiscount = parseFloat(discountPrice) || 0;
            const basePrice = (storedSellingPrice + storedDiscount).toFixed(2);

            document.getElementById('selling_price').value = basePrice;
            document.getElementById('discount_price').value = storedDiscount;
            document.getElementById('barcode').value = barcode || '';
            document.getElementById('modalProductName').textContent = productName;

            // Set current page value
            document.getElementById('current_page').value = "{{ $stocks->currentPage() }}";

            // Update final price display
            calculateFinalPrice();

            // Show modal
            document.getElementById('editStockModal').classList.remove('hidden');
        }

        function calculateFinalPrice() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const discount = parseFloat(document.getElementById('discount_price').value) || 0;
            const finalPrice = Math.max(0, sellingPrice - discount);
            document.getElementById('final_price_display').textContent = 'LKR ' + finalPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function closeEditModal() {
            document.getElementById('editStockModal').classList.add('hidden');
            document.getElementById('editStockForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('editStockModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Validate quantity adjustment requires reason
        document.getElementById('editStockForm').addEventListener('submit', function(e) {
            const adjustment = document.getElementById('quantity_adjustment').value;
            const reason = document.getElementById('adjustment_reason').value;

            if (adjustment && adjustment != 0 && !reason) {
                e.preventDefault();
                alert('Please select a reason for the quantity adjustment');
                document.getElementById('adjustment_reason').focus();
                return false;
            }
        });

        // Show/hide required indicator on reason field
        document.getElementById('quantity_adjustment').addEventListener('input', function(e) {
            const reasonRequired = document.getElementById('reason_required');
            if (e.target.value && e.target.value != 0) {
                reasonRequired.style.display = 'inline';
            } else {
                reasonRequired.style.display = 'none';
            }
        });
    </script>
@endsection
