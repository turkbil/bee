<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\FaviconController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use Modules\Page\App\Http\Controllers\Front\PageController;
use App\Services\DynamicRouteService;
use Modules\Search\App\Http\Controllers\SearchPageController;

// DESIGN LIBRARY STATIC FILES - MUST BE FIRST, BEFORE ADMIN & CATCHALL ROUTES
Route::get('design', [App\Http\Controllers\DesignLibraryController::class, 'index'])->name('designs.index');
Route::get('design/{file}', [App\Http\Controllers\DesignLibraryController::class, 'show'])->where('file', '.*')->name('designs.show');

// GOOGLE SHOPPING FEED - Controller test
Route::get('productfeed', [\Modules\Shop\App\Http\Controllers\GoogleShoppingFeedController::class, 'index']);

// Admin routes
require __DIR__.'/admin/web.php';

// 🧹 Test route'ları arşivlendi

// STORAGE ROUTES - EN ÖNCE TANIMLANMALI (high priority)
// Tenant medya dosyalarına erişim
Route::middleware([InitializeTenancy::class])
    ->get('/storage/tenant{id}/{path}', [StorageController::class, 'tenantMedia'])
    ->where('id', '[0-9]+')
    ->where('path', '.*')
    ->name('storage.tenant');

// Normal storage dosyalarına erişim (tenant-aware)
Route::middleware([InitializeTenancy::class])
    ->get('/storage/{path}', [StorageController::class, 'publicStorage'])
    ->where('path', '.*')
    ->name('storage.public');

// Health check endpoint for Docker containers
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check'])->name('health.check');

// System health check endpoint for AI translation system
Route::get('/health/system', [App\Http\Controllers\HealthController::class, 'systemHealth'])->name('health.system');

// Metrics endpoint (empty response for monitoring tools)
Route::get('/metrics', function () {
    return response('', 204);
})->name('metrics');

// FAVICON ROUTE - Dynamic tenant-aware favicon (high priority)
Route::middleware([InitializeTenancy::class])
    ->get('/favicon.ico', [FaviconController::class, 'show'])
    ->name('favicon');

// SEARCH ROUTES - Must be before catch-all routes
Route::middleware(['tenant'])->group(function () {
    // Search with query parameter (?q=keyword)
    Route::get('/search', [SearchPageController::class, 'show'])
        ->name('search.query');

    // Search with URL parameter (/search/keyword)
    Route::get('/search/{query}', [SearchPageController::class, 'show'])
        ->name('search.show');

    // Popular searches (SEO)
    Route::get('/populer-aramalar', [SearchPageController::class, 'tags'])
        ->name('search.tags');
});

// PWA Manifest - Dynamic (2025 Best Practice)
Route::get('/manifest.json', function () {
    $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name');
    $siteDescription = setting('site_description') ?: '';
    $themeColor = setting('site_theme_color') ?: '#000000';
    $themeColorLight = setting('site_theme_color_light') ?: '#ffffff';

    $manifest = [
        'name' => $siteName,
        'short_name' => $siteName,
        'description' => $siteDescription,
        'start_url' => url('/'),
        'display' => 'standalone',
        'background_color' => $themeColorLight,
        'theme_color' => $themeColor,
        'orientation' => 'portrait-primary',
        'icons' => []
    ];

    // Logo varsa icon olarak ekle
    $siteLogo = setting('site_logo');
    if ($siteLogo) {
        $logoUrl = cdn($siteLogo);
        $manifest['icons'][] = [
            'src' => $logoUrl,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ];
    }

    return response()->json($manifest);
})->name('manifest');

// Test SEO component
// 🧹 SEO test route arşivlendi

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

// Dynamic Robots.txt - Tenant aware (2025 SEO Best Practice)
Route::middleware([InitializeTenancy::class])
    ->get('/robots.txt', [App\Http\Controllers\RobotsController::class, 'generate'])
    ->name('robots');

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

