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

// 🔄 CSRF TOKEN REFRESH ENDPOINT (Login page auto-refresh için)
Route::get('/api/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
})->middleware('web');

// 🔐 SESSION CHECK - Tenant 1001 (Muzibu) için session kontrolü
// 🔥 FIX: Bu route web.php'de olmalı (API middleware grubu session kullanmaz!)
Route::get('/api/session/check', function (\Illuminate\Http\Request $request) {
    // Tenant 1001 kontrolü
    if (!tenant() || tenant()->id != 1001) {
        return response()->json(['authenticated' => false]);
    }

    // Auth kontrolü
    if (!auth()->check()) {
        return response()->json(['authenticated' => false], 401);
    }

    // 🔥 DEVICE LIMIT: Session DB'de var mı kontrol et
    // Redis'ten silinen session'lar için logout zorla
    try {
        $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
        $user = auth()->user();

        \Log::info('🔐 SESSION CHECK: Starting', [
            'user_id' => $user->id,
            'session_id' => substr(session()->getId(), 0, 20) . '...',
        ]);

        // DB'de session var mı kontrol et
        if (!$deviceService->sessionExists($user)) {
            // Session DB'de yok -> LOGOUT
            \Log::info('🔐 SESSION CHECK: Session NOT found in DB - returning 401');
            return response()->json(['authenticated' => false], 401);
        }

        \Log::info('🔐 SESSION CHECK: Session found - returning authenticated: true');
    } catch (\Exception $e) {
        \Log::error('🔐 SESSION CHECK: DeviceService error', [
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
    }

    // ✅ Session hem Laravel auth'da hem DB'de geçerli
    return response()->json([
        'authenticated' => true,
        'user' => [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]
    ]);
})->name('api.session.check');

// 🛒 SHOP & CART PRIORITY ROUTES (Wildcard'dan önce tanımlanmalı!)
// NOT: Bu route'lar modülde tanımlanabilirdi ama Livewire component'ler modül route'unda catch-all'dan önce olmalı
Route::get('/cart', \Modules\Cart\App\Http\Livewire\Front\CartPage::class)->name('cart.index');
// shop.checkout kaldırıldı - Cart modülü kullanılıyor: /cart/checkout
// Route::get('/shop/payment/{orderNumber}', [\Modules\Shop\App\Http\Controllers\PaymentPageController::class, 'show'])->name('shop.payment.page');

// 🎵 MUZIBU SEARCH (Livewire) - Priority route (wildcard'dan önce)
// /search Muzibu tenant'ında SearchPageController'dan /ara'ya redirect yapıyor
Route::middleware([InitializeTenancy::class, 'tenant', 'locale.site'])
    ->get('/ara', \Modules\Muzibu\App\Http\Livewire\Frontend\SearchResults::class)
    ->name('muzibu.search');

// 🔀 MUZIBU OLD URL REDIRECTS - Backward compatibility
Route::redirect('/muzibu/song/{slug}', '/songs/{slug}', 301);
Route::redirect('/song/{slug}', '/songs/{slug}', 301);
Route::redirect('/muzibu/album/{slug}', '/albums/{slug}', 301);
Route::redirect('/muzibu/artist/{slug}', '/artists/{slug}', 301);
Route::redirect('/muzibu/playlist/{slug}', '/playlists/{slug}', 301);
Route::redirect('/muzibu/genre/{slug}', '/genres/{slug}', 301);
Route::redirect('/muzibu/sector/{slug}', '/sectors/{slug}', 301);

// 👑 SUBSCRIPTION PLANS
Route::get('/subscription/plans', \Modules\Subscription\App\Http\Livewire\Front\SubscriptionPlansComponent::class)->name('subscription.plans');
Route::middleware('auth')->get('/subscription/success', \Modules\Subscription\App\Http\Controllers\Front\SubscriptionSuccessController::class)->name('subscription.success');

// 💳 PAYMENT ROUTES - Tenant context için middleware zorunlu!
Route::middleware([InitializeTenancy::class])->group(function () {
    Route::get('/payment/success', [\Modules\Payment\App\Http\Controllers\PaymentSuccessController::class, 'show'])->name('payment.success');
    Route::get('/payment/{orderNumber}', [\Modules\Payment\App\Http\Controllers\PaymentPageController::class, 'show'])->name('payment.page');
});

// PDF Export - Wildcard'dan önce tanımlanmalı
Route::middleware([InitializeTenancy::class, 'site'])
    ->get('/shop/pdf/{slug}', [\Modules\Shop\App\Http\Controllers\Front\ShopController::class, 'exportPdf'])
    ->name('shop.pdf');

// 🎯 LANDING PAGES - Google Ads Campaign Routes
require __DIR__.'/landing.php';

// DESIGN LIBRARY STATIC FILES - MUST BE FIRST, BEFORE ADMIN & CATCHALL ROUTES
Route::get('design', [App\Http\Controllers\DesignLibraryController::class, 'index'])->name('designs.index');

// 🧪 TEST & DEVELOPMENT ROUTES (BEFORE catch-all!)
require __DIR__.'/test.php';

// Design catch-all route (must be LAST!)
Route::get('design/{file}', [App\Http\Controllers\DesignLibraryController::class, 'show'])->where('file', '.*')->name('designs.show');

// README STATIC HTML FILES - Serve without database/tenant middleware
Route::get('readme/{path?}', [\App\Http\Controllers\ReadmeController::class, 'show'])
    ->where('path', '.*')
    ->name('readme.show');

// GOOGLE SHOPPING FEED - Needs tenant middleware
Route::middleware(['web', 'tenant'])->group(function () {
    Route::get('productfeed', [\Modules\Shop\App\Http\Controllers\GoogleShoppingFeedController::class, 'index']);
    Route::get('googlemerchant', [\Modules\Shop\App\Http\Controllers\GoogleShoppingFeedController::class, 'index']); // Alias for backward compatibility
});

// Admin routes
require __DIR__.'/admin/web.php';

// 🧹 Test route'ları arşivlendi

// 🚧 UNDER CONSTRUCTION PROTECTION - Password verification endpoint (no middleware)
Route::post('/verify-construction-password', [App\Http\Controllers\ConstructionAccessController::class, 'verify'])
    ->withoutMiddleware([\App\Http\Middleware\UnderConstructionProtection::class])
    ->name('construction.verify');

// 🎵 MUZIBU ROUTES - Domain-specific routes (Tenant 1001)
// Load module routes with domain filter
$muzibuDomains = \Illuminate\Support\Facades\DB::table('domains')
    ->where('tenant_id', 1001)
    ->pluck('domain')
    ->toArray();

foreach ($muzibuDomains as $index => $domain) {
    Route::middleware([\Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class])
        ->domain($domain)
        ->name($index === 0 ? '' : "d{$index}.")
        ->group(module_path('Muzibu', 'routes/web.php'));
}

// 🎵 MUZIBU STREAMING ROUTES - EN ÖNCE TANIMLANMALI (high priority)
// Sadece tenant middleware - session/web middleware yok (stateless streaming)
Route::middleware([InitializeTenancy::class])
    ->group(function () {
        // Encryption key endpoint (GET + OPTIONS for CORS)
        Route::match(['get', 'options'], '/stream/key/{songHash}', [\App\Http\Controllers\Streaming\MuzikStreamController::class, 'getEncryptionKey'])
            ->name('stream.key');

        // 🔑 ENCRYPTION KEY ENDPOINT MOVED TO MuzibuServiceProvider::loadApiRoutes()
        // Uses correct SongController instead of deprecated MuzikStreamController
        // Route: /api/muzibu/songs/{id}/key -> SongController::serveEncryptionKey()

        // HLS playlist ve chunk'lar (GET + OPTIONS for CORS)
        Route::match(['get', 'options'], '/stream/play/{songHash}/{filename}', [\App\Http\Controllers\Streaming\MuzikStreamController::class, 'streamFile'])
            ->where('filename', '.*')
            ->name('stream.play');
    });

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

    // LogoService'den logo al (header/homepage ile aynı kaynak)
    $logoService = app(\App\Services\LogoService::class);
    $logoUrl = $logoService->getSchemaLogoUrl(); // Önce light logo, yoksa dark logo

    if ($logoUrl) {
        // PWA standartları - Farklı boyutlar
        $iconSizes = [
            ['size' => '192x192', 'purpose' => 'any'],
            ['size' => '512x512', 'purpose' => 'any'],
            ['size' => '192x192', 'purpose' => 'maskable'],
            ['size' => '512x512', 'purpose' => 'maskable']
        ];

        foreach ($iconSizes as $icon) {
            $manifest['icons'][] = [
                'src' => $logoUrl,
                'sizes' => $icon['size'],
                'type' => 'image/png',
                'purpose' => $icon['purpose']
            ];
        }
    }

    // PWA Shortcuts (tenant-aware - Aktif modüllere göre dinamik)
    $shortcuts = [];

    // Shop modülü aktifse
    if (Module::isEnabled('Shop')) {
        $shortcuts[] = [
            'name' => __('Ürünler'),
            'short_name' => __('Ürünler'),
            'description' => __('Tüm ürünleri görüntüle'),
            'url' => url('/shop'),
            'icons' => $logoUrl ? [['src' => $logoUrl, 'sizes' => '192x192']] : []
        ];
    }

    // Blog modülü aktifse
    if (Module::isEnabled('Blog')) {
        $shortcuts[] = [
            'name' => __('Blog'),
            'short_name' => __('Blog'),
            'description' => __('Blog yazılarını oku'),
            'url' => url('/blog'),
            'icons' => $logoUrl ? [['src' => $logoUrl, 'sizes' => '192x192']] : []
        ];
    }

    // Portfolio modülü aktifse
    if (Module::isEnabled('Portfolio')) {
        $shortcuts[] = [
            'name' => __('Portföy'),
            'short_name' => __('Portföy'),
            'description' => __('Projelerimizi görüntüle'),
            'url' => url('/portfolio'),
            'icons' => $logoUrl ? [['src' => $logoUrl, 'sizes' => '192x192']] : []
        ];
    }

    // Contact/Page modülü her zaman var (fallback)
    $shortcuts[] = [
        'name' => __('İletişim'),
        'short_name' => __('İletişim'),
        'description' => __('Bize ulaşın'),
        'url' => url('/iletisim'),
        'icons' => $logoUrl ? [['src' => $logoUrl, 'sizes' => '192x192']] : []
    ];

    // Max 4 shortcut (PWA standardı)
    $manifest['shortcuts'] = array_slice($shortcuts, 0, 4);

    // 1 yıl cache (manifest nadiren değişir)
    return response()->json($manifest)
        ->header('Cache-Control', 'public, max-age=31536000, immutable');
})->name('manifest');

