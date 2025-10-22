<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Livewire\Admin\SearchAnalyticsComponent;
use Modules\Search\App\Http\Livewire\Admin\SearchQueriesManagement;

// Search Module Admin Routes
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('search')
            ->name('search.')
            ->group(function () {
                Route::get('/', SearchQueriesManagement::class)
                    ->middleware('module.permission:search,view')
                    ->name('index');

                Route::get('/analytics', SearchAnalyticsComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('analytics');

                Route::get('/manage', SearchQueriesManagement::class)
                    ->middleware('module.permission:search,manage')
                    ->name('manage');
            });
    });
