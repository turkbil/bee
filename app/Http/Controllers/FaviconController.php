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
            // setting() helper ile tenant-aware favicon path'i al
            // SettingValue tablosunda string olarak tutuluyor: "storage/tenant2/settings/3/favicon_xxx.ico"
            $faviconPath = setting('site_favicon');

            if (!$faviconPath) {
                return null;
            }

            // Relative path'i absolute path'e çevir
            // storage/tenant2/settings/3/... -> /storage/tenant2/app/public/settings/3/...
            // storage_path('app/public') zaten tenant prefix'li olduğu için tenant{id} kısmını strip et
            $tenantId = tenant() ? tenant('id') : null;
            $strippedPath = $faviconPath;

            if ($tenantId) {
                // "storage/tenant{id}/" kısmını çıkar
                $strippedPath = preg_replace('#^storage/tenant' . $tenantId . '/#', '', $faviconPath);
            } else {
                // Central context: "storage/" kısmını çıkar
                $strippedPath = preg_replace('#^storage/#', '', $faviconPath);
            }

            $absolutePath = storage_path('app/public/' . $strippedPath);

            // Dosya var mı kontrol et
            if (!file_exists($absolutePath)) {
                \Log::warning('Favicon file not found: ' . $absolutePath . ' (original: ' . $faviconPath . ')');
                return null;
            }

            return $absolutePath;

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
