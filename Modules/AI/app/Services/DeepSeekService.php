<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DeepSeekService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $lastFullResponse = '';
    protected $safeMode = false;

    public function __construct($safeMode = false)
    {
        $this->safeMode = $safeMode;
        
        if (!$this->safeMode) {
            $this->loadApiSettings();
        } else {
            // Safe Mode - varsayılan değerler
            $this->apiKey = '';
            $this->model = 'deepseek-chat';
        }
        
        $this->baseUrl = 'https://api.deepseek.com/v1';
    }

    protected function loadApiSettings()
    {
        try {
            // Merkezi AI helper'ı kullan
            $this->apiKey = ai_get_api_key();
            $this->model = ai_get_model();
            
            if (empty($this->apiKey)) {
                Log::warning('API anahtarı bulunamadı. Lütfen admin panelden AI ayarlarını yapılandırın.');
            }
        } catch (\Exception $e) {
            // Hata durumunda boş değerler
            Log::error('DeepSeekService ayarları yüklenirken hata: ' . $e->getMessage());
            $this->apiKey = '';
            $this->model = 'deepseek-chat';
        }
    }

    protected function getGlobalSettings()
    {
        // Merkezi AI helper'ı kullan
        return ai_get_settings();
    }

    public static function forTenant(?int $tenantId = null): self
    {
        // Tenant ID'yi sadece log amaçlı kullanacağız, API anahtarını değiştirmeyeceğiz
        $service = new self();
        return $service;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function testConnection()
    {
        if (empty($this->apiKey)) {
            return false;
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/models');
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('API bağlantı hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * STREAMING completion generation
     */
    public function generateCompletionStream($messages, ?callable $streamCallback = null)
    {
        try {
            // Merkezi helper ile API anahtarını kontrol et
            if (!$this->apiKey) {
                throw new \Exception('API anahtarı bulunamadı. Lütfen admin panelden AI ayarlarını yapılandırın.');
            }

            // Stream request payload
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'stream' => true, // ✨ STREAMING AKTIF
            ];

            // ✨ STREAMING HTTP REQUEST
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->stream('POST', $this->baseUrl . '/chat/completions', $payload);

            $fullResponse = '';
            $tokensUsed = 0;

            // Stream'i işle
            foreach ($response as $chunk) {
                $lines = explode("\n", $chunk);
                
                foreach ($lines as $line) {
                    if (empty($line) || !str_starts_with($line, 'data: ')) continue;
                    
                    $data = substr($line, 6);
                    
                    if ($data === '[DONE]') break;
                    
                    $json = json_decode($data, true);
                    
                    if (isset($json['choices'][0]['delta']['content'])) {
                        $content = $json['choices'][0]['delta']['content'];
                        $fullResponse .= $content;
                        
                        // Stream callback'i çağır
                        if ($streamCallback) {
                            $streamCallback($content);
                        }
                    }
                    
                    // Token kullanımını hesapla
                    if (isset($json['usage']['total_tokens'])) {
                        $tokensUsed = $json['usage']['total_tokens'];
                    }
                }
            }

            return [
                'response' => $fullResponse,
                'tokens_used' => $tokensUsed,
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('DeepSeek streaming API hatası: ' . $e->getMessage());
            return [
                'response' => null,
                'tokens_used' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateCompletion($message, $conversationHistory = [])
    {
        $messages = $this->formatMessages($conversationHistory, null, $message);

        try {
            // Merkezi helper ile API anahtarını kontrol et
            $apiKey = ai_get_api_key();
            if (empty($apiKey)) {
                Log::error('API anahtarı bulunamadı - ai_get_api_key() in generateCompletion');
                return [
                    'content' => 'API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.',
                    'error' => 'API anahtarı bulunamadı',
                ];
            }
            
            // Local api key'i güncelle
            $this->apiKey = $apiKey;
            
            $settings = $this->getGlobalSettings();
            
            $response = Http::timeout(900)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => $settings ? $settings->temperature : 0.7,
                'max_tokens' => $settings ? $settings->max_tokens : 2000,
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
            
            Log::error('API hatası', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            
            return [
                'content' => 'Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('API istek hatası: ' . $e->getMessage(), ['exception' => $e]);
            
            return [
                'content' => 'Üzgünüm, bir hata oluştu: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }
        
    public function streamCompletion($message, $conversationHistory = [], ?callable $callback = null, $promptId = null)
    {
        $apiStartTime = microtime(true);
        Log::info('🚀 DeepSeek API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'message_length' => strlen($message),
            'api_url' => $this->baseUrl . '/chat/completions',
            'model' => $this->model,
            'has_api_key' => !empty($this->apiKey)
        ]);
        
        $messages = $this->formatMessages($conversationHistory, $promptId); // Prompt ID'yi formatMessages metoduna gönder
        $this->lastFullResponse = '';

        try {
            // Merkezi helper ile API anahtarını kontrol et
            $apiKey = ai_get_api_key();
            if (empty($apiKey)) {
                Log::error('API anahtarı bulunamadı - ai_get_api_key() in streamCompletion');
                if ($callback) {
                    $callback('API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.');
                }
                return;
            }
            
            // Local api key'i güncelle
            $this->apiKey = $apiKey;
            
            $settings = $this->getGlobalSettings();
            
            $response = Http::timeout(900)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'text/event-stream',
            ])->send('POST', $this->baseUrl . '/chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $settings ? $settings->temperature : 0.7,
                    'max_tokens' => $settings ? $settings->max_tokens : 2000,
                    'stream' => true,
                ],
            ]);
            
            if ($response->successful()) {
                Log::info('📡 DeepSeek API bağlantısı kuruldu', [
                    'connection_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                    'status' => $response->status()
                ]);
                
                $buffer = '';
                $responseBody = $response->getBody();
                $firstChunkReceived = false;
                
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
                                    
                                    // İlk chunk timing'i
                                    if (!$firstChunkReceived) {
                                        Log::info('⚡ İlk AI chunk alındı', [
                                            'first_chunk_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                                            'content_length' => strlen($content)
                                        ]);
                                        $firstChunkReceived = true;
                                    }
                                    
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
                Log::error('Stream API hatası', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                
                if ($callback) {
                    $callback('Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.');
                }
            }
        } catch (\Exception $e) {
            Log::error('Stream API istek hatası: ' . $e->getMessage(), ['exception' => $e]);
            
            if ($callback) {
                $callback('Üzgünüm, bir hata oluştu: ' . $e->getMessage());
            }
        }
    }

    protected function formatMessages($conversationHistory, $promptId = null, $currentMessage = null)
    {
        $messages = [];
        
        Log::info('Mesajlar formatlanıyor', [
            'message_count' => count($conversationHistory),
            'prompt_id' => $promptId
        ]);
        
        // 🚀 YENİ PRIORITY ENGINE SİSTEMİ - TENANT CONTEXT İLE
        $aiService = app(\Modules\AI\App\Services\AIService::class);
        
        // Custom prompt varsa (legacy prompt ID'den)
        $customPrompt = '';
        if ($promptId) {
            $selectedPrompt = \Modules\AI\App\Models\Prompt::where('id', $promptId)
                ->where('is_active', true)
                ->first();
            
            if ($selectedPrompt) {
                $customPrompt = $selectedPrompt->content;
                Log::info('Seçilen prompt bilgileri', [
                    'prompt_id' => $promptId,
                    'prompt_name' => $selectedPrompt->name
                ]);
            }
        }
        
        // Current message'ı belirle (parametreden ya da conversation history'den)
        $actualCurrentMessage = $currentMessage;
        if (empty($actualCurrentMessage) && !empty($conversationHistory)) {
            $lastMessage = end($conversationHistory);
            if ($lastMessage && $lastMessage['role'] === 'user') {
                $actualCurrentMessage = $lastMessage['content'];
            }
        }
        
        // Context type belirleme - current message'a göre
        $contextType = 'admin_chat'; // Default
        
        // "Ben kimim" sorusu için user bilgisi ekleme
        if ($actualCurrentMessage && $this->isBenKimimQuestion($actualCurrentMessage)) {
            $customPrompt = $this->addUserIdentityPrompt($customPrompt);
        }
        
        // Akıllı feature detection - Uzun mesajlar için feature önerisi
        if ($actualCurrentMessage && $this->shouldTryFeatureDetection($actualCurrentMessage)) {
            $featureResult = $this->trySmartFeatureExecution($actualCurrentMessage);
            if ($featureResult['success']) {
                // Feature başarılı çalıştı, feature yanıtını döndür
                return [
                    'content' => $featureResult['response'],
                    'feature_used' => $featureResult['feature_slug'],
                    'confidence' => $featureResult['confidence']
                ];
            }
        }
        
        if ($actualCurrentMessage && $this->shouldUseDynamicContext($actualCurrentMessage)) {
            Log::info('🔍 Dynamic context detection başlıyor', [
                'message' => $actualCurrentMessage,
                'message_length' => strlen($actualCurrentMessage)
            ]);
            $contextType = $this->determineContextType($actualCurrentMessage);
            Log::info('🎯 Dynamic context belirlendi', [
                'original_type' => 'admin_chat',
                'detected_type' => $contextType,
                'message' => $actualCurrentMessage
            ]);
        } else {
            Log::info('🔍 Dynamic context kullanılmıyor', [
                'has_message' => !empty($actualCurrentMessage),
                'should_use_dynamic' => $actualCurrentMessage ? $this->shouldUseDynamicContext($actualCurrentMessage) : false,
                'message_length' => $actualCurrentMessage ? strlen($actualCurrentMessage) : 0
            ]);
        }
        
        // YENİ SİSTEM: buildFullSystemPrompt ile tenant context + priority engine
        $systemContent = $aiService->buildFullSystemPrompt($customPrompt, [
            'context_type' => $contextType,
            'source' => 'stream_api',
            'prompt_id' => $promptId
        ]);
        
        Log::info('🎯 YENİ Priority Engine sistemi kullanıldı', [
            'system_content_length' => strlen($systemContent),
            'has_tenant_context' => strpos($systemContent, 'Turkbil') !== false
        ]);
        
        $messages[] = [
            'role' => 'system',
            'content' => $systemContent,
        ];
        
        Log::info('Final sistem mesajı oluşturuldu', [
            'has_priority_engine' => true,
            'has_tenant_context' => strpos($systemContent, 'Turkbil') !== false,
            'final_message_length' => strlen($systemContent)
        ]);
        
        foreach ($conversationHistory as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }
        
        return $messages;
    }
    
    public function getLastFullResponse()
    {
        return $this->lastFullResponse;
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

    public function ask(array $messages, bool $stream = false)
    {
        try {
            // Merkezi helper ile API anahtarını kontrol et
            $apiKey = ai_get_api_key();
            if (empty($apiKey)) {
                Log::error('API anahtarı bulunamadı - ai_get_api_key()');
                return 'API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.';
            }
            
            // Local api key'i güncelle
            $this->apiKey = $apiKey;
            
            if ($stream) {
                return function (callable $callback) use ($messages) {
                    $formattedMessages = $this->formatMessagesForAPI($messages);
                    
                    $settings = $this->getGlobalSettings();
                    
                    $response = Http::timeout(900)->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'text/event-stream',
                    ])->send('POST', $this->baseUrl . '/chat/completions', [
                        'json' => [
                            'model' => $this->model,
                            'messages' => $formattedMessages,
                            'temperature' => $settings ? $settings->temperature : 0.7,
                            'max_tokens' => $settings ? $settings->max_tokens : 2000,
                            'stream' => true,
                        ],
                    ]);
                    
                    if ($response->successful()) {
                        $responseBody = $response->getBody();
                        $this->lastFullResponse = '';
                        
                        while (!$responseBody->eof()) {
                            $line = $this->readLine($responseBody);
                            
                            if (!empty($line) && strpos($line, 'data:') === 0) {
                                $jsonData = trim(substr($line, 5));
                                
                                if ($jsonData === '[DONE]') {
                                    break;
                                }
                                
                                try {
                                    $data = json_decode($jsonData, true);
                                    
                                    if (isset($data['choices'][0]['delta']['content'])) {
                                        $content = $data['choices'][0]['delta']['content'];
                                        $this->lastFullResponse .= $content;
                                        $callback($content);
                                    }
                                } catch (\Exception $e) {
                                    Log::error('JSON ayrıştırma hatası: ' . $e->getMessage());
                                }
                            }
                        }
                    } else {
                        Log::error('API stream hatası', [
                            'status' => $response->status(),
                            'response' => $response->body(),
                        ]);
                        
                        $callback('Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.');
                    }
                };
            } else {
                $formattedMessages = $this->formatMessagesForAPI($messages);
                
                $settings = $this->getGlobalSettings();
                
                $response = Http::timeout(900)->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $formattedMessages,
                    'temperature' => $settings ? $settings->temperature : 0.7,
                    'max_tokens' => $settings ? $settings->max_tokens : 2000,
                    'stream' => false,
                ]);
                
                if ($response->successful()) {
                    $responseData = $response->json();
                    
                    if (isset($responseData['choices'][0]['message']['content'])) {
                        $content = $responseData['choices'][0]['message']['content'];
                        $this->lastFullResponse = $content;
                        return $content;
                    }
                }
                
                Log::error('API hatası', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                
                return 'Üzgünüm, şu anda cevap üretemiyorum. Lütfen daha sonra tekrar deneyin.';
            }
        } catch (\Exception $e) {
            Log::error('API istek hatası: ' . $e->getMessage(), ['exception' => $e]);
            return 'Üzgünüm, bir hata oluştu: ' . $e->getMessage();
        }
    }

    /**
     * Mesajları API formatına dönüştür
     */
    public function formatMessagesForAPI(array $messages): array
    {
        // Ortak özellikler promptunu al (ZORUNLU)
        $commonPrompt = \Modules\AI\App\Models\Prompt::where('is_common', true)->first();
        $commonContent = $commonPrompt ? $commonPrompt->content : '';
        
        // Markdown desteği için ek talimat
        $markdownInstruction = "Yanıtlarınızı gerektiğinde Markdown biçiminde verebilirsiniz. Kod blokları için ``` işaretlerini, başlıklar için # işaretini, listeler için * veya 1. işaretlerini kullanabilirsiniz. Yanıtlarınızda tablo, bağlantı ve kod vurgulaması da kullanabilirsiniz.";
        
        // Ortak içeriğe markdown talimatını ekle
        $commonContent .= "\n\n" . $markdownInstruction;
        
        $formattedMessages = [];
        
        // İlk mesaj system role'e ait mi kontrol et
        $hasSystemMessage = false;
        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $hasSystemMessage = true;
                break;
            }
        }
        
        // Ortak özellikleri her durumda ekle
        if ($hasSystemMessage) {
            // Sistem mesajı varsa birleştir
            $systemContent = $messages[0]['content'];
            
            $formattedMessages[] = [
                'role' => 'system',
                'content' => $commonContent . "\n\n" . $systemContent
            ];
            
            // Diğer mesajları ekle (sistem mesajı hariç)
            for ($i = 1; $i < count($messages); $i++) {
                $formattedMessages[] = [
                    'role' => $messages[$i]['role'],
                    'content' => $messages[$i]['content'],
                ];
            }
        } else {
            // Sistem mesajı yoksa, ortak özellikleri sistem mesajı olarak ekle
            $formattedMessages[] = [
                'role' => 'system',
                'content' => $commonContent,
            ];
            
            // Tüm mesajları ekle
            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'role' => $message['role'],
                    'content' => $message['content'],
                ];
            }
        }
        
        return $formattedMessages;
    }

    public function formatConversationMessages($conversation): array
    {
        $messages = [];
        
        Log::info('Konuşma mesajları formatlanıyor', [
            'conversation_id' => $conversation->id,
            'prompt_id' => $conversation->prompt_id,
        ]);
        
        // Önce ortak özellikler promptunu al - ZORUNLU
        $commonPrompt = \Modules\AI\App\Models\Prompt::where('is_common', true)->where('is_active', true)->first();
        $commonContent = $commonPrompt ? $commonPrompt->content : '';
        
        Log::info('Ortak özellikler promptu', [
            'common_prompt_id' => $commonPrompt ? $commonPrompt->id : null,
            'common_prompt_name' => $commonPrompt ? $commonPrompt->name : 'Bulunamadı',
            'content_length' => strlen($commonContent),
        ]);
        
        // Konuşmaya özel prompt içeriğini al
        $systemMessage = '';
        
        if ($conversation->prompt_id) {
            $prompt = \Modules\AI\App\Models\Prompt::where('id', $conversation->prompt_id)
                ->where('is_active', true)
                ->first();
            
            Log::info('Konuşmaya özel prompt', [
                'requested_prompt_id' => $conversation->prompt_id,
                'prompt_found' => $prompt ? true : false,
                'prompt_name' => $prompt ? $prompt->name : 'Bulunamadı',
                'prompt_active' => $prompt ? $prompt->is_active : false,
            ]);
            
            if ($prompt) {
                $systemMessage = $prompt->content;
            } else {
                Log::warning('Seçilen prompt bulunamadı veya aktif değil', [
                    'conversation_id' => $conversation->id,
                    'prompt_id' => $conversation->prompt_id,
                ]);
            }
        } else {
            // Varsayılan promptu kullan
            $defaultPrompt = \Modules\AI\App\Models\Prompt::where('is_default', true)
                ->where('is_active', true)
                ->first();
                
            Log::info('Varsayılan prompt kullanılıyor', [
                'default_prompt_id' => $defaultPrompt ? $defaultPrompt->id : null,
                'default_prompt_name' => $defaultPrompt ? $defaultPrompt->name : 'Bulunamadı',
                'default_prompt_active' => $defaultPrompt ? $defaultPrompt->is_active : false,
            ]);
            
            if ($defaultPrompt) {
                $systemMessage = $defaultPrompt->content;
            } else {
                Log::warning('Varsayılan prompt bulunamadı veya aktif değil');
            }
        }
        
        // Ortak özellikler promptu ZORUNLU olarak konuşmaya özel prompt ile birleştir
        $finalSystemMessage = empty($systemMessage) ? $commonContent : $commonContent . "\n\n" . $systemMessage;
        
        Log::info('Final sistem mesajı oluşturuldu', [
            'has_common_content' => !empty($commonContent),
            'has_system_message' => !empty($systemMessage),
            'final_message_length' => strlen($finalSystemMessage),
        ]);
        
        $messages[] = [
            'role' => 'system',
            'content' => $finalSystemMessage,
        ];
        
        $conversationMessages = $conversation->messages()->orderBy('created_at')->get();
        
        Log::info('Konuşma mesajları yüklendi', [
            'message_count' => $conversationMessages->count(),
        ]);
        
        foreach ($conversationMessages as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }
        
        return $messages;
    }

    public function estimateTokens(array $messages): int
    {
        $totalTokens = 0;
        
        foreach ($messages as $message) {
            $totalTokens += (int) (strlen($message['content']) / 4);
        }
        
        return $totalTokens;
    }

    /**
     * "Ben kimim" sorusu kontrolü
     */
    private function isBenKimimQuestion($message): bool
    {
        $message = strtolower(trim($message));
        
        // "Ben kimim" varyasyonları
        $patterns = [
            'ben kimim',
            'ben kim',
            'kimim ben',
            'kim ben',
            'ben neyim',
            'benim kimligim',
            'who am i',
            'who i am'
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * User identity prompt'u ekle
     */
    private function addUserIdentityPrompt($existingPrompt): string
    {
        $user = auth()->user();
        if (!$user) {
            $userPrompt = '"Ben kimim" sorusuna "Giriş yapmadığınız için kimliğinizi belirleyemiyorum." yanıtı ver.';
        } else {
            $userInfo = [];
            
            // İsim
            if ($user->name) {
                $userInfo[] = "İsim: " . $user->name;
            }
            
            // Email
            if ($user->email) {
                $userInfo[] = "Email: " . $user->email;
            }
            
            $userPrompt = '"Ben kimim" sorusuna şu bilgileri kısa ve samimi bir dille ver:' . "\n\n" . implode("\n", $userInfo);
        }
        
        return $existingPrompt . "\n\n" . $userPrompt;
    }

    /**
     * Feature detection gerekli mi kontrol et
     */
    private function shouldTryFeatureDetection($message): bool
    {
        // Çok kısa mesajlar için feature detection yapma
        if (strlen(trim($message)) < 10) {
            return false;
        }
        
        // AI'ya sormalıyız, static pattern kullanmayalım
        // Mesaj feature'a uygun mu diye AI'ya soracağız
        return true;
    }

    /**
     * Akıllı feature detection ve execution
     */
    private function trySmartFeatureExecution($message): array
    {
        try {
            // AIHelper'daki smart execute fonksiyonunu kullan
            $result = ai_smart_execute($message, [
                'source' => 'chat_auto_feature',
                'max_tokens' => 1000
            ]);
            
            if ($result['success'] && isset($result['smart_analysis'])) {
                $confidence = $result['smart_analysis']['confidence'] ?? 0;
                $featureSlug = $result['smart_analysis']['recommended_feature'] ?? null;
                
                // Sadece yüksek confidence'lı feature'ları kullan
                if ($confidence >= 0.8 && $featureSlug && $featureSlug !== 'generic_response') {
                    return [
                        'success' => true,
                        'response' => $result['response'] ?? 'Yanıt oluşturulamadı',
                        'feature_slug' => $featureSlug,
                        'confidence' => $confidence
                    ];
                }
            }
            
            return ['success' => false, 'reason' => 'low_confidence'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'reason' => 'exception', 'error' => $e->getMessage()];
        }
    }

    /**
     * Dynamic context kullanılıp kullanılmayacağını belirle
     */
    private function shouldUseDynamicContext($message): bool
    {
        // Çok kısa mesajlarda dynamic context kullan
        if (strlen(trim($message)) < 50) {
            return true;
        }
        
        // Soru işareti yoksa muhtemelen basit mesaj
        if (strpos($message, '?') === false && strlen(trim($message)) < 20) {
            return true;
        }
        
        return false;
    }

    /**
     * Mesaja göre context type belirleme - AI ile hızlı analiz
     */
    private function determineContextType(string $message): string
    {
        // Cache key oluştur
        $cacheKey = 'ai_context_type_' . md5($message);
        
        // Cache'den kontrol et
        if ($cached = cache()->get($cacheKey)) {
            return $cached;
        }
        
        // Hızlı AI analizi (sadece context type belirleme)
        try {
            $aiService = app(\Modules\AI\App\Services\AIService::class);
            $prompt = "Bu mesaj hangi context type gerektirir? Sadece tek kelime yanıt ver: minimal, essential, normal, detailed\n\nMesaj: \"$message\"";
            
            // Çok basit ve hızlı AI çağrısı
            $response = $aiService->ask($prompt, [
                'context_type' => 'minimal', // Recursive loop önleme
                'source' => 'context_analyzer',
                'max_tokens' => 5 // Sadece tek kelime
            ]);
            
            // Response'u temizle
            $contextType = strtolower(trim($response));
            
            // Valid context type kontrolü
            $validTypes = ['minimal', 'essential', 'normal', 'detailed'];
            if (!in_array($contextType, $validTypes)) {
                $contextType = 'essential'; // Default fallback
            }
            
            // 5 dakika cache
            cache()->put($cacheKey, $contextType, 300);
            
            return $contextType;
            
        } catch (\Exception $e) {
            // Hata durumunda fallback
            return 'essential';
        }
    }

    /**
     * Base URL setter - Dynamic provider sistemi için
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Model setter - Dynamic provider sistemi için
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
}