<?php

namespace Modules\AI\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\AI\App\Http\Livewire\Admin\Chat\ChatPanel;
use Modules\AI\App\Http\Livewire\Admin\Settings\SettingsPanel;
use Modules\AI\App\Http\Livewire\Admin\Settings\Modals\PromptEditModal;
use Modules\AI\App\Http\Livewire\Admin\Settings\Modals\PromptDeleteModal;
use Modules\AI\App\Http\Livewire\Admin\Tokens\TokenManagement;
use Modules\AI\App\Http\Livewire\Admin\Tokens\TokenPackageManagement;
use Modules\AI\App\Http\Livewire\Admin\Features\AIFeaturesDashboard;
use Modules\AI\App\Http\Livewire\Admin\Features\AIExamples;
use Modules\AI\App\Http\Livewire\Admin\Features\AITestPanel;
use Modules\AI\App\Http\Livewire\Admin\Features\AIFeaturesManagement;
use Modules\AI\App\Http\Livewire\Admin\Features\AIFeatureManageComponent;
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

        // View'ları yükle (sadece admin) - Routes RouteServiceProvider'da yükleniyor
        $this->loadViewsFrom(module_path('AI', 'resources/views'), 'ai');

        // Livewire bileşenlerini kaydet - ai::admin namespace ile
        Livewire::component('ai::admin.chat-panel', ChatPanel::class);
        Livewire::component('ai::admin.settings-panel', SettingsPanel::class);
        Livewire::component('ai::admin.modals.prompt-edit-modal', PromptEditModal::class);
        Livewire::component('ai::admin.modals.prompt-delete-modal', PromptDeleteModal::class);
        Livewire::component('ai::admin.token-management', TokenManagement::class);
        Livewire::component('ai::admin.token-package-management', TokenPackageManagement::class);
        Livewire::component('ai::admin.ai-features-dashboard', AIFeaturesDashboard::class);
        Livewire::component('ai::admin.ai-examples', AIExamples::class);
        Livewire::component('ai::admin.ai-test-panel', AITestPanel::class);
        Livewire::component('ai::admin.ai-features-management', AIFeaturesManagement::class);
        Livewire::component('ai::admin.ai-feature-manage-component', AIFeatureManageComponent::class);
        
        // Credit Warning System - Livewire Component
        Livewire::component('ai::admin.credit-warning-component', \Modules\AI\App\Http\Livewire\Admin\CreditWarningComponent::class);
        Livewire::component('ai::admin.ai-profile-management', \Modules\AI\App\Http\Livewire\Admin\Profile\AIProfileManagement::class);
        Livewire::component('ai::admin.ai-profile-wizard-step', \Modules\AI\App\Http\Livewire\Admin\Profile\AIProfileWizardStep::class);
        
        // Universal Input System V3 - Livewire Component
        Livewire::component('ai::admin.features.universal-input-component', \Modules\AI\App\Http\Livewire\Admin\Features\UniversalInputComponent::class);
        
        // Eski kayıtlar da korunacak (backward compatibility)
        Livewire::component('chat-panel', ChatPanel::class);
        Livewire::component('settings-panel', SettingsPanel::class);
        Livewire::component('modals.prompt-edit-modal', PromptEditModal::class);
        Livewire::component('modals.prompt-delete-modal', PromptDeleteModal::class);
        Livewire::component('token-management', TokenManagement::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        
        // PERFORMANCE: Single AI module active check for both services
        $aiModuleActive = $this->isAIModuleActive();
        
        // AI Service singleton kaydı - Lazy yükleme yaklaşımı
        $this->app->singleton(AIService::class, function ($app) use ($aiModuleActive) {
            // Servisi talep üzerine başlat (lazy loading)
            $deepSeekService = $app->make(DeepSeekService::class);
            return new AIService($deepSeekService);
        });
        
        // DeepSeek Service singleton kaydı - Lazy yükleme yaklaşımı
        $this->app->singleton(DeepSeekService::class, function ($app) use ($aiModuleActive) {
            // Servisi talep üzerine başlat (lazy loading)
            return new DeepSeekService(!$aiModuleActive); // !$aiModuleActive = safe mode
        });
        
        // Enterprise Credit System Services
        $this->app->singleton(\Modules\AI\App\Services\CreditWarningService::class);
        $this->app->singleton(\Modules\AI\App\Services\ModelBasedCreditService::class);
        $this->app->singleton(\Modules\AI\App\Services\SilentFallbackService::class);
        $this->app->singleton(\Modules\AI\App\Services\CentralFallbackService::class);
        $this->app->singleton(\Modules\AI\App\Services\CreditCalculatorService::class);
        $this->app->singleton(\Modules\AI\App\Services\AIProviderManager::class);
    }
    
    /**
     * AI modülünün aktif olup olmadığını kontrol et
     * Bu yöntem, sadece AI modülü ile ilgili işlemlerde çağrılacak
     */
    private function isAIModuleActive(): bool
    {
        // PERFORMANCE: Static cache to prevent multiple calls per request
        static $cachedResult = null;
        if ($cachedResult !== null) {
            return $cachedResult;
        }
        
        // Eğer bir AI rotasındaysak veya AI modülü sayfasına erişiliyorsa
        $currentRoute = request()->route()?->getName() ?? '';
        $currentPath = request()->path();
        
        // AI modülü ile ilgili rotalar veya cPanel rotaları için kontrolü yap
        if (strpos($currentRoute, 'ai.') === 0 || 
            strpos($currentRoute, 'admin.ai.') === 0 ||
            strpos($currentPath, 'ai') === 0 ||
            strpos($currentPath, 'admin/ai') === 0) {
                
            // PERFORMANCE: Cache table existence check for 1 hour
            $cachedResult = cache()->remember('ai_settings_table_exists', 3600, function() {
                return Schema::hasTable('ai_settings');
            });
            return $cachedResult;
        }
        
        // Diğer tüm durumlar için güvenli modu kullan
        $cachedResult = false;
        return $cachedResult;
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
        
        // AI sadece admin modülü olduğu için tema klasörü yok
    
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