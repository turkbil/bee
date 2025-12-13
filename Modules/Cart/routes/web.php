<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Http\Controllers\Front\OrderHistoryController;

// Frontend Cart Routes
Route::middleware(['tenant', 'locale.site'])
    ->prefix('cart')
    ->name('cart.')
    ->group(function () {
        Route::get('/', \Modules\Cart\App\Http\Livewire\Front\CartPage::class)->name('index');
        Route::get('/checkout', \Modules\Cart\App\Http\Livewire\Front\CheckoutPage::class)->name('checkout')->middleware('auth');
    });

// Sipariş Geçmişi Routes (Auth required)
Route::middleware(['tenant', 'locale.site', 'auth'])
    ->prefix('siparislerim')
    ->name('shop.orders.')
    ->group(function () {
        Route::get('/', [OrderHistoryController::class, 'index'])->name('index');
        Route::get('/{orderId}', [OrderHistoryController::class, 'show'])->name('show')->where('orderId', '[0-9]+');
        Route::get('/no/{orderNumber}', [OrderHistoryController::class, 'showByNumber'])->name('by-number');
    });
