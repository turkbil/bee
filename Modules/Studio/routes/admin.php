<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Admin\StudioController;
use Modules\Studio\App\Http\Controllers\Admin\AssetController;
use Modules\Studio\App\Http\Livewire\Admin\StudioIndexComponent;
use Modules\Studio\App\Http\Livewire\EditorComponent;

Route::middleware(['web', 'auth', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('studio')
            ->name('studio.')
            ->group(function () {
                Route::get('/', StudioIndexComponent::class)
                    ->middleware('module.permission:studio,view')
                    ->name('index');
                
                Route::get('/editor/{module}/{id}', EditorComponent::class)
                    ->middleware('module.permission:studio,view')
                    ->name('editor');
                
                Route::post('/save/{module}/{id}', [StudioController::class, 'save'])
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
            });
    });