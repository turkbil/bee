<?php

/**
 * 🌊 STREAMING CHAT ENDPOINT
 *
 * Bu method PublicAIController.php dosyasının SONUNA eklenecek
 * Satır 2180'den sonraya ekle
 */

    /**
     * 🌊 Shop Assistant Chat - STREAMING VERSION
     *
     * Server-Sent Events (SSE) ile streaming response
     * ChatGPT benzeri typing effect
     *
     * @param Request $request
     * @return Response (SSE stream)
     */
    public function shopAssistantChatStream(Request $request)
    {
        // Validation (aynı)
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
            'product_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'page_slug' => 'nullable|string|max:255',
        ]);

        // Headers for SSE
        return response()->stream(function () use ($validated, $request) {

            // Set headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no'); // Nginx için

            // Conversation logic (shopAssistantChat ile aynı)
            $sessionId = $validated['session_id'] ?? 'guest_' . uniqid();

            $conversation = \Modules\AI\App\Models\AIConversation::firstOrCreate([
                'session_id' => $sessionId,
            ], [
                'tenant_id' => tenant('id'),
                'user_id' => auth()->id(),
                'context_type' => 'shop_assistant',
                'context_id' => $validated['product_id'] ?? null,
            ]);

            // Save user message
            $conversation->messages()->create([
                'role' => 'user',
                'content' => $validated['message'],
            ]);

            // Build context (aynı)
            $contextOptions = [
                'product_id' => $validated['product_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'page_slug' => $validated['page_slug'] ?? null,
            ];

            $aiContext = app(\App\Services\AI\Context\ModuleContextOrchestrator::class)->buildAIContext(
                $validated['message'],
                $contextOptions
            );

            // Conversation history (10 mesaj)
            $conversationHistory = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();

            // Build prompt
            $optimizedPromptService = new \Modules\AI\App\Services\OptimizedPromptService();
            $enhancedSystemPrompt = $optimizedPromptService->getFullPrompt($aiContext, $conversationHistory);

            // Prepare messages for OpenAI
            $messages = [
                ['role' => 'system', 'content' => $enhancedSystemPrompt]
            ];

            foreach ($conversationHistory as $historyMsg) {
                $messages[] = $historyMsg;
            }

            // Get OpenAI service
            $provider = \Modules\AI\App\Models\AIProvider::where('name', 'openai')
                ->where('is_active', true)
                ->first();

            $service = new \Modules\AI\App\Services\OpenAIService([
                'provider_id' => $provider->id,
                'api_key' => $provider->api_key,
                'base_url' => $provider->base_url,
                'model' => 'gpt-4o-mini',
            ]);

            // Streaming callback
            $fullResponse = '';
            $streamCallback = function($chunk) use (&$fullResponse) {
                $fullResponse .= $chunk;

                // Send SSE data
                echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";

                // Flush immediately
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            };

            // Start streaming
            echo "data: " . json_encode(['event' => 'start']) . "\n\n";
            flush();

            try {
                // Call streaming API
                $service->generateCompletionStream($messages, $streamCallback, [
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ]);

                // Save assistant message
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'model' => 'gpt-4o-mini',
                ]);

                // Send end event
                echo "data: " . json_encode([
                    'event' => 'end',
                    'session_id' => $sessionId,
                    'conversation_id' => $conversation->id,
                ]) . "\n\n";
                flush();

            } catch (\Exception $e) {
                // Send error event
                echo "data: " . json_encode([
                    'event' => 'error',
                    'error' => 'Bir hata oluştu. Lütfen tekrar deneyin.'
                ]) . "\n\n";
                flush();
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
