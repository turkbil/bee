<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ModuleAccessService;
use App\Http\Middleware\TenantModuleMiddleware;
use Modules\ModuleManagement\App\Models\Module;

class ModulePermissionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ModuleAccessService singleton olarak kaydet
        $this->app->singleton(ModuleAccessService::class, function ($app) {
            return new ModuleAccessService();
        });
        
        // Konfigürasyon dosyasını yayınla
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/module-permissions.php', 'module-permissions'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigürasyon dosyasını publish edilebilir yap
        $this->publishes([
            __DIR__ . '/../../config/module-permissions.php' => config_path('module-permissions.php'),
        ], 'config');
        
        // Middleware'i kaydet
        $this->app['router']->aliasMiddleware('tenant.module', TenantModuleMiddleware::class);
        
        // Admin middleware grubu - hem tenant hem auth kontrolü
        $this->app['router']->middlewareGroup('admin', [
            'web',
            'auth',
            'tenant',
        ]);
        
        // Dinamik olarak modül middleware gruplarını oluştur
        try {
            $modules = Module::where('is_active', true)->get();
            
            foreach ($modules as $module) {
                $this->app['router']->middlewareGroup("module.{$module->name}", [
                    'web', 
                    'auth', 
                    'tenant',
                    "tenant.module:{$module->name},view"
                ]);
            }
        } catch (\Exception $e) {
            // Veritabanı henüz hazır değilse hata oluşabilir (migration sırasında vb.)
            // Bu durumda sessizce devam et
            \Illuminate\Support\Facades\Log::warning("Modül middleware grupları oluşturulurken hata: " . $e->getMessage());
        }
        
        // Blade direktiflerini ekle
        \Blade::if('hasmoduleaccess', function ($moduleName, $permissionType = 'view') {
            return app(ModuleAccessService::class)->canAccess($moduleName, $permissionType);
        });
        
        \Blade::directive('moduleaccess', function ($expression) {
            return "<?php if(app(\App\Services\ModuleAccessService::class)->canAccess({$expression})): ?>";
        });
        
        \Blade::directive('endmoduleaccess', function () {
            return "<?php endif; ?>";
        });
    }
}