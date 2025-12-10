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
     *
     * ⚠️ CRITICAL: Bu metod route'dan ID alıyor, ama tenant context'i set edilmeden önce çağrılabilir
     * Bu yüzden manuel path kullanıyoruz (tenancy suffix eklemeden)
     */
    public function tenantMedia(Request $request, $id, $path)
    {
        // Decode URL encoding
        $path = urldecode($path);

        // Construct tenant storage path - Direct path (tenancy middleware henüz çalışmamış olabilir)
        $fullPath = base_path("storage/tenant{$id}/app/public/{$path}");

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

        \Log::info('📁 [STORAGE] publicStorage called', ['path' => $path, 'url' => $request->fullUrl()]);

        // Tenant context kontrolü - eğer tenant context'i varsa tenant klasörüne yönlendir
        if (app()->bound(\Stancl\Tenancy\Tenancy::class)) {
            $tenancy = app(\Stancl\Tenancy\Tenancy::class);

            \Log::info('📁 [STORAGE] Tenancy bound:', ['initialized' => $tenancy->initialized]);

            if ($tenancy->initialized) {
                $tenantId = tenant('id');

                \Log::info('📁 [STORAGE] Tenant initialized:', ['tenant_id' => $tenantId]);

                // Eğer path 'tenant{id}/' ile başlıyorsa onu kullan
                if (preg_match('/^tenant(\d+)\/(.+)$/', $path, $matches)) {
                    $targetTenantId = $matches[1];
                    $relativePath = $matches[2];
                } else {
                    $targetTenantId = $tenantId;
                    $relativePath = ltrim($path, '/');
                }

                // ⚠️ CRITICAL FIX: Tenant context ZATEN initialize edilmiş (middleware'den geçti)
                // storage_path() otomatik tenant prefix ekliyor, manuel eklememeliyiz!
                $fullPath = storage_path("app/public/{$relativePath}");

                \Log::info('📁 [STORAGE] Resolved path:', [
                    'relativePath' => $relativePath,
                    'fullPath' => $fullPath,
                    'exists' => file_exists($fullPath)
                ]);

                return $this->serveFile($fullPath);
            }
        }

        // Central context - normal storage path
        if (preg_match('/^tenant(\d+)\/(.+)$/', $path, $matches)) {
            $targetTenantId = $matches[1];
            $relativePath = $matches[2];
            // Direct path (central context, tenancy initialize edilmemiş)
            $fullPath = base_path("storage/tenant{$targetTenantId}/app/public/{$relativePath}");
        } else {
            $fullPath = storage_path("app/public/{$path}");
        }

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
