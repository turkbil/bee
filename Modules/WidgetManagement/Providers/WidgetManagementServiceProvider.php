<?php

namespace Modules\WidgetManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetGalleryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\FileWidgetListComponent;
use Modules\WidgetManagement\app\Services\WidgetService;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Modules\WidgetManagement\app\Support\ShortcodeParser;
use Modules\WidgetManagement\app\Support\HandlebarsRenderer;
use Nwidart\Modules\Traits\PathNamespace;
use Illuminate\Support\Facades\Blade;

class WidgetManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;
    
    protected string $name = 'WidgetManagement';
    
    protected string $nameLower = 'widgetmanagement';

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        
        // Central veritabanında çalışıyorsak tüm migration'ları yükle
        if (!app()->bound('tenancy.tenant')) {
            // Central migrations 
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations')
            ]);
        } else {
            // Tenant migrations
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations/tenant')
            ]);
        }

        $this->loadRoutesFrom(module_path($this->name, 'routes/web.php'));
        $this->loadViewsFrom(module_path($this->name, 'resources/views'), $this->nameLower);
        
        // Livewire bileşenlerini kaydedelim
        Livewire::component('widget-component', WidgetComponent::class);
        Livewire::component('widget-manage-component', WidgetManageComponent::class);
        Livewire::component('widget-item-component', WidgetItemComponent::class);
        Livewire::component('widget-item-manage-component', WidgetItemManageComponent::class);
        Livewire::component('widget-settings-component', WidgetSettingsComponent::class);
        Livewire::component('widget-gallery-component', WidgetGalleryComponent::class);
        Livewire::component('widget-category-component', WidgetCategoryComponent::class);
        Livewire::component('widget-category-manage-component', WidgetCategoryManageComponent::class);
        Livewire::component('file-widget-list-component', FileWidgetListComponent::class);
        
        // Widget blade direktifleri
        $this->registerBladeDirectives();
        
        // Helper Dosyasını Yükle
        $this->loadHelperFile();
    }

    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        $this->app->singleton('widget.service', function ($app) {
            return new WidgetService();
        });
        
        $this->app->singleton('widget.item.service', function ($app) {
            return new WidgetItemService($app['widget.service']);
        });
        
        // ShortcodeParser singleton kaydı
        $this->app->singleton('shortcode.parser', function ($app) {
            return new ShortcodeParser();
        });
        
        // Handlebars Renderer
        $this->app->singleton('handlebars.renderer', function ($app) {
            return new HandlebarsRenderer();
        });
    }
    
    /**
     * Blade direktiflerini kaydet
     */
    protected function registerBladeDirectives(): void
    {
        // Widget blade direktifi - ID ile render
        Blade::directive('widget', function ($expression) {
            return "<?php echo widget_by_id($expression); ?>";
        });
        
        // Widget block direktifi - Slug ile render
        Blade::directive('widgetblock', function ($expression) {
            return "<?php echo widget_by_slug($expression); ?>";
        });
        
        // Widgets direktifi - Pozisyona göre render
        Blade::directive('widgets', function ($expression) {
            return "<?php echo widgets_by_position($expression); ?>";
        });
        
        // Shortcode parse direktifi
        Blade::directive('parsewidgets', function ($expression) {
            return "<?php echo parse_widget_shortcodes($expression); ?>";
        });

        // Module widget blade directive - ID ile render
        Blade::directive('modulewidget', function ($expression) {
            return "<?php echo module_widget_by_id($expression); ?>";
        });

        // Module widgets blade directive - Module ID ile render
        Blade::directive('modulewidgets', function ($expression) {
            return "<?php echo module_widgets_by_module($expression); ?>";
        });
    }
    
    /**
     * Helper dosyasını yükle
     */
    protected function loadHelperFile(): void
    {
        $helperPath = module_path($this->name, 'app/Helpers/WidgetHelper.php');
        
        if (file_exists($helperPath)) {
            require_once $helperPath;
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
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path($this->nameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->name, 'config/config.php'), $this->nameLower
        );
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
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'widget.service',
            'widget.item.service',
            'shortcode.parser',
            'handlebars.renderer'
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