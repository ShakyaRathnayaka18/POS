<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;

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

// Add resource route for purchase orders
Route::resource('purchase-orders', PurchaseOrderController::class);

Route::get('/stock', function () {
    return view('stock-in.index');
})->name('stock-in.index');

Route::get('/stock-create', function () {
    return view('stock-in.create');
})->name('stock-in.create');

// Add resource route for suppliers
Route::resource('suppliers', SupplierController::class);
