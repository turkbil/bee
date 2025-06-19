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
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            InitializeTenancy::class, // Tenant middleware'ini web grubuna ekledik
        ]);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Admin route'larını ÖNCELİKLİ yükle
            if (file_exists(base_path('routes/admin/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/admin/web.php'));
            }
            
            // NOT: Modül rotaları kendi ServiceProvider'ları tarafından yükleniyor
            // Burada tekrar yüklemeye gerek yok, çift yükleme sorununa neden oluyor
        
            // Web route'larını SON olarak yükle (dynamic route'lar burada)
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