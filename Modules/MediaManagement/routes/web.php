<?php

use Illuminate\Support\Facades\Route;
use Modules\MediaManagement\App\Http\Controllers\ThumbmakerController;
use App\Http\Middleware\InitializeTenancy;

/*
|--------------------------------------------------------------------------
| Web Routes - MediaManagement
|--------------------------------------------------------------------------
|
| MediaManagement modülü universal medya yönetimi sağlar.
| Frontend'de özel bir sayfası yoktur, modüller arası kullanılır.
|
*/

// Universal Thumbmaker - Public route (cache friendly, tenant-aware)
Route::middleware([InitializeTenancy::class, 'throttle:600,1'])
    ->get('/thumbmaker', [ThumbmakerController::class, 'generate'])
    ->name('thumbmaker');

// Frontend route'ları gerekirse buraya eklenebilir
