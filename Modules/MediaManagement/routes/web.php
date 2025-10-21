<?php

use Illuminate\Support\Facades\Route;
use Modules\MediaManagement\App\Http\Controllers\ThumbmakerController;

/*
|--------------------------------------------------------------------------
| Web Routes - MediaManagement
|--------------------------------------------------------------------------
|
| MediaManagement modülü universal medya yönetimi sağlar.
| Frontend'de özel bir sayfası yoktur, modüller arası kullanılır.
|
*/

// Universal Thumbmaker - Public route (cache friendly)
Route::get('/thumbmaker', [ThumbmakerController::class, 'generate'])
    ->name('thumbmaker')
    ->middleware('throttle:600,1'); // 600 istek/dakika limit

// Frontend route'ları gerekirse buraya eklenebilir
