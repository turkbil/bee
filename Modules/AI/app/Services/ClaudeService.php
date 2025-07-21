<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    
    public function __construct()
    {
        $this->apiKey = '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';
        $this->baseUrl = 'https://api.anthropic.com/v1';
        $this->model = 'claude-3-haiku-20240307'; // Hızlı model
    }
    
    public function generateCompletionStream($messages, ?callable $streamCallback = null)
    {
        $startTime = microtime(true);
        
        Log::info('🚀 Claude API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'model' => $this->model,
            'api_url' => $this->baseUrl . '/messages'
        ]);
        
        try {
            // Claude API'de system mesajları ayrı parameter olarak gönderilmeli
            $systemMessage = '';
            $userMessages = [];
            
            foreach ($messages as $message) {
                if (isset($message['role']) && $message['role'] === 'system') {
                    $systemMessage .= $message['content'] . "\n";
                } else {
                    $userMessages[] = $message;
                }
            }
            
            // Non-streaming request payload (Claude API format)
            $payload = [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.3,
                'messages' => $userMessages, // Sadece user/assistant mesajları
                'stream' => false, // Non-streaming for testing
            ];
            
            // System mesajı varsa ekle
            if (!empty(trim($systemMessage))) {
                $payload['system'] = trim($systemMessage);
            }
            
            // HTTP REQUEST
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->timeout(120)->post($this->baseUrl . '/messages', $payload);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Claude API token usage bilgilerini al
                $inputTokens = $responseData['usage']['input_tokens'] ?? 0;
                $outputTokens = $responseData['usage']['output_tokens'] ?? 0;
                $totalTokens = $inputTokens + $outputTokens;
                
                Log::info('✅ Claude API başarılı', [
                    'duration_ms' => $duration,
                    'status' => $response->status(),
                    'response_length' => strlen($responseData['content'][0]['text'] ?? ''),
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'total_tokens' => $totalTokens
                ]);
                
                return [
                    'response' => $responseData['content'][0]['text'] ?? '',
                    'tokens_used' => $totalTokens,
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'success' => true,
                    'duration_ms' => $duration,
                    'usage_details' => $responseData['usage'] ?? []
                ];
            } else {
                $errorData = $response->json();
                Log::error('Claude API hatası: ' . $response->status() . ' - ' . json_encode($errorData));
                
                return [
                    'response' => null,
                    'tokens_used' => 0,
                    'success' => false,
                    'error' => $errorData,
                    'duration_ms' => $duration
                ];
            }
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('Claude streaming API hatası: ' . $e->getMessage());
            
            return [
                'response' => null,
                'tokens_used' => 0,
                'success' => false,
                'error' => $e->getMessage(),
                'duration_ms' => $duration
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

    public function testMessage($prompt = "Merhaba, nasılsın?")
    {
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];
        
        $result = $this->generateCompletionStream($messages);
        
        Log::info('Claude Test Sonucu', [
            'success' => $result['success'],
            'duration_ms' => $result['duration_ms'],
            'response_preview' => substr($result['response'] ?? '', 0, 100)
        ]);
        
        return $result;
    }
}