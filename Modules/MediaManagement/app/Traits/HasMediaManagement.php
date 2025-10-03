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
            return $this->mediaConfig;
        }

        // 2. Module config
        $moduleName = $this->getModuleName();
        $moduleConfig = config("{$moduleName}.media.collections");
        if (!empty($moduleConfig)) {
            return $moduleConfig;
        }

        // 3. Default templates (featured_image + gallery)
        return [
            'featured_image' => config('mediamanagement.collection_templates.featured_image'),
            'gallery' => config('mediamanagement.collection_templates.gallery'),
        ];
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
}
