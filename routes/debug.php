<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Services\ModuleSlugService;

// Dynamic Route Debug Sayfası
Route::get('/debug/dynamic-routes', function () {
    return view('debug.dynamic-route-test');
})->name('debug.dynamic-routes');

// Cache temizleme route'ları
Route::get('/debug/clear-cache', function () {
    Cache::flush();
    return redirect('/debug/dynamic-routes')->with('message', '✅ Tüm cache temizlendi!');
})->name('debug.clear-cache');

Route::get('/debug/clear-module-cache', function () {
    ModuleSlugService::clearCache();
    return redirect('/debug/dynamic-routes')->with('message', '✅ Module cache temizlendi!');
})->name('debug.clear-module-cache');

Route::get('/debug/clear-route-cache', function () {
    $resolver = app(\App\Contracts\DynamicRouteResolverInterface::class);
    $resolver->clearRouteCache();
    return redirect('/debug/dynamic-routes')->with('message', '✅ Route cache temizlendi!');
})->name('debug.clear-route-cache');

// Test route'ları - Manuel slug testleri
Route::get('/debug/test-slug/{module}/{action}', function ($module, $action) {
    $slug = ModuleSlugService::getSlug($module, $action);
    return response()->json([
        'module' => $module,
        'action' => $action,
        'slug' => $slug,
        'timestamp' => now()
    ]);
})->name('debug.test-slug');

Route::get('/debug/test-resolve/{slug1}/{slug2?}/{slug3?}', function ($slug1, $slug2 = null, $slug3 = null) {
    $resolver = app(\App\Contracts\DynamicRouteResolverInterface::class);
    
    try {
        $result = $resolver->resolve($slug1, $slug2, $slug3);
        return response()->json([
            'input' => [
                'slug1' => $slug1,
                'slug2' => $slug2,
                'slug3' => $slug3
            ],
            'result' => $result,
            'found' => $result !== null,
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'input' => [
                'slug1' => $slug1,
                'slug2' => $slug2,
                'slug3' => $slug3
            ],
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()
        ], 500);
    }
})->name('debug.test-resolve');

// DEBUG ROUTES - WEB.PHP'DEN TAŞINDI
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