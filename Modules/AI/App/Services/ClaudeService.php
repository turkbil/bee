<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Claude (Anthropic) AI Service
 * OpenAI-compatible wrapper for Claude API
 */
class ClaudeService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $providerId;

    public function __construct($config = null)
    {
        if ($config && is_array($config)) {
            $this->providerId = $config['provider_id'] ?? null;
            $this->apiKey = $config['api_key'] ?? null;
            $this->baseUrl = $config['base_url'] ?? 'https://api.anthropic.com';
            $this->model = $config['model'] ?? 'claude-3-haiku-20240307';
        }
    }

    /**
     * Ask Claude with conversation history support
     *
     * @param string|array $messages
     * @param array $options - custom_prompt, conversation_history, temperature
     * @return string
     */
    public function ask($messages, $options = [])
    {
        // 🧠 Build full messages array
        $fullMessages = [];

        // 1. System prompt (Claude uses separate system field)
        $systemPrompt = $options['custom_prompt'] ?? 'Sen yardımcı bir AI asistanısın.';

        // 2. Conversation history
        if (!empty($options['conversation_history']) && is_array($options['conversation_history'])) {
            foreach ($options['conversation_history'] as $historyMsg) {
                $fullMessages[] = $historyMsg;
            }
        }

        // 3. Current message
        if (is_string($messages)) {
            $fullMessages[] = [
                'role' => 'user',
                'content' => $messages
            ];
        } elseif (is_array($messages)) {
            $fullMessages = array_merge($fullMessages, $messages);
        }

        // Call Claude API
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
                ->timeout(120)
                ->post($this->baseUrl . '/v1/messages', [
                    'model' => $this->model,
                    'max_tokens' => $options['max_tokens'] ?? 4096,
                    'temperature' => $options['temperature'] ?? 0.7,
                    'system' => $systemPrompt,
                    'messages' => $fullMessages,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['content'][0]['text'] ?? '';

                Log::info('✅ Claude API response received', [
                    'model' => $this->model,
                    'response_length' => strlen($content),
                    'tokens' => $data['usage']['output_tokens'] ?? 0,
                ]);

                return $content;
            } else {
                throw new \Exception('Claude API error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('❌ Claude API error', [
                'error' => $e->getMessage(),
                'model' => $this->model,
            ]);

            throw $e;
        }
    }
}
