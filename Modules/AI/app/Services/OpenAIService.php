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
     * GERÇEK STREAMING completion generation
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
            // DEBUG: Messages'ı log'la
            Log::info('🔍 OpenAI Messages Debug', [
                'messages_count' => count($messages),
                'messages' => array_map(function($msg) {
                    return [
                        'role' => $msg['role'],
                        'content_type' => gettype($msg['content']),
                        'content_preview' => is_string($msg['content']) ? substr($msg['content'], 0, 100) : 'NOT_STRING'
                    ];
                }, $messages)
            ]);
            
            // GERÇEK STREAMING REQUEST PAYLOAD
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
                'stream' => true, // ✨ STREAMING AÇIK!
                'stream_options' => [
                    'include_usage' => true // Token usage bilgisi için
                ]
            ];

            // ✨ STREAMING HTTP REQUEST
            $fullResponse = '';
            $inputTokens = 0;
            $outputTokens = 0;
            $totalTokens = 0;
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                // SSE formatını parse et
                $lines = explode("\n", $response->body());
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    
                    // SSE veri satırlarını filtrele
                    if (strpos($line, 'data: ') === 0) {
                        $jsonData = substr($line, 6); // "data: " kısmını çıkar
                        
                        // [DONE] kontrolü
                        if ($jsonData === '[DONE]') {
                            break;
                        }
                        
                        // JSON parse et
                        $data = json_decode($jsonData, true);
                        if (!$data) continue;
                        
                        // Content chunk'ını al
                        $chunk = $data['choices'][0]['delta']['content'] ?? '';
                        
                        if (!empty($chunk)) {
                            $fullResponse .= $chunk;
                            
                            // Callback varsa çağır (Frontend'e gönder)
                            if ($streamCallback && is_callable($streamCallback)) {
                                call_user_func($streamCallback, $chunk);
                            }
                        }
                        
                        // Token usage bilgilerini al (varsa)
                        if (isset($data['usage'])) {
                            $inputTokens = $data['usage']['prompt_tokens'] ?? 0;
                            $outputTokens = $data['usage']['completion_tokens'] ?? 0;
                            $totalTokens = $data['usage']['total_tokens'] ?? 0;
                        }
                    }
                }
                
                Log::info('⚡ OpenAI yanıt alındı', [
                    'response_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                    'response_length' => strlen($fullResponse),
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'total_tokens' => $totalTokens
                ]);
            } else {
                throw new \Exception('OpenAI API hatası: ' . $response->status() . ' - ' . $response->body());
            }

            $totalTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            Log::info('🏁 OpenAI streaming tamamlandı', [
                'total_time_ms' => $totalTime,
                'response_length' => strlen($fullResponse),
                'total_tokens' => $totalTokens
            ]);

            return [
                'response' => $fullResponse,
                'tokens_used' => $totalTokens,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'success' => true,
                'provider' => 'openai',
                'model' => $this->model,
                'time_ms' => $totalTime,
                'usage_details' => [
                    'prompt_tokens' => $inputTokens,
                    'completion_tokens' => $outputTokens,
                    'total_tokens' => $totalTokens
                ]
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI streaming API hatası: ' . $e->getMessage());
            return [
                'response' => 'Üzgünüm, bir hata oluştu: ' . $e->getMessage(),
                'tokens_used' => 0,
                'input_tokens' => 0,
                'output_tokens' => 0,
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

        // Normal request - tam response döndür (token bilgileri ile)
        return $this->generateCompletionStream($messages);
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