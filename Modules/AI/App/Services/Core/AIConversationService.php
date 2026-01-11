<?php

namespace Modules\AI\App\Services\Core;

use Modules\AI\App\Services\ConversationTracker;
use Modules\AI\App\Helpers\TenantHelpers;
use Illuminate\Support\Facades\Log;

/**
 * 💬 AI CONVERSATION SERVICE - Konuşma tracking ve kayıt işlemleri
 */
class AIConversationService
{
    /**
     * 📊 CREATE CONVERSATION RECORD
     */
    public function createConversationRecord(string $userMessage, string $aiResponse, string $type = 'chat', array $metadata = []): void
    {
        try {
            $conversationData = [
                'tenant_id' => TenantHelpers::getTenantId(),
                'user_id' => $this->getUserId(),
                'session_id' => $metadata['session_id'] ?? 'conversation_' . uniqid(),
                'title' => $metadata['title'] ?? 'AI Conversation',
                'type' => $type,
                'feature_name' => $metadata['feature_name'] ?? 'general',
                'is_demo' => $metadata['is_demo'] ?? false,
                'prompt_id' => $metadata['prompt_id'] ?? 1,
                'metadata' => array_merge([
                    'source' => 'conversation_service',
                    'message_length' => strlen($userMessage),
                    'response_length' => strlen($aiResponse),
                    'timestamp' => now()->toISOString()
                ], $metadata)
            ];

            ConversationTracker::trackConversation(
                $conversationData,
                $userMessage,
                $aiResponse
            );

            Log::info('💬 Conversation kaydedildi', [
                'type' => $type,
                'session_id' => $conversationData['session_id'],
                'user_message_length' => strlen($userMessage),
                'ai_response_length' => strlen($aiResponse)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Conversation kayıt hatası', [
                'error' => $e->getMessage(),
                'type' => $type,
                'metadata' => $metadata
            ]);
        }
    }

    /**
     * 🔍 GET USER ID WITH FALLBACK
     */
    private function getUserId(): int
    {
        if (auth()->check()) {
            $userId = auth()->id();
            if ($userId) {
                return (int) $userId;
            }
        }
        
        Log::info('⚠️ Auth user bulunamadı, default user (1) kullanılıyor');
        return 1;
    }

    /**
     * 📈 TRACK CONVERSATION METRICS
     */
    public function trackMetrics(array $conversationData): void
    {
        try {
            // Conversation metrics tracking
            $metrics = [
                'tenant_id' => $conversationData['tenant_id'] ?? null,
                'user_id' => $conversationData['user_id'] ?? 1,
                'type' => $conversationData['type'] ?? 'unknown',
                'feature_name' => $conversationData['feature_name'] ?? 'general',
                'timestamp' => now(),
                'metadata' => $conversationData['metadata'] ?? []
            ];

            // Log metrics for analytics
            Log::info('📊 Conversation metrics', $metrics);

        } catch (\Exception $e) {
            Log::error('❌ Metrics tracking error', [
                'error' => $e->getMessage(),
                'conversation_data' => $conversationData
            ]);
        }
    }

    /**
     * 🗂️ GET CONVERSATION HISTORY
     */
    public function getConversationHistory(string $sessionId, int $limit = 10): array
    {
        try {
            // Bu method ConversationTracker'dan history alabilir
            // Şimdilik basit bir log döndürelim
            
            Log::info('📜 Conversation history istendi', [
                'session_id' => $sessionId,
                'limit' => $limit
            ]);

            return [
                'success' => true,
                'session_id' => $sessionId,
                'messages' => [], // ConversationTracker'dan gelecek
                'total' => 0
            ];

        } catch (\Exception $e) {
            Log::error('❌ Conversation history error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'messages' => []
            ];
        }
    }
}