@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
    <div x-data="cashierPos()" x-init="init()" @keydown.window.ctrl.m.prevent="toggleManualModeShortcut()"
        class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-6rem)]">
        <!-- Left Main Area: Product Search & Cart -->
        <div class="flex-1 flex flex-col gap-2 overflow-hidden">
            <!-- Manual Entry Mode Toggle -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-3 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="isManualMode" @change="toggleManualMode" :disabled="cart.length > 0"
                            class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-yellow-500">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            <i class="fas fa-edit mr-1"></i> Manual Entry Mode
                        </span>
                    </label>
                    <span x-show="cart.length > 0" class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i> Clear cart to switch modes
                    </span>
                </div>
            </div>

            <!-- Product Search -->
            <div x-show="!isManualMode" x-transition class="bg-white dark:bg-gray-800 shadow rounded-lg p-0 flex-shrink-0">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts"
                        @keydown.enter.prevent="selectFirstProduct"
                        placeholder="Search products by name, barcode, or SKU..."
                        class="w-full pl-10 pr-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-lg shadow-sm">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400 text-lg"></i>

                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0 && searchQuery.length > 0" x-transition
                        class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-xl max-h-96 overflow-y-auto">
                        <template x-for="product in searchResults" :key="product.id">
                            <button @click="addToCart(product)" type="button"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-colors">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white text-lg"
                                            x-text="product.product_name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <span class="bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-xs mr-2"
                                                x-text="product.sku"></span>
                                            <span x-text="product.category"></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-primary-600 dark:text-primary-400">
                                            <span x-text="'LKR ' + parseFloat(product.selling_price).toFixed(2)"></span>
                                            <span class="text-xs font-normal text-gray-500"
                                                x-text="'/' + (product.is_weighted ? product.unit : (product.base_unit || product.unit || 'pcs'))"></span>
                                        </div>
                                        <div x-show="product.discount_amount > 0" class="text-xs text-red-500 font-medium">
                                            - LKR <span x-text="parseFloat(product.discount_amount).toFixed(2)"></span>
                                            Discount
                                        </div>
                                        <div class="text-xs"
                                            :class="product.available_quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                            <span
                                                x-text="product.is_weighted ? parseFloat(product.available_quantity / 1000).toFixed(2) : parseFloat(product.available_quantity).toFixed(product.allow_decimal_sales ? 2 : 0)"></span>
                                            <span
                                                x-text="product.is_weighted ? product.unit : (product.base_unit || product.unit || 'pcs')"></span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div x-show="isSearching" class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
                </div>
            </div>

            <!-- Manual Entry Form (Manual Mode) -->
            <div x-show="isManualMode" x-transition
                class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-400 dark:border-yellow-600 shadow rounded-lg p-4 flex-shrink-0">
                <div class="flex items-center mb-3">
                    <i class="fas fa-edit text-yellow-600 dark:text-yellow-400 text-xl mr-2"></i>
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">Manual Product Entry</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="manualEntry.product_name"
                            @keydown.enter.prevent="$refs.priceInput.focus()" placeholder="Enter product name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Price (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="manualEntry.price" step="0.01" min="0" x-ref="priceInput"
                            @keydown.enter.prevent="$refs.quantityInput.focus()" placeholder="0.00"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="manualEntry.quantity" step="0.01" min="0.01"
                            x-ref="quantityInput" @keydown.enter.prevent="$refs.barcodeInput.focus()" placeholder="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Barcode (Optional)
                        </label>
                        <input type="text" x-model="manualEntry.entered_barcode" x-ref="barcodeInput"
                            @keydown.enter.prevent="handleManualBarcode(false)"
                            @input.debounce.500ms="handleManualBarcode(true)" placeholder="Scan or enter barcode"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                <button @click="addManualItemToCart()" type="button"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md transition-colors">
                    <i class="fas fa-plus-circle mr-2"></i> Add to Cart
                </button>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Manual entries will be reconciled later with actual products
                </p>
            </div>

            <!-- Shopping Cart Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-2 flex-grow flex flex-col overflow-hidden">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Shopping Cart</h2>
                    <div class="flex gap-2">
                        <button @click="showSavedCartsModal = true; loadSavedCarts()" type="button"
                            class="text-sm bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1.5 rounded-md transition-colors">
                            <i class="fas fa-folder-open mr-1"></i> Saved Carts <span x-show="savedCarts.length > 0"
                                class="ml-1 bg-green-600 text-white px-1.5 rounded-full text-xs"
                                x-text="savedCarts.length"></span>
                        </button>
                        <button x-show="cart.length > 0" @click="showSaveCartModal = true" type="button"
                            class="text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded-md transition-colors">
                            <i class="fas fa-save mr-1"></i> Save
                        </button>
                        <button x-show="cart.length > 0" @click="clearCart" type="button"
                            class="text-sm bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-md transition-colors">
                            <i class="fas fa-trash mr-1"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="flex-grow overflow-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                            <tr class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                <th class="px-4 py-3 border-b dark:border-gray-600">Code</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600">Product</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-right">Price</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-center">Qty</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-center">Discount</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-right">Total</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                        <span x-text="item.sku || '-'"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 dark:text-white" x-text="item.product_name">
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <span
                                                x-text="item.is_weighted ? parseFloat(item.available_quantity / 1000).toFixed(2) : parseFloat(item.available_quantity).toFixed(item.allow_decimal_sales ? 2 : 0)"></span>
                                            <span
                                                x-text="item.is_weighted ? item.unit : (item.base_unit || item.unit || 'pcs')"></span>
                                            available
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-medium">
                                        <span x-text="parseFloat(item.selling_price).toFixed(2)"></span>
                                        <span class="text-xs text-gray-500"
                                            x-text="'/' + (item.is_weighted ? item.unit : (item.base_unit || item.unit || 'pcs'))"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <!-- Weighted products: show weight, no editing -->
                                        <div x-show="item.is_weighted" class="text-center">
                                            <div class="font-medium text-sm"
                                                x-text="(item.quantity / 1000).toFixed(3) + ' kg'"></div>
                                            <div class="text-xs text-gray-500">Fixed weight</div>
                                        </div>
                                        <!-- Regular products: quantity controls -->
                                        <div x-show="!item.is_weighted" class="flex items-center justify-center gap-1">
                                            <button @click="decrementQuantity(index)"
                                                class="w-7 h-7 rounded bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 flex items-center justify-center transition-colors">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" x-model.number="item.quantity"
                                                @input="validateQuantity(index)"
                                                :step="item.allow_decimal_sales ? '0.01' : '1'"
                                                :min="item.allow_decimal_sales ? '0.01' : '1'"
                                                class="w-16 text-center border border-gray-300 dark:border-gray-600 rounded py-1 text-sm dark:bg-gray-700 dark:text-white focus:ring-1 focus:ring-primary-500">
                                            <button @click="incrementQuantity(index)"
                                                :disabled="item.quantity >= item.available_quantity"
                                                class="w-7 h-7 rounded bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 flex items-center justify-center transition-colors disabled:opacity-50">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                        <div x-show="!item.is_weighted" class="text-xs text-center text-gray-500 mt-1"
                                            x-text="item.base_unit || item.unit || 'pcs'"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <select x-model="item.discountType" @change="applyDiscount(index)"
                                                class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs dark:bg-gray-700 dark:text-white">
                                                <option value="none">None</option>
                                                <option value="percentage">%</option>
                                                <option value="fixed_amount">LKR</option>
                                            </select>
                                            <input type="number" x-model.number="item.discountValue"
                                                @input="applyDiscount(index)" :disabled="item.discountType === 'none'"
                                                min="0" step="0.01"
                                                class="w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs dark:bg-gray-700 dark:text-white"
                                                placeholder="0">
                                        </div>
                                        <div x-show="item.discountAmount > 0"
                                            class="text-xs text-green-600 dark:text-green-400 mt-1 text-center">
                                            LKR <span x-text="item.discountAmount.toFixed(2)"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                                        <span x-text="calculateItemTotal(item).toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="removeFromCart(index)"
                                            class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="cart.length === 0">
                                <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i
                                        class="fas fa-shopping-cart text-5xl mb-4 text-gray-300 dark:text-gray-600 block"></i>
                                    <p class="text-lg">Cart is empty</p>
                                    <p class="text-sm">Search and add products to get started</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Shift Status & Payment Section -->
        <div class="w-full lg:w-1/3 xl:w-1/4 flex flex-col gap-6 overflow-y-auto pr-2">
            <!-- Shift Status Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex-shrink-0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Shift Status</h3>
                    <div class="flex gap-2">
                        @can('manage own shifts')
                            <template x-if="!activeShift">
                                <button @click="showClockInModal = true"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold"
                                    title="Clock In">
                                    <i class="fas fa-sign-in-alt"></i>
                                </button>
                            </template>
                            <template x-if="activeShift">
                                <button @click="showClockOutModal = true"
                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold"
                                    title="Clock Out">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </template>
                            <a href="{{ route('shifts.my-shifts') }}"
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold"
                                title="My Shifts">
                                <i class="fas fa-history"></i>
                            </a>
                        @endcan
                    </div>
                </div>

                <div>
                    <template x-if="activeShift">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="px-2 py-0.5 bg-green-500 text-white rounded-full text-xs font-semibold">
                                    Active
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Started:</span>
                                <span class="text-gray-900 dark:text-white font-medium"
                                    x-text="activeShift ? new Date(activeShift.clock_in_at).toLocaleTimeString() : ''"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                                <span class="text-gray-900 dark:text-white font-medium" x-text="shiftDuration"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Sales:</span>
                                <span class="text-gray-900 dark:text-white font-medium"
                                    x-text="shiftStats ? shiftStats.total_sales_count : 0"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="!activeShift">
                        <div class="text-center py-2 text-gray-500 dark:text-gray-400 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i> Please clock in to start
                        </div>
                    </template>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex-grow flex flex-col gap-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Payment Details</h2>

                <!-- Order Summary -->
                <div class="space-y-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-medium dark:text-white" x-text="'LKR ' + totals.subtotal.toFixed(2)"></span>
                    </div>
                    <div x-show="totals.discount > 0"
                        class="flex justify-between text-sm text-green-600 dark:text-green-400">
                        <span>Discount:</span>
                        <span class="font-medium">LKR <span x-text="totals.discount.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                        <span class="font-medium dark:text-white" x-text="'LKR ' + totals.tax.toFixed(2)"></span>
                    </div>
                    <hr class="border-gray-200 dark:border-gray-600 my-1">
                    <div class="flex justify-between text-lg font-bold">
                        <span class="dark:text-white">Total:</span>
                        <span class="text-green-600 dark:text-green-400" x-text="'LKR ' + totals.total.toFixed(2)"></span>
                    </div>
                    <div x-show="totals.discount > 0" class="text-xs text-center text-green-600 dark:text-green-400 pt-1">
                        You saved LKR <span x-text="totals.discount.toFixed(2)"></span>!
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Customer (Optional)</h3>
                    <input type="text" x-model="customerName" placeholder="Name"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <input type="text" x-model="customerPhone" placeholder="Phone"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Payment Methods -->
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="paymentMethod = 'cash'" type="button"
                            class="p-2 border rounded text-center transition-colors text-sm"
                            :class="paymentMethod === 'cash' ?
                                'border-primary-500 bg-primary-50 dark:bg-primary-900 text-primary-700 dark:text-primary-300' :
                                'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            <i class="fas fa-money-bill-wave block mb-1"></i> Cash
                        </button>
                        <button @click="paymentMethod = 'card'" type="button"
                            class="p-2 border rounded text-center transition-colors text-sm"
                            :class="paymentMethod === 'card' ?
                                'border-primary-500 bg-primary-50 dark:bg-primary-900 text-primary-700 dark:text-primary-300' :
                                'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            <i class="fas fa-credit-card block mb-1"></i> Card
                        </button>
                        <button @click="paymentMethod = 'credit'" type="button"
                            class="p-2 border rounded text-center transition-colors text-sm"
                            :class="paymentMethod === 'credit' ?
                                'border-primary-500 bg-primary-50 dark:bg-primary-900 text-primary-700 dark:text-primary-300' :
                                'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            <i class="fas fa-handshake block mb-1"></i> Credit
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Input -->
                <div x-show="paymentMethod === 'cash'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Received</label>
                    <input type="text" x-model="amountReceived" @input="calculateChange" step="0.01"
                        placeholder="0.00" name="amountReceived"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">

                    {{-- Hidden input to capture the calculated change --}}
                    <input type="hidden" name="changeAmount" :value="changeAmount.toFixed(2)">

                    <div class="mt-1 text-sm">
                        Change: <span class="font-bold"
                            :class="changeAmount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                            x-text="'LKR ' + changeAmount.toFixed(2)"></span>
                    </div>
                </div>

                <!-- Credit Payment Options -->
                <div x-show="paymentMethod === 'credit'" x-transition class="space-y-2">
                    <select x-model="selectedCustomerId"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Customer...</option>
                        @foreach (\App\Models\Customer::active()->get() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} (LKR
                                {{ number_format($customer->availableCredit, 2) }})</option>
                        @endforeach
                    </select>
                    <select x-model="creditTerms"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Credit Terms...</option>
                        @foreach (\App\Enums\CreditTermsEnum::cases() as $term)
                            <option value="{{ $term->value }}">{{ $term->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto pt-4">
                    <button @click="completeSaleAndPrint" type="button"
                        :disabled="cart.length === 0 || isProcessing || !paymentMethod || (paymentMethod === 'cash' &&
                            amountReceived < totals.total)"
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 font-bold text-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                        <i :class="isProcessing ? 'fas fa-spinner fa-spin' : 'fas fa-check'" class="mr-2"></i>
                        <span x-text="isProcessing ? 'Processing...' : 'Complete Sale'"></span>
                    </button>

                    <!-- Error Message -->
                    <div x-show="errorMessage" x-transition
                        class="mt-2 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-2 rounded border border-red-200 dark:border-red-800">
                        <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="errorMessage"></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Save Cart Modal -->
        <div x-show="showSaveCartModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showSaveCartModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Save Cart</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cart Name
                                (Optional)</label>
                            <input type="text" x-model="saveCartName" placeholder="Auto-generated if empty"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex space-x-2">
                            <button @click="saveCart" type="button" :disabled="isSavingCart"
                                class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition-colors disabled:opacity-50">
                                <i :class="isSavingCart ? 'fas fa-spinner fa-spin' : 'fas fa-save'" class="mr-2"></i>
                                <span x-text="isSavingCart ? 'Saving...' : 'Save'"></span>
                            </button>
                            <button @click="showSaveCartModal = false; saveCartName = ''" type="button"
                                class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Carts Modal -->
        <div x-show="showSavedCartsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showSavedCartsModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Saved Carts</h3>

                    <div x-show="isLoadingSavedCarts" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Loading saved carts...</p>
                    </div>

                    <div x-show="!isLoadingSavedCarts && savedCarts.length === 0" class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No saved carts found.</p>
                    </div>

                    <div x-show="!isLoadingSavedCarts && savedCarts.length > 0"
                        class="space-y-3 max-h-96 overflow-y-auto">
                        <template x-for="savedCart in savedCarts" :key="savedCart.id">
                            <div
                                class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-white"
                                            x-text="savedCart.cart_name || 'Unnamed Cart'"></h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-shopping-cart mr-1"></i>
                                            <span x-text="savedCart.items_count"></span> items
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="new Date(savedCart.created_at).toLocaleString()"></span>
                                        </p>
                                        <p x-show="savedCart.customer_name"
                                            class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-user mr-1"></i>
                                            <span x-text="savedCart.customer_name"></span>
                                        </p>
                                    </div>
                                    <div class="flex space-x-2 ml-4">
                                        <button @click="loadCart(savedCart.id)" type="button"
                                            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                            <i class="fas fa-download"></i> Load
                                        </button>
                                        <button @click="deleteSavedCart(savedCart.id)" type="button"
                                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4">
                        <button @click="showSavedCartsModal = false" type="button"
                            class="w-full bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clock In Modal -->
        <div x-show="showClockInModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 shadow-xl">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Clock In</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Cash
                            (Optional)</label>
                        <input type="number" x-model="openingCash" step="0.01" placeholder="0.00"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes
                            (Optional)</label>
                        <textarea x-model="shiftNotes" rows="3" placeholder="Any notes about this shift..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button @click="clockIn()"
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold">
                            <i class="fas fa-check mr-2"></i>Confirm Clock In
                        </button>
                        <button @click="showClockInModal = false; openingCash = ''; shiftNotes = ''"
                            class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 rounded font-semibold">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clock Out Modal -->
        <div x-show="showClockOutModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 shadow-xl">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Clock Out</h3>
                <template x-if="shiftStats">
                    <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded">
                        <p class="text-sm text-gray-700 dark:text-gray-300">Total Sales: <span class="font-semibold"
                                x-text="'LKR ' + (shiftStats.total_sales || 0).toFixed(2)"></span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Transactions: <span class="font-semibold"
                                x-text="shiftStats.total_sales_count || 0"></span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Expected Cash: <span class="font-semibold"
                                x-text="'LKR ' + (shiftStats.expected_cash || 0).toFixed(2)"></span></p>
                    </div>
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Cash
                            (Optional)</label>
                        <input type="number" x-model="closingCash" step="0.01" placeholder="0.00"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes
                            (Optional)</label>
                        <textarea x-model="shiftNotes" rows="3" placeholder="Any notes about this shift..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button @click="clockOut()"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-semibold">
                            <i class="fas fa-sign-out-alt mr-2"></i>Confirm Clock Out
                        </button>
                        <button @click="showClockOutModal = false; closingCash = ''; shiftNotes = ''"
                            class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 rounded font-semibold">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Batch Selection Modal -->
        <div x-show="showBatchSelectionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showBatchSelectionModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Select Batch</h3>
                        <button @click="showBatchSelectionModal = false" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Multiple batches found. Please select the one
                        you want to checkout.</p>

                    <div class="space-y-3 max-h-96 overflow-y-auto p-1">
                        <template x-for="stock in selectedProductStocks" :key="stock.stock_id">
                            <button @click="addToCart(stock); showBatchSelectionModal = false" type="button"
                                class="w-full text-left border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all transform hover:scale-[1.01] shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 dark:text-white text-lg"
                                            x-text="stock.product_name"></div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span
                                                class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2.5 py-1 rounded text-xs font-mono font-bold"
                                                x-text="'Batch: ' + stock.batch_number"></span>
                                            <span
                                                class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded text-xs font-bold"
                                                x-text="'Qty: ' + (stock.is_weighted ? parseFloat(stock.available_quantity / 1000).toFixed(2) : parseFloat(stock.available_quantity).toFixed(stock.allow_decimal_sales ? 2 : 0)) + ' ' + (stock.is_weighted ? stock.unit : (stock.base_unit || stock.unit || 'pcs'))"></span>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="font-bold text-primary-600 dark:text-primary-400 text-xl">
                                            <span x-text="'LKR ' + parseFloat(stock.selling_price).toFixed(2)"></span>
                                        </div>
                                        <div x-show="stock.discount_amount > 0"
                                            class="text-sm text-red-500 font-bold mt-1">
                                            <i class="fas fa-tag mr-1"></i> - LKR <span
                                                x-text="parseFloat(stock.discount_amount).toFixed(2)"></span> Off
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div class="mt-6">
                        <button @click="showBatchSelectionModal = false" type="button"
                            class="w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 font-bold transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Hide number input arrows (spinners) */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        function cashierPos() {
            return {
                searchQuery: '',
                searchResults: [],
                isSearching: false,
                cart: [],
                totals: {
                    subtotal: 0,
                    tax: 0,
                    total: 0
                },
                paymentMethod: 'cash',
                amountReceived: 0,
                changeAmount: 0,
                customerName: '',
                customerPhone: '',
                selectedCustomerId: '',
                creditTerms: '',
                isProcessing: false,
                errorMessage: '',
                showSaveCartModal: false,
                showSavedCartsModal: false,
                saveCartName: '',
                isSavingCart: false,
                savedCarts: [],
                isLoadingSavedCarts: false,
                showBatchSelectionModal: false,
                selectedProductStocks: [],

                // Manual entry mode properties
                isManualMode: false,
                cartType: 'regular', // 'regular' or 'manual'
                manualEntry: {
                    product_name: '',
                    price: '',
                    quantity: 1,
                    entered_barcode: '',
                    tax: 0
                },

                // Shift management properties
                activeShift: null,
                shiftStats: null,
                shiftDuration: '00:00',
                showClockInModal: false,
                showClockOutModal: false,
                openingCash: '',
                closingCash: '',
                shiftNotes: '',

                init() {
                    // Load saved carts count
                    this.loadSavedCarts();
                    // Fetch active shift
                    this.fetchActiveShift();
                    // Update shift timer every minute
                    setInterval(() => {
                        if (this.activeShift) {
                            this.updateShiftDuration();
                        }
                    }, 60000);
                    // Focus on search input on load
                    this.$nextTick(() => {
                        document.querySelector('input[x-model="searchQuery"]')?.focus();
                    });

                    // Add keyboard shortcuts for Clock In/Out
                    document.addEventListener('keydown', (e) => {
                        @can('manage own shifts')
                            // F11 - Open Clock In modal (when no active shift)
                            if (e.key === 'F11') {
                                e.preventDefault(); // Prevent default F11 behavior (fullscreen)

                                if (!this.activeShift) {
                                    // No active shift - open Clock In modal
                                    this.showClockInModal = true;
                                    // Auto-focus on Opening Cash field after modal opens
                                    this.$nextTick(() => {
                                        document.querySelector('input[x-model="openingCash"]')?.focus();
                                    });
                                }
                            }

                            // F12 - Confirm Clock In (when Clock In modal is open)
                            if (e.key === 'Enter') {
                                e.preventDefault(); // Prevent default F12 behavior

                                if (this.showClockInModal) {
                                    this.clockIn();
                                }
                            }

                            // F9 - Open Clock Out modal (when active shift exists)
                            if (e.key === 'F9') {
                                e.preventDefault(); // Prevent default F9 behavior

                                if (this.activeShift) {
                                    // Active shift exists - open Clock Out modal
                                    this.showClockOutModal = true;
                                    // Auto-focus on Closing Cash field after modal opens
                                    this.$nextTick(() => {
                                        document.querySelector('input[x-model="closingCash"]')?.focus();
                                    });
                                }
                            }

                            // F10 - Confirm Clock Out (when Clock Out modal is open)
                            if (e.key === 'Enter') {
                                e.preventDefault(); // Prevent default F10 behavior

                                if (this.showClockOutModal) {
                                    this.clockOut();
                                }
                            }
                        @endcan

                        // F2 or Insert - Complete Sale and Print Directly (bypass tab opening)
                        if (e.key === 'F2' || e.key === 'Insert') {
                            e.preventDefault();

                            // Only trigger if cart has items and payment is valid
                            if (this.cart.length > 0 && !this.isProcessing) {
                                this.completeSaleAndPrint();
                            }
                        }

                        // Ctrl+M - Toggle Manual Mode
                        if (e.ctrlKey && e.key === 'm') {
                            e.preventDefault();
                            this.isManualMode = !this.isManualMode;
                            this.toggleManualMode();

                            // Focus on product name field when entering manual mode
                            if (this.isManualMode) {
                                this.$nextTick(() => {
                                    document.querySelector('input[x-model="manualEntry.product_name"]')
                                        ?.focus();
                                });
                            }
                        }
                    });
                },

                async searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    this.isSearching = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch(
                            `{{ route('api.products.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await response.json();
                        this.searchResults = data;

                        // Check for exact barcode match with multiple results
                        if (this.searchResults.length > 1) {
                            const exactMatch = this.searchResults.filter(p => p.barcode === this.searchQuery);
                            if (exactMatch.length > 1) {
                                // Multiple batches for same barcode
                                this.openBatchSelectionModal(exactMatch);
                            }
                        } else if (this.searchResults.length === 1 && this.searchResults[0].barcode === this
                            .searchQuery) {
                            // Exact unique barcode match - add directly
                            this.addToCart(this.searchResults[0]);
                        }
                    } catch (error) {
                        console.error('Search error:', error);
                        this.errorMessage = 'Error searching products. Please try again.';
                    } finally {
                        this.isSearching = false;
                    }
                },

                openBatchSelectionModal(stocks) {
                    this.selectedProductStocks = stocks;
                    this.showBatchSelectionModal = true;
                    this.searchResults = [];
                    this.searchQuery = '';
                },

                selectFirstProduct() {
                    if (this.searchResults.length === 1) {
                        this.addToCart(this.searchResults[0]);
                    } else if (this.searchResults.length > 1) {
                        // If multiple results, show modal
                        this.openBatchSelectionModal(this.searchResults);
                    }
                },

                addToCart(product) {
                    if (!product.in_stock || product.available_quantity <= 0) {
                        this.errorMessage = `${product.product_name} is out of stock.`;
                        return;
                    }

                    // Handle weighted products (each scan is unique)
                    if (product.is_weighted) {
                        // For weighted products, check if exact same barcode already in cart
                        const existingWeighted = this.cart.findIndex(item =>
                            item.is_weighted && item.barcode === product.barcode
                        );

                        if (existingWeighted !== -1) {
                            this.errorMessage = 'This weighted item is already in the cart. Please scan a new barcode.';
                            return;
                        }

                        // Add weighted product with exact scanned weight
                        this.cart.push({
                            ...product,
                            quantity: product.quantity, // Weight in grams
                            allow_quantity_edit: false, // Cannot edit weighted product quantity
                            discountType: 'percentage',
                            discountValue: 0,
                            discountAmount: 0,
                            originalPrice: product.selling_price, // Price per kg
                            final_price: product.selling_price // Initialize final_price
                        });
                        this.calculateTotals();
                    } else {
                        // Regular product handling
                        // Check if product already in cart
                        const existingIndex = this.cart.findIndex(item => item.id === product.id && !item.is_weighted);

                        if (existingIndex !== -1) {
                            // Increment quantity if not exceeding available stock
                            const currentItem = this.cart[existingIndex];
                            const step = currentItem.allow_decimal_sales ? 0.1 : 1;
                            const newQty = Math.round((currentItem.quantity + step) * 100) / 100;

                            if (newQty <= currentItem.available_quantity) {
                                currentItem.quantity = newQty;
                                this.calculateTotals();
                            } else {
                                const unit = currentItem.base_unit || currentItem.unit || 'pcs';
                                this.errorMessage =
                                    `Maximum available quantity (${currentItem.available_quantity} ${unit}) reached for ${product.product_name}.`;
                            }
                        } else {
                            // Add new item to cart - start with 1 unit or 0.1 for decimal products
                            const initialQty = product.allow_decimal_sales ? 0.1 : 1;
                            this.cart.push({
                                ...product,
                                quantity: initialQty,
                                allow_quantity_edit: true,
                                // Discount fields
                                discountType: 'percentage',
                                discountValue: 0,
                                discountAmount: 0,
                                originalPrice: product.selling_price,
                                final_price: product.selling_price // Initialize final_price
                            });
                            this.calculateTotals();
                        }
                    }

                    this.calculateTotals();
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.errorMessage = '';

                    // Keep focus on search input for continuous scanning
                    this.$nextTick(() => {
                        document.querySelector('input[x-model="searchQuery"]')?.focus();
                    });
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.calculateTotals();
                    this.errorMessage = '';
                },

                incrementQuantity(index) {
                    const item = this.cart[index];
                    const step = item.allow_decimal_sales ? 0.1 : 1;
                    const newQty = Math.round((item.quantity + step) * 100) / 100; // Avoid floating point issues

                    // For manual items, bypass available_quantity check
                    if (item.isManual || newQty <= item.available_quantity) {
                        item.quantity = newQty;
                        this.calculateTotals();
                    }
                },

                decrementQuantity(index) {
                    const item = this.cart[index];
                    const step = item.allow_decimal_sales ? 0.1 : 1;
                    const minQty = item.allow_decimal_sales ? 0.01 : 1;
                    const newQty = Math.round((item.quantity - step) * 100) / 100;

                    if (newQty >= minQty) {
                        item.quantity = newQty;
                        this.calculateTotals();
                    }
                },

                validateQuantity(index) {
                    const item = this.cart[index];
                    const minQty = item.allow_decimal_sales ? 0.01 : 1;

                    if (item.quantity < minQty) {
                        item.quantity = minQty;
                    } else if (!item.isManual && item.quantity > item.available_quantity) {
                        // Only check max quantity for non-manual items
                        item.quantity = parseFloat(item.available_quantity);
                        const unit = item.base_unit || item.unit || 'pcs';
                        this.errorMessage = `Maximum available quantity is ${item.available_quantity} ${unit}.`;
                    }

                    // Round to appropriate precision
                    item.quantity = item.allow_decimal_sales ?
                        Math.round(item.quantity * 100) / 100 :
                        Math.floor(item.quantity);

                    this.calculateTotals();
                },

                applyDiscount(index) {
                    const item = this.cart[index];

                    if (item.discountType === 'none') {
                        // Reset to original price
                        item.discountAmount = 0;
                        item.discountValue = 0;
                        item.final_price = item.selling_price; // Reset final price
                    } else {
                        // Calculate discount
                        const subtotal = item.quantity * item.selling_price;
                        let discountAmount = 0;

                        if (item.discountType === 'percentage') {
                            discountAmount = (subtotal * item.discountValue) / 100;
                        } else if (item.discountType === 'fixed_amount') {
                            discountAmount = Math.min(item.discountValue, subtotal);
                        }

                        item.discountAmount = discountAmount;
                        item.final_price = (subtotal - discountAmount) / item.quantity; // Update final price per unit
                    }

                    this.calculateTotals();
                },

                calculateItemTotal(item) {
                    let originalSubtotal;

                    // Calculate subtotal based on product type
                    if (item.is_weighted) {
                        // For weighted products: (weight_grams / 1000) × price_per_kg
                        originalSubtotal = (item.quantity / 1000) * parseFloat(item.originalPrice || item.selling_price);
                    } else {
                        // Regular products: quantity × price
                        originalSubtotal = item.quantity * parseFloat(item.originalPrice || item.selling_price);
                    }

                    const discountAmount = parseFloat(item.discountAmount || 0);
                    const subtotalAfterDiscount = originalSubtotal - discountAmount;
                    const tax = subtotalAfterDiscount * (parseFloat(item.tax) / 100);
                    return subtotalAfterDiscount + tax;
                },

                calculateTotals() {
                    let subtotal = 0;
                    let tax = 0;
                    let totalDiscount = 0;

                    this.cart.forEach(item => {
                        const originalPrice = parseFloat(item.selling_price);
                        const discountAmount = parseFloat(item.discountAmount || 0);

                        let itemSubtotal;
                        if (item.is_weighted) {
                            // For weighted products: (weight_grams / 1000) × price_per_kg
                            itemSubtotal = (item.quantity / 1000) * originalPrice;
                        } else {
                            // Regular products: quantity × price
                            itemSubtotal = item.quantity * originalPrice;
                        }

                        const itemSubtotalAfterDiscount = itemSubtotal - discountAmount;
                        const itemTax = itemSubtotalAfterDiscount * (parseFloat(item.tax) / 100);

                        subtotal += itemSubtotal;
                        totalDiscount += discountAmount;
                        tax += itemTax;
                    });

                    this.totals.subtotal = subtotal;
                    this.totals.discount = totalDiscount;
                    this.totals.tax = tax;
                    this.totals.total = (subtotal - totalDiscount) + tax;

                    this.calculateChange();
                },

                calculateChange() {
                    if (this.paymentMethod === 'cash') {
                        this.changeAmount = this.amountReceived - this.totals.total;
                    } else {
                        this.changeAmount = 0;
                    }
                },

                clearCart() {
                    if (confirm('Are you sure you want to clear the cart?')) {
                        this.cart = [];
                        this.cartType = 'regular';
                        this.calculateTotals();
                        this.errorMessage = '';
                    }
                },

                toggleManualMode() {
                    // Prevent switching if cart has items
                    if (this.cart.length > 0) {
                        this.isManualMode = !this.isManualMode; // Revert toggle
                        alert('Please clear the cart before switching modes.');
                        return;
                    }

                    // Update cart type based on mode
                    this.cartType = this.isManualMode ? 'manual' : 'regular';
                    this.errorMessage = '';

                    // Reset manual entry form when switching modes
                    this.manualEntry = {
                        product_name: '',
                        price: '',
                        quantity: 1,
                        entered_barcode: '',
                        tax: 0
                    };
                },

                toggleManualModeShortcut() {
                    // Toggle manual mode with Ctrl+M
                    this.isManualMode = !this.isManualMode;
                    this.toggleManualMode();

                    // Focus on product name field when entering manual mode
                    if (this.isManualMode) {
                        this.$nextTick(() => {
                            const input = document.querySelector('input[x-model="manualEntry.product_name"]');
                            if (input) input.focus();
                        });
                    }
                },

                async handleManualBarcode(isAuto = false) {
                    const barcode = this.manualEntry.entered_barcode;

                    // If no barcode entered, proceed with standard manual entry validation (only if not auto)
                    if (!barcode) {
                        if (!isAuto) this.addManualItemToCart();
                        return;
                    }

                    try {
                        this.isProcessing = true;
                        // Search for the product by barcode, including out of stock items
                        const response = await fetch(
                            `{{ route('api.products.search') }}?q=${encodeURIComponent(barcode)}&include_out_of_stock=1`
                        );
                        const results = await response.json();

                        // Check for exact barcode match
                        const exactMatch = results.find(p => p.barcode === barcode);

                        if (exactMatch) {
                            // Convert to manual item format and add to cart directly
                            this.manualEntry = {
                                product_name: exactMatch.product_name,
                                price: exactMatch.selling_price,
                                quantity: 1,
                                entered_barcode: barcode,
                                tax: exactMatch.tax || 0
                            };

                            // Add to cart immediately as a manual item
                            this.addManualItemToCart();
                        } else {
                            // No match found
                            // If auto-triggered (scanning/typing), don't show validation errors yet
                            // Only add manual item if manually triggered (Enter key)
                            if (!isAuto) {
                                this.addManualItemToCart();
                            }
                        }
                    } catch (error) {
                        console.error('Barcode lookup error:', error);
                        if (!isAuto) this.addManualItemToCart();
                    } finally {
                        this.isProcessing = false;
                    }
                },

                addManualItemToCart() {
                    // Validate required fields
                    if (!this.manualEntry.product_name || !this.manualEntry.product_name.trim()) {
                        alert('Product name is required!');
                        return;
                    }

                    if (!this.manualEntry.price || parseFloat(this.manualEntry.price) <= 0) {
                        alert('Valid price is required!');
                        return;
                    }

                    if (!this.manualEntry.quantity || parseFloat(this.manualEntry.quantity) <= 0) {
                        alert('Valid quantity is required!');
                        return;
                    }

                    // Add manual item to cart
                    const price = parseFloat(this.manualEntry.price);
                    const quantity = parseFloat(this.manualEntry.quantity);
                    const tax = parseFloat(this.manualEntry.tax) || 0;
                    const subtotal = price * quantity;
                    const taxAmount = subtotal * (tax / 100);
                    const total = subtotal + taxAmount;

                    this.cart.push({
                        id: Date.now(), // Unique ID for manual items
                        product_name: this.manualEntry.product_name.trim(),
                        price: price,
                        selling_price: price, // For cart display (line 175)
                        originalPrice: price, // For discount calculations
                        quantity: quantity,
                        entered_barcode: this.manualEntry.entered_barcode || null,
                        tax: tax,
                        subtotal: subtotal,
                        total: total,
                        isManual: true, // Flag to identify manual items
                        sku: 'MANUAL', // Display as MANUAL in cart
                        unit: 'pcs',
                        discountType: 'percentage', // For consistency with regular items
                        discountValue: 0, // For discount functionality
                        discountAmount: 0, // For discount functionality
                        allow_decimal_sales: false, // Enforce whole numbers for manual items
                        final_price: price // Initialize final_price with the entered price
                    });

                    // Set cart type
                    this.cartType = 'manual';

                    // Reset manual entry form
                    this.manualEntry = {
                        product_name: '',
                        price: '',
                        quantity: 1,
                        entered_barcode: '',
                        tax: 0
                    };

                    // Recalculate totals
                    this.calculateTotals();
                    this.errorMessage = '';

                    // Focus back on product name input
                    this.$nextTick(() => {
                        document.querySelector('input[x-model="manualEntry.product_name"]')?.focus();
                    });
                },

                async completeSaleAndPrint() {
                    // This method completes the sale and directly prints without opening a new tab
                    // Validation
                    if (this.cart.length === 0) {
                        this.errorMessage = 'Cart is empty. Add products to complete sale.';
                        return;
                    }

                    if (!this.paymentMethod) {
                        this.errorMessage = 'Please select a payment method.';
                        return;
                    }

                    if (this.paymentMethod === 'cash' && this.amountReceived < this.totals.total) {
                        this.errorMessage = 'Amount received is less than total amount.';
                        return;
                    }

                    if (this.paymentMethod === 'credit') {
                        if (!this.selectedCustomerId) {
                            this.errorMessage = 'Please select a customer for credit sale.';
                            return;
                        }
                        if (!this.creditTerms) {
                            this.errorMessage = 'Please select credit terms.';
                            return;
                        }
                    }

                    this.isProcessing = true;
                    this.errorMessage = '';

                    try {
                        // Determine endpoint and payload based on cart type
                        const endpoint = this.cartType === 'manual' ? '{{ route('manual-sales.store') }}' :
                            '{{ route('sales.store') }}';

                        let payload;
                        if (this.cartType === 'manual') {
                            // Manual sale payload
                            payload = {
                                payment_method: this.paymentMethod,
                                customer_id: this.selectedCustomerId || null,
                                customer_name: this.customerName || null,
                                customer_phone: this.customerPhone || null,
                                amountReceived: this.paymentMethod === 'cash' ? this.amountReceived : null,
                                changeAmount: this.paymentMethod === 'cash' ? this.changeAmount : 0,
                                items: this.cart.map(item => ({
                                    product_name: item.product_name,
                                    price: item.price,
                                    quantity: item.quantity,
                                    entered_barcode: item.entered_barcode,
                                    tax: item.tax || 0,
                                    discount: item.discountType !== 'none' ? {
                                        type: item.discountType,
                                        value: item.discountValue,
                                        amount: item.discountAmount,
                                        final_price: (item.quantity * item.originalPrice - item
                                            .discountAmount) / item.quantity
                                    } : null
                                }))
                            };
                        } else {
                            // Regular sale payload
                            payload = {
                                payment_method: this.paymentMethod,
                                customer_id: this.paymentMethod === 'credit' ? this.selectedCustomerId : null,
                                credit_terms: this.paymentMethod === 'credit' ? this.creditTerms : null,
                                customer_name: this.customerName || null,
                                customer_phone: this.customerPhone || null,
                                amountReceived: this.paymentMethod === 'cash' ? this.amountReceived : null,
                                changeAmount: this.paymentMethod === 'cash' ? this.changeAmount : 0,
                                items: this.cart.map(item => ({
                                    product_id: item.id,
                                    stock_id: item.stock_id,
                                    quantity: item.quantity,
                                    selling_price: item.selling_price,
                                    tax: item.tax,
                                    discount: {
                                        type: item.discountType,
                                        value: item.discountValue,
                                        amount: item.discountAmount,
                                        final_price: item.final_price
                                    }
                                }))
                            };
                        }

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Success - fetch the receipt HTML and print directly
                            const saleId = this.cartType === 'manual' ? data.manual_sale.id : data.sale.id;
                            const receiptUrl = this.cartType === 'manual' ?
                                `{{ url('/manual-sales') }}/${saleId}` :
                                `{{ url('/sales') }}/${saleId}`;

                            // Fetch the receipt page
                            const receiptResponse = await fetch(receiptUrl);
                            const receiptHtml = await receiptResponse.text();

                            // Create a hidden iframe for printing
                            const printFrame = document.createElement('iframe');
                            printFrame.style.display = 'none';
                            document.body.appendChild(printFrame);

                            // Write the receipt HTML to the iframe
                            printFrame.contentDocument.open();
                            printFrame.contentDocument.write(receiptHtml);
                            printFrame.contentDocument.close();

                            // Wait for content to load, then print
                            printFrame.onload = () => {
                                setTimeout(() => {
                                    printFrame.contentWindow.print();

                                    // Clean up after printing
                                    setTimeout(() => {
                                        document.body.removeChild(printFrame);

                                        // Reset the cart and form
                                        this.cart = [];
                                        this.cartType = 'regular';
                                        this.customerName = '';
                                        this.customerPhone = '';
                                        this.amountReceived = 0;
                                        this.selectedCustomerId = '';
                                        this.creditTerms = '';
                                        this.calculateTotals();

                                        // Show success message
                                        toastr.success('Sale completed and sent to printer!');

                                        // Refocus on search
                                        this.$nextTick(() => {
                                            document.querySelector(
                                                'input[x-model="searchQuery"]')?.focus();
                                        });
                                    }, 1000);
                                }, 500);
                            };
                        } else {
                            this.errorMessage = data.message || 'Error processing sale. Please try again.';
                        }
                    } catch (error) {
                        console.error('Sale error:', error);
                        this.errorMessage = 'Error processing sale. Please try again.';
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async completeSale() {
                    // Validation
                    if (this.cart.length === 0) {
                        this.errorMessage = 'Cart is empty. Add products to complete sale.';
                        return;
                    }

                    if (!this.paymentMethod) {
                        this.errorMessage = 'Please select a payment method.';
                        return;
                    }

                    if (this.paymentMethod === 'cash' && this.amountReceived < this.totals.total) {
                        this.errorMessage = 'Amount received is less than total amount.';
                        return;
                    }

                    if (this.paymentMethod === 'credit') {
                        if (!this.selectedCustomerId) {
                            this.errorMessage = 'Please select a customer for credit sale.';
                            return;
                        }
                        if (!this.creditTerms) {
                            this.errorMessage = 'Please select credit terms.';
                            return;
                        }
                    }

                    this.isProcessing = true;
                    this.errorMessage = '';

                    try {
                        // Determine endpoint and payload based on cart type
                        const endpoint = this.cartType === 'manual' ? '{{ route('manual-sales.store') }}' :
                            '{{ route('sales.store') }}';

                        let payload;
                        if (this.cartType === 'manual') {
                            // Manual sale payload
                            payload = {
                                payment_method: this.paymentMethod,
                                customer_id: this.selectedCustomerId || null,
                                customer_name: this.customerName || null,
                                customer_phone: this.customerPhone || null,
                                amountReceived: this.paymentMethod === 'cash' ? this.amountReceived : null,
                                changeAmount: this.paymentMethod === 'cash' ? this.changeAmount : 0,
                                items: this.cart.map(item => ({
                                    product_name: item.product_name,
                                    price: item.price,
                                    quantity: item.quantity,
                                    entered_barcode: item.entered_barcode,
                                    tax: item.tax || 0,
                                    discount: item.discountType !== 'none' ? {
                                        type: item.discountType,
                                        value: item.discountValue,
                                        amount: item.discountAmount,
                                        final_price: (item.quantity * item.originalPrice - item
                                            .discountAmount) / item.quantity
                                    } : null
                                }))
                            };
                        } else {
                            // Regular sale payload
                            payload = {
                                payment_method: this.paymentMethod,
                                customer_id: this.paymentMethod === 'credit' ? this.selectedCustomerId : null,
                                credit_terms: this.paymentMethod === 'credit' ? this.creditTerms : null,
                                customer_name: this.customerName || null,
                                customer_phone: this.customerPhone || null,
                                amountReceived: this.paymentMethod === 'cash' ? this.amountReceived : null,
                                changeAmount: this.paymentMethod === 'cash' ? this.changeAmount : 0,
                                items: this.cart.map(item => ({
                                    product_id: item.id,
                                    stock_id: item.stock_id,
                                    quantity: item.quantity,
                                    selling_price: item.selling_price,
                                    tax: item.tax,
                                    discount: {
                                        type: item.discountType,
                                        value: item.discountValue,
                                        amount: item.discountAmount,
                                        final_price: item.final_price
                                    }
                                }))
                            };
                        }

                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Success - redirect to receipt
                            if (this.cartType === 'manual') {
                                window.location.href = `{{ url('/manual-sales') }}/${data.manual_sale.id}`;
                            } else {
                                window.location.href = `{{ url('/sales') }}/${data.sale.id}`;
                            }
                        } else {
                            this.errorMessage = data.message || 'Error processing sale. Please try again.';
                        }
                    } catch (error) {
                        console.error('Sale error:', error);
                        this.errorMessage = 'Error processing sale. Please try again.';
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async saveCart() {
                    if (this.cart.length === 0) {
                        this.errorMessage = 'Cart is empty. Nothing to save.';
                        return;
                    }

                    this.isSavingCart = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch('{{ route('api.saved-carts.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                cart_name: this.saveCartName || null,
                                customer_name: this.customerName || null,
                                customer_phone: this.customerPhone || null,
                                payment_method: this.paymentMethod || null,
                                items: this.cart
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Clear current cart
                            this.cart = [];
                            this.customerName = '';
                            this.customerPhone = '';
                            this.paymentMethod = 'cash';
                            this.amountReceived = 0;
                            this.calculateTotals();

                            // Close modal and reset
                            this.showSaveCartModal = false;
                            this.saveCartName = '';

                            // Reload saved carts
                            this.loadSavedCarts();

                            alert('Cart saved successfully!');
                        } else {
                            this.errorMessage = data.message || 'Failed to save cart.';
                        }
                    } catch (error) {
                        console.error('Save cart error:', error);
                        this.errorMessage = 'Failed to save cart. Please try again.';
                    } finally {
                        this.isSavingCart = false;
                    }
                },

                async loadSavedCarts() {
                    this.isLoadingSavedCarts = true;

                    try {
                        const response = await fetch('{{ route('api.saved-carts.index') }}');
                        const data = await response.json();
                        this.savedCarts = data;
                    } catch (error) {
                        console.error('Load saved carts error:', error);
                    } finally {
                        this.isLoadingSavedCarts = false;
                    }
                },

                async loadCart(savedCartId) {
                    if (this.cart.length > 0) {
                        if (!confirm('Loading this cart will replace your current cart. Continue?')) {
                            return;
                        }
                    }

                    this.errorMessage = '';

                    try {
                        const response = await fetch(`{{ url('/api/saved-carts') }}/${savedCartId}`);
                        const savedCart = await response.json();

                        // Map saved cart items to cart format
                        this.cart = savedCart.items.map(item => ({
                            id: item.product.id,
                            product_name: item.product.product_name,
                            sku: item.product.sku,
                            barcode: item.product.barcode,
                            category: item.product.category?.name || 'N/A',
                            brand: item.product.brand?.name || 'N/A',
                            selling_price: item.price,
                            tax: item.tax,
                            unit: item.product.unit || 'pcs',
                            available_quantity: item.stock?.available_quantity || item.product
                                .available_stocks_sum_available_quantity || 0,
                            quantity: item.quantity
                        }));

                        // Restore customer info and payment method
                        this.customerName = savedCart.customer_name || '';
                        this.customerPhone = savedCart.customer_phone || '';
                        this.paymentMethod = savedCart.payment_method || 'cash';

                        // Calculate totals
                        this.calculateTotals();

                        // Close modal
                        this.showSavedCartsModal = false;

                        alert('Cart loaded successfully!');
                    } catch (error) {
                        console.error('Load cart error:', error);
                        this.errorMessage = 'Failed to load cart. Please try again.';
                    }
                },

                async deleteSavedCart(savedCartId) {
                    if (!confirm('Are you sure you want to delete this saved cart?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('/api/saved-carts') }}/${savedCartId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Reload saved carts
                            this.loadSavedCarts();
                            alert('Cart deleted successfully!');
                        } else {
                            alert('Failed to delete cart.');
                        }
                    } catch (error) {
                        console.error('Delete cart error:', error);
                        alert('Failed to delete cart. Please try again.');
                    }
                },

                // Shift Management Methods
                async fetchActiveShift() {
                    try {
                        const response = await fetch('{{ route('shifts.current') }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.activeShift = data.data.shift;
                            this.shiftStats = data.data.statistics;
                            this.updateShiftDuration();
                        } else {
                            this.activeShift = null;
                            this.shiftStats = null;
                        }
                    } catch (error) {
                        console.error('Fetch active shift error:', error);
                    }
                },

                updateShiftDuration() {
                    if (!this.activeShift || !this.activeShift.clock_in_at) return;
                    const start = new Date(this.activeShift.clock_in_at);
                    const now = new Date();
                    const diff = Math.floor((now - start) / 1000 / 60); // minutes
                    const hours = Math.floor(diff / 60);
                    const minutes = diff % 60;
                    this.shiftDuration = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                },

                async clockIn() {
                    try {
                        const response = await fetch('{{ route('shifts.clock-in') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                opening_cash: this.openingCash || null,
                                notes: this.shiftNotes || null
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            toastr.success(data.message);
                            this.showClockInModal = false;
                            await this.fetchActiveShift();
                            this.openingCash = '';
                            this.shiftNotes = '';
                        } else {
                            toastr.error(data.message);
                        }
                    } catch (error) {
                        console.error('Clock in error:', error);
                        toastr.error('Failed to clock in. Please try again.');
                    }
                },

                async clockOut() {
                    if (!this.activeShift) return;

                    try {
                        const response = await fetch(`{{ url('/shifts') }}/${this.activeShift.id}/clock-out`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                closing_cash: this.closingCash || null,
                                notes: this.shiftNotes || null
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            toastr.success(data.message);
                            this.showClockOutModal = false;
                            this.activeShift = null;
                            this.shiftStats = null;
                            this.closingCash = '';
                            this.shiftNotes = '';
                        } else {
                            toastr.error(data.message);
                        }
                    } catch (error) {
                        console.error('Clock out error:', error);
                        toastr.error('Failed to clock out. Please try again.');
                    }
                }
            }
        }
    </script>
@endsection
