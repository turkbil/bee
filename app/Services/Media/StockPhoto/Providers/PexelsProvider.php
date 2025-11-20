<?php

namespace App\Services\Media\StockPhoto\Providers;

use App\Services\Media\StockPhoto\Contracts\StockPhotoProviderInterface;
use App\Services\Media\StockPhoto\DTOs\MediaRequest;
use App\Services\Media\StockPhoto\DTOs\MediaResponse;
use App\Services\Media\StockPhoto\Exceptions\MediaNotFoundException;
use App\Services\Media\StockPhoto\Exceptions\ProviderNotAvailableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pexels Provider
 *
 * API Docs: https://www.pexels.com/api/documentation/
 * Cost: FREE (Unlimited)
 * Rate Limit: 200 requests/hour
 */
class PexelsProvider implements StockPhotoProviderInterface
{
    private const API_BASE_URL = 'https://api.pexels.com/v1';
    private const API_TIMEOUT = 30;

    private ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.pexels.api_key');
    }

    public function getName(): string
    {
        return 'pexels';
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
            $response = Http::timeout(self::API_TIMEOUT)
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                ])
                ->get(self::API_BASE_URL . '/search', [
                    'query' => $request->query,
                    'orientation' => $request->orientation,
                    'per_page' => $request->perPage,
                    'locale' => $request->locale,
                ]);

            if ($response->failed()) {
                if ($response->status() === 429) {
                    throw ProviderNotAvailableException::rateLimitExceeded($this->getName());
                }

                throw ProviderNotAvailableException::forProvider(
                    $this->getName(),
                    "HTTP {$response->status()}"
                );
            }

            $data = $response->json();

            if (empty($data['photos'])) {
                throw MediaNotFoundException::forQuery($this->getName(), $request->query);
            }

            // Random bir görsel seç (çeşitlilik için)
            $photo = $data['photos'][array_rand($data['photos'])];

            // En uygun boyutu seç
            $imageUrl = $this->selectBestSize($photo, $request);

            return new MediaResponse(
                url: $imageUrl,
                width: $photo['width'],
                height: $photo['height'],
                provider: $this->getName(),
                photographer: $photo['photographer'] ?? null,
                photographerUrl: $photo['photographer_url'] ?? null,
                providerUrl: $photo['url'] ?? null,
                providerId: $photo['id'] ?? null,
                altText: $photo['alt'] ?? $request->query,
                metadata: [
                    'cost' => 0.0, // FREE
                    'avg_color' => $photo['avg_color'] ?? null,
                    'original_width' => $photo['width'],
                    'original_height' => $photo['height'],
                ]
            );

        } catch (MediaNotFoundException|ProviderNotAvailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Pexels API error', [
                'error' => $e->getMessage(),
                'query' => $request->query,
            ]);

            throw ProviderNotAvailableException::forProvider(
                $this->getName(),
                $e->getMessage()
            );
        }
    }

    public function getRateLimit(): array
    {
        // Pexels: 200 requests/hour
        return [
            'remaining' => null, // API response'da yok
            'limit' => 200,
            'reset_at' => null,
        ];
    }

    /**
     * En uygun boyutu seç
     */
    private function selectBestSize(array $photo, MediaRequest $request): string
    {
        $sizes = $photo['src'] ?? [];

        // Öncelik sırası: large2x > large > medium > original
        if (!empty($sizes['large2x'])) {
            return $sizes['large2x'];
        }

        if (!empty($sizes['large'])) {
            return $sizes['large'];
        }

        if (!empty($sizes['medium'])) {
            return $sizes['medium'];
        }

        return $sizes['original'] ?? $sizes['large'] ?? '';
    }
}
