<?php
// Modules/Studio/routes/api.php
use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Api\StudioApiController;

// API rotaları
Route::middleware(['auth:sanctum'])
    ->prefix('api/v1/studio')
    ->name('api.studio.')
    ->group(function () {
        // Widgetlar
        Route::get('/widgets', [StudioApiController::class, 'getWidgets'])
            ->name('widgets');
        
        // Temalar
        Route::get('/themes', [StudioApiController::class, 'getThemes'])
            ->name('themes');
        
        // Widget ekle
        Route::post('/widgets/add', [StudioApiController::class, 'addWidget'])
            ->name('widgets.add');
        
        // Widget içeriği getir
        Route::get('/widgets/{id}', [StudioApiController::class, 'getWidgetContent'])
            ->name('widgets.get');
        
        // İçerik kaydetme
        Route::post('/save-content', [StudioApiController::class, 'saveContent'])
            ->name('save-content');
    });