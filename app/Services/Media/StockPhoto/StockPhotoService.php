<?php

namespace App\Services\Media\StockPhoto;

use App\Services\Media\StockPhoto\Contracts\StockPhotoProviderInterface;
use App\Services\Media\StockPhoto\DTOs\MediaRequest;
use App\Services\Media\StockPhoto\DTOs\MediaResponse;
use App\Services\Media\StockPhoto\Exceptions\MediaNotFoundException;
use App\Services\Media\StockPhoto\Exceptions\ProviderNotAvailableException;
use App\Services\Media\StockPhoto\Providers\DallEProvider;
use App\Services\Media\StockPhoto\Providers\PexelsProvider;
use App\Services\Media\StockPhoto\Providers\PixabayProvider;
use App\Services\Media\StockPhoto\Providers\UnsplashProvider;
use Illuminate\Support\Facades\Log;

/**
 * Stock Photo Service
 *
 * Provider yönetimi ve görsel arama/indirme işlemleri
 */
class StockPhotoService
{
    private array $providers = [];
    private array $providerInstances = [];

    public function __construct()
    {
        $this->registerDefaultProviders();
    }

    /**
     * Varsayılan provider'ları kaydet
     */
    private function registerDefaultProviders(): void
    {
        $this->registerProvider('pexels', PexelsProvider::class);
        $this->registerProvider('unsplash', UnsplashProvider::class);
        $this->registerProvider('pixabay', PixabayProvider::class);
        $this->registerProvider('dalle', DallEProvider::class);
    }

    /**
     * Provider kaydet
     */
    public function registerProvider(string $name, string $class): void
    {
        $this->providers[$name] = $class;
    }

    /**
     * Provider instance al
     */
    public function getProvider(string $name): StockPhotoProviderInterface
    {
        if (!isset($this->providerInstances[$name])) {
            if (!isset($this->providers[$name])) {
                throw new \InvalidArgumentException("Provider '{$name}' not registered");
            }

            $this->providerInstances[$name] = app($this->providers[$name]);
        }

        return $this->providerInstances[$name];
    }

    /**
     * Kullanılabilir provider'ları listele
     */
    public function getAvailableProviders(): array
    {
        $available = [];

        foreach ($this->providers as $name => $class) {
            $provider = $this->getProvider($name);

            if ($provider->isAvailable()) {
                $available[] = $name;
            }
        }

        return $available;
    }

    /**
     * Belirli bir provider ile görsel al
     */
    public function fetchFromProvider(string $providerName, MediaRequest $request): MediaResponse
    {
        $provider = $this->getProvider($providerName);

        if (!$provider->isAvailable()) {
            throw ProviderNotAvailableException::forProvider($providerName);
        }

        Log::info("Fetching media from {$providerName}", [
            'query' => $request->query,
            'orientation' => $request->orientation,
        ]);

        try {
            $response = $provider->fetch($request);

            Log::info("Media fetched successfully from {$providerName}", [
                'provider' => $response->provider,
                'cost' => $response->getCost(),
                'url' => $response->url,
            ]);

            return $response;

        } catch (MediaNotFoundException $e) {
            Log::warning("No media found on {$providerName}", [
                'query' => $request->query,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } catch (ProviderNotAvailableException $e) {
            Log::error("Provider {$providerName} not available", [
                'query' => $request->query,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Otomatik provider seçimi ile görsel al
     *
     * Strategy:
     * 1. Primary provider'dan dene
     * 2. Başarısız olursa fallback'lere geç
     * 3. Hepsi başarısız olursa exception fırlat
     */
    public function fetch(MediaRequest $request, ?array $providerOrder = null): MediaResponse
    {
        $providers = $providerOrder ?? $this->getAvailableProviders();

        if (empty($providers)) {
            throw new ProviderNotAvailableException('No providers available');
        }

        $errors = [];

        foreach ($providers as $providerName) {
            try {
                return $this->fetchFromProvider($providerName, $request);

            } catch (MediaNotFoundException $e) {
                $errors[$providerName] = $e->getMessage();
                continue;

            } catch (ProviderNotAvailableException $e) {
                $errors[$providerName] = $e->getMessage();
                continue;
            }
        }

        // Hepsi başarısız oldu
        Log::error('All providers failed', [
            'query' => $request->query,
            'tried_providers' => array_keys($errors),
            'errors' => $errors,
        ]);

        throw new MediaNotFoundException(
            "No media found after trying " . count($errors) . " provider(s): " .
            implode(', ', array_keys($errors))
        );
    }

    /**
     * Random provider seçimi ile görsel al
     */
    public function fetchRandom(MediaRequest $request): MediaResponse
    {
        $available = $this->getAvailableProviders();

        if (empty($available)) {
            throw new ProviderNotAvailableException('No providers available');
        }

        // Random sırala
        shuffle($available);

        return $this->fetch($request, $available);
    }

    /**
     * Ücretsiz provider'lardan görsel al (DALL-E hariç)
     */
    public function fetchFree(MediaRequest $request): MediaResponse
    {
        $freeProviders = ['pexels', 'unsplash', 'pixabay'];
        $available = array_intersect($freeProviders, $this->getAvailableProviders());

        if (empty($available)) {
            throw new ProviderNotAvailableException('No free providers available');
        }

        // Random sırala (çeşitlilik için)
        shuffle($available);

        return $this->fetch($request, $available);
    }

    /**
     * Provider istatistikleri
     */
    public function getProviderStats(): array
    {
        $stats = [];

        foreach ($this->providers as $name => $class) {
            $provider = $this->getProvider($name);

            $stats[$name] = [
                'name' => $provider->getName(),
                'available' => $provider->isAvailable(),
                'rate_limit' => $provider->getRateLimit(),
            ];
        }

        return $stats;
    }
}
