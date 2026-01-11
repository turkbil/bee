<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, Log, Validator};
use Illuminate\Validation\Rule;
use Modules\AI\App\Services\V3\SmartAnalyzer;
use Modules\AI\App\Services\Context\ContextAwareEngine;
use Carbon\Carbon;

/**
 * UNIVERSAL INPUT SYSTEM V3 - ANALYTICS CONTROLLER
 * 
 * Enterprise-level analytics controller with advanced metrics,
 * performance monitoring, and intelligent insights generation.
 * 
 * Features:
 * - Real-time usage analytics with smart caching
 * - Performance metrics and bottleneck detection
 * - Predictive behavior modeling
 * - Advanced reporting with customizable timeframes
 * - Context-aware analytics filtering
 * - Export capabilities for enterprise reporting
 * 
 * @author Claude Code
 * @version 3.0
 */
class AnalyticsController extends Controller
{
    public function __construct(
        private SmartAnalyzer $smartAnalyzer,
        private ContextAwareEngine $contextEngine
    ) {}

    /**
     * Get comprehensive analytics dashboard data
     */
    public function getDashboardMetrics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'timeframe' => ['string', Rule::in(['1h', '24h', '7d', '30d', '90d', '1y'])],
                'metrics' => ['array'],
                'metrics.*' => ['string', Rule::in(['usage', 'performance', 'features', 'users', 'errors'])],
                'context_filters' => ['array'],
                'include_predictions' => ['boolean'],
                'refresh_cache' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $timeframe = $request->input('timeframe', '24h');
            $metrics = $request->input('metrics', ['usage', 'performance', 'features']);
            $contextFilters = $request->input('context_filters', []);
            $includePredictions = $request->boolean('include_predictions', false);
            $refreshCache = $request->boolean('refresh_cache', false);

            $cacheKey = "analytics_dashboard_{$timeframe}_" . md5(serialize([$metrics, $contextFilters]));
            
            if ($refreshCache) {
                Cache::forget($cacheKey);
            }

            $analytics = Cache::remember($cacheKey, now()->addMinutes(15), function () use (
                $timeframe, $metrics, $contextFilters, $includePredictions
            ) {
                $context = $this->contextEngine->buildAnalyticsContext([
                    'timeframe' => $timeframe,
                    'filters' => $contextFilters,
                    'requested_metrics' => $metrics
                ]);

                $dashboardData = $this->smartAnalyzer->generateDashboardMetrics($context);

                if ($includePredictions) {
                    $dashboardData['predictions'] = $this->smartAnalyzer->generatePredictiveInsights($context);
                }

                return $dashboardData;
            });

