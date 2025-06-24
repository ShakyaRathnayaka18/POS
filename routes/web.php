<?php

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

Route::get('/categories', function () {
    return view('categories.index');
})->name('categories.index');

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

Route::get('/products/{id}/edit', function ($id) {
    return view('products.index', ['id' => $id]);
})->name('products.edit');


Route::get('/purchase-orders-index', function () {
    return view('purchase-orders.index');
})->name('purchase-orders.index');

Route::get('/purchase-orders-create', function () {
    return view('purchase-orders.create');
})->name('purchase-orders.create');

Route::get('/stock', function () {
    return view('stock-in.index');
})->name('stock-in.index');

Route::get('/stock-create', function () {
    return view('stock-in.create');
})->name('stock-in.create');

Route::get('/suppliers', function () {
    return view('suppliers.index');
})->name('suppliers.index');

Route::get('/suppliers-create', function () {
    return view('suppliers.create');
})->name('suppliers.create');
