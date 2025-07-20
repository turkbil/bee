<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
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
        // 1. TENANT - Domain belirleme (EN ÖNCELİKLİ) - Sadece web
        $middleware->prependToGroup('web', \App\Http\Middleware\InitializeTenancy::class);
        
        // 2. DİL - Tenant'tan HEMEN sonra (Session'dan ÖNCE çalışmalı) - admin hariç
        // SiteSetLocaleMiddleware web grubundan kaldırıldı, sadece belirli route'larda kullanılacak
        
        // 4. TEMA - Dil'den sonra
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckThemeStatus::class);
        
        // 5. SEO & CACHE - Son sırada
        $middleware->appendToGroup('web', \App\Http\Middleware\SeoMetaTagMiddleware::class);
        $middleware->appendToGroup('web', \Spatie\MissingPageRedirector\RedirectsMissingPages::class);
        $middleware->appendToGroup('web', \Spatie\ResponseCache\Middlewares\CacheResponse::class);
        
        // Middleware alias tanımları
        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenancy::class,
            // 'auth.cache.bypass' => \App\Http\Middleware\AuthCacheBypass::class, // KALDIRILDI - Performance killer!
            'page.tracker' => \App\Http\Middleware\PageTracker::class,
            'root.access' => \App\Http\Middleware\RootAccessMiddleware::class,
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'module.permission' => \Modules\UserManagement\App\Http\Middleware\ModulePermissionMiddleware::class,
            'locale.admin' => \Modules\LanguageManagement\app\Http\Middleware\AdminSetLocaleMiddleware::class,
            'locale.site' => \Modules\LanguageManagement\app\Http\Middleware\SiteSetLocaleMiddleware::class,
            'ai.tokens' => \App\Http\Middleware\CheckAITokensMiddleware::class,
            'admin.tenant.select' => \App\Http\Middleware\AdminTenantSelection::class,
        ]);
                
        // Admin middleware grubu
        $middleware->group('admin', [
            'web',
            'auth',
            'admin.access',
            'locale.admin',
        ]);
        
        // API middleware grubu
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        
        // Site middleware grubu (admin olmayan rotalar için)
        $middleware->group('site', [
            'web',
            'locale.site',
        ]);
                
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

// Helper dosylarını doğru sırada yükle
$helperFiles = [
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