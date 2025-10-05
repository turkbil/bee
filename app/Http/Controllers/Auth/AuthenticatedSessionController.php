<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Services\CacheManager;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse|Response
    {
        // Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        // Auth sayfaları için locale - session'da varsa kullan, yoksa tenant default
        try {
            // Önce session'daki locale'i kontrol et (redirect'ten gelebilir)
            $sessionLocale = session('tenant_locale');

            if ($sessionLocale && is_valid_tenant_locale($sessionLocale)) {
                // Session'da locale varsa kullan
                app()->setLocale($sessionLocale);
            } else {
                // Yoksa tenant default locale kullan
                $tenant = tenant();
                $defaultLocale = $tenant && $tenant->tenant_default_locale
                    ? $tenant->tenant_default_locale
                    : 'tr';

                app()->setLocale($defaultLocale);
                session(['tenant_locale' => $defaultLocale]);
            }
        } catch (\Exception $e) {
            // Fallback
            app()->setLocale('tr');
        }

        // Login sayfası cache'lenmesin
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Son giriş zamanını güncelle
        $user = Auth::user();
        if ($user) {
            $user->last_login_at = Carbon::now();
            $user->save();
            
            // Giriş log'u
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'giriş yaptı';
                })
                ->log("\"{$user->name}\" giriş yaptı");
        }
        
        // User locale'lerini session'a yükle - REGENERATE'DEN ÖNCE!
        if ($user) {
            // Admin context locale'ini session'a kaydet
            if ($user->admin_locale) {
                session(['admin_locale' => $user->admin_locale]);
                \Log::info('🔄 LOGIN: Admin locale loaded', [
                    'user_id' => $user->id,
                    'admin_locale' => $user->admin_locale
                ]);
            }
            
            // Site context locale yükle (domain-specific)
            if ($user->tenant_locale) {
                $domain = request()->getHost();
                $sessionKey = 'tenant_locale_' . str_replace('.', '_', $domain);
                session([$sessionKey => $user->tenant_locale]);
                session(['tenant_locale' => $user->tenant_locale]);
                \Log::info('🔄 LOGIN: Tenant locale loaded', [
                    'user_id' => $user->id,
                    'tenant_locale' => $user->tenant_locale,
                    'domain_session_key' => $sessionKey
                ]);
            }
        }

        // 🧹 LOGIN CACHE TEMİZLEME - Sadece user-specific cache'ler (development mode'da tüm sistem cache temizleme gereksiz)
        try {
            // Kullanıcı tercihlerine göre locale ayarla
            if ($user->tenant_locale) {
                app()->setLocale($user->tenant_locale);
            }

            // Sadece guest cache'leri temizle (auth/guest ayrımı için)
            $this->clearGuestCaches();

            \Log::info('🧹 LOGIN: Guest cache temizleme tamamlandı', [
                'user_id' => $user->id,
                'user_locale' => $user->tenant_locale
            ]);
        } catch (\Exception $e) {
            \Log::warning('Login cache clear error: ' . $e->getMessage());
        }

        // Session regenerate işlemi EN SONDA - user preferences kaydedildikten sonra
        $request->session()->regenerate();

        // Dashboard'a giderken SetLocaleMiddleware halledecek, burada ayarlamıyoruz
        \Log::info('🔄 LOGIN: Session locales loaded, middleware will handle locale', [
            'user_id' => $user->id,
            'admin_locale' => $user->admin_locale,
            'tenant_locale' => $user->tenant_locale
        ]);

        // Normal redirect - cache bypass header'ları ile
        $intendedUrl = session()->pull('url.intended', route('dashboard', absolute: false));
        
        return redirect($intendedUrl)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Çıkış log'u ÖNCE - user bilgisi kaybolmadan
        if ($user) {
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'çıkış yaptı';
                })
                ->log("\"{$user->name}\" çıkış yaptı");

            // 🧹 LOGOUT: Sadece user auth cache (hafif & hızlı)
            try {
                $this->clearUserAuthCaches($user->id);
            } catch (\Exception $e) {
                \Log::warning('Logout cache clear error: ' . $e->getMessage());
            }
        }

        // Session'ı invalidate et - cookie'ler otomatik temizlenecek
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Login sayfasına redirect - fresh session garantisi
        // Query parameter ile logout mesajı (session invalidate sonrası with() çalışmaz)
        return redirect('/login?logged_out=1')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->header('Clear-Site-Data', '"cache"'); // Sadece cache temizle, cookies'i korumalı (dark mode vb.)
    }
    
    /**
     * Login cache temizleme - Admin CacheController mantığıyla
     */
    protected function clearLoginCache(): void
    {
        try {
            $tenant = tenant();
            $clearedCaches = [];
            
            // 1. Laravel Cache temizle
            \Illuminate\Support\Facades\Cache::flush();
            $clearedCaches[] = 'Laravel Cache';
            
            // 2. Redis cache'i tenant-aware temizle (admin sistemindeki gibi)
            $redis = \Illuminate\Support\Facades\Redis::connection();
            
            if ($tenant) {
                // Tenant cache'leri temizle
                $pattern = "tenant:{$tenant->id}:*";
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $redis->del($key);
                    }
                    $clearedCaches[] = "Tenant Cache ({$tenant->id})";
                }
                
                // ResponseCache tenant-specific temizle
                $responseCachePattern = "*tenant_{$tenant->id}_*";
                $responseCacheKeys = $redis->keys($responseCachePattern);
                
                if (!empty($responseCacheKeys)) {
                    foreach ($responseCacheKeys as $key) {
                        $redis->del($key);
                    }
                    $clearedCaches[] = "Response Cache (Tenant {$tenant->id})";
                }
            } else {
                // Central domain cache temizle
                $centralKeys = $redis->keys('central:*');
                if (!empty($centralKeys)) {
                    foreach ($centralKeys as $key) {
                        $redis->del($key);
                    }
                    $clearedCaches[] = 'Central Cache';
                }
            }
            
            // 3. System cache'leri temizle (admin sistemindeki gibi)
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            \Artisan::call('config:clear');
            $clearedCaches[] = 'System Caches (view, route, config)';
            
            // 4. ResponseCache global temizle
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
                $clearedCaches[] = 'Response Cache';
            }
            
            \Log::info('🧹 LOGIN: Cache temizleme tamamlandı', [
                'tenant_id' => $tenant ? $tenant->id : 'central',
                'cleared_caches' => $clearedCaches
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Login cache clear error: ' . $e->getMessage());
        }
    }
    
    /**
     * Guest cache'lerini temizle - auth olan kullanıcı guest cache'lerini görmemeli
     */
    protected function clearGuestCaches(): void
    {
        if (!class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            return;
        }

        // Redis'ten guest pattern'li cache'leri bul ve sil
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();

            // TenantCacheProfile pattern: tenant_{tenantId}_guest_*
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->id : 'central';

            // Tenant-aware guest cache pattern
            $guestCachePattern = "*tenant_{$tenantId}_guest_*";
            $guestCacheKeys = $redis->keys($guestCachePattern);

            if (!empty($guestCacheKeys)) {
                // Her guest cache key'ini sil
                foreach ($guestCacheKeys as $key) {
                    $redis->del($key);
                }

                \Log::info('🧹 GUEST CACHE CLEAR', [
                    'tenant_id' => $tenantId,
                    'pattern' => $guestCachePattern,
                    'cleared_keys_count' => count($guestCacheKeys),
                    'sample_keys' => array_slice($guestCacheKeys, 0, 3)
                ]);
            }

        } catch (\Exception $e) {
            \Log::warning('Redis guest cache clear error: ' . $e->getMessage());

            // Fallback: Tüm ResponseCache'i temizle
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
            \Log::info('🧹 FALLBACK: Tüm ResponseCache temizlendi');
        }
    }
    
    /**
     * Belirli kullanıcının auth cache'lerini temizle
     */
    protected function clearUserAuthCaches(int $userId): void
    {
        if (!class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            return;
        }

        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();

            // TenantCacheProfile pattern: tenant_{tenantId}_auth_{userId}_*
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->id : 'central';

            // Tenant-aware auth cache pattern
            $authCachePattern = "*tenant_{$tenantId}_auth_{$userId}_*";
            $authCacheKeys = $redis->keys($authCachePattern);

            if (!empty($authCacheKeys)) {
                // Her auth cache key'ini sil
                foreach ($authCacheKeys as $key) {
                    $redis->del($key);
                }

                \Log::info('🧹 AUTH CACHE CLEAR', [
                    'user_id' => $userId,
                    'tenant_id' => $tenantId,
                    'pattern' => $authCachePattern,
                    'cleared_keys_count' => count($authCacheKeys),
                    'sample_keys' => array_slice($authCacheKeys, 0, 3)
                ]);
            }

        } catch (\Exception $e) {
            \Log::warning("Redis auth cache clear error for user {$userId}: " . $e->getMessage());
        }
    }
    
    /**
     * Tenant-specific ResponseCache temizleme - İzolasyon için kritik!
     */
    protected function clearTenantResponseCache(): void
    {
        if (!class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            return;
        }
        
        try {
            $tenant = tenant();
            
            if ($tenant) {
                // Sadece bu tenant'ın response cache tag'ini temizle
                $tenantTag = 'tenant_' . $tenant->id . '_response_cache';
                \Spatie\ResponseCache\Facades\ResponseCache::forget($tenantTag);
                
                \Log::info('🧹 TENANT RESPONSE CACHE CLEAR', [
                    'tenant_id' => $tenant->id,
                    'cache_tag' => $tenantTag
                ]);
            } else {
                // Central domain için central tag'i temizle
                $centralTag = 'central_response_cache';
                \Spatie\ResponseCache\Facades\ResponseCache::forget($centralTag);
                
                \Log::info('🧹 CENTRAL RESPONSE CACHE CLEAR', [
                    'cache_tag' => $centralTag
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::warning('Tenant response cache clear error: ' . $e->getMessage());
            
            // Fallback: Tüm ResponseCache temizle
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
            \Log::info('🧹 FALLBACK: Tüm ResponseCache temizlendi');
        }
    }
}