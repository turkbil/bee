<?php  
namespace App\Providers;  

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Services\ModuleTenantPermissionService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleTenantPermissionService::class, function ($app) {
            return new ModuleTenantPermissionService();
        });
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
            
            // Tenant bazlı Redis önbellek ayarları
            config([
                'cache.prefix' => 'tenant_' . $tenantId . '_cache_',
                'cache.stores.redis.prefix' => 'tenant_' . $tenantId . '_cache_',
                'database.redis.options.prefix' => 'tenant_' . $tenantId . '_',
                'session.cookie' => 'tenant_' . $tenantId . '_session',
            ]);
            
            // Tenant için önbellek mağazası ayarla
            Cache::extend('tenant', function ($app) use ($tenantId) {
                $config = $app['config']->get('cache.stores.redis');
                $prefix = 'tenant_' . $tenantId . '_cache:';
                
                return Cache::repository(new \Illuminate\Cache\RedisStore(
                    $app['redis'],
                    $prefix,
                    $config['connection'] ?? 'default'
                ));
            });
        }
    }
}