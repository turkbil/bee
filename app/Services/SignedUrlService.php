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

        // Build the URL manually with signature (use current tenant domain)
        $domain = request()->getSchemeAndHttpHost();
        $baseUrl = $domain . "/api/muzibu/songs/{$songId}/serve";

        // Add expiration timestamp
        $url = $baseUrl . '?expires=' . $expiration->timestamp;

        // Generate signature
        $signature = hash_hmac('sha256', $url, config('app.key'));

        return $url . '&signature=' . $signature;
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

        // Build the URL manually with signature (use current tenant domain)
        $domain = request()->getSchemeAndHttpHost();
        $baseUrl = $domain . "/api/muzibu/songs/{$songId}/serve";

        // Add expiration timestamp
        $url = $baseUrl . '?expires=' . $expiration->timestamp;

        // Generate signature
        $signature = hash_hmac('sha256', $url, config('app.key'));

        return $url . '&signature=' . $signature;
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
