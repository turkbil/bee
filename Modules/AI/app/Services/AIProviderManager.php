<?php

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIProviderManager
{
    protected $defaultProvider;
    protected $providers;

    public function __construct()
    {
        $this->loadProviders();
    }

    /**
     * Provider'ları yükle
     */
    protected function loadProviders()
    {
        $this->providers = Cache::remember('ai_providers', 3600, function () {
            return AIProvider::getActive();
        });

        $this->defaultProvider = $this->providers->where('is_default', true)->first();
    }

    /**
     * Varsayılan provider'ı getir
     */
    public function getDefaultProvider()
    {
        return $this->defaultProvider;
    }

    /**
     * Provider'ı isim ile getir
     */
    public function getProvider($name)
    {
        return $this->providers->where('name', $name)->first();
    }

    /**
     * Aktif provider'ları getir
     */
    public function getActiveProviders()
    {
        return $this->providers;
    }

    /**
     * Provider'ın servis instance'ını getir
     */
    public function getProviderService($providerName = null)
    {
        $provider = $providerName ? $this->getProvider($providerName) : $this->getDefaultProvider();

        if (!$provider) {
            throw new \Exception("AI Provider not found: " . ($providerName ?? 'default'));
        }

        if (!$provider->isAvailable()) {
            throw new \Exception("AI Provider not available: " . $provider->name);
        }

        return $provider->getServiceInstance();
    }

    /**
     * Provider performansını güncelle
     */
    public function updateProviderPerformance($providerName, $responseTime)
    {
        $provider = $this->getProvider($providerName);
        if ($provider) {
            $provider->updatePerformance($responseTime);
            
            // Cache'i temizle
            Cache::forget('ai_providers');
            $this->loadProviders();
        }
    }

    /**
     * En hızlı provider'ı getir
     */
    public function getFastestProvider()
    {
        return $this->providers
            ->where('average_response_time', '>', 0)
            ->sortBy('average_response_time')
            ->first();
    }

    /**
     * Provider'ları öncelik sırasına göre getir
     */
    public function getProvidersByPriority()
    {
        return $this->providers
            ->sortByDesc('priority')
            ->values();
    }

    /**
     * Automatic failover - bir provider çalışmazsa diğerine geç
     */
    public function getProviderServiceWithFailover($preferredProvider = null)
    {
        $providers = $preferredProvider 
            ? $this->providers->where('name', $preferredProvider)->concat($this->getProvidersByPriority())
            : $this->getProvidersByPriority();

        foreach ($providers as $provider) {
            try {
                if ($provider->isAvailable()) {
                    $service = $provider->getServiceInstance();
                    
                    Log::info("AI Provider seçildi: {$provider->name}", [
                        'provider' => $provider->name,
                        'average_response_time' => $provider->average_response_time,
                        'priority' => $provider->priority
                    ]);
                    
                    return ['provider' => $provider, 'service' => $service];
                }
            } catch (\Exception $e) {
                Log::warning("AI Provider unavailable: {$provider->name}", [
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        throw new \Exception("No available AI providers found");
    }
}