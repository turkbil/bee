<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TenantStorageController extends Controller
{
    /**
     * Serve tenant storage files bypassing symlink restrictions
     *
     * @param Request $request
     * @param string $path
     * @return BinaryFileResponse
     */
    public function serve(Request $request, string $path)
    {
        // Get tenant ID from current tenant context
        $tenantId = tenant('id');

        // Build full path to file in tenant storage
        $storagePath = storage_path("tenant{$tenantId}/app/public/{$path}");

        // Security: Prevent directory traversal
        $realPath = realpath($storagePath);
        $basePath = realpath(storage_path("tenant{$tenantId}/app/public"));

        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404, 'File not found');
        }

        // Check if file exists
        if (!file_exists($realPath) || !is_file($realPath)) {
            abort(404, 'File not found');
        }

        // Get file info
        $mimeType = mime_content_type($realPath) ?: 'application/octet-stream';
        $fileSize = filesize($realPath);

        // Create binary file response with caching headers
        return response()->file($realPath, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=2592000, immutable', // 30 days
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000),
            'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', filemtime($realPath)),
            'ETag' => md5_file($realPath),
        ]);
    }
}
