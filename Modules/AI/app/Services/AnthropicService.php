<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $lastFullResponse = '';

    public function __construct()
    {
        $this->apiKey = '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';
        $this->baseUrl = 'https://api.anthropic.com';
        $this->model = 'claude-3-haiku-20240307';
    }

    /**
     * Claude API completion generation
     */
    public function generateCompletionStream($messages, ?callable $streamCallback = null)
    {
        $apiStartTime = microtime(true);
        Log::info('🚀 Claude API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'model' => $this->model,
            'api_url' => $this->baseUrl . '/v1/messages'
        ]);

        try {
            // Claude API formatına uygun system ve user message ayrımı
            $systemMessage = '';
            $userMessages = [];
            
            foreach ($messages as $message) {
                if ($message['role'] === 'system') {
                    $systemMessage .= $message['content'] . "\n";
                } else {
                    $userMessages[] = [
                        'role' => $message['role'],
                        'content' => $message['content']
                    ];
                }
            }

            $payload = [
                'model' => $this->model,
                'max_tokens' => 800,
                'messages' => $userMessages
            ];

            // System message varsa ekle
            if (!empty($systemMessage)) {
                $payload['system'] = trim($systemMessage);
            }

            // ✨ Claude API Request
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->timeout(120)->post($this->baseUrl . '/v1/messages', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                $fullResponse = $responseData['content'][0]['text'] ?? '';
                $tokensUsed = ($responseData['usage']['input_tokens'] ?? 0) + ($responseData['usage']['output_tokens'] ?? 0);
                
                Log::info('⚡ Claude yanıt alındı', [
                    'response_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                    'response_length' => strlen($fullResponse),
                    'tokens_used' => $tokensUsed
                ]);
            } else {
                throw new \Exception('Claude API hatası: ' . $response->status() . ' - ' . $response->body());
            }

            $totalTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            Log::info('🏁 Claude streaming tamamlandı', [
                'total_time_ms' => $totalTime,
                'response_length' => strlen($fullResponse),
                'tokens_used' => $tokensUsed
            ]);

            return [
                'response' => $fullResponse,
                'tokens_used' => $tokensUsed,
                'success' => true,
                'provider' => 'anthropic',
                'model' => $this->model,
                'time_ms' => $totalTime
            ];

        } catch (\Exception $e) {
            Log::error('Claude API hatası: ' . $e->getMessage());
            return [
                'response' => null,
                'tokens_used' => 0,
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'anthropic'
            ];
        }
    }

    /**
     * AIService uyumlu ask metodu
     */
    public function ask($messages, $stream = false)
    {
        // Streaming varsa generateCompletionStream kullan
        if ($stream) {
            return $this->generateCompletionStream($messages);
        }

        // Normal request
        $result = $this->generateCompletionStream($messages);
        return $result['response'] ?? null;
    }

    /**
     * Token tahminleme metodu
     */
    public function estimateTokens($messages)
    {
        $text = '';
        foreach ($messages as $message) {
            $text .= ($message['content'] ?? '') . ' ';
        }
        
        // Claude token tahmini (basit hesaplama)
        return intval(strlen($text) / 4);
    }

    /**
     * API Key setter
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Base URL setter
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Model setter
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Test için basit mesaj gönder
     */
    public function testMessage($prompt = "Merhaba, nasılsın?")
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Sen yardımcı bir AI asistanısın. Kısa ve samimi yanıtlar ver.'
            ],
            [
                'role' => 'user', 
                'content' => $prompt
            ]
        ];

        return $this->generateCompletionStream($messages);
    }
}