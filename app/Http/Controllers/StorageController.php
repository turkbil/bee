<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve tenant media files through Laravel (bypass Nginx restrictions)
     * Route: /storage/tenant{id}/{path}
     */
    public function tenantMedia(Request $request, $id, $path)
    {
        // Decode URL encoding
        $path = urldecode($path);

        // Construct tenant storage path
        $fullPath = storage_path("tenant{$id}/app/public/{$path}");

        return $this->serveFile($fullPath);
    }

    /**
     * Serve public storage files through Laravel (bypass Nginx restrictions)
     * Route: /storage/{path}
     *
     * TENANT-AWARE: Otomatik olarak aktif tenant'ın klasörüne yönlendirir
     */
    public function publicStorage(Request $request, $path)
    {
        // Decode URL encoding
        $path = urldecode($path);

        // Tenant context kontrolü - eğer tenant context'i varsa tenant klasörüne yönlendir
        if (app()->bound(\Stancl\Tenancy\Tenancy::class)) {
            $tenancy = app(\Stancl\Tenancy\Tenancy::class);

            if ($tenancy->initialized) {
                // Tenant ID'yi al
                $tenantId = tenant('id');

                // TenantPathGenerator ile uyumlu path:
                // storage/tenant2/app/public/tenant2/{media_id}/file.png
                // URL'den gelen: 1/file.png
                // Eklememiz gereken prefix: tenant{id}/
                $fullPath = storage_path("app/public/tenant{$tenantId}/{$path}");

                return $this->serveFile($fullPath);
            }
        }

        // Central context - normal storage path
        $fullPath = storage_path("app/public/{$path}");

        return $this->serveFile($fullPath);
    }

    /**
     * Serve file with security checks and caching headers
     */
    protected function serveFile(string $fullPath)
    {
        // Security check - prevent directory traversal
        $realPath = realpath($fullPath);
        $basePath = realpath(storage_path());

        if (!$realPath || !str_starts_with($realPath, $basePath)) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!file_exists($realPath) || !is_file($realPath)) {
            abort(404, 'File not found');
        }

        // Return file with proper headers
        return response()->file($realPath, [
            'Cache-Control' => 'public, max-age=31536000', // 1 year cache
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
        ]);
    }
}
