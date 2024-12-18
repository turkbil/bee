<?php
// Modules/Page/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Controllers\PageController;

Route::middleware(['web', 'auth'])
    ->prefix('admin/page')
    ->name('admin.page.')
    ->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('list', [PageController::class, 'list'])->name('list');
        Route::match(['get', 'post'], '/manage/{page_id?}', [PageController::class, 'manage'])->name('manage');
        Route::post('toggle-active/{pageId}', [PageController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('{pageId}', [PageController::class, 'delete'])->name('delete');
    });
