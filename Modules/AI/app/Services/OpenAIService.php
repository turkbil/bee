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

    protected $provider;
    protected $providerId;

    public function __construct($config = null)
    {
        if ($config && is_array($config)) {
            // YENİ GLOBAL STANDART - Constructor'dan config al
            $this->providerId = $config['provider_id'] ?? null;
            $this->apiKey = $config['api_key'] ?? null;
            $this->baseUrl = $config['base_url'] ?? 'https://api.openai.com';
            $this->model = $config['model'] ?? 'gpt-4o-mini';
            
            // Base URL'yi ayarla
            if (!str_contains($this->baseUrl, '/v1')) {
                $this->baseUrl = rtrim($this->baseUrl, '/') . '/v1';
            }
        } else {
            // ESKİ FALLBACK - Compatibility için
            $this->loadProviderConfig();
        }
    }

    /**
     * Load AI provider configuration dynamically
     */
    protected function loadProviderConfig()
    {
        // First check tenant's AI provider
        $tenantId = session('tenant_id', 1);
        $tenant = \App\Models\Tenant::find($tenantId);
        
        $provider = null;
        
        // Check if tenant has specific AI provider
        if ($tenant && $tenant->ai_enabled && $tenant->default_ai_provider_id) {
            $provider = \Modules\AI\App\Models\AIProvider::where('id', $tenant->default_ai_provider_id)
                ->where('is_active', true)
                ->first();
        }
        
        // If no tenant provider, get default provider
        if (!$provider) {
            $provider = \Modules\AI\App\Models\AIProvider::where('is_default', true)
                ->where('is_active', true)
                ->first();
        }
        
        // If still no provider, get provider with highest priority (fallback)
        if (!$provider) {
            $provider = \Modules\AI\App\Models\AIProvider::where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();
        }
        
        // If no active provider found, throw exception
        if (!$provider) {
            throw new \Exception('No active AI provider found');
        }
        
        // Set provider configuration
        $this->apiKey = $provider->api_key;
        $this->baseUrl = rtrim($provider->base_url, '/');
        $this->model = $provider->default_model;
        
        // Adjust URL for different providers
        if ($provider->name === 'deepseek') {
            // DeepSeek uses same OpenAI-compatible endpoint
            $this->baseUrl = $this->baseUrl . '/v1';
        } elseif ($provider->name === 'openai') {
            // OpenAI URL is already correct
            if (!str_contains($this->baseUrl, '/v1')) {
                $this->baseUrl = $this->baseUrl . '/v1';
            }
        }
        
        Log::info('AI Provider configured', [
            'provider' => $provider->name,
            'tenant_id' => $tenantId,
            'model' => $this->model,
            'base_url' => $this->baseUrl
        ]);
    }

    /**
     * GERÇEK STREAMING completion generation
     */
    public function generateCompletionStream($messages, ?callable $streamCallback = null, $options = [])
    {
        $apiStartTime = microtime(true);
        Log::info('🚀 OpenAI API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'model' => $this->model,
            'api_url' => $this->baseUrl . '/chat/completions'
        ]);

        try {
            // Defensive: messages parametresi string gelirse array'e çevir
            if (is_string($messages)) {
                $messages = [
                    ['role' => 'user', 'content' => $messages]
                ];
            }
            
            // Messages array olmalı
            if (!is_array($messages)) {
                throw new \InvalidArgumentException('Messages must be an array');
            }
            
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
            $isGPT5 = $this->isGPT5Model($this->model);

            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => !$isGPT5, // GPT-5 doesn't support streaming without verified org
            ];

            // Add stream_options only if streaming is enabled
            if (!$isGPT5) {
                $payload['stream_options'] = [
                    'include_usage' => true // Token usage bilgisi için
                ];
            }

            // Temperature support check (GPT-5 doesn't support custom temperature)
            if (!$isGPT5) {
                $payload['temperature'] = $options['temperature'] ?? 0.7;
            }

            // max_tokens - Default 4000 (rate limit aşımını önlemek için)
            // Options'da özel değer varsa onu kullan
            $payload['max_tokens'] = $options['max_tokens'] ?? 4000;

            Log::info('🎯 OpenAI Request Payload', [
                'model' => $payload['model'],
                'max_tokens' => $payload['max_tokens'] ?? 'unlimited',
                'temperature' => $payload['temperature'] ?? 'default (not set for GPT-5)',
                'messages_count' => count($payload['messages'])
            ]);

            // ✨ STREAMING HTTP REQUEST
            $fullResponse = '';
            $inputTokens = 0;
            $outputTokens = 0;
            $totalTokens = 0;
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(600)->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                // GPT-5 non-streaming response (JSON)
                if ($isGPT5) {
                    $data = $response->json();

                    $fullResponse = $data['choices'][0]['message']['content'] ?? '';

                    // Token usage
                    if (isset($data['usage'])) {
                        $inputTokens = $data['usage']['prompt_tokens'] ?? 0;
                        $outputTokens = $data['usage']['completion_tokens'] ?? 0;
                        $totalTokens = $data['usage']['total_tokens'] ?? 0;
                    }
                } else {
                    // Streaming response (SSE format)
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
     *
     * @param string|array $messages - User message (string) veya messages array
     * @param bool $stream
     * @param array $options - custom_prompt, conversation_history, temperature vb.
     */
    public function ask($messages, $stream = false, $options = [])
    {
        // 🧠 CONVERSATION MEMORY: Build full messages array
        $fullMessages = [];

        // 1. System prompt ekle (varsa)
        if (!empty($options['custom_prompt'])) {
            $fullMessages[] = [
                'role' => 'system',
                'content' => $options['custom_prompt']
            ];
        }

        // 2. Conversation history ekle (varsa)
        if (!empty($options['conversation_history']) && is_array($options['conversation_history'])) {
            foreach ($options['conversation_history'] as $historyMsg) {
                $fullMessages[] = $historyMsg;
            }
        }

        // 3. Current user message ekle
        if (is_string($messages)) {
            $fullMessages[] = [
                'role' => 'user',
                'content' => $messages
            ];
        } elseif (is_array($messages)) {
            // Eğer messages zaten array ise, direkt kullan
            $fullMessages = array_merge($fullMessages, $messages);
        }

        // Streaming varsa generateCompletionStream kullan
        if ($stream) {
            return $this->generateCompletionStream($fullMessages, null, $options);
        }

        // Normal request - tam response döndür (token bilgileri ile)
        $result = $this->generateCompletionStream($fullMessages, null, $options);

        // String response dön (compatibility için)
        return $result['response'] ?? '';
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
     * GLOBAL STANDART - Provider bilgilerini al
     */
    public function getProviderInfo()
    {
        return [
            'provider_id' => $this->providerId,
            'api_key_set' => !empty($this->apiKey),
            'base_url' => $this->baseUrl,
            'model' => $this->model
        ];
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
     * Check if model is GPT-5 (which doesn't support custom temperature)
     */
    private function isGPT5Model($modelName)
    {
        return str_contains(strtolower($modelName), 'gpt-5');
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