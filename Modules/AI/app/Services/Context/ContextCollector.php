<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Context Collector Base Class
 * Tüm context collector'ların base sınıfı
 */
abstract class ContextCollector
{
    protected string $collectorName;
    protected int $cacheTtl = 1800; // 30 dakika
    protected bool $cacheEnabled = true;

    public function __construct(string $collectorName)
    {
        $this->collectorName = $collectorName;
    }

    /**
     * Ana context collection metodu
     */
    public function collect(array $options = []): array
    {
        $cacheKey = $this->generateCacheKey($options);
        
        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            Log::debug("Context cache hit for {$this->collectorName}", [
                'cache_key' => $cacheKey
            ]);
            return Cache::get($cacheKey);
        }

        $context = $this->collectContext($options);
        $enrichedContext = $this->enrichContext($context, $options);
        
        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $enrichedContext, now()->addSeconds($this->cacheTtl));
            Log::debug("Context cached for {$this->collectorName}", [
                'cache_key' => $cacheKey,
                'context_size' => strlen(json_encode($enrichedContext))
            ]);
        }

        return $enrichedContext;
    }

    /**
     * Alt sınıflar tarafından implement edilecek
     */
    abstract protected function collectContext(array $options): array;

    /**
     * Context'i zenginleştir (opsiyonel override)
     */
    protected function enrichContext(array $context, array $options): array
    {
        return array_merge($context, [
            'collected_at' => now()->toISOString(),
            'collector' => $this->collectorName,
            'options' => $options
        ]);
    }

    /**
     * Cache anahtarı oluştur
     */
    protected function generateCacheKey(array $options): string
    {
        $tenant_id = tenant('id') ?? 'default';
        $user_id = auth()->id() ?? 'guest';
        $options_hash = md5(serialize($options));
        
        return "context_{$this->collectorName}_{$tenant_id}_{$user_id}_{$options_hash}";
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(array $options = []): void
    {
        $cacheKey = $this->generateCacheKey($options);
        Cache::forget($cacheKey);
        
        Log::debug("Context cache cleared for {$this->collectorName}", [
            'cache_key' => $cacheKey
        ]);
    }

    /**
     * Cache ayarlarını güncelle
     */
    public function setCacheSettings(int $ttl, bool $enabled = true): self
    {
        $this->cacheTtl = $ttl;
        $this->cacheEnabled = $enabled;
        return $this;
    }

    /**
     * Context'in öncelik skorunu hesapla
     */
    protected function calculatePriority(array $context): int
    {
        // Base priority calculation - override edilebilir
        $priority = 5; // Normal priority
        
        if (isset($context['is_critical']) && $context['is_critical']) {
            $priority = 1; // High priority
        }
        
        if (isset($context['data_completeness']) && $context['data_completeness'] > 0.8) {
            $priority -= 1; // Boost priority for complete data
        }
        
        return max(1, min(5, $priority));
    }

    /**
     * Context validation
     */
    protected function validateContext(array $context): bool
    {
        // Base validation - override edilebilir
        return !empty($context) && is_array($context);
    }
}