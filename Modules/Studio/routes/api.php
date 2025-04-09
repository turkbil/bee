<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Api\StudioApiController;

Route::middleware(['auth:sanctum'])->prefix('v1/studio')->group(function () {
    Route::get('/widgets', [StudioApiController::class, 'getWidgets']);
    Route::get('/themes', [StudioApiController::class, 'getThemes']);
    Route::post('/save-content', [StudioApiController::class, 'saveContent']);
});