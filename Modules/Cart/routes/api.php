<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Http\Controllers\Api\CartApiController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Cart modülü API route'ları
 * Alpine.js component'ler için AJAX endpoint'leri
 *
*/

// Cart API - Session gerekli (CartWidget sync için)
Route::middleware([
    'web',  // Session için web middleware
    'tenant',
])->prefix('cart')->name('api.cart.')->group(function () {
    Route::post('/add', [CartApiController::class, 'addItem'])->name('add');
    Route::post('/update', [CartApiController::class, 'updateItem'])->name('update');
    Route::post('/remove', [CartApiController::class, 'removeItem'])->name('remove');
    Route::get('/count', [CartApiController::class, 'getCount'])->name('count');
});
