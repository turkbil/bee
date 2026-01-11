<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Chat;

use Illuminate\Support\Facades\{Log, Cache, Redis, Event};
use Illuminate\Support\Collection;
use Modules\AI\App\Models\{AIFeature, AICreditUsage, ChatSession, ChatMessage};
use Modules\AI\App\Services\{
    AICreditService,
    ProviderOptimizationService,
    ResponseTemplateEngine,
    DatabaseLearningService
};
use Modules\AI\App\Events\{MessageReceived, MessageProcessed, SessionStarted, SessionEnded};
use Modules\AI\App\Exceptions\{ChatException, InsufficientCreditsException};
use Modules\AI\App\Repositories\Contracts\AIFeatureRepositoryInterface;
use Carbon\Carbon;

/**
 * 🚀 CHAT SERVICE V2 - Next-Generation Real-time AI Chat System
 * 
 * Advanced chat service with real-time capabilities, WebSocket support,
 * session management, and intelligent message routing.
 * 
 * KEY FEATURES:
 * - Real-time WebSocket communication
 * - Session-based conversation management
 * - Intelligent provider routing
 * - Context-aware responses
 * - Multi-user conversation support
 * - Advanced message formatting
 * - Performance optimization
 * - Comprehensive analytics
 * 
 * @version 2.0
 */