// security.txt - Tenant-aware dynamic route (RFC 9116)
Route::get('/.well-known/security.txt', function () {
    $contactEmail = setting('contact_email') ?: setting('admin_email') ?: 'security@' . request()->getHost();
    $contactUrl = url('/iletisim');
    $tenant = tenant();
    $companyName = setting('site_title') ?: config('app.name');

    $content = "# Security Policy - {$companyName}\n";
    $content .= "#\n";
    $content .= "# If you discover a security vulnerability, please report it responsibly.\n";
    $content .= "# We appreciate your efforts to improve our security.\n\n";
    $content .= "Contact: mailto:{$contactEmail}\n";
    $content .= "Contact: {$contactUrl}\n";
    $content .= "Expires: " . now()->addYear()->toIso8601String() . "\n";
    $content .= "Preferred-Languages: " . app()->getLocale() . ", en\n";
    $content .= "Canonical: " . url('/.well-known/security.txt') . "\n\n";
    $content .= "# Thank you for helping keep {$companyName} and our users safe!\n";

    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=utf-8')
        ->header('Cache-Control', 'public, max-age=86400'); // 1 gün cache
});

// humans.txt - Tenant-aware dynamic route
Route::get('/humans.txt', function () {
    $companyName = setting('site_title') ?: config('app.name');
    $contactEmail = setting('contact_email') ?: 'info@' . request()->getHost();
    $tenant = tenant();
    $tenantName = $tenant ? $tenant->title : 'Central';

    // Aktif modülleri al
    $enabledModules = [];
    foreach (['Shop', 'Blog', 'Portfolio', 'Page', 'Search'] as $module) {
        if (Module::isEnabled($module)) {
            $enabledModules[] = $module;
        }
    }

    $content = "/* TEAM */\n\n";
    $content .= "Developer: Nurullah Okatan\n";
    $content .= "Contact: nurullah@nurullah.net\n";
    $content .= "Location: Istanbul, Turkey\n\n";

    $content .= "/* SITE */\n\n";
    $content .= "Company: {$companyName}\n";
    $content .= "Tenant: {$tenantName}\n";
    $content .= "Contact: {$contactEmail}\n";
    $content .= "Last update: " . now()->format('Y/m/d') . "\n";
    $content .= "Language: " . implode(', ', \App\Services\TenantLanguageProvider::getActiveLanguageCodes()) . "\n";
    $content .= "Doctype: HTML5\n";
    $content .= "Standards: HTML5, CSS3, JavaScript ES6+\n";
    $content .= "Components: Laravel 11, Livewire 3, Alpine.js, Tailwind CSS\n";
    $content .= "IDE: PhpStorm, VS Code\n\n";

    $content .= "/* FEATURES */\n\n";
    $content .= "- Multi-tenant SaaS platform\n";
    $content .= "- Progressive Web App (PWA)\n";
    $content .= "- Service Worker for offline support\n";
    $content .= "- Dynamic manifest.json\n";
    $content .= "- Responsive design (mobile-first)\n";
    $content .= "- Dark mode support\n";
    $content .= "- Multi-language support\n";
    $content .= "- SEO optimized\n";
    $content .= "- Enabled modules: " . implode(', ', $enabledModules) . "\n\n";

    $content .= "/* THANKS */\n\n";
    $content .= "To all open-source contributors who made this possible!\n\n";
    $content .= "Laravel - https://laravel.com\n";
    $content .= "Livewire - https://livewire.laravel.com\n";
    $content .= "Alpine.js - https://alpinejs.dev\n";
    $content .= "Tailwind CSS - https://tailwindcss.com\n\n";

    $content .= "                               _\n";
    $content .= "                            _ooOoo_\n";
    $content .= "                           o8888888o\n";
    $content .= "                           88\" . \"88\n";
    $content .= "                           (| -_- |)\n";
    $content .= "                           O\\  =  /O\n";
    $content .= "                        ____/`---'\\____\n";
    $content .= "                      .'  \\\\|     |//  `.\n";
    $content .= "                     /  \\\\|||  :  |||//  \\\n";
    $content .= "                    /  _||||| -:- |||||_  \\\n";
    $content .= "                    |   | \\\\\\  -  /'| |   |\n";
    $content .= "                    | \\_|  `\\`---'//  |_/ |\n";
    $content .= "                    \\  .-\\__ `-. -'__/-.  /\n";
    $content .= "                  ___`. .'  /--.--\\  `. .'___\n";
    $content .= "               .\"\" '<  `.___\\_<|>_/___.' _> \\\"\".\n";
    $content .= "              | | :  `- \\`. ;`. _/; .'/ /  .' ; |\n";
    $content .= "              \\  \\ `-.   \\_\\_`. _.'_/_/  -' _.' /\n";
    $content .= "  =============`-.`___`-.__ \\ \\___  /__.-'_.'_.-'================\n\n";
    $content .= "                       Buddha Bless - No Bugs!\n";

    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=utf-8')
        ->header('Cache-Control', 'public, max-age=86400'); // 1 gün cache
});

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

