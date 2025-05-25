<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Api\StudioApiController;

Route::prefix('admin/studio')->middleware(['web', 'auth'])->group(function () {
    Route::prefix('api')->group(function () {
        Route::get('widgets', [StudioApiController::class, 'getWidgets']);
        Route::get('blocks', [StudioApiController::class, 'getBlocks']);
        Route::get('widget-content/{tenantWidgetId}', [StudioApiController::class, 'getWidgetContent']);
        Route::get('module-widget/{moduleId}', [StudioApiController::class, 'getModuleWidget']);
    });
});