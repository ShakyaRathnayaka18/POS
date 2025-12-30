@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div x-data="adminDashboard()" x-init="init()" class="p-6">
    <!-- Time Travel Loading Screen -->
    <div x-show="isTimeLoading"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[9999] flex items-center justify-center"
         style="backdrop-filter: blur(10px); background: rgba(0, 0, 0, 0.5);">

        <div class="text-center">
            <!-- Animated SVG Clock -->
            <div class="clock-container mx-auto mb-8"
                 :class="{ 'clock-shake': shouldShake, 'clock-shatter': shouldShatter }">
                <img src="{{ asset('images/time.svg') }}" alt="Time Travel" class="clock-svg">
            </div>

            <!-- Animated Phrase -->
            <div class="text-white text-2xl font-bold animate-pulse"
                 :class="{ 'fade-out-text': shouldShatter }"
                 x-text="currentLoadingPhrase"></div>
        </div>
    </div>

    <!-- Time Travel Mode Indicator -->
    <div x-show="timeTravelActive"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-4">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-history text-2xl"></i>
                <div>
                    <p class="font-bold">Time Travel Mode Active</p>
                    <p class="text-sm opacity-90">Viewing dashboard as of: <span x-text="formatDate(timeTravelDate)"></span></p>
                </div>
            </div>
            <button @click="disableTimeTravel()" class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Return to Present
            </button>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        <div class="flex gap-3">
            <!-- Time Travel Toggle -->
            <div class="relative" x-data="{ showPicker: false }">
                <button @click="showPicker = !showPicker"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    Time Travel
                </button>

                <!-- Date Picker Dropdown -->
                <div x-show="showPicker"
                     @click.away="showPicker = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="absolute right-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 z-50 min-w-[300px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Date
                    </label>
                    <input type="date"
                           x-model="timeTravelDate"
                           :max="new Date().toISOString().split('T')[0]"
                           @change="enableTimeTravel(); showPicker = false"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <!-- Refresh Button -->
            <button @click="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sync-alt mr-2" :class="{ 'fa-spin': loadingRefresh }"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Metric Cards Grid (4x2) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Today's Sales -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Today's Sales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        LKR {{ number_format($todaysSales, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-dollar-sign text-2xl text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div style="perspective: 1000px;">
            <div class="relative transform-gpu transition-all duration-700 ease-in-out"
                 :class="{ 'rotate-y-180': outOfStockFlipped }"
                 @click="flipAndShowOutOfStockModal()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
                     style="backface-visibility: hidden;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Out of Stock</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ $outOfStock['count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Click to view</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                            <i class="fas fa-box-open text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Margin -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Profit Margin</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($profitMargin['percentage'], 2) }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        LKR {{ number_format($profitMargin['amount'], 2) }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-chart-line text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Active Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Customers</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($activeCustomers) }}
                    </p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-users text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- Customer Credits Outstanding -->
        <div style="perspective: 1000px;">
            <div class="relative transform-gpu transition-all duration-700 ease-in-out"
                 :class="{ 'rotate-y-180': customerCreditsFlipped }"
                 @click="flipAndShowCustomerCreditsModal()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
                     style="backface-visibility: hidden;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer Credits</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                LKR {{ number_format($customerCredits['total_amount'], 2) }}
                            </p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                            <i class="fas fa-hand-holding-usd text-2xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Credits Outstanding -->
        <div style="perspective: 1000px;">
            <div class="relative transform-gpu transition-all duration-700 ease-in-out"
                 :class="{ 'rotate-y-180': supplierCreditsFlipped }"
                 @click="flipAndShowSupplierCreditsModal()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
                     style="backface-visibility: hidden;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Supplier Credits</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                LKR {{ number_format($supplierCredits['total_amount'], 2) }}
                            </p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                            <i class="fas fa-file-invoice-dollar text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Credits -->
        <div style="perspective: 1000px;">
            <div class="relative transform-gpu transition-all duration-700 ease-in-out"
                 :class="{ 'rotate-y-180': overdueFlipped }"
                 @click="flipAndShowOverdueModal()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
                     style="backface-visibility: hidden;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Overdue Credits</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ $overdueCredits['count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                LKR {{ number_format($overdueCredits['total_amount'], 2) }}
                            </p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Shifts -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Shifts</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $activeShifts->count() }}
                    </p>
                    @if($activeShifts->count() > 0)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $activeShifts->pluck('user.name')->join(', ') }}
                        </p>
                    @endif
                </div>
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                    <i class="fas fa-clock text-2xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>

        <!-- Expiring Batches with Flip Animation -->
        <div style="perspective: 1000px;">
            <div class="relative transform-gpu transition-all duration-700 ease-in-out"
                 :class="{ 'rotate-y-180': cardFlipped }"
                 @click="flipAndShowModal()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200"
                     style="backface-visibility: hidden;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Expiring Batches</p>
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                {{ $expiringBatches['days_30'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Within 30 days
                            </p>
                        </div>
                        <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-full">
                            <i class="fas fa-exclamation-triangle text-2xl text-amber-600 dark:text-amber-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Selling Products Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top 10 Most Sold Items</h2>
                <div class="flex gap-2">
                    <button @click="updateTopSellingProducts('today')"
                            :class="topSellingPeriod === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm">
                        Today
                    </button>
                    <button @click="updateTopSellingProducts('week')"
                            :class="topSellingPeriod === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm">
                        Week
                    </button>
                    <button @click="updateTopSellingProducts('month')"
                            :class="topSellingPeriod === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm">
                        Month
                    </button>
                </div>
            </div>
            <!-- Loading Skeleton -->
            <div x-show="loadingTopSelling" class="h-[350px] flex items-center justify-center">
                <div class="animate-pulse space-y-4 w-full">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>
            <!-- Chart -->
            <div class="relative h-[350px]" :class="{ 'hidden': loadingTopSelling }">
                <canvas id="topSellingChart"></canvas>
            </div>
        </div>

        <!-- Profit Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profit Over Time</h2>
                <div class="flex gap-2">
                    <button @click="updateProfit('daily')"
                            :class="profitPeriod === 'daily' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95">
                        Daily
                    </button>
                    <button @click="updateProfit('monthly')"
                            :class="profitPeriod === 'monthly' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95">
                        Monthly
                    </button>
                    <button @click="showCustomDatePicker = true"
                            :class="profitPeriod === 'custom' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-3 py-1 rounded text-sm transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95">
                        Custom
                    </button>
                </div>
            </div>
            <!-- Loading Skeleton -->
            <div x-show="loadingProfit" class="h-[350px] flex items-center justify-center">
                <div class="animate-pulse space-y-4 w-full">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>
            <!-- Chart -->
            <div class="relative h-[350px]" :class="{ 'hidden': loadingProfit }">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Out of Stock Modal with Pagination -->
    <div x-show="showOutOfStockModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeOutOfStockModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Out of Stock Products</h3>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Brand</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="product in paginatedOutOfStock" :key="product.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="product.product_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="product.sku"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="product.category?.category_name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="product.brand?.brand_name || 'N/A'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span x-text="((outOfStockPage - 1) * itemsPerPage) + 1"></span>
                        to <span x-text="Math.min(outOfStockPage * itemsPerPage, outOfStockItems.length)"></span>
                        of <span x-text="outOfStockItems.length"></span> products
                    </div>
                    <div class="flex gap-2">
                        <button @click="outOfStockPage--"
                                :disabled="outOfStockPage === 1"
                                :class="outOfStockPage === 1 ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Previous
                        </button>

                        <template x-for="page in totalOutOfStockPages" :key="page">
                            <button @click="outOfStockPage = page"
                                    :class="outOfStockPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 rounded transition-colors duration-200"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="outOfStockPage++"
                                :disabled="outOfStockPage === totalOutOfStockPages"
                                :class="outOfStockPage === totalOutOfStockPages ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Next
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeOutOfStockModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Credits Modal -->
    <div x-show="showOverdueModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeOverdueModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overdue Customer Credits</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Credit #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="credit in paginatedOverdueCredits" :key="credit.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="credit.customer.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="credit.credit_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="new Date(credit.due_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400" x-text="'LKR ' + parseFloat(credit.outstanding_amount).toFixed(2)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400" x-text="getDaysOverdue(credit.due_date) + ' days'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-semibold" x-text="((overdueCreditsPage - 1) * itemsPerPage) + 1"></span>
                        to <span class="font-semibold" x-text="Math.min(overdueCreditsPage * itemsPerPage, overdueCredits.length)"></span>
                        of <span class="font-semibold" x-text="overdueCredits.length"></span> credits
                    </div>
                    <div class="flex gap-2">
                        <button @click="overdueCreditsPage--"
                                :disabled="overdueCreditsPage === 1"
                                :class="overdueCreditsPage === 1 ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Previous
                        </button>

                        <template x-for="page in totalOverdueCreditsPages" :key="page">
                            <button @click="overdueCreditsPage = page"
                                    :class="overdueCreditsPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 rounded transition-colors duration-200"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="overdueCreditsPage++"
                                :disabled="overdueCreditsPage === totalOverdueCreditsPages"
                                :class="overdueCreditsPage === totalOverdueCreditsPages ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Next
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeOverdueModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Credits Modal -->
    <div x-show="showCustomerCreditsModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeCustomerCreditsModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Outstanding Customer Credits</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Credit Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Invoice Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Outstanding Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="credit in paginatedCustomerCredits" :key="credit.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="credit.customer.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="credit.credit_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="new Date(credit.invoice_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="new Date(credit.due_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600 dark:text-orange-400" x-text="'LKR ' + parseFloat(credit.outstanding_amount).toFixed(2)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': credit.status === 'pending',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': credit.status === 'partial',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': credit.status === 'overdue'
                                        }" class="px-2 py-1 rounded-full text-xs font-medium capitalize" x-text="credit.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-semibold" x-text="((customerCreditsPage - 1) * itemsPerPage) + 1"></span>
                        to <span class="font-semibold" x-text="Math.min(customerCreditsPage * itemsPerPage, customerCreditsData.length)"></span>
                        of <span class="font-semibold" x-text="customerCreditsData.length"></span> credits
                    </div>
                    <div class="flex gap-2">
                        <button @click="customerCreditsPage--"
                                :disabled="customerCreditsPage === 1"
                                :class="customerCreditsPage === 1 ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Previous
                        </button>

                        <template x-for="page in totalCustomerCreditsPages" :key="page">
                            <button @click="customerCreditsPage = page"
                                    :class="customerCreditsPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 rounded transition-colors duration-200"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="customerCreditsPage++"
                                :disabled="customerCreditsPage === totalCustomerCreditsPages"
                                :class="customerCreditsPage === totalCustomerCreditsPages ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Next
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeCustomerCreditsModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Credits Modal -->
    <div x-show="showSupplierCreditsModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="closeSupplierCreditsModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Outstanding Supplier Credits</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Supplier Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Credit Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Invoice Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Outstanding Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="credit in paginatedSupplierCredits" :key="credit.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="credit.supplier.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="credit.credit_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="new Date(credit.invoice_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="new Date(credit.due_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600 dark:text-orange-400" x-text="'LKR ' + parseFloat(credit.outstanding_amount).toFixed(2)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': credit.status === 'pending',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': credit.status === 'partial',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': credit.status === 'overdue'
                                        }" class="px-2 py-1 rounded-full text-xs font-medium capitalize" x-text="credit.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-semibold" x-text="((supplierCreditsPage - 1) * itemsPerPage) + 1"></span>
                        to <span class="font-semibold" x-text="Math.min(supplierCreditsPage * itemsPerPage, supplierCreditsData.length)"></span>
                        of <span class="font-semibold" x-text="supplierCreditsData.length"></span> credits
                    </div>
                    <div class="flex gap-2">
                        <button @click="supplierCreditsPage--"
                                :disabled="supplierCreditsPage === 1"
                                :class="supplierCreditsPage === 1 ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Previous
                        </button>

                        <template x-for="page in totalSupplierCreditsPages" :key="page">
                            <button @click="supplierCreditsPage = page"
                                    :class="supplierCreditsPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 rounded transition-colors duration-200"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="supplierCreditsPage++"
                                :disabled="supplierCreditsPage === totalSupplierCreditsPages"
                                :class="supplierCreditsPage === totalSupplierCreditsPages ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Next
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeSupplierCreditsModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Date Picker Modal -->
    <div x-show="showCustomDatePicker"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showCustomDatePicker = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Custom Date Range</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date"
                               x-model="customStartDate"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input type="date"
                               x-model="customEndDate"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                <div class="mt-6 flex gap-2 justify-end">
                    <button @click="showCustomDatePicker = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button @click="applyCustomDateRange()"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Batches Modal with Pagination -->
    <div x-show="showExpiringBatchesModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showExpiringBatchesModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300 delay-75"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expiring Batches (Next 90 Days)</h3>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expiry Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Brand</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Days Until Expiry</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="batch in paginatedBatches" :key="batch.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="batch.product.product_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="batch.batch_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="new Date(batch.expiry_date).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="batch.available_quantity"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="batch.product.category?.category_name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="batch.product.brand?.brand_name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold"
                                        :class="{
                                            'text-red-600 dark:text-red-400': getDaysUntilExpiry(batch.expiry_date) <= 30,
                                            'text-yellow-600 dark:text-yellow-400': getDaysUntilExpiry(batch.expiry_date) > 30 && getDaysUntilExpiry(batch.expiry_date) <= 60,
                                            'text-orange-600 dark:text-orange-400': getDaysUntilExpiry(batch.expiry_date) > 60
                                        }"
                                        x-text="getDaysUntilExpiry(batch.expiry_date) + ' days'">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span x-text="((expiringBatchesPage - 1) * itemsPerPage) + 1"></span>
                        to <span x-text="Math.min(expiringBatchesPage * itemsPerPage, expiringBatches.length)"></span>
                        of <span x-text="expiringBatches.length"></span> batches
                    </div>
                    <div class="flex gap-2">
                        <button @click="expiringBatchesPage--"
                                :disabled="expiringBatchesPage === 1"
                                :class="expiringBatchesPage === 1 ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Previous
                        </button>

                        <template x-for="page in totalPages" :key="page">
                            <button @click="expiringBatchesPage = page"
                                    :class="expiringBatchesPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 rounded transition-colors duration-200"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="expiringBatchesPage++"
                                :disabled="expiringBatchesPage === totalPages"
                                :class="expiringBatchesPage === totalPages ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded transition-colors duration-200">
                            Next
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeExpiringBatchesModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store Chart.js instances outside Alpine.js reactivity to avoid proxy issues
let topSellingChartInstance = null;
let profitChartInstance = null;

function adminDashboard() {
    return {
        loadingTopSelling: false,
        loadingProfit: false,
        loadingRefresh: false,
        topSellingPeriod: 'today',
        profitPeriod: 'daily',
        showOutOfStockModal: false,
        showOverdueModal: false,
        showCustomerCreditsModal: false,
        showSupplierCreditsModal: false,
        showCustomDatePicker: false,
        customStartDate: '',
        customEndDate: '',
        showExpiringBatchesModal: false,
        cardFlipped: false,
        outOfStockFlipped: false,
        overdueFlipped: false,
        customerCreditsFlipped: false,
        supplierCreditsFlipped: false,
        expiringBatchesPage: 1,
        outOfStockPage: 1,
        overdueCreditsPage: 1,
        customerCreditsPage: 1,
        supplierCreditsPage: 1,
        itemsPerPage: 10,
        expiringBatches: @json($expiringBatches['batches']),
        outOfStockItems: @json($outOfStock['products']),
        overdueCredits: @json($overdueCredits['credits']),
        customerCreditsData: @json($customerCredits['credits']),
        supplierCreditsData: @json($supplierCredits['credits']),

        // Time travel state
        timeTravelActive: @json($asOfDate !== null),
        timeTravelDate: @json($asOfDate),

        // Time travel loading state
        isTimeLoading: false,
        currentLoadingPhrase: '',
        loadingPhraseIndex: 0,
        loadingPhraseInterval: null,
        shouldShake: false,
        shouldShatter: false,

        // Phrases for time travel
        timeTravelPhrases: [
            "Hang on Morty we are going back in time!!!",
            "Vertexcore AI is working",
            "Hang on tight",
            "The details are falling into place; we're nearly there",
            "We are putting the finishing touches on this",
            "Almost there"
        ],

        // Phrases for returning to present
        returnPhrases: [
            "Morty, we are going BACK TO THE FUTURE",
            "Vertexcore AI is working",
            "Hang on tight",
            "The details are falling into place; we're nearly there",
            "We are putting the finishing touches on this",
            "Almost there"
        ],

        get paginatedBatches() {
            const start = (this.expiringBatchesPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.expiringBatches.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.expiringBatches.length / this.itemsPerPage);
        },

        get paginatedOutOfStock() {
            const start = (this.outOfStockPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.outOfStockItems.slice(start, end);
        },

        get totalOutOfStockPages() {
            return Math.ceil(this.outOfStockItems.length / this.itemsPerPage);
        },

        get paginatedOverdueCredits() {
            const start = (this.overdueCreditsPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.overdueCredits.slice(start, end);
        },

        get totalOverdueCreditsPages() {
            return Math.ceil(this.overdueCredits.length / this.itemsPerPage);
        },

        get paginatedCustomerCredits() {
            const start = (this.customerCreditsPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.customerCreditsData.slice(start, end);
        },

        get totalCustomerCreditsPages() {
            return Math.ceil(this.customerCreditsData.length / this.itemsPerPage);
        },

        get paginatedSupplierCredits() {
            const start = (this.supplierCreditsPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.supplierCreditsData.slice(start, end);
        },

        get totalSupplierCreditsPages() {
            return Math.ceil(this.supplierCreditsData.length / this.itemsPerPage);
        },

        init() {
            // Chart.js works reliably with canvas elements - no need for $nextTick
            this.initTopSellingChart();
            this.initProfitChart();
        },

        flipAndShowModal() {
            this.cardFlipped = true;
            setTimeout(() => {
                this.showExpiringBatchesModal = true;
            }, 350);
        },

        closeExpiringBatchesModal() {
            this.showExpiringBatchesModal = false;
            setTimeout(() => {
                this.cardFlipped = false;
                this.expiringBatchesPage = 1;
            }, 300);
        },

        flipAndShowOutOfStockModal() {
            this.outOfStockFlipped = true;
            setTimeout(() => {
                this.showOutOfStockModal = true;
            }, 350);
        },

        closeOutOfStockModal() {
            this.showOutOfStockModal = false;
            setTimeout(() => {
                this.outOfStockFlipped = false;
                this.outOfStockPage = 1;
            }, 300);
        },

        flipAndShowOverdueModal() {
            this.overdueFlipped = true;
            setTimeout(() => {
                this.showOverdueModal = true;
            }, 350);
        },

        closeOverdueModal() {
            this.showOverdueModal = false;
            setTimeout(() => {
                this.overdueFlipped = false;
                this.overdueCreditsPage = 1;
            }, 300);
        },

        flipAndShowCustomerCreditsModal() {
            this.customerCreditsFlipped = true;
            setTimeout(() => {
                this.showCustomerCreditsModal = true;
            }, 350);
        },

        closeCustomerCreditsModal() {
            this.showCustomerCreditsModal = false;
            setTimeout(() => {
                this.customerCreditsFlipped = false;
                this.customerCreditsPage = 1;
            }, 300);
        },

        flipAndShowSupplierCreditsModal() {
            this.supplierCreditsFlipped = true;
            setTimeout(() => {
                this.showSupplierCreditsModal = true;
            }, 350);
        },

        closeSupplierCreditsModal() {
            this.showSupplierCreditsModal = false;
            setTimeout(() => {
                this.supplierCreditsFlipped = false;
                this.supplierCreditsPage = 1;
            }, 300);
        },

        getDaysUntilExpiry(expiryDate) {
            const today = new Date();
            const expiry = new Date(expiryDate);
            const diffTime = expiry - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        },

        getDaysOverdue(dueDate) {
            const today = new Date();
            const due = new Date(dueDate);
            const diffTime = today - due;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        },

        initTopSellingChart() {
            // Destroy existing chart if it exists
            if (topSellingChartInstance) {
                topSellingChartInstance.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const data = @json($topSellingProducts);

            const ctx = document.getElementById('topSellingChart');

            topSellingChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.product_name),
                    datasets: [{
                        label: 'Quantity Sold',
                        data: data.map(item => item.total_quantity),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(209, 213, 219, 0.3)'
                            },
                            ticks: {
                                color: isDark ? '#9CA3AF' : '#4B5563'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: isDark ? '#9CA3AF' : '#4B5563'
                            }
                        }
                    }
                }
            });
        },

        initProfitChart() {
            // Destroy existing chart if it exists
            if (profitChartInstance) {
                profitChartInstance.destroy();
            }

            const isDark = document.documentElement.classList.contains('dark');
            const data = @json($profitData);

            const ctx = document.getElementById('profitChart');

            profitChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Gross Profit',
                            data: data.gross_profit,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Net Profit',
                            data: data.net_profit,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: isDark ? '#9CA3AF' : '#4B5563',
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'LKR ' + context.parsed.y.toFixed(2);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(209, 213, 219, 0.3)'
                            },
                            ticks: {
                                color: isDark ? '#9CA3AF' : '#4B5563'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(209, 213, 219, 0.3)'
                            },
                            ticks: {
                                color: isDark ? '#9CA3AF' : '#4B5563',
                                callback: function(value) {
                                    return 'LKR ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        },

        async updateTopSellingProducts(period) {
            this.topSellingPeriod = period;
            this.loadingTopSelling = true;

            try {
                const urlParams = new URLSearchParams(window.location.search);
                const asOfDate = urlParams.get('as_of_date');

                let url = `{{ url('/') }}/api/dashboard/top-selling-products?period=${period}`;
                if (asOfDate) {
                    url += `&as_of_date=${asOfDate}`;
                }

                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (topSellingChartInstance) {
                    topSellingChartInstance.data.labels = data.map(item => item.product_name);
                    topSellingChartInstance.data.datasets[0].data = data.map(item => item.total_quantity);
                    topSellingChartInstance.update();
                }
            } catch (error) {
                console.error('Error updating top selling products:', error);
            } finally {
                this.loadingTopSelling = false;
            }
        },

        async updateProfit(period) {
            this.profitPeriod = period;
            this.loadingProfit = true;

            try {
                const urlParams = new URLSearchParams(window.location.search);
                const asOfDate = urlParams.get('as_of_date');

                let url = `{{ url('/') }}/api/dashboard/profit-data?period=${period}`;

                if (period === 'custom' && this.customStartDate && this.customEndDate) {
                    url += `&start_date=${this.customStartDate}&end_date=${this.customEndDate}`;
                }

                if (asOfDate) {
                    url += `&as_of_date=${asOfDate}`;
                }

                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (profitChartInstance) {
                    profitChartInstance.data.labels = data.labels;
                    profitChartInstance.data.datasets[0].data = data.gross_profit;
                    profitChartInstance.data.datasets[1].data = data.net_profit;
                    profitChartInstance.update();
                }
            } catch (error) {
                console.error('Error updating profit chart:', error);
            } finally {
                this.loadingProfit = false;
            }
        },

        applyCustomDateRange() {
            if (!this.customStartDate || !this.customEndDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(this.customStartDate) > new Date(this.customEndDate)) {
                alert('Start date must be before end date');
                return;
            }

            this.showCustomDatePicker = false;
            this.updateProfit('custom');
        },

        async refreshData() {
            this.loadingRefresh = true;
            try {
                await fetch('{{ url('/') }}/api/dashboard/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                location.reload();
            } catch (error) {
                console.error('Error refreshing data:', error);
            } finally {
                this.loadingRefresh = false;
            }
        },

        // Start loading with phrase cycling
        startLoading(phrases) {
            this.isTimeLoading = true;
            this.loadingPhraseIndex = 0;
            this.currentLoadingPhrase = phrases[0];
            this.shouldShake = false;
            this.shouldShatter = false;

            // Cycle through phrases every 3 seconds
            this.loadingPhraseInterval = setInterval(() => {
                this.loadingPhraseIndex = (this.loadingPhraseIndex + 1) % phrases.length;
                this.currentLoadingPhrase = phrases[this.loadingPhraseIndex];
            }, 3000);
        },

        // Stop loading and clear interval
        stopLoading() {
            this.isTimeLoading = false;
            if (this.loadingPhraseInterval) {
                clearInterval(this.loadingPhraseInterval);
                this.loadingPhraseInterval = null;
            }
        },

        enableTimeTravel() {
            if (!this.timeTravelDate) return;

            // Start loading animation
            this.startLoading(this.timeTravelPhrases);

            this.timeTravelActive = true;

            // Navigate to the new page
            const url = new URL(window.location);
            url.searchParams.set('as_of_date', this.timeTravelDate);

            // Trigger shake and shatter before navigation
            setTimeout(() => {
                this.shouldShake = true;
            }, 500);

            setTimeout(() => {
                this.shouldShake = false;
                this.shouldShatter = true;
            }, 1300);

            // Hide loading screen after shatter animation
            setTimeout(() => {
                this.isTimeLoading = false;
            }, 2300);

            // Navigate after shatter completes
            setTimeout(() => {
                window.location.href = url.toString();
            }, 2500);
        },

        disableTimeTravel() {
            // Start loading animation
            this.startLoading(this.returnPhrases);

            this.timeTravelActive = false;
            this.timeTravelDate = null;

            // Navigate to the new page
            const url = new URL(window.location);
            url.searchParams.delete('as_of_date');

            // Trigger shake and shatter before navigation
            setTimeout(() => {
                this.shouldShake = true;
            }, 500);

            setTimeout(() => {
                this.shouldShake = false;
                this.shouldShatter = true;
            }, 1300);

            // Hide loading screen after shatter animation
            setTimeout(() => {
                this.isTimeLoading = false;
            }, 2300);

            // Navigate after shatter completes
            setTimeout(() => {
                window.location.href = url.toString();
            }, 2500);
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.rotate-y-180 {
    transform: rotateY(180deg);
}

/* SVG Clock Animation */
.clock-container {
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clock-svg {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0 0 10px rgba(124, 58, 237, 0.5));
}

/* Phrase fade animation */
@keyframes phraseChange {
    0%, 100% { opacity: 0; transform: translateY(10px); }
    10%, 90% { opacity: 1; transform: translateY(0); }
}

.phrase-change {
    animation: phraseChange 3s ease-in-out;
}

/* Clock shake animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
    20%, 40%, 60%, 80% { transform: translateX(10px); }
}

.clock-shake {
    animation: shake 0.5s ease-in-out infinite;
}

/* Clock shatter animation */
@keyframes shatter {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    25% {
        transform: scale(1.1);
    }
    50% {
        transform: scale(1.15) rotate(5deg);
    }
    75% {
        opacity: 0.7;
        transform: scale(1.2) rotate(-5deg);
        filter: blur(2px);
    }
    100% {
        opacity: 0;
        transform: scale(1.5) rotate(10deg);
        filter: blur(5px);
    }
}

.clock-shatter {
    animation: shatter 1s ease-out forwards;
}

/* Fade out text when clock shatters */
.fade-out-text {
    animation: fadeOutQuick 0.3s ease-out forwards;
}

@keyframes fadeOutQuick {
    0% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}
</style>
@endsection
