<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SignedUrlService
{
    /**
     * Generate signed URL for song stream
     *
     * @param int $songId
     * @param int $expiresInMinutes (default: 30 dakika)
     * @param bool $forceMP3 Force MP3 output even if HLS available (for fallback)
     * @return string
     */
    public function generateStreamUrl(int $songId, int $expiresInMinutes = 30, bool $forceMP3 = false): string
    {
        $expiration = Carbon::now()->addMinutes($expiresInMinutes);
        $expires = $expiration->timestamp;

        // Build the URL manually with signature (use current tenant domain)
        // 🔧 Use tenant's primary domain instead of request domain
        $tenant = tenancy()->tenant;
        $tenantDomain = $tenant ? $tenant->domains()->where('is_primary', 1)->first()?->domain : null;
        $domain = $tenantDomain ? 'https://' . $tenantDomain : request()->getSchemeAndHttpHost();
        $baseUrl = $domain . "/api/muzibu/songs/{$songId}/serve";

        // 🔐 Generate signature (must match ValidateSignedUrl middleware)
        // Signature: hash(baseUrl + songId + expires + app_key)
        $signature = hash_hmac('sha256', $baseUrl . $songId . $expires, config('app.key'));

        // 🎵 Build URL with force_mp3 flag if needed (for HLS fallback)
        $url = $baseUrl . '?expires=' . $expires . '&signature=' . $signature;
        if ($forceMP3) {
            $url .= '&force_mp3=1';
        }

        return $url;
    }

    /**
     * Generate signed URL for HLS playlist
     * HLS için daha uzun süre (60 dakika) - chunked streaming
     * 
     * @param int $songId
     * @param int $expiresInMinutes (default: 60 dakika)
     * @return string
     */
    public function generateHlsUrl(int $songId, int $expiresInMinutes = 60): string
    {
        $expiration = Carbon::now()->addMinutes($expiresInMinutes);
        $expires = $expiration->timestamp;

        // Build HLS playlist URL (use current tenant domain)
        // 🔧 Use tenant's primary domain (or first domain) instead of request domain
        $tenant = tenancy()->tenant;
        $tenantDomain = $tenant
            ? ($tenant->domains()->where('is_primary', 1)->first()?->domain
               ?? $tenant->domains()->first()?->domain)
            : null;
        $domain = $tenantDomain ? 'https://' . $tenantDomain : request()->getSchemeAndHttpHost();

        // 🎵 HLS through /hls/ endpoint (NOT /api/ - avoids Laravel CORS middleware)
        // Laravel CORS adds `Access-Control-Allow-Credentials: true` to /api/* paths
        // which conflicts with `Access-Control-Allow-Origin: *` - browsers reject this
        // By using /hls/ path, controller handles CORS directly without middleware conflict
        $hlsUrl = $domain . "/hls/muzibu/songs/{$songId}/playlist.m3u8";

        return $hlsUrl; // HLS doesn't need signed URL (AES-128 encrypted)
    }

    /**
     * Validate signed URL manually (fallback)
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function validateSignature($request): bool
    {
        return $request->hasValidSignature();
    }

    /**
     * Check if URL is expired
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function isExpired($request): bool
    {
        if (!$request->hasValidSignature(false)) {
            return true;
        }

        $expires = $request->query('expires');
        if (!$expires) {
            return true;
        }

        return Carbon::now()->timestamp > $expires;
    }

    /**
     * Get remaining time for signed URL
     * 
     * @param \Illuminate\Http\Request $request
     * @return int seconds
     */
    public function getRemainingSeconds($request): int
    {
        $expires = $request->query('expires');
        if (!$expires) {
            return 0;
        }

        return max(0, $expires - Carbon::now()->timestamp);
    }
}
