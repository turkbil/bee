<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RedisResilienceService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedisHealthCheckMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Admin sayfalarında Redis sağlığını kontrol et
        if ($request->is('admin/*') || $request->is('horizon/*')) {
            $this->performHealthCheck();
        }

        return $next($request);
    }

    /**
     * Perform Redis health check
     */
    private function performHealthCheck(): void
    {
        try {
            // Redis connection durumunu kontrol et
            $healthStatus = RedisResilienceService::healthCheck();
            
            // Eğer Redis unhealthy ise otomatik düzelt
            if ($healthStatus['redis_status'] !== 'healthy') {
                Log::warning('Redis health check failed, attempting auto-recovery', $healthStatus);
                
                // Otomatik recovery dene
                RedisResilienceService::ensureConnection();
                
                // Recovery sonrası tekrar kontrol et
                $retryStatus = RedisResilienceService::healthCheck();
                if ($retryStatus['redis_status'] === 'healthy') {
                    Log::info('Redis auto-recovery successful', $retryStatus);
                } else {
                    Log::error('Redis auto-recovery failed', $retryStatus);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Redis health check middleware error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}