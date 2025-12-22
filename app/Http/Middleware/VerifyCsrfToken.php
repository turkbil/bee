<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // API rotalarını CSRF doğrulamasından hariç tutmak için ekleyebilirsiniz
        'api/*', // API routes - Sanctum kendi CSRF kontrolünü yapar

        // Payment Gateway Callbacks (PayTR, Stripe vs.)
        'payment/callback/*',
    ];
    
    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Multi-tenant ortamında token kontrolü
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            \Log::warning('🔐 CSRF: No token in request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'has_session' => $request->hasSession(),
            ]);
            return false;
        }

        $sessionToken = $request->session()->token();
        $match = hash_equals($sessionToken, $token);

        if (!$match) {
            \Log::warning('🔐 CSRF: Token mismatch', [
                'url' => $request->fullUrl(),
                'session_token' => substr($sessionToken, 0, 20) . '...',
                'request_token' => substr($token, 0, 20) . '...',
                'session_id' => $request->session()->getId(),
            ]);
        }

        return $match;
    }
    
    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        // Session domain ile tutarlı olmalı
        // Tenant context'te domain'i host'tan al (nokta prefix ile subdomain desteği)
        if (tenant()) {
            $host = $request->getHost();
            // Subdomain desteği için nokta prefix ekle (www.domain.com için .domain.com)
            $config['domain'] = '.' . $host;
        }
        // Central domain için .env SESSION_DOMAIN kullan

        $response->headers->setCookie(
            new \Symfony\Component\HttpFoundation\Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                $this->availableAt(60 * $config['lifetime']),
                $config['path'],
                $config['domain'],
                $config['secure'],
                false,
                false,
                $config['same_site'] ?? 'lax'
            )
        );

        return $response;
    }
}