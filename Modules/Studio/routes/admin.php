<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Admin\StudioController;
use Modules\Studio\App\Http\Controllers\Admin\AssetController;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('studio')
            ->name('studio.')
            ->group(function () {
                Route::get('/', [StudioController::class, 'index'])
                    ->middleware('module.permission:studio,view')
                    ->name('index');

                Route::get('/editor/{module}/{id}/{locale?}', [StudioController::class, 'editor'])
                    ->middleware('module.permission:studio,view')
                    ->name('editor');
                
                Route::post('/save/{module}/{id}/{locale?}', [StudioController::class, 'save'])
                    ->middleware('module.permission:studio,update')
                    ->name('save');
                
                Route::post('/api/assets/upload', [StudioController::class, 'uploadAssets'])
                    ->middleware('module.permission:studio,update')
                    ->name('api.assets.upload');
                
                Route::post('/api/assets', [AssetController::class, 'upload'])
                    ->middleware('module.permission:studio,update')
                    ->name('api.assets');
                
                Route::get('/api/assets', [AssetController::class, 'index'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.assets.index');
                
                Route::get('/api/assets/optimize/{path}', [AssetController::class, 'optimize'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.assets.optimize');
                
                Route::get('/publish-resources', [StudioController::class, 'publishResources'])
                    ->middleware('module.permission:studio,update')
                    ->name('publish-resources');

                Route::get('/api/blocks', [StudioController::class, 'getBlocks'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.blocks');

                // Web.php'den taşınan API rotaları
                Route::get('/api/widgets', [\Modules\Studio\App\Http\Controllers\Api\StudioApiController::class, 'getWidgets'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.widgets');
                Route::get('/api/widget-content/{tenantWidgetId}', [\Modules\Studio\App\Http\Controllers\Api\StudioApiController::class, 'getWidgetContent'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.widget-content');
                Route::get('/api/module-widget/{moduleId}', [\Modules\Studio\App\Http\Controllers\Api\StudioApiController::class, 'getModuleWidget'])
                    ->middleware('module.permission:studio,view')
                    ->name('api.module-widget');
            });
    });