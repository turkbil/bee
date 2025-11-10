<?php

use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Http\Controllers\Api\FavoriteApiController;

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

Route::prefix('v1/favorites')->group(function () {
    Route::get('/', [FavoriteApiController::class, 'index'])->name('api.favorites.index');
    Route::get('{slug}', [FavoriteApiController::class, 'show'])->name('api.favorites.show');
});
