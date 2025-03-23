<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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
            // Redis önbelleğini tenant'a göre yapılandır
            $prefix = 'tenant_' . $event->tenant->id . ':';
            Redis::prefix($prefix);
            
            // Cache prefix'ini ayarla
            Cache::setPrefix($prefix);
            
            // Tenant bilgisini session'a kaydet
            session(['current_tenant' => $event->tenant]);
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