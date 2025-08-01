<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Performance Monitoring Service
 * 
 * URL oluşturma, cache ve routing performansını izler
 */
class PerformanceMonitoringService
{
    private const METRICS_CACHE_KEY = 'performance_metrics';
    private const METRICS_TTL = 300; // 5 dakika
    
    private ?float $startTime = null;
    private array $timings = [];

    /**
     * İşlem zamanlamasını başlat
     */
    public function startTiming(string $operation): void
    {
        $this->startTime = microtime(true);
        $this->timings[$operation] = [
            'start' => $this->startTime,
            'end' => null,
            'duration' => null
        ];
    }

    /**
     * İşlem zamanlamasını bitir
     */
    public function endTiming(string $operation): float
    {
        $endTime = microtime(true);
        
        if (isset($this->timings[$operation])) {
            $this->timings[$operation]['end'] = $endTime;
            $this->timings[$operation]['duration'] = $endTime - $this->timings[$operation]['start'];
            
            // Metriği kaydet
            $this->recordMetric($operation, $this->timings[$operation]['duration']);
            
            return $this->timings[$operation]['duration'];
        }
        
        return 0.0;
    }

    /**
     * URL oluşturma performansını izle
     */
    public function trackUrlGeneration(string $type, float $duration): void
    {
        $key = "url_generation_{$type}";
        $this->recordMetric($key, $duration);
        
        // Yavaş URL oluşturmaları logla
        if ($duration > 0.1) { // 100ms üzeri
            Log::warning('Slow URL generation detected', [
                'type' => $type,
                'duration_ms' => round($duration * 1000, 2),
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);
        }
    }

    /**
     * Cache performansını izle
     */
    public function trackCacheOperation(string $operation, bool $hit, float $duration): void
    {
        $key = "cache_{$operation}";
        $this->recordMetric($key, $duration);
        
        if ($hit) {
            Cache::increment('cache_hits_total');
        } else {
            Cache::increment('cache_misses_total');
        }
    }

    /**
     * Route çözümleme performansını izle
     */
    public function trackRouteResolution(string $path, float $duration): void
    {
        $this->recordMetric('route_resolution', $duration);
        
        // Yavaş route çözümlemeleri logla
        if ($duration > 0.05) { // 50ms üzeri
            Log::warning('Slow route resolution detected', [
                'path' => $path,
                'duration_ms' => round($duration * 1000, 2)
            ]);
        }
    }

    /**
     * Genel performans metriklerini getir
     */
    public function getMetrics(): array
    {
        return Cache::remember(self::METRICS_CACHE_KEY, now()->addSeconds(self::METRICS_TTL), function () {
            $metrics = [
                'url_generation' => $this->getUrlGenerationMetrics(),
                'cache' => $this->getCacheMetrics(),
                'route' => $this->getRouteMetrics(),
                'database' => $this->getDatabaseMetrics(),
                'memory' => $this->getMemoryMetrics()
            ];
            
            return $metrics;
        });
    }

    /**
     * Performans raporu oluştur
     */
    public function generateReport(): array
    {
        $metrics = $this->getMetrics();
        
        return [
            'summary' => [
                'total_url_generations' => $metrics['url_generation']['total'] ?? 0,
                'average_url_generation_time' => $metrics['url_generation']['average'] ?? 0,
                'cache_hit_rate' => $metrics['cache']['hit_rate'] ?? 0,
                'slow_operations' => $this->getSlowOperations(),
                'memory_usage' => $metrics['memory']['current'] ?? 0,
                'peak_memory' => $metrics['memory']['peak'] ?? 0
            ],
            'recommendations' => $this->getRecommendations($metrics),
            'detailed_metrics' => $metrics
        ];
    }

    /**
     * Performans uyarılarını kontrol et
     */
    public function checkPerformanceAlerts(): array
    {
        $alerts = [];
        $metrics = $this->getMetrics();
        
        // Cache hit rate düşük
        if (($metrics['cache']['hit_rate'] ?? 100) < 70) {
            $alerts[] = [
                'level' => 'warning',
                'message' => 'Cache hit rate is below 70%',
                'value' => $metrics['cache']['hit_rate'] . '%'
            ];
        }
        
        // Ortalama URL oluşturma süresi yüksek
        if (($metrics['url_generation']['average'] ?? 0) > 0.05) {
            $alerts[] = [
                'level' => 'warning',
                'message' => 'Average URL generation time is high',
                'value' => round($metrics['url_generation']['average'] * 1000, 2) . 'ms'
            ];
        }
        
        // Memory kullanımı yüksek
        $memoryUsagePercent = ($metrics['memory']['current'] ?? 0) / ($metrics['memory']['limit'] ?? 1) * 100;
        if ($memoryUsagePercent > 80) {
            $alerts[] = [
                'level' => 'critical',
                'message' => 'Memory usage is above 80%',
                'value' => round($memoryUsagePercent, 2) . '%'
            ];
        }
        
        return $alerts;
    }

    // Private helper methods

    private function recordMetric(string $key, float $value): void
    {
        // Toplam sayaç
        Cache::increment("metric_{$key}_count");
        
        // Toplam değer
        $total = Cache::get("metric_{$key}_total", 0) + $value;
        Cache::put("metric_{$key}_total", $total, now()->addHours(24));
        
        // Min/Max
        $min = Cache::get("metric_{$key}_min", PHP_FLOAT_MAX);
        $max = Cache::get("metric_{$key}_max", 0);
        
        if ($value < $min) {
            Cache::put("metric_{$key}_min", $value, now()->addHours(24));
        }
        
        if ($value > $max) {
            Cache::put("metric_{$key}_max", $value, now()->addHours(24));
        }
    }

    private function getUrlGenerationMetrics(): array
    {
        $types = ['model', 'module', 'path'];
        $metrics = [];
        
        foreach ($types as $type) {
            $key = "url_generation_{$type}";
            $count = Cache::get("metric_{$key}_count", 0);
            $total = Cache::get("metric_{$key}_total", 0);
            
            $metrics[$type] = [
                'count' => $count,
                'total' => $total,
                'average' => $count > 0 ? $total / $count : 0,
                'min' => Cache::get("metric_{$key}_min", 0),
                'max' => Cache::get("metric_{$key}_max", 0)
            ];
        }
        
        $totalCount = array_sum(array_column($metrics, 'count'));
        $totalTime = array_sum(array_column($metrics, 'total'));
        
        $metrics['total'] = $totalCount;
        $metrics['average'] = $totalCount > 0 ? $totalTime / $totalCount : 0;
        
        return $metrics;
    }

    private function getCacheMetrics(): array
    {
        $hits = Cache::get('cache_hits_total', 0);
        $misses = Cache::get('cache_misses_total', 0);
        $total = $hits + $misses;
        
        return [
            'hits' => $hits,
            'misses' => $misses,
            'total' => $total,
            'hit_rate' => $total > 0 ? ($hits / $total) * 100 : 0
        ];
    }

    private function getRouteMetrics(): array
    {
        $count = Cache::get("metric_route_resolution_count", 0);
        $total = Cache::get("metric_route_resolution_total", 0);
        
        return [
            'count' => $count,
            'total' => $total,
            'average' => $count > 0 ? $total / $count : 0,
            'min' => Cache::get("metric_route_resolution_min", 0),
            'max' => Cache::get("metric_route_resolution_max", 0)
        ];
    }

    private function getDatabaseMetrics(): array
    {
        try {
            $queryLog = DB::getQueryLog();
            $queryCount = count($queryLog);
            $totalTime = array_sum(array_column($queryLog, 'time'));
            
            return [
                'query_count' => $queryCount,
                'total_time' => $totalTime,
                'average_time' => $queryCount > 0 ? $totalTime / $queryCount : 0
            ];
        } catch (\Exception $e) {
            return [
                'query_count' => 0,
                'total_time' => 0,
                'average_time' => 0
            ];
        }
    }

    private function getMemoryMetrics(): array
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => $this->getMemoryLimit()
        ];
    }

    private function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        
        if (preg_match('/^(\d+)(.)$/', $limit, $matches)) {
            $value = (int) $matches[1];
            switch (strtoupper($matches[2])) {
                case 'G':
                    return $value * 1024 * 1024 * 1024;
                case 'M':
                    return $value * 1024 * 1024;
                case 'K':
                    return $value * 1024;
            }
        }
        
        return (int) $limit;
    }

    private function getSlowOperations(): array
    {
        $slowOps = [];
        
        // URL generation
        $urlMetrics = $this->getUrlGenerationMetrics();
        foreach (['model', 'module', 'path'] as $type) {
            if (isset($urlMetrics[$type]['max']) && $urlMetrics[$type]['max'] > 0.1) {
                $slowOps[] = [
                    'operation' => "URL Generation ({$type})",
                    'max_time' => round($urlMetrics[$type]['max'] * 1000, 2) . 'ms'
                ];
            }
        }
        
        // Route resolution
        $routeMetrics = $this->getRouteMetrics();
        if ($routeMetrics['max'] > 0.05) {
            $slowOps[] = [
                'operation' => 'Route Resolution',
                'max_time' => round($routeMetrics['max'] * 1000, 2) . 'ms'
            ];
        }
        
        return $slowOps;
    }

    private function getRecommendations(array $metrics): array
    {
        $recommendations = [];
        
        // Cache recommendations
        if (($metrics['cache']['hit_rate'] ?? 100) < 70) {
            $recommendations[] = 'Consider increasing cache TTL or implementing more aggressive caching strategies';
        }
        
        // URL generation recommendations
        if (($metrics['url_generation']['average'] ?? 0) > 0.05) {
            $recommendations[] = 'URL generation is slow. Consider optimizing database queries or implementing batch operations';
        }
        
        // Memory recommendations
        $memoryUsagePercent = ($metrics['memory']['current'] ?? 0) / ($metrics['memory']['limit'] ?? 1) * 100;
        if ($memoryUsagePercent > 70) {
            $recommendations[] = 'Memory usage is high. Consider optimizing memory-intensive operations or increasing memory limit';
        }
        
        return $recommendations;
    }
}