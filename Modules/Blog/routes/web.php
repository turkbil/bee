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
