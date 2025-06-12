<?php
// Modules/Announcement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Announcement\App\Http\Controllers\Front\AnnouncementController;

// Ön yüz rotaları - DynamicRouteService tarafından yönetiliyor
// Route::middleware(['web'])
//     ->group(function () {
//         Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
//         Route::get('/announcements/{slug}', [AnnouncementController::class, 'show'])->name('announcements.show');
//     });