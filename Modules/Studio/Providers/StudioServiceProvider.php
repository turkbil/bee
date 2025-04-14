<?php

namespace Modules\Studio\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Studio\App\Http\Livewire\StudioEditor;
use Modules\Studio\App\Http\Livewire\StudioWidgetManager;
use Modules\Studio\App\Services\StudioAssetService;
use Modules\Studio\App\Services\StudioThemeService;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioContentParserService;
use Modules\Studio\App\Services\StudioManagerService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

class StudioServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Studio';
    protected string $moduleNameLower = 'studio';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Varlık yayınlama
        $this->publishAssets();

        // Livewire bileşenlerini kaydet
        Livewire::component('studio-editor', StudioEditor::class);
        Livewire::component('studio-widget-manager', StudioWidgetManager::class);
        
        // Blade direktifleri
        $this->registerBladeDirectives();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        
        // Servisleri kaydet
        $this->registerServices();
    }

    /**
     * Servisleri kaydet
     */
    protected function registerServices(): void
    {
        // Merkezi yönetim servisi
        $this->app->singleton('studio.manager', function ($app) {
            return new StudioManagerService();
        });
        
        // Asset servisi - JS/CSS dosyalarını yönetir
        $this->app->singleton('studio.assets', function ($app) {
            return new StudioAssetService();
        });
        
        // Widget servisi - Widgetları yönetir
        $this->app->singleton('studio.widget', function ($app) {
            return new StudioWidgetService();
        });
        
        // Tema servisi - Temaları yönetir
        $this->app->singleton('studio.theme', function ($app) {
            return new StudioThemeService();
        });
        
        // İçerik ayrıştırma servisi
        $this->app->singleton('studio.parser', function ($app) {
            return new StudioContentParserService();
        });
    }

    /**
     * Blade direktiflerini kaydet
     */
    protected function registerBladeDirectives(): void
    {
        // CSS dosyalarını yüklemek için
        Blade::directive('studiocss', function () {
            return "<?php echo app('studio.assets')->renderCss(); ?>";
        });
        
        // JS dosyalarını yüklemek için
        Blade::directive('studiojs', function () {
            return "<?php echo app('studio.assets')->renderJs(); ?>";
        });
    }

    /**
     * Varlıkları (assets) yayınla
     */
    protected function publishAssets(): void
    {
        // Asset kaynak ve hedef yolları
        $sourcePath = module_path($this->moduleName, 'Resources/assets');
        $destinationPath = public_path('modules/studio');

        // Hedef dizin yoksa oluştur
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        // Yayın yapılandırması
        $this->publishes([
            $sourcePath => $destinationPath,
        ], 'studio-assets');

        // Geliştirme modunda assets dosyalarını kopyala
        $this->copyAssets($sourcePath, $destinationPath);
    }

    /**
     * Varlıkları kopyala
     */
    protected function copyAssets(string $sourcePath, string $destinationPath): void
    {
        if (File::isDirectory($sourcePath)) {
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            foreach (File::allFiles($sourcePath) as $file) {
                $fileDest = $destinationPath . '/' . $file->getRelativePathname();
                $directory = dirname($fileDest);
                
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                
                File::copy($file->getPathname(), $fileDest);
            }
        }
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (File::isDirectory($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'studio-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'studio.manager',
            'studio.assets',
            'studio.widget',
            'studio.theme',
            'studio.parser'
        ];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (File::isDirectory($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}