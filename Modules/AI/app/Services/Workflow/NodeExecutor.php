<?php

namespace Modules\AI\App\Services\Workflow;

use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\Cache\TenantAwareCacheService;
use Modules\AI\App\Services\Workflow\Nodes\NodeFactory;

/**
 * Node Executor
 *
 * Generic node execution engine
 * Tenant-aware, cache-enabled
 */
class NodeExecutor
{
    protected $cacheService;
    protected $nodeFactory;

    public function __construct(
        TenantAwareCacheService $cacheService,
        NodeFactory $nodeFactory
    ) {
        $this->cacheService = $cacheService;
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * Node çalıştır (cache-aware)
     */
    public function execute(string $nodeId, array $context): array
    {
        // Find node in array (nodes are indexed numerically, not by ID)
        $nodeData = null;
        foreach ($context['flow']['nodes'] ?? [] as $node) {
            if ($node['id'] === $nodeId) {
                $nodeData = $node;
                break;
            }
        }

        if (!$nodeData) {
            throw new \Exception("Node not found: {$nodeId}");
        }

        $nodeType = $nodeData['type'];
        $config = $nodeData['config'] ?? [];

        Log::info("🚀 Executing node: {$nodeId}", [
            'type' => $nodeType
        ]);

        $startTime = microtime(true);

        // Cache strategy'yi kontrol et
        $cacheStrategy = $this->getCacheStrategy($nodeType, $config, $context);

        if ($cacheStrategy['enabled']) {
            $result = $this->cacheService->remember(
                $nodeType,
                $cacheStrategy['key_params'],
                $cacheStrategy['ttl'],
                fn() => $this->executeNode($nodeData, $context)
            );
        } else {
            $result = $this->executeNode($nodeData, $context);
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info("✅ Node completed: {$nodeId}", [
            'duration_ms' => $duration,
            'cached' => $cacheStrategy['enabled']
        ]);

        // Metrics kaydet (temporarily disabled - table doesn't exist yet)
        // $this->recordMetrics($nodeId, $nodeType, $duration, true);

        return $result;
    }

    /**
     * Node'u gerçekten çalıştır
     */
    protected function executeNode(array $nodeData, array $context): array
    {
        $node = $this->nodeFactory->make($nodeData['type'], $nodeData['config']);

        return $node->execute($context);
    }

    /**
     * Cache strategy'yi belirle
     */
    protected function getCacheStrategy(string $nodeType, array $config, array $context): array
    {
        // Flow-level cache strategy
        $flowCacheStrategy = $context['flow']['cache_strategy'][$nodeType] ?? null;

        if ($flowCacheStrategy) {
            return [
                'enabled' => $flowCacheStrategy['enabled'] ?? false,
                'ttl' => $flowCacheStrategy['ttl'] ?? 300,
                'key_params' => $this->extractKeyParams($context, $flowCacheStrategy['key_fields'] ?? [])
            ];
        }

        // Default strategy (node type based)
        return $this->getDefaultCacheStrategy($nodeType, $context);
    }

    /**
     * Default cache strategy
     */
    protected function getDefaultCacheStrategy(string $nodeType, array $context): array
    {
        $strategies = [
            // ❌ ASLA cache'lenmeyecek node'lar
            'ai_response' => ['enabled' => false],
            'sentiment_detection' => ['enabled' => false],
            'context_builder' => ['enabled' => false],
            'message_saver' => ['enabled' => false],

            // ✅ Cache'lenecek node'lar
            'product_search' => [
                'enabled' => true,
                'ttl' => 300,
                'key_params' => ['query', 'category']
            ],
            'category_detection' => [
                'enabled' => true,
                'ttl' => 600,
                'key_params' => ['message']
            ],
            'history_loader' => [
                'enabled' => true,
                'ttl' => 60,
                'key_params' => ['session_id']
            ],
            'price_query' => [
                'enabled' => true,
                'ttl' => 300,
                'key_params' => ['product_ids']
            ],
            'stock_sorter' => [
                'enabled' => true,
                'ttl' => 120,
                'key_params' => ['product_ids']
            ],
            'link_generator' => [
                'enabled' => true,
                'ttl' => 3600,
                'key_params' => ['product_ids']
            ]
        ];

        $strategy = $strategies[$nodeType] ?? ['enabled' => false];

        if ($strategy['enabled'] && isset($strategy['key_params'])) {
            $strategy['key_params'] = $this->extractKeyParams($context, $strategy['key_params']);
        }

        return $strategy;
    }

    /**
     * Context'ten key parametreleri çıkar
     */
    protected function extractKeyParams(array $context, array $fields): array
    {
        $params = [];

        foreach ($fields as $field) {
            $params[$field] = data_get($context, $field, 'null');
        }

        return $params;
    }

    /**
     * Metrics kaydet
     */
    protected function recordMetrics(string $nodeId, string $nodeType, float $duration, bool $success): void
    {
        \DB::table('tenant_flow_metrics')->insert([
            'tenant_id' => tenant('id') ?? 1,
            'node_id' => $nodeId,
            'node_type' => $nodeType,
            'duration_ms' => $duration,
            'success' => $success,
            'created_at' => now()
        ]);
    }
}
