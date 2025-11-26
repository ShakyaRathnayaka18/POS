<div id="sidebar"
    class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white dark:bg-gray-900 shadow-xl overflow-y-auto border-r border-gray-100 dark:border-gray-800 sidebar-scroll">
    <div class="flex h-full flex-col">


        <div class="flex flex-1 flex-col">
            <nav class="flex-1 px-4 py-6 space-y-2" x-data="{ openSections: ['dashboard', 'products-inventory', 'sales-operations', 'finance-accounting', 'hr', 'admin'] }">
                <!-- Dashboard Section -->
                <div class="space-y-1">
                    <button @click="openSections.includes('dashboard') ? openSections = openSections.filter(s => s !== 'dashboard') : openSections.push('dashboard')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Dashboard</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('dashboard') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('dashboard')" x-collapse class="space-y-1">
                        @can('view dashboard')
                        <a href="{{ route('dashboard.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out {{ request()->routeIs('dashboard.index') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <i class="fas fa-chart-line mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Admin Dashboard
                        </a>
                        @endcan
                        <a href="{{ route('cashier.dashboard') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-cash-register  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Cashier
                        </a>
                    </div>
                </div>

                <!-- Products & Inventory Section -->
                @canany(['view products', 'view categories', 'view brands', 'view suppliers', 'view grns', 'view batches', 'view stocks', 'view vendor codes', 'view stock in'])
                <div class="space-y-1">
                    <button @click="openSections.includes('products-inventory') ? openSections = openSections.filter(s => s !== 'products-inventory') : openSections.push('products-inventory')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Products & Inventory</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('products-inventory') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('products-inventory')" x-collapse class="space-y-1">
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
                        @can('view suppliers')
                        <a href="{{ route('suppliers.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-truck  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Suppliers
                        </a>
                        @endcan
                        @can('view grns')
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

                <!-- Sales & Operations Section -->
                @canany(['view sales', 'view sales returns', 'view supplier returns', 'view expenses', 'manage own shifts', 'view reports', 'view shifts'])
                <div class="space-y-1">
                    <button @click="openSections.includes('sales-operations') ? openSections = openSections.filter(s => s !== 'sales-operations') : openSections.push('sales-operations')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Sales & Operations</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('sales-operations') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('sales-operations')" x-collapse class="space-y-1">
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

                <!-- Human Resources Section -->
                @canany(['view employees', 'view payroll', 'view own payroll'])
                <div class="space-y-1">
                    <button @click="openSections.includes('hr') ? openSections = openSections.filter(s => s !== 'hr') : openSections.push('hr')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Payroll</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('hr') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('hr')" x-collapse class="space-y-1">
                        @can('view employees')
                        <a href="{{ route('employees.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-user-tie  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Employees
                        </a>
                        @endcan
                        @can('view payroll')
                        <a href="{{ route('payroll.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-money-check-alt  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Payroll
                        </a>
                        @endcan
                        @can('view payroll reports')
                        <a href="{{ route('payroll.reports') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-file-invoice-dollar  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Payroll Reports
                        </a>
                        @endcan
                        @can('view own payroll')
                        <a href="{{ route('payroll.my-payroll') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-wallet  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            My Payroll
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Finance & Accounting Section -->
                @canany(['view supplier credits', 'view supplier payments', 'view customers', 'view chart of accounts', 'view journal entries', 'view income statement', 'view balance sheet', 'view trial balance', 'view general ledger'])
                <div class="space-y-1">
                    <button @click="openSections.includes('finance-accounting') ? openSections = openSections.filter(s => s !== 'finance-accounting') : openSections.push('finance-accounting')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Finance & Accounting</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('finance-accounting') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('finance-accounting')" x-collapse class="space-y-1">
                        @can('view supplier credits')
                        <a href="{{ route('supplier-credits.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-file-invoice-dollar  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Supplier Credits
                        </a>
                        @endcan
                        @can('view supplier payments')
                        <a href="{{ route('supplier-payments.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-money-bill-wave  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Supplier Payments
                        </a>
                        @endcan
                        @can('view customers')
                        <a href="{{ route('customers.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-users  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Customers
                        </a>
                        @endcan
                        @can('view chart of accounts')
                        <a href="{{ route('accounts.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-list-alt  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Chart of Accounts
                        </a>
                        @endcan
                        @can('view journal entries')
                        <a href="{{ route('journal-entries.index') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-book  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Journal Entries
                        </a>
                        @endcan
                        @can('view income statement')
                        <a href="{{ route('reports.income-statement') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-chart-line  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Income Statement
                        </a>
                        @endcan
                        @can('view balance sheet')
                        <a href="{{ route('reports.balance-sheet') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-balance-scale  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Balance Sheet
                        </a>
                        @endcan
                        @can('view trial balance')
                        <a href="{{ route('reports.trial-balance') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-calculator  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            Trial Balance
                        </a>
                        @endcan
                        @can('view general ledger')
                        <a href="{{ route('reports.general-ledger') }}"
                            class="group flex items-center px-3 py-3 text-base font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all duration-200 ease-in-out">
                            <i class="fas fa-file-alt  mr-4 h-4 w-4 mt-2 mb-1" style="color: #4ea9dd;"></i>
                            General Ledger
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Admin Section -->
                @canany(['view users', 'view roles', 'view permissions'])
                <div class="space-y-1">
                    <button @click="openSections.includes('admin') ? openSections = openSections.filter(s => s !== 'admin') : openSections.push('admin')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Administration</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openSections.includes('admin') ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openSections.includes('admin')" x-collapse class="space-y-1">
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
                    VPOS v1.0
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
