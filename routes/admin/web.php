<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Http\Middleware\InitializeTenancy;
use Modules\LanguageManagement\app\Models\TenantLanguage;

// System health check endpoint - AI çeviri için
Route::middleware(['web', 'auth', 'tenant'])->get('/admin/system/health', [App\Http\Controllers\HealthController::class, 'systemHealth'])->name('admin.system.health');

// 🚀 GERÇEK ZAMANLI PROGRESS TRACKING API
Route::middleware(['web', 'auth', 'tenant'])->post('/admin/api/translation-progress', [App\Http\Controllers\Admin\TranslationProgressController::class, 'checkProgress'])->name('admin.translation.progress');

// Laravel log temizleme API (admin only)
Route::middleware(['web', 'auth', 'tenant'])->post('/admin/api/clear-log', [App\Http\Controllers\Admin\TranslationProgressController::class, 'clearLog'])->name('admin.clear.log');

// Tenant dilleri API endpoint'i - AI Uyarı Sistemi ile Güçlendirildi
Route::middleware(['web', 'auth', 'tenant'])->get('/admin/api/tenant-languages', function () {
    try {
        // GÜNCELLEME: AI uyarı sistemi için is_main_language bilgisi de dahil edildi
        $languages = TenantLanguage::where('is_visible', true)
            ->orderBy('sort_order')
            ->get(['code', 'name', 'flag_icon', 'is_main_language'])
            ->map(function ($language) {
                return [
                    'code' => $language->code,
                    'name' => $language->name,
                    'flag' => $language->flag_icon ?? getFlagForLanguage($language->code),
                    'is_main_language' => (bool) $language->is_main_language  // AI uyarı sistemi için
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'languages' => $languages
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load tenant languages: ' . $e->getMessage()
        ], 500);
    }
});

// Global Language Session Update - Tüm modüller kullanabilir
Route::middleware(['admin', 'tenant'])->post('/admin/language/update-session', function (\Illuminate\Http\Request $request) {
    $language = $request->input('language');
    $availableLanguages = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();

    if ($language && in_array($language, $availableLanguages)) {
        session(['admin_locale' => $language]);
        return response()->json([
            'success' => true,
            'status' => 'success',
            'language' => $language
        ]);
    }

    return response()->json([
        'success' => false,
        'status' => 'error',
        'message' => 'Invalid language code'
    ], 400);
})->name('language.update-session');

// Cache temizleme endpoint'i
Route::middleware(['admin', 'tenant'])->post('/admin/cache/clear', function () {
    try {
        // Tenant-specific cache temizleme
        Cache::flush();
        
        // Response cache temizleme (eğer varsa)
        if (config('responsecache.enabled')) {
            Artisan::call('responsecache:clear');
        }
        
        // View cache temizleme
        Artisan::call('view:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'Tenant cache başarıyla temizlendi'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Cache temizlenirken hata oluştu: ' . $e->getMessage()
        ], 500);
    }
})->name('cache.clear');

// Debug Route'ları - Translation Debug System  
Route::middleware(['admin', 'tenant'])->prefix('admin/debug')->name('admin.debug.')->group(function () {
    Route::get('/translation', [\App\Http\Controllers\Admin\TranslationDebugController::class, 'index'])->name('translation');
    Route::post('/translation/test', [\App\Http\Controllers\Admin\TranslationDebugController::class, 'testTranslation'])->name('translation.test');
    Route::post('/translation/clear-logs', [\App\Http\Controllers\Admin\TranslationDebugController::class, 'clearLogs'])->name('translation.clear-logs');
    Route::post('/translation/clear-cache', [\App\Http\Controllers\Admin\TranslationDebugController::class, 'clearCache'])->name('translation.clear-cache');
    Route::get('/translation/stream-logs', [\App\Http\Controllers\Admin\TranslationDebugController::class, 'streamLogs'])->name('translation.stream-logs');
    
    // Advanced Debug Routes
    Route::post('/translation/test-page', [\App\Http\Controllers\Admin\TranslationDebugAdvancedController::class, 'testPageTranslation'])->name('translation.test-page');
    Route::post('/translation/analyze-page', [\App\Http\Controllers\Admin\TranslationDebugAdvancedController::class, 'analyzePageData'])->name('translation.analyze-page');
    Route::get('/translation/full-debug', [\App\Http\Controllers\Admin\TranslationDebugAdvancedController::class, 'fullSystemDebug'])->name('translation.full-debug');
});

