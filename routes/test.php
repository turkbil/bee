<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Test & Development Routes
|--------------------------------------------------------------------------
|
| Test route'lar1 buraya eklenir. Production'da bu dosya y�klenmez.
|
*/

// ========================================
// >� SALES SHOWCASE - B2B HERO DESIGNS
// ========================================

Route::middleware('tenant')->group(function () {
    // DELUXE INDUSTRIAL - Design Playground
    Route::view('design/deluxe-industrial/v1', 'design.deluxe-industrial.v1')->name('design.deluxe-industrial.v1');
    Route::view('design/deluxe-industrial/v2', 'design.deluxe-industrial.v2')->name('design.deluxe-industrial.v2');
    Route::view('design/deluxe-industrial/v3', 'design.deluxe-industrial.v3')->name('design.deluxe-industrial.v3');
    Route::view('design/deluxe-industrial/v4', 'design.deluxe-industrial.v4')->name('design.deluxe-industrial.v4');
    Route::view('design/deluxe-industrial/v5', 'design.deluxe-industrial.v5')->name('design.deluxe-industrial.v5');
    Route::view('design/deluxe-industrial/v6', 'design.deluxe-industrial.v6')->name('design.deluxe-industrial.v6');
    Route::view('design/deluxe-industrial/v7', 'design.deluxe-industrial.v7')->name('design.deluxe-industrial.v7');
    Route::view('design/deluxe-industrial/v8', 'design.deluxe-industrial.v8')->name('design.deluxe-industrial.v8');
    Route::view('design/deluxe-industrial/v9', 'design.deluxe-industrial.v9')->name('design.deluxe-industrial.v9');
    Route::view('design/deluxe-industrial/v10', 'design.deluxe-industrial.v10')->name('design.deluxe-industrial.v10');
    Route::view('design/deluxe-industrial/v11', 'design.deluxe-industrial.v11')->name('design.deluxe-industrial.v11');
    Route::view('design/deluxe-industrial/v12', 'design.deluxe-industrial.v12')->name('design.deluxe-industrial.v12');
    Route::view('design/deluxe-industrial/v13', 'design.deluxe-industrial.v13')->name('design.deluxe-industrial.v13');

    // SERVICE HUB - Horizontal Service Slider
    Route::view('design/service-hub', 'design.service-hub')->name('design.service-hub');

    // HERO SLIDER - Homepage Hero as Horizontal Slider
    Route::view('design/hero-slider', 'design.hero-slider')->name('design.hero-slider');

    // BLOG LAYOUTS - News Magazine Style Designs
    Route::view('design/blog-layouts/v1', 'design.blog-layouts.v1')->name('design.blog-layouts.v1');
    Route::view('design/blog-layouts/v2', 'design.blog-layouts.v2')->name('design.blog-layouts.v2');
    Route::view('design/blog-layouts/v3', 'design.blog-layouts.v3')->name('design.blog-layouts.v3');

    // ========================================
    // HERO WIDGET TEST - Widget Render Test
    // ========================================
    Route::get('test/hero-widget', function () {
        return view('test.hero-widget');
    })->name('test.hero-widget');

    // ========================================
    // ADMIN UI DESIGN ALTERNATIVES - Product Manage
    // ========================================
    Route::middleware('auth')->group(function () {
        // Ana seçici sayfa
        Route::view('admin/ui-test', 'test.admin.ui-selector')->name('test.admin.ui.selector');

        // Tasarım alternatifleri
        Route::view('admin/ui-test/v1', 'test.admin.design-v1')->name('test.admin.ui.v1');
        Route::view('admin/ui-test/v2', 'test.admin.design-v2')->name('test.admin.ui.v2');
        Route::view('admin/ui-test/v3', 'test.admin.design-v3')->name('test.admin.ui.v3');
        Route::view('admin/ui-test/v4', 'test.admin.design-v4')->name('test.admin.ui.v4');
    });
});
