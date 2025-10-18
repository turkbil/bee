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
     * Tenant provider/model seçimi - YENI SİSTEM
     * Tenant'ın kendi seçimi varsa onu kullan, yoksa central default'u kullan
     */
    public function getTenantProvider($tenantId = null)
    {
        // Tenant ID'yi belirle
        if (!$tenantId) {
            $tenantId = tenant('id') ?: session('admin_tenant_id');
        }
        
        // 1. Tenant'ın kendi seçimi var mı?
        if ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if ($tenant && $tenant->default_ai_provider_id) {
                $tenantProvider = $this->providers->where('id', $tenant->default_ai_provider_id)->first();
                if ($tenantProvider && $tenantProvider->isAvailable()) {
                    Log::debug("🎯 Tenant provider selected", [
                        'tenant_id' => $tenantId,
                        'provider' => $tenantProvider->name,
                        'provider_id' => $tenantProvider->id
                    ]);
                    return $tenantProvider;
                }
            }
        }

        // 2. Central default provider kullan
        $defaultProvider = $this->providers->where('is_default', true)->first();
        if ($defaultProvider && $defaultProvider->isAvailable()) {
            Log::debug("🔧 Central default provider selected", [
                'provider' => $defaultProvider->name,
                'is_default' => true,
                'tenant_id' => $tenantId
            ]);
            return $defaultProvider;
        }
        
        throw new \Exception("No available AI provider found for tenant: " . ($tenantId ?: 'none'));
    }

    /**
     * Tenant model seçimi - YENI SİSTEM  
     */
    public function getTenantModel($tenantId = null)
    {
        if (!$tenantId) {
            $tenantId = tenant('id') ?: session('admin_tenant_id');
        }
        
        // 1. Tenant'ın kendi model seçimi var mı?
        if ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if ($tenant && $tenant->default_ai_model) {
                Log::debug("🎯 Tenant model selected", [
                    'tenant_id' => $tenantId,
                    'model' => $tenant->default_ai_model
                ]);
                return $tenant->default_ai_model;
            }
        }

        // 2. Provider'ın default model'ini kullan
        $provider = $this->getTenantProvider($tenantId);
        $defaultModel = $provider->default_model ?? 'gpt-3.5-turbo';

        Log::debug("🔧 Default model selected", [
            'model' => $defaultModel,
            'provider' => $provider->name,
            'tenant_id' => $tenantId
        ]);

        return $defaultModel;
    }

    /**
     * Provider + Model birlikte getir - YENI SİSTEM
     */
    public function getTenantProviderWithModel($tenantId = null)
    {
        $provider = $this->getTenantProvider($tenantId);
        $model = $this->getTenantModel($tenantId);
        
        return [
            'provider' => $provider,
            'model' => $model,
            'service' => $provider->getServiceInstance()
        ];
    }

    /**
     * Central defaults getir
     */
    public function getCentralDefaults()
    {
        $defaultProvider = $this->providers->where('is_default', true)->first();
        
        return [
            'provider' => $defaultProvider,
            'model' => $defaultProvider?->default_model ?? 'gpt-3.5-turbo'
        ];
    }

    /**
     * Provider service without failover - STRICT MODE
     * Sadece varsayılan provider'ı dener, başarısız olursa exception fırlatır
     */
    public function getProviderServiceWithoutFailover()
    {
        $provider = $this->getDefaultProvider();

        if (!$provider) {
            throw new \Exception("No default AI provider configured");
        }

        if (!$provider->isAvailable()) {
            throw new \Exception("Default AI provider is not available: " . $provider->name);
        }

        $service = $provider->getServiceInstance();

        Log::debug("🔥 Strict provider selected (no failover)", [
            'provider' => $provider->name,
            'model' => $provider->default_model,
            'priority' => $provider->priority
        ]);

        return ['provider' => $provider, 'service' => $service];
    }

    /**
     * Get fallback provider (next available provider)
     *
     * @param string|null $excludeProviderName Current provider to exclude
     * @return array|null ['provider' => AIProvider, 'service' => ServiceInstance, 'model' => string]
     */
    public function getFallbackProvider($excludeProviderName = null)
    {
        // Aktif provider'ları priority sırasına göre al
        $activeProviders = $this->providers
            ->where('is_active', true)
            ->sortBy('priority');

        Log::info('🔍 Fallback provider aranıyor', [
            'exclude' => $excludeProviderName,
            'available_count' => $activeProviders->count()
        ]);

        foreach ($activeProviders as $provider) {
            // Mevcut provider'ı atla
            if ($excludeProviderName && $provider->name === $excludeProviderName) {
                continue;
            }

            // Provider kullanılabilir mi?
            if ($provider->isAvailable()) {
                try {
                    $service = $provider->getServiceInstance();

                    Log::info('✅ Fallback provider bulundu', [
                        'provider' => $provider->name,
                        'model' => $provider->default_model,
                        'priority' => $provider->priority
                    ]);

                    return [
                        'provider' => $provider,
                        'service' => $service,
                        'model' => $provider->default_model,
                        'success' => true
                    ];
                } catch (\Exception $e) {
                    Log::warning('⚠️ Fallback provider initialization failed', [
                        'provider' => $provider->name,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }

        Log::error('❌ Fallback provider bulunamadı');
        return null;
    }
}