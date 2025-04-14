<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Api\StudioApiController;

Route::middleware(['auth:api'])
    ->prefix('v1/studio')
    ->name('studio.')
    ->group(function () {
        // Widget ve tema bilgilerini getir
        Route::get('/widgets', [StudioApiController::class, 'getWidgets'])
            ->name('widgets');
            
        Route::get('/themes', [StudioApiController::class, 'getThemes'])
            ->name('themes');
        
        // Widget içeriğini getir
        Route::get('/widget/{widgetId}', [StudioApiController::class, 'getWidgetContent'])
            ->name('widget.content');
        
        // Modül ayarlarını getir/kaydet
        Route::get('/settings/{module}/{id}', [StudioApiController::class, 'getSettings'])
            ->name('settings');
            
        Route::post('/settings', [StudioApiController::class, 'saveSettings'])
            ->name('settings.save');
        
        // İçerik kaydet
        Route::post('/content', [StudioApiController::class, 'saveContent'])
            ->name('content.save');
    });