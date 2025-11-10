<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\PendingReviewsComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewStatisticsComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewManageComponent;

// Admin rotaları - Yorum ve Puan yönetimi
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('reviewsystem')
            ->name('reviewsystem.')
            ->group(function () {
                // Tüm yorumlar
                Route::get('/', ReviewComponent::class)->name('index');

                // Onay bekleyenler
                Route::get('/pending', PendingReviewsComponent::class)->name('pending');

                // İstatistikler
                Route::get('/statistics', ReviewStatisticsComponent::class)->name('statistics');

                // Manuel yorum ekle/düzenle
                Route::get('/add', ReviewManageComponent::class)->name('add');
                Route::get('/edit/{id}', ReviewManageComponent::class)->name('edit');
            });
    });
