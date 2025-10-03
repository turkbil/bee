<?php

use Illuminate\Support\Facades\Route;
use Modules\Announcement\App\Http\Controllers\Api\AnnouncementApiController;

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

Route::prefix('v1/announcements')->group(function () {
    Route::get('/', [AnnouncementApiController::class, 'index'])->name('api.announcements.index');
    Route::get('{slug}', [AnnouncementApiController::class, 'show'])->name('api.announcements.show');
});
