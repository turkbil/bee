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
    })->name('mediamanagement.index');


    Route::post('/mediamanagement/library/upload', \Modules\MediaManagement\App\Http\Controllers\Admin\MediaLibraryUploadController::class)
        ->name('mediamanagement.library.upload');

});
