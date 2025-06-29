<?php  
namespace App\Providers;  

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;
use App\Services\ModuleTenantPermissionService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Contracts binding
        $this->app->bind(
            \App\Contracts\ModuleAccessServiceInterface::class,
            \App\Services\ModuleAccessService::class
        );
        
        $this->app->bind(
            \App\Contracts\ThemeRepositoryInterface::class,
            \App\Repositories\ThemeRepository::class
        );
        
        $this->app->bind(
            \App\Contracts\DynamicRouteResolverInterface::class,
            \App\Services\DynamicRouteResolver::class
        );
        
        $this->app->bind(
            \App\Contracts\ModuleRepositoryInterface::class,
            \App\Repositories\ModuleRepository::class
        );
        
        // Service singletons
        $this->app->singleton(ModuleTenantPermissionService::class, function ($app) {
            return new ModuleTenantPermissionService();
        });
        
        $this->app->singleton('settings', function ($app) {
            return new SettingsService();
        });
        
        // ThemeService singleton - simplified for emergency fix
        $this->app->singleton(\App\Services\ThemeService::class, function ($app) {
            return new \App\Services\ThemeService();
        });
        
        // Module permission helper services
        $this->app->singleton(\App\Services\ModulePermissionChecker::class);
        $this->app->singleton(\App\Services\ModuleAccessCache::class);
        
        // Dynamic route services
        $this->app->singleton(\App\Services\DynamicRouteRegistrar::class);
        
        $this->loadHelperFiles();
    }

    public function boot(): void
    {
        // HTTPS kullanıyorsanız bu ayarı aktif edin
        if(env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        
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