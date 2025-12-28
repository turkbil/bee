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
use Modules\Muzibu\App\Http\Controllers\Front\DashboardController;
use Modules\Muzibu\App\Http\Controllers\Front\CorporateFrontController;

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
Route::get('/songs', [SongController::class, 'index'])->name('muzibu.songs.index');
Route::get('/songs/{slug}', [SongController::class, 'show'])->name('muzibu.songs.show');

// Radios
Route::get('/radios', [RadioController::class, 'index'])->name('muzibu.radios.index');

// Corporate Public Page (No Auth Required)
Route::get('/corporate', [CorporateFrontController::class, 'index'])->name('muzibu.corporate.index');
Route::get('/api/corporate', [CorporateFrontController::class, 'apiIndex'])->name('muzibu.corporate.api');

// User Library (Auth + Verified Required)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/muzibu/favorites', [FavoritesController::class, 'index'])->name('muzibu.favorites');
    Route::get('/muzibu/my-playlists', [MyPlaylistsController::class, 'index'])->name('muzibu.my-playlists');

    // Playlist Management (Edit Page)
    Route::get('/muzibu/playlist/{slug}/edit', [MyPlaylistsController::class, 'edit'])->name('muzibu.playlist.edit');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('muzibu.dashboard');
    Route::get('/api/dashboard', [DashboardController::class, 'apiIndex'])->name('muzibu.dashboard.api');
    Route::get('/muzibu/listening-history', [DashboardController::class, 'history'])->name('muzibu.listening-history');
    Route::get('/api/muzibu/listening-history', [DashboardController::class, 'apiHistory'])->name('muzibu.listening-history.api');

    // My Subscriptions (Abonelik Geçmişi)
    Route::get('/my-subscriptions', [DashboardController::class, 'subscriptions'])->name('muzibu.my-subscriptions');
    Route::get('/api/my-subscriptions', [DashboardController::class, 'apiSubscriptions'])->name('muzibu.my-subscriptions.api');

    // Corporate Routes (Frontend - Auth Required)
    Route::prefix('corporate')->name('muzibu.corporate.')->group(function () {
        Route::get('/dashboard', [CorporateFrontController::class, 'dashboard'])->name('dashboard');
        Route::get('/api/dashboard', [CorporateFrontController::class, 'apiDashboard'])->name('dashboard.api');
        Route::get('/join', [CorporateFrontController::class, 'join'])->name('join');
        Route::get('/api/join', [CorporateFrontController::class, 'apiJoin'])->name('join.api');
        Route::post('/join', [CorporateFrontController::class, 'doJoin'])->name('doJoin');
        Route::get('/my-corporate', [CorporateFrontController::class, 'myCorporate'])->name('my');
        Route::get('/api/my-corporate', [CorporateFrontController::class, 'apiMyCorporate'])->name('my.api');
        Route::post('/leave', [CorporateFrontController::class, 'leave'])->name('leave');
        Route::post('/regenerate-code', [CorporateFrontController::class, 'regenerateCode'])->name('regenerate');
        Route::post('/remove-member/{id}', [CorporateFrontController::class, 'removeMember'])->name('remove-member');
        Route::post('/update-branch/{id}', [CorporateFrontController::class, 'updateBranchName'])->name('update-branch');
        Route::post('/update-company-name', [CorporateFrontController::class, 'updateCompanyName'])->name('update-company-name');
        Route::post('/disband', [CorporateFrontController::class, 'disband'])->name('disband');
        Route::post('/create', [CorporateFrontController::class, 'createCorporate'])->name('create');
        Route::post('/check-code', [CorporateFrontController::class, 'checkCodeAvailability'])->name('check-code');

        // Corporate Subscription Management (Üyelikleri Yönet)
        Route::get('/subscriptions', [CorporateFrontController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/api/subscriptions', [CorporateFrontController::class, 'apiSubscriptions'])->name('subscriptions.api');
        Route::post('/subscriptions/purchase', [CorporateFrontController::class, 'purchaseSubscriptions'])->name('subscriptions.purchase');

        // Corporate Member Listening History (Üye Dinleme Geçmişi)
        Route::get('/member/{id}/history', [CorporateFrontController::class, 'memberHistory'])->name('member-history');
        Route::get('/api/member/{id}/history', [CorporateFrontController::class, 'apiMemberHistory'])->name('member-history.api');
    });

    // 🚀 SPA Compatible API Routes (/api/corporate/...)
    // SPA router uses '/api' + path format, so we need these routes
    Route::prefix('api/corporate')->group(function () {
        Route::get('/dashboard', [CorporateFrontController::class, 'apiDashboard'])->name('api.corporate.dashboard');
        Route::get('/join', [CorporateFrontController::class, 'apiJoin'])->name('api.corporate.join');
        Route::get('/my-corporate', [CorporateFrontController::class, 'apiMyCorporate'])->name('api.corporate.my');
        Route::get('/subscriptions', [CorporateFrontController::class, 'apiSubscriptions'])->name('api.corporate.subscriptions');
        Route::get('/member/{id}/history', [CorporateFrontController::class, 'apiMemberHistory'])->name('api.corporate.member-history');
    });
});

// 🔍 Search Results Page (Livewire) - Moved to main routes/web.php (priority route)
// Route moved to avoid catch-all conflicts - same pattern as Cart module

// 🎵 HLS Storage Files - CORS enabled (tenant-aware)
Route::get('/storage/tenant{tenantId}/muzibu/{path}', function ($tenantId, $path) {
    $filePath = storage_path("tenant{$tenantId}/app/public/muzibu/{$path}");
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    return response()->file($filePath, [
        'Content-Type' => mime_content_type($filePath),
        'Access-Control-Allow-Origin' => request()->header('Origin'),
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Range, Cookie',
        'Access-Control-Allow-Credentials' => 'true',
        'Cache-Control' => 'public, max-age=2592000',
    ]);
})->where('path', '.*');
