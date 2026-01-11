<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\ManualSaleController;
use App\Http\Controllers\ManualSaleReconciliationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SavedCartController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorCodeController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'head'], '/', function () {
    if (auth()->check()) {
        // Redirect to admin dashboard if user has permission, otherwise cashier
        if (auth()->user()->can('view dashboard')) {
            return redirect()->route('dashboard.index');
        }

        return redirect()->to('/cashier');
    }

    return redirect()->route('login');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Admin Dashboard Routes
    Route::middleware(['permission:view dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // API endpoints for AJAX updates
        Route::get('/api/dashboard/top-selling-products', [DashboardController::class, 'topSellingProducts'])
            ->name('dashboard.top-selling-products');
        Route::get('/api/dashboard/profit-data', [DashboardController::class, 'profitData'])
            ->name('dashboard.profit-data');
        Route::post('/api/dashboard/clear-cache', [DashboardController::class, 'clearCache'])
            ->name('dashboard.clear-cache');
    });

    // Cashier Dashboard - requires create sales permission
    Route::middleware(['permission:create sales'])->group(function () {
        Route::get('/cashier', function () {
            return view('cashier.dashboard');
        })->name('cashier.dashboard');
    });

    // Shift Management - cashier routes
    Route::middleware(['permission:manage own shifts'])->group(function () {
        Route::post('/shifts/clock-in', [ShiftController::class, 'clockIn'])->name('shifts.clock-in');
        Route::post('/shifts/{shift}/clock-out', [ShiftController::class, 'clockOut'])->name('shifts.clock-out');
        Route::get('/shifts/current', [ShiftController::class, 'current'])->name('shifts.current');
        Route::get('/my-shifts', [ShiftController::class, 'userShifts'])->name('shifts.my-shifts');
    });

    // Shift Management - manager/admin routes
    Route::middleware(['permission:view shifts'])->group(function () {
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    });

    Route::middleware(['permission:approve shifts'])->group(function () {
        Route::post('/shifts/{shift}/approve', [ShiftController::class, 'approve'])->name('shifts.approve');
    });

    // Products Management - requires appropriate permissions
    Route::middleware(['permission:view categories'])->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::middleware(['permission:view brands'])->group(function () {
        Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::middleware(['permission:view products'])->group(function () {
        Route::post('products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulk_store');
        Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Expenses Management
    Route::middleware(['permission:view expenses'])->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    });

    Route::middleware(['permission:manage expenses'])->group(function () {
        Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

        // Expense Category Management (AJAX)
        Route::post('/expense-categories', [ExpenseController::class, 'storeCategory'])->name('expense-categories.store');
        Route::get('/api/expense-categories', [ExpenseController::class, 'getCategories'])->name('api.expense-categories.index');
    });

    Route::middleware(['permission:view expenses'])->group(function () {
        Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    });

    Route::middleware(['permission:manage expenses'])->group(function () {
        Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::post('/expenses/{expense}/mark-as-paid', [ExpenseController::class, 'markAsPaid'])->name('expenses.mark-as-paid');
    });

    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });

    // Sales Returns - requires view sales returns permission
    Route::middleware(['permission:view sales returns'])->group(function () {
        Route::get('/sales-returns', [SalesReturnController::class, 'index'])->name('sales-returns.index');
        Route::get('/sales-returns/create', [SalesReturnController::class, 'create'])->name('sales-returns.create');
        Route::post('/sales-returns', [SalesReturnController::class, 'store'])->name('sales-returns.store');
        Route::get('/sales-returns/{salesReturn}', [SalesReturnController::class, 'show'])->name('sales-returns.show');
        Route::post('/sales-returns/{salesReturn}/refund', [SalesReturnController::class, 'processRefund'])->name('sales-returns.refund');
        Route::post('/sales-returns/{salesReturn}/cancel', [SalesReturnController::class, 'cancel'])->name('sales-returns.cancel');
        Route::get('/sales-returns/get-returnable-items/{sale}', [SalesReturnController::class, 'getReturnableItems'])->name('sales.returnable-items');
    });

    // Supplier Returns - requires view supplier returns permission
    Route::middleware(['permission:view supplier returns'])->group(function () {
        Route::get('/supplier-returns', [SupplierReturnController::class, 'index'])->name('supplier-returns.index');
        Route::get('/supplier-returns/create', [SupplierReturnController::class, 'create'])->name('supplier-returns.create');
        Route::post('/supplier-returns', [SupplierReturnController::class, 'store'])->name('supplier-returns.store');
        Route::get('/supplier-returns/{supplierReturn}', [SupplierReturnController::class, 'show'])->name('supplier-returns.show');
        Route::post('/supplier-returns/{supplierReturn}/approve', [SupplierReturnController::class, 'approve'])->name('supplier-returns.approve');
        Route::post('/supplier-returns/{supplierReturn}/complete', [SupplierReturnController::class, 'complete'])->name('supplier-returns.complete');
        Route::post('/supplier-returns/{supplierReturn}/cancel', [SupplierReturnController::class, 'cancel'])->name('supplier-returns.cancel');
        Route::get('/good-receive-notes/{grn}/returnable-stock', [SupplierReturnController::class, 'getReturnableStock'])->name('good-receive-notes.returnable-stock');
    });

    // Sales routes - requires view sales permission
    Route::middleware(['permission:view sales'])->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });

    // Manual Sales routes - requires create sales permission (same as cashier)
    Route::middleware(['permission:create sales'])->group(function () {
        Route::post('/manual-sales', [ManualSaleController::class, 'store'])->name('manual-sales.store');
    });

    Route::middleware(['permission:view sales'])->group(function () {
        Route::get('/manual-sales', [ManualSaleController::class, 'index'])->name('manual-sales.index');
        Route::get('/manual-sales/{manualSale}', [ManualSaleController::class, 'show'])->name('manual-sales.show');
    });

    // Manual Sales Reconciliation routes - requires view sales permission
    Route::middleware(['permission:view sales'])->group(function () {
        Route::get('/manual-sales/reconciliation', [ManualSaleReconciliationController::class, 'index'])
            ->name('manual-sales.reconciliation.index');
        Route::get('/manual-sales/{manualSale}/reconciliation', [ManualSaleReconciliationController::class, 'show'])
            ->name('manual-sales.reconciliation.show');
        Route::post('/manual-sales/reconciliation/search-product', [ManualSaleReconciliationController::class, 'searchProductByBarcode'])
            ->name('manual-sales.reconciliation.search-product');
        Route::post('/manual-sales/{manualSale}/reconciliation', [ManualSaleReconciliationController::class, 'reconcile'])
            ->name('manual-sales.reconciliation.reconcile');
    });

    // API routes for cashier
    Route::get('/api/products/search', [SaleController::class, 'searchProducts'])->name('api.products.search');
    Route::get('/api/products/{product}/stock', [SaleController::class, 'getProductStock'])->name('api.products.stock');

    // Global Search API
    Route::get('/api/global-search', [GlobalSearchController::class, 'search'])->name('api.global-search');

    // Saved carts routes
    Route::get('/api/saved-carts', [SavedCartController::class, 'index'])->name('api.saved-carts.index');
    Route::post('/api/saved-carts', [SavedCartController::class, 'store'])->name('api.saved-carts.store');
    Route::get('/api/saved-carts/{savedCart}', [SavedCartController::class, 'show'])->name('api.saved-carts.show');
    Route::delete('/api/saved-carts/{savedCart}', [SavedCartController::class, 'destroy'])->name('api.saved-carts.destroy');

    // Good Receive Notes (GRN) routes - requires view grns permission
    Route::middleware(['permission:view grns'])->group(function () {
        Route::get('suppliers/{supplier}/products', [GoodReceiveNoteController::class, 'getSupplierProducts'])->name('suppliers.products');
        Route::get('suppliers/{supplier}/credit-info', [GoodReceiveNoteController::class, 'getSupplierCreditInfo'])->name('suppliers.credit-info');
        Route::resource('good-receive-notes', GoodReceiveNoteController::class);
    });

    // Batch management routes - requires view batches permission
    Route::middleware(['permission:view batches'])->group(function () {
        Route::resource('batches', BatchController::class)->only(['index', 'show']);
        Route::get('batches/expiring', [BatchController::class, 'expiring'])->name('batches.expiring');
    });

    // Stock management routes - requires view stocks permission
    Route::middleware(['permission:view stocks'])->group(function () {
        Route::resource('stocks', StockController::class)->only(['index', 'show']);
        Route::patch('stocks/{stock}/update-barcode', [StockController::class, 'updateBarcode'])
            ->name('stocks.update-barcode');
        Route::patch('stocks/{stock}', [StockController::class, 'update'])
            ->name('stocks.update');
    });

    // Stock Adjustment routes - requires manage stock adjustments permission
    Route::middleware(['permission:manage stock adjustments'])->group(function () {
        Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])
            ->name('stock-adjustments.index');
        Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])
            ->name('stock-adjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])
            ->name('stock-adjustments.store');
        Route::get('/stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'show'])
            ->name('stock-adjustments.show');
        Route::post('/stock-adjustments/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve'])
            ->name('stock-adjustments.approve');
        Route::post('/stock-adjustments/{stockAdjustment}/reject', [StockAdjustmentController::class, 'reject'])
            ->name('stock-adjustments.reject');
    });

    // Stock In routes - requires view stock in permission
    Route::middleware(['permission:view stock in'])->group(function () {
        Route::get('/stock', function () {
            return view('stock-in.index');
        })->name('stock-in.index');

        Route::get('/stock-create', function () {
            return view('stock-in.create');
        })->name('stock-in.create');
    });

    // Suppliers routes - requires view suppliers permission
    Route::middleware(['permission:view suppliers'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    // Vendor codes management routes - requires view vendor codes permission
    Route::middleware(['permission:view vendor codes'])->group(function () {
        Route::get('vendor-codes/products-without-codes', [VendorCodeController::class, 'getProductsWithoutVendorCodes'])
            ->name('vendor-codes.products-without-codes');
        Route::post('vendor-codes/bulk-sync', [VendorCodeController::class, 'bulkSync'])
            ->name('vendor-codes.bulk-sync');
        Route::resource('vendor-codes', VendorCodeController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // User Management Routes - Admin only
    Route::middleware(['permission:view users'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Unified Roles & Permissions Management - Admin only
    Route::middleware(['permission:view roles|view permissions'])->group(function () {
        Route::get('/roles-permissions', [RoleController::class, 'index'])->name('roles-permissions.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Employee Management Routes
    Route::middleware(['permission:view employees'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::post('/employees/{employee}/terminate', [EmployeeController::class, 'terminate'])->name('employees.terminate');
        Route::post('/employees/{employee}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');
    });

    // Employee's own payroll (must be before wildcard routes)
    Route::middleware(['permission:view own payroll'])->group(function () {
        Route::get('/payroll/my-payroll', [PayrollController::class, 'myPayroll'])->name('payroll.my-payroll');
    });

    // Payroll Management Routes
    Route::middleware(['permission:view payroll'])->group(function () {
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('/payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::delete('/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    });

    Route::middleware(['permission:process payroll'])->group(function () {
        Route::post('/payroll/{payroll}/process', [PayrollController::class, 'process'])->name('payroll.process');
        Route::put('/payroll/settings', [PayrollController::class, 'updateSettings'])->name('payroll.settings.update');
    });

    Route::middleware(['permission:approve payroll'])->group(function () {
        Route::post('/payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
    });

    Route::middleware(['permission:view payroll reports'])->group(function () {
        Route::get('/payroll/reports/overview', [PayrollController::class, 'reports'])->name('payroll.reports');
        Route::get('/payroll/{payroll}/export', [PayrollController::class, 'export'])->name('payroll.export');
    });

    // Supplier Credits Routes
    Route::middleware(['permission:view supplier credits'])->group(function () {
        Route::get('/supplier-credits', [\App\Http\Controllers\SupplierCreditController::class, 'index'])->name('supplier-credits.index');
        Route::get('/supplier-credits/{supplierCredit}', [\App\Http\Controllers\SupplierCreditController::class, 'show'])->name('supplier-credits.show');
    });

    // Supplier Payments Routes
    Route::middleware(['permission:view supplier payments'])->group(function () {
        Route::get('/supplier-payments', [\App\Http\Controllers\SupplierPaymentController::class, 'index'])->name('supplier-payments.index');
    });

    Route::middleware(['permission:create supplier payments'])->group(function () {
        Route::get('/supplier-payments/create', [\App\Http\Controllers\SupplierPaymentController::class, 'create'])->name('supplier-payments.create');
        Route::post('/supplier-payments', [\App\Http\Controllers\SupplierPaymentController::class, 'store'])->name('supplier-payments.store');
    });

    Route::middleware(['permission:view supplier payments'])->group(function () {
        Route::get('/supplier-payments/{supplierPayment}', [\App\Http\Controllers\SupplierPaymentController::class, 'show'])->name('supplier-payments.show');
    });

    // Customer Credits Management Routes - unified view for customers, credits, and payments
    Route::middleware(['permission:view customers'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/customers/process-payment', [CustomerController::class, 'processPayment'])->name('customers.process-payment');
    });

    // Accounting - Chart of Accounts Routes
    Route::middleware(['permission:view chart of accounts'])->group(function () {
        Route::get('/accounts', [\App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/{account}', [\App\Http\Controllers\AccountController::class, 'show'])->name('accounts.show');
    });

    Route::middleware(['permission:create accounts'])->group(function () {
        Route::get('/accounts/create', [\App\Http\Controllers\AccountController::class, 'create'])->name('accounts.create');
        Route::post('/accounts', [\App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
    });

    Route::middleware(['permission:edit accounts'])->group(function () {
        Route::get('/accounts/{account}/edit', [\App\Http\Controllers\AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('/accounts/{account}', [\App\Http\Controllers\AccountController::class, 'update'])->name('accounts.update');
    });

    Route::middleware(['permission:delete accounts'])->group(function () {
        Route::delete('/accounts/{account}', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');
    });

    // Accounting - Journal Entries Routes
    Route::middleware(['permission:view journal entries'])->group(function () {
        Route::get('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journal-entries.index');
        Route::get('/journal-entries/{journalEntry}', [\App\Http\Controllers\JournalEntryController::class, 'show'])->name('journal-entries.show');
    });

    Route::middleware(['permission:create journal entries'])->group(function () {
        Route::get('/journal-entries/create', [\App\Http\Controllers\JournalEntryController::class, 'create'])->name('journal-entries.create');
        Route::post('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'store'])->name('journal-entries.store');
    });

    Route::middleware(['permission:post journal entries'])->group(function () {
        Route::post('/journal-entries/{journalEntry}/post', [\App\Http\Controllers\JournalEntryController::class, 'post'])->name('journal-entries.post');
    });

    Route::middleware(['permission:void journal entries'])->group(function () {
        Route::post('/journal-entries/{journalEntry}/void', [\App\Http\Controllers\JournalEntryController::class, 'void'])->name('journal-entries.void');
    });

    // Accounting - Financial Reports Routes
    Route::middleware(['permission:view income statement'])->group(function () {
        Route::get('/reports/income-statement', [\App\Http\Controllers\ReportController::class, 'incomeStatement'])->name('reports.income-statement');
    });

    Route::middleware(['permission:view balance sheet'])->group(function () {
        Route::get('/reports/balance-sheet', [\App\Http\Controllers\ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    });

    Route::middleware(['permission:view trial balance'])->group(function () {
        Route::get('/reports/trial-balance', [\App\Http\Controllers\ReportController::class, 'trialBalance'])->name('reports.trial-balance');
    });

    Route::middleware(['permission:view general ledger'])->group(function () {
        Route::get('/reports/general-ledger', [\App\Http\Controllers\ReportController::class, 'generalLedger'])->name('reports.general-ledger');
    });
});
