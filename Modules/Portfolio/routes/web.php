<?php
// Modules/Portfolio/routes/web.php
use Illuminate\Support\Facades\Route;

// Namespace'leri düzeltelim
use Modules\Portfolio\App\Http\Livewire\PortfolioComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioManageComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryManageComponent;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('portfolio')
            ->name('portfolio.')
            ->group(function () {
                Route::get('/', PortfolioComponent::class)->name('index');
                Route::get('/manage/{id?}', PortfolioManageComponent::class)->name('manage');
                
                // Kategori Rotaları
                Route::get('/category', PortfolioCategoryComponent::class)->name('category.index');
                Route::get('/category/manage/{id?}', PortfolioCategoryManageComponent::class)->name('category.manage');
            });
    });