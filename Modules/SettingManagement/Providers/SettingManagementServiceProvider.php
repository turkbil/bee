<?php

namespace Modules\SettingManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\SettingManagement\App\Http\Livewire\Settings\GroupList;
use Modules\SettingManagement\App\Http\Livewire\Settings\ItemList;
use Modules\SettingManagement\App\Http\Livewire\Settings\Manage;
use Modules\SettingManagement\App\Http\Livewire\Settings\TenantValue;
use Modules\SettingManagement\App\Http\Livewire\Settings\GroupManage;
use Modules\SettingManagement\App\Http\Livewire\Settings\Values;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class SettingManagementServiceProvider extends ServiceProvider
{
    use \Nwidart\Modules\Traits\PathNamespace;
    
    protected string $moduleName = 'SettingManagement';
    
    protected string $moduleNameLower = 'settingmanagement';

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
        
        // Central migrations
        $this->loadMigrationsFrom([
            module_path($this->moduleName, 'database/migrations')
        ]);
        
        // Tenant migrations - SADECE tenant_values tablosu
        if (app()->has('tenancy.migrator')) {
            app('tenancy.migrator')
                ->path(module_path($this->moduleName, 'database/migrations/tenant'));
        }

        $this->loadRoutesFrom(module_path($this->moduleName, 'routes/web.php'));
        $this->loadViewsFrom(module_path($this->moduleName, 'resources/views'), $this->moduleNameLower);
        $this->registerLivewireComponents();
    }

    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
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
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath         = module_path($this->moduleName, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey    = $this->moduleNameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key          = ($relativePath === 'config.php') ? $this->moduleNameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], $configPath);
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    public function registerViews(): void
    {
        $viewPath   = resource_path('views/modules/settingmanagement');
        $sourcePath = module_path('SettingManagement', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'settingmanagement-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'settingmanagement');
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
            if (is_dir($path . '/modules/settingmanagement')) {
                $paths[] = $path . '/modules/settingmanagement';
            }
        }

        return $paths;
    }

    /**
     * Register Livewire components
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('settingmanagement::settings.group-list', GroupList::class);
        Livewire::component('settingmanagement::settings.item-list', ItemList::class);
        Livewire::component('settingmanagement::settings.manage', Manage::class);
        Livewire::component('settingmanagement::settings.tenant-value', TenantValue::class);
        Livewire::component('settingmanagement::settings.group-manage', GroupManage::class);
        Livewire::component('settingmanagement::settings.values', Values::class);
    }
}