<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class DeepSeekService
{
    protected $apiKey;
    protected $model;
    protected $temperature;
    protected $maxTokens;
    protected $apiBaseUrl = 'https://api.deepseek.com/v1';

    /**
     * Constructor
     * 
     * @param string|null $apiKey
     * @param string|null $model
     * @param float|null $temperature
     * @param int|null $maxTokens
     */
    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
        ?float $temperature = null,
        ?int $maxTokens = null
    ) {
        // Veritabanından ayarları al
        $settings = $this->getSettings();
        
        $this->apiKey = $apiKey ?? $settings->api_key ?? null;
        $this->model = $model ?? $settings->model ?? 'deepseek-chat';
        $this->temperature = $temperature ?? $settings->temperature ?? 0.7;
        $this->maxTokens = $maxTokens ?? $settings->max_tokens ?? 4096;
    }

    /**
     * Modele soru sor
     *
     * @param array $messages
     * @param bool $stream
     * @return string|null|\Closure
     */
    public function ask(array $messages, bool $stream = false)
    {
        try {
            // Stream talebi ise HTTP yöntemini kullan
            if ($stream) {
                return $this->askWithStream($messages);
            }
            
            // Normal yanıt talebi ise
            return $this->askWithHttp($messages);
        } catch (\Exception $e) {
            Log::error('DeepSeek API istek hatası: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Stream destekli istek
     * 
     * @param array $messages
     * @return \Closure
     */
    protected function askWithStream(array $messages): \Closure
    {
        return function ($callback) use ($messages) {
            try {
                $client = new Client();
                
                $response = $client->post($this->apiBaseUrl . '/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'text/event-stream'
                    ],
                    'json' => [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => $this->temperature,
                        'max_tokens' => $this->maxTokens,
                        'stream' => true
                    ],
                    'stream' => true
                ]);
                
                $body = $response->getBody();
                $buffer = '';
                
                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    if (empty($chunk)) continue;
                    
                    $buffer .= $chunk;
                    
                    // SSE formatındaki veriyi parçala
                    $lines = explode("\n\n", $buffer);
                    $bufferRemains = '';
                    
                    foreach ($lines as $i => $line) {
                        // Son satırı bufferda tut (tamamlanmamış olabilir)
                        if ($i === count($lines) - 1 && substr($buffer, -2) !== "\n\n") {
                            $bufferRemains = $line;
                            continue;
                        }
                        
                        // "data:" ile başlayan satırları işle
                        if (strpos($line, 'data:') === 0) {
                            $data = substr($line, 5);
                            
                            // DONE mesajını atla
                            if (trim($data) === '[DONE]') {
                                continue;
                            }
                            
                            try {
                                $json = json_decode(trim($data), true);
                                
                                // İçerik varsa callback ile döndür
                                if (isset($json['choices'][0]['delta']['content'])) {
                                    $content = $json['choices'][0]['delta']['content'];
                                    $callback($content);
                                }
                            } catch (\Exception $e) {
                                Log::error('Stream JSON parse hatası: ' . $e->getMessage());
                                continue;
                            }
                        }
                    }
                    
                    // Kalan buffer'ı güncelle
                    $buffer = $bufferRemains;
                }
            } catch (\Exception $e) {
                Log::error('Stream işleme hatası: ' . $e->getMessage());
                throw $e;
            }
        };
    }

    /**
     * Standart HTTP isteği
     * 
     * @param array $messages
     * @return string|null
     */
    protected function askWithHttp(array $messages): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiBaseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        } else {
            Log::error('DeepSeek API hatası: ' . $response->body());
            return null;
        }
    }

    /**
     * Mesaj geçmişinden token sayısını tahmin et
     *
     * @param array $messages
     * @return int
     */
    public function estimateTokens(array $messages): int
    {
        $totalChars = 0;
        foreach ($messages as $message) {
            $totalChars += strlen($message['content']);
        }
        
        // Kaba bir tahmin: her 4 karakter için 1 token
        return ceil($totalChars / 4);
    }

    /**
     * Tenant ID'ye göre ayarları yükle
     *
     * @param int|null $tenantId
     * @return self
     */
    public static function forTenant(?int $tenantId = null): self
    {
        // Root tenant için ayarları kullan (tenant_id = 1)
        $settings = Cache::remember('ai_settings', now()->addMinutes(30), function () {
            return Setting::where('tenant_id', 1)->first();
        });

        if (!$settings) {
            return new self();
        }

        return new self(
            $settings->api_key,
            $settings->model,
            $settings->temperature,
            $settings->max_tokens
        );
    }
    
    /**
     * Ayarları veritabanından al
     * 
     * @return object|null
     */
    protected function getSettings()
    {
        return Cache::remember('ai_settings', now()->addMinutes(30), function () {
            return Setting::where('tenant_id', 1)->first() ?? new \stdClass();
        });
    }

    /**
     * Konuşma mesajlarını formatla
     *
     * @param \Modules\AI\App\Models\Conversation $conversation
     * @param string|null $systemPrompt
     * @return array
     */
    public function formatConversationMessages($conversation, ?string $systemPrompt = null): array
    {
        $messages = [];

        // Sistem promptunu ekle
        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        } elseif ($conversation->prompt_id) {
            $prompt = $conversation->prompt;
            if ($prompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $prompt->content
                ];
            }
        }

        // Konuşma geçmişini ekle (son 10 mesaj)
        $conversationMessages = $conversation->messages()
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        foreach ($conversationMessages as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content
            ];
        }

        return $messages;
    }
}