<?php

namespace App\Services\AI\Providers;

use App\Contracts\AI\AIProviderInterface;
use Modules\AI\App\Services\DeepSeekService;
use Illuminate\Support\Facades\Log;
use Exception;

class DeepSeekProvider implements AIProviderInterface
{
    protected DeepSeekService $deepSeekService;

    public function __construct()
    {
        $this->deepSeekService = new DeepSeekService();
    }

    public function getName(): string
    {
        return 'deepseek';
    }

    public function sendRequest(array $messages, array $options = []): array
    {
        try {
            $response = $this->deepSeekService->generateCompletion($messages[0]['content'] ?? '', $messages);
            
            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            return [
                'content' => $response['content'],
                'usage' => [
                    'total_tokens' => $this->calculateTokens($response['content']),
                    'prompt_tokens' => $this->calculateTokensForMessages($messages),
                    'completion_tokens' => $this->calculateTokens($response['content'])
                ],
                'model' => $options['model'] ?? 'deepseek-chat',
                'provider' => $this->getName()
            ];

        } catch (Exception $e) {
            Log::error('DeepSeek API request failed', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);
            
            throw $e;
        }
    }

    public function sendStreamRequest(array $messages, array $options = []): \Generator
    {
        try {
            $totalTokens = 0;
            $responseContent = '';
            
            $streamCallback = function($content) use (&$totalTokens, &$responseContent) {
                $responseContent .= $content;
                $totalTokens = $this->calculateTokens($responseContent);
                
                return [
                    'content' => $content,
                    'delta' => ['content' => $content],
                    'usage' => [
                        'total_tokens' => $totalTokens,
                        'completion_tokens' => $totalTokens
                    ]
                ];
            };

            $this->deepSeekService->streamCompletion(
                $messages[0]['content'] ?? '', 
                $messages, 
                function($content) use ($streamCallback) {
                    $result = $streamCallback($content);
                    return $result;
                }
            );

            // Stream tamamlandığında final usage bilgilerini yield et
            yield [
                'content' => '',
                'usage' => [
                    'total_tokens' => $totalTokens,
                    'prompt_tokens' => $this->calculateTokensForMessages($messages),
                    'completion_tokens' => $totalTokens
                ],
                'model' => $options['model'] ?? 'deepseek-chat',
                'provider' => $this->getName(),
                'finished' => true
            ];

        } catch (Exception $e) {
            Log::error('DeepSeek stream request failed', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);
            
            throw $e;
        }
    }

    public function calculateTokens(string $text): int
    {
        // Basit token hesaplama (4 karakter = 1 token)
        // Gerçek token hesaplama için OpenAI'nin tiktoken kütüphanesi kullanılabilir
        return (int) ceil(strlen($text) / 4);
    }

    protected function calculateTokensForMessages(array $messages): int
    {
        $totalTokens = 0;
        foreach ($messages as $message) {
            $totalTokens += $this->calculateTokens($message['content'] ?? '');
        }
        return $totalTokens;
    }

    public function isActive(): bool
    {
        return $this->deepSeekService->testConnection();
    }

    public function validateConfiguration(): bool
    {
        try {
            return !empty($this->deepSeekService->getApiKey()) && $this->deepSeekService->testConnection();
        } catch (Exception $e) {
            Log::error('DeepSeek configuration validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function testConnection(): array
    {
        try {
            $isConnected = $this->deepSeekService->testConnection();
            
            return [
                'success' => $isConnected,
                'provider' => $this->getName(),
                'message' => $isConnected ? 'Bağlantı başarılı' : 'Bağlantı başarısız',
                'api_key_status' => !empty($this->deepSeekService->getApiKey()) ? 'Mevcut' : 'Bulunamadı'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'provider' => $this->getName(),
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}