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
        // NOT: InitializeTenancy middleware bootstrap/app.php'de tanımlı - burada duplikasyon giderildi

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // NOT: ModuleRouteService her request'te çalışmamalı!
        // Bu sadece application boot'ta 1 kez çalışacak şekilde taşındı

        $this->routes(function () {
            // Admin route'larını ÖNCELİKLİ yükle
            if (file_exists(base_path('routes/admin/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/admin/web.php'));
            }

            // Landing Pages (Google Ads Campaigns)
            if (file_exists(base_path('routes/landing.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/landing.php'));
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