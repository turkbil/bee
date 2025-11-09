<?php

namespace Modules\Muzibu\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MuzibuServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Muzibu';

    protected string $nameLower = 'muzibu';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Muzibu Observer kaydı
        \Modules\Muzibu\App\Models\Muzibu::observe(\Modules\Muzibu\App\Observers\MuzibuObserver::class);
        \Modules\Muzibu\App\Models\MuzibuCategory::observe(\Modules\Muzibu\App\Observers\MuzibuCategoryObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Muzibu', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Muzibu', 'routes/admin.php'));

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Muzibu', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'muzibu-themes');
        }
        $this->loadViewsFrom(module_path('Muzibu', 'resources/views'), 'muzibu');

        // TODO: Livewire component'ler Artist, Song, Album, Genre, Playlist, Radio için ayrı ayrı oluşturulacak
        // Livewire::component('muzibu-artist-component', ArtistComponent::class);
        // Livewire::component('muzibu-song-component', SongComponent::class);
        // Livewire::component('muzibu-album-component', AlbumComponent::class);
        // vb.
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Repository Pattern bindings
        $this->app->singleton(\Modules\Muzibu\App\Repositories\ArtistRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\GenreRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\AlbumRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\SongRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\SectorRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\PlaylistRepository::class);
        $this->app->singleton(\Modules\Muzibu\App\Repositories\RadioRepository::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Muzibu\App\Console\WarmMuzibuCacheCommand::class,
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
        $viewPath = resource_path('views/modules/muzibu');
        $sourcePath = module_path('Muzibu', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'muzibu-module-views']);

        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Muzibu', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/muzibu');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'muzibu-module-theme-views']);
        }

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'muzibu');
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
            if (is_dir($path . '/modules/muzibu')) {
                $paths[] = $path . '/modules/muzibu';
            }
        }

        return $paths;
    }
}
