<?php

namespace Modules\AI\App\Services\Providers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * DALL-E 3 Provider
 *
 * Wrapper for OpenAI DALL-E 3 API
 */
class DallE3Provider
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.openai.com/v1/images/generations';

    public function __construct()
    {
        $this->apiKey = config('ai.openai_api_key');

        if (empty($this->apiKey)) {
            throw new Exception('OpenAI API key not configured. Set OPENAI_API_KEY in .env');
        }
    }

    /**
     * Generate image via DALL-E 3
     *
     * @param string $prompt Image description
     * @param array $options ['size' => '1024x1024', 'quality' => 'hd']
     * @return array ['url' => 'https://...', 'revised_prompt' => '...']
     * @throws Exception
     */
    public function generate(string $prompt, array $options = []): array
    {
        $size = $options['size'] ?? '1024x1024';
        $quality = $options['quality'] ?? 'hd';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => $size,
                'quality' => $quality,
            ]);

            if (!$response->successful()) {
                $error = $response->json('error.message') ?? 'Unknown error';
                throw new Exception('DALL-E 3 API error: ' . $error);
            }

            $data = $response->json();

            if (empty($data['data'][0]['url'])) {
                throw new Exception('No image URL in DALL-E 3 response');
            }

            return [
                'url' => $data['data'][0]['url'],
                'revised_prompt' => $data['data'][0]['revised_prompt'] ?? $prompt,
            ];

        } catch (Exception $e) {
            Log::error('DALL-E 3 generation failed', [
                'prompt' => $prompt,
                'options' => $options,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate API key by making a test request
     */
    public function validateApiKey(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get('https://api.openai.com/v1/models');

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }
}
