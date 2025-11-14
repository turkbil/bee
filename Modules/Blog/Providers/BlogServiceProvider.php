<?php

namespace Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Blog\App\Http\Livewire\Admin\BlogComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogManageComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogCategoryComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogCategoryManageComponent;
use Modules\Blog\App\Http\Livewire\Admin\BlogAiDraftComponent;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BlogServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Blog';

    protected string $nameLower = 'blog';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Blog Observer kaydı - Geçici olarak kapatıldı (migration sonrası aktif edilecek)
        // \Modules\Blog\App\Models\Blog::observe(\Modules\Blog\App\Observers\BlogObserver::class);
        // \Modules\Blog\App\Models\BlogCategory::observe(\Modules\Blog\App\Observers\BlogCategoryObserver::class);
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Tema Klasörleri - YENİ YAPI
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        // Front themes klasörü için kontrol ekle
        $frontThemesPath = module_path('Blog', 'resources/views/front/themes');
        if (is_dir($frontThemesPath)) {
            $this->loadViewsFrom($frontThemesPath, 'blog-themes');
        }
        $this->loadViewsFrom(module_path('Blog', 'resources/views'), 'blog');

        Livewire::component('blog-component', BlogComponent::class);
        Livewire::component('blog-manage-component', BlogManageComponent::class);
        Livewire::component('blog-category-component', BlogCategoryComponent::class);
        Livewire::component('blog-category-manage-component', BlogCategoryManageComponent::class);
        Livewire::component('blog-ai-draft-component', BlogAiDraftComponent::class);
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
            \Modules\Blog\App\Contracts\BlogRepositoryInterface::class,
            \Modules\Blog\App\Repositories\BlogRepository::class
        );

        $this->app->bind(
            \Modules\Blog\App\Contracts\BlogCategoryRepositoryInterface::class,
            \Modules\Blog\App\Repositories\BlogCategoryRepository::class
        );

        $this->app->bind(
            \App\Contracts\GlobalSeoRepositoryInterface::class,
            \App\Repositories\GlobalSeoRepository::class
        );

        // Service Layer bindings
        $this->app->singleton(\Modules\Blog\App\Services\BlogService::class);
        $this->app->singleton(\Modules\Blog\App\Services\BlogCategoryService::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // Geçici olarak kapatıldı - Command dosyası oluşturulduğunda aktif edilecek
        // $this->commands([
        //     \Modules\Blog\App\Console\WarmBlogCacheCommand::class,
        // ]);
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
        $viewPath = resource_path('views/modules/blog');
        $sourcePath = module_path('Blog', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'blog-module-views']);

        // Tema klasörlerinin yapılandırması - YENİ YAPI
        $themeSourcePath = module_path('Blog', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/blog');

        // Sadece klasör varsa publish et
        if (is_dir($themeSourcePath)) {
            $this->publishes([
                $themeSourcePath => $themeViewPath,
            ], ['views', 'blog-module-theme-views']);
        }

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'blog');
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
            if (is_dir($path . '/modules/blog')) {
                $paths[] = $path . '/modules/blog';
            }
        }

        return $paths;
    }
}
