<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    // By default, no namespace is used to support the callable array syntax.
    public static string $controllerNamespace = '';

    public function events()
    {
        return [
            // Tenant events
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                JobPipeline::make([
                    Jobs\CreateDatabase::class, // ✅ DB oluştur
                    Jobs\MigrateDatabase::class, // ✅ Migration çalıştır
                    Jobs\SeedDatabase::class, // ✅ Otomatik seed: roles, users, sample data

                    // Your own jobs to prepare the tenant.
                    // Provision API keys, create S3 buckets, anything you want!

                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(false), // `false` by default, but you probably want to make this `true` for production.
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [
                JobPipeline::make([
                    \App\Jobs\SafeDeleteDatabase::class, // ✅ Safe delete - DB yoksa hata vermez
                    \App\Jobs\UnregisterDatabaseFromPlesk::class, // ✅ Otomatik Plesk silme aktif
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false), // `false` by default, but you probably want to make this `true` for production.
            ],

            // Domain events
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [
                \App\Listeners\CreateTenantDomains::class, // ✅ Otomatik www.domain ekleme
                \App\Listeners\RegisterDomainAliasInPlesk::class, // ✅ Otomatik Plesk domain alias
            ],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [
                function (Events\DeletingDomain $event) {
                    // Domain silinmeden önce Plesk alias'ı sil (senkron çalıştır)
                    \App\Jobs\UnregisterDomainAliasFromPlesk::dispatchSync(
                        $event->domain->domain,
                        $event->domain->tenant_id
                    );
                },
            ],
            Events\DomainDeleted::class => [
                // Domain alias silme işlemi DeletingDomain'de yapılır (domain silinmeden önce)
            ],

            // Database events
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [
                \App\Listeners\RegisterTenantDatabaseToPlesk::class, // ✅ Otomatik Plesk kayıt aktif
            ],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
                function (Events\TenancyInitialized $event) {
                    // 🔄 Tenant context'e geçince log yapılandırması
                    $tenantId = tenant('id');
                    if (!$tenantId) {
                        return;
                    }

                    // storage_path() zaten tenant storage'ı dönüyor (storage/tenant23)
                    $logPath = storage_path('logs/tenant.log');

                    // Logs klasörü yoksa oluştur
                    $logDir = dirname($logPath);
                    if (!file_exists($logDir)) {
                        mkdir($logDir, 0777, true);
                        chmod($logDir, 0777);
                    }

                    // Tenant log path'ini güncelle
                    config([
                        'logging.default' => 'tenant',
                        'logging.channels.tenant.path' => $logPath,
                    ]);

                    // Log manager'ı yeniden yükle
                    app()->forgetInstance('log');

                    // 🔐 Session lifetime: Tenant setting'den al
                    $sessionLifetime = (int) setting('auth_session_lifetime', 120);
                    if ($sessionLifetime > 0) {
                        config(['session.lifetime' => $sessionLifetime]);
                    }
                },
            ],

            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
                function (Events\TenancyEnded $event) {
                    // 🔄 Central context'e dönünce default log channel'ı eski haline al
                    config(['logging.default' => env('LOG_CHANNEL', 'stack')]);
                },
            ],

            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],

            // Fired only when a synced resource is changed in a different DB than the origin DB (to avoid infinite loops)
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->bootEvents();
        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes()
    {
        $this->app->booted(function () {
            if (file_exists(base_path('routes/tenant.php'))) {
                Route::namespace(static::$controllerNamespace)
                    ->group(base_path('routes/tenant.php'));
            }
        });
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,
    
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];
    
        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}
