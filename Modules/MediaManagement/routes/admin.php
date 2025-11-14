<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - MediaManagement
|--------------------------------------------------------------------------
|
| MediaManagement modülü universal medya yönetimi sağlar.
| Kendi admin sayfası yoktur, diğer modüllere entegre edilir.
|
*/

Route::middleware(['auth', 'tenant'])->prefix('admin')->name('admin.')->group(function () {

    // MediaManagement index - Bilgilendirme sayfası
    Route::get('/mediamanagement', function () {
        return view('mediamanagement::admin.index');
    })->middleware('permission:mediamanagement.view')
      ->name('mediamanagement.index');

    // Thumbmaker Kullanım Kılavuzu
    Route::get('/mediamanagement/thumbmaker-guide', function () {
        return view('mediamanagement::admin.thumbmaker-guide');
    })->middleware('permission:mediamanagement.view')
      ->name('mediamanagement.thumbmaker-guide');

    // AI Image Generator
    Route::get('/mediamanagement/ai-generator', \Modules\MediaManagement\App\Http\Livewire\Admin\AiImageGeneratorComponent::class)
        ->middleware('permission:mediamanagement.view')
        ->name('mediamanagement.ai-generator');

    Route::post('/mediamanagement/library/upload', \Modules\MediaManagement\App\Http\Controllers\Admin\MediaLibraryUploadController::class)
        ->middleware('permission:mediamanagement.create')
        ->name('mediamanagement.library.upload');

    // Manual featured image upload - Livewire bypass for SSL issues
    Route::post('/mediamanagement/featured-upload', [\Modules\MediaManagement\App\Http\Livewire\Admin\UniversalMediaComponent::class, 'manualFeaturedUpload'])
        ->middleware('permission:mediamanagement.create')
        ->name('mediamanagement.featured.upload');

});
