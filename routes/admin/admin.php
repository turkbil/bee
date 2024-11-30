<?php

use Illuminate\Support\Facades\Route;

// Admin Dashboard
Route::get('/dashboard', function () {
    return view('admin.index');
})->name('admin.dashboard');

// Ek modüller için rotalar (örneğin kullanıcılar, ayarlar, raporlar)