// Genel admin rotaları - sadece roller tablosunda kaydı olan kullanıcılar için  
Route::middleware(['admin', 'tenant'])->prefix('admin')->name('admin.')->group(function () {
    
    // Slug benzersizlik kontrolü - AJAX
    Route::post('/check-slug', function(\Illuminate\Http\Request $request) {
        $slug = $request->input('slug');
        $module = $request->input('module', 'Page');
        $excludeId = $request->input('exclude_id');
        
        if (!$slug) {
            return response()->json(['unique' => false, 'message' => 'Slug boş olamaz']);
        }
        
        // Module'a göre model seç
        $modelClass = "Modules\\{$module}\\App\\Models\\{$module}";
        
        if (!class_exists($modelClass)) {
            return response()->json(['unique' => false, 'message' => 'Model bulunamadı']);
        }
        
        $query = $modelClass::where('slug->tr', $slug)
            ->orWhere('slug->en', $slug)
            ->orWhere('slug->ar', $slug);
            
        // Mevcut kaydı hariç tut (düzenleme sırasında)
        if ($excludeId) {
            $primaryKey = (new $modelClass)->getKeyName();
            $query->where($primaryKey, '!=', $excludeId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'unique' => !$exists,
            'message' => $exists ? 'Bu slug zaten kullanılıyor' : 'Kullanılabilir'
        ]);
    });
    
    // /admin rotası - dashboard'a yönlendir
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    // Admin dashboard rotası - TÜM yetkilendirilmiş kullanıcılar için (editor, admin, root)
    Route::get('/dashboard', function () {
        
        // Rol kontrolü
        if (!auth()->user()->hasAnyRole(['admin', 'root', 'editor'])) {
            abort(403, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }
        
        $currentTenant = null;
        
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        }
        
        if (!$currentTenant) {
            return view('admin.index');
        }
        
        // Redis'te tenant bazlı önbellekleme
        $redisKey = "tenant:{$currentTenant->id}:stats";
        $tenantStats = Cache::store('redis')->remember($redisKey, 3600, function () use ($currentTenant) {
            return [
                'id' => $currentTenant->id,
                'name' => $currentTenant->title ?? 'Varsayılan',
                'domain' => request()->getHost(),
                'created_at' => $currentTenant->created_at?->format('d.m.Y H:i:s') ?? 'Belirtilmemiş'
            ];
        });
        
        return view('admin.index', compact('tenantStats'));
    })->name('dashboard');
    
    // Dashboard widget layout API endpoints
    Route::post('/dashboard/save-layout', [\App\Http\Controllers\Admin\DashboardController::class, 'saveDashboardLayout'])->name('dashboard.save-layout');
    Route::get('/dashboard/get-layout', [\App\Http\Controllers\Admin\DashboardController::class, 'getDashboardLayout'])->name('dashboard.get-layout');
    
    
    // Yetkisiz erişim sayfası - özel 403 sayfasına yönlendirilecek
    Route::get('/access-denied', function() {
        abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
    })->name('access.denied');
    
    
    // Cache clear endpoints
    Route::post('/cache/clear', [\App\Http\Controllers\Admin\CacheController::class, 'clearCache'])->name('cache.clear');
    Route::post('/cache/clear-all', [\App\Http\Controllers\Admin\CacheController::class, 'clearAllCache'])->name('cache.clear.all');
    
    // AI Widget Routes
    Route::group(['prefix' => 'ai', 'as' => 'ai.'], function () {
        Route::post('/execute-feature', [\Modules\AI\App\Http\Controllers\Admin\Chat\AIChatController::class, 'executeWidgetFeature'])->name('execute-feature');
        Route::post('/send-message', [\Modules\AI\App\Http\Controllers\Admin\Chat\AIChatController::class, 'sendMessage'])->name('send-message');
    });
    
    // Translation routes - Background processing for long-running translations
    Route::prefix('translation')->name('translation.')->group(function () {
        Route::post('/translate-page-async', [\App\Http\Controllers\Admin\TranslationController::class, 'translatePageAsync'])->name('page.async');
        Route::get('/status', [\App\Http\Controllers\Admin\TranslationController::class, 'checkTranslationStatus'])->name('status');
        Route::post('/instant', [\App\Http\Controllers\Admin\TranslationController::class, 'translateInstant'])->name('instant');
    });
    
    // API endpoints for AI Translation Modal
    Route::get('/api/tenant-languages', function () {
        try {
            // KAYNAK DİL İÇİN: TÜM görünür diller (ana dil dahil)
            $languages = TenantLanguage::where('is_visible', true)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->select('code', 'name', 'native_name', 'flag_icon', 'is_main_language')
                ->get()
                ->map(function ($lang) {
                    return [
                        'code' => $lang->code,
                        'name' => $lang->name,
                        'native_name' => $lang->native_name,
                        'flag' => $lang->flag_icon ?? getFlagForLanguage($lang->code),
                        'is_main_language' => (bool) $lang->is_main_language
                    ];
                });
            
            return response()->json([
                'success' => true,
                'languages' => $languages->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error('Tenant languages API error: ' . $e->getMessage());
            
            // Fallback with basic languages
            $fallbackLanguages = [
                ['code' => 'tr', 'name' => 'Turkish', 'native_name' => 'Türkçe', 'flag' => '🇹🇷'],
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧'],
                ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'flag' => '🇸🇦'],
            ];
            
            return response()->json([
                'success' => true,
                'languages' => $fallbackLanguages
            ]);
        }
    })->name('api.tenant-languages');
    
    // SEO Management Routes - Artık SeoManagement modülünde
    
    // Global SEO Widget Routes - Artık SeoManagement modülünde
    
    // Debug routes
    Route::get('/debug-language', function() {
        return view('admin.debug-language');
    })->name('debug.language');
    
    // Real-time debug page
    Route::get('/debug-page', function() {
        return view('admin.debug-page');
    })->name('debug.page');
    
    Route::post('/debug-clear-sessions', function() {
        session()->forget(['admin_locale', 'tenant_locale']);
        return response()->json(['status' => 'success', 'message' => 'Sessions cleared']);
    })->name('debug.clear.sessions');
    
    // JavaScript Language Session Route - CRITICAL FIX
    Route::post('/set-js-language', function() {
        $language = request()->input('language');
        
        if (in_array($language, ['tr', 'en', 'ar'])) {
            session(['js_current_language' => $language]);
            
            \Log::info('📝 AJAX: JavaScript language session\'a kaydedildi', [
                'language' => $language,
                'session_set' => true,
                'request_method' => 'AJAX'
            ]);
            
            return response()->json([
                'success' => true,
                'language' => $language,
                'message' => 'Language saved to session'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid language'
        ], 400);
    })->name('set.js.language');
    
    Route::post('/debug-site-language/{locale}', function($locale) {
        // Tenant language kodlarını kontrol et
        $validSiteLanguages = [];
        try {
            $validSiteLanguages = \DB::table('tenant_languages')->where('is_active', true)->pluck('code')->toArray();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
        
        if (!in_array($locale, $validSiteLanguages)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid language code', 'valid' => $validSiteLanguages]);
        }
        
        // Site locale session'ını güncelle
        session(['tenant_locale' => $locale]);
        
        // Cache temizleme
        $tenantId = tenant('id');
        if ($tenantId) {
            cache()->tags(["tenant_{$tenantId}_response_cache"])->flush();
        }
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Site language changed to ' . $locale,
            'new_locale' => $locale,
            'session' => [
                'admin_locale' => session('admin_locale'),
                'tenant_locale' => session('tenant_locale')
            ]
        ]);
    })->name('debug.site.language');
    
    // Admin dil değiştirme - SADECE admin_locale değişir, tenant_locale değişmez
    Route::get('/language/{locale}', function ($locale) {
        // AdminLanguage tablosundan geçerli dilleri kontrol et
        $validAdminLanguages = [];
        try {
            if (class_exists('Modules\LanguageManagement\App\Models\AdminLanguage')) {
                $validAdminLanguages = \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)
                    ->pluck('code')
                    ->toArray();
            }
        } catch (\Exception $e) {
            $validAdminLanguages = ['tr', 'en']; // Fallback
        }
        
        // Geçerlik kontrolü - sadece admin language tablosundaki diller
        if (!in_array($locale, $validAdminLanguages) || !auth()->check()) {
            return redirect()->back();
        }
        
        // SADECE admin_locale güncelle, tenant_locale'e dokunma
        auth()->user()->update(['admin_locale' => $locale]);
        session(['admin_locale' => $locale]);
        app()->setLocale($locale);
        
        // Site locale session'ını koru (değiştirme)
        // $currentSiteLocale = session('tenant_locale'); - Bu otomatik korunur
        
        // 🧹 TENANT-AWARE RESPONSE CACHE TEMİZLEME (sadece admin interface için)
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
            \Log::warning('Admin language switch cache clear error: ' . $e->getMessage());
        }
        
        return redirect()->back();
    })->name('language.switch');
    
    // Studio AJAX Language Switch - POST için
    Route::post('/language/switch', function () {
        $locale = request()->input('language');
        $type = request()->input('type', 'admin');
        
        \Log::info('🔄 STUDIO DİL DEĞİŞİMİ BAŞLADI', [
            'requested_language' => $locale,
            'type' => $type,
            'current_user_id' => auth()->id(),
            'current_url' => request()->url()
        ]);
        
        // Geçerli dilleri kontrol et
        $validLanguages = [];
        try {
            $validLanguages = \DB::table('tenant_languages')
                ->where('is_active', true)
                ->pluck('code')
                ->toArray();
        } catch (\Exception $e) {
            $validLanguages = ['tr']; // Fallback
            \Log::warning('tenant_languages tablosu bulunamadı, fallback kullanılıyor', ['error' => $e->getMessage()]);
        }
        
        // Geçerlik kontrolü
        if (!in_array($locale, $validLanguages) || !auth()->check()) {
            \Log::warning('❌ Geçersiz dil kodu veya giriş yapmamış', ['requested' => $locale, 'valid' => $validLanguages, 'authenticated' => auth()->check()]);
            return response()->json(['success' => false, 'message' => 'Geçersiz dil kodu'], 400);
        }
        
        // Session güncelle
        session(['admin_locale' => $locale]);
        app()->setLocale($locale);
        
        \Log::info('✅ Studio admin locale güncellendi', [
            'new_admin_locale' => $locale,
            'session_updated' => true
        ]);
        
        // 🧹 DİL DEĞİŞİMİ - Full cache clear (CacheManager)
        try {
            \App\Services\CacheManager::clearAllLanguageRelatedCaches();
            \Log::info('🧹 Studio dil değişimi: Tüm dil-related cache temizlendi', ['locale' => $locale]);
        } catch (\Exception $e) {
            \Log::warning('Studio cache clear error: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Dil başarıyla değiştirildi',
            'language' => $locale,
            'reload_required' => true
        ]);
    })->name('language.switch.ajax');

    // AdminLanguageSwitcher için alias route - SADECE admin_locale değişir
    Route::get('/admin-language/{locale}', function ($locale) {
        \Log::info('🔄 ADMİN DİL DEĞİŞİMİ BAŞLADI', [
            'requested_language' => $locale,
            'current_user_id' => auth()->id(),
            'current_url' => request()->url(),
            'session_before' => [
                'admin_locale' => session('admin_locale'),
                'tenant_locale' => session('tenant_locale')
            ]
        ]);
        
        // AdminLanguage tablosundan geçerli dilleri kontrol et
        $validAdminLanguages = [];
        try {
            if (class_exists('Modules\LanguageManagement\App\Models\AdminLanguage')) {
                $validAdminLanguages = \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)
                    ->pluck('code')
                    ->toArray();
            }
        } catch (\Exception $e) {
            $validAdminLanguages = ['tr', 'en']; // Fallback
            \Log::warning('AdminLanguage model bulunamadı, fallback kullanılıyor', ['error' => $e->getMessage()]);
        }
        
        \Log::info('✅ Geçerli admin dilleri kontrol edildi', ['valid_languages' => $validAdminLanguages]);
        
        // Geçerlik kontrolü - sadece admin language tablosundaki diller
        if (!in_array($locale, $validAdminLanguages) || !auth()->check()) {
            \Log::warning('❌ Geçersiz admin dil kodu veya giriş yapmamış', ['requested' => $locale, 'valid' => $validAdminLanguages, 'authenticated' => auth()->check()]);
            return redirect()->back();
        }
        
        // SADECE admin_locale güncelle, tenant_locale'e dokunma
        auth()->user()->update(['admin_locale' => $locale]);
        session(['admin_locale' => $locale]);
        app()->setLocale($locale);
        
        \Log::info('✅ Admin locale güncellendi', [
            'new_admin_locale' => $locale,
            'tenant_locale_unchanged' => session('tenant_locale'),
            'session_after' => [
                'admin_locale' => session('admin_locale'),
                'tenant_locale' => session('tenant_locale')
            ]
        ]);
        
        // Site locale session'ını koru (değiştirme)
        // $currentSiteLocale = session('tenant_locale'); - Bu otomatik korunur
        
        // 🧹 DİL DEĞİŞİMİ - Full cache clear (CacheManager)
        try {
            \App\Services\CacheManager::clearAllLanguageRelatedCaches();
            \Log::info('🧹 Admin dil değişimi: Tüm dil-related cache temizlendi', ['locale' => $locale]);
        } catch (\Exception $e) {
            \Log::warning('Admin language switch cache clear error: ' . $e->getMessage());
        }
        
        \Log::info('🎯 ADMİN DİL DEĞİŞİMİ TAMAMLANDI', [
            'final_sessions' => [
                'admin_locale' => session('admin_locale'),
                'tenant_locale' => session('tenant_locale')
            ],
            'redirect_to' => 'back'
        ]);
        
        return redirect()->back();
    })->name('admin.language.switch');

    // Cache Clear Routes
    Route::post('/cache/clear', function () {
        try {
            Cache::flush();
            return response()->json(['success' => true, 'message' => 'Cache temizlendi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cache temizleme başarısız'], 500);
        }
    })->name('cache.clear');

    Route::post('/cache/clear-all', function () {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json(['success' => true, 'message' => 'Sistem cache temizlendi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Sistem cache temizleme başarısız'], 500);
        }
    })->name('cache.clear.all');


    // AI Token Management routes kaldırıldı - AI modülündeki routes/admin.php kullanılıyor
    
    // System status endpoints for modal auto cleanup
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/queue-status', [App\Http\Controllers\Admin\SystemController::class, 'queueStatus'])->name('queue-status');
        Route::get('/health', [App\Http\Controllers\Admin\SystemController::class, 'healthCheck'])->name('health');
    });
    
});

