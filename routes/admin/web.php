<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Middleware\InitializeTenancy;

// Genel admin rotaları - sadece roller tablosunda kaydı olan kullanıcılar için  
Route::middleware(['web', 'auth', 'tenant', 'locale.admin'])->prefix('admin')->name('admin.')->group(function () {
    
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
    
    
    // Yetkisiz erişim sayfası - özel 403 sayfasına yönlendirilecek
    Route::get('/access-denied', function() {
        abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
    })->name('access.denied');
    
    // Debug log endpoint - sadece geliştirme için
    Route::post('/debug-log', function() {
        if (!config('app.debug')) return response('Disabled', 403);
        
        $data = request()->json()->all();
        \Illuminate\Support\Facades\Log::channel('single')->info('JS_DEBUG', $data);
        
        return response()->json(['status' => 'logged']);
    })->name('debug.log');
    
    // Cache clear endpoints
    Route::post('/cache/clear', [\App\Http\Controllers\Admin\CacheController::class, 'clearCache'])->name('cache.clear');
    Route::post('/cache/clear-all', [\App\Http\Controllers\Admin\CacheController::class, 'clearAllCache'])->name('cache.clear.all');
    
    // Debug routes
    Route::get('/debug-language', function() {
        return view('admin.debug-language');
    })->name('debug.language');
    
    Route::post('/debug-clear-sessions', function() {
        session()->forget(['admin_locale', 'site_locale']);
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
        session(['site_locale' => $locale]);
        
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
                'site_locale' => session('site_locale')
            ]
        ]);
    })->name('debug.site.language');
    
    // Admin dil değiştirme - SADECE admin_locale değişir, site_locale değişmez
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
        
        // SADECE admin_locale güncelle, site_locale'e dokunma
        auth()->user()->update(['admin_locale' => $locale]);
        session(['admin_locale' => $locale]);
        app()->setLocale($locale);
        
        // Site locale session'ını koru (değiştirme)
        // $currentSiteLocale = session('site_locale'); - Bu otomatik korunur
        
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
                'site_locale' => session('site_locale')
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
        
        // SADECE admin_locale güncelle, site_locale'e dokunma
        auth()->user()->update(['admin_locale' => $locale]);
        session(['admin_locale' => $locale]);
        app()->setLocale($locale);
        
        \Log::info('✅ Admin locale güncellendi', [
            'new_admin_locale' => $locale,
            'site_locale_unchanged' => session('site_locale'),
            'session_after' => [
                'admin_locale' => session('admin_locale'),
                'site_locale' => session('site_locale')
            ]
        ]);
        
        // Site locale session'ını koru (değiştirme)
        // $currentSiteLocale = session('site_locale'); - Bu otomatik korunur
        
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
                'site_locale' => session('site_locale')
            ],
            'redirect_to' => 'back'
        ]);
        
        return redirect()->back();
    })->name('admin.language.switch');
});

// Diğer admin routes - spesifik modül erişimleri için admin.access middleware'i kullanabilirsiniz
Route::middleware(['web', 'auth', InitializeTenancy::class, 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Burada spesifik admin kontrolleri gerektiren rotaları tanımlayabilirsiniz
});