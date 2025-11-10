<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewSystem\App\Http\Controllers\Api\ReviewSystemApiController;

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

Route::prefix('v1/reviewsystems')->group(function () {
    Route::get('/', [ReviewSystemApiController::class, 'index'])->name('api.reviewsystems.index');
    Route::get('{slug}', [ReviewSystemApiController::class, 'show'])->name('api.reviewsystems.show');
});
