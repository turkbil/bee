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
        // 'api/*',

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
            return false;
        }
        
        // Session token ile request token'ı karşılaştır
        return hash_equals($request->session()->token(), $token);
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

        // Session domain ile tutarlı olmalı - SESSION_DOMAIN config'den al
        // .env'de SESSION_DOMAIN=.tuufi.com ayarı var
        // tenant() varsa tenant domain, yoksa config'den al
        if (tenant()) {
            $config['domain'] = $request->getHost();
        }
        // Central domain için .env SESSION_DOMAIN kullan (wildcard support)
        // config/session.php'de zaten SESSION_DOMAIN env var'ı okunuyor

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