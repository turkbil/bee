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
 * Pixabay Provider
 *
 * API Docs: https://pixabay.com/api/docs/
 * Cost: FREE (Unlimited)
 * Rate Limit: None (unlimited)
 */
class PixabayProvider implements StockPhotoProviderInterface
{
    private const API_BASE_URL = 'https://pixabay.com/api/';
    private const API_TIMEOUT = 30;

    private ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.pixabay.api_key');
    }

    public function getName(): string
    {
        return 'pixabay';
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
                ->get(self::API_BASE_URL, [
                    'key' => $this->apiKey,
                    'q' => $request->query,
                    'orientation' => $this->mapOrientation($request->orientation),
                    'per_page' => $request->perPage,
                    'lang' => $request->locale,
                    'image_type' => 'photo',
                    'safesearch' => 'true',
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

            if (empty($data['hits'])) {
                throw MediaNotFoundException::forQuery($this->getName(), $request->query);
            }

            // Random bir görsel seç
            $photo = $data['hits'][array_rand($data['hits'])];

            // En uygun boyutu seç
            $imageUrl = $this->selectBestSize($photo);

            return new MediaResponse(
                url: $imageUrl,
                width: $photo['imageWidth'],
                height: $photo['imageHeight'],
                provider: $this->getName(),
                photographer: $photo['user'] ?? null,
                photographerUrl: "https://pixabay.com/users/{$photo['user']}-{$photo['user_id']}/",
                providerUrl: $photo['pageURL'] ?? null,
                providerId: $photo['id'] ?? null,
                altText: $this->generateAltText($photo, $request->query),
                metadata: [
                    'cost' => 0.0, // FREE
                    'views' => $photo['views'] ?? 0,
                    'downloads' => $photo['downloads'] ?? 0,
                    'likes' => $photo['likes'] ?? 0,
                    'tags' => $photo['tags'] ?? null,
                    'original_width' => $photo['imageWidth'],
                    'original_height' => $photo['imageHeight'],
                ]
            );

        } catch (MediaNotFoundException|ProviderNotAvailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Pixabay API error', [
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
        // Pixabay: Unlimited (no rate limit)
        return [
            'remaining' => null,
            'limit' => null,
            'reset_at' => null,
        ];
    }

    /**
     * Orientation mapping (Pixabay uses different values)
     */
    private function mapOrientation(string $orientation): string
    {
        return match($orientation) {
            'landscape' => 'horizontal',
            'portrait' => 'vertical',
            default => 'all',
        };
    }

    /**
     * En uygun boyutu seç
     */
    private function selectBestSize(array $photo): string
    {
        // Öncelik sırası: largeImageURL > webformatURL > previewURL
        if (!empty($photo['largeImageURL'])) {
            return $photo['largeImageURL'];
        }

        if (!empty($photo['webformatURL'])) {
            return $photo['webformatURL'];
        }

        return $photo['previewURL'] ?? '';
    }

    /**
     * Alt text oluştur (Pixabay alt text sağlamıyor)
     */
    private function generateAltText(array $photo, string $query): string
    {
        $tags = $photo['tags'] ?? $query;

        if (is_string($tags)) {
            return ucfirst($tags);
        }

        return ucfirst($query);
    }
}
