<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\Api\PaymentApiController;

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

Route::prefix('v1/payments')->group(function () {
    Route::get('/', [PaymentApiController::class, 'index'])->name('api.payments.index');
    Route::get('{slug}', [PaymentApiController::class, 'show'])->name('api.payments.show');
});
