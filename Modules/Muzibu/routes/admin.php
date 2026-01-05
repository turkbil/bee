<?php
// Modules/Muzibu/routes/admin.php
use Illuminate\Support\Facades\Route;

// Admin rotaları - Livewire Pattern
// 'muzibu.admin' middleware: Central domain'den erişildiğinde Muzibu tenant context'ini başlatır
Route::middleware(['admin', 'tenant', \Modules\Muzibu\App\Http\Middleware\InitializeMuzibuAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('muzibu')
            ->name('muzibu.')
            ->group(function () {
                // Muzibu Dashboard
                Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\DashboardController::class, 'index'])
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

                        // Bulk HLS Convert
                        Route::get('/bulk-convert', \Modules\Muzibu\App\Http\Livewire\Admin\SongBulkConvertComponent::class)
                            ->middleware('module.permission:muzibu,create')
                            ->name('bulk-convert');
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

                        // Bulk Upload
                        Route::get('/bulk-upload/{id}', \Modules\Muzibu\App\Http\Livewire\Admin\AlbumBulkUploadComponent::class)
                            ->middleware('module.permission:muzibu,create')
                            ->name('bulk-upload');
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

                // HLS Streaming Dokümantasyonu
                Route::get('/docs/hls-streaming', function () {
                    return view('muzibu::admin.docs.hls-streaming');
                })
                    ->middleware('module.permission:muzibu,view')
                    ->name('docs.hls-streaming');

                // Corporate Accounts - Kurumsal Hesaplar
                Route::prefix('corporate')
                    ->name('corporate.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\CorporateController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\CorporateController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');

                        Route::get('/usage', [\Modules\Muzibu\App\Http\Controllers\Admin\CorporateController::class, 'usage'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('usage');
                    });

                // Corporate Spots - Kurumsal Anonslari
                Route::prefix('spot')
                    ->name('spot.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\SpotController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\SpotController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Certificates - Sertifikalar
                Route::prefix('certificate')
                    ->name('certificate.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\CertificateController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', [\Modules\Muzibu\App\Http\Controllers\Admin\CertificateController::class, 'manage'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('manage');
                    });

                // Stats - Dinleme İstatistikleri
                Route::prefix('stats')
                    ->name('stats.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\StatsController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');
                    });

                // AI Cover - AI Görsel Üretimi
                Route::prefix('ai-cover')
                    ->name('ai-cover.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\AICoverController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');
                    });

                // User Playlist - Kullanıcı Listeleri
                Route::prefix('user-playlist')
                    ->name('user-playlist.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\UserPlaylistController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');
                    });

                // Listening History - Dinleme Geçmişi
                Route::prefix('listening-history')
                    ->name('listening-history.')
                    ->group(function () {
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\ListeningHistoryController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');
                    });

                // Abuse Reports - Suistimal Raporları
                Route::prefix('abuse-reports')
                    ->name('abuse.')
                    ->group(function () {
                        // API endpoints - ÖNCELİKLİ (/{id} route'undan önce tanımlanmalı!)
                        Route::get('/api/list', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'apiList'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.list');

                        Route::get('/api/stats', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'apiStats'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.stats');

                        Route::get('/api/timeline/{userId}', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'apiTimeline'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.timeline');

                        Route::get('/api/users', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'apiUsers'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('api.users');

                        // Liste sayfası
                        Route::get('/', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'index'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        // Toplu tarama başlat
                        Route::post('/scan', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'startScan'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('scan');

                        // Tek kullanıcı tara
                        Route::post('/scan-user/{userId}', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'scanUser'])
                            ->middleware('module.permission:muzibu,create')
                            ->name('scan.user');

                        // Raporu incele/aksiyon al
                        Route::post('/{id}/review', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'review'])
                            ->middleware('module.permission:muzibu,update')
                            ->name('review');

                        // Detay sayfası (Timeline ile) - EN SONA! (wildcard {id} route)
                        Route::get('/{id}', [\Modules\Muzibu\App\Http\Controllers\Admin\AbuseReportController::class, 'show'])
                            ->middleware('module.permission:muzibu,view')
                            ->name('show');
                    });
            });
    });
