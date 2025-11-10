<?php
// Modules/ReviewSystem/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewSystemComponent;
use Modules\ReviewSystem\App\Http\Livewire\Admin\ReviewSystemManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('reviewsystem')
            ->name('reviewsystem.')
            ->group(function () {
                Route::get('/', ReviewSystemComponent::class)
                    ->middleware('module.permission:reviewsystem,view')
                    ->name('index');

                Route::get('/manage/{id?}', ReviewSystemManageComponent::class)
                    ->middleware('module.permission:reviewsystem,update')
                    ->name('manage');
            });
    });
