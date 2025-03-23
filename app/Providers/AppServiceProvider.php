<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tenant için Redis önbellek yapılandırması
        if (TenantHelpers::isTenant()) {
            $tenantId = tenant_id();
            
            // ResponseCache için tenant bazlı tag ayarlaması
            config([
                'responsecache.cache_tag' => 'tenant_' . $tenantId . '_response_cache',
                'responsecache.cache_lifetime_in_seconds' => 86400, // 24 saat
            ]);
        }
    }
}