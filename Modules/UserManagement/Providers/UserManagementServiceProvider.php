<?php
namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\UserManagement\App\Http\Livewire\UserComponent;
use Modules\UserManagement\App\Http\Livewire\UserManageComponent;
use Modules\UserManagement\App\Http\Livewire\RoleComponent;
use Modules\UserManagement\App\Http\Livewire\RoleManageComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionComponent;
use Modules\UserManagement\App\Http\Livewire\PermissionManageComponent;
use Modules\UserManagement\App\Http\Livewire\ModulePermissionComponent;
use Modules\UserManagement\App\Http\Livewire\UserModulePermissionComponent;
use Modules\UserManagement\App\Http\Livewire\ActivityLogComponent;
use Modules\UserManagement\App\Http\Livewire\UserActivityLogComponent;
use Modules\UserManagement\App\Http\Livewire\Modals\ConfirmActionModal;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Modules\UserManagement\App\Console\Commands\ProtectBaseRoles;
use Modules\UserManagement\App\Http\Middleware\ModulePermissionMiddleware;

class UserManagementServiceProvider extends ServiceProvider
{
   use PathNamespace;

   protected string $name = 'UserManagement';

   protected string $nameLower = 'usermanagement';

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
    
        // Modülün routes ve views dosyalarını yükle
        $this->loadRoutesFrom(module_path('UserManagement', 'routes/web.php'));
        $this->loadViewsFrom(module_path('UserManagement', 'resources/views'), 'usermanagement');
    
        // Livewire component'lerini kaydet
        Livewire::component('usermanagement.user-component', UserComponent::class);
        Livewire::component('usermanagement.user-manage-component', UserManageComponent::class);
        Livewire::component('usermanagement.role-component', RoleComponent::class);
        Livewire::component('usermanagement.role-manage-component', RoleManageComponent::class);
        Livewire::component('usermanagement.permission-component', PermissionComponent::class);
        Livewire::component('usermanagement.permission-manage-component', PermissionManageComponent::class);
        
        // Yeni eklenen bileşenler
        Livewire::component('usermanagement.module-permission-component', ModulePermissionComponent::class);
        Livewire::component('usermanagement.user-module-permission-component', UserModulePermissionComponent::class);
        
        // Aktivite log bileşenleri
        Livewire::component('usermanagement.activity-log-component', ActivityLogComponent::class);
        Livewire::component('usermanagement.user-activity-log-component', UserActivityLogComponent::class);
        
        Livewire::component('usermanagement.confirm-action-modal', ConfirmActionModal::class);

        // Middleware'i kaydet
        $this->app['router']->aliasMiddleware('module.permission', ModulePermissionMiddleware::class);
    }
    
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(ModulePermissionHelperServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ProtectBaseRoles::class
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
        $viewPath   = resource_path('views/modules/usermanagement');
        $sourcePath = module_path('UserManagement', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'usermanagement-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'usermanagement');
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
            if (is_dir($path . '/modules/usermanagement')) {
                $paths[] = $path . '/modules/usermanagement';
            }
        }

        return $paths;
    }
}