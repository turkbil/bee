<?php

use Illuminate\Support\Facades\Route;
use Modules\SeoManagement\App\Http\Controllers\Admin\SeoManagementController;
use Modules\SeoManagement\App\Http\Controllers\Admin\SeoAIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'tenant']], function () {
    Route::get('/seomanagement', [SeoManagementController::class, 'index'])
        ->name('admin.seomanagement.index');

    // SEO AI Routes - Complete System
    Route::prefix('seo/ai')->name('admin.seo.ai.')->group(function () {
        Route::post('analyze', [SeoAIController::class, 'analyze'])
            ->name('analyze');
        Route::post('generate', [SeoAIController::class, 'generateSeo'])
            ->name('generate');
        Route::post('suggestions', [SeoAIController::class, 'getSuggestions'])
            ->name('suggestions');
        Route::post('save', [SeoAIController::class, 'saveSeoData'])
            ->name('save');
        Route::get('history', [SeoAIController::class, 'getAnalysisHistory'])
            ->name('history');
    });
});