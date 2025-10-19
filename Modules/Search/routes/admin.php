<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Livewire\Admin\SearchAnalyticsComponent;

// Search Module Admin Routes
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('search')
            ->name('search.')
            ->group(function () {
                Route::get('/', SearchAnalyticsComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('index');

                Route::get('/analytics', SearchAnalyticsComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('analytics');
            });
    });
