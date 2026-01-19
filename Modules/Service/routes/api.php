<?php

use Illuminate\Support\Facades\Route;
use Modules\Service\App\Http\Controllers\Api\ServiceApiController;

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

Route::prefix('v1/services')->group(function () {
    Route::get('/', [ServiceApiController::class, 'index'])->name('api.services.index');
    Route::get('{slug}', [ServiceApiController::class, 'show'])->name('api.services.show');
});
