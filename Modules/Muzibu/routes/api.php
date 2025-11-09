<?php

use Illuminate\Support\Facades\Route;
use Modules\Muzibu\App\Http\Controllers\Api\MuzibuApiController;

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

Route::prefix('v1/muzibus')->group(function () {
    Route::get('/', [MuzibuApiController::class, 'index'])->name('api.muzibus.index');
    Route::get('{slug}', [MuzibuApiController::class, 'show'])->name('api.muzibus.show');
});
