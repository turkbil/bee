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
     * CSRF token süresini uzatmak için bu metodu ekleyin
     */
    protected function tokensMatch($request)
    {
        // Multi-tenant ortamında token sorunlarını önlemek için
        // önce session'ı yeniliyoruz
        if ($request->session()->has('tenant_id')) {
            $request->session()->regenerateToken();
        }
        
        return parent::tokensMatch($request);
    }
}