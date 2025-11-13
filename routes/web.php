<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SavedCartController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->to('/cashier');
    }

    return redirect()->route('login');
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Cashier Dashboard - requires create sales permission
    Route::middleware(['permission:create sales'])->group(function () {
        Route::get('/cashier', function () {
            return view('cashier.dashboard');
        })->name('cashier.dashboard');
    });

    // Products Management - requires appropriate permissions
    Route::middleware(['permission:view categories'])->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::middleware(['permission:view brands'])->group(function () {
        Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::middleware(['permission:view products'])->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::middleware(['permission:view expenses'])->group(function () {
        Route::get('/expenses', function () {
            return view('expenses.index');
        })->name('expenses.index');
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

    // API routes for cashier
    Route::get('/api/products/search', [SaleController::class, 'searchProducts'])->name('api.products.search');
    Route::get('/api/products/{product}/stock', [SaleController::class, 'getProductStock'])->name('api.products.stock');

    // Saved carts routes
    Route::get('/api/saved-carts', [SavedCartController::class, 'index'])->name('api.saved-carts.index');
    Route::post('/api/saved-carts', [SavedCartController::class, 'store'])->name('api.saved-carts.store');
    Route::get('/api/saved-carts/{savedCart}', [SavedCartController::class, 'show'])->name('api.saved-carts.show');
    Route::delete('/api/saved-carts/{savedCart}', [SavedCartController::class, 'destroy'])->name('api.saved-carts.destroy');

    // Good Receive Notes (GRN) routes - requires view good receive notes permission
    Route::middleware(['permission:view good receive notes'])->group(function () {
        Route::get('suppliers/{supplier}/products', [GoodReceiveNoteController::class, 'getSupplierProducts'])->name('suppliers.products');
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
});
