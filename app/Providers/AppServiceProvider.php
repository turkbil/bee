<?php  
namespace App\Providers;  

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Services\ModuleTenantPermissionService;
use App\Services\SettingsService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleTenantPermissionService::class, function ($app) {
            return new ModuleTenantPermissionService();
        });
        
        $this->app->singleton('settings', function ($app) {
            return new SettingsService();
        });
        
        $this->loadHelperFiles();
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
    
    protected function loadHelperFiles(): void
    {
        $helpersPath = app_path('Helpers');
        if (is_dir($helpersPath)) {
            $files = glob($helpersPath . '/*.php');
            foreach ($files as $file) {
                require_once $file;
            }
        }
    }
}