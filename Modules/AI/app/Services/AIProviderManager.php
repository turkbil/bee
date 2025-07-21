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
     * Provider'ları doğru sırada getir - 3 Aşamalı Sistem:
     * 1. Tenant'ın seçtiği provider (tenants.default_ai_provider_id)
     * 2. Sistem varsayılanı (ai_providers.is_default=1)  
     * 3. Fallback (priority sırasına göre)
     */
    public function getOrderedProviders($tenantId = null)
    {
        $orderedProviders = collect();
        
        // 1. AŞAMA: Tenant'ın seçtiği provider
        if ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if ($tenant && $tenant->default_ai_provider_id) {
                $tenantProvider = $this->providers->where('id', $tenant->default_ai_provider_id)->first();
                if ($tenantProvider) {
                    $orderedProviders->push($tenantProvider);
                    Log::info("🎯 Tenant provider seçildi", [
                        'tenant_id' => $tenantId,
                        'provider' => $tenantProvider->name,
                        'provider_id' => $tenantProvider->id
                    ]);
                }
            }
        }
        
        // 2. AŞAMA: Sistem varsayılanı (eğer tenant'ta yoksa)
        $defaultProvider = $this->providers->where('is_default', true)->first();
        if ($defaultProvider && !$orderedProviders->contains('id', $defaultProvider->id)) {
            $orderedProviders->push($defaultProvider);
            Log::info("🔧 Sistem varsayılan provider eklendi", [
                'provider' => $defaultProvider->name,
                'is_default' => true
            ]);
        }
        
        // 3. AŞAMA: Fallback (priority sırasına göre geri kalanlar)
        $fallbackProviders = $this->providers
            ->whereNotIn('id', $orderedProviders->pluck('id'))
            ->sortByDesc('priority');
            
        foreach ($fallbackProviders as $provider) {
            $orderedProviders->push($provider);
        }
        
        return $orderedProviders;
    }

    /**
     * Automatic failover - bir provider çalışmazsa diğerine geç
     * 3 Aşamalı Provider Seçimi ile
     */
    public function getProviderServiceWithFailover($preferredProvider = null, $tenantId = null)
    {
        // Tenant ID'yi al (session'dan veya parametre)
        if (!$tenantId && session('admin_tenant_id')) {
            $tenantId = session('admin_tenant_id');
        }
        
        // 3 aşamalı provider seçimi kullan
        $providers = $preferredProvider 
            ? $this->providers->where('name', $preferredProvider)->concat($this->getOrderedProviders($tenantId))
            : $this->getOrderedProviders($tenantId);

        foreach ($providers as $provider) {
            try {
                if ($provider->isAvailable()) {
                    $service = $provider->getServiceInstance();
                    
                    Log::info("AI Provider seçildi: {$provider->name}", [
                        'provider' => $provider->name,
                        'average_response_time' => $provider->average_response_time,
                        'priority' => $provider->priority,
                        'tenant_id' => $tenantId,
                        'selection_reason' => $preferredProvider ? 'preferred' : 'priority_order'
                    ]);
                    
                    return ['provider' => $provider, 'service' => $service];
                }
            } catch (\Exception $e) {
                Log::warning("AI Provider unavailable: {$provider->name}", [
                    'error' => $e->getMessage(),
                    'tenant_id' => $tenantId
                ]);
                continue;
            }
        }

        throw new \Exception("No available AI providers found for tenant: " . ($tenantId ?: 'none'));
    }
}