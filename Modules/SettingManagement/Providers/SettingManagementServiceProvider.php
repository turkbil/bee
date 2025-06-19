<?php

namespace Modules\SettingManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\SettingManagement\App\Http\Livewire\GroupListComponent;
use Modules\SettingManagement\App\Http\Livewire\ItemListComponent;
use Modules\SettingManagement\App\Http\Livewire\ManageComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantValueComponent;
use Modules\SettingManagement\App\Http\Livewire\GroupManageComponent;
use Modules\SettingManagement\App\Http\Livewire\ValuesComponent;
use Modules\SettingManagement\App\Http\Livewire\TenantSettingsComponent;
use Modules\SettingManagement\App\Http\Livewire\FormBuilderComponent;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class SettingManagementServiceProvider extends ServiceProvider
{
    use \Nwidart\Modules\Traits\PathNamespace;
    
    protected string $name = 'SettingManagement';
    
    protected string $nameLower = 'settingmanagement';

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
        
        // Helpers klasörü varsa otomatik yükle
        $helpersPath = module_path($this->name, 'App/Helpers');
        if (is_dir($helpersPath)) {
            $files = glob($helpersPath . '/*.php');
            foreach ($files as $file) {
                require_once $file;
            }
        }
        
        // Central veritabanında çalışıyorsak tüm migration'ları yükle
        if (!app()->bound('tenancy.tenant')) {
            // Central migrations - settings_groups ve settings tabloları için
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations')
            ]);
        } else {
            // Tenant veritabanında SADECE tenant_values tablosunu yükle
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations/tenant')
            ]);
        }

        $this->loadRoutesFrom(module_path($this->name, 'routes/web.php'));
        $this->loadRoutesFrom(module_path($this->name, 'routes/admin.php'));
        $this->loadViewsFrom(module_path($this->name, 'resources/views'), $this->nameLower);
        
        // Livewire bileşenlerini kaydedelim
        Livewire::component('group-list-component', GroupListComponent::class);
        Livewire::component('item-list-component', ItemListComponent::class);
        Livewire::component('manage-component', ManageComponent::class);
        Livewire::component('tenant-value-component', TenantValueComponent::class);
        Livewire::component('group-manage-component', GroupManageComponent::class);
        Livewire::component('values-component', ValuesComponent::class);
        Livewire::component('tenant-settings-component', TenantSettingsComponent::class);
        Livewire::component('form-builder-component', FormBuilderComponent::class);
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
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath         = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey    = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key          = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

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
}