readonly class ChatServiceV2
{
    // WebSocket event types
    public const EVENT_MESSAGE_RECEIVED = 'message.received';
    public const EVENT_MESSAGE_PROCESSED = 'message.processed';
    public const EVENT_TYPING_START = 'typing.start';
    public const EVENT_TYPING_STOP = 'typing.stop';
    public const EVENT_SESSION_START = 'session.start';
    public const EVENT_SESSION_END = 'session.end';
    public const EVENT_ERROR = 'error';

    // Session configurations
    private const SESSION_TTL = 3600; // 1 hour
    private const MAX_MESSAGES_PER_SESSION = 100;
    private const MAX_CONCURRENT_SESSIONS = 10;

    // Message processing configurations
    private const MAX_MESSAGE_LENGTH = 2000;
    private const TYPING_INDICATOR_DELAY = 1500; // 1.5 seconds
    private const RESPONSE_TIMEOUT = 30000; // 30 seconds

    public function __construct(
        private AICreditService $creditService,
        private ProviderOptimizationService $providerOptimizationService,
        private ResponseTemplateEngine $templateEngine,
        private DatabaseLearningService $databaseLearningService,
        private AIFeatureRepositoryInterface $featureRepository
    ) {
        Log::info('ChatServiceV2 initialized', [
            'version' => '2.0',
            'websocket_enabled' => true,
            'max_concurrent_sessions' => self::MAX_CONCURRENT_SESSIONS,
        ]);
    }

    /**
     * 💬 Start a new chat session
     */
    public function startSession(array $config = []): ChatSession
    {
        $session = ChatSession::create([
            'session_id' => $this->generateSessionId(),
            'user_id' => $config['user_id'] ?? null,
            'tenant_id' => $config['tenant_id'] ?? tenant('id'),
            'channel' => $config['channel'] ?? 'web',
            'context' => $config['context'] ?? [],
            'settings' => array_merge([
                'language' => 'tr',
                'response_format' => 'conversational',
                'enable_typing_indicator' => true,
                'enable_context_awareness' => true,
            ], $config['settings'] ?? []),
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addSeconds(self::SESSION_TTL),
        ]);

        // Cache session for quick access
        $this->cacheSession($session);

        // Fire session started event
        Event::dispatch(new SessionStarted($session));

        // Send WebSocket notification
        $this->broadcastSessionEvent(self::EVENT_SESSION_START, $session, [
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
            'settings' => $session->settings,
        ]);

        Log::info('Chat session started', [
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
            'channel' => $session->channel,
        ]);

        return $session;
    }

    /**
     * 📤 Process incoming message with real-time handling
     */
    public function processMessage(
        string $sessionId,
        string $message,
        array $options = []
    ): array {
        $startTime = microtime(true);

        try {
            // Get and validate session
            $session = $this->getActiveSession($sessionId);
            if (!$session) {
                throw new ChatException("Session {$sessionId} not found or expired");
            }

            // Validate message
            $this->validateMessage($message, $session);

            // Create message record
            $chatMessage = $this->createMessage($session, $message, 'user', $options);

            // Fire message received event
            Event::dispatch(new MessageReceived($chatMessage));

            // Send typing indicator
            if ($session->settings['enable_typing_indicator'] ?? true) {
                $this->sendTypingIndicator($sessionId, true);
            }

            // Process with AI
            $aiResponse = $this->processWithAI($session, $chatMessage, $options);

            // Stop typing indicator
            if ($session->settings['enable_typing_indicator'] ?? true) {
                $this->sendTypingIndicator($sessionId, false);
            }

            // Create AI response message
            $responseMessage = $this->createMessage(
                $session,
                $aiResponse['content'],
                'assistant',
                array_merge($options, [
                    'provider' => $aiResponse['provider'],
                    'feature_slug' => $aiResponse['feature_slug'] ?? null,
                    'credits_used' => $aiResponse['credits_used'] ?? 0,
                    'processing_time' => microtime(true) - $startTime,
                ])
            );

            // Fire message processed event
            Event::dispatch(new MessageProcessed($responseMessage));

            // Update session activity
            $this->updateSessionActivity($session);

            // Send WebSocket response
            $this->broadcastMessage($sessionId, $responseMessage);

            // Update analytics
            $this->updateChatAnalytics($session, $chatMessage, $responseMessage);

            return [
                'success' => true,
                'message' => $responseMessage,
                'session' => $session,
                'processing_time' => microtime(true) - $startTime,
                'credits_used' => $aiResponse['credits_used'] ?? 0,
                'provider' => $aiResponse['provider'],
            ];

        } catch (\Exception $e) {
            $this->handleProcessingError($sessionId, $e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * 📋 Get session messages with pagination
     */
    public function getSessionMessages(
        string $sessionId,
        int $page = 1,
        int $perPage = 20
    ): array {
        $session = $this->getActiveSession($sessionId);
        if (!$session) {
            throw new ChatException("Session {$sessionId} not found");
        }

        $messages = ChatMessage::where('session_id', $session->id)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'last_page' => $messages->lastPage(),
            ],
            'session' => $session,
        ];
    }

    /**
     * 🔚 End chat session
     */
    public function endSession(string $sessionId): bool
    {
        $session = $this->getActiveSession($sessionId);
        if (!$session) {
            return false;
        }

        // Update session status
        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        // Clear cache
        $this->clearSessionCache($sessionId);

        // Fire session ended event
        Event::dispatch(new SessionEnded($session));

        // Send WebSocket notification
        $this->broadcastSessionEvent(self::EVENT_SESSION_END, $session);

        // Generate session summary
        $this->generateSessionSummary($session);

        Log::info('Chat session ended', [
            'session_id' => $sessionId,
            'duration' => $session->ended_at->diffInMinutes($session->started_at),
            'messages_count' => $session->messages()->count(),
        ]);

        return true;
    }

    /**
     * 📊 Get real-time session analytics
     */
    public function getSessionAnalytics(string $sessionId): array
    {
        $session = $this->getActiveSession($sessionId);
        if (!$session) {
            throw new ChatException("Session {$sessionId} not found");
        }

        $messages = $session->messages;
        $userMessages = $messages->where('role', 'user');
        $assistantMessages = $messages->where('role', 'assistant');

        return [
            'session_id' => $sessionId,
            'status' => $session->status,
            'duration_minutes' => $session->started_at->diffInMinutes(now()),
            'total_messages' => $messages->count(),
            'user_messages' => $userMessages->count(),
            'assistant_messages' => $assistantMessages->count(),
            'total_credits_used' => $assistantMessages->sum('metadata.credits_used'),
            'average_response_time' => $assistantMessages->avg('metadata.processing_time'),
            'features_used' => $assistantMessages->pluck('metadata.feature_slug')
                ->filter()
                ->countBy()
                ->toArray(),
            'providers_used' => $assistantMessages->pluck('metadata.provider')
                ->filter()
                ->countBy()
                ->toArray(),
            'conversation_topics' => $this->extractConversationTopics($messages),
            'quality_metrics' => $this->calculateQualityMetrics($session, $messages),
        ];
    }

    /**
     * 🌐 Get active sessions for user/tenant
     */
    public function getActiveSessions(?int $userId = null, ?int $tenantId = null): Collection
    {
        $query = ChatSession::where('status', 'active')
            ->where('expires_at', '>', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->with(['messages' => function($q) {
                $q->latest()->limit(1); // Last message only
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * ⚡ Send real-time typing indicator
     */
    public function sendTypingIndicator(string $sessionId, bool $isTyping): void
    {
        $event = $isTyping ? self::EVENT_TYPING_START : self::EVENT_TYPING_STOP;
        
        $this->broadcastToSession($sessionId, $event, [
            'typing' => $isTyping,
            'timestamp' => now()->toISOString(),
        ]);

        // Auto-stop typing after delay if no message
        if ($isTyping) {
            $this->scheduleTypingStop($sessionId, self::TYPING_INDICATOR_DELAY);
        }
    }

    // Private helper methods

    private function generateSessionId(): string
    {
        return 'chat_' . uniqid() . '_' . time();
    }

    private function getActiveSession(string $sessionId): ?ChatSession
    {
        // Try cache first
        $cacheKey = "chat_session:{$sessionId}";
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return $cached;
        }

        // Get from database
        $session = ChatSession::where('session_id', $sessionId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($session) {
            $this->cacheSession($session);
        }

        return $session;
    }

    private function cacheSession(ChatSession $session): void
    {
        $cacheKey = "chat_session:{$session->session_id}";
        Cache::put($cacheKey, $session, self::SESSION_TTL);
    }

    private function clearSessionCache(string $sessionId): void
    {
        Cache::forget("chat_session:{$sessionId}");
    }

    private function validateMessage(string $message, ChatSession $session): void
    {
        if (strlen($message) > self::MAX_MESSAGE_LENGTH) {
            throw new ChatException("Message too long (max " . self::MAX_MESSAGE_LENGTH . " characters)");
        }

        $messageCount = $session->messages()->count();
        if ($messageCount >= self::MAX_MESSAGES_PER_SESSION) {
            throw new ChatException("Session message limit reached");
        }

        // Check user credit balance if authenticated
        if ($session->user_id && !$this->hasInsufficientCredits($session->user_id)) {
            throw new InsufficientCreditsException("User has insufficient credits");
        }
    }

    private function createMessage(
        ChatSession $session,
        string $content,
        string $role,
        array $metadata = []
    ): ChatMessage {
        return ChatMessage::create([
            'session_id' => $session->id,
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    private function processWithAI(
        ChatSession $session,
        ChatMessage $message,
        array $options
    ): array {
        // Get optimal provider
        $provider = $this->providerOptimizationService->getOptimalProvider(
            $options['feature_type'] ?? 'chat',
            $options['requirements'] ?? [],
            $session->tenant_id
        );

        // Build context
        $context = $this->buildConversationContext($session, $message, $options);

        // Get feature if specified
        $feature = null;
        if (!empty($options['feature_slug'])) {
            $feature = $this->featureRepository->findBySlug($options['feature_slug']);
        }

        // Process with template engine
        $templateResponse = $this->templateEngine->buildTemplateAwarePrompt(
            $feature,
            array_merge($options, ['context' => $context])
        );

        // Mock AI processing (replace with actual AI service call)
        $aiResponse = [
            'content' => $this->generateMockResponse($message->content, $context, $feature),
            'provider' => $provider['provider']->name,
            'feature_slug' => $feature?->slug,
            'credits_used' => $this->calculateCreditsUsed($session, $message, $provider),
        ];

        // Deduct credits if user is authenticated
        if ($session->user_id && $aiResponse['credits_used'] > 0) {
            $this->creditService->deductCredits(
                $session->user_id,
                $aiResponse['credits_used'],
                [
                    'session_id' => $session->session_id,
                    'message_id' => $message->id,
                    'feature_slug' => $aiResponse['feature_slug'],
                ]
            );
        }

        return $aiResponse;
    }

    private function buildConversationContext(
        ChatSession $session,
        ChatMessage $currentMessage,
        array $options
    ): array {
        // Get recent messages for context
        $recentMessages = $session->messages()
            ->where('id', '<', $currentMessage->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $context = [
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
            'conversation_history' => $recentMessages->map(function($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'timestamp' => $msg->created_at->toISOString(),
                ];
            })->toArray(),
            'session_settings' => $session->settings,
            'session_context' => $session->context,
        ];

        // Add database learning context if enabled
        if ($session->settings['enable_context_awareness'] ?? true) {
            $databaseContext = $this->databaseLearningService->buildContext([
                'detail_level' => 'medium',
                'focus_areas' => $options['focus_areas'] ?? [],
                'tenant_id' => $session->tenant_id,
            ]);
            
            $context['database_context'] = $databaseContext;
        }

        return $context;
    }

    private function generateMockResponse(
        string $userMessage,
        array $context,
        ?AIFeature $feature
    ): string {
        // Mock AI response generation
        // In production, this would call the actual AI service
        
        $responses = [
            "I understand your question about: {$userMessage}. Let me help you with that.",
            "Based on our conversation, I can provide you with the following insights...",
            "Thank you for your message. Here's what I found...",
            "That's an interesting point. Let me elaborate on that topic.",
        ];

        $response = $responses[array_rand($responses)];

        if ($feature) {
            $response .= " (Using {$feature->getTranslated('name')} feature)";
        }

        return $response;
    }

    private function calculateCreditsUsed(
        ChatSession $session,
        ChatMessage $message,
        array $provider
    ): int {
        // Base credit cost
        $baseCredits = 1;

        // Adjust based on message length
        $messageLength = strlen($message->content);
        if ($messageLength > 100) {
            $baseCredits += intval($messageLength / 100);
        }

        // Apply provider multiplier
        $multiplier = $provider['cost_estimate'] ?? 1.0;
        
        return max(1, intval($baseCredits * $multiplier));
    }

    private function hasInsufficientCredits(int $userId): bool
    {
        $credits = $this->creditService->getUserCredits($userId);
        return $credits < 1;
    }

    private function updateSessionActivity(ChatSession $session): void
    {
        $session->update([
            'updated_at' => now(),
            'expires_at' => now()->addSeconds(self::SESSION_TTL),
        ]);

        $this->cacheSession($session);
    }

    private function broadcastSessionEvent(
        string $event,
        ChatSession $session,
        array $data = []
    ): void {
        $payload = array_merge([
            'event' => $event,
            'session_id' => $session->session_id,
            'timestamp' => now()->toISOString(),
        ], $data);

        $this->broadcastToSession($session->session_id, $event, $payload);
    }

    private function broadcastMessage(string $sessionId, ChatMessage $message): void
    {
        $this->broadcastToSession($sessionId, self::EVENT_MESSAGE_PROCESSED, [
            'message' => [
                'id' => $message->id,
                'role' => $message->role,
                'content' => $message->content,
                'metadata' => $message->metadata,
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    private function broadcastToSession(string $sessionId, string $event, array $data): void
    {
        try {
            // In production, this would use WebSocket broadcasting
            // For now, we'll store in Redis for retrieval
            $redisKey = "websocket:session:{$sessionId}";
            $payload = json_encode([
                'event' => $event,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);

            Redis::lpush($redisKey, $payload);
            Redis::expire($redisKey, 300); // 5 minutes

            Log::debug('WebSocket event broadcasted', [
                'session_id' => $sessionId,
                'event' => $event,
            ]);

        } catch (\Exception $e) {
            Log::warning('WebSocket broadcast failed', [
                'session_id' => $sessionId,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function scheduleTypingStop(string $sessionId, int $delayMs): void
    {
        // In production, this would use a job queue
        // For now, we'll set a Redis key with expiration
        Redis::setex("typing_stop:{$sessionId}", intval($delayMs / 1000), '1');
    }

    private function updateChatAnalytics(
        ChatSession $session,
        ChatMessage $userMessage,
        ChatMessage $assistantMessage
    ): void {
        try {
            $analytics = [
                'session_id' => $session->session_id,
                'user_id' => $session->user_id,
                'tenant_id' => $session->tenant_id,
                'message_pair' => [
                    'user_message_length' => strlen($userMessage->content),
                    'assistant_message_length' => strlen($assistantMessage->content),
                    'processing_time' => $assistantMessage->metadata['processing_time'] ?? 0,
                    'credits_used' => $assistantMessage->metadata['credits_used'] ?? 0,
                ],
                'timestamp' => now()->toISOString(),
            ];

            // Store analytics in Redis
            Redis::lpush('chat_analytics', json_encode($analytics));
            Redis::ltrim('chat_analytics', 0, 9999); // Keep last 10k entries

        } catch (\Exception $e) {
            Log::warning('Chat analytics update failed', [
                'error' => $e->getMessage(),
                'session_id' => $session->session_id,
            ]);
        }
    }

    private function handleProcessingError(string $sessionId, \Exception $e, float $processingTime): void
    {
        Log::error('Chat message processing failed', [
            'session_id' => $sessionId,
            'error' => $e->getMessage(),
            'processing_time' => $processingTime,
            'trace' => $e->getTraceAsString(),
        ]);

        // Send error to WebSocket
        $this->broadcastToSession($sessionId, self::EVENT_ERROR, [
            'error' => $e->getMessage(),
            'type' => get_class($e),
        ]);
    }

    private function extractConversationTopics(Collection $messages): array
    {
        // Mock topic extraction
        // In production, this would use NLP or AI to extract topics
        return [
            'general_inquiry',
            'technical_support',
            'feature_request',
        ];
    }

    private function calculateQualityMetrics(ChatSession $session, Collection $messages): array
    {
        $assistantMessages = $messages->where('role', 'assistant');
        
        return [
            'response_time_avg' => $assistantMessages->avg('metadata.processing_time') ?? 0,
            'response_length_avg' => $assistantMessages->avg(function($msg) {
                return strlen($msg->content);
            }) ?? 0,
            'engagement_score' => min(100, $messages->count() * 10), // Simple engagement metric
            'session_duration' => $session->started_at->diffInMinutes(now()),
        ];
    }

    private function generateSessionSummary(ChatSession $session): void
    {
        $summary = [
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
            'duration_minutes' => $session->ended_at->diffInMinutes($session->started_at),
            'total_messages' => $session->messages()->count(),
            'total_credits_used' => $session->messages()
                ->where('role', 'assistant')
                ->sum('metadata.credits_used'),
            'topics_discussed' => $this->extractConversationTopics($session->messages),
            'quality_metrics' => $this->calculateQualityMetrics($session, $session->messages),
        ];

        // Store summary
        Cache::put("session_summary:{$session->session_id}", $summary, 86400 * 30); // 30 days

        Log::info('Session summary generated', $summary);
    }
}