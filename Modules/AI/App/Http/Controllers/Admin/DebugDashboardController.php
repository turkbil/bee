<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\AIPriorityEngine;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * AI DEBUG DASHBOARD CONTROLLER
 * 
 * Gelişmiş AI analytics ve debugging için interactive dashboard
 * - Real-time prompt analysis
 * - Tenant-specific usage patterns
 * - Interactive prompt viewer
 * - Performance analytics
 */
class DebugDashboardController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Ana dashboard sayfası
     */
    public function index(Request $request)
    {
        // Tenant filter
        $tenantId = $request->get('tenant_id');
        $dateRange = $request->get('date_range', '7'); // Son 7 gün
        $feature = $request->get('feature');

        // Quick stats
        $stats = $this->getQuickStats($tenantId, $dateRange, $feature);
        
        // Hourly usage for charts
        $stats['hourly_usage'] = $this->getHourlyUsageData($tenantId, 1); // Son 24 saat
        
        // Recent logs
        $recentLogs = $this->getRecentLogs($tenantId, $feature, 20);
        
        // Top features
        $topFeatures = $this->getTopFeatures($tenantId, $dateRange);
        
        // Prompt usage stats
        $promptStats = $this->getPromptUsageStats($tenantId, $dateRange);
        
        // Tenants list for filter
        $tenants = $this->getTenantsWithUsage();
        
        // Active features
        $features = AIFeature::active()->orderBy('name')->get();
        
        // Available providers ve models
        $availableProviders = $this->getAvailableProviders();
        
        // Provider/Model statistics
        $stats['provider_stats'] = $this->getProviderModelStats($tenantId, $dateRange);

        return view('ai::admin.debug-dashboard.index', compact(
            'stats', 'recentLogs', 'topFeatures', 'promptStats', 
            'tenants', 'features', 'tenantId', 'dateRange', 'feature', 'availableProviders'
        ));
    }

    /**
     * Real-time prompt testi
     */
    public function testPrompt(Request $request): JsonResponse
    {
        $request->validate([
            'input' => 'required|string|min:3',
            'feature_slug' => 'nullable|string',
            'context_type' => 'in:minimal,essential,normal,detailed,complete',
            'provider_model' => 'nullable|string'
        ]);

        try {
            $input = $request->input;
            $featureSlug = $request->feature_slug;
            $contextType = $request->context_type ?? 'normal';
            $providerModel = $request->provider_model;

            // Feature'ı bul
            $feature = null;
            if ($featureSlug) {
                $feature = AIFeature::where('slug', $featureSlug)->first();
            }

            // Debug mode'da prompt build et
            $startTime = microtime(true);
            $options = [
                'context_type' => $contextType,
                'feature_name' => $featureSlug,
                'debug_mode' => true,
                'start_time' => $startTime
            ];

            if ($feature) {
                $options['feature'] = $feature;
            }

            // Priority engine'den component'leri al
            $components = $this->buildTestComponents($feature, $options);
            
            // Scoring simulation
            $scoredComponents = AIPriorityEngine::scoreComponents($components);
            $threshold = AIPriorityEngine::CONTEXT_THRESHOLDS[$contextType] ?? 4000;
            
            // Filter components
            $usedComponents = array_filter($scoredComponents, fn($c) => $c['final_score'] >= $threshold);
            $filteredComponents = array_filter($scoredComponents, fn($c) => $c['final_score'] < $threshold);
            
            // Sort by score
            usort($usedComponents, fn($a, $b) => $b['final_score'] <=> $a['final_score']);
            usort($filteredComponents, fn($a, $b) => $b['final_score'] <=> $a['final_score']);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'analysis' => [
                    'input' => $input,
                    'feature' => $feature ? $feature->name : 'Generic Chat',
                    'context_type' => $contextType,
                    'provider_model' => $providerModel ?: 'Default (Current Active)',
                    'threshold' => $threshold,
                    'execution_time_ms' => $executionTime,
                    'total_components' => count($components),
                    'used_components' => count($usedComponents),
                    'filtered_components' => count($filteredComponents),
                    'used_prompts' => array_map(function($comp, $index) {
                        return [
                            'position' => $index + 1,
                            'name' => $comp['name'],
                            'category' => $comp['category'],
                            'category_label' => $this->getCategoryLabel($comp['category']),
                            'priority' => $comp['priority'],
                            'priority_label' => $this->getPriorityLabel($comp['priority']),
                            'base_weight' => $comp['base_weight'],
                            'multiplier' => $comp['multiplier'],
                            'position_bonus' => $comp['position_bonus'],
                            'final_score' => $comp['final_score'],
                            'content_preview' => substr($comp['content'], 0, 150) . '...',
                            'content_length' => strlen($comp['content']),
                            'score_explanation' => $this->explainScore($comp)
                        ];
                    }, $usedComponents, array_keys($usedComponents)),
                    'filtered_prompts' => array_map(function($comp) use ($threshold) {
                        return [
                            'name' => $comp['name'],
                            'category' => $comp['category'],
                            'category_label' => $this->getCategoryLabel($comp['category']),
                            'priority' => $comp['priority'],
                            'priority_label' => $this->getPriorityLabel($comp['priority']),
                            'final_score' => $comp['final_score'],
                            'threshold' => $threshold,
                            'difference' => $threshold - $comp['final_score'],
                            'filter_reason' => 'Below threshold (' . $comp['final_score'] . ' < ' . $threshold . ')'
                        ];
                    }, $filteredComponents),
                    'scoring_summary' => [
                        'highest_score' => !empty($usedComponents) ? max(array_column($usedComponents, 'final_score')) : 0,
                        'lowest_used_score' => !empty($usedComponents) ? min(array_column($usedComponents, 'final_score')) : 0,
                        'average_score' => !empty($usedComponents) ? round(array_sum(array_column($usedComponents, 'final_score')) / count($usedComponents)) : 0,
                        'total_content_length' => array_sum(array_column($usedComponents, 'content_length'))
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prompt içeriğini detaylı göster
     */
    public function getPromptDetails(Request $request): JsonResponse
    {
        $request->validate([
            'prompt_name' => 'required|string',
            'category' => 'required|string'
        ]);

        try {
            $promptName = $request->prompt_name;
            $category = $request->category;

            // Prompt'u bul
            $prompt = null;
            if ($category === 'system_common' && $promptName === 'Ortak Özellikler') {
                $prompt = Prompt::where('name', 'Ortak Özellikler')->first();
            } else {
                $prompt = Prompt::where('name', $promptName)->first();
            }

            if (!$prompt) {
                return response()->json([
                    'success' => false,
                    'error' => 'Prompt not found'
                ], 404);
            }

            // Usage statistics
            $usageStats = $this->getPromptUsageStatistics($promptName);

            return response()->json([
                'success' => true,
                'prompt' => [
                    'name' => $prompt->name,
                    'content' => $prompt->content,
                    'category' => $prompt->prompt_category ?? $category,
                    'priority' => $prompt->priority ?? 3,
                    'ai_weight' => $prompt->ai_weight ?? 50,
                    'is_system' => $prompt->is_system ?? false,
                    'is_active' => $prompt->is_active ?? true,
                    'created_at' => $prompt->created_at?->format('d.m.Y H:i'),
                    'updated_at' => $prompt->updated_at?->format('d.m.Y H:i'),
                    'usage_stats' => $usageStats,
                    'content_analysis' => [
                        'length' => strlen($prompt->content),
                        'word_count' => str_word_count($prompt->content),
                        'line_count' => substr_count($prompt->content, "\n") + 1,
                        'has_turkish' => preg_match('/[çğıöşüÇĞIİÖŞÜ]/', $prompt->content) ? true : false,
                        'mentions_markdown' => stripos($prompt->content, 'markdown') !== false,
                        'mentions_turkish' => stripos($prompt->content, 'türkçe') !== false || stripos($prompt->content, 'turkish') !== false
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load prompt details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tenant detay analytics
     */
    public function tenantAnalytics(Request $request, string $tenantId)
    {
        $dateRange = $request->get('date_range', '30');
        
        // Tenant bilgisi
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Analytics data
        $analytics = [
            'tenant' => $tenant,
            'usage_timeline' => $this->getTenantUsageTimeline($tenantId, $dateRange),
            'feature_breakdown' => $this->getTenantFeatureBreakdown($tenantId, $dateRange),
            'prompt_efficiency' => $this->getTenantPromptEfficiency($tenantId, $dateRange),
            'user_activity' => $this->getTenantUserActivity($tenantId, $dateRange),
            'performance_metrics' => $this->getTenantPerformanceMetrics($tenantId, $dateRange)
        ];

        return view('ai::admin.debug-dashboard.tenant-analytics', compact('analytics', 'tenantId', 'dateRange'));
    }

    /**
     * Live log stream (AJAX endpoint)
     */
    public function liveLogStream(Request $request): JsonResponse
    {
        $lastId = $request->get('last_id', 0);
        $tenantId = $request->get('tenant_id');
        
        $query = DB::table('ai_tenant_debug_logs')
            ->where('id', '>', $lastId)
            ->orderBy('id', 'desc')
            ->limit(50);
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $logs = $query->get()->map(function($log) {
            return [
                'id' => $log->id,
                'timestamp' => Carbon::parse($log->created_at)->format('H:i:s'),
                'tenant_id' => $log->tenant_id,
                'feature_slug' => $log->feature_slug,
                'request_type' => $log->request_type,
                'used_prompts' => $log->actually_used_prompts,
                'total_prompts' => $log->total_available_prompts,
                'execution_time' => $log->execution_time_ms,
                'has_error' => $log->has_error,
                'input_preview' => $log->input_preview
            ];
        });

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'last_id' => $logs->isNotEmpty() ? $logs->first()['id'] : $lastId
        ]);
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    private function buildTestComponents($feature, array $options): array
    {
        // Simulate building components like AIPriorityEngine does
        $components = [];

        // System components
        $components[] = [
            'name' => 'Ortak Özellikler',
            'category' => 'system_common',
            'priority' => 1,
            'content' => 'System prompt content...',
            'position' => 0
        ];

        // Feature-specific
        if ($feature) {
            if ($feature->quick_prompt) {
                $components[] = [
                    'name' => $feature->name . ' - Quick Prompt',
                    'category' => 'feature_definition',
                    'priority' => 1,
                    'content' => $feature->quick_prompt,
                    'position' => 0
                ];
            }

            // Expert prompts
            $expertPrompts = $feature->prompts()->where('is_active', true)->get();
            foreach ($expertPrompts as $index => $prompt) {
                $components[] = [
                    'name' => $prompt->name,
                    'category' => 'expert_knowledge',
                    'priority' => $prompt->priority ?? 2,
                    'content' => $prompt->content,
                    'position' => $index + 1
                ];
            }
        }

        // Tenant/brand context (gerçek data yoksa component ekleme)
        // Conditional info (gerçek data yoksa component ekleme)

        return $components;
    }

    private function getCategoryLabel(string $category): string
    {
        $labels = [
            'system_common' => 'Ortak Sistem',
            'system_hidden' => 'Gizli Sistem',
            'feature_definition' => 'Feature Tanımı',
            'expert_knowledge' => 'Uzman Bilgisi',
            'tenant_identity' => 'Tenant Kimliği',
            'secret_knowledge' => 'Gizli Bilgi',
            'brand_context' => 'Marka Context',
            'response_format' => 'Yanıt Formatı',
            'conditional_info' => 'Şartlı Bilgi'
        ];

        return $labels[$category] ?? $category;
    }

    private function getPriorityLabel(int $priority): string
    {
        $labels = [
            1 => 'Kritik (×1.5)',
            2 => 'Önemli (×1.2)',
            3 => 'Normal (×1.0)',
            4 => 'Opsiyonel (×0.6)',
            5 => 'Nadir (×0.3)'
        ];

        return $labels[$priority] ?? "Priority {$priority}";
    }

    private function explainScore(array $component): string
    {
        return sprintf(
            "%d × %.1f + %d = %d",
            $component['base_weight'],
            $component['multiplier'],
            $component['position_bonus'],
            $component['final_score']
        );
    }

    private function getQuickStats(?string $tenantId, string $dateRange, ?string $feature): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        // YENİ SİSTEM: Öncelikle ai_credit_usage tablosundan gerçek verileri al
        $creditQuery = DB::table('ai_credit_usage')
            ->where('used_at', '>=', $startDate);
            
        if ($tenantId) {
            $creditQuery->where('tenant_id', $tenantId);
        }
        
        if ($feature) {
            $creditQuery->where('feature_slug', $feature);
        }

        // Provider/model istatistikleri ekle
        $providerStats = $this->getProviderModelStats($tenantId, $dateRange);

        // Gerçek token kullanımını hesapla (input_tokens + output_tokens)
        $totalInputTokens = $creditQuery->sum('input_tokens') ?? 0;
        $totalOutputTokens = $creditQuery->sum('output_tokens') ?? 0;
        $totalTokens = $totalInputTokens + $totalOutputTokens;
        
        // Credits kullanımını da göster
        $totalCredits = $creditQuery->sum('credits_used') ?? 0;
        $totalRequests = $creditQuery->count();

        // Processing time hesaplama (metadata'dan)
        $avgProcessingTime = $creditQuery
            ->selectRaw('COALESCE(AVG(JSON_EXTRACT(metadata, "$.processing_time")), 0) as avg_time')
            ->first()->avg_time ?? 0;

        // Fallback: Eğer credit usage'da veri yoksa eski tablodan al
        if ($totalRequests == 0) {
            $legacyQuery = DB::table('ai_tenant_debug_logs')
                ->where('created_at', '>=', $startDate);

            if ($tenantId) {
                $legacyQuery->where('tenant_id', $tenantId);
            }

            if ($feature) {
                $legacyQuery->where('feature_slug', $feature);
            }

            $totalRequests = $legacyQuery->count();
            $avgProcessingTime = $legacyQuery->avg('execution_time_ms') ?? 0;
        }

        // Error rate hesaplama (debug logs'tan)
        $errorQuery = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $errorQuery->where('tenant_id', $tenantId);
        }

        if ($feature) {
            $errorQuery->where('feature_slug', $feature);
        }

        $totalDebugLogs = $errorQuery->count();
        $errorCount = $errorQuery->where('has_error', 1)->count();
        $errorRate = $totalDebugLogs > 0 ? round(($errorCount / $totalDebugLogs) * 100, 2) : 0;

        return [
            'total_requests' => $totalRequests, // GERÇEK REQUEST SAYISI
            'avg_execution_time' => round($avgProcessingTime, 2), // GERÇEK PROCESSING TIME
            'avg_prompts_used' => 0, // Gerçek veri yoksa 0
            'total_tokens' => $totalTokens, // GERÇEK TOKEN KULLANIMI
            'total_credits' => $totalCredits, // KREDİ KULLANIMI
            'input_tokens' => $totalInputTokens,
            'output_tokens' => $totalOutputTokens,
            'error_rate' => $errorRate,
            'provider_stats' => $providerStats
        ];
    }

    /**
     * Provider ve model bazlı kullanım istatistikleri
     */
    private function getProviderModelStats(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        // Credit usage tablondan model bazlı istatistik
        $tokenQuery = DB::table('ai_credit_usage')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('provider_name')
            ->where('provider_name', '!=', '');

        if ($tenantId) {
            $tokenQuery->where('tenant_id', $tenantId);
        }

        // Provider bazlı kullanım
        $modelUsage = $tokenQuery
            ->select('provider_name as model', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(input_tokens + output_tokens) as total_tokens'))
            ->groupBy('provider_name')
            ->orderBy('usage_count', 'desc')
            ->get();

        // Provider bazlı grupla
        $providerStats = [];
        $totalUsage = $modelUsage->sum('usage_count');
        
        foreach ($modelUsage as $usage) {
            $model = $usage->model;
            
            // Provider/model ayır
            if (str_contains($model, '/')) {
                [$provider, $modelName] = explode('/', $model, 2);
            } else {
                $provider = 'unknown';
                $modelName = $model;
            }

            if (!isset($providerStats[$provider])) {
                $providerStats[$provider] = [
                    'name' => ucfirst($provider),
                    'total_usage' => 0,
                    'total_tokens' => 0,
                    'usage_percentage' => 0,
                    'models' => []
                ];
            }

            $providerStats[$provider]['total_usage'] += $usage->usage_count;
            $providerStats[$provider]['total_tokens'] += $usage->total_tokens;
            $providerStats[$provider]['models'][$modelName] = [
                'name' => $modelName,
                'usage_count' => $usage->usage_count,
                'total_tokens' => $usage->total_tokens,
                'percentage' => $totalUsage > 0 ? round(($usage->usage_count / $totalUsage) * 100, 1) : 0
            ];
        }

        // Provider usage percentage hesapla
        foreach ($providerStats as $provider => &$stats) {
            $stats['usage_percentage'] = $totalUsage > 0 ? round(($stats['total_usage'] / $totalUsage) * 100, 1) : 0;
        }

        // En çok kullanılandan az kullanılana sırala
        uasort($providerStats, fn($a, $b) => $b['total_usage'] <=> $a['total_usage']);

        return [
            'providers' => $providerStats,
            'total_usage' => $totalUsage,
            'unique_models' => $modelUsage->count(),
            'top_model' => $modelUsage->first()?->model ?? 'N/A'
        ];
    }

    private function getRecentLogs(?string $tenantId, ?string $feature = null, int $limit = 20): \Illuminate\Support\Collection
    {
        // YENİ SİSTEM: Gerçek AI kullanım verilerini ai_credit_usage tablosundan al
        // Bu tablo chat paneli de dahil tüm AI kullanımlarını içerir

        $query = DB::table('ai_credit_usage')
            ->leftJoin('users', 'ai_credit_usage.user_id', '=', 'users.id')
            ->select([
                'ai_credit_usage.id',
                'ai_credit_usage.used_at as created_at',
                'ai_credit_usage.tenant_id',
                'ai_credit_usage.feature_slug',
                'ai_credit_usage.provider_name',
                DB::raw('COALESCE(ai_credit_usage.model, ai_credit_usage.provider_name, "unknown") as model'),
                'ai_credit_usage.input_tokens',
                'ai_credit_usage.output_tokens',
                DB::raw('ai_credit_usage.input_tokens + ai_credit_usage.output_tokens as total_tokens'),
                'ai_credit_usage.credits_used',
                'ai_credit_usage.credit_cost',
                'ai_credit_usage.conversation_id',
                'ai_credit_usage.metadata',
                DB::raw('COALESCE(JSON_UNQUOTE(JSON_EXTRACT(ai_credit_usage.metadata, "$.request_type")), "chat") as request_type'),
                'users.name as user_name',
                // Legacy format için uyumluluk alanları ekle
                DB::raw('COALESCE(JSON_EXTRACT(ai_credit_usage.metadata, "$.prompts_used"), 1) as actually_used_prompts'),
                DB::raw('COALESCE(JSON_EXTRACT(ai_credit_usage.metadata, "$.total_prompts"), 5) as total_available_prompts'),
                DB::raw('COALESCE(JSON_EXTRACT(ai_credit_usage.metadata, "$.processing_time"), 0) as execution_time_ms'),
                DB::raw('CASE WHEN JSON_EXTRACT(ai_credit_usage.metadata, "$.success") = false THEN 1 ELSE 0 END as has_error'),
                DB::raw('COALESCE(LEFT(JSON_UNQUOTE(JSON_EXTRACT(ai_credit_usage.metadata, "$.user_input")), 100), "Chat message") as input_preview')
            ])
            ->orderBy('ai_credit_usage.used_at', 'desc')
            ->limit($limit);

        if ($tenantId) {
            $query->where('ai_credit_usage.tenant_id', $tenantId);
        }

        if ($feature) {
            $query->where('ai_credit_usage.feature_slug', $feature);
        }

        $results = $query->get();

        // Tenant bilgisini sonradan ekle (cross-database JOIN yerine)
        if ($results->isNotEmpty()) {
            $tenantIds = $results->pluck('tenant_id')->unique()->filter();
            $tenants = \App\Models\Tenant::whereIn('id', $tenantIds)->pluck('title', 'id');

            $results->transform(function ($item) use ($tenants) {
                $item->tenant_name = $tenants->get($item->tenant_id) ?? 'N/A';
                return $item;
            });
        }
        
        // Eğer hiç veri yoksa, fallback olarak eski tablo ile deneme yap
        if ($results->isEmpty()) {
            $query = DB::table('ai_tenant_debug_logs')
                ->select('ai_tenant_debug_logs.*')
                ->orderBy('ai_tenant_debug_logs.created_at', 'desc')
                ->limit($limit);

            if ($tenantId) {
                $query->where('ai_tenant_debug_logs.tenant_id', $tenantId);
            }

            if ($feature) {
                $query->where('ai_tenant_debug_logs.feature_slug', $feature);
            }

            $results = $query->get();

            // Tenant bilgisini sonradan ekle
            if ($results->isNotEmpty()) {
                $tenantIds = $results->pluck('tenant_id')->unique()->filter();
                $tenants = \App\Models\Tenant::whereIn('id', $tenantIds)->pluck('title', 'id');

                $results->transform(function ($item) use ($tenants) {
                    $item->tenant_name = $tenants->get($item->tenant_id) ?? 'N/A';
                    return $item;
                });
            }
        }

        return $results;
    }

    private function getTopFeatures(?string $tenantId, string $dateRange): \Illuminate\Support\Collection
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        // YENİ SİSTEM: Gerçek AI kullanım verilerinden feature istatistikleri
        $query = DB::table('ai_credit_usage')
            ->select([
                'feature_slug', 
                DB::raw('COUNT(*) as usage_count'), 
                DB::raw('COALESCE(AVG(JSON_EXTRACT(metadata, "$.processing_time")), 0) as avg_time')
            ])
            ->where('used_at', '>=', $startDate)
            ->whereNotNull('feature_slug')
            ->where('feature_slug', '!=', '')
            ->groupBy('feature_slug')
            ->orderBy('usage_count', 'desc')
            ->limit(10);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $results = $query->get();
        
        // Eğer ai_credit_usage'da veri yoksa, fallback olarak eski tabloyu dene
        if ($results->isEmpty()) {
            $query = DB::table('ai_tenant_debug_logs')
                ->select('feature_slug', DB::raw('COUNT(*) as usage_count'), DB::raw('AVG(execution_time_ms) as avg_time'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('feature_slug')
                ->orderBy('usage_count', 'desc')
                ->limit(10);

            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }

            $results = $query->get();
        }
        
        // Veri yoksa boş collection döndür
        if ($results->isEmpty()) {
            return collect([]);
        }
        
        return $results;
    }

    private function getPromptUsageStats(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        // Real prompt usage data from ai_tenant_debug_logs
        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        // Get actual prompt usage from JSON data
        $totalRequests = $query->count();
        
        if ($totalRequests == 0) {
            return [
                'most_used_prompts' => [],
                'least_used_prompts' => []
            ];
        }

        // Get system prompts for reference
        $systemPrompts = \Modules\AI\App\Models\Prompt::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // Calculate usage stats from actual data
        $promptUsage = [];
        foreach ($systemPrompts as $promptId => $promptName) {
            $usageCount = $query->whereRaw("JSON_CONTAINS(prompts_analysis, JSON_OBJECT('prompt_name', ?))", [$promptName])->count();
            $usagePercentage = $totalRequests > 0 ? round(($usageCount / $totalRequests) * 100, 1) : 0;
            
            $promptUsage[] = [
                'name' => $promptName,
                'usage_count' => $usageCount,
                'usage_percentage' => $usagePercentage
            ];
        }

        // Sort by usage
        usort($promptUsage, fn($a, $b) => $b['usage_count'] <=> $a['usage_count']);

        return [
            'most_used_prompts' => array_slice($promptUsage, 0, 5),
            'least_used_prompts' => array_slice(array_reverse($promptUsage), 0, 3)
        ];
    }

    private function getTenantsWithUsage(): \Illuminate\Support\Collection
    {
        return DB::table('ai_tenant_debug_logs')
            ->select('tenant_id', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('tenant_id')
            ->orderBy('usage_count', 'desc')
            ->limit(50)
            ->get();
    }

    private function getPromptUsageStatistics(string $promptName): array
    {
        // Gerçek prompt usage statistics (veri yoksa 0 döndür)
        $recentUsage = DB::table('ai_credit_usage')
            ->where('conversation_id', 'like', '%' . $promptName . '%')
            ->count();
            
        return [
            'total_usage' => $recentUsage,
            'last_7_days' => DB::table('ai_credit_usage')
                ->where('conversation_id', 'like', '%' . $promptName . '%')
                ->where('used_at', '>=', Carbon::now()->subDays(7))
                ->count(),
            'success_rate' => 100.0, // Başarı oranı metadata'dan hesaplanabilir
            'avg_execution_time' => DB::table('ai_credit_usage')
                ->whereRaw('JSON_EXTRACT(metadata, "$.processing_time") IS NOT NULL')
                ->avg(DB::raw('JSON_EXTRACT(metadata, "$.processing_time")')) ?? 0
        ];
    }

    private function getTenantUsageTimeline(string $tenantId, string $dateRange): array
    {
        // Implementation for tenant timeline
        return [];
    }

    private function getTenantFeatureBreakdown(string $tenantId, string $dateRange): array
    {
        // Implementation for feature breakdown
        return [];
    }

    private function getTenantPromptEfficiency(string $tenantId, string $dateRange): array
    {
        // Implementation for prompt efficiency
        return [];
    }

    private function getTenantUserActivity(string $tenantId, string $dateRange): array
    {
        // Implementation for user activity
        return [];
    }

    private function getTenantPerformanceMetrics(string $tenantId, string $dateRange): array
    {
        // Implementation for performance metrics
        return [];
    }

    /**
     * Kullanılabilir AI provider'ları ve modellerini getir
     */
    private function getAvailableProviders(): array
    {
        try {
            // AI providers'dan provider bilgilerini al
            $aiProviders = \Modules\AI\App\Models\AIProvider::orderBy('priority')->get();
            if ($aiProviders->isEmpty()) {
                return $this->getDefaultProviders();
            }

            $providers = [];
            $activeProvider = 'deepseek'; // Default active provider

            foreach ($aiProviders as $provider) {
                if (!$provider->is_active) {
                    continue;
                }

                $models = [];
                // Safe JSON decode - check if it's already an array
                $availableModels = [];
                if ($provider->available_models) {
                    if (is_array($provider->available_models)) {
                        $availableModels = $provider->available_models;
                    } elseif (is_string($provider->available_models)) {
                        $availableModels = json_decode($provider->available_models, true) ?: [];
                    }
                }
                if (is_array($availableModels)) {
                    foreach ($availableModels as $modelKey => $modelData) {
                        $models[] = [
                            'key' => $modelKey,
                            'name' => $modelData['name'] ?? $modelKey,
                            'cost_per_1m_input' => $modelData['input_cost'] ?? 0,
                            'cost_per_1m_output' => $modelData['output_cost'] ?? 0,
                            'is_default' => ($provider->default_model ?? '') === $modelKey
                        ];
                    }
                }

                $providers[] = [
                    'key' => $provider->name,
                    'name' => $provider->display_name ?? ucfirst($provider->name),
                    'is_active' => $provider->is_default,
                    'default_model' => $provider->default_model ?? '',
                    'models' => $models,
                    'description' => $provider->description ?? '',
                    'average_response_time' => $provider->average_response_time ?? 0
                ];
            }

            // Aktif provider'ı en üste koy
            usort($providers, fn($a, $b) => $b['is_active'] <=> $a['is_active']);

            return $providers;

        } catch (\Exception $e) {
            \Log::error('getAvailableProviders error: ' . $e->getMessage());
            return $this->getDefaultProviders();
        }
    }

    /**
     * Varsayılan provider listesi (fallback)
     */
    private function getDefaultProviders(): array
    {
        return [
            [
                'key' => 'claude',
                'name' => 'Claude AI',
                'is_active' => true,
                'default_model' => 'claude-3-haiku-20240307',
                'models' => [
                    [
                        'key' => 'claude-3-haiku-20240307',
                        'name' => 'Claude 3 Haiku',
                        'cost_per_1m_input' => 0.25,
                        'cost_per_1m_output' => 1.25,
                        'is_default' => true
                    ]
                ],
                'description' => 'Anthropic Claude AI',
                'average_response_time' => 2500
            ],
            [
                'key' => 'deepseek',
                'name' => 'DeepSeek AI',
                'is_active' => false,
                'default_model' => 'deepseek-chat',
                'models' => [
                    [
                        'key' => 'deepseek-chat',
                        'name' => 'DeepSeek Chat',
                        'cost_per_1m_input' => 0.14,
                        'cost_per_1m_output' => 0.28,
                        'is_default' => true
                    ]
                ],
                'description' => 'DeepSeek AI Chat Model',
                'average_response_time' => 24000
            ]
        ];
    }


    /**
     * Performance Analytics Sayfası
     */
    public function performanceAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', '7');
        $tenantId = $request->get('tenant_id');
        
        // Performance metrics
        $performanceData = [
            'avg_execution_time' => $this->getAverageExecutionTime($tenantId, $dateRange),
            'prompt_efficiency' => $this->getPromptEfficiency($tenantId, $dateRange),
            'token_usage_trends' => $this->getTokenUsageTrends($tenantId, $dateRange),
            'error_rates' => $this->getErrorRates($tenantId, $dateRange),
            'peak_usage_hours' => $this->getPeakUsageHours($tenantId, $dateRange)
        ];
        
        $tenants = $this->getTenantsWithUsage();
        
        return view('ai::admin.debug-dashboard.performance', compact(
            'performanceData', 'tenants', 'tenantId', 'dateRange'
        ));
    }

    /**
     * Prompt Usage Heatmap
     */
    public function promptHeatmap(Request $request)
    {
        $dateRange = $request->get('date_range', '7');
        $tenantId = $request->get('tenant_id');
        
        // Heatmap data
        $heatmapData = [
            'hourly_usage' => $this->getHourlyUsageData($tenantId, (int) $dateRange),
            'prompt_popularity' => $this->getPromptPopularity($tenantId, $dateRange),
            'feature_heatmap' => $this->getFeatureHeatmap($tenantId, $dateRange),
            'geographic_usage' => $this->getGeographicUsage($tenantId, $dateRange)
        ];
        
        $tenants = $this->getTenantsWithUsage();
        
        return view('ai::admin.debug-dashboard.heatmap', compact(
            'heatmapData', 'tenants', 'tenantId', 'dateRange'
        ));
    }

    /**
     * Error Analysis
     */
    public function errorAnalysis(Request $request)
    {
        $dateRange = $request->get('date_range', '7');
        $tenantId = $request->get('tenant_id');
        
        // Error analysis data
        $errorData = [
            'error_summary' => $this->getErrorSummary($tenantId, $dateRange),
            'error_trends' => $this->getErrorTrends($tenantId, $dateRange),
            'top_error_messages' => $this->getTopErrorMessages($tenantId, $dateRange),
            'error_by_feature' => $this->getErrorsByFeature($tenantId, $dateRange),
            'recovery_rates' => $this->getRecoveryRates($tenantId, $dateRange)
        ];
        
        $tenants = $this->getTenantsWithUsage();
        
        return view('ai::admin.debug-dashboard.errors', compact(
            'errorData', 'tenants', 'tenantId', 'dateRange'
        ));
    }

    /**
     * Export Data
     */
    public function exportData(Request $request, string $type)
    {
        $dateRange = $request->get('date_range', '7');
        $tenantId = $request->get('tenant_id');
        
        switch ($type) {
            case 'csv':
                return $this->exportCsv($tenantId, $dateRange);
            case 'json':
                return $this->exportJson($tenantId, $dateRange);
            case 'excel':
                return $this->exportExcel($tenantId, $dateRange);
            default:
                abort(404, 'Export type not supported');
        }
    }

    // ============================================================================
    // HEATMAP DATA METHODS
    // ============================================================================

    private function getHeatmapData(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);
        $previousWeekStart = Carbon::now()->subDays($days * 2);
        $previousWeekEnd = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        // En popüler prompt'u bul
        $mostPopularPrompt = $this->getMostPopularPrompt($tenantId, $dateRange);
        
        // Yoğun saati bul
        $peakHour = $this->getPeakUsageHour($tenantId, $dateRange);
        
        // Trend hesapla
        $currentWeekUsage = $query->count();
        $previousWeekUsage = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $previousWeekStart)
            ->where('created_at', '<', $previousWeekEnd)
            ->when($tenantId, function($q) use ($tenantId) {
                return $q->where('tenant_id', $tenantId);
            })
            ->count();
            
        $trendChange = $previousWeekUsage > 0 
            ? round((($currentWeekUsage - $previousWeekUsage) / $previousWeekUsage) * 100, 1)
            : ($currentWeekUsage > 0 ? 100 : 0);

        // Isı skoru hesapla (0-100 arası)
        $totalRequests = $currentWeekUsage;
        $heatScore = min(100, round(($totalRequests / 100) * 10)); // Her 100 request = 10 derece
        $heatLevel = $heatScore > 80 ? 'Çok sıcak' : ($heatScore > 50 ? 'Sıcak' : ($heatScore > 20 ? 'Ilık' : 'Soğuk'));

        // Popüler prompt'ları getir
        $popularPrompts = $this->getPopularPromptsList($tenantId, $dateRange);
        
        // Saatlik kullanım
        $hourlyUsage = $this->getHourlyUsage($tenantId, $dateRange);
        
        // Feature heatmap
        $featureHeatmap = $this->getFeatureHeatmap($tenantId, $dateRange);

        return [
            'most_popular_prompt' => $mostPopularPrompt,
            'hourly_usage' => $hourlyUsage,
            'trend_change' => $trendChange,
            'heat_score' => $heatScore,
            'heat_level' => $heatLevel,
            'popular_prompts' => $popularPrompts,
            'feature_heatmap' => $featureHeatmap
        ];
    }

    private function getMostPopularPrompt(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $totalRequests = $query->count();
        
        if ($totalRequests == 0) {
            return [
                'name' => 'Veri yok',
                'usage_percentage' => 0
            ];
        }

        // En çok kullanılan sistem prompt'unu bul
        $systemPrompts = \Modules\AI\App\Models\Prompt::where('is_active', true)
            ->orderBy('name')
            ->get();

        $maxUsage = 0;
        $mostPopular = null;

        foreach ($systemPrompts as $prompt) {
            $usageCount = $query->whereRaw("JSON_CONTAINS(prompts_analysis, JSON_OBJECT('prompt_name', ?))", [$prompt->name])->count();
            if ($usageCount > $maxUsage) {
                $maxUsage = $usageCount;
                $mostPopular = [
                    'name' => $prompt->name,
                    'usage_percentage' => round(($usageCount / $totalRequests) * 100, 1)
                ];
            }
        }

        return $mostPopular ?: [
            'name' => 'Veri yok',
            'usage_percentage' => 0
        ];
    }

    private function getPopularPromptsList(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $totalRequests = $query->count();
        
        if ($totalRequests == 0) {
            return [];
        }

        $systemPrompts = \Modules\AI\App\Models\Prompt::where('is_active', true)
            ->orderBy('name')
            ->limit(5)
            ->get();

        $promptUsage = [];
        foreach ($systemPrompts as $prompt) {
            $usageCount = $query->whereRaw("JSON_CONTAINS(prompts_analysis, JSON_OBJECT('prompt_name', ?))", [$prompt->name])->count();
            $usagePercentage = round(($usageCount / $totalRequests) * 100, 1);
            
            $promptUsage[] = [
                'name' => $prompt->name,
                'category' => $prompt->prompt_category ?? 'system',
                'usage_count' => $usageCount,
                'usage_percentage' => $usagePercentage
            ];
        }

        usort($promptUsage, fn($a, $b) => $b['usage_count'] <=> $a['usage_count']);
        
        return array_slice($promptUsage, 0, 5);
    }

    private function getPeakUsageHour(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $hourlyStats = $query->select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as requests')
        )
        ->groupBy('hour')
        ->orderBy('requests', 'desc')
        ->first();

        return $hourlyStats ? [
            'hour' => $hourlyStats->hour,
            'requests' => $hourlyStats->requests
        ] : [
            'hour' => '--',
            'requests' => 0
        ];
    }

    private function getHourlyUsage(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        // Gerçek veri varsa gün×saat matrisi döndür, yoksa boş array
        $data = $query->select(
            DB::raw('DAYNAME(created_at) as day'),
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as requests')
        )
        ->groupBy('day', 'hour')
        ->get();

        if ($data->isEmpty()) {
            return []; // Veri yoksa boş array döndür
        }

        // Günleri organize et
        $weekdays = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        $result = [];
        
        foreach ($weekdays as $day) {
            $hours = array_fill(0, 24, 0);
            
            // Bu güne ait verileri doldur
            foreach ($data as $row) {
                if ($row->day === $day) {
                    $hours[$row->hour] = (int)$row->requests;
                }
            }
            
            $result[] = [
                'day' => $day,
                'hours' => $hours
            ];
        }

        return $result;
    }

    private function getFeatureHeatmap(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->select(
            'feature_slug',
            DB::raw('COUNT(*) as usage_count'),
            DB::raw('AVG(execution_time_ms) as avg_time')
        )
        ->groupBy('feature_slug')
        ->orderBy('usage_count', 'desc')
        ->get()
        ->toArray();
    }

    // ============================================================================
    // HELPER METHODS FOR NEW FEATURES
    // ============================================================================

    private function getAverageExecutionTime(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate)
            ->where('has_error', false);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return [
            'overall_avg' => $query->avg('execution_time_ms'),
            'by_feature' => $query->select('feature_slug', DB::raw('AVG(execution_time_ms) as avg_time'))
                ->groupBy('feature_slug')
                ->orderBy('avg_time', 'desc')
                ->get()
        ];
    }

    private function getPromptEfficiency(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return [
            'avg_prompts_used' => $query->avg('actually_used_prompts'),
            'efficiency_ratio' => $query->selectRaw('AVG(actually_used_prompts / total_available_prompts * 100) as ratio')->first()->ratio ?? 0,
            'by_context_type' => $query->select('context_type', DB::raw('AVG(actually_used_prompts) as avg_used'))
                ->groupBy('context_type')
                ->get()
        ];
    }

    private function getTokenUsageTrends(?string $tenantId, string $dateRange): array
    {
        // Token kullanım trendlerini gerçek veri ile hesapla
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $tokenQuery = DB::table('ai_credit_usage')
            ->where('used_at', '>=', $startDate);
            
        if ($tenantId) {
            $tokenQuery->where('tenant_id', $tenantId);
        }

        return [
            'daily_usage' => (clone $tokenQuery)->selectRaw('DATE(used_at) as date, SUM(input_tokens + output_tokens) as tokens')
                ->groupBy('date')
                ->orderBy('date')
                ->get()->toArray(),
            'peak_hours' => (clone $tokenQuery)->selectRaw('HOUR(used_at) as hour, SUM(input_tokens + output_tokens) as tokens')
                ->groupBy('hour')
                ->orderBy('tokens', 'desc')
                ->get()->toArray(),
            'cost_trends' => (clone $tokenQuery)->selectRaw('DATE(used_at) as date, SUM(credits_used) as cost')
                ->groupBy('date')
                ->orderBy('date')
                ->get()->toArray()
        ];
    }

    private function getErrorRates(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $totalRequests = $query->count();
        $errorRequests = $query->where('has_error', true)->count();

        return [
            'overall_error_rate' => $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0,
            'error_by_feature' => $query->select('feature_slug', DB::raw('COUNT(*) as total'), 
                DB::raw('SUM(CASE WHEN has_error THEN 1 ELSE 0 END) as errors'))
                ->groupBy('feature_slug')
                ->get()
        ];
    }

    private function getPeakUsageHours(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as requests')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('requests', 'desc')
            ->get()
            ->toArray();
    }

    private function getHourlyUsageData(?string $tenantId, int $days): array
    {
        $startDate = Carbon::now()->subDays($days);
        
        $query = DB::table('ai_tenant_debug_logs')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour');
            
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $results = $query->get();
        
        // 24 saat için array oluştur
        $hourlyData = array_fill(0, 24, 0);
        foreach ($results as $result) {
            $hourlyData[$result->hour] = $result->count;
        }
        
        // Veri yoksa boş array döndür (gerçek data yok demek)
        // Sadece gerçek kullanım verilerini döndür - sahte veri yok
        
        return $hourlyData;
    }

    private function getPromptPopularity(?string $tenantId, string $dateRange): array
    {
        $promptStats = $this->getPromptUsageStats($tenantId, $dateRange);
        
        // Calculate trending (comparing to previous period)
        $prevDays = (int) $dateRange;
        $currentStart = Carbon::now()->subDays($prevDays);
        $prevStart = Carbon::now()->subDays($prevDays * 2);
        $prevEnd = Carbon::now()->subDays($prevDays);
        
        $currentQuery = DB::table('ai_tenant_debug_logs')->where('created_at', '>=', $currentStart);
        $prevQuery = DB::table('ai_tenant_debug_logs')->whereBetween('created_at', [$prevStart, $prevEnd]);
        
        if ($tenantId) {
            $currentQuery->where('tenant_id', $tenantId);
            $prevQuery->where('tenant_id', $tenantId);
        }
        
        $currentTotal = $currentQuery->count();
        $prevTotal = $prevQuery->count();
        
        // Calculate trend percentage
        $trendPercentage = 0;
        if ($prevTotal > 0 && $currentTotal > 0) {
            $trendPercentage = round((($currentTotal - $prevTotal) / $prevTotal) * 100, 1);
        }
        
        // Most popular prompt
        $mostPopular = !empty($promptStats['most_used_prompts']) ? $promptStats['most_used_prompts'][0] : null;
        
        // Peak hour calculation
        $peakHour = $this->getPeakUsageHours($tenantId, $dateRange);
        $peakHourData = !empty($peakHour) ? $peakHour[0] : null;
        
        // Heat score calculation (based on usage intensity)
        $heatScore = $currentTotal > 0 ? min(100, round($currentTotal / 10)) : 0;
        
        return [
            'most_popular_prompt' => $mostPopular,
            'trend_percentage' => $trendPercentage,
            'peak_hour' => $peakHourData,
            'heat_score' => $heatScore,
            'most_used' => $promptStats['most_used_prompts'],
            'least_used' => $promptStats['least_used_prompts']
        ];
    }


    private function getGeographicUsage(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        // Group by IP address first 3 octets for geographic approximation
        return $query->selectRaw('SUBSTRING_INDEX(ip_address, ".", 3) as ip_range, COUNT(*) as requests')
            ->whereNotNull('ip_address')
            ->groupBy(DB::raw('SUBSTRING_INDEX(ip_address, ".", 3)'))
            ->orderBy('requests', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function getErrorSummary(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate)
            ->where('has_error', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return [
            'total_errors' => $query->count(),
            'unique_errors' => $query->distinct('error_message')->count('error_message'),
            'error_rate' => $this->getErrorRates($tenantId, $dateRange)['overall_error_rate']
        ];
    }

    private function getErrorTrends(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate)
            ->where('has_error', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as error_count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getTopErrorMessages(?string $tenantId, string $dateRange): array
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate)
            ->where('has_error', true)
            ->whereNotNull('error_message');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->select('error_message', DB::raw('COUNT(*) as count'))
            ->groupBy('error_message')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getErrorsByFeature(?string $tenantId, string $dateRange): array
    {
        return $this->getErrorRates($tenantId, $dateRange)['error_by_feature']->toArray();
    }

    private function getRecoveryRates(?string $tenantId, string $dateRange): array
    {
        // Gerçek recovery rate analizi (metadata'dan success/failure)
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_credit_usage')
            ->where('used_at', '>=', $startDate);
            
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $totalRequests = $query->count();
        $successfulRequests = $query->whereRaw('JSON_EXTRACT(metadata, "$.success") = true')->count();
        
        $successRate = $totalRequests > 0 ? ($successfulRequests / $totalRequests) * 100 : 100;
        
        return [
            'auto_recovery_rate' => $successRate,
            'manual_intervention_required' => 100 - $successRate
        ];
    }

    private function exportCsv(?string $tenantId, string $dateRange)
    {
        $data = $this->getExportData($tenantId, $dateRange);
        
        $filename = 'ai_debug_logs_' . ($tenantId ?? 'all') . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Timestamp', 'Tenant ID', 'Feature', 'Used Prompts', 
                'Total Prompts', 'Execution Time (ms)', 'Has Error'
            ]);
            
            // CSV data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->created_at,
                    $row->tenant_id,
                    $row->feature_slug,
                    $row->actually_used_prompts,
                    $row->total_available_prompts,
                    $row->execution_time_ms,
                    $row->has_error ? 'Yes' : 'No'
                ]);
            }
            
            fclose($file);
        }, $filename, $headers);
    }

    private function exportJson(?string $tenantId, string $dateRange)
    {
        $data = $this->getExportData($tenantId, $dateRange);
        
        $filename = 'ai_debug_logs_' . ($tenantId ?? 'all') . '_' . date('Y-m-d') . '.json';
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportExcel(?string $tenantId, string $dateRange)
    {
        // Implementation would require phpoffice/phpspreadsheet package
        abort(501, 'Excel export not implemented yet');
    }

    private function getExportData(?string $tenantId, string $dateRange)
    {
        $days = (int) $dateRange;
        $startDate = Carbon::now()->subDays($days);

        $query = DB::table('ai_tenant_debug_logs')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(10000); // Limit for performance

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get();
    }

    /**
     * Heatmap sayfası
     */
    public function heatmap(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $dateRange = $request->get('date_range', '7');

        // Heatmap data
        $heatmapData = $this->getHeatmapData($tenantId, $dateRange);

        return view('ai::admin.debug-dashboard.heatmap', compact('heatmapData', 'tenantId', 'dateRange'));
    }
}