<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\DebugController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Page\App\Http\Controllers\Front\PageController;
use App\Services\DynamicRouteService;

// Admin routes
require __DIR__.'/admin/web.php';


// Ana sayfa route'u  
Route::middleware(['web', 'tenant', 'locale.site', 'page.tracker'])->get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');

// Sitemap route'u
Route::middleware([InitializeTenancy::class])->get('/sitemap.xml', function() {
    $sitemap = \App\Services\TenantSitemapService::generate();
    return response($sitemap->render(), 200, [
        'Content-Type' => 'application/xml'
    ]);
})->name('sitemap');

// Normal Laravel route'ları - ÖNCE tanımlanmalı
Route::middleware('auth')->group(function () {
    // Profile routes - ayrı sayfalar
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    
    // Profile actions
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar.upload');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['web', 'auth', 'verified', 'locale.site'])->name('dashboard');


// Auth route'ları
require __DIR__.'/auth.php';

// Test route'ları - dinamik route'lardan ÖNCE olmalı
require __DIR__.'/test.php';
require __DIR__.'/test-schema.php';

// Cache Debug Route'ları
Route::middleware([InitializeTenancy::class])->group(function () {
    Route::get('/debug/cache', [\App\Http\Controllers\CacheDebugController::class, 'index'])->name('cache.debug');
    Route::get('/debug/cache/clear', [\App\Http\Controllers\CacheDebugController::class, 'clearCache'])->name('cache.debug.clear');
    Route::get('/debug/redis', [\App\Http\Controllers\RedisTestController::class, 'test'])->name('redis.test');
});
require __DIR__.'/debug-lang.php';
require __DIR__.'/cache-test.php';

// Test route'ları temizlendi

// Debug route'ları
Route::middleware([InitializeTenancy::class])->get('/debug/portfolio', [DebugController::class, 'portfolioDebug'])->name('debug.portfolio');

// URL Prefix Test Route
Route::middleware([InitializeTenancy::class])->get('/debug/url-prefix', function() {
    return view('debug.url-prefix-test');
})->name('debug.url-prefix');

// Language Debug Test Route
Route::middleware([InitializeTenancy::class])->get('/debug/language-test', function() {
    return view('debug.language-test');
})->name('debug.language-test');

// Simple Language Debug Test Route
Route::middleware([InitializeTenancy::class])->get('/debug/simple-lang-test', function() {
    return view('debug.simple-lang-test');
})->name('debug.simple-lang-test');

// Language Switch Debug Test Route - cache bypass
Route::middleware([InitializeTenancy::class])->get('/debug/language-switch-test', function() {
    return view('debug.language-switch-test');
})->name('debug.language-switch-test');

Route::middleware([InitializeTenancy::class])->post('/debug/url-prefix-save', function() {
    $tenant = tenant();
    $data = $tenant->data ?? [];
    $data['url_prefix'] = [
        'mode' => request('mode'),
        'default_language' => request('default_language')
    ];
    $tenant->update(['data' => $data]);
    
    \Modules\LanguageManagement\app\Services\UrlPrefixService::clearCache();
    
    return redirect('/debug/url-prefix')->with('success', 'Ayarlar kaydedildi!');
})->name('debug.url-prefix.save');

// Site dil değiştirme route'u - B30 tarzı GET request
Route::middleware([InitializeTenancy::class, 'web'])->get('/language/{locale}', function($locale) {
    \Log::info('🌐 LANGUAGE SWITCH BAŞLADI', [
        'requested_locale' => $locale,
        'current_app_locale' => app()->getLocale(),
        'session_before' => [
            'site_locale' => session('site_locale'),
            'site_language' => session('site_language'),
            'locale' => session('locale'),
        ],
        'user_authenticated' => auth()->check(),
        'request_url' => request()->fullUrl(),
        'referrer' => request()->header('referer')
    ]);
    
    // Dil geçerli mi kontrol et
    if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
        $availableLocales = \Modules\LanguageManagement\app\Services\UrlPrefixService::getAvailableLocales();
        if (!in_array($locale, $availableLocales)) {
            \Log::error('❌ LANGUAGE SWITCH GEÇERSİZ DİL', ['locale' => $locale, 'available' => $availableLocales]);
            abort(404);
        }
        \Log::info('✅ LANGUAGE SWITCH DİL GEÇERLİ', ['locale' => $locale, 'available' => $availableLocales]);
    }
    
    // DOMAIN-SPECIFIC SESSION KEY OLUŞTUR - B30 tarzı
    $domain = request()->getHost();
    $sessionKey = 'site_locale_' . str_replace('.', '_', $domain);
    
    // TÜM ESKI SESSION KEY'LERİNİ TEMİZLE (domain-specific olarak)
    session()->forget(['site_language', 'locale', 'site_locale', $sessionKey]);
    \Log::info('🗑️ LANGUAGE SWITCH ESKİ SESSION TEMIZLENDI');
    
    // DOMAIN-SPECIFIC KEY KULLAN - B30 tarzı
    session([$sessionKey => $locale]);
    \Log::info('💾 LANGUAGE SWITCH YENİ SESSION KAYDEDILDI', [
        'domain' => $domain,
        'session_key' => $sessionKey,
        'locale' => $locale,
        $sessionKey => session($sessionKey)
    ]);
    
    // Laravel locale'ini hemen ayarla
    app()->setLocale($locale);
    \Log::info('🔧 LANGUAGE SWITCH APP LOCALE AYARLANDI', ['app_locale' => app()->getLocale()]);
    
    // User tercihini kaydet (context-aware)
    if (auth()->check()) {
        $user = auth()->user();
        
        // URL'den context belirle
        $isAdminContext = str_contains(request()->url(), '/admin/');
        
        if ($isAdminContext) {
            // Admin panelinde değişim
            $user->update(['admin_language_preference' => $locale]);
            \Log::info('👤 USER ADMIN LANGUAGE PREFERENCE UPDATED', [
                'user_id' => $user->id,
                'admin_language_preference' => $locale
            ]);
        } else {
            // Site/frontend değişimi
            $user->update(['site_language_preference' => $locale]);
            \Log::info('👤 USER SITE LANGUAGE PREFERENCE UPDATED', [
                'user_id' => $user->id, 
                'site_language_preference' => $locale
            ]);
        }
    }
    
    // HIZLI CACHE TEMİZLEME - B30 tarzı, SADECE GEREKLİ OLANLAR
    try {
        // Sadece ResponseCache temizle (yeterli)
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
        
        \Log::info('Language switch cache cleared', [
            'locale' => $locale,
            'cache_cleared' => true
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Cache clear error: ' . $e->getMessage());
    }
    
    \Log::info('🎯 LANGUAGE SWITCH TAMAMLANDI', [
        'locale' => $locale,
        'final_app_locale' => app()->getLocale(),
        'redirect_to' => 'home'
    ]);
    
    // CACHE BYPASS PARAMETRELERİ - response cache'i atlatmak için
    $cacheBypassParams = [
        '_' => time(), // Timestamp
        'lang_changed' => $locale, // Dil değişti işareti
        'cb' => substr(md5($locale . time()), 0, 8) // Cache buster
    ];
    
    // BULUNDUĞU SAYFAYA GERİ DÖN - referrer'a göre
    $referrer = request()->header('referer');
    if ($referrer && $referrer !== request()->fullUrl()) {
        // Referrer URL'ini parse et ve query parametrelerini ekle
        $referrerUrl = $referrer;
        $separator = strpos($referrerUrl, '?') !== false ? '&' : '?';
        $referrerWithParams = $referrerUrl . $separator . http_build_query($cacheBypassParams);
        
        return redirect($referrerWithParams)
            ->with('success', 'Dil değiştirildi: ' . strtoupper($locale))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Cache-Bypass', 'language-switch');
    } else {
        // Referrer yoksa anasayfaya - query parametreleri ile
        $homeUrlWithParams = '/?' . http_build_query($cacheBypassParams);
        
        return redirect($homeUrlWithParams)
            ->with('success', 'Dil değiştirildi: ' . strtoupper($locale))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Cache-Bypass', 'language-switch');
    }
})->name('language.switch');

// Dinamik modül route'ları - sadece frontend içerik için
Route::middleware([InitializeTenancy::class, 'web', \Modules\LanguageManagement\app\Http\Middleware\SetLocaleMiddleware::class . ':site'])
    ->group(function () {
        // Dil prefix'li route'lar - tenant bazlı aktif diller
        Route::get('/{lang}/{slug1}', function($lang, $slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard)[^/]+$');
        
        Route::get('/{lang}/{slug1}/{slug2}', function($lang, $slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard)[^/]+$')
         ->where('slug2', '[^/]+');
         
        // Catch-all route'ları - prefix olmayan - sadece content route'ları için
        // Regex ile admin, api vb. system route'larını hariç tut
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard)[^/]+$');
        
        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard)[^/]+$')
         ->where('slug2', '[^/]+');
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