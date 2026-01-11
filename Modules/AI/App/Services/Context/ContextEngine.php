<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Modules\AI\App\Services\Templates\SmartTemplateEngine as TemplateEngine;
use Modules\AI\App\Services\Templates\ConfigBasedTemplateRepository;

/**
 * Context Engine - Ana Context Yöneticisi
 * Tüm context collector'ları yönetir ve birleşik context oluşturur
 */
readonly class ContextEngine
{
    private UserContextCollector $userCollector;
    private TenantContextCollector $tenantCollector;
    private PageContextCollector $pageCollector;
    private TemplateEngine $templateEngine;
    public function __construct(
        ?TemplateEngine $templateEngine = null
    ) {
        $this->userCollector = new UserContextCollector();
        $this->tenantCollector = new TenantContextCollector();
        $this->pageCollector = new PageContextCollector();
        $this->templateEngine = $templateEngine ?? new TemplateEngine(new ConfigBasedTemplateRepository());
    }

    /**
     * Tam context oluştur - tüm collector'lardan veri topla
     */
    public function buildFullContext(array $options = []): array
    {
        $startTime = microtime(true);
        
        // Mode belirleme (chat vs feature)
        $mode = $this->detectMode($options);
        
        // Context'leri paralel olarak topla
        $contexts = $this->collectAllContexts($options);
        
        // Priority'ye göre sırala
        $sortedContexts = $this->sortByPriority($contexts);
        
        // Birleşik context oluştur
        $unifiedContext = $this->unifyContexts($sortedContexts, $mode);
        
        // Performance metrics ekle
        $unifiedContext['performance'] = [
            'collection_time' => round((microtime(true) - $startTime) * 1000, 2),
            'contexts_collected' => count($contexts),
            'mode' => $mode,
            'total_size' => strlen(json_encode($unifiedContext))
        ];

        Log::debug('Full context built', [
            'mode' => $mode,
            'contexts_count' => count($contexts),
            'collection_time_ms' => $unifiedContext['performance']['collection_time'],
            'final_size' => $unifiedContext['performance']['total_size']
        ]);

        return $unifiedContext;
    }

    /**
     * Hızlı context oluştur - sadece kritik bilgiler
     */
    public function buildQuickContext(array $options = []): string
    {
        $mode = $this->detectMode($options);
        
        // Quick mode için sadece yüksek priority context'leri al
        $quickOptions = array_merge($options, ['quick_mode' => true]);
        
        $contexts = [];
        
        // User context (her zaman dahil)
        $userContext = $this->userCollector->collect($quickOptions);
        if ($userContext['priority'] <= 2) {
            $contexts['user'] = $userContext;
        }
        
        // Tenant context (feature mode'da kritik)
        if ($mode === 'feature') {
            $tenantContext = $this->tenantCollector->collect($quickOptions);
            if ($tenantContext['priority'] <= 2) {
                $contexts['tenant'] = $tenantContext;
            }
        }
        
        // Context text'lerini birleştir
        $contextTexts = [];
        foreach ($contexts as $name => $context) {
            if (!empty($context['context_text'])) {
                $contextTexts[] = $context['context_text'];
            }
        }
        
        return implode("\n\n", $contextTexts);
    }

    /**
     * Mode'a göre özelleştirilmiş context
     */
    public function buildContextForMode(string $mode, array $options = []): string
    {
        $options['mode'] = $mode;
        
        switch ($mode) {
            case 'chat':
                return $this->buildChatContext($options);
                
            case 'feature':
                return $this->buildFeatureContext($options);
                
            case 'page':
                return $this->buildPageContext($options);
                
            default:
                return $this->buildQuickContext($options);
        }
    }

    /**
     * Chat mode için özelleştirilmiş context
     */
    private function buildChatContext(array $options): string
    {
        $contexts = [];
        
        // ÖNCE AI IDENTITY - En kritik!
        $tenantContext = $this->tenantCollector->collect($options);
        if ($tenantContext['type'] === 'complete_profile' && isset($tenantContext['company']['name'])) {
            // AI kimlik tanımı - İLK SIRADA
            $contexts[] = "🤖 AI IDENTITY: Sen " . $tenantContext['company']['name'] . " şirketinin yapay zeka modelisin.";
            
            // Şirket kurucusu bilgisi
            if (isset($tenantContext['company']['founder'])) {
                $contexts[] = "👨‍💼 COMPANY FOUNDER: " . $tenantContext['company']['founder'];
            }
        }
        
        // SONRA User context (kime hitap ettiği bilgisi)
        $userContext = $this->userCollector->collect($options);
        $contexts[] = $userContext['context_text'];
        
        return implode("\n\n", $contexts);
    }

    /**
     * Feature mode için özelleştirilmiş context (YENİ ENTEGRE SİSTEM)
     */
    private function buildFeatureContext(array $options): string
    {
        $contexts = [];
        
        // Fallback: Basit sistem (FeatureTypeManager kaldırıldı)
        $tenantContext = $this->tenantCollector->collectWithMode('normal');
        if (!empty($tenantContext['context_text'])) {
            $contexts[] = $tenantContext['context_text'];
        }
        
        // 3. User context (arka plan)
        $userContext = $this->userCollector->collect($options);
        if ($userContext['has_user']) {
            $contexts[] = "👤 USER: " . $userContext['name'] . " tarafından kullanılıyor.";
        }
        
        // 4. Page context (varsa)
        if (isset($options['page_id']) || isset($options['url'])) {
            $pageContext = $this->pageCollector->collect($options);
            if ($pageContext['has_page']) {
                $contexts[] = $pageContext['context_text'];
            }
        }
        
        return implode("\n\n", $contexts);
    }
    
    /**
     * Template context'i hazırla - TemplateEngine için
     */
    private function buildTemplateContext(array $options): array
    {
        $context = [];
        
        // Tenant bilgileri
        if ($tenant = tenant()) {
            $context['tenant_name'] = $tenant->id;
            
            // AI Tenant Profile'dan bilgileri al
            $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenant->id)->first();
            if ($profile && $profile->data) {
                $data = $profile->data;
                $context['company_name'] = $data['company_name'] ?? '';
                $context['sector'] = $data['sector'] ?? '';
            }
        }
        
        // User bilgileri
        if ($user = auth()->user()) {
            $context['user_name'] = $user->name;
        }
        
        // Feature bilgileri
        if (isset($options['feature'])) {
            $feature = $options['feature'];
            $context['feature_name'] = $feature->name;
        }
        
        return $context;
    }

    /**
     * Page mode için özelleştirilmiş context
     */
    private function buildPageContext(array $options): string
    {
        $contexts = [];
        
        // Page context (en önemli)
        $pageContext = $this->pageCollector->collect($options);
        if (!empty($pageContext['context_text'])) {
            $contexts[] = $pageContext['context_text'];
        }
        
        // Tenant context
        $tenantContext = $this->tenantCollector->collectWithMode('minimal');
        if ($tenantContext['type'] === 'complete_profile') {
            $contexts[] = $tenantContext['context_text'];
        }
        
        // User context (minimal)
        $userContext = $this->userCollector->collect($options);
        if ($userContext['has_user']) {
            $contexts[] = "👤 USER: " . $userContext['name'];
        }
        
        return implode("\n\n", $contexts);
    }

    /**
     * Tüm context'leri topla
     */
    private function collectAllContexts(array $options): array
    {
        return [
            'user' => $this->userCollector->collect($options),
            'tenant' => $this->tenantCollector->collect($options),
            'page' => $this->pageCollector->collect($options)
        ];
    }

    /**
     * Context'leri priority'ye göre sırala
     */
    private function sortByPriority(array $contexts): Collection
    {
        return collect($contexts)
            ->sortBy(function ($context) {
                return $context['priority'] ?? 5;
            })
            ->filter(function ($context) {
                return !empty($context['context_text']);
            });
    }

    /**
     * Context'leri birleştir
     */
    private function unifyContexts(Collection $sortedContexts, string $mode): array
    {
        $unified = [
            'mode' => $mode,
            'contexts' => $sortedContexts->toArray(),
            'context_text' => '',
            'priorities' => [],
            'metadata' => []
        ];

        // Context text'leri birleştir
        $contextTexts = [];
        $priorities = [];
        
        foreach ($sortedContexts as $name => $context) {
            $contextTexts[] = $context['context_text'];
            $priorities[$name] = $context['priority'];
            
            // Metadata topla
            $unified['metadata'][$name] = [
                'type' => $context['type'] ?? 'unknown',
                'priority' => $context['priority'] ?? 5,
                'size' => strlen($context['context_text']),
                'collected_at' => $context['collected_at'] ?? now()
            ];
        }

        $unified['context_text'] = implode("\n\n", $contextTexts);
        $unified['priorities'] = $priorities;

        return $unified;
    }

    /**
     * Mode algılama
     */
    private function detectMode(array $options): string
    {
        // Explicit mode
        if (isset($options['mode'])) {
            return $options['mode'];
        }

        // Feature ID varsa feature mode
        if (isset($options['feature_id'])) {
            return 'feature';
        }

        // Page bilgisi varsa page mode
        if (isset($options['page_id']) || isset($options['url'])) {
            return 'page';
        }

        // Request path'den algıla
        $currentPath = request()->path();
        if (str_contains($currentPath, 'admin/ai/chat')) {
            return 'chat';
        }

        // Default
        return 'chat';
    }

    /**
     * Context cache'ini temizle - Entegre sistem ile
     */
    public function clearAllCache(): void
    {
        $this->userCollector->clearCache();
        $this->tenantCollector->clearCache();
        $this->pageCollector->clearCache();
        
        // Template cache'lerini de temizle
        $this->templateEngine->clearTemplateCache();
        
        // Global context cache'leri de temizle
        $tenantId = tenant('id') ?? 'default';
        $pattern = "context_*_{$tenantId}_*";
        
        try {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear context cache pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
        
        Log::info('All context cache cleared (integrated system)', [
            'tenant_id' => $tenantId,
            'cleared_components' => ['collectors', 'templates', 'feature_types', 'global_cache']
        ]);
    }

    /**
     * Context collector'ları al
     */
    public function getCollectors(): array
    {
        return [
            'user' => $this->userCollector,
            'tenant' => $this->tenantCollector,
            'page' => $this->pageCollector
        ];
    }

    /**
     * Belirli collector'ı al
     */
    public function getCollector(string $type): ?ContextCollector
    {
        return match($type) {
            'user' => $this->userCollector,
            'tenant' => $this->tenantCollector,
            'page' => $this->pageCollector,
            default => null
        };
    }

    /**
     * Context health check
     */
    public function healthCheck(): array
    {
        $health = [
            'status' => 'healthy',
            'collectors' => [],
            'cache_status' => 'unknown',
            'performance' => []
        ];

        // Collector'ları test et
        foreach (['user', 'tenant', 'page'] as $collectorName) {
            try {
                $collector = $this->getCollector($collectorName);
                $startTime = microtime(true);
                $context = $collector->collect(['health_check' => true]);
                $responseTime = (microtime(true) - $startTime) * 1000;
                
                $health['collectors'][$collectorName] = [
                    'status' => 'healthy',
                    'response_time_ms' => round($responseTime, 2),
                    'context_size' => strlen($context['context_text'] ?? ''),
                    'priority' => $context['priority'] ?? 5
                ];
            } catch (\Exception $e) {
                $health['collectors'][$collectorName] = [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
                $health['status'] = 'degraded';
            }
        }

        // Cache status
        try {
            Cache::put('context_health_test', 'ok', 10);
            $health['cache_status'] = Cache::get('context_health_test') === 'ok' ? 'healthy' : 'error';
            Cache::forget('context_health_test');
        } catch (\Exception $e) {
            $health['cache_status'] = 'error';
        }

        // Performance check
        $startTime = microtime(true);
        $this->buildQuickContext(['health_check' => true]);
        $health['performance']['quick_context_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        return $health;
    }

    /**
     * Context istatistikleri - Yeni infrastructure ile
     */
    public function getStatistics(): array
    {
        $tenantId = tenant('id') ?? 'default';
        
        try {
            $cacheKeys = Cache::getRedis()->keys("context_*_{$tenantId}_*");
            $cacheCount = count($cacheKeys);
            
            // Cache size hesaplama (yaklaşık)
            $totalCacheSize = 0;
            foreach (array_slice($cacheKeys, 0, 10) as $key) { // Sample 10 keys
                try {
                    $value = Cache::get($key);
                    if ($value) {
                        $totalCacheSize += strlen(json_encode($value));
                    }
                } catch (\Exception $e) {
                    // Ignore individual cache errors
                }
            }
            
            return [
                'tenant_id' => $tenantId,
                'cache_entries' => $cacheCount,
                'estimated_cache_size_bytes' => $totalCacheSize * ($cacheCount / 10),
                'collectors' => [
                    'user' => get_class($this->userCollector),
                    'tenant' => get_class($this->tenantCollector),
                    'page' => get_class($this->pageCollector)
                ],
                'template_stats' => $this->templateEngine->getTemplateStats(),
                // 'feature_type_stats' kaldırıldı
                'generated_at' => now()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to generate statistics',
                'message' => $e->getMessage(),
                'tenant_id' => $tenantId
            ];
        }
    }
}