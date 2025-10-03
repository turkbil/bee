<?php
namespace Modules\Page\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Page\App\Http\Livewire\Admin\PageComponent;
use Modules\Page\App\Http\Livewire\Admin\PageManageComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PageServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Page';

    protected string $nameLower = 'page';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Page Observer kaydı
        \Modules\Page\App\Models\Page::observe(\Modules\Page\App\Observers\PageObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Page', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Page', 'routes/admin.php'));
        
        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Page', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'page-themes');
        }
        $this->loadViewsFrom(module_path('Page', 'resources/views'), 'page');

        Livewire::component('page-component', PageComponent::class);
        Livewire::component('page-manage-component', PageManageComponent::class);
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
            \Modules\Page\App\Contracts\PageRepositoryInterface::class,
            \Modules\Page\App\Repositories\PageRepository::class
        );
        
        $this->app->bind(
            \App\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );
        
        // Service Layer bindings
        $this->app->singleton(\Modules\Page\App\Services\PageService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Page\App\Console\WarmPageCacheCommand::class,
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
        $viewPath = resource_path('views/modules/page');
        $sourcePath = module_path('Page', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'page-module-views']);
        
        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Page', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/page');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'page-module-theme-views']);
        }
    
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'page');
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
            if (is_dir($path . '/modules/page')) {
                $paths[] = $path . '/modules/page';
            }
        }

        return $paths;
    }
}