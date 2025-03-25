<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;

// Admin routes
require __DIR__.'/admin/web.php';

// Ana web routes - hem central hem tenant için çalışacak
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Normal üyelerin dashboard'a erişimi
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Test erişim rotası
    Route::get('/admin/test-access', [\App\Http\Controllers\TestAccessController::class, 'testAccess'])->name('test.access');
});

// Tenant medya dosyalarına erişim
Route::get('/storage/tenant{id}/{path}', [StorageController::class, 'tenantMedia'])
    ->where('id', '[0-9]+')
    ->where('path', '.*');

// Normal storage dosyalarına erişim
Route::get('/storage/{path}', [StorageController::class, 'publicStorage'])
    ->where('path', '(?!tenant)[/\w\.-]+')
    ->name('storage.public');

require __DIR__.'/auth.php';