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

if (!function_exists('media_conversion_config')) {
    /**
     * Media conversion yapılandırması döndür
     * Config dosyasından veya override değerlerle
     *
     * @param string $conversionName Conversion adı (thumb, medium, large)
     * @param array $override Override edilecek değerler
     * @return array Conversion config
     *
     * @example media_conversion_config('thumb')
     * @example media_conversion_config('thumb', ['width' => 400, 'quality' => 90])
     */
    function media_conversion_config(string $conversionName, array $override = []): array
    {
        $config = config("mediamanagement.conversions.{$conversionName}", []);

        return array_merge($config, $override);
    }
}

if (!function_exists('media_thumb_size')) {
    /**
     * Thumbnail boyutlarını döndür
     *
     * @param string $type Boyut tipi: 'width', 'height', 'both', 'aspect'
     * @return int|array|float
     *
     * @example media_thumb_size('width') // 300
     * @example media_thumb_size('height') // 200
     * @example media_thumb_size('both') // ['width' => 300, 'height' => 200]
     * @example media_thumb_size('aspect') // 1.5 (3:2 ratio)
     */
    function media_thumb_size(string $type = 'both')
    {
        $config = config('mediamanagement.conversions.thumb', []);
        $width = $config['width'] ?? 300;
        $height = $config['height'] ?? 200;

        return match($type) {
            'width' => $width,
            'height' => $height,
            'aspect' => round($width / $height, 2),
            'ratio' => $width . ':' . $height,
            'both' => ['width' => $width, 'height' => $height],
            default => ['width' => $width, 'height' => $height],
        };
    }
}

if (!function_exists('media_quality')) {
    /**
     * Media conversion kalitesini döndür
     *
     * @param string $conversionName Conversion adı
     * @return int Quality (0-100)
     *
     * @example media_quality('thumb') // 85
     * @example media_quality('medium') // 90
     */
    function media_quality(string $conversionName = 'thumb'): int
    {
        return config("mediamanagement.conversions.{$conversionName}.quality", 85);
    }
}

if (!function_exists('media_format')) {
    /**
     * Media conversion formatını döndür
     *
     * @param string $conversionName Conversion adı
     * @return string Format (webp, jpg, png)
     *
     * @example media_format('thumb') // 'webp'
     */
    function media_format(string $conversionName = 'thumb'): string
    {
        return config("mediamanagement.conversions.{$conversionName}.format", 'webp');
    }
}

if (!function_exists('media_aspect_ratio')) {
    /**
     * CSS aspect-ratio değeri döndür
     *
     * @param string $conversionName Conversion adı
     * @return string CSS aspect-ratio (ör: '3/2', '16/9')
     *
     * @example media_aspect_ratio('thumb') // '3/2'
     */
    function media_aspect_ratio(string $conversionName = 'thumb'): string
    {
        $config = config("mediamanagement.conversions.{$conversionName}", []);
        $width = $config['width'] ?? 300;
        $height = $config['height'] ?? 200;

        // GCD (Greatest Common Divisor) ile basitleştir
        $gcd = function($a, $b) use (&$gcd) {
            return $b ? $gcd($b, $a % $b) : $a;
        };

        $divisor = $gcd($width, $height);
        $ratioWidth = $width / $divisor;
        $ratioHeight = $height / $divisor;

        return $ratioWidth . '/' . $ratioHeight;
    }
}
