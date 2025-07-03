<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\AIServiceManager;
use App\Services\AI\Providers\DeepSeekProvider;
use App\Services\AI\Token\GlobalTokenManager;
use App\Services\AI\Integration\PageAIIntegration;
use App\Contracts\AI\AIProviderInterface;
use App\Contracts\AI\TokenManagerInterface;
use Illuminate\Support\Facades\Log;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Contracts'ları bind et
        $this->app->bind(AIProviderInterface::class, DeepSeekProvider::class);
        $this->app->bind(TokenManagerInterface::class, GlobalTokenManager::class);

        // AIServiceManager'ı singleton olarak kaydet
        $this->app->singleton(AIServiceManager::class, function ($app) {
            return new AIServiceManager(
                $app->make(AIProviderInterface::class),
                $app->make(TokenManagerInterface::class)
            );
        });

        // AI entegrasyonlarını kaydet
        $this->registerAIIntegrations();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/../../config/ai.php' => config_path('ai.php'),
        ], 'ai-config');

        // AI Service Provider başlatıldı
    }

    /**
     * AI entegrasyonlarını kaydet
     */
    protected function registerAIIntegrations(): void
    {
        $this->app->singleton('ai.integrations', function ($app) {
            $manager = $app->make(AIServiceManager::class);
            
            // Page modülü entegrasyonunu kaydet
            if (class_exists('Modules\Page\App\Models\Page')) {
                $pageIntegration = new PageAIIntegration($manager);
                $manager->registerIntegration('page', $pageIntegration);
                
                // Page AI entegrasyonu kaydedildi
            }

            // Diğer modül entegrasyonları buraya eklenebilir
            // Portfolio, Studio, Announcement vs.

            return $manager;
        });
    }

    /**
     * Sağlanan servisler
     */
    public function provides(): array
    {
        return [
            AIServiceManager::class,
            AIProviderInterface::class,
            TokenManagerInterface::class,
            'ai.integrations'
        ];
    }
}