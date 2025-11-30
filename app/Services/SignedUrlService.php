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
     * @return string
     */
    public function generateStreamUrl(int $songId, int $expiresInMinutes = 30): string
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

        return $baseUrl . '?expires=' . $expires . '&signature=' . $signature;
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
        // 🔧 Use tenant's primary domain instead of request domain
        $tenant = tenancy()->tenant;
        $tenantDomain = $tenant ? $tenant->domains()->where('is_primary', 1)->first()?->domain : null;
        $domain = $tenantDomain ? 'https://' . $tenantDomain : request()->getSchemeAndHttpHost();

        // 🎵 HLS uses storage URL, not serve endpoint
        // HLS playlist: /storage/tenant{id}/muzibu/hls/{songId}/playlist.m3u8
        $tenantId = $tenant?->id ?? 'unknown';
        $hlsUrl = $domain . "/storage/tenant{$tenantId}/muzibu/hls/{$songId}/playlist.m3u8";

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
