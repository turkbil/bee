<?php
// Modules/Announcement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Announcement\App\Http\Livewire\Admin\AnnouncementComponent;
use Modules\Announcement\App\Http\Livewire\Admin\AnnouncementManageComponent;

// Admin rotaları
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('announcement')
            ->name('announcement.')
            ->group(function () {
                Route::get('/', AnnouncementComponent::class)
                    ->middleware('module.permission:announcement,view')
                    ->name('index');
                Route::get('/manage/{id?}', AnnouncementManageComponent::class)
                    ->middleware('module.permission:announcement,update')
                    ->name('manage');
            });
    });