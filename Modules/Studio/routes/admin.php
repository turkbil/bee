<?php
// Modules/Studio/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Admin\StudioController;
use Modules\Studio\App\Http\Controllers\Admin\StudioAssetsController;
use Modules\Studio\App\Http\Livewire\StudioEditor;

// Admin rotaları
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('studio')
            ->name('studio.')
            ->group(function () {
                Route::get('/editor/{module}/{id}', StudioEditor::class)
                    ->middleware('module.permission:studio,view')
                    ->name('editor');
                
                Route::post('/save/{module}/{id}', [StudioController::class, 'save'])
                    ->middleware('module.permission:studio,update')
                    ->name('save');
                
                Route::get('/widgets', [StudioController::class, 'widgets'])
                    ->middleware('module.permission:studio,view')
                    ->name('widgets');
                
                Route::get('/api/widgets', [StudioController::class, 'getWidgets'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.widgets');
                
                Route::get('/api/themes', [StudioController::class, 'getThemes'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.themes');
                
                // Varlık yükleme rotası
                Route::post('/api/assets/upload', [StudioController::class, 'uploadAssets'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.assets.upload');
            });
    });