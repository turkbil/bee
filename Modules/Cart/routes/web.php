<?php

use Illuminate\Support\Facades\Route;

// Frontend Cart Routes
Route::middleware(['tenant', 'locale.site'])
    ->prefix('cart')
    ->name('cart.')
    ->group(function () {
        Route::get('/', \Modules\Cart\App\Http\Livewire\Front\CartPage::class)->name('index');
        Route::get('/checkout', \Modules\Cart\App\Http\Livewire\Front\CheckoutPage::class)->name('checkout')->middleware('auth');
    });
