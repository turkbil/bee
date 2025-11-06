<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Modules\AI\App\Services\AI\StreamingAIService;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\Log;

/**
 * AI Response Node
 *
 * Claude/OpenAI ile yanıt üretir
 * Streaming destekli
 */
class AIResponseNode extends BaseNode
{
    /**
     * Get directive value from database (tenant-specific)
     */
    protected function getDirectiveValue(string $key, string $type, $default)
    {
        try {
            $directive = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                ->where('directive_key', $key)
                ->where('is_active', true)
                ->first();

            if ($directive) {
                $value = $directive->directive_value;

                // Type casting
                return match($type) {
                    'integer' => (int) $value,
                    'boolean' => (bool) $value,
                    'float', 'string' => (float) $value,
                    default => $value
                };
            }
        } catch (\Exception $e) {
            \Log::warning("Could not load directive: {$key}", ['error' => $e->getMessage()]);
        }

        return $default;
    }

    public function execute(array $context): array
    {
        $systemPrompt = $this->getConfig('system_prompt', '');

        // Load AI config from directives (panelden düzenlenebilir)
        $maxTokens = $this->getDirectiveValue('max_tokens', 'integer', $this->getConfig('max_tokens', 500));
        $temperature = $this->getDirectiveValue('temperature', 'string', $this->getConfig('temperature', 0.7));

        $stream = $this->getConfig('stream', false);
        $provider = $this->getConfig('provider', 'anthropic');

        // Prepare messages
        $messages = $this->prepareMessages($context, $systemPrompt);

        Log::info('🤖 AI Response Node executing', [
            'provider' => $provider,
            'stream' => $stream,
            'tokens' => $maxTokens
        ]);

        if ($stream) {
            return $this->executeStreaming($provider, $messages, $context);
        }

        return $this->executeStandard($provider, $messages, $maxTokens, $temperature);
    }

    /**
     * Streaming execution
     */
    protected function executeStreaming(string $provider, array $messages, array $context): array
    {
        $streamingService = new StreamingAIService($provider);
        $channel = $this->getStreamChannel($context);

        $fullResponse = $streamingService->stream($messages, function($chunk) use ($channel, $streamingService) {
            $streamingService->broadcastChunk($channel, $chunk);
        });

        return [
            'ai_response' => $fullResponse,
            'streaming' => true
        ];
    }

    /**
     * Standard (non-streaming) execution
     */
    protected function executeStandard(string $provider, array $messages, int $maxTokens, float $temperature): array
    {
        Log::info('🚨 executeStandard CALLED', [
            'messages_count' => count($messages),
            'messages_dump' => json_encode($messages)
        ]);

        // Use real AIService
        $aiService = app(AIService::class);

        // Extract system prompt from first message if exists
        $systemPrompt = '';
        if (isset($messages[0]) && $messages[0]['role'] === 'assistant') {
            $systemPrompt = $messages[0]['content'];
            array_shift($messages); // Remove system message from messages array
        }

        // Get user message
        $userMessage = '';
        foreach ($messages as $msg) {
            if ($msg['role'] === 'user') {
                $userMessage = $msg['content'];
            }
        }

        Log::info('🔍 AI Request Debug', [
            'user_message' => $userMessage,
            'system_prompt_length' => strlen($systemPrompt),
            'total_messages' => count($messages)
        ]);

        try {
            // BYPASS AIService - Use direct provider
            // AIService ignores our system_prompt, so we call provider directly

            $providerService = app(\Modules\AI\App\Services\OpenAIService::class);

            $aiResponse = $providerService->ask([
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ], false, [
                'temperature' => $temperature,
                'max_tokens' => $maxTokens
            ]);

            Log::info('✅ AI Response generated', [
                'length' => strlen($aiResponse)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ AI Response failed', [
                'error' => $e->getMessage()
            ]);

            $aiResponse = 'Üzgünüm, şu anda yanıt üretemiyorum. Lütfen daha sonra tekrar deneyin.';
        }

        return [
            'ai_response' => $aiResponse,
            'streaming' => false
        ];
    }

