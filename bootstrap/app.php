<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin rotalarını ekliyoruz
            Route::prefix('admin')
                ->middleware('web') // Web middleware kullanıyoruz
                ->group(base_path('routes/admin/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(InitializeTenancyByDomain::class);

        $middleware->append(App\Http\Middleware\AppendSiteIdToSession::class);

        // Ek middleware'ler eklenebilir
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling ayarları
    })
    ->create();
