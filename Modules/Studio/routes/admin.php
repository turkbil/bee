<?php
// Modules/Studio/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Admin\StudioController;
use Modules\Studio\App\Http\Controllers\Admin\AssetController;
use Modules\Studio\App\Http\Livewire\Admin\StudioComponent;
use Modules\Studio\App\Http\Livewire\EditorComponent;
use Modules\Studio\App\Http\Livewire\WidgetManagerComponent;

// Admin rotaları
Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('studio')
            ->name('studio.')
            ->group(function () {
                Route::get('/', StudioComponent::class)
                ->middleware('module.permission:studio,view')
                ->name('index');

                // Editor
                Route::get('/editor/{module}/{id}', EditorComponent::class)
                    ->middleware('module.permission:studio,view')
                    ->name('editor');
                
                // İçerik kaydetme
                Route::post('/save/{module}/{id}', [StudioController::class, 'save'])
                    ->middleware('module.permission:studio,update')
                    ->name('save');
                
                // Widgetlar
                Route::get('/widgets', WidgetManagerComponent::class)
                    ->middleware('module.permission:studio,view')
                    ->name('widgets');
                
                // API endpoint'leri
                Route::get('/api/widgets', [StudioController::class, 'getWidgets'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.widgets');
                
                // Varlık yükleme
                Route::post('/api/assets/upload', [StudioController::class, 'uploadAssets'])
                    ->middleware('module.permission:studio,update')
                    ->name('api.assets.upload');
                
                // Asset Controller Rotaları
                Route::post('/api/assets', [AssetController::class, 'upload'])
                    ->middleware('module.permission:studio,update')
                    ->name('api.assets');
                
                Route::get('/api/assets', [AssetController::class, 'index'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.assets.index');
                
                Route::get('/api/assets/optimize/{path}', [AssetController::class, 'optimize'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.assets.optimize');
                
                // Kaynakları yayınla
                Route::get('/publish-resources', [StudioController::class, 'publishResources'])
                    ->middleware('module.permission:studio,update')
                    ->name('publish-resources');

                // Blokları getir
                Route::get('/api/blocks', [StudioController::class, 'getBlocks'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.blocks');
            });
    });