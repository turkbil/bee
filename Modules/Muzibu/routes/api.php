<?php

use Illuminate\Support\Facades\Route;
use Modules\Muzibu\app\Http\Controllers\Api\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Api\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Api\SongController;
use Modules\Muzibu\app\Http\Controllers\Api\GenreController;
use Modules\Muzibu\app\Http\Controllers\Api\SectorController;
use Modules\Muzibu\app\Http\Controllers\Api\DeviceController;
use Modules\Muzibu\app\Http\Controllers\Api\QueueRefillController;

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

    // Playlists - General API throttle
    Route::prefix('playlists')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [PlaylistController::class, 'index'])->name('api.muzibu.playlists.index');
        Route::get('/featured', [PlaylistController::class, 'featured'])->name('api.muzibu.playlists.featured');
        Route::get('/{id}', [PlaylistController::class, 'show'])->name('api.muzibu.playlists.show');

        // User Playlist Management (auth required)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/my-playlists', [PlaylistController::class, 'myPlaylists'])->name('api.muzibu.playlists.my-playlists');
            Route::post('/clone', [PlaylistController::class, 'clone'])->name('api.muzibu.playlists.clone');
            Route::post('/quick-create', [PlaylistController::class, 'quickCreate'])->name('api.muzibu.playlists.quick-create');
            Route::post('/{id}/add-song', [PlaylistController::class, 'addSong'])->name('api.muzibu.playlists.add-song');
            Route::delete('/{id}/remove-song/{songId}', [PlaylistController::class, 'removeSong'])->name('api.muzibu.playlists.remove-song');
            Route::put('/{id}/reorder', [PlaylistController::class, 'reorder'])->name('api.muzibu.playlists.reorder');
            Route::delete('/{id}', [PlaylistController::class, 'delete'])->name('api.muzibu.playlists.delete');
        });
    });

    // Albums - General API throttle
    Route::prefix('albums')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [AlbumController::class, 'index'])->name('api.muzibu.albums.index');
        Route::get('/new-releases', [AlbumController::class, 'newReleases'])->name('api.muzibu.albums.new-releases');
        Route::get('/{id}', [AlbumController::class, 'show'])->name('api.muzibu.albums.show');
    });

    // Songs - Stream endpoints with strict throttle
    Route::prefix('songs')->group(function () {
        Route::get('/recent', [SongController::class, 'recent'])->name('api.muzibu.songs.recent')->middleware(['auth:sanctum', 'throttle.user:api']);
        Route::get('/popular', [SongController::class, 'popular'])->name('api.muzibu.songs.popular')->middleware('throttle.user:api');
        Route::get('/last-played', [SongController::class, 'lastPlayed'])->name('api.muzibu.songs.last-played')->middleware('throttle.user:api');
        Route::post('/{id}/track-play', [SongController::class, 'trackPlay'])->name('api.muzibu.songs.track-play')->middleware(['auth:sanctum', 'throttle.user:api']);

        // Premium Limit System - SongStreamController (HLS conversion logic) - STRICT STREAM THROTTLE
        Route::get('/{id}/stream', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'stream'])
            ->name('api.muzibu.songs.stream')
            ->middleware('throttle.user:stream'); // 🔥 Guest: 30/min, Member: 120/min, Premium: 300/min
        Route::get('/{id}/serve', [SongController::class, 'serve'])
            ->name('api.muzibu.songs.serve')
            ->middleware(['signed.url', 'throttle.user:stream']); // 🔐 Signed URL + rate limiting
        Route::get('/{id}/key', [SongController::class, 'serveEncryptionKey'])
            ->name('api.muzibu.songs.encryption-key')
            ->middleware('throttle.user:api'); // Rate limit to prevent key harvesting

        // 🎯 Dynamic Playlist (User tipine göre 4 chunk: 3 çal + 1 buffer)
        Route::get('/{id}/playlist', [SongController::class, 'dynamicPlaylist'])
            ->name('api.muzibu.songs.dynamic-playlist')
            ->middleware('throttle.user:stream');

        // 🔒 Chunk Serve (Token-based authorization)
        Route::get('/{id}/chunk/{chunkName}', [SongController::class, 'serveChunk'])
            ->name('api.muzibu.songs.serve-chunk')
            ->middleware('throttle.user:stream');

        Route::get('/{id}/conversion-status', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'checkConversionStatus'])
            ->name('api.muzibu.songs.conversion-status')
            ->middleware('throttle.user:api');
        Route::post('/{id}/play', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'incrementPlayCount'])
            ->name('api.muzibu.songs.play')
            ->middleware('throttle.user:api');
        Route::post('/{id}/track-progress', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'trackProgress'])
            ->name('api.muzibu.songs.track-progress')
            ->middleware(['auth:sanctum', 'throttle.user:api']);
    });

    // Genres - General API throttle
    Route::prefix('genres')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [GenreController::class, 'index'])->name('api.muzibu.genres.index');
        Route::get('/{id}/songs', [GenreController::class, 'songs'])->name('api.muzibu.genres.songs');
    });

    // Sectors - General API throttle
    Route::prefix('sectors')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [SectorController::class, 'index'])->name('api.muzibu.sectors.index');
        Route::get('/{id}/playlists', [SectorController::class, 'playlists'])->name('api.muzibu.sectors.playlists');
        Route::get('/{id}/songs', [SectorController::class, 'songs'])->name('api.muzibu.sectors.songs');
    });

    // Queue Refill - Context-based infinite queue system
    Route::post('/queue/refill', [QueueRefillController::class, 'refill'])
        ->name('api.muzibu.queue.refill')
        ->middleware('throttle.user:api');
});

// Device Management (Tenant 1001 only) - Outside muzibu prefix
Route::prefix('devices')->middleware('auth:sanctum')->group(function () {
    Route::post('/check', [DeviceController::class, 'check'])->name('api.devices.check');
    Route::get('/active', [DeviceController::class, 'index'])->name('api.devices.index');
    Route::delete('/{sessionId}', [DeviceController::class, 'destroy'])->name('api.devices.destroy');
    Route::delete('/all', [DeviceController::class, 'destroyAll'])->name('api.devices.destroyAll');
});
