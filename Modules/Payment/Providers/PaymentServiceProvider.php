<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Payment\App\Http\Livewire\Admin\PaymentComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentManageComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentCategoryComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentCategoryManageComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PaymentServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Payment';

    protected string $nameLower = 'payment';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Payment Observer kaydı
        \Modules\Payment\App\Models\Payment::observe(\Modules\Payment\App\Observers\PaymentObserver::class);
        \Modules\Payment\App\Models\PaymentCategory::observe(\Modules\Payment\App\Observers\PaymentCategoryObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Payment', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Payment', 'routes/admin.php'));

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Payment', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'payment-themes');
        }
        $this->loadViewsFrom(module_path('Payment', 'resources/views'), 'payment');

        Livewire::component('payment-component', PaymentComponent::class);
        Livewire::component('payment-manage-component', PaymentManageComponent::class);
        Livewire::component('payment-category-component', PaymentCategoryComponent::class);
        Livewire::component('payment-category-manage-component', PaymentCategoryManageComponent::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Repository Pattern bindings
        $this->app->bind(
            \Modules\Payment\App\Contracts\PaymentRepositoryInterface::class,
            \Modules\Payment\App\Repositories\PaymentRepository::class
        );

        $this->app->bind(
            \Modules\Payment\App\Contracts\PaymentCategoryRepositoryInterface::class,
            \Modules\Payment\App\Repositories\PaymentCategoryRepository::class
        );

        $this->app->bind(
            \App\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );

        // Service Layer bindings
        $this->app->singleton(\Modules\Payment\App\Services\PaymentService::class);
        $this->app->singleton(\Modules\Payment\App\Services\PaymentCategoryService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Payment\App\Console\WarmPaymentCacheCommand::class,
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
        $viewPath = resource_path('views/modules/payment');
        $sourcePath = module_path('Payment', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'payment-module-views']);

        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Payment', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/payment');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'payment-module-theme-views']);
        }

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'payment');
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
            if (is_dir($path . '/modules/payment')) {
                $paths[] = $path . '/modules/payment';
            }
        }

        return $paths;
    }
}
