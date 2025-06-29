<?php

namespace Modules\LanguageManagement\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Livewire\Livewire;
use Modules\LanguageManagement\app\Http\Livewire\Admin\LanguageSettingsComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\AdminLanguageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\AdminLanguageManageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TenantLanguageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TenantLanguageManageComponent;
use Modules\LanguageManagement\app\Http\Livewire\Admin\TranslationManageComponent;
use Modules\LanguageManagement\app\Http\Livewire\LanguageSwitcher;
use Modules\LanguageManagement\app\Http\Livewire\AdminLanguageSwitcher;
use Modules\LanguageManagement\app\Http\Middleware\SetLocaleMiddleware;
use Modules\LanguageManagement\app\Http\Middleware\CentralDomainOnly;

class LanguageManagementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'LanguageManagement';

    protected string $nameLower = 'languagemanagement';

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
        $this->registerLivewireComponents();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // Middleware kaydet
        $this->app['router']->aliasMiddleware('set.locale', SetLocaleMiddleware::class);
        $this->app['router']->aliasMiddleware('central.domain', CentralDomainOnly::class);
        
        // Helper fonksiyonları yükle
        require_once module_path($this->name, 'app/Helpers/language_helpers.php');
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
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
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
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('languagemanagement::admin.language-settings', LanguageSettingsComponent::class);
        Livewire::component('languagemanagement::admin.system-language', AdminLanguageComponent::class);
        Livewire::component('languagemanagement::admin.system-language-manage', AdminLanguageManageComponent::class);
        Livewire::component('languagemanagement::admin.site-language', TenantLanguageComponent::class);
        Livewire::component('languagemanagement::admin.site-language-manage', TenantLanguageManageComponent::class);
        Livewire::component('languagemanagement::admin.translation-manage', TranslationManageComponent::class);
        Livewire::component('languagemanagement::language-switcher', LanguageSwitcher::class);
        Livewire::component('languagemanagement::admin-language-switcher', AdminLanguageSwitcher::class);
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
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
