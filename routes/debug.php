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