@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div x-data="cashierPos()" x-init="init()">
    <!-- Shift Status Bar -->
    <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex justify-between items-center">
            <div>
                <template x-if="activeShift">
                    <div class="flex items-center gap-4">
                        <span class="inline-block px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                            <i class="fas fa-clock mr-1"></i> Active Shift
                        </span>
                        <span class="text-gray-700 dark:text-gray-300">
                            Started: <span x-text="activeShift ? new Date(activeShift.clock_in_at).toLocaleTimeString() : ''"></span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-300">
                            Duration: <span x-text="shiftDuration"></span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-300">
                            Sales: <span x-text="shiftStats ? shiftStats.total_sales_count : 0"></span>
                        </span>
                    </div>
                </template>
                <template x-if="!activeShift">
                    <span class="text-gray-500 dark:text-gray-400">
                        <i class="fas fa-exclamation-circle mr-2"></i>No active shift - Please clock in to start
                    </span>
                </template>
            </div>
            <div class="flex gap-2">
                @can('manage own shifts')
                    <template x-if="!activeShift">
                        <button @click="showClockInModal = true"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold">
                            <i class="fas fa-sign-in-alt mr-2"></i>Clock In
                        </button>
                    </template>
                    <template x-if="activeShift">
                        <button @click="showClockOutModal = true"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-semibold">
                            <i class="fas fa-sign-out-alt mr-2"></i>Clock Out
                        </button>
                    </template>
                    <a href="{{ route('shifts.my-shifts') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-semibold">
                        <i class="fas fa-history mr-2"></i>My Shifts
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Search & Cart -->
        <div class="lg:col-span-2 space-y-6">
        <!-- Product Search -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Search</h2>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchProducts"
                    @keydown.enter.prevent="selectFirstProduct"
                    placeholder="Search products by name, barcode, or SKU..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>

                <!-- Search Results Dropdown -->
                <div
                    x-show="searchResults.length > 0 && searchQuery.length > 0"
                    x-transition
                    class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg max-h-96 overflow-y-auto">
                    <template x-for="product in searchResults" :key="product.id">
                        <button
                            @click="addToCart(product)"
                            type="button"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white" x-text="product.product_name"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span>SKU: </span><span x-text="product.sku"></span>
                                        <span class="ml-2" x-show="product.barcode">Barcode: <span x-text="product.barcode"></span></span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span x-text="product.category"></span> | <span x-text="product.brand"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-gray-900 dark:text-white" x-text="'LKR ' + parseFloat(product.selling_price).toFixed(2)"></div>
                                    <div class="text-xs" :class="product.available_quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                        <span x-text="product.available_quantity"></span> <span x-text="product.unit"></span> available
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div x-show="isSearching" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-spinner fa-spin mr-2"></i>Searching...
            </div>
        </div>

        <!-- Shopping Cart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Shopping Cart</h2>

            <div x-show="cart.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                <p>Cart is empty. Search and add products to get started.</p>
            </div>

            <div x-show="cart.length > 0" class="space-y-3">
                <!-- Cart Items -->
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.product_name"></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                LKR <span x-text="parseFloat(item.selling_price).toFixed(2)"></span> each
                                <span class="ml-2 text-xs">Tax: <span x-text="item.tax"></span>%</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Available: <span x-text="item.available_quantity"></span> <span x-text="item.unit"></span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button
                                @click="decrementQuantity(index)"
                                type="button"
                                class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-500">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input
                                type="number"
                                x-model.number="item.quantity"
                                @input="validateQuantity(index)"
                                min="1"
                                :max="item.available_quantity"
                                class="w-16 text-center font-medium dark:text-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-2 py-1">
                            <button
                                @click="incrementQuantity(index)"
                                type="button"
                                :disabled="item.quantity >= item.available_quantity"
                                class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus text-xs"></i>
                            </button>

                            <!-- Added spacing for currency and removed the previous alignment problem -->
                            <span class="w-20 text-right font-medium dark:text-white ml-4" x-text="'LKR ' + calculateItemTotal(item).toFixed(2)"></span>

                            <button
                                @click="removeFromCart(index)"
                                type="button"
                                class="text-red-500 hover:text-red-700 ml-4">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Cart Actions -->
            <div x-show="cart.length > 0" class="mt-4 flex space-x-2">
                <button
                    @click="clearCart"
                    type="button"
                    class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Clear Cart
                </button>
                <button
                    @click="showSaveCartModal = true"
                    type="button"
                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Cart
                </button>
            </div>

            <!-- Load Cart Button -->
            <div class="mt-4">
                <button
                    @click="showSavedCartsModal = true; loadSavedCarts()"
                    type="button"
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-folder-open mr-2"></i>Load Saved Cart <span x-show="savedCarts.length > 0" class="ml-1 bg-green-800 px-2 py-0.5 rounded-full text-xs" x-text="savedCarts.length"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="space-y-6">
        <!-- Order Summary -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="font-medium dark:text-white" x-text="'LKR ' + totals.subtotal.toFixed(2)"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                    <span class="font-medium dark:text-white" x-text="'LKR ' + totals.tax.toFixed(2)"></span>
                </div>
                <hr class="border-gray-200 dark:border-gray-600">
                <div class="flex justify-between text-lg font-bold">
                    <span class="dark:text-white">Total:</span>
                    <span class="text-green-600 dark:text-green-400" x-text="'LKR ' + totals.total.toFixed(2)"></span>
                </div>
            </div>
        </div>

        <!-- Customer Information (Optional) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information (Optional)</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                    <input
                        type="text"
                        x-model="customerName"
                        placeholder="Walk-in Customer"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                    <input
                        type="text"
                        x-model="customerPhone"
                        placeholder="Optional"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Method</h2>
            <div class="space-y-3">
                <button
                    @click="paymentMethod = 'cash'"
                    type="button"
                    class="w-full p-3 border-2 rounded-lg text-left transition-colors"
                    :class="paymentMethod === 'cash' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave mr-3" :class="paymentMethod === 'cash' ? 'text-primary-600' : 'text-gray-600 dark:text-gray-400'"></i>
                        <span class="font-medium" :class="paymentMethod === 'cash' ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300'">Cash</span>
                    </div>
                </button>
                <button
                    @click="paymentMethod = 'card'"
                    type="button"
                    class="w-full p-3 border-2 rounded-lg text-left transition-colors"
                    :class="paymentMethod === 'card' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card mr-3" :class="paymentMethod === 'card' ? 'text-primary-600' : 'text-gray-600 dark:text-gray-400'"></i>
                        <span class="font-medium" :class="paymentMethod === 'card' ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300'">Card</span>
                    </div>
                </button>
                <button
                    @click="paymentMethod = 'credit'"
                    type="button"
                    class="w-full p-3 border-2 rounded-lg text-left transition-colors"
                    :class="paymentMethod === 'credit' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                    <div class="flex items-center">
                        <i class="fas fa-handshake mr-3" :class="paymentMethod === 'credit' ? 'text-primary-600' : 'text-gray-600 dark:text-gray-400'"></i>
                        <span class="font-medium" :class="paymentMethod === 'credit' ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300'">Credit</span>
                    </div>
                </button>
            </div>

            <!-- Cash Payment Input -->
            <div x-show="paymentMethod === 'cash'" x-transition class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Received</label>
                <input
                    type="number"
                    x-model.number="amountReceived"
                    @input="calculateChange"
                    step="0.01"
                    placeholder="0.00"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Change: <span class="font-medium text-lg" :class="changeAmount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" x-text="'LKR ' + changeAmount.toFixed(2)"></span>
                </div>
            </div>

            <!-- Credit Payment Options -->
            <div x-show="paymentMethod === 'credit'" x-transition class="mt-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Customer *</label>
                    <select x-model="selectedCustomerId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select a customer...</option>
                        @foreach(\App\Models\Customer::active()->get() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - Available Credit: LKR {{ number_format($customer->availableCredit, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Credit Terms *</label>
                    <select x-model="creditTerms" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select credit terms...</option>
                        @foreach(\App\Enums\CreditTermsEnum::cases() as $term)
                            <option value="{{ $term->value }}">{{ $term->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button
                @click="completeSale"
                type="button"
                :disabled="cart.length === 0 || isProcessing || !paymentMethod || (paymentMethod === 'cash' && amountReceived < totals.total)"
                class="w-full bg-primary-600 text-white py-3 px-4 rounded-md hover:bg-primary-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i :class="isProcessing ? 'fas fa-spinner fa-spin' : 'fas fa-check'" class="mr-2"></i>
                <span x-text="isProcessing ? 'Processing...' : 'Complete Sale'"></span>
            </button>
        </div>

        <!-- Error Message -->
        <div x-show="errorMessage" x-transition class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded">
            <p x-text="errorMessage"></p>
        </div>
    </div>

    <!-- Save Cart Modal -->
    <div x-show="showSaveCartModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showSaveCartModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Save Cart</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cart Name (Optional)</label>
                        <input
                            type="text"
                            x-model="saveCartName"
                            placeholder="Auto-generated if empty"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="saveCart"
                            type="button"
                            :disabled="isSavingCart"
                            class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 transition-colors disabled:opacity-50">
                            <i :class="isSavingCart ? 'fas fa-spinner fa-spin' : 'fas fa-save'" class="mr-2"></i>
                            <span x-text="isSavingCart ? 'Saving...' : 'Save'"></span>
                        </button>
                        <button
                            @click="showSaveCartModal = false; saveCartName = ''"
                            type="button"
                            class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saved Carts Modal -->
    <div x-show="showSavedCartsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
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

                <div x-show="!isLoadingSavedCarts && savedCarts.length > 0" class="space-y-3 max-h-96 overflow-y-auto">
                    <template x-for="savedCart in savedCarts" :key="savedCart.id">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white" x-text="savedCart.cart_name || 'Unnamed Cart'"></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        <span x-text="savedCart.items_count"></span> items
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="new Date(savedCart.created_at).toLocaleString()"></span>
                                    </p>
                                    <p x-show="savedCart.customer_name" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        <i class="fas fa-user mr-1"></i>
                                        <span x-text="savedCart.customer_name"></span>
                                    </p>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button
                                        @click="loadCart(savedCart.id)"
                                        type="button"
                                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                        <i class="fas fa-download"></i> Load
                                    </button>
                                    <button
                                        @click="deleteSavedCart(savedCart.id)"
                                        type="button"
                                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4">
                    <button
                        @click="showSavedCartsModal = false"
                        type="button"
                        class="w-full bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-colors">
                        Close
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
            },

            async searchProducts() {
                if (this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }

                this.isSearching = true;
                this.errorMessage = '';

                try {
                    const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await response.json();
                    this.searchResults = data;
                } catch (error) {
                    console.error('Search error:', error);
                    this.errorMessage = 'Error searching products. Please try again.';
                } finally {
                    this.isSearching = false;
                }
            },

            selectFirstProduct() {
                if (this.searchResults.length > 0) {
                    this.addToCart(this.searchResults[0]);
                }
            },

            addToCart(product) {
                if (!product.in_stock || product.available_quantity <= 0) {
                    this.errorMessage = `${product.product_name} is out of stock.`;
                    return;
                }

                // Check if product already in cart
                const existingIndex = this.cart.findIndex(item => item.id === product.id);

                if (existingIndex !== -1) {
                    // Increment quantity if not exceeding available stock
                    const currentItem = this.cart[existingIndex];
                    if (currentItem.quantity < currentItem.available_quantity) {
                        currentItem.quantity++;
                        this.calculateTotals();
                    } else {
                        this.errorMessage = `Maximum available quantity (${currentItem.available_quantity}) reached for ${product.product_name}.`;
                    }
                } else {
                    // Add new item to cart
                    this.cart.push({
                        ...product,
                        quantity: 1
                    });
                    this.calculateTotals();
                }

                // Clear search
                this.searchQuery = '';
                this.searchResults = [];
                this.errorMessage = '';

                // Refocus search input
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
                if (this.cart[index].quantity < this.cart[index].available_quantity) {
                    this.cart[index].quantity++;
                    this.calculateTotals();
                }
            },

            decrementQuantity(index) {
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity--;
                    this.calculateTotals();
                } else {
                    this.removeFromCart(index);
                }
            },

            validateQuantity(index) {
                const item = this.cart[index];
                if (item.quantity < 1) {
                    item.quantity = 1;
                } else if (item.quantity > item.available_quantity) {
                    item.quantity = item.available_quantity;
                    this.errorMessage = `Maximum available quantity is ${item.available_quantity}.`;
                }
                this.calculateTotals();
            },

            calculateItemTotal(item) {
                const subtotal = item.quantity * parseFloat(item.selling_price);
                const tax = subtotal * (parseFloat(item.tax) / 100);
                return subtotal + tax;
            },

            calculateTotals() {
                let subtotal = 0;
                let tax = 0;

                this.cart.forEach(item => {
                    const itemSubtotal = item.quantity * parseFloat(item.selling_price);
                    const itemTax = itemSubtotal * (parseFloat(item.tax) / 100);
                    subtotal += itemSubtotal;
                    tax += itemTax;
                });

                this.totals.subtotal = subtotal;
                this.totals.tax = tax;
                this.totals.total = subtotal + tax;

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
                    this.calculateTotals();
                    this.errorMessage = '';
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
                    const response = await fetch('/sales', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            payment_method: this.paymentMethod,
                            customer_id: this.paymentMethod === 'credit' ? this.selectedCustomerId : null,
                            credit_terms: this.paymentMethod === 'credit' ? this.creditTerms : null,
                            customer_name: this.customerName || null,
                            customer_phone: this.customerPhone || null,
                            amount_received: this.paymentMethod === 'cash' ? this.amountReceived : null,
                            items: this.cart.map(item => ({
                                product_id: item.id,
                                quantity: item.quantity
                            }))
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Success - redirect to receipt
                        window.location.href = `/sales/${data.sale.id}`;
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
                    const response = await fetch('/api/saved-carts', {
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
                    const response = await fetch('/api/saved-carts');
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
                    const response = await fetch(`/api/saved-carts/${savedCartId}`);
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
                        available_quantity: item.stock?.available_quantity || item.product.available_stocks_sum_available_quantity || 0,
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
                    const response = await fetch(`/api/saved-carts/${savedCartId}`, {
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
                    const response = await fetch('/shifts/current', {
                        headers: { 'Accept': 'application/json' }
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
                    const response = await fetch('/shifts/clock-in', {
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
                    const response = await fetch(`/shifts/${this.activeShift.id}/clock-out`, {
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

<!-- Clock In Modal -->
<div x-show="showClockInModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 shadow-xl">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Clock In</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Cash (Optional)</label>
                <input type="number" x-model="openingCash" step="0.01" placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                <textarea x-model="shiftNotes" rows="3" placeholder="Any notes about this shift..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <div class="flex gap-2">
                <button @click="clockIn()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold">
                    <i class="fas fa-check mr-2"></i>Confirm Clock In
                </button>
                <button @click="showClockInModal = false; openingCash = ''; shiftNotes = ''" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 rounded font-semibold">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clock Out Modal -->
<div x-show="showClockOutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 shadow-xl">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Clock Out</h3>
        <template x-if="shiftStats">
            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded">
                <p class="text-sm text-gray-700 dark:text-gray-300">Total Sales: <span class="font-semibold" x-text="'$' + (shiftStats.total_sales || 0).toFixed(2)"></span></p>
                <p class="text-sm text-gray-700 dark:text-gray-300">Transactions: <span class="font-semibold" x-text="shiftStats.total_sales_count || 0"></span></p>
                <p class="text-sm text-gray-700 dark:text-gray-300">Expected Cash: <span class="font-semibold" x-text="'$' + (shiftStats.expected_cash || 0).toFixed(2)"></span></p>
            </div>
        </template>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Cash (Optional)</label>
                <input type="number" x-model="closingCash" step="0.01" placeholder="0.00"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                <textarea x-model="shiftNotes" rows="3" placeholder="Any notes about this shift..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <div class="flex gap-2">
                <button @click="clockOut()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-semibold">
                    <i class="fas fa-sign-out-alt mr-2"></i>Confirm Clock Out
                </button>
                <button @click="showClockOutModal = false; closingCash = ''; shiftNotes = ''" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 rounded font-semibold">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection