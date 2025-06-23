<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex h-full flex-col overflow-y-auto border-r border-gray-200 dark:border-gray-700 pt-16 lg:pt-0">
        <div class="flex flex-1 flex-col">
            <nav class="flex-1 space-y-1 px-2 py-4">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dashboard</h3>
                    <a href="{{ route('cashier.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-cash-register mr-3 h-6 w-6"></i>
                        Cashier
                    </a>
                    <a href="{{ route('inventory.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-boxes mr-3 h-6 w-6"></i>
                        Inventory
                    </a>
                </div>

                <!-- Products -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Products</h3>
                    <a href="{{ route('products.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-cube mr-3 h-6 w-6"></i>
                        All Products
                    </a>
                    <a href="{{ route('categories.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-tags mr-3 h-6 w-6"></i>
                        Categories
                    </a>
                    <a href="{{ route('brands.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-copyright mr-3 h-6 w-6"></i>
                        Brands
                    </a>
                </div>

                <!-- Inventory -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inventory</h3>
                    <a href="{{ route('suppliers.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-truck mr-3 h-6 w-6"></i>
                        Suppliers
                    </a>
                    <a href="{{ route('purchase-orders.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-file-invoice mr-3 h-6 w-6"></i>
                        Purchase Orders
                    </a>
                    <a href="{{ route('stock-in.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-arrow-down mr-3 h-6 w-6"></i>
                        Stock In
                    </a>
                </div>

                <!-- Sales -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</h3>
                    <a href="{{ route('sales.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-chart-line mr-3 h-6 w-6"></i>
                        Sales History
                    </a>
                    <a href="{{ route('returns.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-undo mr-3 h-6 w-6"></i>
                        Returns
                    </a>
                    <a href="{{ route('expenses.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-receipt mr-3 h-6 w-6"></i>
                        Expenses
                    </a>
                </div>

                <!-- Reports -->
                <div class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reports</h3>
                    <a href="{{ route('reports.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-chart-bar mr-3 h-6 w-6"></i>
                        Analytics
                    </a>
                </div>
            </nav>
        </div>
    </div>
</div>
