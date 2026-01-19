<?php
// Modules/Service/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Service\App\Http\Livewire\Admin\ServiceComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceManageComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceCategoryComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceCategoryManageComponent;

// Admin rotaları
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('service')
            ->name('service.')
            ->group(function () {
                Route::get('/', ServiceComponent::class)
                    ->middleware('module.permission:service,view')
                    ->name('index');

                Route::get('/manage/{id?}', ServiceManageComponent::class)
                    ->middleware('module.permission:service,update')
                    ->name('manage');

                // Kategori route'ları
                Route::prefix('category')
                    ->name('category.')
                    ->group(function () {
                        Route::get('/', ServiceCategoryComponent::class)
                            ->middleware('module.permission:service,view')
                            ->name('index');

                        Route::get('/manage/{id?}', ServiceCategoryManageComponent::class)
                            ->middleware('module.permission:service,update')
                            ->name('manage');
                    });
            });
    });
