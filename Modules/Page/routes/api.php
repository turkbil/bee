<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Controllers\Api\PageApiController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1/pages')->group(function () {
    Route::get('/', [PageApiController::class, 'index'])->name('api.pages.index');
    Route::get('homepage', [PageApiController::class, 'homepage'])->name('api.pages.homepage');
    Route::get('by-id/{id}', [PageApiController::class, 'getById'])->name('api.pages.get-by-id');
    Route::get('{slug}', [PageApiController::class, 'show'])->name('api.pages.show');
});
