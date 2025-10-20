<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\SettingManagement\App\Models\Setting;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FaviconController extends Controller
{
    /**
     * Serve favicon.ico dynamically based on tenant/central context
     *
     * @return BinaryFileResponse
     */
    public function show()
    {
        // Cache key tenant bazlı (central veya tenant ID)
        $tenantId = tenant() ? tenant('id') : 'central';
        $cacheKey = 'favicon_path_' . $tenantId;

        // Cache'den favicon path'i al (24 saat - favicon nadiren değişir)
        $faviconPath = Cache::remember($cacheKey, 86400, function() {
            return $this->getFaviconPath();
        });

        // Dosya yoksa 404 döndür
        if (!$faviconPath || !file_exists($faviconPath)) {
            return $this->getDefaultFavicon();
        }

        // Favicon'u response olarak döndür
        return response()->file($faviconPath, [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=86400', // 24 saat browser cache
        ]);
    }

    /**
     * Settings'den favicon path'ini al
     *
     * @return string|null
     */
    private function getFaviconPath(): ?string
    {
        try {
            // Settings'den site_favicon key'ini çek
            $setting = Setting::where('key', 'site_favicon')
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                return null;
            }

            // Spatie Media Library - Direkt path al (URL parse gereksiz)
            $media = $setting->getFirstMedia('featured_image');

            if (!$media) {
                return null;
            }

            // Absolute path döndür
            return $media->getPath();

        } catch (\Exception $e) {
            \Log::error('Favicon path error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Default favicon döndür (yoksa 404)
     *
     * @return BinaryFileResponse
     */
    private function getDefaultFavicon()
    {
        $defaultPath = public_path('favicon.ico');

        if (file_exists($defaultPath)) {
            return response()->file($defaultPath, [
                'Content-Type' => 'image/x-icon',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // Default da yoksa 404
        abort(404, 'Favicon not found');
    }
}
