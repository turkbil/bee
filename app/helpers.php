<?php

/**
 * Laravel 12 Compatibility Helpers
 *
 * Laravel 12'de kaldırılan helper fonksiyonlar
 */

if (!function_exists('array_last')) {
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    function array_last($array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : end($array);
        }

        return Illuminate\Support\Arr::last($array, $callback, $default);
    }
}

/**
 * ======================================================================
 * Thumbmaker Helper Functions
 * ======================================================================
 */

if (!function_exists('thumb')) {
    /**
     * Universal Thumbmaker Helper
     *
     * Anında görsel boyutlandırma, format dönüştürme
     *
     * @param  string|object  $src  Görsel URL veya Media objesi
     * @param  int|null  $width  Genişlik
     * @param  int|null  $height  Yükseklik
     * @param  array  $options  Ek parametreler [quality, alignment, scale, format, cache]
     * @return string  Thumbmaker URL
     *
     * Örnek Kullanım:
     * thumb($media, 400, 300)
     * thumb($media, 400, 300, ['quality' => 90, 'alignment' => 'c'])
     * thumb('https://example.com/image.jpg', 800, null, ['format' => 'webp'])
     */
    function thumb($src, ?int $width = null, ?int $height = null, array $options = []): string
    {
        // Media objesi ise URL'e çevir
        if (is_object($src) && method_exists($src, 'getUrl')) {
            $src = $src->getUrl();
        }

        // Eğer src zaten thumbmaker URL'i ise direkt döndür
        if (str_contains($src, '/thumbmaker?')) {
            return $src;
        }

        // Parametreleri hazırla
        $params = array_filter([
            'src' => $src,
            'w' => $width,
            'h' => $height,
            'q' => $options['quality'] ?? null,
            'a' => $options['alignment'] ?? null,
            's' => $options['scale'] ?? null,
            'f' => $options['format'] ?? 'webp',
            'c' => $options['cache'] ?? 1,
        ]);

        return route('thumbmaker', $params);
    }
}

if (!function_exists('thumb_url')) {
    /**
     * Thumbmaker URL Builder (Alternatif isim)
     *
     * @param  string  $src
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  int  $quality
     * @return string
     */
    function thumb_url(string $src, ?int $width = null, ?int $height = null, int $quality = 85): string
    {
        return thumb($src, $width, $height, ['quality' => $quality]);
    }
}
