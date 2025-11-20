<?php
// Modules/Blog/routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Blog\App\Http\Controllers\Front\BlogController;

// ⚠️  FRONTEND ROUTES DynamicRouteService TARAFINDAN YÖNETİLİYOR
// Bu dosyada sadece özel route'lar (homeblog gibi) tanımlanmalı
// Normal content route'ları (index, show) DynamicRouteService'den geliyor

// Tag route'ları
Route::get('blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('{locale}/blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag.localized');

// Category route'ları
Route::get('blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('{locale}/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category.localized');

// Infinity scroll API
Route::get('api/blog/load-more', [BlogController::class, 'loadMore'])->name('blog.load-more');