            Log::info('Analytics dashboard metrics retrieved', [
                'timeframe' => $timeframe,
                'metrics_requested' => $metrics,
                'cache_used' => !$refreshCache,
                'data_points' => count($analytics['metrics'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'meta' => [
                    'timeframe' => $timeframe,
                    'generated_at' => now(),
                    'cache_expires_at' => now()->addMinutes(15),
                    'includes_predictions' => $includePredictions
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get analytics dashboard metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate dashboard metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed usage analytics for specific features
     */
    public function getFeatureAnalytics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'feature_ids' => ['array'],
                'feature_ids.*' => ['integer', 'exists:ai_features,id'],
                'timeframe' => ['string', Rule::in(['1h', '6h', '24h', '7d', '30d'])],
                'group_by' => ['string', Rule::in(['hour', 'day', 'week', 'feature', 'user'])],
                'include_comparisons' => ['boolean'],
                'include_trends' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $featureIds = $request->input('feature_ids', []);
            $timeframe = $request->input('timeframe', '24h');
            $groupBy = $request->input('group_by', 'hour');
            $includeComparisons = $request->boolean('include_comparisons', false);
            $includeTrends = $request->boolean('include_trends', true);

            $context = $this->contextEngine->buildAnalyticsContext([
                'type' => 'feature_analytics',
                'feature_ids' => $featureIds,
                'timeframe' => $timeframe,
                'grouping' => $groupBy
            ]);

            $analytics = $this->smartAnalyzer->analyzeFeatureUsage($context);

            if ($includeComparisons) {
                $analytics['comparisons'] = $this->smartAnalyzer->generateFeatureComparisons($context);
            }

            if ($includeTrends) {
                $analytics['trends'] = $this->smartAnalyzer->analyzeUsageTrends($context);
            }

            Log::info('Feature analytics retrieved', [
                'feature_count' => count($featureIds),
                'timeframe' => $timeframe,
                'group_by' => $groupBy,
                'data_points' => count($analytics['usage_data'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'meta' => [
                    'feature_count' => count($featureIds),
                    'timeframe' => $timeframe,
                    'grouping' => $groupBy,
                    'generated_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get feature analytics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate feature analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics and bottleneck analysis
     */
    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'metric_types' => ['array'],
                'metric_types.*' => ['string', Rule::in(['response_time', 'memory_usage', 'cpu_usage', 'database', 'cache'])],
                'timeframe' => ['string', Rule::in(['1h', '6h', '24h', '7d'])],
                'include_bottlenecks' => ['boolean'],
                'include_recommendations' => ['boolean'],
                'threshold_analysis' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $metricTypes = $request->input('metric_types', ['response_time', 'memory_usage']);
            $timeframe = $request->input('timeframe', '24h');
            $includeBottlenecks = $request->boolean('include_bottlenecks', true);
            $includeRecommendations = $request->boolean('include_recommendations', true);
            $thresholdAnalysis = $request->boolean('threshold_analysis', false);

            $context = $this->contextEngine->buildAnalyticsContext([
                'type' => 'performance_metrics',
                'metrics' => $metricTypes,
                'timeframe' => $timeframe,
                'analysis_depth' => $includeBottlenecks ? 'deep' : 'standard'
            ]);

            $performance = $this->smartAnalyzer->analyzePerformanceMetrics($context);

            if ($includeBottlenecks) {
                $performance['bottlenecks'] = $this->smartAnalyzer->detectBottlenecks($context);
            }

            if ($includeRecommendations) {
                $performance['recommendations'] = $this->smartAnalyzer->generatePerformanceRecommendations($context);
            }

            if ($thresholdAnalysis) {
                $performance['threshold_analysis'] = $this->smartAnalyzer->analyzePerformanceThresholds($context);
            }

            Log::info('Performance metrics analyzed', [
                'metric_types' => $metricTypes,
                'timeframe' => $timeframe,
                'bottlenecks_detected' => count($performance['bottlenecks'] ?? []),
                'recommendations_count' => count($performance['recommendations'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $performance,
                'meta' => [
                    'metric_types' => $metricTypes,
                    'timeframe' => $timeframe,
                    'analysis_completed_at' => now(),
                    'next_analysis_at' => now()->addHour()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to analyze performance metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze performance metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate custom analytics report
     */
    public function generateCustomReport(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'report_name' => ['required', 'string', 'max:255'],
                'report_type' => ['required', 'string', Rule::in(['usage', 'performance', 'features', 'users', 'custom'])],
                'date_range' => ['required', 'array'],
                'date_range.start' => ['required', 'date'],
                'date_range.end' => ['required', 'date', 'after:date_range.start'],
                'filters' => ['array'],
                'metrics' => ['required', 'array', 'min:1'],
                'export_format' => ['string', Rule::in(['json', 'csv', 'xlsx', 'pdf'])],
                'include_charts' => ['boolean'],
                'include_insights' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reportConfig = [
                'name' => $request->input('report_name'),
                'type' => $request->input('report_type'),
                'date_range' => $request->input('date_range'),
                'filters' => $request->input('filters', []),
                'metrics' => $request->input('metrics'),
                'export_format' => $request->input('export_format', 'json'),
                'include_charts' => $request->boolean('include_charts', false),
                'include_insights' => $request->boolean('include_insights', true)
            ];

            $context = $this->contextEngine->buildAnalyticsContext([
                'type' => 'custom_report',
                'config' => $reportConfig
            ]);

            $report = $this->smartAnalyzer->generateCustomReport($context);

            Log::info('Custom analytics report generated', [
                'report_name' => $reportConfig['name'],
                'report_type' => $reportConfig['type'],
                'date_range' => $reportConfig['date_range'],
                'metrics_count' => count($reportConfig['metrics']),
                'export_format' => $reportConfig['export_format']
            ]);

            return response()->json([
                'success' => true,
                'data' => $report,
                'meta' => [
                    'report_config' => $reportConfig,
                    'generated_at' => now(),
                    'data_points' => $report['total_data_points'] ?? 0,
                    'insights_included' => $reportConfig['include_insights']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate custom report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate custom report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time analytics data
     */
    public function getRealTimeMetrics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'metrics' => ['array'],
                'metrics.*' => ['string', Rule::in(['active_users', 'requests_per_minute', 'error_rate', 'response_time'])],
                'interval' => ['integer', 'min:1', 'max:60'], // minutes
                'include_history' => ['boolean']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $metrics = $request->input('metrics', ['active_users', 'requests_per_minute']);
            $interval = $request->input('interval', 1);
            $includeHistory = $request->boolean('include_history', false);

            $context = $this->contextEngine->buildAnalyticsContext([
                'type' => 'realtime_metrics',
                'metrics' => $metrics,
                'interval' => $interval
            ]);

            $realTimeData = $this->smartAnalyzer->getRealTimeMetrics($context);

            if ($includeHistory) {
                $realTimeData['history'] = $this->smartAnalyzer->getRealTimeHistory($context, 30); // Last 30 intervals
            }

            return response()->json([
                'success' => true,
                'data' => $realTimeData,
                'meta' => [
                    'interval_minutes' => $interval,
                    'metrics_tracked' => $metrics,
                    'timestamp' => now(),
                    'next_update' => now()->addMinutes($interval)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get real-time metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get real-time metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'export_type' => ['required', 'string', Rule::in(['dashboard', 'features', 'performance', 'custom'])],
                'format' => ['required', 'string', Rule::in(['csv', 'xlsx', 'json', 'pdf'])],
                'date_range' => ['required', 'array'],
                'date_range.start' => ['required', 'date'],
                'date_range.end' => ['required', 'date', 'after:date_range.start'],
                'filters' => ['array'],
                'include_charts' => ['boolean'],
                'email_to' => ['email']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $exportConfig = [
                'type' => $request->input('export_type'),
                'format' => $request->input('format'),
                'date_range' => $request->input('date_range'),
                'filters' => $request->input('filters', []),
                'include_charts' => $request->boolean('include_charts', false),
                'email_to' => $request->input('email_to')
            ];

            $context = $this->contextEngine->buildAnalyticsContext([
                'type' => 'export_analytics',
                'config' => $exportConfig
            ]);

            $exportResult = $this->smartAnalyzer->exportAnalyticsData($context);

            Log::info('Analytics data exported', [
                'export_type' => $exportConfig['type'],
                'format' => $exportConfig['format'],
                'date_range' => $exportConfig['date_range'],
                'file_size' => $exportResult['file_size'] ?? 0,
                'emailed' => !empty($exportConfig['email_to'])
            ]);

            return response()->json([
                'success' => true,
                'data' => $exportResult,
                'meta' => [
                    'export_config' => $exportConfig,
                    'exported_at' => now(),
                    'expires_at' => now()->addDays(7) // Download link expires in 7 days
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to export analytics data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export analytics data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'cache_types' => ['array'],
                'cache_types.*' => ['string', Rule::in(['dashboard', 'features', 'performance', 'realtime', 'all'])],
                'confirm' => ['required', 'boolean', 'accepted']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cacheTypes = $request->input('cache_types', ['all']);
            
            $clearedCaches = $this->smartAnalyzer->clearAnalyticsCache($cacheTypes);

            Log::info('Analytics cache cleared', [
                'cache_types' => $cacheTypes,
                'cleared_keys' => count($clearedCaches),
                'cleared_by' => auth()->id() ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Analytics cache cleared successfully',
                'data' => [
                    'cleared_cache_types' => $cacheTypes,
                    'cleared_keys_count' => count($clearedCaches),
                    'cleared_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear analytics cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear analytics cache: ' . $e->getMessage()
            ], 500);
        }
    }
}