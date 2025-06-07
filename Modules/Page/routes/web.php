<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Controllers\Front\PageController;

// Ön yüz rotaları
Route::middleware(['web'])
    ->group(function () {
        Route::get('/', [PageController::class, 'homepage'])->name('home');
        Route::get('/' . module_setting('page', 'routes.index_slug', 'pages'), [PageController::class, 'index'])->name('pages.index');
        Route::get('/' . module_setting('page', 'routes.show_slug', 'page') . '/{slug}', [PageController::class, 'show'])->name('pages.show');
    });