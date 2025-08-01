<?php

namespace Modules\MenuManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuComponent;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuManageComponent;
use Modules\MenuManagement\App\Http\Livewire\Admin\MenuItemManageComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MenuManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'MenuManagement';

    protected string $nameLower = 'menumanagement';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Observer kayıtları
        \Modules\MenuManagement\App\Models\Menu::observe(\Modules\MenuManagement\App\Observers\MenuObserver::class);
        \Modules\MenuManagement\App\Models\MenuItem::observe(\Modules\MenuManagement\App\Observers\MenuItemObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Register routes - admin routes dahil
        $this->app->booted(function () {
            $this->registerRoutes();
        });
        
        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        $this->loadViewsFrom(module_path('MenuManagement', 'resources/views/front/themes'), 'menumanagement-themes');
        $this->loadViewsFrom(module_path('MenuManagement', 'resources/views'), 'menumanagement');

        Livewire::component('menu-component', MenuComponent::class);
        Livewire::component('menu-manage-component', MenuManageComponent::class);
        Livewire::component('menu-item-manage-component', MenuItemManageComponent::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // Repository Pattern bindings
        $this->app->bind(
            \Modules\MenuManagement\App\Contracts\MenuRepositoryInterface::class,
            \Modules\MenuManagement\App\Repositories\MenuRepository::class
        );
        
        $this->app->bind(
            \Modules\MenuManagement\App\Contracts\MenuItemRepositoryInterface::class,
            \Modules\MenuManagement\App\Repositories\MenuItemRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );
        
        // Service Layer bindings
        $this->app->singleton(\Modules\MenuManagement\App\Services\MenuService::class);
        $this->app->singleton(\Modules\MenuManagement\App\Services\MenuUrlBuilderService::class);
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
        $moduleLangPath = module_path($this->name, 'resources/lang');
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
        $viewPath = resource_path('views/modules/menumanagement');
        $sourcePath = module_path('MenuManagement', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'menumanagement-module-views']);
        
        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('MenuManagement', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/menumanagement');
        
        $this->publishes([
            $themeSourcePath => $themeViewPath,
        ], ['views', 'menumanagement-module-theme-views']);
    
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'menumanagement');
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
            if (is_dir($path . '/modules/menumanagement')) {
                $paths[] = $path . '/modules/menumanagement';
            }
        }

        return $paths;
    }

    /**
     * Register module routes
     */
    protected function registerRoutes(): void
    {
        // Web routes - direkt dosya yolu
        $webRoute = __DIR__ . '/../routes/web.php';
        if (file_exists($webRoute)) {
            $this->loadRoutesFrom($webRoute);
        }
        
        // Admin routes - direkt dosya yolu
        $adminRoute = __DIR__ . '/../routes/admin.php';
        if (file_exists($adminRoute)) {
            $this->loadRoutesFrom($adminRoute);
        }
    }
}