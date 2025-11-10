<?php

use Illuminate\Support\Facades\Route;

// Admin rotaları - Yorum ve Puan yönetimi
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('reviews')
            ->name('reviews.')
            ->group(function () {
                // Tüm yorumlar
                Route::get('/', function() {
                    return view('reviewsystem::admin.index');
                })->name('index');

                // Onay bekleyenler
                Route::get('/pending', function() {
                    return view('reviewsystem::admin.pending');
                })->name('pending');

                // İstatistikler
                Route::get('/statistics', function() {
                    return view('reviewsystem::admin.statistics');
                })->name('statistics');
            });
    });