// 🧹 Test ve debug route'ları development tamamlandıktan sonra arşivlendi
// Test route dosyaları: archive/removed-controllers/ klasöründe


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
        
        // 🎯 RETURN URL KONTROLÜ - Auth sayfaları için
        $returnUrl = request()->get('return');

        if ($returnUrl && filter_var($returnUrl, FILTER_VALIDATE_URL)) {
            // Return URL varsa direkt oraya dön (auth sayfaları için)
            $redirectUrl = $returnUrl;
        } else {
            // Normal dil değiştirme akışı
            $currentRoute = request()->route();
            $referer = request()->header('referer', '/');
            $defaultLocale = get_tenant_default_locale();

            // 🔐 AUTH ROUTE DESTEĞİ - referer'dan auth sayfası kontrolü
            $authRoutes = [
                'login', 'register', 'logout',
                'forgot-password', 'reset-password',
                'password', 'confirm-password',
                'verify-email', 'email',
                'profile', 'dashboard'
            ];

            $isAuthPage = false;
            $authPage = null;

            foreach ($authRoutes as $route) {
                if (str_contains($referer, '/' . $route)) {
                    $isAuthPage = true;
                    $authPage = $route;
                    break;
                }
            }

            if ($isAuthPage && $authPage) {
                // Auth sayfası için locale-aware URL oluştur
                $redirectUrl = $locale === $defaultLocale
                    ? url('/' . $authPage)
                    : url('/' . $locale . '/' . $authPage);

                \Log::info('Language switch for auth page', [
                    'auth_page' => $authPage,
                    'locale' => $locale,
                    'redirect_url' => $redirectUrl
                ]);
            } else {
                // Normal sayfa için CanonicalHelper kullan
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
            }
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

// SHOP PDF EXPORT ROUTE - Dinamik route'lardan ÖNCE tanımlanmalı!
Route::middleware([InitializeTenancy::class, 'site'])
    ->get('/shop/pdf/{slug}', [\Modules\Shop\App\Http\Controllers\Front\ShopController::class, 'exportPdf'])
    ->name('shop.pdf');

// Dinamik modül route'ları - sadece frontend içerik için
Route::middleware([InitializeTenancy::class, 'site'])
    ->group(function () {
        // Dil prefix'li route'lar - tenant bazlı aktif diller
        Route::get('/{lang}/{slug1}', function($lang, $slug1) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }

            // ⚠️ AUTH ROUTE FALLBACK - Tüm auth sayfaları için
            $authRoutes = [
                'login', 'register', 'logout',
                'forgot-password', 'reset-password',
                'password', 'confirm-password',
                'verify-email', 'email',
                'profile', 'dashboard'
            ];
            if (in_array($slug1, $authRoutes)) {
                // Locale set et ve auth route'una redirect
                app()->setLocale($lang);
                session(['tenant_locale' => $lang]);

                \Log::info('AUTH ROUTE FALLBACK', [
                    'lang' => $lang,
                    'slug1' => $slug1,
                    'redirect_to' => '/' . $slug1
                ]);

                return redirect('/' . $slug1)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            }

            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);

            // DynamicRouteService'e locale bilgisini geç
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, null, null, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '[^/]+');
        
        Route::get('/{lang}/{slug1}/{slug2}', function($lang, $slug1, $slug2) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }

            // ⚠️ AUTH ROUTE FALLBACK - Token/ID içeren auth sayfaları için
            $authRoutes = [
                'reset-password',      // /reset-password/{token}
                'verify-email',        // /verify-email/{id}/{hash}
                'password',            // /password/*
                'confirm-password',    // /confirm-password/*
                'email'                // /email/*
            ];
            if (in_array($slug1, $authRoutes)) {
                app()->setLocale($lang);
                session(['tenant_locale' => $lang]);
                return redirect('/' . $slug1 . '/' . $slug2)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            }

            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);

            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, null, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '[^/]+')
         ->where('slug2', '^(?!pdf)[^/]+$');
         
        Route::get('/{lang}/{slug1}/{slug2}/{slug3}', function($lang, $slug1, $slug2, $slug3) {
            // Dil geçerliliği kontrolü
            if (!is_valid_tenant_locale($lang)) {
                abort(404);
            }

            // ⚠️ AUTH ROUTE FALLBACK - /en/verify-email/id/hash gibi istekler için
            $authRoutes = ['verify-email'];
            if (in_array($slug1, $authRoutes)) {
                app()->setLocale($lang);
                session(['tenant_locale' => $lang]);
                return redirect('/' . $slug1 . '/' . $slug2 . '/' . $slug3)
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            }

            // App locale'i ayarla
            app()->setLocale($lang);
            session(['route_locale' => $lang]);

            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3, $lang);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '[^/]+')
         ->where('slug2', '^(?!pdf)[^/]+$')
         ->where('slug3', '[^/]+');

        // ⚡ SHOP MODULE EXPLICIT ROUTES (catch-all'dan ÖNCE tanımlanmalı!)
        Route::middleware(['web', 'tenant', 'locale.site', 'frontend.auto.seo'])
            ->prefix('shop')
            ->group(function () {
                // Shop Category (2-level route)
                Route::get('/kategori/{slug}', [\Modules\Shop\App\Http\Controllers\Front\ShopController::class, 'category'])
                    ->name('shop.category.explicit');

                // Shop Brand (2-level route)
                Route::get('/brand/{slug}', [\Modules\Shop\App\Http\Controllers\Front\ShopController::class, 'brand'])
                    ->name('shop.brand.explicit');
            });

        // Catch-all route'ları - prefix olmayan - sadece content route'ları için
        // Regex ile admin, api vb. system route'larını hariç tut
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed)[^/]+$');

        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed)[^/]+$')
         ->where('slug2', '^(?!pdf)[^/]+$');

        Route::get('/{slug1}/{slug2}/{slug3}', function($slug1, $slug2, $slug3) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed)[^/]+$')
         ->where('slug2', '^(?!pdf)[^/]+$')
         ->where('slug3', '[^/]+');
    });


// Basit thumbmaker link servisi
Route::get('/thumbmaker/{encoded}/{width?}/{height?}/{quality?}', \App\Http\Controllers\ThumbmakerLinkController::class)
    ->where(['encoded' => '[A-Za-z0-9-_]+', 'width' => '\d+', 'height' => '\d+', 'quality' => '\d+']);

// V3 Mega Menu Test Page
Route::middleware([InitializeTenancy::class])->get('/test/mega-menu-v3', function () {
    return view('themes.ixtif.pages.mega-menu-v3-test');
})->name('test.megamenu.v3');

// 403 hata sayfası rotası
Route::get('/403', function () {
    return response()->view('errors.403', [], 403);
})->name('errors.403');

// CSRF token yenileme rotası
Route::get('/csrf-refresh', function () {
    return csrf_token();
})->name('csrf.refresh')->middleware('web');
Route::get('/test-megamenu-v3', function() { return view('themes.ixtif.pages.test-megamenu-v3'); });
