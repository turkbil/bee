<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Livewire\Admin\SearchAnalyticsComponent;
use Modules\Search\App\Http\Livewire\Admin\SearchQueriesManagementComponent;
use Modules\Search\App\Http\Livewire\Admin\RecentSearchesComponent;

// Search Module Admin Routes
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('search')
            ->name('search.')
            ->group(function () {
                Route::get('/', SearchQueriesManagementComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('index');

                Route::get('/analytics', SearchAnalyticsComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('analytics');

                Route::get('/recent', RecentSearchesComponent::class)
                    ->middleware('module.permission:search,view')
                    ->name('recent');
            });
    });
