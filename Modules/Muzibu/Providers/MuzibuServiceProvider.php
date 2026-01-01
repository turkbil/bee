<?php

namespace Modules\Muzibu\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        // Load helpers
        $helpersPath = module_path($this->name, 'app/Helpers/helpers.php');
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }

        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Web routes - loaded in routes/web.php with domain filter
        // $this->loadWebRoutes(); // Disabled - handled in main routes/web.php

        // Admin routes - global
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

        // Blade Component Namespace Kayıtları (x-muzibu.*)
        $this->registerBladeComponents();

        // View Composers - Sidebar için featured playlists
        $this->registerViewComposers();

        // ✅ MODEL OBSERVERS: UTF-8 Temizleme (30 bin şarkı için production-ready!)
        $this->registerModelObservers();

        // ✅ MORPH MAP: Polymorphic ilişkiler için kısa alias'lar
        // playlistables tablosu bu alias'ları kullanır
        $this->registerMorphMap();
    }

    /**
     * Model Observer'larını kaydet
     * - MuzikDataObserver: UTF-8 temizleme
     * - SongObserver: Cache count güncellemeleri (Album, Genre, Artist)
     * - AlbumObserver: Cache, SEO, Artist albums_count
     */
    protected function registerModelObservers(): void
    {
        // UTF-8 Temizleme Observer
        $dataObserver = \Modules\Muzibu\App\Observers\MuzikDataObserver::class;
        \Modules\Muzibu\App\Models\Song::observe($dataObserver);
        \Modules\Muzibu\App\Models\Album::observe($dataObserver);
        \Modules\Muzibu\App\Models\Artist::observe($dataObserver);
        \Modules\Muzibu\App\Models\Playlist::observe($dataObserver);
        \Modules\Muzibu\App\Models\Radio::observe($dataObserver);
        \Modules\Muzibu\App\Models\Genre::observe($dataObserver);
        \Modules\Muzibu\App\Models\Sector::observe($dataObserver);

        // Cache Count Observers
        \Modules\Muzibu\App\Models\Song::observe(\Modules\Muzibu\App\Observers\SongObserver::class);
        \Modules\Muzibu\App\Models\Album::observe(\Modules\Muzibu\App\Observers\AlbumObserver::class);
    }

    /**
     * Morph Map Kayıtları
     *
     * Polymorphic ilişkilerde tam class adı yerine kısa alias kullanılır.
     * Bu sayede DB'de "Modules\Muzibu\App\Models\Sector" yerine sadece "sector" yazılır.
     *
     * playlistables tablosu bu alias'ları kullanır:
     * - sector -> Sector model
     * - radio -> Radio model
     * - corporate -> MuzibuCorporateAccount model
     * - mood -> (İleride eklenecek)
     */
    protected function registerMorphMap(): void
    {
        // morphMap() = sadece alias tanımla (diğer modellere dokunma)
        // enforceMorphMap() = TÜM morphable modeller tanımlı olmalı (User vb. için hata verir)
        Relation::morphMap([
            'sector' => \Modules\Muzibu\App\Models\Sector::class,
            'radio' => \Modules\Muzibu\App\Models\Radio::class,
            'corporate' => \Modules\Muzibu\App\Models\MuzibuCorporateAccount::class,
            // İleride eklenecek:
            // 'mood' => \Modules\Muzibu\App\Models\Mood::class,
            // 'genre' => \Modules\Muzibu\App\Models\Genre::class,
        ]);
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

        // Spot Components
        Livewire::component('muzibu::admin.spot-component', \Modules\Muzibu\App\Http\Livewire\Admin\SpotComponent::class);
        Livewire::component('muzibu::admin.spot-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\SpotManageComponent::class);

        // Certificate Components
        Livewire::component('muzibu::admin.certificate-component', \Modules\Muzibu\App\Http\Livewire\Admin\CertificateComponent::class);
        Livewire::component('muzibu::admin.certificate-manage-component', \Modules\Muzibu\App\Http\Livewire\Admin\CertificateManageComponent::class);

        // Frontend Components
        Livewire::component('muzibu::frontend.search-results', \Modules\Muzibu\App\Http\Livewire\Frontend\SearchResults::class);
    }

    /**
     * Blade anonymous component'lerini kaydet (x-muzibu.*)
     * resources/views/themes/muzibu/components/ klasöründeki blade dosyaları
     */
    protected function registerBladeComponents(): void
    {
        // x-muzibu.* namespace'ini tema component klasörüne bağla
        Blade::anonymousComponentPath(
            resource_path('views/themes/muzibu/components'),
            'muzibu'
        );
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
     * Load web routes dynamically for all Muzibu tenant domains
     */
    protected function loadWebRoutes(): void
    {
        // Tenant 1001 domain'lerini al (CENTRAL DB - domains tablosu central'da)
        $domains = \Illuminate\Support\Facades\DB::connection('mysql')->table('domains')
            ->where('tenant_id', 1001)
            ->pluck('domain')
            ->toArray();

        foreach ($domains as $index => $domain) {
            \Illuminate\Support\Facades\Route::middleware(['web', \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class])
                ->domain($domain)
                ->name($index === 0 ? '' : "d{$index}.")
                ->group(module_path('Muzibu', 'routes/web.php'));
        }
    }

    /**
     * Load API routes dynamically for all Muzibu tenant domains
     */
    protected function loadApiRoutes(): void
    {
        // Tenant 1001 domain'lerini al (CENTRAL DB - domains tablosu central'da)
        $domains = \Illuminate\Support\Facades\DB::connection('mysql')->table('domains')
            ->where('tenant_id', 1001)
            ->pluck('domain')
            ->toArray();

        foreach ($domains as $index => $domain) {
            // 🔑 HLS ENCRYPTION KEY - WITHOUT SESSION & WITHOUT CORS MIDDLEWARE
            // ⚠️ Route path is /hls-key/... (NOT /api/...) to bypass Laravel's CORS middleware
            // Problem: Laravel CORS has `supports_credentials => true` + `allowed_origins => ['*']`
            // Browser rejects this combination: "Access-Control-Allow-Origin: *" with credentials is invalid
            // Solution: By using /hls-key/ path, controller handles CORS directly without middleware interference
            // Security: Key alone is useless - you need the HLS file to know which key to use
            // Rate limiting: 60 requests per minute per IP (for normal playback)
            \Illuminate\Support\Facades\Route::middleware([
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                'throttle:60,1', // 60 req/min - normal playback için yeterli
                \App\Http\Middleware\FixResponseCacheHeaders::class,
            ])
                ->domain($domain)
                ->match(['get', 'options'], '/hls-key/muzibu/songs/{id}', [\Modules\Muzibu\app\Http\Controllers\Api\SongController::class, 'serveEncryptionKey'])
                ->name(($index === 0 ? '' : "d{$index}.") . 'muzibu.songs.encryption-key');

            // 🎵 HLS FILES (playlist + segments) - WITHOUT SESSION & WITHOUT CORS MIDDLEWARE
            // ⚠️ Route path is /hls/... (NOT /api/...) to bypass Laravel's CORS middleware
            // Laravel CORS config has `supports_credentials => true` which adds `Access-Control-Allow-Credentials: true`
            // This conflicts with `Access-Control-Allow-Origin: *` - browsers reject this combination
            // By using /hls/ path, our controller handles CORS directly without Laravel middleware interference
            \Illuminate\Support\Facades\Route::middleware([
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                'throttle:120,1', // 120 req/min - segments için daha yüksek limit
            ])
                ->domain($domain)
                ->match(['get', 'options'], '/hls/muzibu/songs/{id}/{filename}', [\Modules\Muzibu\app\Http\Controllers\Api\SongStreamController::class, 'serveHls'])
                ->where('filename', 'playlist\.m3u8|segment-\d+\.ts')
                ->name(($index === 0 ? '' : "d{$index}.") . 'muzibu.songs.hls-files');

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
            \Modules\Muzibu\App\Console\Commands\RecalculateCountsCommand::class,
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
