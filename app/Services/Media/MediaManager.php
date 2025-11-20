<?php

namespace App\Services\Media;

use App\Services\Media\StockPhoto\DTOs\MediaRequest;
use App\Services\Media\StockPhoto\DTOs\MediaResponse;
use App\Services\Media\StockPhoto\StockPhotoService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;

/**
 * Media Manager
 *
 * Universal media management facade
 * Blog, Product, Page gibi tüm modüller bu service'i kullanır
 */
class MediaManager
{
    private StockPhotoService $stockPhotoService;

    public function __construct(StockPhotoService $stockPhotoService)
    {
        $this->stockPhotoService = $stockPhotoService;
    }

    /**
     * Görsel al ve model'e ekle
     *
     * @param HasMedia $model Model (Blog, Product, Page vs)
     * @param string $query Arama terimi
     * @param string $collectionName Media collection adı (default: 'default')
     * @param array $options Ek parametreler
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function fetchAndAttach(
        HasMedia $model,
        string $query,
        string $collectionName = 'default',
        array $options = []
    ) {
        // Tenant-aware config al
        $config = $this->getTenantConfig();

        // MediaRequest oluştur
        $request = MediaRequest::fromArray([
            'query' => $query,
            'orientation' => $options['orientation'] ?? 'landscape',
            'width' => $options['width'] ?? null,
            'height' => $options['height'] ?? null,
            'locale' => $options['locale'] ?? app()->getLocale(),
            'metadata' => array_merge($options['metadata'] ?? [], [
                'tenant_id' => tenant('id'),
                'context' => $this->getContextFromModel($model),
            ]),
        ]);

        // Provider stratejisine göre görsel al
        $response = $this->fetchMedia($request, $config);

        // Görseli indir ve model'e ekle
        return $this->attachMediaToModel($model, $response, $collectionName, $options);
    }

    /**
     * Sadece görsel al (model'e ekleme)
     */
    public function fetch(string $query, array $options = []): MediaResponse
    {
        $config = $this->getTenantConfig();

        $request = MediaRequest::fromArray([
            'query' => $query,
            'orientation' => $options['orientation'] ?? 'landscape',
            'width' => $options['width'] ?? null,
            'height' => $options['height'] ?? null,
            'locale' => $options['locale'] ?? app()->getLocale(),
            'metadata' => $options['metadata'] ?? [],
        ]);

        return $this->fetchMedia($request, $config);
    }

    /**
     * Tenant-aware configuration al
     */
    private function getTenantConfig(): array
    {
        // Default config
        $config = [
            'use_ai' => false, // Varsayılan: Stock photo (ücretsiz)
            'strategy' => 'free', // 'free', 'random', 'specific', 'fallback'
            'providers' => ['pexels', 'unsplash', 'pixabay'], // Provider sırası
        ];

        // Tenant settings'den "Fotoğraflar nereden üretilsin?" ayarını çek
        if (function_exists('getTenantSetting')) {
            // blog_ai_blog_fotograflar_nereden_uretilsin: 1 = Yapay Zeka, 0 = Stok Foto
            $useAI = getTenantSetting('blog_ai_blog_fotograflar_nereden_uretilsin', false);

            $config['use_ai'] = (bool) $useAI;

            // AI kullanılacaksa DALL-E'yi provider listesine ekle
            if ($config['use_ai']) {
                $config['strategy'] = 'specific';
                $config['providers'] = ['dalle']; // Sadece DALL-E kullan
            } else {
                // Stock photo kullanılacak (ücretsiz)
                $strategy = getTenantSetting('media_provider_strategy', $config['strategy']);
                $providers = getTenantSetting('media_providers', $config['providers']);

                $config['strategy'] = $strategy;
                $config['providers'] = is_array($providers) ? $providers : explode(',', $providers);
            }
        }

        return $config;
    }

    /**
     * Provider stratejisine göre görsel al
     */
    private function fetchMedia(MediaRequest $request, array $config): MediaResponse
    {
        $strategy = $config['strategy'];
        $providers = $config['providers'];
        $useAI = $config['use_ai'];

        Log::info('📸 MediaManager: Fetching media', [
            'query' => $request->query,
            'use_ai' => $useAI,
            'strategy' => $strategy,
            'providers' => $providers,
            'tenant_id' => tenant('id'),
        ]);

        return match ($strategy) {
            'free' => $this->stockPhotoService->fetchFree($request),
            'random' => $this->stockPhotoService->fetchRandom($request),
            'specific' => $this->stockPhotoService->fetch($request, $providers),
            'fallback' => $this->stockPhotoService->fetch($request),
            default => $this->stockPhotoService->fetchFree($request),
        };
    }

