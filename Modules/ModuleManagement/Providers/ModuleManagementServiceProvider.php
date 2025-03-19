<?php
namespace Modules\ModuleManagement\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\ModuleManagement\App\Http\Livewire\ModuleComponent;
use Modules\ModuleManagement\App\Http\Livewire\ModuleManageComponent;
use Modules\ModuleManagement\App\Http\Livewire\Modals\DeleteModal;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'ModuleManagement';

    protected string $nameLower = 'modulemanagement';

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

        $this->loadRoutesFrom(module_path('ModuleManagement', 'routes/web.php'));
        $this->loadViewsFrom(module_path('ModuleManagement', 'resources/views'), 'modulemanagement');
        $this->loadMigrationsFrom(module_path('ModuleManagement', 'database/migrations'));

        // Livewire bileşenlerini kaydet
        Livewire::component('module-component', ModuleComponent::class);
        Livewire::component('module-manage-component', ModuleManageComponent::class);
        Livewire::component('modals.delete-modal', DeleteModal::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
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
        $viewPath   = resource_path('views/modules/modulemanagement');
        $sourcePath = module_path('ModuleManagement', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'modulemanagement-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'modulemanagement');
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
            if (is_dir($path . '/modules/modulemanagement')) {
                $paths[] = $path . '/modules/modulemanagement';
            }
        }

        return $paths;
    }
}