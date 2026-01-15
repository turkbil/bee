<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWwwDomain
{
    /**
     * Handle an incoming request.
     * Redirect root domain to www subdomain for proper session cookie handling
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Only redirect for muzibu.com (Tenant 1001)
        // Skip for localhost, IP addresses, and already www domains
        if (
            $host === 'muzibu.com'
            && !$request->is('opcache-reset*') // Skip for opcache reset
            && !$request->ajax()
            && !$request->wantsJson()
            && !$request->isMethod('POST') // 🔥 POST requests geçsin (form submissions)
        ) {
            return redirect()->to('https://www.muzibu.com' . $request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