    /**
     * Prepare messages for AI
     */
    protected function prepareMessages(array $context, string $systemPrompt): array
    {
        $messages = [];

        // Build enhanced system prompt with product context
        $enhancedPrompt = $systemPrompt;

        // Add product context if available
        if (!empty($context['product_context']) && !empty($context['products_found'])) {
            // Ürün varsa, ürün listesini ekle
            $enhancedPrompt .= "\n\n" . $context['product_context'];
            $enhancedPrompt .= "\n\n⚡ ÖNEMLİ:";
            $enhancedPrompt .= "\n✅ Yukarıdaki ürünleri öner";
            $enhancedPrompt .= "\n✅ Markdown kullan (başlık: ##, liste: -, kalın: **)";
            $enhancedPrompt .= "\n✅ Link'leri markdown formatında ver: [Ürün Adı](/link)";
            $enhancedPrompt .= "\n❌ HTML kullanma!";
        } else {
            // Ürün yoksa welcome message kullan - ÇEŞİTLİ SEÇENEKLER
            $welcomeMessage = null;

            // Önce variations dene
            try {
                $welcomeVariations = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                    ->where('directive_key', 'welcome_variations')
                    ->where('is_active', true)
                    ->first();

                if ($welcomeVariations && $welcomeVariations->directive_value) {
                    $variations = json_decode($welcomeVariations->directive_value, true);
                    if (is_array($variations) && count($variations) > 0) {
                        $welcomeMessage = $variations[array_rand($variations)];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load welcome_variations', ['error' => $e->getMessage()]);
            }

            // Fallback to single welcome_message
            if (!$welcomeMessage) {
                try {
                    $directive = \App\Models\AITenantDirective::where('tenant_id', tenant('id'))
                        ->where('directive_key', 'welcome_message')
                        ->where('is_active', true)
                        ->first();

                    if ($directive) {
                        $welcomeMessage = $directive->directive_value;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not load welcome_message directive', ['error' => $e->getMessage()]);
                }
            }

            // Final fallback
            if (!$welcomeMessage) {
                $defaults = [
                    '🎯 Hangi ürünümüz ilginizi çekti?',
                    '💼 Size nasıl yardımcı olabilirim?',
                    '🚚 Hangi ürünü arıyorsunuz?',
                    '✨ Hoş geldiniz! Ne lazım?'
                ];
                $welcomeMessage = $defaults[array_rand($defaults)];
            }

            $enhancedPrompt .= "\n\nŞu anda ürün yok. Bu mesajı ver: \"{$welcomeMessage}\"";
        }

        // System prompt (first message)
        if ($enhancedPrompt) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $enhancedPrompt
            ];
        }

        // Conversation history
        if (!empty($context['conversation_history'])) {
            // If no products found, clean history from old product recommendations
            $hasProducts = !empty($context['products_found']);

            foreach ($context['conversation_history'] as $msg) {
                $content = $msg['content'];

                // If no products now, remove old product recommendations from history
                if (!$hasProducts && $msg['role'] === 'assistant') {
                    // Skip if this message contains product listings (markdown headers, prices, stock)
                    if (str_contains($content, '###') || str_contains($content, '**Fiyat:**') || str_contains($content, '**Stok:**')) {
                        continue; // Skip old product recommendations
                    }
                }

                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $content
                ];
            }
        }

        // Current user message
        $messages[] = [
            'role' => 'user',
            'content' => $context['user_message'] ?? ''
        ];

        return $messages;
    }

    /**
     * Get streaming channel name
     */
    protected function getStreamChannel(array $context): string
    {
        $tenantId = tenant('id') ?? 'central';
        $sessionId = $context['session_id'] ?? 'unknown';

        return "tenant.{$tenantId}.conversation.{$sessionId}";
    }
}
