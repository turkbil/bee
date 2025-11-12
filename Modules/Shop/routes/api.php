<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Controllers\Api\ShopApiController;

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

Route::prefix('v1/shops')->group(function () {
    Route::get('/', [ShopApiController::class, 'index'])->name('api.shops.index');
    Route::get('{slug}', [ShopApiController::class, 'show'])->name('api.shops.show');
});
