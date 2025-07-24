<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Http\Middleware\InitializeTenancy;

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
        
        // 🧹 TENANT-AWARE RESPONSE CACHE TEMİZLEME (sadece admin interface için)
        try {
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                $tenant = tenant();
                if ($tenant) {
                    $tenantTag = 'tenant_' . $tenant->id . '_response_cache';
                    \Spatie\ResponseCache\Facades\ResponseCache::forget($tenantTag);
                    \Log::info('🧹 Tenant cache temizlendi', ['tenant_id' => $tenant->id]);
                } else {
                    // Central domain için
                    $centralTag = 'central_response_cache';
                    \Spatie\ResponseCache\Facades\ResponseCache::forget($centralTag);
                    \Log::info('🧹 Central cache temizlendi');
                }
            }
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
});

// Diğer admin routes - spesifik modül erişimleri için admin.access middleware'i kullanabilirsiniz
Route::middleware(['web', 'auth', InitializeTenancy::class, 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Burada spesifik admin kontrolleri gerektiren rotaları tanımlayabilirsiniz
});