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

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        // Eğer kullanıcı zaten giriş yapmışsa profile'a yönlendir
        if (Auth::check()) {
            return redirect()->route('profile.edit')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
        
        return view('auth.login');
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
        
        // User language preference'larını session'a yükle - REGENERATE'DEN ÖNCE!
        if ($user) {
            // Admin context preference'ını session'a kaydet
            if ($user->admin_language_preference) {
                session(['admin_locale' => $user->admin_language_preference]);
                \Log::info('🔄 LOGIN: Admin language preference loaded', [
                    'user_id' => $user->id,
                    'admin_preference' => $user->admin_language_preference
                ]);
            }
            
            // Site context preference yükle (domain-specific)
            if ($user->site_language_preference) {
                $domain = request()->getHost();
                $sessionKey = 'site_locale_' . str_replace('.', '_', $domain);
                session([$sessionKey => $user->site_language_preference]);
                session(['site_locale' => $user->site_language_preference]); // Legacy key
                \Log::info('🔄 LOGIN: Site language preference loaded', [
                    'user_id' => $user->id,
                    'site_preference' => $user->site_language_preference,
                    'domain_session_key' => $sessionKey
                ]);
            }
        }

        // 🧹 LOGIN CACHE TEMİZLEME - Auth/Guest cache karışıklığını önlemek için TÜM response cache'i temizle
        try {
            // Agresif cache temizleme - auth/guest cache çakışmasını önler
            if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                \Spatie\ResponseCache\Facades\ResponseCache::clear();
                \Log::info('🧹 LOGIN: Tüm ResponseCache temizlendi (auth/guest cache karışıklığı önlendi)', ['user_id' => $user->id]);
            }
            
            // Ek olarak guest cache'leri de temizle
            $this->clearGuestCaches();
            \Log::info('🧹 LOGIN: Guest cache\'leri de temizlendi', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            \Log::warning('Login cache clear error: ' . $e->getMessage());
        }

        // Session regenerate işlemi EN SONDA - user preferences kaydedildikten sonra
        $request->session()->regenerate();

        // Dashboard'a giderken SetLocaleMiddleware halledecek, burada ayarlamıyoruz
        \Log::info('🔄 LOGIN: Session preferences loaded, middleware will handle locale', [
            'user_id' => $user->id,
            'admin_preference' => $user->admin_language_preference,
            'site_preference' => $user->site_language_preference
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
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        // Çıkış log'u
        if ($user) {
            activity()
                ->causedBy($user)
                ->inLog('User')
                ->withProperties(['baslik' => $user->name, 'modul' => 'User'])
                ->tap(function ($activity) {
                    $activity->event = 'çıkış yaptı';
                })
                ->log("\"{$user->name}\" çıkış yaptı");
                
            // 🧹 AUTH CACHE TEMİZLEME - Logout sonrası auth cache'leri gitsin
            try {
                $this->clearUserAuthCaches($user->id);
                \Log::info('🧹 LOGOUT: Auth cache\'leri temizlendi', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                \Log::warning('Auth cache clear error: ' . $e->getMessage());
            }
        }

        return redirect('/');
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
        
        // ResponseCache tag'li olarak temizleme yaparız
        // Redis'ten guest pattern'li cache'leri bul ve sil
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            
            // Guest cache pattern'leri: *_guest_*
            $guestCacheKeys = $redis->keys('*_guest_*');
            
            if (!empty($guestCacheKeys)) {
                // Her guest cache key'ini sil
                foreach ($guestCacheKeys as $key) {
                    $redis->del($key);
                }
                
                \Log::info('🧹 GUEST CACHE CLEAR', [
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
            
            // Auth cache pattern'leri: *_auth_{user_id}_*
            $authCacheKeys = $redis->keys("*_auth_{$userId}_*");
            
            if (!empty($authCacheKeys)) {
                // Her auth cache key'ini sil
                foreach ($authCacheKeys as $key) {
                    $redis->del($key);
                }
                
                \Log::info('🧹 AUTH CACHE CLEAR', [
                    'user_id' => $userId,
                    'cleared_keys_count' => count($authCacheKeys),
                    'sample_keys' => array_slice($authCacheKeys, 0, 3)
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::warning("Redis auth cache clear error for user {$userId}: " . $e->getMessage());
        }
    }
}