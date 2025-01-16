<?php // routes/web.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

// Tüm domainler için tenant middleware uygulanıyor.
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
])->group(function () {
    // Ana sayfa
    Route::get('/', function () {
        abort_if(is_null(tenant()), 404); // Tenant yoksa 404 hatası döndür
        return 'Welcome to the application. Tenant ID: ' . tenant('id');
    })->name('home');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Profil işlemleri
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Auth işlemleri
    require __DIR__ . '/auth.php';
});
