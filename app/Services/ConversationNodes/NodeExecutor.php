<?php

namespace App\Services\ConversationNodes;

use App\Models\AIConversation;
use App\Models\AIWorkflowNode;
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
     *
     * Initialize registry on construction to ensure it's always fresh
     */
    public function __construct()
    {
        // ALWAYS reinitialize - clear static registry
        self::$initialized = false;
        self::$nodeRegistry = [];

        // Initialize from database
        $this->initializeRegistry();
        self::$initialized = true;
    }

    /**
     * Initialize node registry from database
     *
     * Loads all active nodes from ai_workflow_nodes table
     * Uses getForTenant() to get both global and tenant-specific nodes
     *
     * @param int|null $forceTenantId Force specific tenant ID (overrides tenant() helper)
     */
    protected function initializeRegistry(?int $forceTenantId = null): void
    {
        try {
            // Get tenant ID - prioritize forced ID, then tenant context
            $tenantId = $forceTenantId ?? (function_exists('tenant') && tenant() ? tenant('id') : null);

            Log::info('🔧 Initializing node registry', [
                'tenant_id' => $tenantId,
                'has_tenant_context' => !is_null($tenantId),
            ]);

            if ($tenantId) {
                // Tenant context: Get both global and tenant-specific nodes
                $nodes = AIWorkflowNode::getForTenant($tenantId);

                Log::info('📦 Nodes fetched from database', [
                    'count' => count($nodes),
                    'ALL_NODES' => $nodes, // FULL DATA DEBUG
                ]);

                foreach ($nodes as $node) {
                    Log::info('✅ Registering node', [
                        'type' => $node['type'],
                        'class' => $node['class'],
                    ]);
                    self::register($node['type'], $node['class']);
                }

                Log::info('Node registry initialized from database (tenant context)', [
                    'tenant_id' => $tenantId,
                    'total_nodes' => count(self::$nodeRegistry),
                    'FULL_REGISTRY' => self::$nodeRegistry, // FULL REGISTRY DEBUG
                ]);
            } else {
                // Central context: Get only global nodes from central DB
                $nodes = \DB::connection('mysql')->table('ai_workflow_nodes')
                    ->where('is_active', true)
                    ->where('is_global', true)
                    ->orderBy('category')
                    ->orderBy('order')
                    ->get();

                foreach ($nodes as $node) {
                    self::register($node->node_key, $node->node_class);
                }

                Log::info('Node registry initialized from database (central context)', [
                    'total_nodes' => count(self::$nodeRegistry),
                    'node_types' => array_keys(self::$nodeRegistry),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize node registry from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // DO NOT fallback - throw exception so we can see the real problem
            throw new \Exception('Node registry initialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * [REMOVED] Fallback function removed - always use database
     * If database fails, system should throw exception, not use stale hardcoded data
     */

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
            // 🚨 CRITICAL: ALWAYS reinitialize registry on EVERY execute()
            // This ensures we ALWAYS have fresh tenant context and correct class mappings
            // Even if constructor was called without tenant context, this will fix it
            Log::info('🔧 NodeExecutor::execute() - Force reinitializing registry', [
                'conversation_id' => $conversation->id,
                'tenant_id' => $conversation->tenant_id,
                'current_tenant' => tenant() ? tenant('id') : null,
            ]);

            self::$initialized = false;
            self::$nodeRegistry = [];
            $this->initializeRegistry($conversation->tenant_id);
            self::$initialized = true;

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

        // DEBUG: Log the registry state for this node type
        Log::warning('🔍 Resolving node handler', [
            'node_type' => $nodeType,
            'handler_class' => $handlerClass,
            'class_exists' => class_exists($handlerClass),
            'registry_keys' => array_keys(self::$nodeRegistry),
        ]);

        if (!class_exists($handlerClass)) {
            // Dump full registry for debugging
            Log::error('❌ Class not found - dumping registry', [
                'node_type' => $nodeType,
                'handler_class' => $handlerClass,
                'full_registry' => self::$nodeRegistry,
            ]);

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
        // Lazy init if needed
        if (!self::$initialized) {
            $instance = new self();
            self::$nodeRegistry = [];
            $instance->initializeRegistry();
            self::$initialized = true;
        }
        return isset(self::$nodeRegistry[$type]);
    }

    /**
     * Get all registered node types
     *
     * @return array<string>
     */
    public static function getRegisteredTypes(): array
    {
        // Lazy init if needed
        if (!self::$initialized) {
            $instance = new self();
            self::$nodeRegistry = [];
            $instance->initializeRegistry();
            self::$initialized = true;
        }
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
