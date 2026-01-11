<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WidgetMonitoringService
{
    const MONITORING_PREFIX = 'widget_monitor_';
    const METRICS_RETENTION = 86400; // 24 hours
    const ALERT_THRESHOLDS = [
        'error_rate' => 0.05, // 5% error rate
        'response_time' => 2000, // 2 seconds
        'memory_usage' => 50 * 1024 * 1024, // 50MB
        'query_count' => 20 // 20 queries per widget
    ];

    /**
     * Widget performans metriklerini kaydet
     */
    public function recordWidgetMetrics(string $widgetId, array $metrics): void
    {
        try {
            $timestamp = now();
            $metricsData = [
                'widget_id' => $widgetId,
                'timestamp' => $timestamp->toISOString(),
                'render_time' => $metrics['render_time'] ?? 0,
                'memory_usage' => $metrics['memory_usage'] ?? 0,
                'query_count' => $metrics['query_count'] ?? 0,
                'cache_hit' => $metrics['cache_hit'] ?? false,
                'error_occurred' => $metrics['error_occurred'] ?? false,
                'error_message' => $metrics['error_message'] ?? null,
                'user_id' => auth()->id(),
                'tenant_id' => $metrics['tenant_id'] ?? null,
                'request_url' => request()->fullUrl(),
                'ip_address' => request()->ip()
            ];

            // Cache'e kısa süreli metrikleri kaydet
            $cacheKey = self::MONITORING_PREFIX . 'metrics_' . $widgetId . '_' . $timestamp->timestamp;
            Cache::put($cacheKey, $metricsData, self::METRICS_RETENTION);

            // Real-time monitoring için ayrı cache
            $this->updateRealTimeMetrics($widgetId, $metricsData);

            // Alert kontrolü
            $this->checkAlerts($widgetId, $metricsData);

            Log::debug('Widget metrics recorded', [
                'widget_id' => $widgetId,
                'metrics' => $metricsData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to record widget metrics', [
                'widget_id' => $widgetId,
                'metrics' => $metrics,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Real-time metrikleri güncelle
     */
    private function updateRealTimeMetrics(string $widgetId, array $metrics): void
    {
        $realTimeKey = self::MONITORING_PREFIX . 'realtime_' . $widgetId;
        $currentData = Cache::get($realTimeKey, [
            'total_requests' => 0,
            'total_errors' => 0,
            'total_render_time' => 0,
            'total_memory' => 0,
            'total_queries' => 0,
            'last_updated' => now()->toISOString()
        ]);

        $currentData['total_requests']++;
        $currentData['total_render_time'] += $metrics['render_time'];
        $currentData['total_memory'] += $metrics['memory_usage'];
        $currentData['total_queries'] += $metrics['query_count'];
        $currentData['last_updated'] = now()->toISOString();

        if ($metrics['error_occurred']) {
            $currentData['total_errors']++;
        }

        // Ortalama değerleri hesapla
        $currentData['avg_render_time'] = $currentData['total_render_time'] / $currentData['total_requests'];
        $currentData['avg_memory'] = $currentData['total_memory'] / $currentData['total_requests'];
        $currentData['avg_queries'] = $currentData['total_queries'] / $currentData['total_requests'];
        $currentData['error_rate'] = $currentData['total_errors'] / $currentData['total_requests'];

        Cache::put($realTimeKey, $currentData, 3600); // 1 hour
    }

    /**
     * Widget metriklerini al
     */
    public function getWidgetMetrics(string $widgetId, int $hours = 1): array
    {
        try {
            $realTimeKey = self::MONITORING_PREFIX . 'realtime_' . $widgetId;
            $realTimeData = Cache::get($realTimeKey, []);

            $since = now()->subHours($hours);
            $historicalData = $this->getHistoricalMetrics($widgetId, $since);

            return [
                'widget_id' => $widgetId,
                'period' => $hours . ' hours',
                'real_time' => $realTimeData,
                'historical' => $historicalData,
                'alerts' => $this->getWidgetAlerts($widgetId),
                'health_score' => $this->calculateHealthScore($widgetId),
                'generated_at' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get widget metrics', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return [
                'widget_id' => $widgetId,
                'error' => 'Failed to retrieve metrics',
                'generated_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Geçmiş metrikleri al
     */
    private function getHistoricalMetrics(string $widgetId, Carbon $since): array
    {
        $pattern = self::MONITORING_PREFIX . 'metrics_' . $widgetId . '_*';
        $keys = Cache::tags(['widget_metrics'])->get($pattern, []);
        
        $data = [];
        $sinceTimestamp = $since->timestamp;

        foreach ($keys as $key) {
            if (strpos($key, (string)$sinceTimestamp) !== false) {
                $metrics = Cache::get($key);
                if ($metrics) {
                    $data[] = $metrics;
                }
            }
        }

        // Zaman sırasına göre sırala
        usort($data, function ($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });

        return $data;
    }

    /**
     * Alert kontrolü yap
     */
    private function checkAlerts(string $widgetId, array $metrics): void
    {
        $alerts = [];

        // Error rate kontrolü
        $realTimeKey = self::MONITORING_PREFIX . 'realtime_' . $widgetId;
        $realTimeData = Cache::get($realTimeKey, []);
        
        if (isset($realTimeData['error_rate']) && $realTimeData['error_rate'] > self::ALERT_THRESHOLDS['error_rate']) {
            $alerts[] = [
                'type' => 'error_rate_high',
                'severity' => 'warning',
                'message' => 'Widget error rate is above threshold',
                'current_value' => $realTimeData['error_rate'],
                'threshold' => self::ALERT_THRESHOLDS['error_rate'],
                'timestamp' => now()->toISOString()
            ];
        }

        // Response time kontrolü
        if ($metrics['render_time'] > self::ALERT_THRESHOLDS['response_time']) {
            $alerts[] = [
                'type' => 'response_time_high',
                'severity' => 'warning',
                'message' => 'Widget response time is above threshold',
                'current_value' => $metrics['render_time'],
                'threshold' => self::ALERT_THRESHOLDS['response_time'],
                'timestamp' => now()->toISOString()
            ];
        }

        // Memory usage kontrolü
        if ($metrics['memory_usage'] > self::ALERT_THRESHOLDS['memory_usage']) {
            $alerts[] = [
                'type' => 'memory_usage_high',
                'severity' => 'warning',
                'message' => 'Widget memory usage is above threshold',
                'current_value' => $metrics['memory_usage'],
                'threshold' => self::ALERT_THRESHOLDS['memory_usage'],
                'timestamp' => now()->toISOString()
            ];
        }

        // Query count kontrolü
        if ($metrics['query_count'] > self::ALERT_THRESHOLDS['query_count']) {
            $alerts[] = [
                'type' => 'query_count_high',
                'severity' => 'warning',
                'message' => 'Widget query count is above threshold',
                'current_value' => $metrics['query_count'],
                'threshold' => self::ALERT_THRESHOLDS['query_count'],
                'timestamp' => now()->toISOString()
            ];
        }

        // Alert'leri kaydet
        if (!empty($alerts)) {
            $this->storeAlerts($widgetId, $alerts);
        }
    }

    /**
     * Alert'leri sakla
     */
    private function storeAlerts(string $widgetId, array $alerts): void
    {
        $alertKey = self::MONITORING_PREFIX . 'alerts_' . $widgetId;
        $existingAlerts = Cache::get($alertKey, []);

        foreach ($alerts as $alert) {
            $existingAlerts[] = $alert;
            
            Log::warning('Widget alert triggered', [
                'widget_id' => $widgetId,
                'alert' => $alert
            ]);
        }

        // Son 50 alert'i tut
        if (count($existingAlerts) > 50) {
            $existingAlerts = array_slice($existingAlerts, -50);
        }

        Cache::put($alertKey, $existingAlerts, 3600); // 1 hour
    }

    /**
     * Widget alert'lerini al
     */
    public function getWidgetAlerts(string $widgetId): array
    {
        $alertKey = self::MONITORING_PREFIX . 'alerts_' . $widgetId;
        return Cache::get($alertKey, []);
    }

    /**
     * Widget health score hesapla
     */
    public function calculateHealthScore(string $widgetId): array
    {
        try {
            $realTimeKey = self::MONITORING_PREFIX . 'realtime_' . $widgetId;
            $metrics = Cache::get($realTimeKey, []);

            if (empty($metrics)) {
                return [
                    'score' => 100,
                    'status' => 'unknown',
                    'factors' => ['No data available']
                ];
            }

            $score = 100;
            $factors = [];

            // Error rate penalty
            if (isset($metrics['error_rate'])) {
                $errorPenalty = min(50, $metrics['error_rate'] * 1000);
                $score -= $errorPenalty;
                if ($errorPenalty > 0) {
                    $factors[] = "Error rate: {$metrics['error_rate']} (-{$errorPenalty} points)";
                }
            }

            // Performance penalty
            if (isset($metrics['avg_render_time']) && $metrics['avg_render_time'] > 1000) {
                $performancePenalty = min(30, ($metrics['avg_render_time'] - 1000) / 100);
                $score -= $performancePenalty;
                $factors[] = "Slow response time (-{$performancePenalty} points)";
            }

            // Memory usage penalty
            if (isset($metrics['avg_memory']) && $metrics['avg_memory'] > 20 * 1024 * 1024) {
                $memoryPenalty = min(20, ($metrics['avg_memory'] - 20 * 1024 * 1024) / (1024 * 1024));
                $score -= $memoryPenalty;
                $factors[] = "High memory usage (-{$memoryPenalty} points)";
            }

            $score = max(0, $score);

            // Status belirleme
            if ($score >= 90) {
                $status = 'excellent';
            } elseif ($score >= 70) {
                $status = 'good';
            } elseif ($score >= 50) {
                $status = 'fair';
            } elseif ($score >= 30) {
                $status = 'poor';
            } else {
                $status = 'critical';
            }

            return [
                'score' => round($score, 2),
                'status' => $status,
                'factors' => $factors,
                'calculated_at' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to calculate health score', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return [
                'score' => 0,
                'status' => 'error',
                'factors' => ['Calculation error: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Sistem geneli widget istatistikleri
     */
    public function getSystemWideStats(): array
    {
        try {
            $pattern = self::MONITORING_PREFIX . 'realtime_*';
            $keys = [];
            
            // Cache'den tüm widget anahtarlarını al (basitleştirilmiş)
            // Production'da daha verimli bir yöntem kullanılmalı
            
            $stats = [
                'total_widgets' => 0,
                'active_widgets' => 0,
                'healthy_widgets' => 0,
                'warning_widgets' => 0,
                'critical_widgets' => 0,
                'total_requests' => 0,
                'total_errors' => 0,
                'avg_response_time' => 0,
                'generated_at' => now()->toISOString()
            ];

            // Bu implementasyon geliştirilebilir
            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get system-wide stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'error' => 'Failed to retrieve system stats',
                'generated_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Monitoring cache'ini temizle
     */
    public function clearMonitoringCache(string $widgetId = null): bool
    {
        try {
            if ($widgetId) {
                // Specific widget cache'ini temizle
                $patterns = [
                    self::MONITORING_PREFIX . 'realtime_' . $widgetId,
                    self::MONITORING_PREFIX . 'alerts_' . $widgetId,
                    self::MONITORING_PREFIX . 'metrics_' . $widgetId . '_*'
                ];

                foreach ($patterns as $pattern) {
                    Cache::forget($pattern);
                }
            } else {
                // Tüm monitoring cache'ini temizle
                Cache::tags(['widget_monitoring'])->flush();
            }

            Log::info('Widget monitoring cache cleared', [
                'widget_id' => $widgetId ?? 'all'
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to clear monitoring cache', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Performance trend analizi
     */
    public function getPerformanceTrend(string $widgetId, int $days = 7): array
    {
        try {
            $trends = [
                'widget_id' => $widgetId,
                'period_days' => $days,
                'trend_data' => [],
                'summary' => [
                    'improving' => false,
                    'degrading' => false,
                    'stable' => true,
                    'recommendation' => 'Monitor performance'
                ],
                'generated_at' => now()->toISOString()
            ];

            // Trend analizi implementasyonu geliştirilebilir
            return $trends;

        } catch (\Exception $e) {
            Log::error('Failed to get performance trend', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return [
                'error' => 'Failed to analyze performance trend',
                'widget_id' => $widgetId
            ];
        }
    }

    /**
     * Monitoring raporu oluştur
     */
    public function generateMonitoringReport(array $widgetIds = [], int $hours = 24): array
    {
        try {
            $report = [
                'report_type' => 'widget_monitoring',
                'period_hours' => $hours,
                'widget_count' => count($widgetIds),
                'widgets' => [],
                'summary' => [
                    'total_requests' => 0,
                    'total_errors' => 0,
                    'avg_response_time' => 0,
                    'healthy_widgets' => 0,
                    'warning_widgets' => 0,
                    'critical_widgets' => 0
                ],
                'generated_at' => now()->toISOString()
            ];

            foreach ($widgetIds as $widgetId) {
                $metrics = $this->getWidgetMetrics($widgetId, $hours);
                $healthScore = $this->calculateHealthScore($widgetId);
                
                $report['widgets'][] = [
                    'widget_id' => $widgetId,
                    'metrics' => $metrics,
                    'health_score' => $healthScore
                ];

                // Summary güncellemesi
                if ($healthScore['score'] >= 70) {
                    $report['summary']['healthy_widgets']++;
                } elseif ($healthScore['score'] >= 30) {
                    $report['summary']['warning_widgets']++;
                } else {
                    $report['summary']['critical_widgets']++;
                }
            }

            Log::info('Monitoring report generated', [
                'widget_count' => count($widgetIds),
                'period_hours' => $hours
            ]);

            return $report;

        } catch (\Exception $e) {
            Log::error('Failed to generate monitoring report', [
                'widget_ids' => $widgetIds,
                'error' => $e->getMessage()
            ]);

            return [
                'error' => 'Failed to generate monitoring report',
                'generated_at' => now()->toISOString()
            ];
        }
    }
}