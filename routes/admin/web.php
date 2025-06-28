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
    
    // Language switch route - hızlı dil değişimi
    Route::get('/language/{locale}', function ($locale) {
        // Cache'li dil kontrolü
        $cacheKey = "system_language_{$locale}";
        $language = Cache::remember($cacheKey, 3600, function() use ($locale) {
            if (class_exists('Modules\LanguageManagement\App\Models\SystemLanguage')) {
                return \Modules\LanguageManagement\App\Models\SystemLanguage::where('code', $locale)
                    ->where('is_active', true)
                    ->first();
            }
            return null;
        });
        
        if ($language && auth()->check()) {
            // Kullanıcı admin dil tercihini hemen güncelle
            $user = auth()->user();
            $user->admin_language_preference = $locale; // admin_language_preference kullan
            $user->language = $locale; // Eski alan da güncellensin
            $user->save();
            
            // Session'a da kaydet hızlı erişim için
            session(['admin_locale' => $locale, 'locale' => $locale]);
            
            // Laravel locale'ini hemen ayarla
            app()->setLocale($locale);
        }
        
        return redirect()->back();
    })->name('language.switch'); // AdminLanguageSwitcher için 'admin.language.switch' route name'i gerekiyor
    
    // AdminLanguageSwitcher için alias route - component admin.language.switch arıyor
    Route::get('/admin-language/{locale}', function ($locale) {
        // Yukarıdaki aynı mantığı kullan
        $cacheKey = "system_language_{$locale}";
        $language = Cache::remember($cacheKey, 3600, function() use ($locale) {
            if (class_exists('Modules\LanguageManagement\App\Models\SystemLanguage')) {
                return \Modules\LanguageManagement\App\Models\SystemLanguage::where('code', $locale)
                    ->where('is_active', true)
                    ->first();
            }
            return null;
        });
        
        if ($language && auth()->check()) {
            // Kullanıcı admin dil tercihini hemen güncelle
            $user = auth()->user();
            $user->admin_language_preference = $locale;
            $user->language = $locale;
            $user->save();
            
            // Session'a da kaydet hızlı erişim için  
            session(['admin_locale' => $locale, 'locale' => $locale]);
            
            // Laravel locale'ini hemen ayarla
            app()->setLocale($locale);
        }
        
        return redirect()->back();
    })->name('admin.language.switch');
});

// Diğer admin routes - spesifik modül erişimleri için admin.access middleware'i kullanabilirsiniz
Route::middleware(['web', 'auth', InitializeTenancy::class, 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    // Burada spesifik admin kontrolleri gerektiren rotaları tanımlayabilirsiniz
});