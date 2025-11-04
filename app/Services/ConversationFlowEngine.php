<?php

namespace App\Services;

use App\Models\{AIConversation, TenantConversationFlow, AITenantDirective};
use App\Services\ConversationNodes\NodeExecutor;
use Illuminate\Support\Facades\{Cache, Log};

/**
 * Conversation Flow Engine
 *
 * Main orchestrator for AI conversation workflows
 * Manages conversation state and node execution
 */
class ConversationFlowEngine
{
    protected NodeExecutor $executor;

    public function __construct(NodeExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * Process user message through conversation flow
     *
     * Main entry point for processing messages
     *
     * @param string $sessionId User session ID
     * @param int $tenantId Tenant ID
     * @param string $userMessage User's message
     * @param int|null $userId Optional user ID
     * @return array Result with AI response
     */
    public function processMessage(string $sessionId, int $tenantId, string $userMessage, ?int $userId = null): array
    {
        $startTime = microtime(true);

        try {
            // Get or create conversation
            $conversation = AIConversation::getOrCreateForSession($sessionId, $tenantId, $userId);

            // Get active flow
            $flow = $this->getFlow($conversation);

            if (!$flow) {
                return $this->fallbackResponse('No active flow configured for this tenant');
            }

            // Get current node
            $currentNode = $this->getCurrentNode($conversation, $flow);

            if (!$currentNode) {
                return $this->fallbackResponse('Invalid flow configuration');
            }

            // Execute node
            $result = $this->executor->execute($currentNode, $conversation, $userMessage);

            if (!$result['success']) {
                return $this->handleError($conversation, $result);
            }

            // Update conversation state
            $this->updateConversationState($conversation, $currentNode, $result);

            // Build AI context
            $aiContext = $this->buildAIContext($conversation, $result);

            // Generate AI response (if prompt provided)
            $aiResponse = null;
            if (!empty($result['prompt'])) {
                $aiResponse = $this->generateAIResponse($result['prompt'], $aiContext);
            }

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Message processed successfully', [
                'conversation_id' => $conversation->id,
                'tenant_id' => $tenantId,
                'current_node' => $currentNode['type'],
                'next_node' => $result['next_node'],
                'has_response' => !empty($aiResponse),
                'execution_time_ms' => $executionTime,
            ]);

            return [
                'success' => true,
                'response' => $aiResponse,
                'current_node' => $currentNode['name'] ?? $currentNode['type'],
                'next_node' => $result['next_node'],
                'context' => $result['data'] ?? [],
                'conversation_id' => $conversation->id,
            ];

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('Message processing failed', [
                'tenant_id' => $tenantId,
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'execution_time_ms' => $executionTime,
            ]);

            return $this->fallbackResponse('An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Get active flow for conversation
     */
    protected function getFlow(AIConversation $conversation): ?TenantConversationFlow
    {
        return Cache::remember(
            "conversation_flow_{$conversation->tenant_id}_{$conversation->flow_id}",
            3600,
            fn() => TenantConversationFlow::find($conversation->flow_id)
        );
    }

    /**
     * Get current node from flow
     */
    protected function getCurrentNode(AIConversation $conversation, TenantConversationFlow $flow): ?array
    {
        $flowData = $flow->flow_data;
        $currentNodeId = $conversation->current_node_id ?? $flow->start_node_id;

        $node = collect($flowData['nodes'] ?? [])->firstWhere('id', $currentNodeId);

        if (!$node) {
            Log::error('Node not found in flow', [
                'flow_id' => $flow->id,
                'node_id' => $currentNodeId,
                'available_nodes' => array_column($flowData['nodes'] ?? [], 'id'),
            ]);
        }

        return $node;
    }

    /**
     * Update conversation state after node execution
     */
    protected function updateConversationState(AIConversation $conversation, array $currentNode, array $result): void
    {
        // Move to next node
        if ($result['next_node']) {
            $conversation->moveToNode($result['next_node'], $result);
        }

        // Merge node data into context
        if (!empty($result['data'])) {
            $conversation->mergeContext($result['data']);
        }
    }

    /**
     * Build AI context from conversation and node result
     */
    protected function buildAIContext(AIConversation $conversation, array $result): array
    {
        return [
            'tenant_id' => $conversation->tenant_id,
            'conversation_id' => $conversation->id,
            'conversation_context' => $conversation->context_data ?? [],
            'node_data' => $result['data'] ?? [],
            'directives' => $this->getTenantDirectives($conversation->tenant_id),
            'message_history' => $this->getMessageHistory($conversation),
        ];
    }

    /**
     * Get tenant directives
     */
    protected function getTenantDirectives(int $tenantId): array
    {
        return AITenantDirective::getAllForTenant($tenantId);
    }

    /**
     * Get conversation message history
     */
    protected function getMessageHistory(AIConversation $conversation): array
    {
        try {
            return $conversation->messages()
                ->latest()
                ->limit(10)
                ->get()
                ->reverse()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ])
                ->toArray();
        } catch (\Exception $e) {
            Log::warning('Could not fetch message history', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Generate AI response using AIService
     */
    protected function generateAIResponse(string $prompt, array $context): string
    {
        // TODO: Integrate with your existing AIService
        // For now, return the prompt (to be replaced with actual AI call)

        try {
            // Check if AIService exists
            if (class_exists(\App\Services\AIService::class)) {
                return app(\App\Services\AIService::class)->ask($prompt, $context);
            }

            // Fallback: return prompt for testing
            return $prompt;
        } catch (\Exception $e) {
            Log::error('AI response generation failed', [
                'error' => $e->getMessage(),
            ]);

            return $prompt; // Fallback to prompt
        }
    }

    /**
     * Handle node execution error
     */
    protected function handleError(AIConversation $conversation, array $result): array
    {
        Log::error('Flow execution error', [
            'conversation_id' => $conversation->id,
            'error' => $result['error'] ?? 'Unknown error',
        ]);

        return [
            'success' => false,
            'response' => 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.',
            'error' => $result['error'] ?? 'Unknown error',
        ];
    }

    /**
     * Fallback response when flow not available
     */
    protected function fallbackResponse(string $message): array
    {
        return [
            'success' => false,
            'response' => 'Üzgünüm, şu anda size yardımcı olamıyorum. Lütfen daha sonra tekrar deneyin.',
            'error' => $message,
        ];
    }

    /**
     * Clear flow cache for tenant
     */
    public static function clearFlowCache(int $tenantId): void
    {
        // Clear all flow caches for tenant
        $flows = TenantConversationFlow::byTenant($tenantId)->get();

        foreach ($flows as $flow) {
            Cache::forget("conversation_flow_{$tenantId}_{$flow->id}");
        }

        // Clear directive cache
        Cache::forget("tenant_directives_{$tenantId}");

        Log::info('Flow cache cleared', ['tenant_id' => $tenantId]);
    }
}
