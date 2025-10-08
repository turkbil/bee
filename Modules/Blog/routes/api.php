<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\App\Http\Controllers\Api\BlogApiController;

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

Route::prefix('v1/blogs')->group(function () {
    Route::get('/', [BlogApiController::class, 'index'])->name('api.blogs.index');
    Route::get('{slug}', [BlogApiController::class, 'show'])->name('api.blogs.show');
});
