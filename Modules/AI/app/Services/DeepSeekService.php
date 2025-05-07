<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Message;

class DeepSeekService
{
    protected string $apiKey;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;
    protected string $apiBaseUrl = 'https://api.deepseek.com/v1';

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
        $this->apiKey = $apiKey ?? config('ai.api_key');
        $this->model = $model ?? config('ai.model', 'deepseek-chat');
        $this->temperature = $temperature ?? config('ai.temperature', 0.7);
        $this->maxTokens = $maxTokens ?? config('ai.max_tokens', 4096);
    }

    /**
     * Modele soru sor
     *
     * @param array $messages
     * @return string|null
     */
    public function ask(array $messages): ?string
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('DeepSeek API istek hatası: ' . $e->getMessage());
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
        return (int) ($totalChars / 4);
    }

    /**
     * Tenant ID'ye göre ayarları yükle
     *
     * @param int|null $tenantId
     * @return self
     */
    public static function forTenant(?int $tenantId = null): self
    {
        // Eğer tenantId null ise, varsayılan ayarlarla servisi döndür
        if ($tenantId === null) {
            return new self(
                config('ai.api_key'),
                config('ai.model', 'deepseek-chat'),
                config('ai.temperature', 0.7),
                config('ai.max_tokens', 4096)
            );
        }

        $settings = Setting::where('tenant_id', $tenantId)->first();

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