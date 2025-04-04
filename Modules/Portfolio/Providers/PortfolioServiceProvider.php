<?php
namespace Modules\Portfolio\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Portfolio\App\Http\Livewire\PortfolioComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioManageComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryComponent;
use Modules\Portfolio\App\Http\Livewire\PortfolioCategoryManageComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PortfolioServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Portfolio';

    protected string $nameLower = 'portfolio';

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

        $this->loadRoutesFrom(module_path('Portfolio', 'routes/web.php'));
        $this->loadViewsFrom(module_path('Portfolio', 'resources/views'), 'portfolio');
        $this->loadMigrationsFrom(module_path('Portfolio', 'database/migrations'));

        Livewire::component('portfolio-component', PortfolioComponent::class);
        Livewire::component('portfolio-manage-component', PortfolioManageComponent::class);
        Livewire::component('portfolio-category-component', PortfolioCategoryComponent::class);
        Livewire::component('portfolio-category-manage-component', PortfolioCategoryManageComponent::class);
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
        $viewPath   = resource_path('views/modules/portfolio');
        $sourcePath = module_path('Portfolio', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'portfolio-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'portfolio');
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
            if (is_dir($path . '/modules/portfolio')) {
                $paths[] = $path . '/modules/portfolio';
            }
        }

        return $paths;
    }
}