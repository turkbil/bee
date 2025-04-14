<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return new class extends ServiceProvider
{
    public function register(): void
    {
        // Middleware'leri kaydet
        $this->app['router']->aliasMiddleware('initialize-tenancy', \App\Http\Middleware\InitializeTenancy::class);
        
        // Middleware gruplarını güncelle
        $this->app['router']->middlewareGroup('web', array_merge(
            $this->app['router']->getMiddlewareGroups()['web'],
            ['initialize-tenancy']
        ));
    }
};
