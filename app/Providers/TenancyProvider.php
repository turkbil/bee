<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis as RedisFacade;

class TenancyProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tenant başlatıldığında çalışacak event
        Event::listen(TenancyInitialized::class, function ($event) {
            // Tenant bazlı Redis ve cache prefix ayarı
            $prefix = 'tenant_' . $event->tenancy->tenant->id . ':';
            Config::set('database.redis.options.prefix', $prefix);
            RedisFacade::purge();
            
            // Cache prefix ayarını config üzerinden yap
            Config::set('cache.prefix', $prefix);
            
            // TENANT-BAZLI RESPONSE CACHE TAG AYARI - İZOLASYON CRİTİK!
            Config::set('responsecache.cache_tag', 'tenant_' . $event->tenancy->tenant->id . '_response_cache');
            
            // Tenant bilgisini session'a kaydet
            session(['current_tenant' => $event->tenancy->tenant]);
        });

        // Yeni tenant oluşturulduğunda çalışacak event
        Event::listen(TenantCreated::class, function ($event) {
            // Domain önbelleğini temizle
            foreach ($event->tenant->domains as $domain) {
                Cache::forget('domain_tenant_' . $domain->domain);
            }
            
            // ModuleService önbelleğini temizle
            Cache::forget('modules_tenant_' . $event->tenant->id);
        });
    }
}