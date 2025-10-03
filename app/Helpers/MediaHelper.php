<?php

/**
 * Media Helper Functions
 * Spatie Media Library için kısa erişim fonksiyonları
 */

if (!function_exists('featured')) {
    /**
     * Featured image URL'i döndür
     *
     * @param \Spatie\MediaLibrary\HasMedia|null $model
     * @param string $conversion Conversion adı (thumb, medium, large, responsive)
     * @return string Image URL veya placeholder
     *
     * @example featured($announcement) // Original
     * @example featured($announcement, 'thumb') // Thumbnail
     * @example featured($page, 'medium') // Medium size
     */
    function featured($model, string $conversion = ''): string
    {
        if (!$model || !method_exists($model, 'hasMedia')) {
            return asset('admin-assets/images/placeholder.jpg');
        }

        if (!$model->hasMedia('featured_image')) {
            return asset('admin-assets/images/placeholder.jpg');
        }

        return $model->getFirstMediaUrl('featured_image', $conversion);
    }
}

if (!function_exists('gallery')) {
    /**
     * Gallery görselleri dizisi döndür
     *
     * @param \Spatie\MediaLibrary\HasMedia|null $model
     * @param string $conversion Conversion adı
     * @return array Gallery media array
     *
     * @example gallery($announcement) // Tüm görseller
     * @example gallery($announcement, 'thumb') // Thumbnails
     */
    function gallery($model, string $conversion = ''): array
    {
        if (!$model || !method_exists($model, 'getMedia')) {
            return [];
        }

        return $model->getMedia('gallery')
            ->sortBy('order_column') // Sıralama ekle
            ->map(function ($media) use ($conversion) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl($conversion),
                    'thumb' => $media->getUrl('thumb'),
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $media->human_readable_size,
                    'mime_type' => $media->mime_type,
                    'responsive' => $media->responsive_images($conversion),
                    'order' => $media->order_column,
                ];
            })->values()->toArray();
    }
}

if (!function_exists('thumb')) {
    /**
     * Media thumbnail URL'i döndür
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
     * @return string Thumbnail URL
     *
     * @example thumb($media)
     */
    function thumb($media): string
    {
        if (!$media) {
            return asset('admin-assets/images/placeholder.jpg');
        }

        return $media->getUrl('thumb');
    }
}

if (!function_exists('media_url')) {
    /**
     * Media URL'i conversion ile döndür
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
     * @param string $conversion Conversion adı
     * @return string Media URL
     *
     * @example media_url($media) // Original
     * @example media_url($media, 'medium') // Medium size
     */
    function media_url($media, string $conversion = ''): string
    {
        if (!$media) {
            return asset('admin-assets/images/placeholder.jpg');
        }

        return $media->getUrl($conversion);
    }
}

if (!function_exists('responsive_image')) {
    /**
     * Responsive image srcset döndür (CDN için)
     *
     * @param \Spatie\MediaLibrary\HasMedia $model
     * @param string $collection Collection adı
     * @param string $conversion Conversion adı
     * @return string|null Responsive srcset
     *
     * @example responsive_image($announcement, 'featured_image', 'responsive')
     */
    function responsive_image($model, string $collection = 'featured_image', string $conversion = 'responsive'): ?string
    {
        if (!$model || !$model->hasMedia($collection)) {
            return null;
        }

        $media = $model->getFirstMedia($collection);
        return $media?->getSrcset($conversion);
    }
}
