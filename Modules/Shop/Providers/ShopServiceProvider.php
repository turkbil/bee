<?php

namespace Modules\Shop\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopProductManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCategoryManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopBrandManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCurrencyComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCurrencyManageComponent;
use Modules\Shop\App\Http\Livewire\Admin\ShopCartComponent;
use Modules\Shop\App\Http\Livewire\Admin\HomepageProductsComponent;
use Modules\Shop\App\Http\Livewire\Front\CartWidget;
use Modules\Shop\App\Http\Livewire\Front\CartPage;
use Modules\Shop\App\Http\Livewire\Front\AddToCartButton;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ShopServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Shop';

    protected string $nameLower = 'shop';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Observers
        \Modules\Shop\App\Models\ShopProduct::observe(\Modules\Shop\App\Observers\ShopProductObserver::class);
        \Modules\Shop\App\Models\ShopCategory::observe(\Modules\Shop\App\Observers\ShopCategoryObserver::class);
        \Modules\Shop\App\Models\ShopBrand::observe(\Modules\Shop\App\Observers\ShopBrandObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Önce rotalar yüklenir
        $this->loadRoutesFrom(module_path('Shop', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('Shop', 'routes/admin.php'));

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Shop', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'shop-themes');
        }
        $this->loadViewsFrom(module_path('Shop', 'resources/views'), 'shop');

        // Admin Livewire Components
        Livewire::component('shop-product-component', ShopProductComponent::class);
        Livewire::component('shop-product-manage-component', ShopProductManageComponent::class);
        Livewire::component('shop-category-component', ShopCategoryComponent::class);
        Livewire::component('shop-category-manage-component', ShopCategoryManageComponent::class);
        Livewire::component('shop-brand-component', ShopBrandComponent::class);
        Livewire::component('shop-brand-manage-component', ShopBrandManageComponent::class);
        Livewire::component('shop-currency-component', ShopCurrencyComponent::class);
        Livewire::component('shop-currency-manage-component', ShopCurrencyManageComponent::class);
        Livewire::component('shop-cart-component', ShopCartComponent::class);
        Livewire::component('homepage-products-component', HomepageProductsComponent::class);

        // Front Livewire Components (Cart System)
        Livewire::component('shop::front.cart-widget', CartWidget::class);
        Livewire::component('shop::front.cart-page', CartPage::class);
        Livewire::component('shop::front.add-to-cart-button', AddToCartButton::class);
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
            \Modules\Shop\App\Contracts\ShopCategoryRepositoryInterface::class,
            \Modules\Shop\App\Repositories\ShopCategoryRepository::class
        );

        $this->app->bind(
            \Modules\Shop\App\Contracts\ShopProductRepositoryInterface::class,
            \Modules\Shop\App\Repositories\ShopProductRepository::class
        );

        $this->app->bind(
            \Modules\Shop\App\Contracts\ShopBrandRepositoryInterface::class,
            \Modules\Shop\App\Repositories\ShopBrandRepository::class
        );

        $this->app->bind(
            \Modules\Shop\App\Contracts\ShopProductVariantRepositoryInterface::class,
            \Modules\Shop\App\Repositories\ShopProductVariantRepository::class
        );

        $this->app->bind(
            \App\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );

        // Service Layer bindings
        $this->app->singleton(\Modules\Shop\App\Services\ShopCategoryService::class);
        $this->app->singleton(\Modules\Shop\App\Services\ShopProductService::class);
        $this->app->singleton(\Modules\Shop\App\Services\ShopBrandService::class);
        $this->app->singleton(\Modules\Shop\App\Services\ShopProductVariantService::class);
        $this->app->singleton(\Modules\Shop\App\Services\ShopCartService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Shop\App\Console\WarmShopCacheCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);

            // CURRENCY RATES AUTO UPDATE - TCMB Daily Update
            // Günde 2 kez: Mesai başında ve sonunda
            // Her tenant için ayrı çalışır

            // Mesai başı - Sabah 09:00
            $schedule->command('tenants:run currency:update-rates')
                     ->dailyAt('09:00')
                     ->withoutOverlapping()
                     ->runInBackground()
                     ->appendOutputTo(storage_path('logs/currency-updates.log'));

            // Mesai sonu - Akşam 17:00
            $schedule->command('tenants:run currency:update-rates')
                     ->dailyAt('17:00')
                     ->withoutOverlapping()
                     ->runInBackground()
                     ->appendOutputTo(storage_path('logs/currency-updates.log'));

            // TEST MODE: Her dakika test etmek için uncomment et
            // $schedule->command('tenants:run currency:update-rates')
            //          ->everyMinute()
            //          ->withoutOverlapping()
            //          ->runInBackground()
            //          ->appendOutputTo(storage_path('logs/currency-updates.log'));
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
        $viewPath = resource_path('views/modules/shop');
        $sourcePath = module_path('Shop', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'shop-module-views']);

        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Shop', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/shop');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'shop-module-theme-views']);
        }

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'shop');
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
            if (is_dir($path . '/modules/shop')) {
                $paths[] = $path . '/modules/shop';
            }
        }

        return $paths;
    }
}
