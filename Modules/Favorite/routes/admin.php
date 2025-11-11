<?php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteComponent;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteStatisticsComponent;

// Admin rotaları - Favoriler yönetimi
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('favorite')
            ->name('favorite.')
            ->group(function () {
                // Ana liste sayfası
                Route::get('/', FavoriteComponent::class)
                    ->middleware('permission:favorite.view')
                    ->name('index');

                // İstatistikler
                Route::get('/statistics', FavoriteStatisticsComponent::class)
                    ->middleware('permission:favorite.view')
                    ->name('statistics');
            });
    });
