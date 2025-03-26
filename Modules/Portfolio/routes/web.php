<?php
// Modules/Portfolio/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Livewire\PortfolioComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioManageComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryManageComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('portfolio')
            ->name('portfolio.')
            ->group(function () {
                Route::get('/', PortfolioComponent::class)
                    ->middleware('module.permission:portfolio,view')
                    ->name('index');
                Route::get('/manage/{id?}', PortfolioManageComponent::class)
                    ->middleware('module.permission:portfolio,update')
                    ->name('manage');
                
                // Kategori Rotaları
                Route::get('/category', PortfolioCategoryComponent::class)
                    ->middleware('module.permission:portfolio,view')
                    ->name('category.index');
                Route::get('/category/manage/{id?}', PortfolioCategoryManageComponent::class)
                    ->middleware('module.permission:portfolio,update')
                    ->name('category.manage');
            });
    });