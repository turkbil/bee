<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TokenService;

class TokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TokenService::class, function ($app) {
            return TokenService::getInstance();
        });
    }

    public function boot()
    {
        //
    }
}