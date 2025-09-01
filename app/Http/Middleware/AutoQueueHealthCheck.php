<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\QueueHealthService;
use Illuminate\Support\Facades\Cache;

class AutoQueueHealthCheck
{
    /**
     * 🚀 OTOMATIK QUEUE HEALTH CHECK MIDDLEWARE
     * Her admin sayfası yüklendiğinde queue health kontrol eder
     */
    public function handle(Request $request, Closure $next)
    {
        // Sadece admin sayfalarında çalışsın
        if (!$request->is('admin/*')) {
            return $next($request);
        }

        // Rate limiting: 5 dakikada bir kontrol et
        $cacheKey = 'queue_health_check_last_run';
        $lastCheck = Cache::get($cacheKey, 0);
        
        if (now()->timestamp - $lastCheck > 300) { // 5 dakika = 300 saniye
            try {
                // Arka planda health check çalıştır (blocking olmadan)
                dispatch(function () {
                    QueueHealthService::checkAndFixQueueHealth();
                })->onQueue('default');
                
                Cache::put($cacheKey, now()->timestamp, 600); // 10 dakika cache
                
            } catch (\Exception $e) {
                // Hata olursa log'a yaz ama sayfayı etkileme
                \Log::error('AutoQueueHealthCheck middleware failed', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $next($request);
    }
}