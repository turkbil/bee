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
        
        // Tenant domain'i için cookie ayarlarını güncelle
        if (tenant()) {
            $config['domain'] = $request->getHost();
        }
        
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
                $config['same_site'] ?? null
            )
        );
        
        return $response;
    }
}