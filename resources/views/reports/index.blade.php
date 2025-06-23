@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
        <div class="flex space-x-2">
            <input type="date" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            <input type="date" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            <button class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">Apply Filter</button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">$45,230</dd>
                            <dd class="text-sm text-green-600">+12.5% from last month</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Gross Profit</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">$18,920</dd>
                            <dd class="text-sm text-green-600">+8.2% from last month</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">1,247</dd>
                            <dd class="text-sm text-green-600">+15.3% from last month</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Customers</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">892</dd>
                            <dd class="text-sm text-green-600">+5.7% from last month</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Sales Trend</h3>
            <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400">Sales Chart Placeholder</p>
                    <p class="text-sm text-gray-400">Chart.js or similar library integration</p>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Top Selling Products</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-lg object-cover" src="https://via.placeholder.com/40" alt="">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Coca Cola 500ml</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">245 sold</div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">$612.50</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-lg object-cover" src="https://via.placeholder.com/40" alt="">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">White Bread</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">189 sold</div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">$236.25</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-lg object-cover" src="https://via.placeholder.com/40" alt="">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Milk 1L</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">156 sold</div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">$468.00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Performance -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Category Performance</h3>
            <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400">Pie Chart Placeholder</p>
                    <p class="text-sm text-gray-400">Category breakdown visualization</p>
                </div>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Inventory Status</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">In Stock</span>
                        <span class="font-medium text-gray-900 dark:text-white">1,219 items</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Low Stock</span>
                        <span class="font-medium text-gray-900 dark:text-white">23 items</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 12%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Out of Stock</span>
                        <span class="font-medium text-gray-900 dark:text-white">5 items</span>
                    </div>
                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 3%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Generate Reports</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Sales Report PDF</span>
            </button>
            <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-file-excel text-green-500 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Inventory Excel</span>
            </button>
            <button class="flex items-center justify-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Analytics Dashboard</span>
            </button>
        </div>
    </div>
</div>
@endsection
