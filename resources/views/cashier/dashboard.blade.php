@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Search & Cart -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Product Search -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Search</h2>
            <div class="relative">
                <input type="text" placeholder="Search products by name, barcode, or SKU..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <!-- Quick Product Grid -->
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <button class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">Coca Cola</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">$2.50</div>
                </button>
                <button class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">Bread</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">$1.25</div>
                </button>
                <button class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">Milk</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">$3.00</div>
                </button>
                <button class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">Eggs</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">$4.50</div>
                </button>
            </div>
        </div>

        <!-- Shopping Cart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Shopping Cart</h2>
            <div class="space-y-3">
                <!-- Cart Item -->
                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white">Coca Cola</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">$2.50 each</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-8 text-center font-medium dark:text-white">2</span>
                        <button class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                        <span class="w-16 text-right font-medium dark:text-white">$5.00</span>
                        <button class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- Another Cart Item -->
                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white">Bread</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">$1.25 each</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-8 text-center font-medium dark:text-white">1</span>
                        <button class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                        <span class="w-16 text-right font-medium dark:text-white">$1.25</span>
                        <button class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Actions -->
            <div class="mt-4 flex space-x-2">
                <button class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Clear Cart
                </button>
                <button class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                    <i class="fas fa-pause mr-2"></i>Hold
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
                    <span class="font-medium dark:text-white">$6.25</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tax (8%):</span>
                    <span class="font-medium dark:text-white">$0.50</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                    <span class="font-medium text-green-600">-$0.00</span>
                </div>
                <hr class="border-gray-200 dark:border-gray-600">
                <div class="flex justify-between text-lg font-bold">
                    <span class="dark:text-white">Total:</span>
                    <span class="dark:text-white">$6.75</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Method</h2>
            <div class="space-y-3">
                <button class="w-full p-3 border-2 border-primary-500 bg-primary-50 dark:bg-primary-900 rounded-lg text-left">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-primary-600 mr-3"></i>
                        <span class="font-medium text-primary-700 dark:text-primary-300">Cash</span>
                    </div>
                </button>
                <button class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card text-gray-600 dark:text-gray-400 mr-3"></i>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Card</span>
                    </div>
                </button>
                <button class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-handshake text-gray-600 dark:text-gray-400 mr-3"></i>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Credit</span>
                    </div>
                </button>
            </div>

            <!-- Cash Payment Input -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Received</label>
                <input type="number" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Change: <span class="font-medium">$0.00</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button class="w-full bg-primary-600 text-white py-3 px-4 rounded-md hover:bg-primary-700 font-medium">
                <i class="fas fa-check mr-2"></i>Complete Sale
            </button>
            <button class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                <i class="fas fa-print mr-2"></i>Print Receipt
            </button>
        </div>
    </div>
</div>
@endsection
