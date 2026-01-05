<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DynamicCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');

        if ($origin && $this->isOriginAllowed($origin)) {
            $response = $next($request);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN');
            $response->headers->set('Access-Control-Max-Age', '86400');

            return $response;
        }

        return $next($request);
    }

    /**
     * Check if origin is allowed
     *
     * @param string $origin
     * @return bool
     */
    protected function isOriginAllowed(string $origin): bool
    {
        $allowedOrigins = $this->getAllowedOrigins();

        return in_array($origin, $allowedOrigins);
    }

    /**
     * Get all allowed origins from database
     *
     * @return array
     */
    protected function getAllowedOrigins(): array
    {
        return Cache::remember('dynamic_cors_origins', 3600, function () {
            try {
                $domains = DB::connection('central')
                    ->table('domains')
                    ->pluck('domain')
                    ->toArray();

                $origins = [];
                foreach ($domains as $domain) {
                    $origins[] = 'https://' . $domain;
                    $origins[] = 'http://' . $domain;
                }

                // Development origins
                $origins[] = 'http://localhost';
                $origins[] = 'http://localhost:3000';
                $origins[] = 'http://127.0.0.1';
                $origins[] = 'http://127.0.0.1:8000';

                return array_unique($origins);

            } catch (\Exception $e) {
                \Log::error('❌ DynamicCors: Failed to load origins', [
                    'error' => $e->getMessage()
                ]);

                // Fallback
                return [
                    'https://tuufi.com',
                    'https://ixtif.com',
                    'https://muzibu.com.tr',
                    'http://localhost',
                ];
            }
        });
    }
}
