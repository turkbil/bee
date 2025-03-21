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
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Medya dosyalarına erişim için route tanımı
Route::get('/storage/tenant{id}/{mediaId}/{filename}', [StorageController::class, 'tenantMedia'])
    ->where('id', '[0-9]+')
    ->where('mediaId', '[0-9]+')
    ->where('filename', '.*');

require __DIR__.'/auth.php';