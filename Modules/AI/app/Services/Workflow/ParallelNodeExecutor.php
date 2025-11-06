<?php

namespace Modules\AI\App\Services\Workflow;

use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Log;

/**
 * Parallel Node Executor
 *
 * Bağımsız node'ları paralel çalıştırır
 * Promise-based async execution
 */
class ParallelNodeExecutor
{
    protected $nodeExecutor;

    public function __construct(NodeExecutor $nodeExecutor)
    {
        $this->nodeExecutor = $nodeExecutor;
    }

    /**
     * Paralel grup çalıştır
     *
     * @param array $parallelGroup ['nodes' => ['node_2', 'node_3'], 'join_at' => 'node_5']
     * @param array $context Shared context
     * @return array Combined results
     */
    public function executeParallelGroup(array $parallelGroup, array $context): array
    {
        $nodes = $parallelGroup['nodes'] ?? [];
        $joinAt = $parallelGroup['join_at'] ?? null;

        if (empty($nodes)) {
            Log::warning('⚠️ Empty parallel group');
            return [];
        }

        Log::info('🚀 Starting parallel execution', [
            'nodes' => $nodes,
            'join_at' => $joinAt
        ]);

        $startTime = microtime(true);

        // Create promises for each node
        $promises = [];
        foreach ($nodes as $nodeId) {
            $promises[$nodeId] = $this->createNodePromise($nodeId, $context);
        }

        // Wait for all promises to complete
        $results = Promise\Utils::unwrap($promises);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('✅ Parallel execution completed', [
            'duration_ms' => $duration,
            'nodes_count' => count($nodes)
        ]);

        return $results;
    }

    /**
     * Node promise oluştur
     */
    protected function createNodePromise(string $nodeId, array $context): Promise\PromiseInterface
    {
        return new Promise\Promise(function () use ($nodeId, $context, &$resolve, &$reject) {
            try {
                $startTime = microtime(true);

                // Execute node
                $result = $this->nodeExecutor->execute($nodeId, $context);

                $duration = round((microtime(true) - $startTime) * 1000, 2);

                Log::info("✅ Node completed: {$nodeId}", [
                    'duration_ms' => $duration
                ]);

                resolve($result);
            } catch (\Exception $e) {
                Log::error("❌ Node failed: {$nodeId}", [
                    'error' => $e->getMessage()
                ]);

                reject($e);
            }
        });
    }

    /**
     * Paralel grupları flow metadata'dan oku
     */
    public function discoverParallelGroups($flowData): array
    {
        $parallelGroups = $flowData['parallel_groups'] ?? [];

        // Auto-detect independent nodes (gelişmiş algoritma)
        if (empty($parallelGroups)) {
            $parallelGroups = $this->autoDetectParallelGroups($flowData);
        }

        return $parallelGroups;
    }

    /**
     * Bağımsız node'ları otomatik tespit et
     */
    protected function autoDetectParallelGroups(array $flowData): array
    {
        $nodes = $flowData['nodes'] ?? [];
        $edges = $flowData['edges'] ?? [];

        $groups = [];
        $visited = [];

        foreach ($nodes as $node) {
            $nodeId = $node['id'];

            if (in_array($nodeId, $visited)) {
                continue;
            }

            // Bu node'un çıkışları (support both from/to and source/target)
            $outputs = array_filter($edges, fn($e) => ($e['from'] ?? $e['source'] ?? null) === $nodeId);

            // Bu node'un girişleri (support both from/to and source/target)
            $inputs = array_filter($edges, fn($e) => ($e['to'] ?? $e['target'] ?? null) === $nodeId);

            // Eğer aynı kaynaktan gelen başka node'lar varsa, paralel olabilir
            if (count($inputs) === 1) {
                $sourceNode = $inputs[0]['from'] ?? $inputs[0]['source'] ?? null;
                $siblings = array_filter($edges, fn($e) => ($e['from'] ?? $e['source'] ?? null) === $sourceNode && ($e['to'] ?? $e['target'] ?? null) !== $nodeId);

                if (count($siblings) > 0) {
                    $parallelNodes = array_map(fn($e) => $e['to'] ?? $e['target'] ?? null, $siblings);
                    $parallelNodes[] = $nodeId;

                    // Bu node'ların ortak hedefi bul (join point)
                    $joinAt = $this->findCommonTarget($parallelNodes, $edges);

                    if ($joinAt) {
                        $groups[] = [
                            'nodes' => $parallelNodes,
                            'join_at' => $joinAt
                        ];

                        $visited = array_merge($visited, $parallelNodes);
                    }
                }
            }
        }

        Log::info('🔍 Auto-detected parallel groups', [
            'groups_count' => count($groups),
            'groups' => $groups
        ]);

        return $groups;
    }

    /**
     * Node'ların ortak hedef node'unu bul
     */
    protected function findCommonTarget(array $nodeIds, array $edges): ?string
    {
        $targets = [];

        foreach ($nodeIds as $nodeId) {
            $nodeOutputs = array_filter($edges, fn($e) => ($e['from'] ?? $e['source'] ?? null) === $nodeId);
            foreach ($nodeOutputs as $output) {
                $targetId = $output['to'] ?? $output['target'] ?? null;
                if ($targetId) {
                    $targets[$targetId] = ($targets[$targetId] ?? 0) + 1;
                }
            }
        }

        // En çok referans alan target'ı döndür
        arsort($targets);
        $commonTarget = array_key_first($targets);

        return $commonTarget && $targets[$commonTarget] === count($nodeIds) ? $commonTarget : null;
    }
}
