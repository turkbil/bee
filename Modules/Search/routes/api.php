<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\App\Http\Controllers\Api\SearchApiController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
*/

// Public Search API (Tenant-aware, Rate Limited)
Route::middleware(['tenant', 'throttle:60,1'])->prefix('search')->group(function () {
    Route::get('/', [SearchApiController::class, 'search'])->name('api.search');
    Route::get('/suggestions', [SearchApiController::class, 'suggestions'])->name('api.search.suggestions');
    Route::post('/track-click', [SearchApiController::class, 'trackClick'])->name('api.search.track-click');
});
