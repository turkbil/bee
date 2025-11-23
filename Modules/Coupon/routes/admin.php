<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\App\Http\Livewire\Admin\CouponComponent;
use Modules\Coupon\App\Http\Livewire\Admin\CouponManageComponent;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('coupon')
            ->name('coupon.')
            ->group(function () {
                Route::get('/', CouponComponent::class)->name('index');
                Route::get('/manage/{id?}', CouponManageComponent::class)->name('manage');
            });
    });
