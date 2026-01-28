<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Pages Routes - Google Ads Campaigns
|--------------------------------------------------------------------------
|
| Bu dosya Google Ads kampanyaları için optimize edilmiş landing page
| route'larını içerir. SEO-friendly URL'ler kullanılır.
|
| Klasör Yapısı: landing/{category}/{product}/{campaign_number}/index.blade.php
| URL Pattern: /{seo-keyword}
|
*/

Route::name('landing.')->group(function() {

    // ========================================
    // MUZIBU LANDING PAGE
    // ========================================

    // Ana Landing Page - Telif Hakları Uyarısı (Sidebar ile)
    Route::get('/landing', function() {
        return view('landing.muzibu.main.1.index');
    })->name('muzibu.main.1');

    // Ana Landing Page - Minimal (Sidebar yok)
    Route::get('/landing2', function() {
        return view('landing.muzibu.main.2.index');
    })->name('muzibu.main.2');

    // Ana Landing Page - Modern Design (Sidebar yok)
    Route::get('/landing3', function() {
        return view('landing.muzibu.main.3.index');
    })->name('muzibu.main.3');

    // Ana Landing Page - Playful/Eğlenceli (Sidebar ile)
    Route::get('/landing4', function() {
        return view('landing.muzibu.main.4.index');
    })->name('muzibu.main.4');

    // Ana Landing Page - Corporate/Kurumsal (Sidebar ile)
    Route::get('/landing5', function() {
        return view('landing.muzibu.main.5.index');
    })->name('muzibu.main.5');

    // ========================================
    // TRANSPALET F4 KAMPANYALARI
    // ========================================

    // Kampanya #1 - Google Ads Kasım 2025
    // Parametreli URL'ler: /elektrikli-transpalet/1, /elektrikli-transpalet/2, ... /elektrikli-transpalet/10
    Route::get('/elektrikli-transpalet/{id?}', function($id = null) {
        return view('landing.transpalet.f4.1.index');
    })->name('transpalet.f4.1')->where('id', '[1-9]|10');

    // Gelecek Kampanyalar (Yorum satırında hazır)
    // Kampanya #2 - Black Friday 2025
    // Route::get('/akulu-transpalet', fn() => view('landing.transpalet.f4.2.index'))->name('transpalet.f4.2');

    // Kampanya #3 - Yılbaşı 2025
    // Route::get('/transpalet-fiyatlari', fn() => view('landing.transpalet.f4.3.index'))->name('transpalet.f4.3');

    // Kampanya #4 - Teknik Keyword
    // Route::get('/li-ion-transpalet', fn() => view('landing.transpalet.f4.4.index'))->name('transpalet.f4.4');

    // ========================================
    // DİĞER ÜRÜNLER İÇİN HAZIR YAPI
    // ========================================

    // Transpalet F5
    // Route::get('/premium-transpalet', fn() => view('landing.transpalet.f5.1.index'))->name('transpalet.f5.1');

    // Forklift
    // Route::get('/elektrikli-forklift', fn() => view('landing.forklift.elektrikli.1.index'))->name('forklift.elektrikli.1');

    // Crane
    // Route::get('/hidrolik-vinc', fn() => view('landing.crane.hidrolik.1.index'))->name('crane.hidrolik.1');

});
