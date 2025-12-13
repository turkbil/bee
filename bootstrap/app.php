<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\JsonResponseServiceProvider::class, // MUST BE FIRST - Override JsonResponse
        \App\Providers\DatabasePoolServiceProvider::class,
        \App\Providers\QueueResilienceServiceProvider::class,
        \App\Providers\LivewireUtf8ServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->booted(function ($app) {
        // PHP execution time ayarları
        if (env('PHP_MAX_EXECUTION_TIME')) {
            ini_set('max_execution_time', env('PHP_MAX_EXECUTION_TIME', 600));
        }
        if (env('PHP_MAX_INPUT_TIME')) {
            ini_set('max_input_time', env('PHP_MAX_INPUT_TIME', 600));
        }
        if (env('PHP_MEMORY_LIMIT')) {
            ini_set('memory_limit', env('PHP_MEMORY_LIMIT', '512M'));
        }
    })
    ->booted(function ($app) {
        // Admin dilleri için namespace kaydet
        $adminLangPath = lang_path('admin');
        if (is_dir($adminLangPath)) {
            $app['translator']->addNamespace('admin', $adminLangPath);
        }
        
        // Legacy module route loading removed - now event-driven via ModuleEnabled events
    })
    ->withMiddleware(function (Middleware $middleware) {
        // 0. TRUST PROXIES - Nginx proxy için (EN ÖNCE!)
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );

        // 0.5. WWW REDIRECT - KALDIRILDI (POST request'leri bozuyordu)
        // $middleware->prependToGroup('web', \App\Http\Middleware\EnsureWwwDomain::class);

        // 1. TENANT - Domain belirleme (EN ÖNCELİKLİ) - Sadece web
        $middleware->prependToGroup('web', \App\Http\Middleware\InitializeTenancy::class);
        
        // 2. REDIS HEALTH CHECK - Redis bağlantı sağlığı kontrolü
        $middleware->appendToGroup('web', \App\Http\Middleware\RedisHealthCheckMiddleware::class);
        
        // 3. DİL - Tenant'tan HEMEN sonra (Session'dan ÖNCE çalışmalı) - admin hariç
        // SiteSetLocaleMiddleware web grubundan kaldırıldı, sadece belirli route'larda kullanılacak
        
        // 4. TEMA - Dil'den sonra
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckThemeStatus::class);
        
        // 5. MissingPageRedirector - DISABLED: Custom tenant-aware 404 page kullanılıyor
        // $middleware->appendToGroup('web', \Spatie\MissingPageRedirector\RedirectsMissingPages::class);

        // 5.5. Security Headers - Performance & Security Best Practices
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);

        // 6. Database Pool - Connection pooling için (Tenant middleware'den sonra)
        $middleware->appendToGroup('web', \App\Http\Middleware\DatabasePoolMiddleware::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\DatabasePoolMiddleware::class);
        
        // 7. Resource Tracking - Gerçek sistem verilerini topla
        $middleware->appendToGroup('web', \Modules\TenantManagement\App\Http\Middleware\ResourceTrackingMiddleware::class);
        $middleware->appendToGroup('api', \Modules\TenantManagement\App\Http\Middleware\ResourceTrackingMiddleware::class);
        
        // 8. ROOT-ONLY DEBUGBAR - Auth'tan sonra çalışmalı
        $middleware->appendToGroup('web', \App\Http\Middleware\RootOnlyDebugbar::class);

        // 9. LIVEWIRE JSON SANITIZER - Livewire JSON responses için UTF-8 sanitization
        $middleware->appendToGroup('web', \App\Http\Middleware\LivewireJsonSanitizer::class);

        // 10. UNDER CONSTRUCTION PROTECTION - Tenant 1001 (muzibu.com.tr) için şifre koruması
        $middleware->appendToGroup('web', \App\Http\Middleware\UnderConstructionProtection::class);

        // Middleware alias tanımları
        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenancy::class,
            // 'auth.cache.bypass' => \App\Http\Middleware\AuthCacheBypass::class, // KALDIRILDI - Performance killer!
            'page.tracker' => \App\Http\Middleware\PageTracker::class,
            'root.access' => \App\Http\Middleware\RootAccessMiddleware::class,
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'admin.nocache' => \App\Http\Middleware\AdminNoCacheMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'module.permission' => \Modules\UserManagement\App\Http\Middleware\ModulePermissionMiddleware::class,
            'locale.admin' => \Modules\LanguageManagement\app\Http\Middleware\AdminSetLocaleMiddleware::class,
            'locale.site' => \Modules\LanguageManagement\app\Http\Middleware\SiteSetLocaleMiddleware::class,
            'ai.tokens' => \App\Http\Middleware\CheckAITokensMiddleware::class,
            'admin.tenant.select' => \App\Http\Middleware\AdminTenantSelection::class,
            'database.pool' => \App\Http\Middleware\DatabasePoolMiddleware::class,
            'tenant.rate.limit' => \Modules\TenantManagement\App\Http\Middleware\TenantRateLimitMiddleware::class,
            'auto.queue.health' => \App\Http\Middleware\AutoQueueHealthCheck::class, // 🚀 OTOMATIK QUEUE HEALTH CHECK
            'root.debugbar' => \App\Http\Middleware\RootOnlyDebugbar::class, // 🛠️ ROOT-ONLY DEBUGBAR
            'frontend.auto.seo' => \App\Http\Middleware\FrontendAutoSeoFillMiddleware::class, // 🎯 FRONTEND AUTO SEO FILL
            // Membership middleware
            'device.limit' => \App\Http\Middleware\CheckDeviceLimit::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'approved' => \App\Http\Middleware\CheckApproval::class,
            // Under construction protection
            'construction' => \App\Http\Middleware\UnderConstructionProtection::class,
            // Rate limiting by user type (guest/member/premium)
            'throttle.user' => \App\Http\Middleware\ThrottleByUserType::class,
            // Signed URL validation for protected resources
            'signed.url' => \App\Http\Middleware\ValidateSignedUrl::class,
            // HLS encryption key - No session middleware
            'hls.nosession' => \App\Http\Middleware\NoSessionForHlsKey::class,
        ]);
                
        // Admin middleware grubu
        $middleware->group('admin', [
            'web',
            'auth',
            'admin.access',
            'admin.nocache', // MUTLAK CACHE ENGELLEMESİ
            'locale.admin',
            // 'auto.queue.health', // 🚨 GEÇİCİ OLARAK KAPALI - Horizon boot loop sorununu çözüyor
            \App\Http\Middleware\FixLegacyTenantUrls::class, // 🔧 Eski tenant URL'lerini düzelt
        ]);
        
        // API middleware grubu
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // 🔐 Sanctum session auth
            \App\Http\Middleware\InitializeTenancy::class, // 🔥 Tenant initialization for API
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        
        // Site middleware grubu (admin olmayan rotalar için)
        $middleware->group('site', [
            'web',
            'locale.site', // Locale belirleme (URL parse için gerekli)
            'frontend.auto.seo', // 🎯 Frontend Auto SEO Fill (Premium tenants) - CACHE'DEN ÖNCE ÇALIŞMALI!
            \Spatie\ResponseCache\Middlewares\CacheResponse::class, // ✅ Response cache (URL-based, locale'den bağımsız)
        ]);

        // Prefetch Cache Headers - TÜM WEB MIDDLEWARE'LERDEN SONRA (EN SONDA!)
        $middleware->appendToGroup('web', \App\Http\Middleware\FixResponseCacheHeaders::class);
                
        // Module middleware grupları - her modül için yetki kontrolü
        $modules = [];
        if (is_dir(base_path('Modules'))) {
            $modules = array_diff(scandir(base_path('Modules')), ['.', '..']);
        }

        foreach ($modules as $module) {
            $moduleName = strtolower($module);
            $middleware->group('module.' . $moduleName, [
                'web',
                'auth',
                'module.permission:' . $moduleName . ',view'
            ]);
        }

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() == 503) {
                return response()->view('errors.offline', ['domain' => $request->getHost()], 503);
            }
        });
    })
    ->create();

// 🔥 Bind custom Console Kernel for blog-ai scheduler
$app->singleton(
    \Illuminate\Contracts\Console\Kernel::class,
    \App\Console\Kernel::class
);

// Helper dosylarını doğru sırada yükle
$helperFiles = [
    app_path('Helpers/LivewireUploadHelper.php'), // 0. Livewire upload helper (Config'den ÖNCE!)
    app_path('Helpers/Functions.php'),           // 1. Temel fonksiyonlar (Log, Settings dahil)
    app_path('Helpers/CacheHelper.php'),         // 2. Cache helper
    app_path('Helpers/LanguageHelper.php'),      // 3. Dil helper'ları
    app_path('Helpers/RouteHelper.php'),         // 4. Route helper'ları
    app_path('Helpers/TranslationHelper.php'),   // 5. Translation helper
    app_path('Helpers/TenantHelpers.php'),       // 6. Tenant helper'ları
    app_path('Helpers/ModulePermissionHelper.php'), // 7. Permission helper
    app_path('Helpers/AITokenHelper.php'),       // 8. AI Token helper
];

foreach ($helperFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

return $app;