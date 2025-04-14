<?php

use Illuminate\Support\Facades\Route;
use Modules\Studio\App\Http\Controllers\Admin\StudioController;
use Modules\Studio\App\Http\Livewire\StudioEditor;
use Modules\Studio\App\Http\Livewire\StudioWidgetManager;

Route::prefix('studio')
    ->name('studio.')
    ->middleware(['auth']) // Sadece auth kontrolü yapıyoruz, yetki kontrolünü kaldırdık
    ->group(function () {
        // Editör Livewire bileşeni
        Route::get('/editor/{module}/{id}', StudioEditor::class)
            ->name('editor');
            
        // İçerik kaydetme (AJAX)
        Route::post('/save/{module}/{id}', [StudioController::class, 'save'])
            ->name('save');
        
        // API endpointleri
        Route::get('/api/widgets', [StudioController::class, 'getWidgets'])
            ->name('api.widgets');
            
        Route::get('/api/themes', [StudioController::class, 'getThemes'])
            ->name('api.themes');
            
        Route::post('/api/assets/upload', [StudioController::class, 'uploadAssets'])
            ->name('api.assets.upload');

        // Yeni eklenen API endpoint'leri
        Route::get('/api/custom-blocks', [StudioController::class, 'getCustomBlocks'])
            ->name('api.custom-blocks');
    });