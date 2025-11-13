<div id="sidebar"
    class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white dark:bg-gray-900 shadow-xl overflow-y-auto border-r border-gray-100 dark:border-gray-800">
    <div class="flex h-full flex-col">


        <div class="flex flex-1 flex-col">
            <nav class="flex-1 px-4 py-6 space-y-8">
                <!-- Dashboard Section -->
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Dashboard
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('cashier.dashboard') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-cash-register  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Cashier
                        </a>
                    </div>
                </div>

                <!-- Products Section -->
                @canany(['view products', 'view categories', 'view brands'])
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Products
                    </h3>
                    <div class="space-y-1">
                        @can('view products')
                        <a href="{{ route('products.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-cube  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            All Products
                        </a>
                        @endcan
                        @can('view categories')
                        <a href="{{ route('categories.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-tags  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Categories
                        </a>
                        @endcan
                        @can('view brands')
                        <a href="{{ route('brands.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-copyright  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Brands
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Inventory Management Section -->
                @canany(['view suppliers', 'view good receive notes', 'view batches', 'view stocks', 'view vendor codes', 'view stock in'])
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Inventory Management
                    </h3>
                    <div class="space-y-1">
                        @can('view suppliers')
                        <a href="{{ route('suppliers.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-truck  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Suppliers
                        </a>
                        @endcan
                        @can('view good receive notes')
                        <a href="{{ route('good-receive-notes.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-file-invoice  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Good Receive Notes
                        </a>
                        @endcan
                        @can('view batches')
                        <a href="{{ route('batches.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-layer-group  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Batches
                        </a>
                        @endcan
                        @can('view stocks')
                        <a href="{{ route('stocks.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-boxes  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Stocks
                        </a>
                        @endcan
                        @can('view vendor codes')
                        <a href="{{ route('vendor-codes.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-barcode  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Vendor Codes
                        </a>
                        @endcan
                        @can('view stock in')
                        <a href="{{ route('stock-in.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-arrow-down  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Stock In
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Sales & Transactions Section -->
                @canany(['view sales', 'view sales returns', 'view supplier returns', 'view expenses', 'manage own shifts'])
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Sales & Transactions
                    </h3>
                    <div class="space-y-1">
                        @can('view sales')
                        <a href="{{ route('sales.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-chart-line  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Sales History
                        </a>
                        @endcan
                        @can('manage own shifts')
                        <a href="{{ route('shifts.my-shifts') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-user-clock  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            My Shifts
                        </a>
                        @endcan
                        @can('view sales returns')
                        <a href="{{ route('sales-returns.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-undo  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Sales Returns
                        </a>
                        @endcan
                        @can('view supplier returns')
                        <a href="{{ route('supplier-returns.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-truck-arrow-right  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Supplier Returns
                        </a>
                        @endcan
                        @can('view expenses')
                        <a href="{{ route('expenses.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-receipt  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Expenses
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Reports & Analytics Section -->
                @canany(['view reports', 'view shifts'])
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Reports & Analytics
                    </h3>
                    <div class="space-y-1">
                        @can('view reports')
                        <a href="{{ route('reports.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-chart-bar  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Analytics
                        </a>
                        @endcan
                        @can('view shifts')
                        <a href="{{ route('shifts.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-clock  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            All Shifts
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Admin Section -->
                @canany(['view users', 'view roles', 'view permissions'])
                <div class="space-y-3">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Administration
                    </h3>
                    <div class="space-y-1">
                        @can('view users')
                        <a href="{{ route('users.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-users  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Users
                        </a>
                        @endcan
                        @canany(['view roles', 'view permissions'])
                        <a href="{{ route('roles-permissions.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-user-shield  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Roles & Permissions
                        </a>
                        @endcanany
                    </div>
                </div>
                @endcanany
            </nav>

            <!-- Footer Section -->
            <div class="border-t border-gray-100 dark:border-gray-800 p-4">
                <div class="text-xs text-gray-400 dark:text-gray-500 text-center">
                    Terminal POS v1.0
                </div>
            </div>
        </div>
    </div>
</div>
