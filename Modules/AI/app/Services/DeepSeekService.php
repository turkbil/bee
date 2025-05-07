<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Exception;

class DeepSeekService
{
    protected $apiKey;
    protected $model;
    protected $temperature;
    protected $maxTokens;
    protected $apiBaseUrl = 'https://api.deepseek.com/v1';

    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
        ?float $temperature = null,
        ?int $maxTokens = null
    ) {
        Log::info('DeepSeekService başlatılıyor');
        
        $settings = $this->getSettings();
        
        $this->apiKey = $apiKey ?? $settings->api_key ?? null;
        $this->model = $model ?? $settings->model ?? 'deepseek-chat';
        $this->temperature = $temperature ?? $settings->temperature ?? 0.7;
        $this->maxTokens = $maxTokens ?? $settings->max_tokens ?? 4096;
        
        Log::info('DeepSeekService ayarları: model=' . $this->model . ', temperature=' . $this->temperature . ', maxTokens=' . $this->maxTokens);
        Log::debug('API Anahtarı: ' . ($this->apiKey ? 'Ayarlandı (ilk 5 karakter: ' . substr($this->apiKey, 0, 5) . '...)' : 'Boş!'));
    }

    public function ask(array $messages, bool $stream = false)
    {
        try {
            Log::info('DeepSeek API isteği başlıyor, stream: ' . ($stream ? 'evet' : 'hayır'));
            Log::debug('Mesaj sayısı: ' . count($messages));
            
            foreach ($messages as $index => $message) {
                Log::debug('Mesaj #' . $index . ': role=' . $message['role'] . ', içerik (ilk 30 karakter): ' . substr($message['content'], 0, 30) . '...');
            }
            
            if (!$this->apiKey) {
                throw new Exception('API anahtarı ayarlanmamış! Lütfen AI ayarlarını kontrol edin.');
            }
            
            if ($stream) {
                Log::info('Stream modunda istek yapılıyor');
                return $this->askWithStream($messages);
            }
            
            Log::info('Normal modda istek yapılıyor');
            return $this->askWithHttp($messages);
        } catch (Exception $e) {
            Log::error('DeepSeek API istek hatası: ' . $e->getMessage());
            Log::error('Hata yığını: ' . $e->getTraceAsString());
            
            if ($stream) {
                return function ($callback) use ($e) {
                    $errorMessage = $this->formatErrorMessage($e);
                    $callback($errorMessage);
                };
            }
            
            return $this->formatErrorMessage($e);
        }
    }
    
    protected function formatErrorMessage(Exception $e): string
    {
        $message = 'Yanıt alınamadı: ';
        
        if ($e instanceof ConnectException) {
            return $message . 'DeepSeek AI sunucusuna bağlanılamadı. İnternet bağlantınızı kontrol edin.';
        } elseif ($e instanceof RequestException) {
            if ($e->getCode() == 401) {
                return $message . 'API anahtarı geçersiz. Lütfen API anahtarınızı kontrol edin.';
            } elseif ($e->getCode() == 429) {
                return $message . 'DeepSeek API hız limiti aşıldı. Lütfen daha sonra tekrar deneyin.';
            } elseif ($e->getCode() >= 500) {
                return $message . 'DeepSeek AI sunucusunda bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
        }
        
        return $message . 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
    }
    
    protected function askWithStream(array $messages): \Closure
    {
        return function ($callback) use ($messages) {
            try {
                Log::info('Stream istek oluşturuluyor');
                
                if (!$this->apiKey) {
                    throw new Exception('API anahtarı ayarlanmamış! Lütfen AI ayarlarını kontrol edin.');
                }
                
                $client = new Client();
                
                Log::debug('Gönderilen istek: ' . json_encode([
                    'url' => $this->apiBaseUrl . '/chat/completions',
                    'headers' => [
                        'Authorization' => 'Bearer *****',
                        'Content-Type' => 'application/json',
                        'Accept' => 'text/event-stream'
                    ],
                    'json' => [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => $this->temperature,
                        'max_tokens' => $this->maxTokens,
                        'stream' => true
                    ]
                ], JSON_UNESCAPED_UNICODE));
                
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
                    'stream' => true,
                    'timeout' => 60,
                    'connect_timeout' => 10
                ]);
                
                Log::info('API stream yanıtı alındı, işleniyor...');
                
                $body = $response->getBody();
                $buffer = '';
                $contentReceived = false;
                
                while (!$body->eof()) {
                    // Tek seferde daha fazla veri okuyalım
                    $chunk = $body->read(1024);
                    if (empty($chunk)) {
                        usleep(10000); // 10ms bekle
                        continue;
                    }
                    
                    $buffer .= $chunk;
                    
                    // SSE formatında "data:" satırlarını işleyelim
                    while (($pos = strpos($buffer, "\n\n")) !== false) {
                        $lines = substr($buffer, 0, $pos + 2);
                        $buffer = substr($buffer, $pos + 2);
                        
                        foreach (explode("\n", $lines) as $line) {
                            if (strpos($line, 'data:') === 0) {
                                $data = substr($line, 5);
                                
                                if (trim($data) === '[DONE]') {
                                    Log::info('Stream tamamlandı [DONE] mesajı alındı');
                                    continue;
                                }
                                
                                try {
                                    $json = json_decode(trim($data), true);
                                    
                                    if (isset($json['choices'][0]['delta']['content'])) {
                                        $content = $json['choices'][0]['delta']['content'];
                                        Log::debug('Stream parçası: ' . $content);
                                        $callback($content);
                                        $contentReceived = true;
                                    }
                                } catch (Exception $e) {
                                    Log::error('Stream JSON parse hatası: ' . $e->getMessage());
                                    Log::error('Hatalı JSON: ' . $data);
                                    continue;
                                }
                            }
                        }
                    }
                }
                
                Log::info('Stream işleme tamamlandı');
                
                // İçerik alınamadıysa hata mesajı gönder
                if (!$contentReceived) {
                    $errorMessage = 'AI yanıtı alınamadı. Lütfen yeniden deneyin veya farklı bir soru sorun.';
                    Log::warning('Stream işlemi tamamlandı fakat içerik alınamadı!');
                    $callback($errorMessage);
                }
            } catch (ConnectException $e) {
                Log::error('Stream bağlantı hatası: ' . $e->getMessage());
                $callback('DeepSeek AI sunucusuna bağlanılamadı. Lütfen internet bağlantınızı kontrol edin.');
            } catch (RequestException $e) {
                Log::error('Stream istek hatası: ' . $e->getMessage() . ', HTTP kodu: ' . $e->getCode());
                
                if ($e->getCode() == 401) {
                    $callback('API anahtarı geçersiz. Lütfen API anahtarınızı kontrol edin.');
                } elseif ($e->getCode() == 429) {
                    $callback('DeepSeek API hız limiti aşıldı. Lütfen daha sonra tekrar deneyin.');
                } elseif ($e->getCode() >= 500) {
                    $callback('DeepSeek AI sunucusunda bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
                } else {
                    $callback('AI yanıtı alınırken bir hata oluştu: ' . $e->getMessage());
                }
            } catch (Exception $e) {
                Log::error('Stream işleme hatası: ' . $e->getMessage());
                Log::error('Hata yığını: ' . $e->getTraceAsString());
                $callback('AI yanıtı alınırken bir hata oluştu: ' . $e->getMessage());
            }
        };
    }

    protected function askWithHttp(array $messages): ?string
    {
        try {
            Log::info('HTTP istek oluşturuluyor');
            
            if (!$this->apiKey) {
                throw new Exception('API anahtarı ayarlanmamış! Lütfen AI ayarlarını kontrol edin.');
            }
            
            $response = Http::timeout(60)
                ->withHeaders([
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
                $content = $data['choices'][0]['message']['content'] ?? null;
                
                if (empty($content)) {
                    Log::warning('API yanıtı başarılı fakat içerik boş!');
                    return 'AI yanıtı alınamadı. Lütfen yeniden deneyin veya farklı bir soru sorun.';
                }
                
                Log::info('API yanıtı başarılı, içerik: ' . substr($content ?? '', 0, 30) . '...');
                return $content;
            } else {
                Log::error('DeepSeek API hatası: ' . $response->status());
                Log::error('Hata içeriği: ' . $response->body());
                
                if ($response->status() == 401) {
                    return 'API anahtarı geçersiz. Lütfen API anahtarınızı kontrol edin.';
                } elseif ($response->status() == 429) {
                    return 'DeepSeek API hız limiti aşıldı. Lütfen daha sonra tekrar deneyin.';
                } elseif ($response->status() >= 500) {
                    return 'DeepSeek AI sunucusunda bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
                }
                
                return 'AI yanıtı alınamadı. Hata kodu: ' . $response->status();
            }
        } catch (Exception $e) {
            Log::error('HTTP istek hatası: ' . $e->getMessage());
            Log::error('Hata yığını: ' . $e->getTraceAsString());
            return $this->formatErrorMessage($e);
        }
    }

    public function estimateTokens(array $messages): int
    {
        $totalChars = 0;
        foreach ($messages as $message) {
            $totalChars += strlen($message['content']);
        }
        
        return ceil($totalChars / 4);
    }

    public static function forTenant(?int $tenantId = null): self
    {
        try {
            Log::info('DeepSeekService::forTenant çağrıldı, tenantId: ' . ($tenantId ?? 'null'));
            
            $settings = Cache::remember('ai_settings', now()->addMinutes(30), function () {
                $setting = Setting::where('tenant_id', 1)->first();
                Log::info('AI ayarları veritabanından alındı: ' . ($setting ? 'bulundu' : 'bulunamadı'));
                return $setting;
            });

            if (!$settings) {
                Log::warning('AI ayarları bulunamadı, varsayılan değerler kullanılacak');
                return new self();
            }

            Log::info('AI ayarları: model=' . $settings->model . ', temperature=' . $settings->temperature);
            
            return new self(
                $settings->api_key,
                $settings->model,
                $settings->temperature,
                $settings->max_tokens
            );
        } catch (Exception $e) {
            Log::error('forTenant metodu hatası: ' . $e->getMessage());
            return new self();
        }
    }
    
    protected function getSettings()
    {
        try {
            Log::info('AI ayarları alınıyor');
            
            return Cache::remember('ai_settings', now()->addMinutes(30), function () {
                $setting = Setting::where('tenant_id', 1)->first();
                Log::info('AI ayarları: ' . ($setting ? 'bulundu' : 'bulunamadı'));
                return $setting ?? new \stdClass();
            });
        } catch (Exception $e) {
            Log::error('AI ayarları alınırken hata: ' . $e->getMessage());
            return new \stdClass();
        }
    }

    public function formatConversationMessages($conversation, ?string $systemPrompt = null): array
    {
        try {
            Log::info('Konuşma mesajları formatlanıyor, ID: ' . $conversation->id);
            
            $messages = [];

            if ($systemPrompt) {
                Log::info('Özel sistem prompt kullanılıyor');
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt
                ];
            } elseif ($conversation->prompt_id) {
                $prompt = $conversation->prompt;
                if ($prompt) {
                    Log::info('Konuşma prompt şablonu kullanılıyor, ID: ' . $prompt->id);
                    $messages[] = [
                        'role' => 'system',
                        'content' => $prompt->content
                    ];
                }
            }

            $conversationMessages = $conversation->messages()
                ->orderBy('id', 'desc')
                ->take(10)
                ->get()
                ->reverse();

            Log::info('Konuşma geçmişi alındı, mesaj sayısı: ' . $conversationMessages->count());
            
            foreach ($conversationMessages as $message) {
                $messages[] = [
                    'role' => $message->role,
                    'content' => $message->content
                ];
                
                Log::debug('Mesaj eklendi: role=' . $message->role . ', içerik (ilk 30 karakter): ' . substr($message->content, 0, 30) . '...');
            }

            Log::info('Toplam mesaj sayısı: ' . count($messages));
            return $messages;
        } catch (Exception $e) {
            Log::error('Konuşma mesajları formatlanırken hata: ' . $e->getMessage());
            return [];
        }
    }
}