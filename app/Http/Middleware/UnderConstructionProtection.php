<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnderConstructionProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if current tenant is under construction (Tenant ID: 1001)
        $tenant = tenant();
        if (!$tenant || $tenant->id !== 1001) {
            return $next($request);
        }

        // Skip POST requests (handled by controller)
        if ($request->isMethod('post')) {
            return $next($request);
        }

        // Check session for access grant
        if (session('construction_access_granted')) {
            return $next($request);
        }

        // Show password protection page
        $error = session('construction_error');
        return response()->view('themes.muzibu.password-protection', [
            'error' => $error
        ], 401);
    }
}
