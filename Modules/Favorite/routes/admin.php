<?php

use Illuminate\Support\Facades\Route;

// Admin rotaları - Favoriler yönetimi
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('favorites')
            ->name('favorites.')
            ->group(function () {
                // Ana liste sayfası (gelecekte Livewire component eklenecek)
                Route::get('/', function() {
                    return view('favorite::admin.index');
                })->name('index');

                // İstatistikler
                Route::get('/statistics', function() {
                    return view('favorite::admin.statistics');
                })->name('statistics');
            });
    });
