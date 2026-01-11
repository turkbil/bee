<?php

namespace Modules\AI\App\Services\Workflow;

use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\Cache\TenantAwareCacheService;
use Modules\AI\App\Services\Workflow\NodeExecutor;
use Modules\AI\App\Services\Workflow\ParallelNodeExecutor;

/**
 * Flow Executor
 *
 * Ana workflow execution engine
 * - Tenant-aware cache
 * - Paralel execution
 * - Async jobs
 * - Streaming support
 */
class FlowExecutor
{
    protected $nodeExecutor;
    protected $parallelExecutor;
    protected $cacheService;

    public function __construct(
        NodeExecutor $nodeExecutor,
        ParallelNodeExecutor $parallelExecutor,
        TenantAwareCacheService $cacheService
    ) {
        $this->nodeExecutor = $nodeExecutor;
        $this->parallelExecutor = $parallelExecutor;
        $this->cacheService = $cacheService;
    }

    /**
     * Flow'u çalıştır
     *
     * @param array $flowData Flow definition
     * @param array $initialContext Initial context
     * @return array Final result
     */
    public function execute(array $flowData, array $initialContext): array
    {
        Log::info('🚀 Flow execution started', [
            'flow_id' => $flowData['id'] ?? 'unknown',
            'tenant_id' => tenant('id')
        ]);

        $startTime = microtime(true);

        // Prepare context
        $context = array_merge($initialContext, [
            'flow' => $flowData
        ]);

        // Discover parallel groups
        $parallelGroups = $this->parallelExecutor->discoverParallelGroups($flowData);

        // Execute flow
        $result = $this->executeFlow($flowData, $context, $parallelGroups);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('✅ Flow execution completed', [
            'duration_ms' => $duration,
            'parallel_groups' => count($parallelGroups)
        ]);

        // Extract final response for controller
        $result['final_response'] = $result['ai_response'] ?? '';

        return $result;
    }

    /**
     * Flow'u çalıştır (node by node)
     */
    protected function executeFlow(array $flowData, array $context, array $parallelGroups): array
    {
        $nodes = $flowData['nodes'] ?? [];
        $currentNodeId = $flowData['start_node'] ?? $nodes[0]['id'] ?? null;
        $visitedNodes = [];

        while ($currentNodeId && !in_array($currentNodeId, $visitedNodes)) {
            $visitedNodes[] = $currentNodeId;

            // Check if node is part of parallel group
            $parallelGroup = $this->findParallelGroup($currentNodeId, $parallelGroups);

            if ($parallelGroup) {
                // Execute parallel group
                $results = $this->parallelExecutor->executeParallelGroup($parallelGroup, $context);

                // Merge results
                foreach ($results as $nodeId => $result) {
                    $context = array_merge($context, $result);
                }

                // Skip to join node
                $currentNodeId = $parallelGroup['join_at'];

                // Mark parallel nodes as visited
                foreach ($parallelGroup['nodes'] as $nodeId) {
                    $visitedNodes[] = $nodeId;
                }
            } else {
                // Execute single node
                $result = $this->nodeExecutor->execute($currentNodeId, $context);

                // Merge result into context
                $context = array_merge($context, $result);

                // Determine next node
                $currentNodeId = $result['next_node'] ?? $this->getNextNode($currentNodeId, $flowData);
            }

            // Check if end node
            if ($this->isEndNode($currentNodeId, $flowData)) {
                break;
            }
        }

        return $context;
    }

    /**
     * Node'un paralel grup içinde olup olmadığını kontrol et
     */
    protected function findParallelGroup(string $nodeId, array $parallelGroups): ?array
    {
        foreach ($parallelGroups as $group) {
            if (in_array($nodeId, $group['nodes'])) {
                return $group;
            }
        }

        return null;
    }

    /**
     * Bir sonraki node'u bul
     */
    protected function getNextNode(string $currentNodeId, array $flowData): ?string
    {
        $edges = $flowData['edges'] ?? [];

        foreach ($edges as $edge) {
            // Support both formats: from/to and source/target
            $from = $edge['from'] ?? $edge['source'] ?? null;
            $to = $edge['to'] ?? $edge['target'] ?? null;

            if ($from === $currentNodeId) {
                return $to;
            }
        }

        return null;
    }

    /**
     * End node kontrolü
     */
    protected function isEndNode(string $nodeId, array $flowData): bool
    {
        $nodes = $flowData['nodes'] ?? [];

        foreach ($nodes as $node) {
            if ($node['id'] === $nodeId && $node['type'] === 'end') {
                return true;
            }
        }

        return false;
    }
}
