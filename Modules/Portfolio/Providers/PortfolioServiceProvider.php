<?php
namespace Modules\Portfolio\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Portfolio\App\Http\Livewire\Admin\PortfolioComponent;
use Modules\Portfolio\App\Http\Livewire\Admin\PortfolioManageComponent;
use Modules\Portfolio\App\Http\Livewire\Admin\PortfolioCategoryComponent;
use Modules\Portfolio\App\Http\Livewire\Admin\PortfolioCategoryManageComponent;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use Modules\Portfolio\App\Repositories\PortfolioRepository;
use Modules\Portfolio\App\Services\PortfolioService;
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

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Portfolio', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Portfolio', 'routes/admin.php'));
        
        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        $this->loadViewsFrom(module_path('Portfolio', 'resources/views/front/themes'), 'portfolio-themes');
        $this->loadViewsFrom(module_path('Portfolio', 'resources/views'), 'portfolio');

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
        
        // Repository Pattern Bindings
        $this->app->bind(PortfolioRepositoryInterface::class, PortfolioRepository::class);
        
        // Service Layer Bindings
        $this->app->singleton(PortfolioService::class, function ($app) {
            return new PortfolioService($app->make(PortfolioRepositoryInterface::class));
        });
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
        $viewPath = resource_path('views/modules/portfolio');
        $sourcePath = module_path('Portfolio', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'portfolio-module-views']);
        
        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Portfolio', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/portfolio');
        
        $this->publishes([
            $themeSourcePath => $themeViewPath,
        ], ['views', 'portfolio-module-theme-views']);
    
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