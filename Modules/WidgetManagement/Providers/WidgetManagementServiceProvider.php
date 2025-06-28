<?php

namespace Modules\WidgetManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\WidgetManagement\app\Http\Livewire\WidgetComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCodeEditorComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetItemManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetSettingsComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetGalleryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetCategoryManageComponent;
use Modules\WidgetManagement\app\Http\Livewire\FileWidgetListComponent;
use Modules\WidgetManagement\app\Http\Livewire\ModuleWidgetListComponent;
use Modules\WidgetManagement\app\Http\Livewire\WidgetFormBuilderComponent;
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

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        
        if (!app()->bound('tenancy.tenant')) {
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations')
            ]);
        } else {
            $this->loadMigrationsFrom([
                module_path($this->name, 'database/migrations/tenant')
            ]);
        }

        $this->loadRoutesFrom(module_path($this->name, 'routes/web.php'));
        $this->loadRoutesFrom(module_path($this->name, 'routes/admin.php'));
        $this->loadViewsFrom(module_path($this->name, 'resources/views'), $this->nameLower);
        
        Livewire::component('widget-component', WidgetComponent::class);
        Livewire::component('widget-manage-component', WidgetManageComponent::class);
        Livewire::component('widget-code-editor-component', WidgetCodeEditorComponent::class);
        Livewire::component('widget-item-component', WidgetItemComponent::class);
        Livewire::component('widget-item-manage-component', WidgetItemManageComponent::class);
        Livewire::component('widget-settings-component', WidgetSettingsComponent::class);
        Livewire::component('widget-gallery-component', WidgetGalleryComponent::class);
        Livewire::component('widget-category-component', WidgetCategoryComponent::class);
        Livewire::component('widget-category-manage-component', WidgetCategoryManageComponent::class);
        Livewire::component('file-widget-list-component', FileWidgetListComponent::class);
        Livewire::component('module-widget-list-component', ModuleWidgetListComponent::class);
        Livewire::component('widget-form-builder-component', WidgetFormBuilderComponent::class);
        
        Livewire::component('modules.widget-management.app.http.livewire.widget-code-editor-component', WidgetCodeEditorComponent::class);
        Livewire::component('modules.widget-management.app.http.livewire.widget-manage-component', WidgetManageComponent::class);
        
        $this->registerBladeDirectives();
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
        
        $this->app->singleton('shortcode.parser', function ($app) {
            return new ShortcodeParser();
        });
        
        $this->app->singleton('handlebars.renderer', function ($app) {
            return new HandlebarsRenderer();
        });
    }
    
    protected function registerBladeDirectives(): void
    {
        Blade::directive('widget', function ($expression) {
            return "<?php echo widget_by_id($expression); ?>";
        });
        
        Blade::directive('widgetblock', function ($expression) {
            return "<?php echo widget_by_slug($expression); ?>";
        });
        
        Blade::directive('widgets', function ($expression) {
            return "<?php echo widgets_by_position($expression); ?>";
        });
        
        Blade::directive('parsewidgets', function ($expression) {
            return "<?php echo parse_widget_shortcodes($expression); ?>";
        });

        Blade::directive('modulewidget', function ($expression) {
            return "<?php echo module_widget_by_id($expression); ?>";
        });

        Blade::directive('modulewidgets', function ($expression) {
            return "<?php echo module_widgets_by_module($expression); ?>";
        });
    }
    
    protected function loadHelperFile(): void
    {
        $helperPath = module_path($this->name, 'app/Helpers/WidgetHelper.php');
        
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    protected function registerCommands(): void
    {
        
    }

    protected function registerCommandSchedules(): void
    {
        
    }

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

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path($this->nameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->name, 'config/config.php'), $this->nameLower
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);

        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
    }

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