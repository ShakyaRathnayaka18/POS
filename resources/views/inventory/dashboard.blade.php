@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Products</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">1,247</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Low Stock</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">23</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Out of Stock</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">5</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Value</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">$45,230</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('stock-in.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-down text-green-600 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Stock In</span>
            </a>
            <a href="{{ route('products.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-plus text-blue-600 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Add Product</span>
            </a>
            <a href="{{ route('good-receive-notes.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-file-invoice text-purple-600 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">New GRN</span>
            </a>
            <a href="{{ route('suppliers.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-truck text-orange-600 mr-3"></i>
                <span class="font-medium text-gray-900 dark:text-white">Add Supplier</span>
            </a>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Low Stock Alerts</h2>
            <a href="{{ route('products.index') }}?filter=low_stock" class="text-primary-600 hover:text-primary-500 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Min Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Coca Cola 500ml</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">SKU: CC500</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">8</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">20</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900">Reorder</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">White Bread</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">SKU: WB001</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">0</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">15</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Out of Stock</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900">Reorder</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Recent Stock Movements</h2>
            <a href="{{ route('stock-in.index') }}" class="text-primary-600 hover:text-primary-500 text-sm font-medium">View All</a>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Stock In - Coca Cola 500ml</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">+50 units • 2 hours ago</div>
                    </div>
                </div>
                <span class="text-sm text-green-600 font-medium">+50</span>
            </div>
            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Sale - White Bread</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">-3 units • 4 hours ago</div>
                    </div>
                </div>
                <span class="text-sm text-red-600 font-medium">-3</span>
            </div>
        </div>
    </div>
</div>
@endsection
