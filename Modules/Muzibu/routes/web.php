<?php
// Modules/Muzibu/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Muzibu\app\Http\Controllers\Front\PlaylistController;
use Modules\Muzibu\app\Http\Controllers\Front\AlbumController;
use Modules\Muzibu\app\Http\Controllers\Front\ArtistController;
use Modules\Muzibu\app\Http\Controllers\Front\GenreController;
use Modules\Muzibu\app\Http\Controllers\Front\SectorController;
use Modules\Muzibu\app\Http\Controllers\Front\FavoritesController;
use Modules\Muzibu\app\Http\Controllers\Front\MyPlaylistsController;
use Modules\Muzibu\app\Http\Controllers\Front\RadioController;
use Modules\Muzibu\app\Http\Controllers\Front\SongController;

// 🎵 FRONTEND ROUTES (Blade Views with SPA)
// Loaded via ServiceProvider with:
// - Domain filter (muzibu.com.tr, www.muzibu.com.tr)
// - Middleware: ['web', InitializeTenancyByDomain]
// - API routes in separate file: routes/api.php

// Home
Route::get('/', [\Modules\Muzibu\app\Http\Controllers\Front\HomeController::class, 'index'])->name('muzibu.home');
// Playlists
Route::get('/playlists', [PlaylistController::class, 'index'])->name('muzibu.playlists.index');
Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('muzibu.playlists.show');

// Albums
Route::get('/albums', [AlbumController::class, 'index'])->name('muzibu.albums.index');
Route::get('/albums/{slug}', [AlbumController::class, 'show'])->name('muzibu.albums.show');

// Artists
Route::get('/artists', [ArtistController::class, 'index'])->name('muzibu.artists.index');
Route::get('/artists/{slug}', [ArtistController::class, 'show'])->name('muzibu.artists.show');

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

// 🔍 Search Results Page (Livewire) - Moved to main routes/web.php (priority route)
// Route moved to avoid catch-all conflicts - same pattern as Cart module
