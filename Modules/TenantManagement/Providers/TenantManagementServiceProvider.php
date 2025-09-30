<?php

namespace Modules\TenantManagement\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TenantManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'TenantManagement';

    protected string $nameLower = 'tenantmanagement';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Load routes
        $this->loadRoutesFrom(module_path($this->name, 'routes/web.php'));
        $this->loadRoutesFrom(module_path($this->name, 'routes/admin.php'));
        $this->loadRoutesFrom(module_path($this->name, 'routes/api.php'));

        // Livewire component registration - moved to end of boot method
        $this->app->booted(function () {
            $this->registerLivewireComponents();
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Service bindings
        $this->app->singleton(\Modules\TenantManagement\App\Services\RealTimeAutoScalingService::class);
        $this->app->singleton(\App\Services\DatabaseConnectionPoolService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        // Ana dil dosyaları - modül klasöründen yükle
        $moduleLangPath = module_path($this->name, 'lang');
        if (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom($moduleLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($moduleLangPath);
        }

        // Resource'daki dil dosyaları (varsa)
        $resourceLangPath = resource_path('lang/modules/' . $this->nameLower);
        if (is_dir($resourceLangPath)) {
            $this->loadTranslationsFrom($resourceLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($resourceLangPath);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        try {
            // Check if classes exist before registering - dual registration for compatibility
            $components = [
                // Short names for internal usage
                'tenantcomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantComponent::class,
                'tenantcachecomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantCacheComponent::class,
                'tenantlimitscomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantLimitsComponent::class,
                'tenantmonitoringcomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantMonitoringComponent::class,
                'tenantratelimitcomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantRateLimitComponent::class,
                'tenantautoscalingcomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantAutoScalingComponent::class,
                'tenantmodulecomponent' => \Modules\TenantManagement\App\Http\Livewire\TenantModuleComponent::class,
                'tenant-module-component' => \Modules\TenantManagement\App\Http\Livewire\TenantModuleComponent::class,
                'tenantpoolmonitoringcomponent' => \Modules\TenantManagement\App\Http\Livewire\Admin\TenantPoolMonitoringComponent::class,
                'tenanthealthcheckcomponent' => \Modules\TenantManagement\App\Http\Livewire\Admin\TenantHealthCheckComponent::class,
                
                // Full namespace names for auto-discovery
                'modules.tenantmanagement.app.http.livewire.tenant-component' => \Modules\TenantManagement\App\Http\Livewire\TenantComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-module-component' => \Modules\TenantManagement\App\Http\Livewire\TenantModuleComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-cache-component' => \Modules\TenantManagement\App\Http\Livewire\TenantCacheComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-limits-component' => \Modules\TenantManagement\App\Http\Livewire\TenantLimitsComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-monitoring-component' => \Modules\TenantManagement\App\Http\Livewire\TenantMonitoringComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-rate-limit-component' => \Modules\TenantManagement\App\Http\Livewire\TenantRateLimitComponent::class,
                'modules.tenantmanagement.app.http.livewire.tenant-auto-scaling-component' => \Modules\TenantManagement\App\Http\Livewire\TenantAutoScalingComponent::class,
                'modules.tenantmanagement.app.http.livewire.admin.tenant-pool-monitoring-component' => \Modules\TenantManagement\App\Http\Livewire\Admin\TenantPoolMonitoringComponent::class,
                'modules.tenantmanagement.app.http.livewire.admin.tenant-health-check-component' => \Modules\TenantManagement\App\Http\Livewire\Admin\TenantHealthCheckComponent::class,
                
                // Queue Monitoring Component
                'queuemonitoringcomponent' => \Modules\TenantManagement\App\Http\Livewire\QueueMonitoringComponent::class,
                'modules.tenantmanagement.app.http.livewire.queue-monitoring-component' => \Modules\TenantManagement\App\Http\Livewire\QueueMonitoringComponent::class,
            ];
            
            foreach ($components as $name => $class) {
                if (class_exists($class)) {
                    Livewire::component($name, $class);
                    // Component registered successfully
                } else {
                    \Log::error("TenantManagement: Class not found {$class}");
                }
            }
            
            // Livewire components registration completed
        } catch (\Exception $e) {
            \Log::error('TenantManagement: registerLivewireComponents failed: ' . $e->getMessage());
        }
    }
}