// Simple Debug Routes - NEW (Basit ve etkili debug sistemi)
Route::middleware(['web', 'auth', InitializeTenancy::class])->prefix('admin/debug/simple')->name('admin.debug.simple.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\SimpleDebugController::class, 'index'])->name('index');
    Route::get('/status', [\App\Http\Controllers\Admin\SimpleDebugController::class, 'systemStatus'])->name('status');
    Route::get('/stream-logs', [\App\Http\Controllers\Admin\SimpleDebugController::class, 'streamLogs'])->name('stream-logs');
    Route::post('/clear-logs', [\App\Http\Controllers\Admin\SimpleDebugController::class, 'clearLogs'])->name('clear-logs');
    Route::post('/test-translation', [\App\Http\Controllers\Admin\SimpleDebugController::class, 'testTranslation'])->name('test-translation');
});

// Diğer admin routes - spesifik modül erişimleri için admin.access middleware'i kullanabilirsiniz
Route::middleware(['web', 'auth', InitializeTenancy::class, 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Burada spesifik admin kontrolleri gerektiren rotaları tanımlayabilirsiniz
});

// AI SILENT FALLBACK ROUTES
Route::middleware(['admin', 'tenant'])->prefix('admin/ai/silent-fallback')->name('admin.ai.silent-fallback.')->group(function () {
    Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, 'index'])->name('index');
    Route::get('/configuration', [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, 'configuration'])->name('configuration');
    Route::post('/test', [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, 'test'])->name('test');
    Route::post('/clear-stats', [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, 'clearStats'])->name('clear-stats');
    Route::get('/analytics', [\Modules\AI\App\Http\Controllers\Admin\SilentFallbackController::class, 'analytics'])->name('analytics');
});