// Sitemap route'u - CACHED (1 saat)
Route::middleware([InitializeTenancy::class])->get('/sitemap.xml', function() {
    $tenantId = tenant()?->id ?? 'central';
    $cacheKey = "sitemap_xml_{$tenantId}";

    // Cache'den al veya oluştur (1 saat = 3600 saniye)
    $sitemapXml = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function() {
        $sitemap = \App\Services\TenantSitemapService::generate();
        return $sitemap->render();
    });

    return response($sitemapXml, 200, [
        'Content-Type' => 'application/xml',
        'Cache-Control' => 'public, max-age=3600'
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
    // ThemeService ile tema-aware dashboard view
    $themeService = app(\App\Services\ThemeService::class);
    $theme = $themeService->getActiveTheme();
    $themeName = $theme ? $theme->name : 'simple';

    // 1. Tema dashboard view kontrolü
    $themeDashboard = "themes.{$themeName}.dashboard";
    if (view()->exists($themeDashboard)) {
        return view($themeDashboard);
    }

    // 2. Simple tema fallback
    if ($themeName !== 'simple') {
        $simpleDashboard = 'themes.simple.dashboard';
        if (view()->exists($simpleDashboard)) {
            return view($simpleDashboard);
        }
    }

    // 3. Global dashboard fallback
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
                'verify-email',        // /verify-email/{slug}/{hash}
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
                    ->name('shop.category');

                // Shop Brand (2-level route)
                Route::get('/brand/{slug}', [\Modules\Shop\App\Http\Controllers\Front\ShopController::class, 'brand'])
                    ->name('shop.brand');
            });

        // ⚡ BLOG MODULE EXPLICIT ROUTES (catch-all'dan ÖNCE tanımlanmalı!)
        Route::middleware(['web', 'tenant', 'locale.site', 'frontend.auto.seo'])
            ->prefix('blog')
            ->group(function () {
                // Blog Category
                Route::get('/category/{slug}', [\Modules\Blog\App\Http\Controllers\Front\BlogController::class, 'category'])
                    ->name('blog.category.explicit');

                // Blog Tag
                Route::get('/tag/{tag}', [\Modules\Blog\App\Http\Controllers\Front\BlogController::class, 'tag'])
                    ->name('blog.tag.explicit');
            });

        // Catch-all route'ları - prefix olmayan - sadece content route'ları için
        // Regex ile admin, api vb. system route'larını hariç tut
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed|cart|siparislerim|payment)[^/]+$');

        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed|cart|siparislerim|payment)[^/]+$')
         ->where('slug2', '^(?!pdf|category|tag)[^/]+$');

        Route::get('/{slug1}/{slug2}/{slug3}', function($slug1, $slug2, $slug3) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3);
        })->where('slug1', '^(?!admin|api|ai|login|logout|register|password|auth|storage|thumbmaker|css|js|assets|profile|dashboard|debug|design|feed|productfeed|cart|siparislerim|payment)[^/]+$')
         ->where('slug2', '^(?!pdf|category|tag)[^/]+$')
         ->where('slug3', '[^/]+');
    });


