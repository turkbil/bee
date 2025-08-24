<?php

namespace Modules\TenantManagement\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResourceTrackingMiddleware
{
    /**
     * Gerçek istekleri ve kaynak kullanımını takip et
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $queryCount = 0;

        // Database query sayacını başlat
        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
            
            // Track slow queries (>100ms)
            if ($query->time > 100) {
                \Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'time' => $query->time . 'ms',
                    'bindings' => $query->bindings
                ]);
            }
        });

        $response = $next($request);

        // İstek bittiğinde metrikleri kaydet
        $this->trackRequestMetrics($request, $response, $startTime, $startMemory, $queryCount);

        return $response;
    }

    /**
     * İstek metriklerini takip et
     */
    private function trackRequestMetrics(Request $request, $response, $startTime, $startMemory, $queryCount)
    {
        try {
            $tenant = $this->getCurrentTenant($request);
            if (!$tenant) {
                return;
            }

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2); // milliseconds
            $memoryUsage = memory_get_usage(true) - $startMemory;
            $currentHour = Carbon::now()->format('Y-m-d-H');

            // Cache'de saatlik sayaçları güncelle
            $this->incrementHourlyCounter("api_requests_{$tenant->id}_{$currentHour}", 1);
            $this->incrementHourlyCounter("db_queries_{$tenant->id}_{$currentHour}", $queryCount);
            $this->updateAverageMetric("response_time_{$tenant->id}_{$currentHour}", $responseTime);
            $this->incrementHourlyCounter("memory_usage_{$tenant->id}_{$currentHour}", $memoryUsage);

            // API endpoint'ini kategorize et
            if ($request->is('api/*')) {
                $this->incrementHourlyCounter("api_endpoints_{$tenant->id}_{$currentHour}", 1);
            }

            // Cache işlemlerini takip et
            if ($request->has('cache') || strpos($request->path(), 'cache') !== false) {
                $this->incrementHourlyCounter("cache_ops_{$tenant->id}_{$currentHour}", 1);
            }

            // AI işlemlerini takip et
            if ($request->is('*/ai/*') || $request->has('ai_operation')) {
                $this->incrementHourlyCounter("ai_ops_{$tenant->id}_{$currentHour}", 1);
            }

        } catch (\Exception $e) {
            \Log::error('ResourceTrackingMiddleware error: ' . $e->getMessage());
        }
    }

    /**
     * Saatlik sayacı artır
     */
    private function incrementHourlyCounter($key, $value)
    {
        $current = Cache::get($key, 0);
        Cache::put($key, $current + $value, 3600); // 1 saat cache
    }

    /**
     * Ortalama metriği güncelle
     */
    private function updateAverageMetric($key, $newValue)
    {
        $data = Cache::get($key, ['total' => 0, 'count' => 0]);
        $data['total'] += $newValue;
        $data['count']++;
        $data['average'] = round($data['total'] / $data['count'], 2);
        
        Cache::put($key, $data, 3600);
    }

    /**
     * Mevcut tenant'ı belirle
     */
    private function getCurrentTenant(Request $request)
    {
        // Tenant context'ten al
        if (function_exists('tenant') && tenant()) {
            return tenant();
        }

        // Domain'den tenant'ı bul
        $host = $request->getHost();
        if ($host) {
            $domain = \App\Models\Domain::where('domain', $host)->first();
            if ($domain && $domain->tenant) {
                return $domain->tenant;
            }
        }

        // Fallback: İlk aktif tenant
        return \App\Models\Tenant::where('is_active', true)->first();
    }
}