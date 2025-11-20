<?php

namespace Modules\MediaManagement\App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Trait HasMediaManagement
 *
 * Model'lere media yönetimi ekler
 * Spatie Media Library üzerine kurulu universal sistem
 *
 * @package Modules\MediaManagement\App\Traits
 */
trait HasMediaManagement
{
    use InteractsWithMedia;

    /**
     * Media collections tanımla
     * Model config'den veya default template'lerden alır
     */
    public function registerMediaCollections(): void
    {
        $collections = $this->getMediaCollectionsConfig();

        foreach ($collections as $collectionName => $config) {
            $collection = $this->addMediaCollection($collectionName);

            // ✅ Tenant-aware disk kullan
            if (method_exists($this, 'getMediaDisk')) {
                $collection->useDisk($this->getMediaDisk($collectionName));
            }

            // Single file check
            if ($config['single_file'] ?? false) {
                $collection->singleFile();
            }

            // Max items
            if (isset($config['max_items']) && $config['max_items'] > 1) {
                // Spatie doesn't have max_items, but we can validate in component
            }

            // Allowed MIME types
            $mediaType = $config['type'] ?? 'image';
            $mimeTypes = config("mediamanagement.media_types.{$mediaType}.mime_types", []);
            if (!empty($mimeTypes)) {
                $collection->acceptsMimeTypes($mimeTypes);
            }

            // Note: Max file size validation is handled at Livewire component level
            // Spatie Media Library v11 doesn't have maxFilesize() method
        }
    }

    /**
     * Media conversions tanımla (sadece image için)
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $collections = $this->getMediaCollectionsConfig();

        foreach ($collections as $collectionName => $config) {
            // Sadece image type için conversion
            if (($config['type'] ?? 'image') !== 'image') {
                continue;
            }

            $conversions = $config['conversions'] ?? [];
            foreach ($conversions as $conversionName) {
                $conversionConfig = config("mediamanagement.conversions.{$conversionName}", []);

                if (empty($conversionConfig)) {
                    continue;
                }

                $conversion = $this->addMediaConversion($conversionName)
                    ->performOnCollections($collectionName);

                // Width & Height
                if (isset($conversionConfig['width'])) {
                    $conversion->width($conversionConfig['width']);
                }
                if (isset($conversionConfig['height'])) {
                    $conversion->height($conversionConfig['height']);
                }

                // Format
                if (isset($conversionConfig['format'])) {
                    $conversion->format($conversionConfig['format']);
                }

                // Quality
                if (isset($conversionConfig['quality'])) {
                    $conversion->quality($conversionConfig['quality']);
                }

                // Responsive images
                if ($conversionConfig['responsive'] ?? false) {
                    $conversion->withResponsiveImages();
                }

                // Queue
                if ($conversionConfig['queued'] ?? true) {
                    $conversion->queued();
                } else {
                    $conversion->nonQueued();
                }
            }
        }
    }

    /**
     * Model'in media collections config'ini al
     * Öncelik: Model property > Module config > Default templates
     */
    protected function getMediaCollectionsConfig(): array
    {
        // 1. Model property (custom name to avoid conflicts)
        if (isset($this->mediaConfig) && is_array($this->mediaConfig)) {
            $collections = $this->mediaConfig;
        } else {
            // 2. Module config
            $moduleName = $this->getModuleName();
            $moduleConfig = config("{$moduleName}.media.collections");
            if (!empty($moduleConfig)) {
                $collections = $moduleConfig;
            } else {
                // 3. Default templates (featured_image + gallery)
                $collections = [
                    'featured_image' => config('mediamanagement.collection_templates.featured_image'),
                    'gallery' => config('mediamanagement.collection_templates.gallery'),
                ];
            }
        }

        $seoOgConfig = config('mediamanagement.collection_templates.seo_og_image');
        if ($seoOgConfig && !isset($collections['seo_og_image'])) {
            $collections['seo_og_image'] = $seoOgConfig;
        }

        return $collections;
    }

    /**
     * Model'in modül adını tespit et
     */
    protected function getModuleName(): string
    {
        $class = get_class($this);

        // Modules\Announcement\App\Models\Announcement -> announcement
        if (preg_match('/Modules\\\\(\w+)\\\\/', $class, $matches)) {
            return strtolower($matches[1]);
        }

        return 'default';
    }

    /**
     * Media collection'ı izin verilen tiplere göre filtrele
     */
    public function getMediaByType(string $collectionName, string $type = 'image')
    {
        return $this->getMedia($collectionName)->filter(function ($media) use ($type) {
            $mimeTypes = config("mediamanagement.media_types.{$type}.mime_types", []);
            return in_array($media->mime_type, $mimeTypes);
        });
    }

    /**
     * Featured image var mı?
     */
    public function hasFeaturedImage(): bool
    {
        return $this->hasMedia('featured_image');
    }

    /**
     * Gallery var mı?
     */
    public function hasGallery(): bool
    {
        return $this->hasMedia('gallery');
    }

    /**
     * Featured image URL
     */
    public function featuredImageUrl(string $conversion = ''): ?string
    {
        if (!$this->hasFeaturedImage()) {
            return null;
        }

        return $this->getFirstMediaUrl('featured_image', $conversion);
    }

    /**
     * Gallery images array
     */
    public function galleryImages(string $conversion = ''): array
    {
        return $this->getMedia('gallery')
            ->sortBy('order_column')
            ->map(function ($media) use ($conversion) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl($conversion),
                    'thumb' => $media->getUrl('thumb'),
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $media->human_readable_size,
                    'mime_type' => $media->mime_type,
                    'order' => $media->order_column,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Spatie Media Library için disk belirleme (tenant-aware)
     * Bu method media kaydolmadan önce çağrılır
     *
     * ⚠️ NOT: Model'ler override edebilir (örn: Setting model gibi)
     */
    public function getMediaDisk(?string $collectionName = null): string
    {
        // 1. Yöntem: tenant() helper (eğer tenancy initialized ise)
        $tenantId = null;
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant('id');
        }

        // 2. Yöntem: Request'ten domain çöz (fallback)
        if (!$tenantId && request()) {
            $host = request()->getHost();
            $centralDomains = config('tenancy.central_domains', []);

            // Central domain değilse tenant'ı bul
            if (!in_array($host, $centralDomains)) {
                try {
                    $domainModel = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
                    if ($domainModel && $domainModel->tenant_id) {
                        $tenantId = $domainModel->tenant_id;
                    }
                } catch (\Exception $e) {
                    // Fallback to public disk
                }
            }
        }

        // Tenant context varsa tenant disk kullan
        if ($tenantId) {
            // ✅ FIX: Her tenant için ayrı disk yerine tek 'tenant' disk kullan
            // Runtime'da doğru tenant için yapılandırılacak
            $diskName = 'tenant';

            // Disk yapılandırmasını tenant-specific olarak ayarla
            // ⚠️ base_path kullan, storage_path değil! (Stancl Tenancy storage_path'i override eder)
            $root = base_path("storage/tenant{$tenantId}/app/public");

            // Directory yoksa oluştur
            if (!is_dir($root)) {
                @mkdir($root, 0775, true);
            }

            // 🔥 Request'ten gerçek URL al (config('app.url') yanlış domain döndürüyor!)
            $appUrl = request() ? request()->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

            config([
                'filesystems.disks.tenant' => [
                    'driver' => 'local',
                    'root' => $root,
                    'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantId}" : null,
                    'visibility' => 'public',
                    'throw' => false,
                ],
            ]);

            return $diskName;
        }

        // Central context için public disk
        return 'public';
    }
}
