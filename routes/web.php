<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Page\App\Http\Controllers\Front\PageController;

// Admin routes
require __DIR__.'/admin/web.php';

// Ana web routes - hem central hem tenant için çalışacak
Route::middleware([InitializeTenancy::class])->get('/', [PageController::class, 'homepage'])->name('home');

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