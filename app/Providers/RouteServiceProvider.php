<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Global olarak tüm route'lara tenant middleware'ini ekle
        Route::middlewareGroup('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            InitializeTenancy::class, // Tenant middleware'ini web grubuna ekledik
        ]);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Modül route'larını yükle ve otomatik middleware ekle
            if (is_dir(base_path('Modules'))) {
                $modules = array_diff(scandir(base_path('Modules')), ['.', '..']);
                foreach ($modules as $module) {
                    $moduleName = strtolower($module);
                    $webRoute = base_path("Modules/{$module}/routes/web.php");
                    
                    if (file_exists($webRoute)) {
                        // TenantManagement modülü sadece root için
                        if ($moduleName === 'tenantmanagement') {
                            Route::middleware(['web', 'auth', function ($request, $next) {
                                if (!auth()->user() || !auth()->user()->isRoot()) {
                                    abort(403, 'Bu alana sadece Root kullanıcılar erişebilir.');
                                }
                                return $next($request);
                            }, 'tenant'])->group($webRoute);
                        } else {
                            // Diğer modüller için tenant.module middleware'ini ekle
                            Route::middleware(['web', 'auth', 'tenant', function ($request, $next) use ($moduleName) {
                                // URL'i kontrol et
                                $uri = $request->path();
                                
                                // Eğer admin/modül ile başlıyorsa izin kontrolü yap
                                if (strpos($uri, "admin/{$moduleName}") === 0) {
                                    $user = auth()->user();
                                    
                                    // Root her şeye erişebilir
                                    if ($user && $user->isRoot()) {
                                        return $next($request);
                                    }
                                    
                                    // Admin her modüle erişebilir (tenantmanagement hariç)
                                    if ($user && $user->isAdmin() && $moduleName !== 'tenantmanagement') {
                                        return $next($request);
                                    }
                                    
                                    // Editor sadece yetkisi olan modüllere erişebilir
                                    if ($user && $user->isEditor() && app(\App\Services\ModuleAccessService::class)->canAccess($moduleName, 'view')) {
                                        return $next($request);
                                    }
                                    
                                    abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
                                }
                                
                                return $next($request);
                            }])->group($webRoute);
                        }
                    }
                }
            }

            // Admin route'larını yükle
            if (file_exists(base_path('routes/admin/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/admin/web.php'));
            }

            // Web route'larını yükle
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // API route'larını yükle
            Route::prefix('api')
                ->middleware(['api', InitializeTenancy::class])
                ->group(base_path('routes/api.php'));
        });
    }
}