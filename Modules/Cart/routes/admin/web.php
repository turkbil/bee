<?php

use Illuminate\Support\Facades\Route;

// Admin Cart Routes
Route::prefix('admin/cart')
    ->middleware(['web', 'admin', 'tenant'])
    ->name('admin.cart.')
    ->group(function () {
        // Cart Management
        Route::get('/', \Modules\Cart\App\Http\Livewire\Admin\CartComponent::class)->name('index');
    });

// Admin Orders Routes
Route::prefix('admin/orders')
    ->middleware(['web', 'admin', 'tenant'])
    ->name('admin.orders.')
    ->group(function () {
        // Orders Management
        Route::get('/', \Modules\Cart\App\Http\Livewire\Admin\OrdersComponent::class)->name('index');
    });
