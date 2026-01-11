<?php

namespace Modules\TenantManagement\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\TenantManagement\App\Services\TenantRateLimitService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TenantRateLimitMiddleware
{
    protected TenantRateLimitService $rateLimitService;

    public function __construct(TenantRateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $tenantId = tenant('id');
            
            if (!$tenantId) {
                // Central domain - no rate limiting
                return $next($request);
            }

            $ip = $request->ip();
            $url = $request->getPathInfo();
            $method = $request->method();

            $result = $this->rateLimitService->checkRateLimit($tenantId, $ip, $url, $method);

            if (!$result['allowed']) {
                Log::warning('Rate limit exceeded', [
                    'tenant_id' => $tenantId,
                    'ip' => $ip,
                    'url' => $url,
                    'method' => $method,
                    'rule_id' => $result['rule_id'],
                    'period' => $result['period']
                ]);

                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many requests. Please try again later.',
                    'limit' => $result['limit'],
                    'remaining' => $result['remaining'],
                    'reset_time' => $result['reset_time']
                ], Response::HTTP_TOO_MANY_REQUESTS, [
                    'X-RateLimit-Limit' => $result['limit'],
                    'X-RateLimit-Remaining' => $result['remaining'],
                    'X-RateLimit-Reset' => $result['reset_time'],
                    'Retry-After' => max(1, $result['reset_time'] - time())
                ]);
            }

            // Rate limit passed, add headers
            $response = $next($request);
            
            if (method_exists($response, 'header')) {
                $response->header('X-RateLimit-Limit', $result['limit']);
                $response->header('X-RateLimit-Remaining', $result['remaining']);
                $response->header('X-RateLimit-Reset', $result['reset_time']);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Rate limit middleware error: ' . $e->getMessage());
            // On error, allow request to continue
            return $next($request);
        }
    }
}