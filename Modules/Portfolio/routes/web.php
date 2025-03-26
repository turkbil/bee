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
                    
                // Yeni portfolio oluşturma
                Route::get('/manage', PortfolioManageComponent::class)
                    ->middleware('module.permission:portfolio,create')
                    ->name('create');
                    
                // Mevcut portfolio düzenleme
                Route::get('/manage/{id}', PortfolioManageComponent::class)
                    ->middleware('module.permission:portfolio,update')
                    ->where('id', '[0-9]+')
                    ->name('edit');
                
                // Kategori Rotaları
                Route::get('/category', PortfolioCategoryComponent::class)
                    ->middleware('module.permission:portfolio,view')
                    ->name('category.index');
                    
                // Yeni kategori oluşturma
                Route::get('/category/manage', PortfolioCategoryManageComponent::class)
                    ->middleware('module.permission:portfolio,create')
                    ->name('category.create');
                    
                // Mevcut kategori düzenleme
                Route::get('/category/manage/{id}', PortfolioCategoryManageComponent::class)
                    ->middleware('module.permission:portfolio,update')
                    ->where('id', '[0-9]+')
                    ->name('category.edit');
            });
    });