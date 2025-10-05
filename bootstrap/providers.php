<?php

return [
    App\Providers\AIServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\DatabasePoolServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\LivewireServiceProvider::class,
    App\Providers\ModulePermissionServiceProvider::class,
    App\Providers\RedisClusterServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\SettingsServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\TenancyProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\TokenServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Providers\WidgetServiceProvider::class,
    Nwidart\Modules\LaravelModulesServiceProvider::class,
    Spatie\MissingPageRedirector\MissingPageRedirectorServiceProvider::class,
    Spatie\ResponseCache\ResponseCacheServiceProvider::class,
];
