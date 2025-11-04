<?php

namespace App\Services\ConversationNodes;

use App\Models\AIConversation;
use Illuminate\Support\Facades\Log;

/**
 * Node Executor Service
 *
 * Orchestrates node execution and manages node registry
 * Acts as the central hub for all node types
 */
class NodeExecutor
{
    /**
     * Registered node handlers
     * Maps node type to handler class
     *
     * @var array<string, string>
     */
    protected static array $nodeRegistry = [];

    /**
     * Whether the registry has been initialized
     */
    protected static bool $initialized = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!self::$initialized) {
            $this->initializeRegistry();
            self::$initialized = true;
        }
    }

    /**
     * Initialize node registry with default nodes
     */
    protected function initializeRegistry(): void
    {
        // Register common nodes (available to all tenants)
        self::register('ai_response', \App\Services\ConversationNodes\Common\AIResponseNode::class);
        self::register('condition', \App\Services\ConversationNodes\Common\ConditionNode::class);
        self::register('collect_data', \App\Services\ConversationNodes\Common\CollectDataNode::class);
        self::register('share_contact', \App\Services\ConversationNodes\Common\ShareContactNode::class);
        self::register('webhook', \App\Services\ConversationNodes\Common\WebhookNode::class);
        self::register('end', \App\Services\ConversationNodes\Common\EndNode::class);

        // Register tenant-specific nodes (İxtif.com - Tenant ID: 2)
        self::register('category_detection', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\CategoryDetectionNode::class);
        self::register('product_recommendation', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\ProductRecommendationNode::class);
        self::register('price_filter', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\PriceFilterNode::class);
        self::register('currency_convert', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\CurrencyConvertNode::class);
        self::register('stock_check', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\StockCheckNode::class);
        self::register('comparison', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\ComparisonNode::class);
        self::register('quotation', \App\Services\ConversationNodes\TenantSpecific\Tenant_2\QuotationNode::class);

        Log::info('Node registry initialized', [
            'total_nodes' => count(self::$nodeRegistry),
            'node_types' => array_keys(self::$nodeRegistry),
        ]);
    }

    /**
     * Execute a node
     *
     * @param array $nodeData Node configuration from flow
     * @param AIConversation $conversation Current conversation
     * @param string $userMessage User's message
     * @return array Execution result
     */
    public function execute(array $nodeData, AIConversation $conversation, string $userMessage): array
    {
        $startTime = microtime(true);

        try {
            // Validate node data
            if (!isset($nodeData['type'])) {
                throw new \Exception('Node type not specified');
            }

            // Get node handler class
            $handlerClass = $this->resolveNodeHandler($nodeData['type']);

            // Instantiate handler with config
            $handler = new $handlerClass($nodeData['config'] ?? []);

            // Validate configuration
            if (!$handler->validate()) {
                throw new \Exception("Invalid node configuration for {$nodeData['type']}");
            }

            // Execute node
            $result = $handler->execute($conversation, $userMessage);

            // Calculate execution time
            $executionTime = round((microtime(true) - $startTime) * 1000, 2); // ms

            // Log successful execution
            Log::info('Node executed successfully', [
                'node_id' => $nodeData['id'] ?? 'unknown',
                'node_type' => $nodeData['type'],
                'node_name' => $nodeData['name'] ?? 'unnamed',
                'conversation_id' => $conversation->id,
                'tenant_id' => $conversation->tenant_id,
                'success' => $result['success'],
                'has_prompt' => !empty($result['prompt']),
                'has_data' => !empty($result['data']),
                'next_node' => $result['next_node'],
                'execution_time_ms' => $executionTime,
            ]);

            return $result;

        } catch (\Exception $e) {
            // Calculate execution time even for errors
            $executionTime = round((microtime(true) - $startTime) * 1000, 2); // ms

            // Log execution failure
            Log::error('Node execution failed', [
                'node_id' => $nodeData['id'] ?? 'unknown',
                'node_type' => $nodeData['type'] ?? 'unknown',
                'conversation_id' => $conversation->id,
                'tenant_id' => $conversation->tenant_id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'next_node' => null,
                'prompt' => null,
                'data' => [],
            ];
        }
    }

    /**
     * Resolve node handler class from type
     *
     * @param string $nodeType Node type identifier
     * @return string Handler class name
     * @throws \Exception If node type not registered
     */
    protected function resolveNodeHandler(string $nodeType): string
    {
        if (!isset(self::$nodeRegistry[$nodeType])) {
            throw new \Exception("Unknown node type: {$nodeType}. Available types: " . implode(', ', array_keys(self::$nodeRegistry)));
        }

        $handlerClass = self::$nodeRegistry[$nodeType];

        if (!class_exists($handlerClass)) {
            throw new \Exception("Node handler class not found: {$handlerClass}");
        }

        return $handlerClass;
    }

    /**
     * Register a node handler
     *
     * @param string $type Node type identifier
     * @param string $handlerClass Handler class name
     * @return void
     */
    public static function register(string $type, string $handlerClass): void
    {
        self::$nodeRegistry[$type] = $handlerClass;
    }

    /**
     * Unregister a node handler
     *
     * @param string $type Node type identifier
     * @return void
     */
    public static function unregister(string $type): void
    {
        unset(self::$nodeRegistry[$type]);
    }

    /**
     * Check if node type is registered
     *
     * @param string $type Node type identifier
     * @return bool
     */
    public static function isRegistered(string $type): bool
    {
        return isset(self::$nodeRegistry[$type]);
    }

    /**
     * Get all registered node types
     *
     * @return array<string>
     */
    public static function getRegisteredTypes(): array
    {
        return array_keys(self::$nodeRegistry);
    }

    /**
     * Get all available nodes metadata
     *
     * Returns metadata for all registered nodes for admin UI
     *
     * @return array
     */
    public static function getAvailableNodes(): array
    {
        $nodes = [];

        foreach (self::$nodeRegistry as $type => $handlerClass) {
            try {
                if (!class_exists($handlerClass)) {
                    Log::warning("Node handler class not found: {$handlerClass}");
                    continue;
                }

                $instance = new $handlerClass();
                $nodes[] = $instance->getMetadata();
            } catch (\Exception $e) {
                Log::error("Failed to get metadata for node type: {$type}", [
                    'handler_class' => $handlerClass,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $nodes;
    }

    /**
     * Get nodes grouped by category
     *
     * @return array
     */
    public static function getNodesByCategory(): array
    {
        $nodes = self::getAvailableNodes();
        $grouped = [];

        foreach ($nodes as $node) {
            $category = $node['category'] ?? 'general';
            $grouped[$category][] = $node;
        }

        return $grouped;
    }

    /**
     * Clear registry (useful for testing)
     */
    public static function clearRegistry(): void
    {
        self::$nodeRegistry = [];
        self::$initialized = false;
    }
}
