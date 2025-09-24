<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;
    protected $maxTokens = 8192; // Increased token limit for Claude Sonnet 4
    protected $lastFullResponse = '';
    protected $providerId;

    public function __construct($config = null)
    {
        if ($config && is_array($config)) {
            // YENİ GLOBAL STANDART - Constructor'dan config al
            $this->providerId = $config['provider_id'] ?? null;
            $this->apiKey = $config['api_key'] ?? null;
            $this->baseUrl = $config['base_url'] ?? 'https://api.anthropic.com';
            $this->model = $config['model'] ?? 'claude-sonnet-4-20250514';
        } else {
            // ESKİ FALLBACK - Compatibility için
            $this->apiKey = null;
            $this->baseUrl = 'https://api.anthropic.com';
            $this->model = 'claude-sonnet-4-20250514';
        }
    }

    /**
     * Set the model to use
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * Set max tokens for response
     */
    public function setMaxTokens(int $maxTokens): void
    {
        $this->maxTokens = $maxTokens;
    }

    /**
     * Claude API completion generation
     */
    public function generateCompletionStream($messages, ?callable $streamCallback = null)
    {
        // FAKE MODE: Network olmadan test için sahte HTML üret
        if (config('ai.fake_mode')) {
            $html = $this->fakeHtmlFromMessages($messages);
            return [
                'response' => $html,
                'tokens_used' => 0,
                'success' => true,
                'provider' => 'anthropic-fake',
                'model' => $this->model,
                'time_ms' => 1
            ];
        }

        $apiStartTime = microtime(true);
        Log::info('🚀 Claude API çağrısı başlatılıyor', [
            'timestamp' => now()->toIso8601String(),
            'model' => $this->model,
            'api_url' => $this->baseUrl . '/v1/messages'
        ]);

        try {
            // Messages array kontrolü
            if (!is_array($messages)) {
                throw new \Exception('Messages must be an array, ' . gettype($messages) . ' given');
            }
            
            // Claude API formatına uygun system ve user message ayrımı
            $systemMessage = '';
            $userMessages = [];
            
            foreach ($messages as $message) {
                if ($message['role'] === 'system') {
                    $systemMessage .= $message['content'] . "\n";
                } else {
                    $userMessages[] = [
                        'role' => $message['role'],
                        'content' => $message['content']
                    ];
                }
            }

            $payload = [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => $userMessages
            ];

            // System message varsa ekle
            if (!empty($systemMessage)) {
                $payload['system'] = trim($systemMessage);
            }

            // ✨ Claude API Request with retry for overload
            $maxRetries = 3;
            $retryCount = 0;
            $response = null;

            while ($retryCount < $maxRetries) {
                $response = Http::withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'anthropic-version' => '2023-06-01'
                ])->timeout(300)->post($this->baseUrl . '/v1/messages', $payload);

                // Eğer başarılı veya overload dışında hata ise döngüden çık
                if ($response->successful() || $response->status() !== 529) {
                    break;
                }

                $retryCount++;
                if ($retryCount < $maxRetries) {
                    Log::warning("🔄 Claude API overload, retry {$retryCount}/{$maxRetries} - 2 saniye bekliyor...");
                    sleep(2); // 2 saniye bekle
                }
            }

            if ($response->successful()) {
                $responseData = $response->json();
                
                $fullResponse = $responseData['content'][0]['text'] ?? '';
                $tokensUsed = ($responseData['usage']['input_tokens'] ?? 0) + ($responseData['usage']['output_tokens'] ?? 0);
                
                Log::info('⚡ Claude yanıt alındı', [
                    'response_time_ms' => round((microtime(true) - $apiStartTime) * 1000, 2),
                    'response_length' => strlen($fullResponse),
                    'tokens_used' => $tokensUsed
                ]);
            } else {
                throw new \Exception('Claude API hatası: ' . $response->status() . ' - ' . $response->body());
            }

            $totalTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            Log::info('🏁 Claude streaming tamamlandı', [
                'total_time_ms' => $totalTime,
                'response_length' => strlen($fullResponse),
                'tokens_used' => $tokensUsed
            ]);

            return [
                'response' => $fullResponse,
                'tokens_used' => $tokensUsed,
                'success' => true,
                'provider' => 'anthropic',
                'model' => $this->model,
                'time_ms' => $totalTime
            ];

        } catch (\Exception $e) {
            Log::error('Claude API hatası: ' . $e->getMessage());
            return [
                'response' => null,
                'tokens_used' => 0,
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'anthropic'
            ];
        }
    }

    private function fakeHtmlFromMessages($messages): string
    {
        $prompt = '';
        foreach ((array)$messages as $m) {
            $prompt .= (is_array($m) ? ($m['content'] ?? '') : (string)$m) . "\n";
        }

        // Basit, uzun görünür bir HTML iskeleti (Tailwind + dark)
        return trim('
<section class="py-20 bg-white dark:bg-gray-900">
  <div class="container mx-auto px-6 sm:px-8 lg:px-12">
    <div class="max-w-4xl">
      <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-6">Ürün Tanıtımı</h1>
      <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">Modern, temiz ve nefes alan tasarım. PDF içeriği landing formatında düzenlenmiştir.</p>
    </div>
  </div>
</section>

<section class="py-16 bg-white dark:bg-gray-900">
  <div class="container mx-auto px-6 sm:px-8 lg:px-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-lg">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Özellik 1</h3>
        <p class="text-gray-700 dark:text-gray-300">Kısa açıklama.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-lg">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Özellik 2</h3>
        <p class="text-gray-700 dark:text-gray-300">Kısa açıklama.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-lg">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Özellik 3</h3>
        <p class="text-gray-700 dark:text-gray-300">Kısa açıklama.</p>
      </div>
    </div>
  </div>
</section>

<section class="py-16 bg-white dark:bg-gray-900">
  <div class="container mx-auto px-6 sm:px-8 lg:px-12">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Teknik Özellikler</h2>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Özellik</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Değer</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Açıklama</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">Kapasite</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">1500 kg</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">Nominal taşıma kapasitesi</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">Akü</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">24V Li-ion</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">Uzun ömürlü enerji kaynağı</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="py-16 bg-white dark:bg-gray-900">
  <div class="container mx-auto px-6 sm:px-8 lg:px-12">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-10 shadow-xl">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Hemen Teklif Alın</h3>
          <p class="text-gray-700 dark:text-gray-300">Satış ekibimizle iletişime geçin.</p>
        </div>
        <button class="inline-flex items-center px-8 py-4 rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all">İletişime Geç</button>
      </div>
    </div>
  </div>
</section>
');
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
