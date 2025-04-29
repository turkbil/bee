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
        
        // Widget blade direktifi
        Blade::directive('widget', function ($expression) {
            return "<?php echo app('widget.service')->renderSingleWidget(\$tenantWidget = \Modules\WidgetManagement\app\Models\TenantWidget::find($expression)); ?>";
        });
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
        return [];
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