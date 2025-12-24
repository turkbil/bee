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
     * Generate signed HLS URL (playlist + segments) tied to login token
     *
     * @param int $songId
     * @param int $expiresInSeconds (default: 3600 saniye = 60 dakika)
     * @param string|null $loginToken
     * @return string
     */
    public function generateHlsUrl(int $songId, int $expiresInSeconds = 3600, ?string $loginToken = null): string
    {
        $expiration = Carbon::now()->addSeconds($expiresInSeconds);
        $expires = $expiration->timestamp;

        // Build HLS playlist URL (use current tenant domain)
        $tenant = tenancy()->tenant;
        $tenantDomain = $tenant
            ? ($tenant->domains()->where('is_primary', 1)->first()?->domain
               ?? $tenant->domains()->first()?->domain)
            : null;
        $domain = $tenantDomain ? 'https://' . $tenantDomain : request()->getSchemeAndHttpHost();

        $basePath = "/hls/muzibu/songs/{$songId}/playlist.m3u8";
        $baseUrl = $domain . $basePath;
        $signatureBase = "/hls/muzibu/songs/{$songId}";

        // Token zorunlu
        $token = $loginToken ?: '';

        $sig = hash_hmac('sha256', "{$signatureBase}|{$token}|{$expires}", config('app.key'));

        return $baseUrl . '?expires=' . $expires . '&token=' . urlencode($token) . '&sig=' . $sig;
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
