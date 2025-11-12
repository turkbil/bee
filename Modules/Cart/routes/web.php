<?php

use Illuminate\Support\Facades\Route;

// Frontend Cart Routes
Route::middleware(['web', 'tenant', 'locale.site'])
    ->prefix('cart')
    ->name('cart.')
    ->group(function () {
        Route::get('/', \Modules\Cart\App\Http\Livewire\Front\CartPage::class)->name('index');
    });
