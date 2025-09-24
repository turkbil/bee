<?php

namespace App\Helpers;

/**
 * TENANT-SAFE ASSET HELPER
 * Asset yönetimi için tenant bağımsız helper
 */
class AssetHelper
{
    /**
     * Get versioned asset URL (cache busting)
     */
    public static function asset($path, $secure = null)
    {
        $url = asset($path, $secure);

        // Production'da asset versioning
        if (config('app.enable_asset_versioning') && app()->environment('production')) {
            $version = config('app.asset_version');
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . 'v=' . $version;
        }

        return $url;
    }

    /**
     * Get CDN or local asset URL (tenant-aware)
     */
    public static function cdn($path, $fallback = true)
    {
        // CDN URL varsa kullan
        $cdnUrl = config('app.cdn_url');
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($path, '/');
        }

        // Fallback to local asset
        return $fallback ? self::asset($path) : $path;
    }

    /**
     * Get minified asset path in production
     */
    public static function minified($path)
    {
        if (app()->environment('production')) {
            $pathInfo = pathinfo($path);
            $minPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.min.' . $pathInfo['extension'];

            // Check if minified version exists
            if (file_exists(public_path($minPath))) {
                return self::asset($minPath);
            }
        }

        return self::asset($path);
    }

    /**
     * Get tenant-safe upload URL
     */
    public static function upload($path, $tenant = null)
    {
        // Tenant context detection
        if (!$tenant && function_exists('tenant')) {
            $tenant = tenant();
        }

        if ($tenant) {
            // Tenant-specific uploads
            return self::asset("storage/tenant{$tenant->id}/" . ltrim($path, '/'));
        }

        // Central uploads
        return self::asset("storage/" . ltrim($path, '/'));
    }

    /**
     * Preload critical CSS
     */
    public static function preloadCss($path)
    {
        $url = self::minified($path);
        return "<link rel=\"preload\" href=\"{$url}\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">";
    }

    /**
     * Defer non-critical JavaScript
     */
    public static function deferJs($path)
    {
        $url = self::minified($path);
        return "<script src=\"{$url}\" defer></script>";
    }

    /**
     * Generate critical CSS inline for above-the-fold content
     */
    public static function criticalCss($path)
    {
        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            $css = file_get_contents($fullPath);
            // Minify CSS for inline use
            $css = preg_replace('/\s+/', ' ', $css);
            $css = str_replace(['; ', ' {', '{ ', ' }', '} '], [';', '{', '{', '}', '}'], $css);

            return "<style>{$css}</style>";
        }

        return "<!-- Critical CSS not found: {$path} -->";
    }

    /**
     * Get responsive image srcset
     */
    public static function responsiveImage($basePath, $alt = '', $sizes = ['300w', '600w', '900w', '1200w'])
    {
        $pathInfo = pathinfo($basePath);
        $srcset = [];

        foreach ($sizes as $size) {
            $width = (int) str_replace('w', '', $size);
            $responsivePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "-{$width}w." . $pathInfo['extension'];

            if (file_exists(public_path($responsivePath))) {
                $srcset[] = self::asset($responsivePath) . " {$size}";
            }
        }

        $srcsetAttr = !empty($srcset) ? 'srcset="' . implode(', ', $srcset) . '"' : '';
        $sizesAttr = !empty($srcset) ? 'sizes="(max-width: 600px) 300px, (max-width: 900px) 600px, (max-width: 1200px) 900px, 1200px"' : '';

        return "<img src=\"" . self::asset($basePath) . "\" alt=\"{$alt}\" {$srcsetAttr} {$sizesAttr} loading=\"lazy\">";
    }

    /**
     * Get asset integrity hash for security
     */
    public static function integrity($path)
    {
        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            $content = file_get_contents($fullPath);
            $hash = base64_encode(hash('sha384', $content, true));
            return "sha384-{$hash}";
        }

        return null;
    }

    /**
     * Generate manifest.json for PWA
     */
    public static function manifest()
    {
        $manifest = [
            'name' => config('app.name'),
            'short_name' => config('app.name'),
            'description' => 'Laravel CMS Application',
            'start_url' => '/',
            'display' => 'standalone',
            'theme_color' => '#3B82F6',
            'background_color' => '#ffffff',
            'icons' => [
                [
                    'src' => self::asset('icons/icon-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => self::asset('icons/icon-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ];

        return json_encode($manifest, JSON_PRETTY_PRINT);
    }
}