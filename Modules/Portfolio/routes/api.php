<?php

use Illuminate\Support\Facades\Route;
use Modules\Portfolio\App\Http\Controllers\Api\PortfolioApiController;

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

Route::prefix('v1/portfolios')->group(function () {
    Route::get('/', [PortfolioApiController::class, 'index'])->name('api.portfolios.index');
    Route::get('{slug}', [PortfolioApiController::class, 'show'])->name('api.portfolios.show');
});
