<?php
// Modules/Announcement/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Announcement\App\Http\Controllers\Front\AnnouncementController;

// Ön yüz rotaları
Route::middleware(['web'])
    ->group(function () {
        Route::get('/' . module_setting('announcement', 'routes.index_slug', 'announcements'), [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/' . module_setting('announcement', 'routes.show_slug', 'announcement') . '/{slug}', [AnnouncementController::class, 'show'])->name('announcements.show');
    });