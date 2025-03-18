<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware'lerde InitializeTenancy'yi KALDIR
        
        // Web middleware grubuna InitializeTenancy ekle
        $middleware->web(append: [
            \App\Http\Middleware\InitializeTenancy::class,
        ]);
        
        // Alias tanımlamaları
        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenancy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();