    /**
     * Görseli model'e ekle
     */
    private function attachMediaToModel(
        HasMedia $model,
        MediaResponse $response,
        string $collectionName,
        array $options = []
    ) {
        // 🔥 FIX: Tenant disk'i BURADA yapılandır (file copy ÖNCESINDE!)
        $diskName = $this->prepareTenantDisk();

        // Görseli indir
        $imageContent = Http::timeout(60)->get($response->url)->body();

        // Geçici dosya oluştur
        $tempPath = sys_get_temp_dir() . '/' . uniqid('media_') . '.jpg';
        file_put_contents($tempPath, $imageContent);

        try {
            // Model'e ekle - DISK AÇIKCA BELİRT!
            $media = $model->addMedia($tempPath)
                ->usingFileName(uniqid('media_') . '.jpg')
                ->toMediaCollection($collectionName, $diskName);

            // Custom properties ekle (metadata)
            $media->setCustomProperty('provider', $response->provider);
            $media->setCustomProperty('width', $response->width);
            $media->setCustomProperty('height', $response->height);
            $media->setCustomProperty('cost', $response->getCost());

            if ($response->photographer) {
                $media->setCustomProperty('photographer', $response->photographer);
                $media->setCustomProperty('photographer_url', $response->photographerUrl);
            }

            if ($response->providerUrl) {
                $media->setCustomProperty('provider_url', $response->providerUrl);
            }

            if ($response->providerId) {
                $media->setCustomProperty('provider_id', $response->providerId);
            }

            $media->save();

            Log::info('MediaManager: Media attached successfully', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'provider' => $response->provider,
                'cost' => $response->getCost(),
                'collection' => $collectionName,
            ]);

            return $media;

        } finally {
            // Geçici dosyayı sil
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    /**
     * Model'den context belirle
     */
    private function getContextFromModel(HasMedia $model): string
    {
        $class = get_class($model);

        if (str_contains($class, 'Blog')) {
            return 'blog';
        }

        if (str_contains($class, 'Product')) {
            return 'product';
        }

        if (str_contains($class, 'Page')) {
            return 'page';
        }

        return 'unknown';
    }

    /**
     * Provider istatistikleri
     */
    public function getProviderStats(): array
    {
        return $this->stockPhotoService->getProviderStats();
    }

    /**
     * Kullanılabilir provider'ları listele
     */
    public function getAvailableProviders(): array
    {
        return $this->stockPhotoService->getAvailableProviders();
    }

    /**
     * Tenant disk'i yapılandır ve disk adını döndür
     *
     * 🔥 FIX: Runtime disk config MediaLibrary file copy sırasında kayboluyordu
     * Bu method ÖNCE config'i ayarlıyor, sonra disk adını dönüyor
     */
    private function prepareTenantDisk(): string
    {
        // Tenant context var mı?
        $tenantId = null;
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant('id');
        }

        // Tenant yoksa public disk kullan
        if (!$tenantId) {
            return 'public';
        }

        // Tenant disk config'ini ayarla
        // ⚠️ base_path kullan, storage_path değil! (Stancl Tenancy storage_path'i override eder)
        $root = base_path("storage/tenant{$tenantId}/app/public");

        // Directory yoksa oluştur
        if (!is_dir($root)) {
            @mkdir($root, 0775, true);
        }

        // Request'ten gerçek URL al
        $appUrl = request() ? request()->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

        // Tenant disk config'ini RUNTIME'da ayarla
        config([
            'filesystems.disks.tenant' => [
                'driver' => 'local',
                'root' => $root,
                'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantId}" : null,
                'visibility' => 'public',
                'throw' => false,
            ],
        ]);

        Log::info('MediaManager: Tenant disk configured', [
            'tenant_id' => $tenantId,
            'disk_root' => $root,
            'disk_url' => config('filesystems.disks.tenant.url'),
        ]);

        return 'tenant';
    }
}
