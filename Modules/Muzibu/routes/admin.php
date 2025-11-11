<?php
// Modules/Muzibu/routes/admin.php
use Illuminate\Support\Facades\Route;

// Admin rotaları - Livewire Pattern
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('muzibu')
            ->name('muzibu.')
            ->group(function () {
                // Muzibu Dashboard - Songs Index (direkt)
                Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\SongController::class, 'index'])
                    ->middleware('module.permission:muzibu,view')
                    ->name('index');

                // Songs - Livewire
                Route::prefix('song')
                    ->name('song.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\SongController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\SongController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Artists - Livewire
                Route::prefix('artist')
                    ->name('artist.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\ArtistController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\ArtistController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Genres - Livewire
                Route::prefix('genre')
                    ->name('genre.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\GenreController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\GenreController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Albums - Livewire
                Route::prefix('album')
                    ->name('album.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\AlbumController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\AlbumController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Playlists - Livewire
                Route::prefix('playlist')
                    ->name('playlist.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');

                        // AJAX API endpoints (önce tanımla, conflict önleme)
                        Route::get('/api/{id}/info', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'getPlaylistInfo'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.info');

                        Route::get('/api/{id}/available', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'getAvailableSongs'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.available');

                        Route::get('/api/{id}/selected', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'getSelectedSongs'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.selected');

                        Route::post('/api/{id}/add', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'addSongs'])
                            ->middleware('module.permission:muzibu,update')
                            ->name('api.add');

                        Route::post('/api/{id}/remove', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'removeSongs'])
                            ->middleware('module.permission:muzibu,update')
                            ->name('api.remove');

                        Route::post('/api/{id}/reorder', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'reorderSongs'])
                            ->middleware('module.permission:muzibu,update')
                            ->name('api.reorder');

                        // Playlist şarkı yönetim sayfası (en sona koy)
                        Route::get('/songs/{id}', [\Modules\Muzibu\App\Http\Controllers\Admin\PlaylistController::class, 'manageSongs'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('songs');
                    });

                // Sectors - Livewire
                Route::prefix('sector')
                    ->name('sector.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\SectorController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\SectorController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Radios - Livewire
                Route::prefix('radio')
                    ->name('radio.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\RadioController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\RadioController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });
            });
    });
