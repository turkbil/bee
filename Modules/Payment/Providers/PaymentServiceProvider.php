<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Payment\App\Http\Livewire\Admin\PaymentsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentDetailComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodManageComponent;
use Modules\Payment\App\Http\Livewire\Front\PaymentPage;
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

        // Admin Livewire Components
        Livewire::component('payment::admin.payments-component', PaymentsComponent::class);
        Livewire::component('payment::admin.payment-detail-component', PaymentDetailComponent::class);
        Livewire::component('payment::admin.payment-methods-component', PaymentMethodsComponent::class);
        Livewire::component('payment::admin.payment-method-manage-component', PaymentMethodManageComponent::class);

        // Frontend Livewire Components
        Livewire::component('payment::front.payment-page', PaymentPage::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Payment\App\Console\UpdatePayTRInstallmentRatesCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);

            // PayTR Taksit Oranları Güncelleme - Günde 2 kez
            // Sabah 09:00 (işyeri açılmadan önce)
            $schedule->command('payment:update-paytr-rates')
                     ->dailyAt('09:00')
                     ->withoutOverlapping()
                     ->runInBackground()
                     ->appendOutputTo(storage_path('logs/paytr-installments.log'));

            // Akşam 18:00 (işyeri kapandıktan sonra)
            $schedule->command('payment:update-paytr-rates')
                     ->dailyAt('18:00')
                     ->withoutOverlapping()
                     ->runInBackground()
                     ->appendOutputTo(storage_path('logs/paytr-installments.log'));
        });
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
