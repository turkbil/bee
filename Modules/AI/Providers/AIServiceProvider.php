<?php

namespace Modules\AI\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\AI\App\Http\Livewire\Admin\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\SettingsPanel;
use Modules\AI\App\Http\Livewire\Admin\Modals\PromptEditModal;
use Modules\AI\App\Http\Livewire\Admin\Modals\PromptDeleteModal;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\DeepSeekService;
use Illuminate\Support\Facades\Schema;

class AIServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'AI';
    protected string $nameLower = 'ai';

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
        $this->loadRoutesFrom(module_path('AI', 'routes/web.php'));
        $this->loadRoutesFrom(module_path('AI', 'routes/admin.php'));
        
        // Tema Klasörleri
        $this->loadViewsFrom(resource_path('views/themes'), 'themes');
        $this->loadViewsFrom(module_path('AI', 'resources/views/front/themes'), 'ai-themes');
        $this->loadViewsFrom(module_path('AI', 'resources/views'), 'ai');

        // Livewire bileşenlerini kaydet
        Livewire::component('chat-panel', ChatPanel::class);
        Livewire::component('settings-panel', SettingsPanel::class);
        Livewire::component('modals.prompt-edit-modal', PromptEditModal::class);
        Livewire::component('modals.prompt-delete-modal', PromptDeleteModal::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // AI Service singleton kaydı - Lazy yükleme yaklaşımı
        $this->app->singleton(AIService::class, function ($app) {
            // Servisi talep üzerine başlat (lazy loading)
            $tableExists = $this->isAIModuleActive();
            $deepSeekService = $app->make(DeepSeekService::class);
            return new AIService($deepSeekService);
        });
        
        // DeepSeek Service singleton kaydı - Lazy yükleme yaklaşımı
        $this->app->singleton(DeepSeekService::class, function ($app) {
            // Servisi talep üzerine başlat (lazy loading)
            $tableExists = $this->isAIModuleActive();
            return new DeepSeekService(!$tableExists); // !$tableExists = safe mode
        });
    }
    
    /**
     * AI modülünün aktif olup olmadığını kontrol et
     * Bu yöntem, sadece AI modülü ile ilgili işlemlerde çağrılacak
     */
    private function isAIModuleActive(): bool
    {
        // Eğer bir AI rotasındaysak veya AI modülü sayfasına erişiliyorsa
        $currentRoute = request()->route()?->getName() ?? '';
        $currentPath = request()->path();
        
        // AI modülü ile ilgili rotalar veya cPanel rotaları için kontrolü yap
        if (strpos($currentRoute, 'ai.') === 0 || 
            strpos($currentRoute, 'admin.ai.') === 0 ||
            strpos($currentPath, 'ai') === 0 ||
            strpos($currentPath, 'admin/ai') === 0) {
                
            return Schema::hasTable('ai_settings');
        }
        
        // Diğer tüm durumlar için güvenli modu kullan
        return false;
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\AI\App\Console\Commands\MigrateRedisConversationsToDatabase::class,
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
        $viewPath = resource_path('views/modules/ai');
        $sourcePath = module_path('AI', 'resources/views');
    
        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'ai-module-views']);
        
        // Tema klasörlerinin yapılandırması
        $themeSourcePath = module_path('AI', 'resources/views/front/themes');
        $themeViewPath = resource_path('views/themes/modules/ai');
        
        $this->publishes([
            $themeSourcePath => $themeViewPath,
        ], ['views', 'ai-module-theme-views']);
    
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'ai');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [AIService::class, DeepSeekService::class];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/ai')) {
                $paths[] = $path . '/modules/ai';
            }
        }

        return $paths;
    }
}