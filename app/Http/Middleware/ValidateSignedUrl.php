<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignedUrl
{
    /**
     * 🔐 Validate signed URL with expiration
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get expires and signature from query params
        $expires = $request->query('expires');
        $signature = $request->query('signature');

        // Both required
        if (!$expires || !$signature) {
            abort(403, 'Invalid URL');
        }

        // Check expiration (Unix timestamp)
        if (time() > $expires) {
            abort(403, 'URL expired');
        }

        // Generate expected signature
        $url = $request->url(); // Base URL without query params
        $songId = $request->route('id');

        // 🔍 Debug logging
        \Log::info('ValidateSignedUrl Debug', [
            'url' => $url,
            'songId' => $songId,
            'expires' => $expires,
            'received_signature' => $signature,
            'hash_input' => $url . $songId . $expires
        ]);

        // Signature: hash(url + songId + expires + app_key)
        $expectedSignature = hash_hmac('sha256', $url . $songId . $expires, config('app.key'));

        \Log::info('Signature comparison', [
            'expected' => $expectedSignature,
            'received' => $signature,
            'match' => hash_equals($expectedSignature, $signature)
        ]);

        // Compare signatures (timing-safe)
        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid signature');
        }

        // Validation passed
        return $next($request);
    }
}
