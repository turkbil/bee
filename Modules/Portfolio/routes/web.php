<?php
// Modules/Portfolio/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Livewire\PortfolioComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioManageComponent;

Route::middleware(['web', 'auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('portfolio')
            ->name('portfolio.')
            ->group(function () {
                Route::get('/', PortfolioComponent::class)->name('index');
                Route::get('/manage/{id?}', PortfolioManageComponent::class)->name('manage');
            });
    });
