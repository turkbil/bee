<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $lastFullResponse = '';

    public function __construct()
    {
        $this->apiKey = config('deepseek.api_key');
        $this->baseUrl = config('deepseek.api_url', 'https://api.deepseek.com/v1');
        $this->model = config('deepseek.model', 'deepseek-chat');
    }

    public function generateCompletion($message, $conversationHistory = [])
    {
        $messages = $this->formatMessages($conversationHistory);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => config('deepseek.temperature', 0.7),
                'max_tokens' => config('deepseek.max_tokens', 2000),
                'stream' => false,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    return [
                        'content' => $responseData['choices'][0]['message']['content'],
                        'full_response' => $responseData,
                    ];
                }
            }
            
            Log::error('DeepSeek API hatası', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            
            return [
                'content' => 'Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('DeepSeek API istek hatası: ' . $e->getMessage(), ['exception' => $e]);
            
            return [
                'content' => 'Üzgünüm, bir hata oluştu: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }
    
    public function streamCompletion($message, $conversationHistory = [], callable $callback = null)
    {
        $messages = $this->formatMessages($conversationHistory);
        $this->lastFullResponse = '';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'text/event-stream',
            ])->timeout(60)->send('POST', $this->baseUrl . '/chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => config('deepseek.temperature', 0.7),
                    'max_tokens' => config('deepseek.max_tokens', 2000),
                    'stream' => true,
                ],
            ]);
            
            if ($response->successful()) {
                $buffer = '';
                $responseBody = $response->getBody();
                
                while (!$responseBody->eof()) {
                    $line = $this->readLine($responseBody);
                    
                    if (!empty($line)) {
                        if (strpos($line, 'data:') === 0) {
                            $jsonData = trim(substr($line, 5));
                            
                            if ($jsonData === '[DONE]') {
                                break;
                            }
                            
                            try {
                                $data = json_decode($jsonData, true);
                                
                                if (isset($data['choices'][0]['delta']['content'])) {
                                    $content = $data['choices'][0]['delta']['content'];
                                    $this->lastFullResponse .= $content;
                                    
                                    if ($callback) {
                                        $callback($content);
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('JSON ayrıştırma hatası: ' . $e->getMessage(), [
                                    'line' => $line,
                                    'exception' => $e,
                                ]);
                            }
                        }
                    }
                }
            } else {
                Log::error('DeepSeek Stream API hatası', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                
                if ($callback) {
                    $callback('Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.');
                }
            }
        } catch (\Exception $e) {
            Log::error('DeepSeek Stream API istek hatası: ' . $e->getMessage(), ['exception' => $e]);
            
            if ($callback) {
                $callback('Üzgünüm, bir hata oluştu: ' . $e->getMessage());
            }
        }
    }
    
    public function getLastFullResponse()
    {
        return $this->lastFullResponse;
    }
    
    protected function formatMessages($conversationHistory)
    {
        $messages = [];
        
        // Sistem mesajı ekle
        $messages[] = [
            'role' => 'system',
            'content' => config('openai.system_message', 'Sen yardımcı bir asistansın. Türkçe olarak cevap ver. Yanıtlarında markdown formatlaması yapma, ** işaretleri ve ## gibi sembolleri olduğu gibi metinde göster. Yanıtlarında hiçbir şekilde formatlamaları (kalın, italik, başlık) uygulama.'),
        ];
        
        // Konuşma geçmişini ekle
        foreach ($conversationHistory as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }
        
        return $messages;
    }
    
    protected function readLine($stream)
    {
        $buffer = '';
        while (!$stream->eof()) {
            $byte = $stream->read(1);
            if ($byte === "\n") {
                break;
            }
            $buffer .= $byte;
        }
        return $buffer;
    }
}