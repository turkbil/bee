<?php

namespace Modules\Service\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Service\App\Http\Livewire\Admin\ServiceComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceManageComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceCategoryComponent;
use Modules\Service\App\Http\Livewire\Admin\ServiceCategoryManageComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ServiceServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Service';

    protected string $nameLower = 'service';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Service Observer kaydı
        \Modules\Service\App\Models\Service::observe(\Modules\Service\App\Observers\ServiceObserver::class);
        \Modules\Service\App\Models\ServiceCategory::observe(\Modules\Service\App\Observers\ServiceCategoryObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Service', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Service', 'routes/admin.php'));

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Service', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'service-themes');
        }
        $this->loadViewsFrom(module_path('Service', 'resources/views'), 'service');

        Livewire::component('service-component', ServiceComponent::class);
        Livewire::component('service-manage-component', ServiceManageComponent::class);
        Livewire::component('service-category-component', ServiceCategoryComponent::class);
        Livewire::component('service-category-manage-component', ServiceCategoryManageComponent::class);
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
            \Modules\Service\App\Contracts\ServiceRepositoryInterface::class,
            \Modules\Service\App\Repositories\ServiceRepository::class
        );

        $this->app->bind(
            \Modules\Service\App\Contracts\ServiceCategoryRepositoryInterface::class,
            \Modules\Service\App\Repositories\ServiceCategoryRepository::class
        );

        $this->app->bind(
            \App\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );

        // Service Layer bindings
        $this->app->singleton(\Modules\Service\App\Services\ServiceService::class);
        $this->app->singleton(\Modules\Service\App\Services\ServiceCategoryService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Service\App\Console\WarmServiceCacheCommand::class,
        ]);
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
        $viewPath = resource_path('views/modules/service');
        $sourcePath = module_path('Service', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'service-module-views']);

        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Service', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/service');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'service-module-theme-views']);
        }

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'service');
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
            if (is_dir($path . '/modules/service')) {
                $paths[] = $path . '/modules/service';
            }
        }

        return $paths;
    }
}
