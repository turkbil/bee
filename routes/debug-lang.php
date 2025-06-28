<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Debug Language Test Routes
Route::middleware(['web'])->prefix('debug-lang')->group(function () {
    
    // 1. Test Frontend Language Switcher  
    Route::get('/frontend-test', function () {
        return view('debug.language-frontend-test');
    })->name('debug.language.frontend');
    
    // All-in-One Debug Page
    Route::get('/all-debug', function () {
        return view('debug.language-all-in-one');
    })->name('debug.language.all');
    
    // 2. Test Admin Language Switcher  
    Route::get('/admin-test', function () {
        return view('debug.language-admin-test');
    })->name('debug.language.admin');
    
    // 3. Test Session Check
    Route::get('/session-check', function (Request $request) {
        return response()->json([
            'session_site_locale' => session('site_locale'), // DOĞRU KEY
            'session_site_language' => session('site_language'), // ESKİ KEY (null olmalı)
            'session_locale' => session('locale'), // ESKİ KEY (null olmalı)
            'app_locale' => app()->getLocale(),
            'request_all' => $request->all(),
            'available_languages' => \Modules\LanguageManagement\App\Models\SiteLanguage::where('is_active', true)->get()
        ]);
    })->name('debug.language.session');
    
    // 4. Test Language Switch Directly
    Route::post('/switch-test/{code}', function (Request $request, $code) {
        // SADECE DOĞRU KEY'İ KULLAN
        session(['site_locale' => $code]);
        session()->save();
        
        // Laravel locale'ini de ayarla
        app()->setLocale($code);
        
        return response()->json([
            'success' => true,
            'switched_to' => $code,
            'session_site_locale' => session('site_locale'),
            'session_site_language' => session('site_language'), // ESKİ KEY (null olmalı)
            'session_locale' => session('locale'), // ESKİ KEY (null olmalı)
            'app_locale' => app()->getLocale(),
            'all_session' => session()->all()
        ]);
    })->name('debug.language.switch');
    
    // 5. Test Livewire Component Data
    Route::get('/livewire-data', function () {
        $component = new \Modules\LanguageManagement\App\Http\Livewire\LanguageSwitcher();
        $component->mount();
        
        return response()->json([
            'currentLanguage' => $component->currentLanguage,
            'availableLanguages' => $component->availableLanguages,
            'showDropdown' => $component->showDropdown,
            'session_site_language' => session('site_language'),
            'session_locale' => session('locale')
        ]);
    })->name('debug.language.livewire');
    
    // 6. Get Recent Logs
    Route::get('/get-logs', function () {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recentLines = array_slice($lines, -30); // Son 30 satır
            
            return response()->json([
                'success' => true,
                'logs' => $recentLines,
                'total_lines' => count($lines)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'logs' => ['Log file not found'],
            'total_lines' => 0
        ]);
    })->name('debug.language.logs');
});

// Site dil debug sayfası
Route::get('/debug/site-language', function () {
    return view('debug.site-language-debug');
})->name('debug.site.language')->middleware('web');

// Cache temizleme endpoint'i
Route::post('/debug/clear-cache/{type}', function (Request $request, $type) {
    $result = ['success' => false, 'message' => 'Bilinmeyen tip'];
    
    try {
        switch ($type) {
            case 'config':
                \Illuminate\Support\Facades\Artisan::call('config:clear');
                $result = ['success' => true, 'message' => 'Config cache temizlendi'];
                break;
                
            case 'route':
                \Illuminate\Support\Facades\Artisan::call('route:clear');
                $result = ['success' => true, 'message' => 'Route cache temizlendi'];
                break;
                
            case 'view':
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                $result = ['success' => true, 'message' => 'View cache temizlendi'];
                break;
                
            case 'all':
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('config:clear');
                \Illuminate\Support\Facades\Artisan::call('route:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                
                // Response cache temizleme
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    \Spatie\ResponseCache\Facades\ResponseCache::clear();
                }
                
                // Redis cache temizleme (site_locale için)
                if (config('cache.default') === 'redis') {
                    \Illuminate\Support\Facades\Redis::flushdb();
                }
                
                $result = ['success' => true, 'message' => 'Tüm cache\'ler temizlendi'];
                break;
        }
    } catch (Exception $e) {
        $result = ['success' => false, 'message' => 'Hata: ' . $e->getMessage()];
    }
    
    return response()->json($result);
})->name('debug.clear.cache')->middleware('web');

// Test için hızlı dil değiştirme
Route::get('/debug/quick-lang/{locale}', function ($locale) {
    // Hızlı session ayarlama
    session(['site_locale' => $locale]);
    app()->setLocale($locale);
    
    return redirect('/debug/site-language')->with('message', "Dil $locale olarak ayarlandı");
})->name('debug.quick.lang')->middleware('web');

// Session debug
Route::get('/debug/session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'all_session' => session()->all(),
        'site_locale' => session('site_locale'),
        'admin_locale' => session('admin_locale'),
        'current_locale' => app()->getLocale(),
        'user_preference' => auth()->check() ? auth()->user()->site_language_preference : null,
    ]);
})->name('debug.session')->middleware('web');

// Database debug
Route::get('/debug/database', function () {
    try {
        $siteLanguages = \Modules\LanguageManagement\app\Models\SiteLanguage::all();
        $systemLanguages = \Modules\LanguageManagement\app\Models\SystemLanguage::all();
        
        return response()->json([
            'site_languages' => $siteLanguages->toArray(),
            'system_languages' => $systemLanguages->toArray(),
            'default_site_lang' => \Modules\LanguageManagement\app\Models\SiteLanguage::where('is_default', true)->first(),
            'active_site_langs' => \Modules\LanguageManagement\app\Models\SiteLanguage::where('is_active', true)->get(),
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('debug.database')->middleware('web');

// Cache debug
Route::get('/debug/cache', function () {
    $cacheData = [];
    
    try {
        // Response cache keys
        if (config('cache.default') === 'redis') {
            $cacheData['redis_keys'] = \Illuminate\Support\Facades\Redis::keys('*');
            $cacheData['response_cache_keys'] = \Illuminate\Support\Facades\Redis::keys('*responsecache*');
        }
        
        // Config değerleri
        $cacheData['config'] = [
            'cache_default' => config('cache.default'),
            'response_cache_enabled' => config('responsecache.enabled'),
            'session_driver' => config('session.driver'),
        ];
        
        // Auth cache key
        $cacheData['auth_cache_key'] = auth()->check() ? 'auth_' . auth()->id() : 'guest';
        
    } catch (Exception $e) {
        $cacheData['error'] = $e->getMessage();
    }
    
    return response()->json($cacheData);
})->name('debug.cache')->middleware('web');

// Route list debug
Route::get('/debug/routes', function () {
    $routes = [];
    
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        if (strpos($route->getName() ?? '', 'language') !== false) {
            $routes[] = [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'middleware' => $route->middleware(),
            ];
        }
    }
    
    return response()->json($routes);
})->name('debug.routes')->middleware('web');