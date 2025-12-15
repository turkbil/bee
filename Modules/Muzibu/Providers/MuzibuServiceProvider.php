<?php

namespace Modules\Muzibu\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Livewire\Livewire;

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
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Routes loaded directly like Cart module
        $this->loadRoutesFrom(module_path('Muzibu', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Muzibu', 'routes/admin.php'));

        // API routes - tenant domain'leri için dinamik yükleme
        $this->loadApiRoutes();

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Muzibu', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'muzibu-themes');
        }
        $this->loadViewsFrom(module_path('Muzibu', 'resources/views'), 'muzibu');

        // Livewire Component Kayıtları
        $this->registerLivewireComponents();

        // View Composers - Sidebar için featured playlists
        $this->registerViewComposers();
    }

    /**
     * Livewire component'lerini kaydet
     */
    protected function registerLivewireComponents(): void
    {
        // Artist Components
        Livewire::component('muzibu::admin.artist-component', \Modules\Muzibu\App\Http\Livewire\Admin\ArtistComponent::class);
        Livewire::component('muzibu::admin.artist-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\ArtistManageComponent::class);

        // Album Components
        Livewire::component('muzibu::admin.album-component', \Modules\Muzibu\App\Http\Livewire\Admin\AlbumComponent::class);
        Livewire::component('muzibu::admin.album-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\AlbumManageComponent::class);
        Livewire::component('muzibu::admin.album-bulk-upload-component', \Modules\Muzibu\App\Http\Livewire\Admin\AlbumBulkUploadComponent::class);

        // Song Components
        Livewire::component('muzibu::admin.song-component', \Modules\Muzibu\App\Http\Livewire\Admin\SongComponent::class);
        Livewire::component('muzibu::admin.song-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\SongManageComponent::class);

        // Genre Components
        Livewire::component('muzibu::admin.genre-component', \Modules\Muzibu\App\Http\Livewire\Admin\GenreComponent::class);
        Livewire::component('muzibu::admin.genre-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\GenreManageComponent::class);

        // Playlist Components
        Livewire::component('muzibu::admin.playlist-component', \Modules\Muzibu\App\Http\Livewire\Admin\PlaylistComponent::class);
        Livewire::component('muzibu::admin.playlist-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\PlaylistManageComponent::class);
        Livewire::component('muzibu::admin.playlist-songs-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\PlaylistSongsManageComponent::class);

        // Radio Components
        Livewire::component('muzibu::admin.radio-component', \Modules\Muzibu\App\Http\Livewire\Admin\RadioComponent::class);
        Livewire::component('muzibu::admin.radio-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\RadioManageComponent::class);

        // Sector Components
        Livewire::component('muzibu::admin.sector-component', \Modules\Muzibu\App\Http\Livewire\Admin\SectorComponent::class);
        Livewire::component('muzibu::admin.sector-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\SectorManageComponent::class);

        // Corporate Account Components
        Livewire::component('muzibu::admin.corporate-account-component', \Modules\Muzibu\App\Http\Livewire\Admin\CorporateAccountComponent::class);

        // Frontend Components
        Livewire::component('muzibu::frontend.search-results', \Modules\Muzibu\App\Http\Livewire\Frontend\SearchResults::class);
    }

    /**
     * Register view composers
     */
    protected function registerViewComposers(): void
    {
        // Sidebar için featured playlists'i tüm sayfalarda sağla
        view()->composer(
            'themes.muzibu.layouts.app',
            \Modules\Muzibu\App\View\Composers\SidebarComposer::class
        );
    }

    /**
     * Load API routes dynamically for all Muzibu tenant domains
     */
    protected function loadApiRoutes(): void
    {
        // Tenant 1001 domain'lerini al (dinamik)
        $domains = \Illuminate\Support\Facades\DB::table('domains')
            ->where('tenant_id', 1001)
            ->pluck('domain')
            ->toArray();

        foreach ($domains as $index => $domain) {
            // 🔑 HLS ENCRYPTION KEY - WITH AUTH! (Şarkı indirme koruması)
            \Illuminate\Support\Facades\Route::middleware([
                'web', // Session/Cookie support
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                'auth', // 🔐 Giriş yapmamış kullanıcılar key alamaz!
                \App\Http\Middleware\FixResponseCacheHeaders::class,
            ])
                ->domain($domain)
                ->get('/api/muzibu/songs/{id}/key', [\Modules\Muzibu\app\Http\Controllers\Api\SongController::class, 'serveEncryptionKey'])
                ->name(($index === 0 ? '' : "d{$index}.") . 'api.muzibu.songs.encryption-key');

            // Main API routes (with session)
            \Illuminate\Support\Facades\Route::middleware(['api', \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class])
                ->domain($domain)
                ->prefix('api')
                ->name($index === 0 ? '' : "d{$index}.")
                ->group(module_path('Muzibu', 'routes/api.php'));
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        // RouteServiceProvider disabled - routes loaded directly like Cart module
        // $this->app->register(RouteServiceProvider::class);

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
