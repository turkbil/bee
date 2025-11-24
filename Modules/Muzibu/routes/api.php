<?php

use Illuminate\Support\Facades\Route;
use Modules\Muzibu\app\Http\Controllers\Api\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Api\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Api\SongController;
use Modules\Muzibu\app\Http\Controllers\Api\GenreController;
use Modules\Muzibu\app\Http\Controllers\Api\SectorController;

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

Route::prefix('muzibu')->group(function () {

    // Playlists
    Route::prefix('playlists')->group(function () {
        Route::get('/', [PlaylistController::class, 'index'])->name('api.muzibu.playlists.index');
        Route::get('/featured', [PlaylistController::class, 'featured'])->name('api.muzibu.playlists.featured');
        Route::get('/{id}', [PlaylistController::class, 'show'])->name('api.muzibu.playlists.show');
    });

    // Albums
    Route::prefix('albums')->group(function () {
        Route::get('/', [AlbumController::class, 'index'])->name('api.muzibu.albums.index');
        Route::get('/new-releases', [AlbumController::class, 'newReleases'])->name('api.muzibu.albums.new-releases');
        Route::get('/{id}', [AlbumController::class, 'show'])->name('api.muzibu.albums.show');
    });

    // Songs
    Route::prefix('songs')->group(function () {
        Route::get('/recent', [SongController::class, 'recent'])->name('api.muzibu.songs.recent')->middleware('auth:sanctum');
        Route::get('/popular', [SongController::class, 'popular'])->name('api.muzibu.songs.popular');
        Route::post('/{id}/track-play', [SongController::class, 'trackPlay'])->name('api.muzibu.songs.track-play')->middleware('auth:sanctum');
        Route::get('/{id}/stream', [SongController::class, 'stream'])->name('api.muzibu.songs.stream');
        Route::get('/{id}/serve', [SongController::class, 'serve'])->name('api.muzibu.songs.serve');

        // Old streaming routes (compatibility)
        Route::get('{songId}/stream', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'stream'])
            ->name('api.muzibu.songs.stream.old');
        Route::get('{songId}/conversion-status', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'checkConversionStatus'])
            ->name('api.muzibu.songs.conversion-status');
        Route::post('{songId}/play', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'incrementPlayCount'])
            ->name('api.muzibu.songs.play');
    });

    // Genres
    Route::prefix('genres')->group(function () {
        Route::get('/', [GenreController::class, 'index'])->name('api.muzibu.genres.index');
        Route::get('/{id}/songs', [GenreController::class, 'songs'])->name('api.muzibu.genres.songs');
    });

    // Sectors
    Route::prefix('sectors')->group(function () {
        Route::get('/', [SectorController::class, 'index'])->name('api.muzibu.sectors.index');
        Route::get('/{id}/playlists', [SectorController::class, 'playlists'])->name('api.muzibu.sectors.playlists');
    });
});
