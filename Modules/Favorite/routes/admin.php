<?php
// Modules/Favorite/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteComponent;
use Modules\Favorite\App\Http\Livewire\Admin\FavoriteManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('favorite')
            ->name('favorite.')
            ->group(function () {
                Route::get('/', FavoriteComponent::class)
                    ->middleware('module.permission:favorite,view')
                    ->name('index');

                Route::get('/manage/{id?}', FavoriteManageComponent::class)
                    ->middleware('module.permission:favorite,update')
                    ->name('manage');
            });
    });
