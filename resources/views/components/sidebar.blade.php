<div id="sidebar" x-data="{
        openSections: ['dashboard', 'products-inventory', 'sales-operations', 'finance-accounting', 'hr', 'admin'],
        menuSearch: '',
        matchesSearch(text) {
            return !this.menuSearch || text.toLowerCase().includes(this.menuSearch.toLowerCase())
        },
        hasMatchInSection(items) {
            return !this.menuSearch || items.some(item => this.matchesSearch(item))
        }
    }"
    class="fixed top-16 left-0 h-[calc(100vh-4rem)] bg-white dark:bg-gray-900 shadow-xl overflow-y-auto border-r border-gray-100 dark:border-gray-800 sidebar-scroll transition-all duration-300 ease-in-out"
    :class="sidebarCollapsed ? 'w-20' : 'w-64'">
    <div class="flex h-full flex-col">

        <!-- Search Bar -->
        <div class="flex items-center p-2 border-b gap-2" :class="sidebarCollapsed ? 'justify-center' : ''">
            <div class="relative flex-1" x-show="!sidebarCollapsed">

                <input type="text" x-model="menuSearch" placeholder="Search menu..."
                    class="w-full pl-9 pr-1 py-2 dark:bg-gray-900 dark:text-white placeholder-gray-400">
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed; if(sidebarCollapsed) { openSections = []; menuSearch = ''; }" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none flex-shrink-0">
                <i class="fas" :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
            </button>
        </div>

        <div class="flex flex-1 flex-col">
            <nav class="flex-1 px-2 py-4 space-y-2">
                <!-- Dashboard Section -->
                <div class="space-y-1" x-show="hasMatchInSection(['Dashboard', 'Admin Dashboard', 'Cashier'])">
                    <button @click="openSections.includes('dashboard') ? openSections = openSections.filter(s => s !== 'dashboard') : openSections.push('dashboard')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Dashboard">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-tachometer-alt w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Dashboard</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('dashboard') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('dashboard') || menuSearch" x-collapse class="space-y-1">
                        @can('view dashboard')
                        <a href="{{ route('dashboard.index') }}"
                            x-show="matchesSearch('Admin Dashboard')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out {{ request()->routeIs('dashboard.index') ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Admin Dashboard">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Admin Dashboard</span>
                        </a>
                        @endcan
                        <a href="{{ route('cashier.dashboard') }}"
                            x-show="matchesSearch('Cashier')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Cashier">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Cashier</span>
                        </a>
                    </div>
                </div>

                <!-- Products & Inventory Section -->
                @canany(['view products', 'view categories', 'view brands', 'view suppliers', 'view grns', 'view batches', 'view stocks', 'view vendor codes', 'view stock in', 'manage stock adjustments'])
                <div class="space-y-1" x-show="hasMatchInSection(['Products', 'Inventory', 'All Products', 'Categories', 'Brands', 'Suppliers', 'Good Receive Notes', 'GRN', 'Batches', 'Stocks', 'Stock Adjustments', 'Adjustments', 'Vendor Codes', 'Stock In'])">
                    <button @click="openSections.includes('products-inventory') ? openSections = openSections.filter(s => s !== 'products-inventory') : openSections.push('products-inventory')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Products & Inventory">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-box-open w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Products & Inventory</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('products-inventory') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('products-inventory') || menuSearch" x-collapse class="space-y-1">
                        @can('view products')
                        <a href="{{ route('products.index') }}"
                            x-show="matchesSearch('All Products')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="All Products">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">All Products</span>
                        </a>
                        @endcan
                        @can('view categories')
                        <a href="{{ route('categories.index') }}"
                            x-show="matchesSearch('Categories')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Categories">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Categories</span>
                        </a>
                        @endcan
                        @can('view brands')
                        <a href="{{ route('brands.index') }}"
                            x-show="matchesSearch('Brands')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Brands">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Brands</span>
                        </a>
                        @endcan
                        @can('view suppliers')
                        <a href="{{ route('suppliers.index') }}"
                            x-show="matchesSearch('Suppliers')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Suppliers">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Suppliers</span>
                        </a>
                        @endcan
                        @can('view grns')
                        <a href="{{ route('good-receive-notes.index') }}"
                            x-show="matchesSearch('Good Receive Notes') || matchesSearch('GRN')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Good Receive Notes">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Good Receive Notes</span>
                        </a>
                        @endcan
                        @can('view batches')
                        <a href="{{ route('batches.index') }}"
                            x-show="matchesSearch('Batches')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Batches">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Batches</span>
                        </a>
                        @endcan
                        @can('view stocks')
                        <a href="{{ route('stocks.index') }}"
                            x-show="matchesSearch('Stocks')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Stocks">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Stocks</span>
                        </a>
                        @endcan
                        @can('manage stock adjustments')
                        <a href="{{ route('stock-adjustments.index') }}"
                            x-show="matchesSearch('Stock Adjustments') || matchesSearch('Adjustments')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out {{ request()->routeIs('stock-adjustments.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Stock Adjustments">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Stock Adjustments</span>
                        </a>
                        @endcan
                        @can('view vendor codes')
                        <a href="{{ route('vendor-codes.index') }}"
                            x-show="matchesSearch('Vendor Codes')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Vendor Codes">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Vendor Codes</span>
                        </a>
                        @endcan
                        @can('view stock in')
                        <a href="{{ route('stock-in.index') }}"
                            x-show="matchesSearch('Stock In')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Stock In">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Stock In</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Sales & Operations Section -->
                @canany(['view sales', 'view sales returns', 'view supplier returns', 'view expenses', 'manage own shifts', 'view reports', 'view shifts', 'view manual sales reconciliation'])
                <div class="space-y-1" x-show="hasMatchInSection(['Sales', 'Operations', 'Sales History', 'Manual Sales', 'Manual Sales History', 'Reconciliation', 'My Shifts', 'Sales Returns', 'Supplier Returns', 'Expenses', 'Analytics', 'Reports', 'All Shifts'])">
                    <button @click="openSections.includes('sales-operations') ? openSections = openSections.filter(s => s !== 'sales-operations') : openSections.push('sales-operations')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Sales & Operations">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-shopping-cart w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Sales & Operations</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('sales-operations') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('sales-operations') || menuSearch" x-collapse class="space-y-1">
                        @can('view sales')
                        <a href="{{ route('sales.index') }}"
                            x-show="matchesSearch('Sales History')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Sales History">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Sales History</span>
                        </a>
                        @endcan
                        @can('view sales')
                        <a href="{{ route('manual-sales.index') }}"
                            x-show="matchesSearch('Manual Sales') || matchesSearch('Manual Sales History')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out {{ request()->routeIs('manual-sales.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Manual Sales History">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Manual Sales History</span>
                        </a>
                        @endcan
                        @can('view manual sales reconciliation')
                        <a href="{{ route('manual-sales.reconciliation.index') }}"
                            x-show="matchesSearch('Reconciliation') || matchesSearch('Manual Sales Reconciliation')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out {{ request()->routeIs('manual-sales.reconciliation.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Manual Sales Reconciliation">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Reconciliation</span>
                        </a>
                        @endcan
                        @can('manage own shifts')
                        <a href="{{ route('shifts.my-shifts') }}"
                            x-show="matchesSearch('My Shifts')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="My Shifts">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">My Shifts</span>
                        </a>
                        @endcan
                        @can('view sales returns')
                        <a href="{{ route('sales-returns.index') }}"
                            x-show="matchesSearch('Sales Returns')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Sales Returns">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Sales Returns</span>
                        </a>
                        @endcan
                        @can('view supplier returns')
                        <a href="{{ route('supplier-returns.index') }}"
                            x-show="matchesSearch('Supplier Returns')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Supplier Returns">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Supplier Returns</span>
                        </a>
                        @endcan
                        @can('view expenses')
                        <a href="{{ route('expenses.index') }}"
                            x-show="matchesSearch('Expenses')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Expenses">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Expenses</span>
                        </a>
                        @endcan
                        @can('view reports')
                        <a href="{{ route('reports.index') }}"
                            x-show="matchesSearch('Analytics') || matchesSearch('Reports')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Analytics">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Analytics</span>
                        </a>
                        @endcan
                        @can('view shifts')
                        <a href="{{ route('shifts.index') }}"
                            x-show="matchesSearch('All Shifts')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="All Shifts">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">All Shifts</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Human Resources Section -->
                @canany(['view employees', 'view payroll', 'view own payroll'])
                <div class="space-y-1" x-show="hasMatchInSection(['Payroll', 'HR', 'Human Resources', 'Employees', 'Payroll Reports', 'My Payroll'])">
                    <button @click="openSections.includes('hr') ? openSections = openSections.filter(s => s !== 'hr') : openSections.push('hr')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Payroll">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-users-cog w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Payroll</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('hr') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('hr') || menuSearch" x-collapse class="space-y-1">
                        @can('view employees')
                        <a href="{{ route('employees.index') }}"
                            x-show="matchesSearch('Employees')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Employees">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Employees</span>
                        </a>
                        @endcan
                        @can('view payroll')
                        <a href="{{ route('payroll.index') }}"
                            x-show="matchesSearch('Payroll')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Payroll">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Payroll</span>
                        </a>
                        @endcan
                        @can('view payroll reports')
                        <a href="{{ route('payroll.reports') }}"
                            x-show="matchesSearch('Payroll Reports')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Payroll Reports">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Payroll Reports</span>
                        </a>
                        @endcan
                        @can('view own payroll')
                        <a href="{{ route('payroll.my-payroll') }}"
                            x-show="matchesSearch('My Payroll')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="My Payroll">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">My Payroll</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Finance & Accounting Section -->
                @canany(['view supplier credits', 'view supplier payments', 'view customers', 'view chart of accounts', 'view journal entries', 'view income statement', 'view balance sheet', 'view trial balance', 'view general ledger'])
                <div class="space-y-1" x-show="hasMatchInSection(['Finance', 'Accounting', 'Supplier Credits', 'Supplier Payments', 'Customers', 'Chart of Accounts', 'Journal Entries', 'Income Statement', 'Balance Sheet', 'Trial Balance', 'General Ledger'])">
                    <button @click="openSections.includes('finance-accounting') ? openSections = openSections.filter(s => s !== 'finance-accounting') : openSections.push('finance-accounting')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Finance & Accounting">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-coins w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Finance & Accounting</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('finance-accounting') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('finance-accounting') || menuSearch" x-collapse class="space-y-1">
                        @can('view supplier credits')
                        <a href="{{ route('supplier-credits.index') }}"
                            x-show="matchesSearch('Supplier Credits')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Supplier Credits">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Supplier Credits</span>
                        </a>
                        @endcan
                        @can('view supplier payments')
                        <a href="{{ route('supplier-payments.index') }}"
                            x-show="matchesSearch('Supplier Payments')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Supplier Payments">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Supplier Payments</span>
                        </a>
                        @endcan
                        @can('view customers')
                        <a href="{{ route('customers.index') }}"
                            x-show="matchesSearch('Customers')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Customers">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Customers</span>
                        </a>
                        @endcan
                        @can('view chart of accounts')
                        <a href="{{ route('accounts.index') }}"
                            x-show="matchesSearch('Chart of Accounts') || matchesSearch('Accounts')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Chart of Accounts">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Chart of Accounts</span>
                        </a>
                        @endcan
                        @can('view journal entries')
                        <a href="{{ route('journal-entries.index') }}"
                            x-show="matchesSearch('Journal Entries') || matchesSearch('Journal')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Journal Entries">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Journal Entries</span>
                        </a>
                        @endcan
                        @can('view income statement')
                        <a href="{{ route('reports.income-statement') }}"
                            x-show="matchesSearch('Income Statement') || matchesSearch('Income')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Income Statement">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Income Statement</span>
                        </a>
                        @endcan
                        @can('view balance sheet')
                        <a href="{{ route('reports.balance-sheet') }}"
                            x-show="matchesSearch('Balance Sheet') || matchesSearch('Balance')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Balance Sheet">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Balance Sheet</span>
                        </a>
                        @endcan
                        @can('view trial balance')
                        <a href="{{ route('reports.trial-balance') }}"
                            x-show="matchesSearch('Trial Balance') || matchesSearch('Trial')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Trial Balance">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Trial Balance</span>
                        </a>
                        @endcan
                        @can('view general ledger')
                        <a href="{{ route('reports.general-ledger') }}"
                            x-show="matchesSearch('General Ledger') || matchesSearch('Ledger')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="General Ledger">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">General Ledger</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Admin Section -->
                @canany(['view users', 'view roles', 'view permissions'])
                <div class="space-y-1" x-show="hasMatchInSection(['Administration', 'Admin', 'Users', 'Roles', 'Permissions', 'Roles & Permissions'])">
                    <button @click="openSections.includes('admin') ? openSections = openSections.filter(s => s !== 'admin') : openSections.push('admin')"
                            class="w-full flex items-center justify-between px-2 py-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors group"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Administration">
                        <div class="flex items-center min-w-0">
                            <i class="fas fa-shield-alt w-8 text-center text-2xl transition-all" :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" style="color: #4ea9dd;"></i>
                            <span x-show="!sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wider truncate">Administration</span>
                        </div>
                        <i x-show="!sidebarCollapsed" class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('admin') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('admin') || menuSearch" x-collapse class="space-y-1">
                        @can('view users')
                        <a href="{{ route('users.index') }}"
                            x-show="matchesSearch('Users')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Users">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Users</span>
                        </a>
                        @endcan
                        @canany(['view roles', 'view permissions'])
                        <a href="{{ route('roles-permissions.index') }}"
                            x-show="matchesSearch('Roles & Permissions') || matchesSearch('Roles') || matchesSearch('Permissions')"
                            class="group flex items-center px-2 py-2 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out"
                            :class="sidebarCollapsed ? 'justify-center' : ''"
                            title="Roles & Permissions">

                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Roles & Permissions</span>
                        </a>
                        @endcanany
                    </div>
                </div>
                @endcanany
            </nav>

            <!-- Footer Section -->
            <div class="border-t border-gray-100 dark:border-gray-800 p-4">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/VPOS.png') }}" alt="VPOS" class="h-8 w-auto mb-2 transition-all duration-300" x-show="!sidebarCollapsed">
                    <!-- <div class="text-xs text-gray-400 dark:text-gray-500 text-center">
                        VPOS v1.0
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Sidebar scrollbar styling */
.sidebar-scroll::-webkit-scrollbar {
    width: 8px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode scrollbar */
.dark .sidebar-scroll::-webkit-scrollbar-track {
    background: #1f2937;
}

.dark .sidebar-scroll::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.dark .sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Firefox scrollbar */
.sidebar-scroll {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f1f1;
}

.dark .sidebar-scroll {
    scrollbar-color: #4b5563 #1f2937;
}
</style>
