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

            // 🚨 SONNET FIX: Reset flow to start for each new message
            $conversation->current_node_id = $flow->start_node_id;
            $conversation->save();

            // 🔄 MULTI-NODE EXECUTION LOOP
            $maxIterations = 20; // Prevent infinite loops
            $iteration = 0;
            $executedNodes = [];
            $finalResult = null;
            $aiResponse = null;

            while ($iteration < $maxIterations) {
                $iteration++;

                // Get current node
                $currentNode = $this->getCurrentNode($conversation, $flow);

                if (!$currentNode) {
                    Log::warning('Flow ended without reaching end node', [
                        'conversation_id' => $conversation->id,
                        'last_node' => $executedNodes[count($executedNodes) - 1] ?? 'none',
                    ]);
                    break;
                }

                Log::info('🔄 Executing node', [
                    'conversation_id' => $conversation->id,
                    'node_id' => $currentNode['id'],
                    'node_type' => $currentNode['type'],
                    'iteration' => $iteration,
                    'node_config' => $currentNode['config'] ?? [],
                ]);

                // Execute node
                $result = $this->executor->execute($currentNode, $conversation, $userMessage);

                Log::info('✅ Node executed', [
                    'conversation_id' => $conversation->id,
                    'node_type' => $currentNode['type'],
                    'success' => $result['success'],
                    'next_node' => $result['next_node'] ?? 'NULL',
                    'has_data' => !empty($result['data']),
                ]);

                if (!$result['success']) {
                    return $this->handleError($conversation, $result);
                }

                // Track executed nodes
                $executedNodes[] = [
                    'id' => $currentNode['id'],
                    'type' => $currentNode['type'],
                    'name' => $currentNode['name'] ?? $currentNode['type'],
                ];

                // Update conversation state
                $this->updateConversationState($conversation, $currentNode, $result);

                // If this was an end node, stop after executing it
                if ($currentNode['type'] === 'end') {
                    Log::info('Flow ended at end node', [
                        'conversation_id' => $conversation->id,
                        'total_nodes_executed' => count($executedNodes),
                    ]);
                    break;
                }

                // Store final result
                $finalResult = $result;

                // If AI Response node, generate AI response
                Log::info('🔍 Checking AI response condition', [
                    'node_type' => $currentNode['type'],
                    'has_prompt' => !empty($result['prompt']),
                    'prompt_preview' => substr($result['prompt'] ?? '', 0, 50),
                ]);

                if ($currentNode['type'] === 'ai_response' && !empty($result['prompt'])) {
                    Log::info('🤖 AI Response Node - Starting generation', [
                        'conversation_id' => $conversation->id,
                        'prompt_length' => strlen($result['prompt']),
                    ]);

                    // Build AI context
                    $aiContext = $this->buildAIContext($conversation, $result);
                    $aiContext['user_message'] = $userMessage;

                    // Generate AI response
                    $aiResponse = $this->generateAIResponse($result['prompt'], $aiContext);

                    // Store in conversation context for next nodes
                    $conversation->addToContext('last_ai_response', $aiResponse);

                    Log::info('✅ AI response generated successfully', [
                        'conversation_id' => $conversation->id,
                        'response_length' => strlen($aiResponse ?? ''),
                        'response_preview' => substr($aiResponse ?? '', 0, 100),
                    ]);
                }

                // If node doesn't have next_node, stop
                if (empty($result['next_node'])) {
                    Log::info('Flow ended (no next_node)', [
                        'conversation_id' => $conversation->id,
                        'last_node' => $currentNode['type'],
                    ]);
                    break;
                }
            }

            if ($iteration >= $maxIterations) {
                Log::error('Flow execution exceeded max iterations', [
                    'conversation_id' => $conversation->id,
                    'max_iterations' => $maxIterations,
                    'executed_nodes' => array_column($executedNodes, 'type'),
                ]);
                return $this->fallbackResponse('Flow execution timeout');
            }

            $result = $finalResult; // Use final result for context building

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Message processed successfully', [
                'conversation_id' => $conversation->id,
                'tenant_id' => $tenantId,
                'nodes_executed' => count($executedNodes),
                'has_response' => !empty($aiResponse),
                'execution_time_ms' => $executionTime,
            ]);

            // 🚨 SONNET FIX: Use processed response if link_generator ran
            $finalResponse = $conversation->context_data['processed_ai_response'] ?? $aiResponse;

            return [
                'success' => true,
                'response' => $finalResponse,
                'nodes_executed' => $executedNodes,
                'context' => [
                    'flow_name' => $flow->flow_name,
                    'nodes_executed' => array_column($executedNodes, 'type'),
                ],
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
                ->orderBy('created_at', 'asc')
                ->limit(20) // Son 20 mesaj
                ->get()
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
        try {
            // Use CentralAIService for AI requests
            $aiService = app(\App\Services\AI\CentralAIService::class);

            // Build context as user message
            $userMessage = $context['user_message'] ?? '';
            $messageHistory = $context['message_history'] ?? [];

            // Build full prompt with conversation history
            $fullPrompt = "SYSTEM: " . $prompt . "\n\n";

            // Add message history for context
            if (!empty($messageHistory)) {
                $fullPrompt .= "CONVERSATION HISTORY:\n";
                foreach ($messageHistory as $msg) {
                    $role = $msg['role'] === 'user' ? 'USER' : 'ASSISTANT';
                    $fullPrompt .= "{$role}: {$msg['content']}\n";
                }
                $fullPrompt .= "\n";
            }

            // Add current user message
            $fullPrompt .= "USER: {$userMessage}\nASSISTANT:";

            // Execute AI request
            $response = $aiService->executeRequest($fullPrompt, [
                'usage_type' => 'conversation_flow',
                'feature_slug' => 'ai_workflow',
                'reference_id' => $context['conversation_id'] ?? null,
                'force_provider' => 'openai', // TODO: Make this configurable per tenant/flow
            ]);

            // Extract response text
            if (isset($response['response'])) {
                // Response is an array with 'content' key
                if (is_array($response['response']) && isset($response['response']['content'])) {
                    return $response['response']['content'];
                }

                // If response is already a string
                if (is_string($response['response'])) {
                    return $response['response'];
                }
            }

            // Fallback
            Log::warning('AI response missing response field', ['response' => $response]);
            return 'Üzgünüm, yanıt oluşturulamadı.';

        } catch (\Exception $e) {
            Log::error('AI response generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return 'Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.';
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
