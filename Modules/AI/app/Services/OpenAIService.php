<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $lastFullResponse = '';

    public function __construct()
    {
        $this->apiKey = 'sk-Rd0uAFfpiAcfdxillkFM1mV0NWxihzz2L4ARj6k2tjT3BlbkFJ6V0IbyeIq53gOxZa31u1xOq94W69xoacMELOL7CIEA';
        $this->baseUrl = 'https://api.openai.com/v1';
        $this->model = 'gpt-4o-mini'; // Try different model
    }

    /**
     * STREAMING completion generation
     */
    public function generateCompletionStream($messages, ?callable $streamCallback = null)
    {
        $apiStartTime = microtime(true);
        Log::info('🚀 OpenAI API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'model' => $this->model,
            'api_url' => $this->baseUrl . '/chat/completions'
        ]);

        try {
            // Non-streaming request payload
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
                'stream' => false,
            ];

            // ✨ NORMAL HTTP REQUEST
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                $fullResponse = $responseData['choices'][0]['message']['content'] ?? '';
                $tokensUsed = $responseData['usage']['total_tokens'] ?? 0;
                
                Log::info('⚡ OpenAI yanıt alındı', [
                    'response_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                    'response_length' => strlen($fullResponse),
                    'tokens_used' => $tokensUsed
                ]);
            } else {
                throw new \Exception('OpenAI API hatası: ' . $response->status() . ' - ' . $response->body());
            }

            $totalTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            Log::info('🏁 OpenAI streaming tamamlandı', [
                'total_time_ms' => $totalTime,
                'response_length' => strlen($fullResponse),
                'tokens_used' => $tokensUsed
            ]);

            return [
                'response' => $fullResponse,
                'tokens_used' => $tokensUsed,
                'success' => true,
                'provider' => 'openai',
                'model' => $this->model,
                'time_ms' => $totalTime
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI streaming API hatası: ' . $e->getMessage());
            return [
                'response' => null,
                'tokens_used' => 0,
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'openai'
            ];
        }
    }

    /**
     * Test için basit mesaj gönder
     */
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
        
        // OpenAI token tahmini (basit hesaplama)
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