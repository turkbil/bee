<?php
// Modules/Muzibu/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Muzibu\app\Http\Controllers\Front\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Front\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Front\GenreController;
use Modules\Muzibu\app\Http\Controllers\Front\SectorController;
use Modules\Muzibu\app\Http\Controllers\Front\FavoritesController;
use Modules\Muzibu\app\Http\Controllers\Front\MyPlaylistsController;
use Modules\Muzibu\app\Http\Controllers\Front\RadioController;
use Modules\Muzibu\app\Http\Controllers\Front\SongController;

// 🚀 SPA API ENDPOINTS (JSON Response) - Muzibu Modülü İçin
Route::middleware(['web', 'tenant'])
    ->prefix('api')
    ->group(function () {
        // Playlists
        Route::get('/playlists', [PlaylistController::class, 'apiIndex'])->name('muzibu.api.playlists.index');
        Route::get('/playlists/{slug}', [PlaylistController::class, 'apiShow'])->name('muzibu.api.playlists.show');

        // Albums
        Route::get('/albums', [AlbumController::class, 'apiIndex'])->name('muzibu.api.albums.index');
        Route::get('/albums/{slug}', [AlbumController::class, 'apiShow'])->name('muzibu.api.albums.show');

        // Genres
        Route::get('/genres', [GenreController::class, 'apiIndex'])->name('muzibu.api.genres.index');
        Route::get('/genres/{slug}', [GenreController::class, 'apiShow'])->name('muzibu.api.genres.show');

        // Sectors
        Route::get('/sectors', [SectorController::class, 'apiIndex'])->name('muzibu.api.sectors.index');
        Route::get('/sectors/{slug}', [SectorController::class, 'apiShow'])->name('muzibu.api.sectors.show');

        // User Library
        Route::get('/favorites', [FavoritesController::class, 'apiIndex'])->name('muzibu.api.favorites');
        Route::get('/my-playlists', [MyPlaylistsController::class, 'apiIndex'])->name('muzibu.api.my-playlists');

        // Radios
        Route::get('/radios', [RadioController::class, 'apiIndex'])->name('muzibu.api.radios');

        // Songs
        Route::get('/songs/{slug}', [SongController::class, 'apiShow'])->name('muzibu.api.songs.show');
    });

// 🎵 FRONTEND ROUTES (Blade Views with SPA) - Muzibu Modülü
Route::middleware(['web', 'tenant'])
    ->group(function () {
        // Home
        Route::get('/', [\Modules\Muzibu\app\Http\Controllers\Front\HomeController::class, 'index'])->name('muzibu.home');

        // Playlists
        Route::get('/playlists', [PlaylistController::class, 'index'])->name('muzibu.playlists.index');
        Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('muzibu.playlists.show');

        // Albums
        Route::get('/albums', [AlbumController::class, 'index'])->name('muzibu.albums.index');
        Route::get('/albums/{slug}', [AlbumController::class, 'show'])->name('muzibu.albums.show');

        // Genres
        Route::get('/genres', [GenreController::class, 'index'])->name('muzibu.genres.index');
        Route::get('/genres/{slug}', [GenreController::class, 'show'])->name('muzibu.genres.show');

        // Sectors
        Route::get('/sectors', [SectorController::class, 'index'])->name('muzibu.sectors.index');
        Route::get('/sectors/{slug}', [SectorController::class, 'show'])->name('muzibu.sectors.show');

        // Songs
        Route::get('/songs/{slug}', [SongController::class, 'show'])->name('muzibu.songs.show');

        // Radios
        Route::get('/radios', [RadioController::class, 'index'])->name('muzibu.radios.index');

        // User Library
        Route::get('/favorites', [FavoritesController::class, 'index'])->name('muzibu.favorites');
        Route::get('/my-playlists', [MyPlaylistsController::class, 'index'])->name('muzibu.my-playlists');

        // Playlist Management (Edit Page)
        Route::get('/playlist/{id}/edit', [MyPlaylistsController::class, 'edit'])->name('muzibu.playlist.edit');
    });

// 🔍 Search Results Page (Livewire) - Moved to main routes/web.php (priority route)
// Route moved to avoid catch-all conflicts - same pattern as Cart module
