<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Controllers\Front\PageController;

// Ön yüz rotaları
Route::middleware(['web'])
    ->group(function () {
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
    });