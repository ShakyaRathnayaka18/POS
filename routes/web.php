<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cashier.dashboard');
});

Route::get('/cashier', function () {
    return view('cashier.dashboard');
})->name('cashier.dashboard');

Route::get('/brands', function () {
    return view('brands.index');
})->name('brands.index');

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

Route::get('/returns', function () {
    return view('returns.index');
})->name('returns.index');

Route::get('/sales', function () {
    return view('sales.index');
})->name('sales.index');

Route::get('/products-index', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products-create', function () {
    return view('products.create');
})->name('products.create');

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
