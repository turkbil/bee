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
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StudioServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Studio';
    protected string $nameLower = 'studio';

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
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

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
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], $configPath);
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
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
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }
        return $paths;
    }
}