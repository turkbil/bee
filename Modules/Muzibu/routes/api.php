<?php

use Illuminate\Support\Facades\Route;
use Modules\Muzibu\app\Http\Controllers\Api\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Api\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Api\ArtistController;
use Modules\Muzibu\app\Http\Controllers\Api\SongController;
use Modules\Muzibu\app\Http\Controllers\Api\GenreController;
use Modules\Muzibu\app\Http\Controllers\Api\SectorController;
use Modules\Muzibu\app\Http\Controllers\Api\RadioController;
use Modules\Muzibu\app\Http\Controllers\Api\RatingController;
use Modules\Muzibu\app\Http\Controllers\Api\DeviceController;
use Modules\Muzibu\app\Http\Controllers\Api\QueueRefillController;
use Modules\Muzibu\app\Http\Controllers\Front\SearchController;

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

// 🔑 HLS ENCRYPTION KEY - MOVED TO MuzibuServiceProvider::loadApiRoutes()
// Route registered separately without session middleware for performance

Route::prefix('muzibu')->group(function () {

    // Search - Meilisearch powered
    Route::get('/search', [SearchController::class, 'search'])
        ->name('api.muzibu.search')
        ->middleware('throttle.user:api');

    // Playlists - General API throttle
    Route::prefix('playlists')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [PlaylistController::class, 'index'])->name('api.muzibu.playlists.index');
        Route::get('/featured', [PlaylistController::class, 'featured'])->name('api.muzibu.playlists.featured');
        Route::get('/{id}', [PlaylistController::class, 'show'])->name('api.muzibu.playlists.show');

        // User Playlist Management (auth required - supports both web session and sanctum token)
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/my-playlists', [PlaylistController::class, 'myPlaylists'])->name('api.muzibu.playlists.my-playlists');
            Route::post('/clone', [PlaylistController::class, 'clone'])->name('api.muzibu.playlists.clone');
            Route::post('/quick-create', [PlaylistController::class, 'quickCreate'])->name('api.muzibu.playlists.quick-create');
            Route::post('/{id}/add-song', [PlaylistController::class, 'addSong'])->name('api.muzibu.playlists.add-song');
            Route::post('/{id}/copy', [PlaylistController::class, 'copy'])->name('api.muzibu.playlists.copy');
            Route::delete('/{id}/remove-song/{songId}', [PlaylistController::class, 'removeSong'])->name('api.muzibu.playlists.remove-song');
            Route::put('/{id}/reorder', [PlaylistController::class, 'reorder'])->name('api.muzibu.playlists.reorder');
            Route::put('/{id}', [PlaylistController::class, 'update'])->name('api.muzibu.playlists.update');
            Route::delete('/{id}', [PlaylistController::class, 'delete'])->name('api.muzibu.playlists.delete');
        });
    });

    // Albums - General API throttle
    Route::prefix('albums')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [AlbumController::class, 'index'])->name('api.muzibu.albums.index');
        Route::get('/new-releases', [AlbumController::class, 'newReleases'])->name('api.muzibu.albums.new-releases');
        Route::get('/{id}', [AlbumController::class, 'show'])->name('api.muzibu.albums.show');
    });

    // Artists - General API throttle
    Route::prefix('artists')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [ArtistController::class, 'index'])->name('api.muzibu.artists.index');
        Route::get('/{id}', [ArtistController::class, 'show'])->name('api.muzibu.artists.show');
        Route::get('/{id}/albums', [ArtistController::class, 'albums'])->name('api.muzibu.artists.albums');
        Route::get('/{id}/songs', [ArtistController::class, 'songs'])->name('api.muzibu.artists.songs');
    });

    // Songs - Stream endpoints with strict throttle
    Route::prefix('songs')->group(function () {
        Route::get('/recent', [SongController::class, 'recent'])->name('api.muzibu.songs.recent')->middleware(['auth:sanctum', 'throttle.user:api']);
        Route::get('/popular', [SongController::class, 'popular'])->name('api.muzibu.songs.popular')->middleware('throttle.user:api');
        Route::get('/last-played', [SongController::class, 'lastPlayed'])->name('api.muzibu.songs.last-played')->middleware('throttle.user:api');
        Route::post('/{id}/track-play', [SongController::class, 'trackPlay'])->name('api.muzibu.songs.track-play')->middleware(['auth:sanctum', 'throttle.user:api']);

        // Get which playlists contain a song (for playlist select modal)
        Route::get('/{id}/playlists', [SongController::class, 'getPlaylistsContainingSong'])->name('api.muzibu.songs.playlists')->middleware(['web', 'auth']);

        // Get song by ID (for queue restoration after page refresh)
        Route::get('/{id}', [SongController::class, 'show'])->name('api.muzibu.songs.show')->middleware('throttle.user:api');

        // Premium Limit System - SongStreamController (HLS conversion logic) - STRICT STREAM THROTTLE
        // 🔥 FIX: StartSession middleware eklendi - auth('web')->user() çalışsın diye
        Route::get('/{id}/stream', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'stream'])
            ->name('api.muzibu.songs.stream')
            ->middleware(['web', 'throttle.user:stream']); // web middleware session başlatır
        Route::get('/{id}/serve', [SongController::class, 'serve'])
            ->name('api.muzibu.songs.serve')
            ->middleware(['signed.url', 'throttle.user:stream']); // 🔐 Signed URL + rate limiting

        // 🔑 HLS Encryption Key Endpoint (for playlist.m3u8 #EXT-X-KEY URI)
        Route::match(['get', 'options'], '/{id}/key', [\Modules\Muzibu\App\Http\Controllers\Api\SongStreamController::class, 'serveKey'])
            ->name('api.muzibu.songs.key')
            ->middleware('throttle.user:stream'); // Public access, rate limited, no session needed

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

    // Radios - General API throttle
    Route::prefix('radios')->middleware('throttle.user:api')->group(function () {
        Route::get('/', [RadioController::class, 'index'])->name('api.muzibu.radios.index');
        Route::get('/{id}/songs', [RadioController::class, 'songs'])->name('api.muzibu.radios.songs');
    });

    // Rating - Universal rating for all content types (auth required)
    Route::post('/{type}/{id}/rate', [RatingController::class, 'rate'])
        ->name('api.muzibu.rate')
        ->middleware(['auth:sanctum', 'throttle.user:api'])
        ->where('type', 'songs|albums|playlists|genres|sectors|radios');

    // 🤖 AI ASSISTANT ACTIONS - Playlist & Play Management
    Route::prefix('ai')->middleware('throttle.user:api')->group(function () {
        // 🎯 NEW: Playlist oluşturma (ACTION button için - Auth required)
        Route::post('/playlist/create', [PlaylistController::class, 'createFromAI'])
            ->middleware('auth:sanctum')
            ->name('api.muzibu.ai.playlist.create');

        // ❤️ Favorilere ekleme (ACTION button için - Auth required)
        Route::post('/favorite/add', [\Modules\Muzibu\App\Http\Controllers\Front\FavoritesController::class, 'addToFavorites'])
            ->middleware('auth:sanctum')
            ->name('api.muzibu.ai.favorite.add');

        // Playlist'e şarkı ekleme (toplu - Auth required)
        Route::post('/playlist/{id}/add-songs', [PlaylistController::class, 'aiAddSongs'])
            ->middleware('auth:sanctum')
            ->name('api.muzibu.ai.playlist.add-songs');

        // 🔒 Play actions (song, playlist, album, radio - Premium required)
        Route::post('/play/{type}/{id}', [\Modules\Muzibu\App\Http\Controllers\Api\PlayController::class, 'play'])
            ->middleware(['auth:sanctum', \Modules\Muzibu\app\Http\Middleware\CheckPremiumSubscription::class])
            ->name('api.muzibu.ai.play')
            ->where('type', 'song|playlist|album|radio');

        // 🔒 Queue'ya ekle (toplu şarkı - Premium required)
        Route::post('/queue/add', [\Modules\Muzibu\App\Http\Controllers\Api\PlayController::class, 'addToQueue'])
            ->middleware(['auth:sanctum', \Modules\Muzibu\app\Http\Middleware\CheckPremiumSubscription::class])
            ->name('api.muzibu.ai.queue.add');

        // 🏢 OLD: AI için iş yeri playlist oluşturma (30-200 şarkı, Auth required)
        Route::post('/business/playlist/create', [PlaylistController::class, 'aiCreate'])
            ->middleware('auth:sanctum')
            ->name('api.muzibu.ai.business.playlist.create');
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
