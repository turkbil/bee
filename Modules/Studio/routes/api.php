<?php
// Modules/Studio/routes/api.php
use Illuminate\Support\Facades\Route;
// use Modules\Studio\App\Http\Controllers\Api\StudioApiController;

// API routes commented out until controller is ready
// Route::middleware(['auth:sanctum'])
//     ->prefix('api/v1/studio')
//     ->name('api.studio.')
//     ->group(function () {
//         // Widgetlar
//         Route::get('/widgets', [StudioApiController::class, 'getWidgets'])
//             ->name('widgets');
//         
//         // Widget içeriği getir
//         Route::get('/widgets/{tenantWidgetId}', [StudioApiController::class, 'getWidgetContent'])
//             ->name('widgets.content');
//         
//         // Temalar
//         Route::get('/themes', [StudioApiController::class, 'getThemes'])
//             ->name('themes');
//         
//         // Widget ekle
//         Route::post('/widgets/add', [StudioApiController::class, 'addWidget'])
//             ->name('widgets.add');
//         
//         // İçerik kaydetme
//         Route::post('/save-content', [StudioApiController::class, 'saveContent'])
//             ->name('save-content');
//     });

// Public API rotaları
// Route::middleware(['web', 'tenant'])
//     ->prefix('api/studio')
//     ->name('api.studio.public.')
//     ->group(function () {
//         // Widget içeriği getir (public)
//         Route::get('/widget/{tenantWidgetId}', [StudioApiController::class, 'getWidgetContent'])
//             ->name('widget.content');
//     });