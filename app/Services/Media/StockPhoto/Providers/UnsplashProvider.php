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
 * Unsplash Provider
 *
 * API Docs: https://unsplash.com/documentation
 * Cost: FREE
 * Rate Limit: 50 requests/hour (demo), 5000 requests/hour (production)
 */
class UnsplashProvider implements StockPhotoProviderInterface
{
    private const API_BASE_URL = 'https://api.unsplash.com';
    private const API_TIMEOUT = 30;

    private ?string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.unsplash.access_key');
    }

    public function getName(): string
    {
        return 'unsplash';
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
                    'Authorization' => 'Client-ID ' . $this->apiKey,
                ])
                ->get(self::API_BASE_URL . '/search/photos', [
                    'query' => $request->query,
                    'orientation' => $request->orientation,
                    'per_page' => $request->perPage,
                    'color' => $request->color,
                ]);

            if ($response->failed()) {
                if ($response->status() === 429 || $response->status() === 403) {
                    throw ProviderNotAvailableException::rateLimitExceeded($this->getName());
                }

                throw ProviderNotAvailableException::forProvider(
                    $this->getName(),
                    "HTTP {$response->status()}"
                );
            }

            $data = $response->json();

            if (empty($data['results'])) {
                throw MediaNotFoundException::forQuery($this->getName(), $request->query);
            }

            // Random bir görsel seç
            $photo = $data['results'][array_rand($data['results'])];

            // Download endpoint'ini tetikle (Unsplash guidelines)
            if (!empty($photo['links']['download_location'])) {
                Http::withHeaders([
                    'Authorization' => 'Client-ID ' . $this->apiKey,
                ])->get($photo['links']['download_location']);
            }

            // En uygun boyutu seç
            $imageUrl = $this->selectBestSize($photo, $request);

            return new MediaResponse(
                url: $imageUrl,
                width: $photo['width'],
                height: $photo['height'],
                provider: $this->getName(),
                photographer: $photo['user']['name'] ?? null,
                photographerUrl: $photo['user']['links']['html'] ?? null,
                providerUrl: $photo['links']['html'] ?? null,
                providerId: $photo['id'] ?? null,
                altText: $photo['alt_description'] ?? $photo['description'] ?? $request->query,
                metadata: [
                    'cost' => 0.0, // FREE
                    'color' => $photo['color'] ?? null,
                    'likes' => $photo['likes'] ?? 0,
                    'downloads' => $photo['downloads'] ?? 0,
                    'original_width' => $photo['width'],
                    'original_height' => $photo['height'],
                ]
            );

        } catch (MediaNotFoundException|ProviderNotAvailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unsplash API error', [
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
        // Unsplash: 50/hour (demo) veya 5000/hour (production)
        return [
            'remaining' => null, // Response header'dan alınabilir
            'limit' => 5000,
            'reset_at' => null,
        ];
    }

    /**
     * En uygun boyutu seç
     */
    private function selectBestSize(array $photo, MediaRequest $request): string
    {
        $urls = $photo['urls'] ?? [];

        // Öncelik sırası: full > regular > small
        if (!empty($urls['full'])) {
            return $urls['full'];
        }

        if (!empty($urls['regular'])) {
            return $urls['regular'];
        }

        if (!empty($urls['small'])) {
            return $urls['small'];
        }

        return $urls['raw'] ?? '';
    }
}
