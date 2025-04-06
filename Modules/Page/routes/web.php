<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Controllers\PageFrontController;

// Ön yüz rotaları
Route::middleware(['web'])
    ->name('pages.')
    ->group(function () {
        Route::get('/pages', [PageFrontController::class, 'index'])->name('index');
        Route::get('/pages/{slug}', [PageFrontController::class, 'show'])->name('show');
    });