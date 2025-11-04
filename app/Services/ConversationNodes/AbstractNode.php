<?php

namespace App\Services\ConversationNodes;

use App\Models\AIConversation;

/**
 * Abstract Node Base Class
 *
 * All conversation nodes must extend this class
 * Provides common interface and structure for node execution
 */
abstract class AbstractNode
{
    /**
     * Node configuration from flow
     */
    protected array $config;

    /**
     * Node execution result structure
     */
    protected array $result = [
        'success' => false,
        'prompt' => null,
        'data' => [],
        'next_node' => null,
        'error' => null,
    ];

    /**
     * Constructor
     *
     * @param array $config Node configuration from flow_data
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Execute node logic
     *
     * This is the main method that each node must implement
     * It processes the user message and conversation context
     *
     * @param AIConversation $conversation Current conversation state
     * @param string $userMessage User's message
     * @return array Result array with structure:
     *               - success: bool
     *               - prompt: string (system prompt for AI)
     *               - data: array (contextual data for AI)
     *               - next_node: string|null (next node ID to execute)
     *               - error: string|null (error message if failed)
     */
    abstract public function execute(AIConversation $conversation, string $userMessage): array;

    /**
     * Validate node configuration
     *
     * Check if the node's config contains all required parameters
     * Return true if valid, false otherwise
     *
     * @return bool
     */
    abstract public function validate(): bool;

    /**
     * Get node type identifier
     *
     * Unique identifier for this node type (e.g., 'ai_response', 'show_products')
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * Get node display name
     *
     * Human-readable name shown in admin UI
     *
     * @return string
     */
    abstract public static function getName(): string;

    /**
     * Get node description
     *
     * Brief description of what this node does
     *
     * @return string
     */
    abstract public static function getDescription(): string;

    /**
     * Get configuration schema
     *
     * Defines what configuration options this node accepts
     * Used to generate admin UI forms
     *
     * @return array
     */
    abstract public static function getConfigSchema(): array;

    /**
     * Get input definitions
     *
     * Defines input connection points for this node
     *
     * @return array
     */
    abstract public static function getInputs(): array;

    /**
     * Get output definitions
     *
     * Defines output connection points for this node
     *
     * @return array
     */
    abstract public static function getOutputs(): array;

    /**
     * Get complete node metadata
     *
     * Returns all metadata about this node for admin UI
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'type' => static::getType(),
            'name' => static::getName(),
            'description' => static::getDescription(),
            'config_schema' => static::getConfigSchema(),
            'inputs' => static::getInputs(),
            'outputs' => static::getOutputs(),
            'category' => static::getCategory(),
            'icon' => static::getIcon(),
        ];
    }

    /**
     * Get node category
     *
     * Used to group nodes in admin UI
     * Override in child classes if needed
     *
     * @return string
     */
    public static function getCategory(): string
    {
        return 'general';
    }

    /**
     * Get node icon
     *
     * Icon class/name for admin UI
     * Override in child classes if needed
     *
     * @return string
     */
    public static function getIcon(): string
    {
        return 'ti ti-box';
    }

    /**
     * Get config value with default
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set result success
     *
     * @param string|null $prompt System prompt for AI
     * @param array $data Contextual data
     * @param string|null $nextNode Next node ID
     * @return array
     */
    protected function success(?string $prompt = null, array $data = [], ?string $nextNode = null): array
    {
        return [
            'success' => true,
            'prompt' => $prompt,
            'data' => $data,
            'next_node' => $nextNode,
            'error' => null,
        ];
    }

    /**
     * Set result failure
     *
     * @param string $error Error message
     * @param string|null $nextNode Optional next node (for error handling)
     * @return array
     */
    protected function failure(string $error, ?string $nextNode = null): array
    {
        return [
            'success' => false,
            'prompt' => null,
            'data' => [],
            'next_node' => $nextNode,
            'error' => $error,
        ];
    }

    /**
     * Get tenant directives
     *
     * Helper method to access tenant-specific configuration
     *
     * @param AIConversation $conversation
     * @param string|null $key Specific directive key, or null for all
     * @param mixed $default Default value if directive not found
     * @return mixed
     */
    protected function getDirective(AIConversation $conversation, ?string $key = null, $default = null)
    {
        $directives = \App\Models\AITenantDirective::getAllForTenant($conversation->tenant_id);

        if ($key === null) {
            return $directives;
        }

        return $directives[$key] ?? $default;
    }

    /**
     * Log node execution
     *
     * @param string $level Log level (info, warning, error)
     * @param string $message Log message
     * @param array $context Additional context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::$level($message, array_merge([
            'node_type' => static::getType(),
            'node_class' => static::class,
        ], $context));
    }
}
