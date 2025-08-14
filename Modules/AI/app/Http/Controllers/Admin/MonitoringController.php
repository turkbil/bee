<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AI\App\Services\MonitoringService;
use Modules\AI\App\Services\GlobalAIMonitoringService;
use Modules\AI\App\Services\AICreditService;
use App\Http\Controllers\Controller;

/**
 * GLOBAL AI MONITORING DASHBOARD CONTROLLER - YENİLENMİŞ SİSTEM
 * 
 * Her AI kullanımını global olarak takip eden kapsamlı monitoring dashboard'u yönetir.
 * Real-time veriler, debug bilgileri, analytics ve kredi takibi sağlar.
 */
class MonitoringController extends Controller
{
    public function __construct(
        private readonly MonitoringService $monitoringService,
        private readonly GlobalAIMonitoringService $globalMonitoringService,
        private readonly AICreditService $creditService
    ) {}

    /**
     * GLOBAL AI MONITORING - Ana dashboard sayfası
     */
    public function index(Request $request): View
    {
        // Global monitoring verilerini al
        $realTimeMetrics = $this->globalMonitoringService->getRealTimeMetrics();
        $debugData = $this->globalMonitoringService->getDebugData(limit: 20);
        
        // Kredi bilgileri
        $currentBalance = ai_get_credit_balance();
        $totalUsed = ai_get_total_credits_used();
        $monthlyUsage = ai_get_monthly_credits_used();
        
        // Legacy monitoring sistemi (eski sistem uyumluluğu için)
        try {
            $legacyData = $this->monitoringService->getDashboardData('24h');
        } catch (\Exception $e) {
            $legacyData = [];
        }
        
        return view('ai::admin.monitoring.dashboard', compact(
            'realTimeMetrics',
            'debugData', 
            'currentBalance',
            'totalUsed',
            'monthlyUsage',
            'legacyData'
        ));
    }

    /**
     * Dashboard API endpoint'i
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        $timeframe = $request->get('timeframe', '24h');
        
        // Geçerli timeframe kontrolü
        $validTimeframes = ['1h', '24h', '7d', '30d'];
        if (!in_array($timeframe, $validTimeframes)) {
            $timeframe = '24h';
        }

        try {
            $data = $this->monitoringService->getDashboardData($timeframe);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timeframe' => $timeframe,
                'generated_at' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Dashboard veriler yüklenemedi',
                'message' => config('app.debug') ? $e->getMessage() : 'Sistemsel hata',
            ], 500);
        }
    }

    /**
     * Sistem sağlığı durumu
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $data = $this->monitoringService->getDashboardData('1h');
            
            return response()->json([
                'success' => true,
                'health_status' => $data['overview']['system_status'],
                'overview' => $data['overview'],
                'alerts' => $data['alerts'],
                'real_time_stats' => $data['real_time_stats'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'health_status' => 'unknown',
                'error' => 'Sistem durumu alınamadı',
            ], 500);
        }
    }

    /**
     * Performans metrikleri
     */
    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        $timeframe = $request->get('timeframe', '24h');
        
