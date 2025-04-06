<?php
// Modules/Page/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Page\App\Http\Livewire\PageComponent;
use Modules\Page\App\Http\Livewire\PageManageComponent;
use Modules\Page\Http\Controllers\PageFrontController;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('page')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageComponent::class)
                    ->middleware('module.permission:page,view')
                    ->name('index');
                Route::get('/manage/{id?}', PageManageComponent::class)
                    ->middleware('module.permission:page,update')
                    ->name('manage');
            });
    });


// Ön yüz rotaları
Route::middleware(['web'])
    ->name('pages.')
    ->prefix('pages')
    ->group(function () {
        Route::get('/', [PageFrontController::class, 'index'])->name('index');
        Route::get('/{slug}', [PageFrontController::class, 'show'])->name('show');
    });