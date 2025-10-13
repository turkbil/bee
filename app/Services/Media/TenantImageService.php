<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TenantImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        // GD driver kullanarak Image Manager oluştur
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Tenant-aware görsel yükleme ve işleme
     *
     * @param string $sourcePath Kaynak dosya yolu
     * @param string $targetPath Hedef dosya yolu (tenant-aware olacak)
     * @param array $options İşleme seçenekleri (width, height, quality, format)
     * @return string İşlenmiş dosyanın tam yolu
     */
    public function processImage(string $sourcePath, string $targetPath, array $options = []): string
    {
        $tenantId = $this->getTenantId();

        // Varsayılan seçenekler
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $quality = $options['quality'] ?? 90;
        $format = $options['format'] ?? null;

        // Görseli yükle
        $image = $this->manager->read($sourcePath);

        // Boyutlandırma
        if ($width || $height) {
            $image->scale($width, $height);
        }

        // Format dönüşümü ve kaydetme
        $targetPath = $this->getTenantPath($targetPath, $tenantId);
        $fullPath = storage_path('app/public/' . $targetPath);

        // Dizini oluştur
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Kaydet
        if ($format) {
            $image->save($fullPath, $quality, $format);
        } else {
            $image->save($fullPath, $quality);
        }

        return $targetPath;
    }

    /**
     * Birden fazla boyutta thumbnail oluştur
     *
     * @param string $sourcePath Kaynak dosya
     * @param string $mediaId Media model ID
     * @param array $sizes Boyutlar ['thumb' => [150, 150], 'medium' => [300, 300]]
     * @return array Oluşturulan thumbnail'lerin yolları
     */
    public function createThumbnails(string $sourcePath, string $mediaId, array $sizes = []): array
    {
        $tenantId = $this->getTenantId();
        $thumbnails = [];

        // Varsayılan boyutlar
        $defaultSizes = [
            'thumb' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 600],
        ];

        $sizes = !empty($sizes) ? $sizes : $defaultSizes;

        foreach ($sizes as $name => $dimensions) {
            [$width, $height] = $dimensions;

            $targetPath = "tenant{$tenantId}/{$mediaId}/conversions/{$name}.webp";
            $fullPath = storage_path('app/public/' . $targetPath);

            // Dizini oluştur
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Thumbnail oluştur
            $image = $this->manager->read($sourcePath);
            $image->scale($width, $height);
            $image->toWebp($quality = 90)->save($fullPath);

            $thumbnails[$name] = $targetPath;
        }

        return $thumbnails;
    }

    /**
     * Görsel optimizasyonu (mevcut dosya üzerinde)
     *
     * @param string $path Tenant-aware dosya yolu
     * @param int $quality Kalite (1-100)
     * @return bool
     */
    public function optimizeImage(string $path, int $quality = 85): bool
    {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            $image = $this->manager->read($fullPath);
            $image->save($fullPath, $quality);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format dönüştürme (örn: PNG -> WebP)
     *
     * @param string $sourcePath Kaynak yol
     * @param string $targetFormat Hedef format (webp, jpg, png)
     * @param int $quality Kalite
     * @return string|null Yeni dosya yolu
     */
    public function convertFormat(string $sourcePath, string $targetFormat = 'webp', int $quality = 90): ?string
    {
        $fullSourcePath = storage_path('app/public/' . $sourcePath);

        if (!file_exists($fullSourcePath)) {
            return null;
        }

        try {
            $pathInfo = pathinfo($sourcePath);
            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $targetFormat;
            $fullTargetPath = storage_path('app/public/' . $newPath);

            $image = $this->manager->read($fullSourcePath);

            switch ($targetFormat) {
                case 'webp':
                    $image->toWebp($quality)->save($fullTargetPath);
                    break;
                case 'jpg':
                case 'jpeg':
                    $image->toJpeg($quality)->save($fullTargetPath);
                    break;
                case 'png':
                    $image->toPng()->save($fullTargetPath);
                    break;
                default:
                    return null;
            }

            return $newPath;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Tenant ID'yi al
     */
    protected function getTenantId(): int
    {
        try {
            if (app()->bound(\Stancl\Tenancy\Tenancy::class)) {
                $tenancy = app(\Stancl\Tenancy\Tenancy::class);

                if ($tenancy->initialized) {
                    return tenant('id');
                }
            }

            return 1; // Central context - default tenant
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Tenant-aware path oluştur
     */
    protected function getTenantPath(string $path, int $tenantId): string
    {
        // Eğer path zaten tenant ile başlıyorsa, olduğu gibi döndür
        if (str_starts_with($path, "tenant{$tenantId}/")) {
            return $path;
        }

        // Tenant prefix ekle
        return "tenant{$tenantId}/" . ltrim($path, '/');
    }

    /**
     * Görsel bilgilerini al (boyut, format, vs)
     */
    public function getImageInfo(string $path): ?array
    {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return null;
        }

        try {
            $image = $this->manager->read($fullPath);

            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'mime' => mime_content_type($fullPath),
                'size' => filesize($fullPath),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