        try {
            $data = $this->monitoringService->getDashboardData($timeframe);
            
            return response()->json([
                'success' => true,
                'performance' => $data['performance'],
                'feature_performance' => $data['feature_performance'],
                'timeframe' => $timeframe,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Performans metrikleri alınamadı',
            ], 500);
        }
    }

    /**
     * Kullanım analitikleri
     */
    public function getUsageAnalytics(Request $request): JsonResponse
    {
        $timeframe = $request->get('timeframe', '24h');
        
        try {
            $data = $this->monitoringService->getDashboardData($timeframe);
            
            return response()->json([
                'success' => true,
                'usage_analytics' => $data['usage_analytics'],
                'cost_analysis' => $data['cost_analysis'],
                'timeframe' => $timeframe,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Kullanım analitikleri alınamadı',
            ], 500);
        }
    }

    /**
     * Provider sağlık durumu
     */
    public function getProviderHealth(): JsonResponse
    {
        try {
            $data = $this->monitoringService->getDashboardData('1h');
            
            return response()->json([
                'success' => true,
                'provider_health' => $data['provider_health'],
                'updated_at' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Provider durumu alınamadı',
            ], 500);
        }
    }

    /**
     * Gerçek zamanlı istatistikler
     */
    public function getRealTimeStats(): JsonResponse
    {
        try {
            $data = $this->monitoringService->getDashboardData('1h');
            
            return response()->json([
                'success' => true,
                'real_time_stats' => $data['real_time_stats'],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gerçek zamanlı veriler alınamadı',
            ], 500);
        }
    }

    /**
     * Sistem uyarıları
     */
    public function getAlerts(): JsonResponse
    {
        try {
            $data = $this->monitoringService->getDashboardData('1h');
            
            return response()->json([
                'success' => true,
                'alerts' => $data['alerts'],
                'alert_count' => count($data['alerts']),
                'critical_count' => count(array_filter($data['alerts'], fn($alert) => $alert['type'] === 'critical')),
                'warning_count' => count(array_filter($data['alerts'], fn($alert) => $alert['type'] === 'warning')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Uyarılar alınamadı',
            ], 500);
        }
    }

    /**
     * Maliyet raporu
     */
    public function getCostReport(Request $request): JsonResponse
    {
        $timeframe = $request->get('timeframe', '30d');
        
        try {
            $data = $this->monitoringService->getDashboardData($timeframe);
            
            return response()->json([
                'success' => true,
                'cost_analysis' => $data['cost_analysis'],
                'timeframe' => $timeframe,
                'currency' => 'Credits', // AI sistemde credit kullanılıyor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Maliyet raporu alınamadı',
            ], 500);
        }
    }

    /**
     * Export dashboard data (CSV/JSON)
     */
    public function exportData(Request $request)
    {
        $format = $request->get('format', 'json'); // json, csv
        $timeframe = $request->get('timeframe', '24h');
        
        try {
            $data = $this->monitoringService->getDashboardData($timeframe);
            
            $filename = "ai-monitoring-{$timeframe}-" . now()->format('Y-m-d-H-i-s');
            
            if ($format === 'csv') {
                return $this->exportAsCSV($data, $filename);
            }
            
            return response()->json($data)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Data export edilemedi',
            ], 500);
        }
    }

    // GLOBAL AI MONITORING - YENİ API ENDPOINT'LER

    /**
     * Global real-time metrics API
     */
    public function globalRealTimeMetrics(Request $request): JsonResponse
    {
        try {
            $tenantId = $request->query('tenant_id', tenant('id'));
            $metrics = $this->globalMonitoringService->getRealTimeMetrics($tenantId);
            
            return response()->json([
                'success' => true,
                'data' => $metrics,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Global comprehensive analytics
     */
    public function globalAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'feature', 'provider']);
            $tenantId = $request->query('tenant_id', tenant('id'));
            
            $analytics = $this->globalMonitoringService->getComprehensiveAnalytics($tenantId, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $analytics,
                'filters' => $filters,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Global debug data API
     */
    public function globalDebugData(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 100);
            $tenantId = $request->query('tenant_id', tenant('id'));
            
            $debugData = $this->globalMonitoringService->getDebugData($tenantId, (int) $limit);
            
            return response()->json([
                'success' => true,
                'data' => $debugData,
                'limit' => $limit,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Live usage stream (Server-Sent Events için)
     */
    public function liveStream(Request $request): JsonResponse
    {
        try {
            $tenantId = tenant('id');
            $lastId = $request->query('last_id', 0);
            
            // Son kullanımları al (last_id'den sonrakiler)
            $recentUsage = \Modules\AI\App\Models\AICreditUsage::where('tenant_id', $tenantId)
                ->where('id', '>', $lastId)
                ->orderBy('used_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($usage) {
                    $metadata = json_decode($usage->metadata ?? '{}', true);
                    return [
                        'id' => $usage->id,
                        'timestamp' => $usage->used_at->format('H:i:s'),
                        'feature' => $usage->feature_slug,
                        'provider' => $usage->provider_name,
                        'tokens' => $usage->total_tokens,
                        'credits' => round($usage->credits_used, 4),
                        'processing_time' => $metadata['processing_time'] ?? 0,
                        'success' => $metadata['success'] ?? true
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recentUsage,
                'last_id' => $recentUsage->isNotEmpty() ? $recentUsage->first()['id'] : $lastId,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kredi durumu API
     */
    public function creditStatus(Request $request): JsonResponse
    {
        try {
            $tenantId = tenant('id');
            
            $stats = [
                'current_balance' => ai_get_credit_balance($tenantId),
                'total_used' => ai_get_total_credits_used($tenantId),
                'total_purchased' => ai_get_total_credits_purchased($tenantId),
                'monthly_used' => ai_get_monthly_credits_used($tenantId),
                'daily_used' => ai_get_daily_credits_used($tenantId),
            ];

            return response()->json([
                'success' => true,
                'credit_stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * CSV export helper
     */
    private function exportAsCSV(array $data, string $filename)
    {
        $csvData = [];
        
        // Overview data için CSV satırları
        $csvData[] = ['Metric', 'Value'];
        $csvData[] = ['System Status', $data['overview']['system_status']];
        $csvData[] = ['Active Features', $data['overview']['active_features']];
        $csvData[] = ['Daily Usage', $data['overview']['daily_usage']];
        $csvData[] = ['Avg Response Time', $data['overview']['avg_response_time'] . 'ms'];
        $csvData[] = ['Success Rate', ($data['overview']['success_rate'] * 100) . '%'];
        
        // Feature performance data
        $csvData[] = ['', ''];
        $csvData[] = ['Feature Performance', ''];
        $csvData[] = ['Feature Name', 'Total Requests', 'Avg Response Time', 'Success Rate', 'Performance Score'];
        
        foreach ($data['feature_performance'] as $feature) {
            $csvData[] = [
                $feature['name'],
                $feature['total_requests'],
                $feature['avg_response_time'] . 'ms',
                $feature['success_rate'] . '%',
                $feature['performance_score'],
            ];
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];
        
        return response()->streamDownload(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, "{$filename}.csv", $headers);
    }
}