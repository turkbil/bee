<?php
// Modules/Muzibu/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Muzibu\App\Http\Livewire\Admin\MuzibuComponent;
use Modules\Muzibu\App\Http\Livewire\Admin\MuzibuManageComponent;
use Modules\Muzibu\App\Http\Livewire\Admin\MuzibuCategoryComponent;
use Modules\Muzibu\App\Http\Livewire\Admin\MuzibuCategoryManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('muzibu')
            ->name('muzibu.')
            ->group(function () {
                Route::get('/', MuzibuComponent::class)
                    ->middleware('module.permission:muzibu,view')
                    ->name('index');

                Route::get('/manage/{id?}', MuzibuManageComponent::class)
                    ->middleware('module.permission:muzibu,update')
                    ->name('manage');

                // Kategori route'ları
                Route::prefix('category')
                    ->name('category.')
                    ->group(function () {
                        Route::get('/', MuzibuCategoryComponent::class)
                            ->middleware('module.permission:muzibu,view')
                            ->name('index');

                        Route::get('/manage/{id?}', MuzibuCategoryManageComponent::class)
                            ->middleware('module.permission:muzibu,update')
                            ->name('manage');
                    });
            });
    });
