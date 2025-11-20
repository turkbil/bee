<?php

namespace App\Services\Media\StockPhoto\Providers;

use App\Services\Media\StockPhoto\Contracts\StockPhotoProviderInterface;
use App\Services\Media\StockPhoto\DTOs\MediaRequest;
use App\Services\Media\StockPhoto\DTOs\MediaResponse;
use App\Services\Media\StockPhoto\Exceptions\MediaNotFoundException;
use App\Services\Media\StockPhoto\Exceptions\ProviderNotAvailableException;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * DALL-E Provider
 *
 * API Docs: https://platform.openai.com/docs/guides/images
 * Cost: $0.040 (1024×1024 standard) - $0.120 (1792×1024 HD)
 * Rate Limit: OpenAI account limit
 *
 * NOT: Bu provider AI-generated görsel oluşturur (fallback olarak kullanılmalı)
 */
class DallEProvider implements StockPhotoProviderInterface
{
    // Cost mapping (2024 pricing)
    private const COST_MAP = [
        '1024x1024' => ['standard' => 0.040, 'hd' => 0.080],
        '1792x1024' => ['standard' => 0.080, 'hd' => 0.120],
        '1024x1792' => ['standard' => 0.080, 'hd' => 0.120],
    ];

    private ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.openai.api_key');
    }

    public function getName(): string
    {
        return 'dalle';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function fetch(MediaRequest $request): MediaResponse
    {
        if (!$this->isAvailable()) {
            throw ProviderNotAvailableException::missingApiKey($this->getName());
        }

        try {
            // Size ve quality ayarlarını belirle
            [$size, $quality] = $this->determineSizeAndQuality($request);

            // Prompt'u AI için optimize et
            $optimizedPrompt = $this->optimizePrompt($request->query);

            // DALL-E-3 ile görsel oluştur
            $response = OpenAI::images()->create([
                'model' => 'dall-e-3',
                'prompt' => $optimizedPrompt,
                'size' => $size,
                'quality' => $quality,
                'n' => 1,
            ]);

            if (empty($response->data)) {
                throw MediaNotFoundException::forQuery($this->getName(), $request->query);
            }

            $image = $response->data[0];

            // Boyut parse et
            [$width, $height] = explode('x', $size);

            // Maliyeti hesapla
            $cost = $this->calculateCost($size, $quality);

            return new MediaResponse(
                url: $image->url,
                width: (int) $width,
                height: (int) $height,
                provider: $this->getName(),
                photographer: null, // AI-generated
                photographerUrl: null,
                providerUrl: null,
                providerId: null,
                altText: $request->query,
                metadata: [
                    'cost' => $cost,
                    'model' => 'dall-e-3',
                    'quality' => $quality,
                    'revised_prompt' => $image->revised_prompt ?? null,
                    'ai_generated' => true,
                ]
            );

        } catch (MediaNotFoundException|ProviderNotAvailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('DALL-E API error', [
                'error' => $e->getMessage(),
                'query' => $request->query,
            ]);

            // OpenAI quota error kontrolü
            if (str_contains($e->getMessage(), 'insufficient_quota')) {
                throw ProviderNotAvailableException::forProvider(
                    $this->getName(),
                    'Insufficient quota (credits exhausted)'
                );
            }

            throw ProviderNotAvailableException::forProvider(
                $this->getName(),
                $e->getMessage()
            );
        }
    }

    public function getRateLimit(): array
    {
        // OpenAI account-level limits
        return [
            'remaining' => null,
            'limit' => null,
            'reset_at' => null,
        ];
    }

    /**
     * Size ve quality belirle
     */
    private function determineSizeAndQuality(MediaRequest $request): array
    {
        $orientation = $request->orientation;

        // Default: standard quality (ucuz)
        $quality = 'standard';

        // Size'ı orientation'a göre seç
        $size = match($orientation) {
            'landscape' => '1792x1024', // Horizontal (cheapest landscape)
            'portrait' => '1024x1792',  // Vertical
            default => '1024x1024',     // Square
        };

        return [$size, $quality];
    }

    /**
     * Maliyeti hesapla
     */
    private function calculateCost(string $size, string $quality): float
    {
        return self::COST_MAP[$size][$quality] ?? 0.0;
    }

    /**
     * Prompt'u AI için optimize et
     */
    private function optimizePrompt(string $query): string
    {
        // Basit prompt enhancement
        // Blog/product bağlamında daha iyi sonuç için
        return "Professional high-quality photograph of {$query}, sharp focus, well-lit, commercial photography style";
    }
}
