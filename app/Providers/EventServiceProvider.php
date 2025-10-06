<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ModuleEnabled;
use App\Events\ModuleDisabled;
use App\Listeners\LoadModuleRoutes;
use App\Listeners\ClearModuleRouteCache;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Module management events
        ModuleEnabled::class => [
            LoadModuleRoutes::class,
            ClearModuleRouteCache::class . '@handleModuleEnabled',
        ],
        
        ModuleDisabled::class => [
            ClearModuleRouteCache::class . '@handleModuleDisabled',
        ],
        
        // Module tenant permission events
        \App\Events\ModuleAddedToTenant::class => [
            \App\Listeners\HandleModuleTenantPermissions::class . '@handleModuleAdded',
        ],
        
        \App\Events\ModuleRemovedFromTenant::class => [
            \App\Listeners\HandleModuleTenantPermissions::class . '@handleModuleRemoved',
        ],

        // Tenant database events
        \Stancl\Tenancy\Events\DatabaseMigrated::class => [
            \App\Listeners\RegisterTenantDatabaseToPlesk::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}