<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\App\Http\Controllers\Front\ShopController;
use Modules\Shop\App\Http\Controllers\Front\ShopQuoteController;
use Modules\Shop\App\Http\Controllers\GoogleShoppingFeedController;

// GOOGLE SHOPPING FEED - Moved to routes/web.php for higher priority

// CART ROUTES (Sepet) - WILDCARD'DAN ÖNCE TANIMLANMALI!
Route::middleware(['web', 'tenant', 'locale.site', 'frontend.auto.seo'])
    ->prefix('shop')
    ->group(function () {
        // Cart Page - Sepet sayfası
        Route::get('/cart', \Modules\Shop\App\Http\Livewire\Front\CartPage::class)->name('shop.cart');

        // Checkout Page - Sipariş ver (şimdilik cart'a yönlendir)
        Route::get('/checkout', function () {
            return redirect()->route('shop.cart');
        })->name('shop.checkout');
    });

// DESIGN VERSION ROUTES (Test için farklı tasarımlar - Wildcard'dan önce tanımlanmalı!)
Route::middleware(['web', 'tenant', 'locale.site', 'frontend.auto.seo'])
    ->prefix('shop')
    ->group(function () {
        // V1 - Modern & Minimalist
        Route::get('/v1/{slug}', [ShopController::class, 'showV1'])->name('shop.v1');

        // V2 - E-ticaret Stili
        Route::get('/v2/{slug}', [ShopController::class, 'showV2'])->name('shop.v2');

        // V3 - Kurumsal/Profesyonel
        Route::get('/v3/{slug}', [ShopController::class, 'showV3'])->name('shop.v3');

        // V4 - Landing Page Stili
        Route::get('/v4/{slug}', [ShopController::class, 'showV4'])->name('shop.v4');

        // V5 - Dark Premium
        Route::get('/v5/{slug}', [ShopController::class, 'showV5'])->name('shop.v5');

        // V6 - Hybrid (V4 left + V2 sticky right)
        Route::get('/v6/{slug}', [ShopController::class, 'showV6'])->name('shop.v6');
    });

// NORMAL SHOP ROUTES
Route::middleware(['web', 'tenant', 'locale.site', 'frontend.auto.seo'])
    ->prefix('shop')
    ->group(function () {
        // Shop Index - Tüm ürünler
        Route::get('/', [ShopController::class, 'index'])->name('shop.index');

        // Shop Category
        Route::get('/kategori/{slug}', [ShopController::class, 'category'])->name('shop.category');

        // Shop Brand
        Route::get('/brand/{slug}', [ShopController::class, 'brand'])->name('shop.brand');

        // Quote (Teklif) Form
        Route::post('/quote', [ShopQuoteController::class, 'submit'])->name('shop.quote.submit');

        // ID-based fallback routes (AI ID kullanırsa redirect)
        Route::get('/product/{id}', [ShopController::class, 'showById'])->name('shop.show.by-id')->where('id', '[0-9]+');
        Route::get('/category-by-id/{id}', [ShopController::class, 'categoryById'])->name('shop.category.by-id')->where('id', '[0-9]+');

        // PDF Export - routes/web.php'de tanımlı (dinamik route'lardan önce)
        // Route::get('/pdf/{slug}', [ShopController::class, 'exportPdf'])->name('shop.pdf');

        // Shop Product Detail - WILDCARD (en sonda olmalı!)
        Route::get('/{slug}', [ShopController::class, 'show'])->name('shop.show');
    });
