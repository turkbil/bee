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

// Simple test route
Route::get('/test', function () {
    return 'Laravel is working!';
});


// Health check endpoint for Docker containers
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check'])->name('health.check');

// System health check endpoint for AI translation system
Route::get('/health/system', [App\Http\Controllers\HealthController::class, 'systemHealth'])->name('health.system');

// Test SEO component
Route::get('/test-seo', function() {
    return view('test-seo');
});

// Ana sayfa route'ları - Çoklu dil desteği ile
Route::middleware(['site', 'page.tracker'])->group(function () {
    // Varsayılan dil için ana sayfa (prefix yok)
    Route::get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');
    
    // Diğer diller için ana sayfa (prefix'li)
    Route::get('/{locale}', function($locale) {
        // Geçerli dil kontrolü
        $validLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
        $defaultLocale = get_tenant_default_locale();
        
        // Geçerli bir dil ise
        if (in_array($locale, $validLocales)) {
            // Locale'i ayarla ve homepage'i göster
            app()->setLocale($locale);
            session(['tenant_locale' => $locale]);
            
            // Varsayılan dil bile olsa göster, redirect etme
            // Çünkü kullanıcı alternate link'ten geliyor olabilir
            $controller = app(\Modules\Page\App\Http\Controllers\Front\PageController::class);
            $seoService = app(\App\Services\SeoMetaTagService::class);
            return $controller->homepage($seoService);
        }
        
        // Geçersiz locale ise 404
        abort(404);
    })->where('locale', getSupportedLanguageRegex())->name('home.locale');
});

// Cache temizleme route'u
Route::post('/clear-cache', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'clearCache'])->name('clear.cache');

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
})->middleware(['site', 'auth', 'verified'])->name('dashboard');


// Auth route'ları
require __DIR__.'/auth.php';

// Test route'ları - dinamik route'lardan ÖNCE olmalı
require __DIR__.'/test.php';
require __DIR__.'/test-schema.php';

// Debug route'ları
require __DIR__.'/debug.php';


// Site dil değiştirme route'u - Laravel Native Localization
Route::middleware(['site'])->withoutMiddleware(\Spatie\ResponseCache\Middlewares\CacheResponse::class)->get('/language/{locale}', function($locale) {
    // Geçerli dil kontrolü - dinamik olarak
    $validLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
    
    if (in_array($locale, $validLocales)) {
        // Session ve locale güncelle
        session(['tenant_locale' => $locale]);
        app()->setLocale($locale);
        
        // Cookie'ye kaydet (365 gün)
        \Cookie::queue('tenant_locale_preference', $locale, 525600);
        
        // Kullanıcı tercihini güncelle
        if (auth()->check()) {
            auth()->user()->update(['tenant_locale' => $locale]);
        }
        
        // 🚀 TÜM CACHE'LERİ TEMİZLE - YENİ SİSTEM
        \App\Services\CacheManager::clearAllLanguageRelatedCaches();
        
        // Log dil değişikliğini
        \Log::info('Language switched', [
            'new_locale' => $locale,
            'user_id' => auth()->id() ?? 'guest',
            'tenant_id' => tenant()?->id ?? 'central'
        ]);
        
        // 🎯 LARAVEL NATIVE ÇÖZÜM - Route bazlı dil değiştirme
        // Request'ten model ve action bilgilerini al
        $currentRoute = request()->route();
        $referer = request()->header('referer', '/');
        $defaultLocale = get_tenant_default_locale();
        
        // CanonicalHelper'dan alternatif link'leri al
        $model = request()->get('_model');
        $moduleAction = request()->get('_action', 'show');
        
        if ($model || str_contains($referer, '://')) {
            // Eğer model varsa veya referer'dan geliyorsa
            $alternateLinks = \App\Helpers\CanonicalHelper::generateAlternateLinks($model, $moduleAction);
            
            if (isset($alternateLinks[$locale])) {
                // Hedef dil için URL varsa oraya yönlendir
                $redirectUrl = $alternateLinks[$locale]['url'];
            } else {
                // Yoksa ana sayfaya
                $redirectUrl = $locale === $defaultLocale ? url('/') : url("/{$locale}");
            }
        } else {
            // Model yoksa basit ana sayfa yönlendirmesi
            $redirectUrl = $locale === $defaultLocale ? url('/') : url("/{$locale}");
        }
        
        // Cache-busting headers ile redirect
        return redirect($redirectUrl)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, s-maxage=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->header('X-Accel-Expires', '0')
            ->header('Vary', 'Accept-Language')
            ->with('success', __('admin.language_changed_successfully'));
    }
    
    return redirect()->back()->with('error', __('admin.invalid_language'));
})->name('language.switch');

// Dinamik modül route'ları - sadece frontend içerik için
Route::middleware([InitializeTenancy::class, 'site'])
    ->group(function () {
        // Dil prefix'li route'lar - tenant bazlı aktif diller
        Route::get('/{lang}/{slug1}', function($lang, $slug1) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }
            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);
            
            // DynamicRouteService'e locale bilgisini geç
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, null, null, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$');
        
        Route::get('/{lang}/{slug1}/{slug2}', function($lang, $slug1, $slug2) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }
            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);
            
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, null, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+');
         
        Route::get('/{lang}/{slug1}/{slug2}/{slug3}', function($lang, $slug1, $slug2, $slug3) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }
            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);
            
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+')
         ->where('slug3', '[^/]+');
         
        // Catch-all route'ları - prefix olmayan - sadece content route'ları için
        // Regex ile admin, api vb. system route'larını hariç tut
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$');
        
        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+');
         
        Route::get('/{slug1}/{slug2}/{slug3}', function($slug1, $slug2, $slug3) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+')
         ->where('slug3', '[^/]+');
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