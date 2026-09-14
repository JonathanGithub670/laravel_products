<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesDiscountController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show', 'create', 'edit']);
    Route::resource('stocks', StockController::class)->except(['show', 'create', 'edit']);
    Route::resource('sales', SaleController::class)->except(['show', 'create', 'edit']);
    Route::resource('discounts', DiscountController::class)->except(['show', 'create', 'edit']);
    Route::resource('sales-discounts', SalesDiscountController::class)->only(['index', 'destroy']);
});

Route::redirect('/', '/dashboard');
