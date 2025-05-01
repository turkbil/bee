<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancy;
use App\Services\ModuleAccessService;

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
                            // Middleware'i isim ile kullanıyoruz, closure yerine
                            Route::middleware(['web', 'auth', 'root.access', 'tenant'])
                                ->group($webRoute);
                        } else {
                            // Diğer modüller için izin kontrolü yap
                            Route::middleware(['web', 'auth', 'tenant', "module.permission:{$moduleName},view"])
                                ->group($webRoute);
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
            if (file_exists(base_path('routes/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            }
        
            // API route'larını yükle - dosyanın varlığını kontrol ediyoruz
            if (file_exists(base_path('routes/api.php'))) {
                Route::prefix('api')
                    ->middleware(['api', 'tenant'])
                    ->group(base_path('routes/api.php'));
            }
        });
    }
}