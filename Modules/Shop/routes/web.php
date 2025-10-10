<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Controllers\Front\ShopController;

Route::middleware(['web', 'tenant'])
    ->prefix('shop')
    ->group(function () {
        Route::get('/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
        Route::get('/brand/{slug}', [ShopController::class, 'brand'])->name('shop.brand');
    });
