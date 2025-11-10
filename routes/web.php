<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SavedCartController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\VendorCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cashier.dashboard');
});

Route::get('/cashier', function () {
    return view('cashier.dashboard');
})->name('cashier.dashboard');

// Add resource route for categories
Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);

Route::get('/expenses', function () {
    return view('expenses.index');
})->name('expenses.index');

Route::get('/inventory', function () {
    return view('inventory.dashboard');
})->name('inventory.dashboard');

Route::get('/reports', function () {
    return view('reports.index');
})->name('reports.index');

// Sales Returns
Route::get('/sales-returns', [SalesReturnController::class, 'index'])->name('sales-returns.index');
Route::get('/sales-returns/create', [SalesReturnController::class, 'create'])->name('sales-returns.create');
Route::post('/sales-returns', [SalesReturnController::class, 'store'])->name('sales-returns.store');
Route::get('/sales-returns/{salesReturn}', [SalesReturnController::class, 'show'])->name('sales-returns.show');
Route::post('/sales-returns/{salesReturn}/refund', [SalesReturnController::class, 'processRefund'])->name('sales-returns.refund');
Route::post('/sales-returns/{salesReturn}/cancel', [SalesReturnController::class, 'cancel'])->name('sales-returns.cancel');
Route::get('/sales-returns/get-returnable-items/{sale}', [SalesReturnController::class, 'getReturnableItems'])->name('sales.returnable-items');

// Supplier Returns
Route::get('/supplier-returns', [SupplierReturnController::class, 'index'])->name('supplier-returns.index');
Route::get('/supplier-returns/create', [SupplierReturnController::class, 'create'])->name('supplier-returns.create');
Route::post('/supplier-returns', [SupplierReturnController::class, 'store'])->name('supplier-returns.store');
Route::get('/supplier-returns/{supplierReturn}', [SupplierReturnController::class, 'show'])->name('supplier-returns.show');
Route::post('/supplier-returns/{supplierReturn}/approve', [SupplierReturnController::class, 'approve'])->name('supplier-returns.approve');
Route::post('/supplier-returns/{supplierReturn}/complete', [SupplierReturnController::class, 'complete'])->name('supplier-returns.complete');
Route::post('/supplier-returns/{supplierReturn}/cancel', [SupplierReturnController::class, 'cancel'])->name('supplier-returns.cancel');
Route::get('/good-receive-notes/{grn}/returnable-stock', [SupplierReturnController::class, 'getReturnableStock'])->name('good-receive-notes.returnable-stock');

// Sales routes
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

// API routes for cashier
Route::get('/api/products/search', [SaleController::class, 'searchProducts'])->name('api.products.search');
Route::get('/api/products/{product}/stock', [SaleController::class, 'getProductStock'])->name('api.products.stock');

// Saved carts routes
Route::get('/api/saved-carts', [SavedCartController::class, 'index'])->name('api.saved-carts.index');
Route::post('/api/saved-carts', [SavedCartController::class, 'store'])->name('api.saved-carts.store');
Route::get('/api/saved-carts/{savedCart}', [SavedCartController::class, 'show'])->name('api.saved-carts.show');
Route::delete('/api/saved-carts/{savedCart}', [SavedCartController::class, 'destroy'])->name('api.saved-carts.destroy');

Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

// Good Receive Notes (GRN) routes
Route::get('suppliers/{supplier}/products', [GoodReceiveNoteController::class, 'getSupplierProducts'])->name('suppliers.products');
Route::resource('good-receive-notes', GoodReceiveNoteController::class);

// Batch management routes
Route::resource('batches', BatchController::class)->only(['index', 'show']);
Route::get('batches/expiring', [BatchController::class, 'expiring'])->name('batches.expiring');

// Stock management routes
Route::resource('stocks', StockController::class)->only(['index', 'show']);

Route::get('/stock', function () {
    return view('stock-in.index');
})->name('stock-in.index');

Route::get('/stock-create', function () {
    return view('stock-in.create');
})->name('stock-in.create');

// Add resource route for suppliers
Route::resource('suppliers', SupplierController::class);

// Vendor codes management routes
Route::resource('vendor-codes', VendorCodeController::class)->only(['index', 'store', 'update', 'destroy']);