// AI CENTRAL FALLBACK ROUTES
Route::middleware(['admin', 'tenant'])->prefix('admin/ai/central-fallback')->name('admin.ai.central-fallback.')->group(function () {
    Route::get('/', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'index'])->name('index');
    Route::get('/configuration', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'updateConfiguration'])->name('update-configuration');
    Route::post('/test', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'test'])->name('test');
    Route::get('/statistics', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'statistics'])->name('statistics');
    Route::post('/model-recommendations', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'modelRecommendations'])->name('model-recommendations');
    Route::post('/reset-failures', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'resetProviderFailures'])->name('reset-failures');
    Route::post('/clear-statistics', [\Modules\AI\App\Http\Controllers\Admin\CentralFallbackController::class, 'clearStatistics'])->name('clear-statistics');
});

// Universal Entity mapping endpoint for translation system
Route::middleware(['web', 'auth', 'tenant'])->get('/admin/api/entity-mapping', function () {
    try {
        $mapping = \App\Services\TranslationModuleRegistry::getEntityMapping();
        $stats = \App\Services\TranslationModuleRegistry::getStats();

        return response()->json([
            'success' => true,
            'mapping' => $mapping,
            'stats' => $stats,
            'cache_status' => 'registry_based'
        ]);
    } catch (\Exception $e) {
        \Log::error('🚨 Entity mapping registry error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Fallback to static mapping
        $fallbackMapping = [
            'portfolio_category' => 'portfolio-category',
            'portfolio' => 'portfolio',
            'announcement' => 'announcement',
            'page' => 'page'
        ];
        
        return response()->json([
            'success' => true,
            'mapping' => $fallbackMapping,
            'fallback' => true,
            'error' => $e->getMessage()
        ]);
    }
});

// ALTERNATIVE TRANSLATION STATUS - Livewire bypass  
Route::middleware(['web', 'auth', 'tenant'])->group(function () {
    Route::get('/admin/translation/progress', [App\Http\Controllers\TranslationStatusController::class, 'checkProgress']);
    Route::post('/admin/translation/progress', [App\Http\Controllers\TranslationStatusController::class, 'updateProgress']);
    
    // JavaScript için API endpoint
    Route::post('/admin/api/translation-status', [App\Http\Controllers\TranslationStatusController::class, 'checkProgress']);
});


// MODAL TEST ROUTES - AI çeviri modallarının test sayfaları (Page module pattern)
Route::middleware(["admin", "tenant"])->prefix("admin/page/modal-test")->name("admin.page.modal.test.")->group(function () {
    Route::get("/1", [App\Http\Controllers\Admin\ModalTestController::class, "test1"])->name("test1");
    Route::get("/2", [App\Http\Controllers\Admin\ModalTestController::class, "test2"])->name("test2");
    Route::get("/3", [App\Http\Controllers\Admin\ModalTestController::class, "test3"])->name("test3");
    Route::get("/4", [App\Http\Controllers\Admin\ModalTestController::class, "test4"])->name("test4");
    Route::get("/5", [App\Http\Controllers\Admin\ModalTestController::class, "test5"])->name("test5");
});

// AI MODAL DESIGN TEST ROUTES - Yeni modal tasarım alternatifleri
Route::middleware(["admin", "tenant"])->prefix("admin/page")->name("admin.page.")->group(function () {
    Route::get("/test", [App\Http\Controllers\Admin\AIModalTestController::class, "index"])->name("test");
});


