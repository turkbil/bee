<?php
// Modules/Announcement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Announcement\App\Http\Controllers\Front\AnnouncementController;

// Ön yüz rotaları - DynamicRouteService tarafından yönetiliyor
// NOT: Statik route'lar kaldırıldı çünkü dinamik route sistemi ile çakışıyordu
// Tüm announcement route'ları artık DynamicRouteService üzerinden yönetiliyor
// Route::middleware(['web'])
//     ->group(function () {
//         Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
//         Route::get('/announcements/{slug}', [AnnouncementController::class, 'show'])->name('announcements.show');
//     });