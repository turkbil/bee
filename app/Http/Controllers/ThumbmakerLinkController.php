<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThumbmakerLinkController extends Controller
{
    public function __invoke(Request $request, string $encoded, ?int $width = null, ?int $height = null, ?int $quality = null)
    {
        $source = $this->decodePath($encoded);
        if (! $source) {
            abort(404);
        }

        $tenantId = $this->resolveTenantId();

        $source = ltrim($source, '/');
        if (! str_starts_with($source, 'storage/')) {
            $source = "storage/tenant{$tenantId}/{$source}";
        }

        $overrides = ['format' => 'webp'];
        if ($width) {
            $overrides['width'] = (int) $width;
        }
        if ($height) {
            $overrides['height'] = (int) $height;
        }
        if ($quality !== null) {
            $overrides['quality'] = max(1, min(100, (int) $quality));
        }

        $url = thumbmaker($source, $overrides);
        if (! $url) {
            abort(404);
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $path = ltrim($path, '/');
        if (! str_starts_with($path, 'storage/')) {
            abort(404);
        }

        $relative = substr($path, strlen('storage/'));
        $absolute = $this->resolveStoragePath($relative, $tenantId);

        if (! $absolute || ! file_exists($absolute)) {
            abort(404);
        }

        return response()->file($absolute, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
        ]);
    }

    protected function resolveTenantId(): int
    {
        if (function_exists('tenant_id') && tenant_id()) {
            return (int) tenant_id();
        }

        if (function_exists('resolve_tenant_id')) {
            $resolved = resolve_tenant_id(false);
            if ($resolved) {
                return (int) $resolved;
            }
        }

        return 1;
    }

    protected function resolveStoragePath(string $relative, int $tenantId): ?string
    {
        $relative = ltrim($relative, '/');

        if (str_starts_with($relative, 'tenant')) {
            [$prefix, $rest] = array_pad(explode('/', $relative, 2), 2, '');
            if (preg_match('/^tenant(\d+)$/', $prefix, $matches)) {
                $tenant = (int) $matches[1];
                return storage_path("tenant{$tenant}/app/public/" . ltrim($rest, '/'));
            }
        }

        return storage_path("tenant{$tenantId}/app/public/" . $relative);
    }

    protected function decodePath(string $encoded): ?string
    {
        $base64 = str_replace(['-', '_'], ['+', '/'], $encoded);
        $padding = strlen($base64) % 4;
        if ($padding) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($base64, true) ?: null;
    }
}
