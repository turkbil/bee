<?php
return [
    App\Providers\AppServiceProvider::class,
    \Nwidart\Modules\LaravelModulesServiceProvider::class,
    App\Providers\AIServiceProvider::class,
    App\Providers\TokenServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\ModulePermissionServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\SettingsServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\TenancyProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\WidgetServiceProvider::class,
    Spatie\MissingPageRedirector\MissingPageRedirectorServiceProvider::class,
    Spatie\ResponseCache\ResponseCacheServiceProvider::class,
];