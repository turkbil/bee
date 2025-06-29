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

// DEBUG ROUTES - EN ÜSTTE OLMALI!
Route::get('/debug-routes', function () {
    $resolver = app(\App\Contracts\DynamicRouteResolverInterface::class);
    
    // ModuleSlugService testleri - DİNAMİK ACTION'LAR
    $moduleData = [];
    foreach (['Page', 'Portfolio', 'Announcement'] as $module) {
        // Her modülün config'inden gerçek action'larını al
        $configPath = base_path("Modules/{$module}/config/config.php");
        if (file_exists($configPath)) {
            $config = include $configPath;
            $actions = isset($config['routes']) ? array_keys($config['routes']) : ['index', 'show'];
        } else {
            $actions = ['index', 'show']; // fallback
        }
        
        foreach ($actions as $action) {
            try {
                $slug = \App\Services\ModuleSlugService::getSlug($module, $action);
                $moduleData[$module][$action] = $slug;
            } catch (Exception $e) {
                $moduleData[$module][$action] = 'ERROR: ' . $e->getMessage();
            }
        }
    }
    
    // Resolver testleri
    $testResults = [];
    $testCases = [
        ['sahife', null, null, 'Page Index'],
        ['sahife', 'iletisim', null, 'Page Show - İletişim'],
        ['sahife', 'hakkimizda', null, 'Page Show - Hakkımızda'],
        ['sahife', 'cerez-politikasi', null, 'Page Show - Çerez Politikası'],
        ['portfolios', null, null, 'Portfolio Index'],
        ['portfolios', 'kurumsal-web-sitesi-abc-holding', null, 'Portfolio Show - Gerçek'],
        ['duyurucuklar', null, null, 'Announcement Index'],
        ['duyurucuklar', 'yeni-hizmetimiz-yayinda', null, 'Announcement Show - Gerçek']
    ];
    
    foreach ($testCases as $test) {
        [$slug1, $slug2, $slug3, $desc] = $test;
        try {
            $result = $resolver->resolve($slug1, $slug2, $slug3);
            $testResults[] = [
                'desc' => $desc,
                'url' => '/' . $slug1 . ($slug2 ? '/' . $slug2 : ''),
                'status' => $result ? 'ÇALIŞIR' : 'ÇALIŞMAZ',
                'found' => $result !== null
            ];
        } catch (Exception $e) {
            $testResults[] = [
                'desc' => $desc,
                'url' => '/' . $slug1 . ($slug2 ? '/' . $slug2 : ''),
                'status' => 'HATA',
                'found' => false
            ];
        }
    }
    
    // Config vs Database
    $configs = [];
    foreach (['Page', 'Portfolio', 'Announcement'] as $module) {
        $configPath = base_path("Modules/{$module}/config/config.php");
        if (file_exists($configPath)) {
            $config = include $configPath;
            $configs[$module] = $config['slugs'] ?? [];
        }
    }
    
    $dbSettings = \App\Models\ModuleTenantSetting::all()->keyBy('module_name');
    
    return view('debug.routes-modern', compact('moduleData', 'testResults', 'configs', 'dbSettings'));
});


// Ana sayfa route'u  
Route::middleware(['web', 'locale.site', 'page.tracker'])->get('/', [\Modules\Page\App\Http\Controllers\Front\PageController::class, 'homepage'])->name('home');

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


// Site dil değiştirme route'u - Tenant-aware cache temizleme ile
Route::get('/language/{locale}', function($locale) {
    // Hızlı kontrol ve güncelleme
    if (in_array($locale, ['tr', 'en', 'ar'])) {
        session(['site_locale' => $locale]);
        app()->setLocale($locale);
        
        if (auth()->check()) {
            auth()->user()->update(['tenant_locale' => $locale]);
        }
        
        // 🧹 TENANT-AWARE RESPONSE CACHE TEMİZLEME
        try {
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                $tenant = tenant();
                if ($tenant) {
                    $tenantTag = 'tenant_' . $tenant->id . '_response_cache';
                    \Spatie\ResponseCache\Facades\ResponseCache::forget($tenantTag);
                } else {
                    // Central domain için
                    $centralTag = 'central_response_cache';
                    \Spatie\ResponseCache\Facades\ResponseCache::forget($centralTag);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Language switch cache clear error: ' . $e->getMessage());
        }
    }
    
    return redirect()->back();
})->middleware(['web'])->name('language.switch');

// Dinamik modül route'ları - sadece frontend içerik için
Route::middleware([InitializeTenancy::class, 'web', \Modules\LanguageManagement\app\Http\Middleware\SetLocaleMiddleware::class . ':site'])
    ->group(function () {
        // Dil prefix'li route'lar - tenant bazlı aktif diller
        Route::get('/{lang}/{slug1}', function($lang, $slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$');
        
        Route::get('/{lang}/{slug1}/{slug2}', function($lang, $slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+');
         
        Route::get('/{lang}/{slug1}/{slug2}/{slug3}', function($lang, $slug1, $slug2, $slug3) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3);
        })->where('lang', getSupportedLanguageRegex())
         ->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+')
         ->where('slug3', '[^/]+');
         
        // Catch-all route'ları - prefix olmayan - sadece content route'ları için
        // Regex ile admin, api vb. system route'larını hariç tut
        Route::get('/{slug1}', function($slug1) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1);
        })->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$');
        
        Route::get('/{slug1}/{slug2}', function($slug1, $slug2) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2);
        })->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
         ->where('slug2', '[^/]+');
         
        Route::get('/{slug1}/{slug2}/{slug3}', function($slug1, $slug2, $slug3) {
            return app(\App\Services\DynamicRouteService::class)->handleDynamicRoute($slug1, $slug2, $slug3);
        })->where('slug1', '^(?!admin|api|login|logout|register|password|auth|storage|css|js|assets|profile|dashboard|debug)[^/]+$')
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