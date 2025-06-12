<?php
// Modules/Portfolio/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Controllers\Front\PortfolioController;

// Ön yüz rotaları - DynamicRouteService tarafından yönetiliyor
// Route::middleware(['web'])
//     ->group(function () {
//         Route::get('/portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
//         Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolios.show');
//         Route::get('/portfolio-category/{slug}', [PortfolioController::class, 'category'])->name('portfolios.category');
//     });