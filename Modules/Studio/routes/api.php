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
        
        // İçerik kaydetme
        Route::post('/save-content', [StudioApiController::class, 'saveContent'])
            ->name('save-content');
    });