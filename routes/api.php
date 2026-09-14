<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// The `web` middleware group is applied so that the API can authenticate
// against the browser session cookie (as chosen for this project).
Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');
});
