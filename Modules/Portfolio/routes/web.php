<?php
// Modules/Portfolio/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Controllers\Front\PortfolioController;

// Ön yüz rotaları
Route::middleware(['web'])
    ->group(function () {
        Route::get('/' . module_setting('portfolio', 'routes.index_slug', 'portfolios'), [PortfolioController::class, 'index'])->name('portfolios.index');
        Route::get('/' . module_setting('portfolio', 'routes.show_slug', 'portfolio') . '/{slug}', [PortfolioController::class, 'show'])->name('portfolios.show');
        Route::get('/' . module_setting('portfolio', 'routes.category_slug', 'portfolio-category') . '/{slug}', [PortfolioController::class, 'category'])->name('portfolios.category');
    });