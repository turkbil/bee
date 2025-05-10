<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class DeepSeekService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $lastFullResponse = '';

    public function __construct()
    {
        $this->loadApiSettings();
        $this->baseUrl = config('deepseek.api_url', 'https://api.deepseek.com/v1');
        $this->model = config('deepseek.model', 'deepseek-chat');
    }

    protected function loadApiSettings()
    {
        // 1. Veritabanından ayarları yüklemeyi dene
        $settings = $this->getGlobalSettings();
        if ($settings && !empty($settings->api_key)) {
            $this->apiKey = $settings->api_key;
            $this->model = $settings->model;
            return;
        }
        
        // 2. Veritabanı ayarları yoksa, .env'den yükle
        $this->apiKey = config('deepseek.api_key');
        
        if (empty($this->apiKey)) {
            Log::warning('API anahtarı bulunamadı. Lütfen .env dosyasına DEEPSEEK_API_KEY ekleyin veya veritabanı ayarlarını yapılandırın.');
        }
    }

    protected function getGlobalSettings()
    {
        $cacheKey = "ai_settings_global";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return Setting::first();
        });
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

    public function generateCompletion($message, $conversationHistory = [])
    {
        $messages = $this->formatMessages($conversationHistory);

        try {
            if (empty($this->apiKey)) {
                Log::error('API anahtarı bulunamadı');
                return [
                    'content' => 'API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.',
                    'error' => 'API anahtarı bulunamadı',
                ];
            }
            
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
    
    public function streamCompletion($message, $conversationHistory = [], ?callable $callback = null)
    {
        $messages = $this->formatMessages($conversationHistory);
        $this->lastFullResponse = '';

        try {
            if (empty($this->apiKey)) {
                Log::error('API anahtarı bulunamadı');
                if ($callback) {
                    $callback('API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.');
                }
                return;
            }
            
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
    
    public function getLastFullResponse()
    {
        return $this->lastFullResponse;
    }
    
    protected function formatMessages($conversationHistory)
    {
        $messages = [];
        
        // Ortak özellikler promptunu al (ZORUNLU)
        $commonPrompt = \Modules\AI\App\Models\Prompt::where('is_common', true)->first();
        $commonContent = $commonPrompt ? $commonPrompt->content : config('deepseek.system_message', 'Sen bir asistansın.');
        
        $messages[] = [
            'role' => 'system',
            'content' => $commonContent,
        ];
        
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

    public function ask(array $messages, bool $stream = false)
    {
        try {
            if (empty($this->apiKey)) {
                Log::error('API anahtarı bulunamadı');
                return 'API anahtarı bulunamadı. Lütfen yöneticinizle iletişime geçin.';
            }
            
            if ($stream) {
                return function (callable $callback) use ($messages) {
                    $formattedMessages = $this->formatMessagesForAPI($messages);
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'text/event-stream',
                    ])->timeout(60)->send('POST', $this->baseUrl . '/chat/completions', [
                        'json' => [
                            'model' => $this->model,
                            'messages' => $formattedMessages,
                            'temperature' => config('deepseek.temperature', 0.7),
                            'max_tokens' => config('deepseek.max_tokens', 2000),
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
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $formattedMessages,
                    'temperature' => config('deepseek.temperature', 0.7),
                    'max_tokens' => config('deepseek.max_tokens', 2000),
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
        
        // Önce ortak özellikler promptunu al - ZORUNLU
        $commonPrompt = \Modules\AI\App\Models\Prompt::where('is_common', true)->first();
        $commonContent = $commonPrompt ? $commonPrompt->content : '';
        
        // Konuşmaya özel prompt içeriğini al
        $systemMessage = '';
        
        if ($conversation->prompt_id) {
            $prompt = \Modules\AI\App\Models\Prompt::find($conversation->prompt_id);
            
            if ($prompt) {
                $systemMessage = $prompt->content;
            }
        }
        
        // Ortak özellikler promptu ZORUNLU olarak konuşmaya özel prompt ile birleştir
        $finalSystemMessage = empty($systemMessage) ? $commonContent : $commonContent . "\n\n" . $systemMessage;
        
        $messages[] = [
            'role' => 'system',
            'content' => $finalSystemMessage,
        ];
        
        $conversationMessages = $conversation->messages()->orderBy('created_at')->get();
        
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
}