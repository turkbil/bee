<?php
// Modules/Muzibu/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Muzibu\app\Http\Controllers\Front\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Front\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Front\GenreController;
use Modules\Muzibu\app\Http\Controllers\Front\SectorController;
use Modules\Muzibu\app\Http\Controllers\Front\FavoritesController;
use Modules\Muzibu\app\Http\Controllers\Front\MyPlaylistsController;
use Modules\Muzibu\app\Http\Controllers\Front\RadioController;

// 🚀 SPA API ENDPOINTS (JSON Response) - Muzibu Modülü İçin
// Sadece muzibu.com.tr domain'i için geçerli
Route::middleware(['web', 'tenant'])
    ->domain('muzibu.com.tr')
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
    });