// Thumbmaker - Query String Format (src=...&w=...&h=...)
Route::get('/thumbmaker', [\Modules\MediaManagement\App\Http\Controllers\ThumbmakerController::class, 'generate'])
    ->name('thumbmaker.generate');

// Thumbmaker - Short Format (/thumb/{media_id}/{width}/{height})
Route::get('/thumb/{mediaId}/{width?}/{height?}', function ($mediaId, $width = null, $height = null) {
    try {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        $url = $media->getUrl();

        // Thumbmaker'a yönlendir
        $params = array_filter([
            'src' => $url,
            'w' => $width,
            'h' => $height,
            'f' => 'webp',
            'c' => 1,
        ]);

        return redirect('/thumbmaker?' . http_build_query($params));
    } catch (\Exception $e) {
        abort(404, 'Media not found');
    }
})->where(['mediaId' => '\d+', 'width' => '\d+', 'height' => '\d+'])->name('thumb.short');

// Basit thumbmaker link servisi (Encoded format)
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


// 🔍 AUTH DEBUG ENDPOINT (geçici)
Route::get("/test-auth-debug", function () {
    return response()->json([
        "auth_check" => Auth::check(),
        "user" => Auth::check() ? [
            "id" => Auth::user()->id,
            "name" => Auth::user()->name,
            "email" => Auth::user()->email,
        ] : null,
        "session_id" => substr(session()->getId(), 0, 20) . "...",
        "session_driver" => config("session.driver"),
        "has_session_cookie" => request()->hasCookie(config("session.cookie")),
    ]);
})->middleware("web");


// 🔍 DEBUG: Test route for cache middleware
Route::middleware(['site'])->get('/test-cache-middleware', function() {
    return response('Cache middleware test')->header('X-Test-Route', 'yes');
});
