<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\App\Http\Livewire\Admin\SubscriptionPlanComponent;
use Modules\Subscription\App\Http\Livewire\Admin\SubscriptionPlanManageComponent;
use Modules\Subscription\App\Http\Livewire\Admin\SubscriptionComponent;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('subscription')
            ->name('subscription.')
            ->group(function () {
                // Plans
                Route::get('/plans', SubscriptionPlanComponent::class)->name('plans.index');
                Route::get('/plans/manage/{id?}', SubscriptionPlanManageComponent::class)->name('plans.manage');

                // Subscriptions
                Route::get('/', SubscriptionComponent::class)->name('index');
            });
    });
