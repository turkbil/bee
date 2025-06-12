<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Page\App\Http\Controllers\Front\PageController;
use App\Services\DynamicRouteService;

// Admin routes
require __DIR__.'/admin/web.php';

// Ana sayfa route'u
Route::middleware([InitializeTenancy::class])->get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');

// Test route'ları - dinamik route'lardan ÖNCE olmalı
require __DIR__.'/test.php';

// Dinamik modül route'ları - her domain için ayrı pattern'lar
Route::middleware([InitializeTenancy::class, 'web'])
    ->group(function () {
        // Catch-all route'ları - runtime'da resolve edilecek
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '[a-zA-Z0-9\-_çğıöşüÇĞIÖŞÜ]+');
        
        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where(['slug1' => '[a-zA-Z0-9\-_çğıöşüÇĞIÖŞÜ]+', 'slug2' => '[a-zA-Z0-9\-_çğıöşüÇĞIÖŞÜ]+']);
    });

// Normal üyelerin dashboard'a erişimi
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tenant medya dosyalarına erişim
Route::get('/storage/tenant{id}/{path}', [StorageController::class, 'tenantMedia'])
    ->where('id', '[0-9]+')
    ->where('path', '.*');

// Normal storage dosyalarına erişim
Route::get('/storage/{path}', [StorageController::class, 'publicStorage'])
    ->where('path', '(?!tenant)[/\w\.-]+')
    ->name('storage.public');

// 403 hata sayfası rotası
Route::get('/403', function () {
    return response()->view('errors.403', [], 403);
})->name('errors.403');

// CSRF token yenileme rotası
Route::get('/csrf-refresh', function () {
    return csrf_token();
})->name('csrf.refresh')->middleware('web');

require __DIR__.'/auth.php';