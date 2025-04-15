<?php

namespace Modules\Studio\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Studio\App\Http\Livewire\EditorComponent;
use Modules\Studio\App\Http\Livewire\WidgetManagerComponent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Modules\Studio\App\Services\AssetService;
use Modules\Studio\App\Services\BlockService;
use Modules\Studio\App\Services\EditorService;
use Modules\Studio\App\Services\WidgetService;
use Modules\Studio\App\Parsers\HtmlParser;
use Modules\Studio\App\Parsers\CssParser;
use Modules\Studio\App\Repositories\SettingsRepository;
use Modules\Studio\App\Support\BlockManager;
use Modules\Studio\App\Support\StudioHelper;

class StudioServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Studio';
    protected string $moduleNameLower = 'studio';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerCommands();
        $this->registerTranslations();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->publishAssets();

        // Rotaları yükle
        $this->loadRoutesFrom(module_path('Studio', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Studio', 'routes/admin.php'));
        $this->loadRoutesFrom(module_path('Studio', 'routes/api.php'));
        
        // Livewire bileşenlerini kaydet
        Livewire::component('studio-editor', EditorComponent::class);
        Livewire::component('studio-widget-manager', WidgetManagerComponent::class);
        
        // Blade direktiflerini kaydet
        Blade::directive('studiocss', function () {
            return "<?php echo app('studio.asset')->renderCss(); ?>";
        });
        
        Blade::directive('studiojs', function () {
            return "<?php echo app('studio.asset')->renderJs(); ?>";
        });
        
        // Facade kaydet
        $this->app->bind('studio', function ($app) {
            return new StudioHelper();
        });
        
        // Olay sağlayıcılarını kaydet
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Servisleri kaydet
        $this->app->singleton('studio.asset', function ($app) {
            return new AssetService();
        });
        
        $this->app->singleton('studio.block', function ($app) {
            return new BlockService();
        });
        
        $this->app->singleton('studio.editor', function ($app) {
            return new EditorService();
        });
        
        $this->app->singleton('studio.widget', function ($app) {
            return new WidgetService();
        });
        
        $this->app->bind(BlockService::class, function ($app) {
            return new BlockService();
        });
        
        // Parser'ları kaydet
        $this->app->bind(HtmlParser::class, function ($app) {
            return new HtmlParser();
        });
        
        $this->app->bind(CssParser::class, function ($app) {
            return new CssParser();
        });
        
        // Repository'leri kaydet
        $this->app->bind(SettingsRepository::class, function ($app) {
            return new SettingsRepository();
        });
        
        // Support sınıflarını kaydet
        $this->app->singleton(BlockManager::class, function ($app) {
            return new BlockManager();
        });
    }

    /**
     * Varlıkları yayınla
     */
    protected function publishAssets(): void
    {
        $sourcePath = module_path('Studio', 'resources/assets');
        $destinationPath = public_path('modules/studio');

        $this->publishes([
            $sourcePath . '/css' => $destinationPath . '/css',
            $sourcePath . '/js' => $destinationPath . '/js',
        ], 'studio-assets');

        // Uygulamanın üretim ortamında olup olmadığını kontrol et
        if (!$this->app->isProduction() && config('studio.dev.auto_publish_assets', true)) {
            $this->publishResourcesForDevMode($sourcePath, $destinationPath);
        }
    }

    private function publishResourcesForDevMode($sourcePath, $destinationPath): void
    {
        try {
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // CSS dosyalarını kopyala
            if (is_dir($sourcePath . '/css')) {
                if (!is_dir($destinationPath . '/css')) {
                    mkdir($destinationPath . '/css', 0755, true);
                }
                foreach (glob($sourcePath . '/css/*.css') as $file) {
                    copy($file, $destinationPath . '/css/' . basename($file));
                }
            }

            // JS dosyalarını kopyala
            if (is_dir($sourcePath . '/js')) {
                if (!is_dir($destinationPath . '/js')) {
                    mkdir($destinationPath . '/js', 0755, true);
                }
                foreach (glob($sourcePath . '/js/*.js') as $file) {
                    copy($file, $destinationPath . '/js/' . basename($file));
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda loglama yap
            \Illuminate\Support\Facades\Log::error('Studio Modülü: Varlıkları yayınlarken hata: ' . $e->getMessage());
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'config/studio.php') => config_path('studio.php'),
        ], 'studio-config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/studio.php'), 'studio'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/studio');
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'studio-views']);

        $this->loadViewsFrom(array_merge([$sourcePath], $this->getPublishableViewPaths()), 'studio');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/studio');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        // İleride eklenebilir
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'studio.asset',
            'studio.block',
            'studio.editor',
            'studio.widget',
            'studio',
            BlockService::class
        ];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/studio')) {
                $paths[] = $path . '/modules/studio';
            }
        }
        return $paths;
    }
}