<?php

use Illuminate\Support\Facades\Route;

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

// Song Streaming API
Route::prefix('muzibu/songs')->group(function () {
    Route::get('{songId}/stream', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'stream'])
        ->name('api.muzibu.songs.stream');

    Route::get('{songId}/conversion-status', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'checkConversionStatus'])
        ->name('api.muzibu.songs.conversion-status');

    Route::post('{songId}/play', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'incrementPlayCount'])
        ->name('api.muzibu.songs.play');
});
