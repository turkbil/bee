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
use Modules\Studio\App\Http\Livewire\Admin\StudioComponent;

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
        
        // Varlıkları otomatik yayınlama işlemi kaldırıldı
        // $this->publishAssets();

        // Rotaları yükle
        $this->loadRoutesFrom(module_path('Studio', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Studio', 'routes/admin.php'));
        $this->loadRoutesFrom(module_path('Studio', 'routes/api.php'));
        
        // Livewire bileşenlerini kaydet
        Livewire::component('studio-editor', EditorComponent::class);
        Livewire::component('studio-widget-manager', WidgetManagerComponent::class);
        Livewire::component('studio-component', StudioComponent::class);        
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
        
        // WidgetManagement modülünü kontrol et ve bağımlılık oluştur
        if (class_exists('Modules\WidgetManagement\Providers\WidgetManagementServiceProvider')) {
            // WidgetManagement modülü yüklü, servisleri kullan
            if (!$this->app->bound('widget.service')) {
                $this->app->singleton('widget.service', function ($app) {
                    return new \Modules\WidgetManagement\App\Services\WidgetService();
                });
            }
            
            if (!$this->app->bound('widget.item.service')) {
                $this->app->singleton('widget.item.service', function ($app) {
                    return new \Modules\WidgetManagement\App\Services\WidgetItemService(
                        $app['widget.service']
                    );
                });
            }
        }
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
     * Varlıkları yayınla - Otomatik yayınlama devre dışı
     * Manuel olarak başka bir yere dosyaları yüklendiği için bu fonksiyon pasif hale getirildi
     */
    protected function publishAssets(): void
    {
        // Varlıkları otomatik olarak kopyalamayı devre dışı bıraktık
        // Kullanıcı manuel olarak başka bir yere yüklemiş
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
        // Ana dil dosyaları - modül klasöründen yükle
        $moduleLangPath = module_path($this->moduleName, 'lang');
        if (is_dir($moduleLangPath)) {
            $this->loadTranslationsFrom($moduleLangPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($moduleLangPath);
        }
        
        // Resource'daki dil dosyaları (varsa)
        $resourceLangPath = resource_path('lang/modules/' . $this->moduleNameLower);
        if (is_dir($resourceLangPath)) {
            $this->loadTranslationsFrom($resourceLangPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($resourceLangPath);
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