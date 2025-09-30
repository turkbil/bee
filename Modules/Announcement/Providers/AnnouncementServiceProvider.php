<?php
namespace Modules\Announcement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Announcement\App\Http\Livewire\Admin\AnnouncementComponent;
use Modules\Announcement\App\Http\Livewire\Admin\AnnouncementManageComponent;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use Modules\Announcement\App\Repositories\AnnouncementRepository;
use Modules\Announcement\App\Services\AnnouncementService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AnnouncementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Announcement';

    protected string $nameLower = 'announcement';

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
        $this->loadRoutesFrom(module_path('Announcement', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Announcement', 'routes/admin.php'));
        
        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Announcement', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'announcement-themes');
        }
        $this->loadViewsFrom(module_path('Announcement', 'resources/views'), 'announcement');

        Livewire::component('announcement-component', AnnouncementComponent::class);
        Livewire::component('announcement-manage-component', AnnouncementManageComponent::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // Repository Pattern Bindings
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        
        // Service Layer Bindings
        $this->app->singleton(AnnouncementService::class, function ($app) {
            return new AnnouncementService(
                $app->make(AnnouncementRepositoryInterface::class),
                $app->make(\App\Contracts\GlobalSeoRepositoryInterface::class)
            );
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
        $viewPath = resource_path('views/modules/announcement');
        $sourcePath = module_path('Announcement', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'announcement-module-views']);
        
        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Announcement', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/announcement');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'announcement-module-theme-views']);
        }
    
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'announcement');
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
            if (is_dir($path . '/modules/announcement')) {
                $paths[] = $path . '/modules/announcement';
            }
        }

        return $paths;
    }
}