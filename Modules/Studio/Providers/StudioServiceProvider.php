<?php

namespace Modules\Studio\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Studio\App\Http\Livewire\StudioEditor;
use Modules\Studio\App\Http\Livewire\StudioWidgetManager;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

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
        $this->publishAssets();

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Studio', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Studio', 'routes/admin.php'));
        
        // Livewire komponentleri
        Livewire::component('studio-editor', StudioEditor::class);
        Livewire::component('studio-widget-manager', StudioWidgetManager::class);
        
        // Blade direktiflerini kaydet
        Blade::directive('studiocss', function () {
            return "<?php echo app('studio.assets')->renderCss(); ?>";
        });
        
        Blade::directive('studiojs', function () {
            return "<?php echo app('studio.assets')->renderJs(); ?>";
        });
        
        // Page modülü ile entegrasyon için event dinleyicisi
        if (class_exists('\Modules\Page\App\Models\Page')) {
            Event::listen('page.edit', function ($page) {
                // Page edit butonunu ekle
                view()->composer('page::admin.livewire.page-manage-component', function ($view) {
                    $view->with('studioEnabled', true);
                });
            });
        }
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
        if (!$this->app->isProduction()) {
            // Varlıkları otomatik olarak yayınla
            $this->publishResourcesForDevMode($sourcePath, $destinationPath);
        }
        
        // Modüler JS dosyalarını yayınla
        $this->publishModularJsFiles();
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
                // Ana js dosyaları
                foreach (glob($sourcePath . '/js/*.js') as $file) {
                    copy($file, $destinationPath . '/' . basename($file));
                }
                
                // Sadece temel GrapesJS dosyasını kopyala (grapes.min.js)
                $grapesJsPath = $sourcePath . '/js/grapes.min.js';
                if (file_exists($grapesJsPath)) {
                    copy($grapesJsPath, $destinationPath . '/grapes.min.js');
                }
                
                // Özel studio.js dosyasını kopyala
                $studioJsPath = $sourcePath . '/js/studio.js';
                if (file_exists($studioJsPath)) {
                    copy($studioJsPath, $destinationPath . '/studio.js');
                }
                
                // Plugin klasörünü oluştur ama sadece belirli plugin'leri kopyala
                if (!is_dir($destinationPath . '/plugins')) {
                    mkdir($destinationPath . '/plugins', 0755, true);
                }
                
                // Sadece gerekli plugin'leri kopyala (ihtiyaç duyarsanız)
                $safePlugins = [
                    // 'gjs-blocks-basic.min.js' 
                    // Şimdilik plugin'leri devre dışı bırakalım
                ];
                
                if (is_dir($sourcePath . '/js/plugins')) {
                    foreach ($safePlugins as $plugin) {
                        $pluginPath = $sourcePath . '/js/plugins/' . $plugin;
                        if (file_exists($pluginPath)) {
                            copy($pluginPath, $destinationPath . '/plugins/' . $plugin);
                        }
                    }
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Studio Modülü: Varlıklar başarıyla yayınlandı.');
        } catch (\Exception $e) {
            // Hata durumunda loglama yap
            \Illuminate\Support\Facades\Log::error('Studio Modülü: Varlıkları yayınlarken hata: ' . $e->getMessage());
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // Studio servislerini kaydet
        $this->app->singleton('studio.assets', function ($app) {
            return new \Modules\Studio\App\Services\StudioAssetService();
        });
        
        $this->app->singleton('studio.widget', function ($app) {
            return new \Modules\Studio\App\Services\StudioWidgetService();
        });
        
        $this->app->singleton('studio.theme', function ($app) {
            return new \Modules\Studio\App\Services\StudioThemeService();
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
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
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
        $viewPath = resource_path('views/modules/studio');
        $sourcePath = module_path('Studio', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'studio-module-views']);
    
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'studio');
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
            if (is_dir($path . '/modules/studio')) {
                $paths[] = $path . '/modules/studio';
            }
        }

        return $paths;
    }
}