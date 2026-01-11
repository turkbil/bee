<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\AI\App\Services\Universal\UniversalInputManager;
use Modules\AI\App\Services\V3\PromptChainBuilder;
use Modules\AI\App\Services\Context\ContextAwareEngine;
use Modules\AI\App\Services\V3\SmartAnalyzer;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIPromptCache;
use Modules\AI\App\Models\AIUsageAnalytics;
use Carbon\Carbon;

/**
 * Enterprise-level cache warming job for AI system performance optimization
 * 
 * Features:
 * - Intelligent cache warming based on usage patterns
 * - Multi-layered caching strategy (prompts, features, analytics)
 * - Predictive cache warming using machine learning insights
 * - Real-time performance monitoring and optimization
 * - Context-aware cache warming for different user types
 * - Smart cache invalidation and refresh cycles
 * - Memory-efficient batch processing
 * - Advanced error handling and recovery mechanisms
 * 
 * @package Modules\AI\app\Jobs
 * @version 3.0.0
 */
final class CacheWarmup implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1800; // 30 minutes
    public int $tries = 3;
    public int $maxExceptions = 5;

    private array $cacheStats = [];
    private array $performanceMetrics = [];
    private int $processedItems = 0;
    private int $totalItems = 0;
    private Carbon $startTime;

    public function __construct(
        private readonly array $warmupConfig = [],
        private readonly bool $forceRefresh = false,
        private readonly array $specificModules = [],
        private readonly string $priority = 'normal'
    ) {
        $this->onQueue($this->priority === 'high' ? 'high-priority' : 'default');
    }

    /**
     * Execute the cache warming job
     */
    public function handle(
        UniversalInputManager $inputManager,
        PromptChainBuilder $promptBuilder,
        ContextAwareEngine $contextEngine,
        SmartAnalyzer $analyzer
    ): void {
        $this->startTime = now();
        $jobId = Str::uuid();
        
        Log::info('AI Cache Warmup started', [
            'job_id' => $jobId,
            'config' => $this->warmupConfig,
            'force_refresh' => $this->forceRefresh,
            'modules' => $this->specificModules,
            'priority' => $this->priority
        ]);

        try {
            // Phase 1: Analyze current cache state
            $this->analyzeCacheState();
            
            // Phase 2: Warm core system caches
            $this->warmCoreCaches($inputManager, $promptBuilder);
            
            // Phase 3: Warm feature-specific caches
            $this->warmFeatureCaches($contextEngine);
            
            // Phase 4: Warm user context caches
            $this->warmContextCaches($contextEngine);
            
            // Phase 5: Warm analytics caches
            $this->warmAnalyticsCaches($analyzer);
            
            // Phase 6: Predictive cache warming
            $this->warmPredictiveCaches($analyzer);
            
            // Phase 7: Optimize cache performance
            $this->optimizeCachePerformance();
            
            // Phase 8: Generate warming report
            $this->generateWarmupReport($jobId);
            
            Log::info('AI Cache Warmup completed successfully', [
                'job_id' => $jobId,
                'processed_items' => $this->processedItems,
                'duration' => $this->startTime->diffInSeconds(),
                'cache_stats' => $this->cacheStats,
                'performance_metrics' => $this->performanceMetrics
            ]);

        } catch (\Throwable $e) {
            Log::error('AI Cache Warmup failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processed_items' => $this->processedItems,
                'total_items' => $this->totalItems
            ]);
            
            $this->fail($e);
        }
    }

    /**
     * Analyze current cache state and performance
     */
    private function analyzeCacheState(): void
    {
        Log::info('Analyzing cache state');
        
        // Check cache hit rates
        $this->cacheStats['hit_rate'] = $this->calculateCacheHitRate();
        
        // Check cache sizes
        $this->cacheStats['cache_sizes'] = $this->calculateCacheSizes();
        
        // Check expired cache entries
        $this->cacheStats['expired_entries'] = $this->findExpiredCacheEntries();
        
        // Check memory usage
        $this->cacheStats['memory_usage'] = $this->calculateMemoryUsage();
        
        Log::info('Cache state analysis completed', $this->cacheStats);
    }

    /**
     * Warm core system caches (prompts, features, templates)
     */
    private function warmCoreCaches(
        UniversalInputManager $inputManager,
        PromptChainBuilder $promptBuilder
    ): void {
        Log::info('Warming core system caches');
        
        // Warm AI features cache
        $this->warmAIFeaturesCache();
        
        // Warm prompts cache
        $this->warmPromptsCache($promptBuilder);
        
        // Warm form configurations cache
        $this->warmFormConfigsCache($inputManager);
        
        // Warm template cache
        $this->warmTemplateCache();
        
        // Warm module integrations cache
        $this->warmModuleIntegrationsCache();
        
        $this->processedItems += 5;
        Log::info('Core caches warmed successfully');
    }

    /**
     * Warm module integrations cache
     */
    private function warmModuleIntegrationsCache(): void
    {
        $cacheKey = 'module_integrations_all';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $integrations = DB::table('ai_module_integrations')
                ->where('is_active', true)
                ->get();
            
            Cache::put($cacheKey, $integrations, now()->addHours(12));
            $this->cacheStats['integrations_cached'] = $integrations->count();
        }
    }

    /**
     * Warm AI features cache
     */
    private function warmAIFeaturesCache(): void
    {
        $cacheKey = 'ai_features_all';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $features = AIFeature::with(['prompts', 'seoSettings'])
                ->where('is_active', true)
                ->get()
                ->keyBy('id');
            
            Cache::put($cacheKey, $features, now()->addHours(6));
            $this->cacheStats['features_cached'] = $features->count();
        }

        // Warm individual feature caches
        $features = Cache::get($cacheKey, collect());
        foreach ($features as $feature) {
            $featureCacheKey = "ai_feature_{$feature->id}";
            if ($this->forceRefresh || !Cache::has($featureCacheKey)) {
                Cache::put($featureCacheKey, $feature, now()->addHours(4));
            }
        }
    }

    /**
     * Warm prompts cache with optimization
     */
    private function warmPromptsCache(PromptChainBuilder $promptBuilder): void
    {
        $cacheKey = 'ai_prompts_optimized';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $prompts = Prompt::where('is_active', true)
                ->orderBy('priority', 'desc')
                ->get();
            
            $optimizedPrompts = [];
            foreach ($prompts as $prompt) {
                try {
                    // Pre-optimize prompt chains
                    $optimizedChain = $promptBuilder->optimizePromptChain($prompt->toArray());
                    $optimizedPrompts[$prompt->id] = [
                        'original' => $prompt,
                        'optimized' => $optimizedChain,
                        'metadata' => [
                            'optimization_score' => $this->calculateOptimizationScore($prompt, $optimizedChain),
                            'cached_at' => now(),
                        ]
                    ];
                } catch (\Exception $e) {
                    Log::warning("Failed to optimize prompt {$prompt->id}", ['error' => $e->getMessage()]);
                    $optimizedPrompts[$prompt->id] = ['original' => $prompt, 'optimized' => null];
                }
            }
            
            Cache::put($cacheKey, $optimizedPrompts, now()->addHours(8));
            $this->cacheStats['prompts_cached'] = count($optimizedPrompts);
        }
    }

    /**
     * Warm form configurations cache
     */
    private function warmFormConfigsCache(UniversalInputManager $inputManager): void
    {
        $moduleConfigs = $this->specificModules ?: $this->getActiveModules();
        
        foreach ($moduleConfigs as $module) {
            $cacheKey = "form_config_{$module}";
            
            if ($this->forceRefresh || !Cache::has($cacheKey)) {
                try {
                    $config = $inputManager->getModuleFormStructure($module);
                    Cache::put($cacheKey, $config, now()->addHours(12));
                } catch (\Exception $e) {
                    Log::warning("Failed to cache form config for {$module}", ['error' => $e->getMessage()]);
                }
            }
        }
        
        $this->cacheStats['form_configs_cached'] = count($moduleConfigs);
    }

    /**
     * Warm template cache
     */
    private function warmTemplateCache(): void
    {
        $templates = DB::table('ai_prompt_templates')
            ->where('is_active', true)
            ->get();
        
        foreach ($templates as $template) {
            $cacheKey = "prompt_template_{$template->id}";
            
            if ($this->forceRefresh || !Cache::has($cacheKey)) {
                $processedTemplate = $this->processTemplate($template);
                Cache::put($cacheKey, $processedTemplate, now()->addHours(6));
            }
        }
        
        $this->cacheStats['templates_cached'] = $templates->count();
    }

    /**
     * Warm feature-specific caches based on usage patterns
     */
    private function warmFeatureCaches(ContextAwareEngine $contextEngine): void
    {
        Log::info('Warming feature-specific caches');
        
        // Get most used features from analytics
        $popularFeatures = $this->getPopularFeatures();
        
        foreach ($popularFeatures as $feature) {
            // Warm feature context cache
            $this->warmFeatureContextCache($feature, $contextEngine);
            
            // Warm feature prompt cache
            $this->warmFeaturePromptCache($feature);
            
            // Warm feature response templates
            $this->warmFeatureResponseTemplates($feature);
        }
        
        $this->processedItems += count($popularFeatures);
        $this->cacheStats['feature_caches_warmed'] = count($popularFeatures);
    }

    /**
     * Warm user context caches
     */
    private function warmContextCaches(ContextAwareEngine $contextEngine): void
    {
        Log::info('Warming context caches');
        
        $contextTypes = ['user', 'module', 'tenant', 'time'];
        
        foreach ($contextTypes as $contextType) {
            try {
                $contexts = $this->getCommonContexts($contextType);
                
                foreach ($contexts as $context) {
                    $cacheKey = "context_{$contextType}_{$context['id']}";
                    
                    if ($this->forceRefresh || !Cache::has($cacheKey)) {
                        $processedContext = $contextEngine->processContext($context);
                        Cache::put($cacheKey, $processedContext, now()->addHours(4));
                    }
                }
                
                $this->cacheStats["context_{$contextType}_cached"] = count($contexts);
                
            } catch (\Exception $e) {
                Log::warning("Failed to warm {$contextType} context cache", ['error' => $e->getMessage()]);
            }
        }
        
        $this->processedItems += count($contextTypes);
    }

    /**
     * Warm analytics caches for dashboard performance
     */
    private function warmAnalyticsCaches(SmartAnalyzer $analyzer): void
    {
        Log::info('Warming analytics caches');
        
        try {
            // Warm usage statistics
            $this->warmUsageStatistics($analyzer);
            
            // Warm performance metrics
            $this->warmPerformanceMetrics($analyzer);
            
            // Warm user behavior patterns
            $this->warmUserBehaviorCache($analyzer);
            
            // Warm system health metrics
            $this->warmSystemHealthCache($analyzer);
            
            $this->processedItems += 4;
            $this->cacheStats['analytics_caches_warmed'] = 4;
            
        } catch (\Exception $e) {
            Log::warning('Failed to warm analytics caches', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Warm predictive caches using ML insights
     */
    private function warmPredictiveCaches(SmartAnalyzer $analyzer): void
    {
        Log::info('Warming predictive caches');
        
        try {
            // Predict popular features for next hour
            $predictions = $analyzer->predictPopularFeatures(now()->addHour());
            
            foreach ($predictions as $prediction) {
                $this->preWarmFeatureCache($prediction['feature_id'], $prediction['confidence']);
            }
            
            // Predict resource usage patterns
            $resourcePredictions = $analyzer->predictResourceUsage();
            $this->optimizeCacheBasedOnPredictions($resourcePredictions);
            
            $this->cacheStats['predictive_caches_warmed'] = count($predictions);
            $this->processedItems += count($predictions);
            
        } catch (\Exception $e) {
            Log::warning('Failed to warm predictive caches', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Optimize cache performance based on current metrics
     */
    private function optimizeCachePerformance(): void
    {
        Log::info('Optimizing cache performance');
        
        // Clean expired entries
        $cleanedEntries = $this->cleanExpiredCacheEntries();
        
        // Defragment cache storage
        $this->defragmentCacheStorage();
        
        // Adjust TTL based on usage patterns
        $this->adjustCacheTTLs();
        
        // Update cache tags for better invalidation
        $this->updateCacheTags();
        
        $this->performanceMetrics['cache_optimization'] = [
            'cleaned_entries' => $cleanedEntries,
            'memory_freed' => $this->calculateMemoryFreed(),
            'optimization_time' => now()->diffInMilliseconds($this->startTime)
        ];
        
        $this->processedItems += 1;
    }

    /**
     * Generate comprehensive warmup report
     */
    private function generateWarmupReport(string $jobId): void
    {
        $report = [
            'job_id' => $jobId,
            'execution_time' => $this->startTime->diffInSeconds(),
            'processed_items' => $this->processedItems,
            'cache_stats' => $this->cacheStats,
            'performance_metrics' => $this->performanceMetrics,
            'memory_usage' => [
                'peak' => memory_get_peak_usage(true),
                'current' => memory_get_usage(true)
            ],
            'recommendations' => $this->generateRecommendations(),
            'next_warmup_scheduled' => now()->addHours(6)->toISOString()
        ];

        // Store report in database
        AIPromptCache::create([
            'cache_key' => "warmup_report_{$jobId}",
            'cache_data' => json_encode($report),
            'expires_at' => now()->addDays(7),
            'cache_tags' => json_encode(['warmup', 'reports']),
            'hit_count' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Cache report for quick access
        Cache::put("warmup_report_latest", $report, now()->addHours(24));
        
        Log::info('Cache warmup report generated', $report);
    }

    /**
     * Calculate cache hit rate based on recent analytics
     */
    private function calculateCacheHitRate(): float
    {
        $recentAnalytics = AIUsageAnalytics::where('created_at', '>=', now()->subHour())
            ->selectRaw('
                SUM(CASE WHEN response_metadata->>"$.cache_hit" = "true" THEN 1 ELSE 0 END) as hits,
                COUNT(*) as total
            ')
            ->first();

        if (!$recentAnalytics || $recentAnalytics->total == 0) {
            return 0.0;
        }

        return round(($recentAnalytics->hits / $recentAnalytics->total) * 100, 2);
    }

    /**
     * Calculate current cache sizes
     */
    private function calculateCacheSizes(): array
    {
        return [
            'features' => Cache::has('ai_features_all') ? strlen(serialize(Cache::get('ai_features_all'))) : 0,
            'prompts' => Cache::has('ai_prompts_optimized') ? strlen(serialize(Cache::get('ai_prompts_optimized'))) : 0,
            'templates' => $this->calculateTemplateCacheSize(),
            'contexts' => $this->calculateContextCacheSize()
        ];
    }

    /**
     * Find expired cache entries that need cleanup
     */
    private function findExpiredCacheEntries(): int
    {
        return AIPromptCache::where('expires_at', '<', now())->count();
    }

    /**
     * Calculate current memory usage
     */
    private function calculateMemoryUsage(): array
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * Get most popular features based on usage analytics
     */
    private function getPopularFeatures(int $limit = 20): array
    {
        return AIUsageAnalytics::select('feature_id', DB::raw('COUNT(*) as usage_count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('feature_id')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get active modules for cache warming
     */
    private function getActiveModules(): array
    {
        return DB::table('ai_module_integrations')
            ->where('is_active', true)
            ->pluck('module_name')
            ->toArray();
    }

    /**
     * Calculate optimization score for a prompt
     */
    private function calculateOptimizationScore(object $original, ?array $optimized): float
    {
        if (!$optimized) return 0.0;
        
        $originalLength = strlen($original->prompt_text ?? '');
        $optimizedLength = strlen($optimized['optimized_text'] ?? '');
        
        if ($originalLength == 0) return 0.0;
        
        return round((1 - ($optimizedLength / $originalLength)) * 100, 2);
    }

    /**
     * Process template for caching
     */
    private function processTemplate(object $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'template_content' => $template->template_content,
            'variables' => json_decode($template->variables ?? '[]', true),
            'compiled_template' => $this->compileTemplate($template),
            'metadata' => [
                'processed_at' => now(),
                'version' => '3.0.0'
            ]
        ];
    }

    /**
     * Compile template for faster execution
     */
    private function compileTemplate(object $template): string
    {
        // Simple template compilation - replace variables with placeholders
        $compiled = $template->template_content ?? '';
        $variables = json_decode($template->variables ?? '[]', true);
        
        foreach ($variables as $variable) {
            $compiled = str_replace("{{{$variable}}}", "{{VAR_{$variable}}}", $compiled);
        }
        
        return $compiled;
    }

    /**
     * Warm feature context cache
     */
    private function warmFeatureContextCache(array $feature, ContextAwareEngine $contextEngine): void
    {
        $cacheKey = "feature_context_{$feature['feature_id']}";
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            try {
                $context = $contextEngine->buildFeatureContext($feature['feature_id']);
                Cache::put($cacheKey, $context, now()->addHours(3));
            } catch (\Exception $e) {
                Log::warning("Failed to warm context for feature {$feature['feature_id']}", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Warm feature prompt cache
     */
    private function warmFeaturePromptCache(array $feature): void
    {
        $cacheKey = "feature_prompts_{$feature['feature_id']}";
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $prompts = DB::table('ai_feature_prompt_relations')
                ->join('ai_prompts', 'ai_prompts.id', '=', 'ai_feature_prompt_relations.prompt_id')
                ->where('ai_feature_prompt_relations.feature_id', $feature['feature_id'])
                ->where('ai_prompts.is_active', true)
                ->select('ai_prompts.*')
                ->orderBy('ai_feature_prompt_relations.priority')
                ->get();
            
            Cache::put($cacheKey, $prompts, now()->addHours(4));
        }
    }

    /**
     * Warm feature response templates
     */
    private function warmFeatureResponseTemplates(array $feature): void
    {
        $cacheKey = "feature_templates_{$feature['feature_id']}";
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $templates = DB::table('ai_prompt_templates')
                ->where('feature_id', $feature['feature_id'])
                ->where('is_active', true)
                ->get();
            
            Cache::put($cacheKey, $templates, now()->addHours(6));
        }
    }

    /**
     * Get common contexts for each type
     */
    private function getCommonContexts(string $contextType): array
    {
        return match($contextType) {
            'user' => $this->getCommonUserContexts(),
            'module' => $this->getCommonModuleContexts(),
            'tenant' => $this->getCommonTenantContexts(),
            'time' => $this->getCommonTimeContexts(),
            default => []
        };
    }

    /**
     * Get common user contexts
     */
    private function getCommonUserContexts(): array
    {
        return AIUsageAnalytics::select('user_id', DB::raw('COUNT(*) as usage_count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('usage_count')
            ->limit(50)
            ->get()
            ->map(fn($item) => ['id' => $item->user_id, 'type' => 'user'])
            ->toArray();
    }

    /**
     * Get common module contexts
     */
    private function getCommonModuleContexts(): array
    {
        return $this->getActiveModules();
    }

    /**
     * Get common tenant contexts
     */
    private function getCommonTenantContexts(): array
    {
        // In multi-tenant system, get active tenants
        return [['id' => 'current', 'type' => 'tenant']];
    }

    /**
     * Get common time contexts
     */
    private function getCommonTimeContexts(): array
    {
        return [
            ['id' => 'morning', 'type' => 'time'],
            ['id' => 'afternoon', 'type' => 'time'],
            ['id' => 'evening', 'type' => 'time'],
            ['id' => 'weekday', 'type' => 'time'],
            ['id' => 'weekend', 'type' => 'time']
        ];
    }

    /**
     * Warm usage statistics cache
     */
    private function warmUsageStatistics(SmartAnalyzer $analyzer): void
    {
        $cacheKey = 'usage_statistics_dashboard';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $stats = $analyzer->generateUsageStatistics();
            Cache::put($cacheKey, $stats, now()->addMinutes(30));
        }
    }

    /**
     * Warm performance metrics cache
     */
    private function warmPerformanceMetrics(SmartAnalyzer $analyzer): void
    {
        $cacheKey = 'performance_metrics_dashboard';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $metrics = $analyzer->generatePerformanceMetrics();
            Cache::put($cacheKey, $metrics, now()->addMinutes(15));
        }
    }

    /**
     * Warm user behavior cache
     */
    private function warmUserBehaviorCache(SmartAnalyzer $analyzer): void
    {
        $cacheKey = 'user_behavior_patterns';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $patterns = $analyzer->analyzeUserBehaviorPatterns();
            Cache::put($cacheKey, $patterns, now()->addHours(2));
        }
    }

    /**
     * Warm system health cache
     */
    private function warmSystemHealthCache(SmartAnalyzer $analyzer): void
    {
        $cacheKey = 'system_health_metrics';
        
        if ($this->forceRefresh || !Cache::has($cacheKey)) {
            $health = $analyzer->generateSystemHealthReport();
            Cache::put($cacheKey, $health, now()->addMinutes(10));
        }
    }

    /**
     * Pre-warm feature cache based on predictions
     */
    private function preWarmFeatureCache(int $featureId, float $confidence): void
    {
        if ($confidence < 0.7) return; // Only warm high-confidence predictions
        
        try {
            $feature = AIFeature::with(['prompts'])->find($featureId);
            if (!$feature) return;
            
            $cacheKey = "predicted_feature_{$featureId}";
            Cache::put($cacheKey, $feature, now()->addHour());
            
        } catch (\Exception $e) {
            Log::warning("Failed to pre-warm feature {$featureId}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Optimize cache based on resource predictions
     */
    private function optimizeCacheBasedOnPredictions(array $resourcePredictions): void
    {
        foreach ($resourcePredictions as $prediction) {
            if ($prediction['resource_type'] === 'memory' && $prediction['predicted_usage'] > 0.8) {
                // High memory usage predicted - reduce cache TTLs
                $this->reduceCacheTTLs();
            } elseif ($prediction['resource_type'] === 'cpu' && $prediction['predicted_usage'] > 0.9) {
                // High CPU usage predicted - prioritize read caches
                $this->prioritizeReadCaches();
            }
        }
    }

    /**
     * Clean expired cache entries
     */
    private function cleanExpiredCacheEntries(): int
    {
        return AIPromptCache::where('expires_at', '<', now())->delete();
    }

    /**
     * Defragment cache storage (Redis-specific optimizations)
     */
    private function defragmentCacheStorage(): void
    {
        // This would be Redis-specific optimization
        // For now, just flush unused tags
        Cache::tags(['expired', 'unused'])->flush();
    }

    /**
     * Adjust cache TTLs based on usage patterns
     */
    private function adjustCacheTTLs(): void
    {
        $highUsageItems = $this->getHighUsageCacheItems();
        
        foreach ($highUsageItems as $item) {
            // Extend TTL for frequently used items
            if (Cache::has($item['key'])) {
                $data = Cache::get($item['key']);
                Cache::put($item['key'], $data, now()->addHours(12));
            }
        }
    }

    /**
     * Update cache tags for better invalidation
     */
    private function updateCacheTags(): void
    {
        // Tag popular features for quick invalidation
        $popularFeatures = $this->getPopularFeatures(10);
        
        foreach ($popularFeatures as $feature) {
            $cacheKey = "ai_feature_{$feature['feature_id']}";
            if (Cache::has($cacheKey)) {
                $data = Cache::get($cacheKey);
                Cache::tags(['popular', 'features', "feature_{$feature['feature_id']}"])
                    ->put($cacheKey, $data, now()->addHours(6));
            }
        }
    }

    /**
     * Calculate memory freed during optimization
     */
    private function calculateMemoryFreed(): int
    {
        $currentMemory = memory_get_usage(true);
        return max(0, ($this->cacheStats['memory_usage']['current'] ?? $currentMemory) - $currentMemory);
    }

    /**
     * Generate optimization recommendations
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        if (($this->cacheStats['hit_rate'] ?? 0) < 70) {
            $recommendations[] = 'Cache hit rate is low. Consider pre-warming more frequently used items.';
        }
        
        if (($this->cacheStats['expired_entries'] ?? 0) > 100) {
            $recommendations[] = 'High number of expired entries. Consider running cleanup more frequently.';
        }
        
        $memoryUsage = $this->cacheStats['memory_usage']['current'] ?? 0;
        if ($memoryUsage > (1024 * 1024 * 512)) { // 512MB
            $recommendations[] = 'High memory usage detected. Consider reducing cache TTLs or implementing cache size limits.';
        }
        
        return $recommendations;
    }

    /**
     * Calculate template cache size
     */
    private function calculateTemplateCacheSize(): int
    {
        $size = 0;
        $templates = DB::table('ai_prompt_templates')->where('is_active', true)->get();
        
        foreach ($templates as $template) {
            $cacheKey = "prompt_template_{$template->id}";
            if (Cache::has($cacheKey)) {
                $size += strlen(serialize(Cache::get($cacheKey)));
            }
        }
        
        return $size;
    }

    /**
     * Calculate context cache size
     */
    private function calculateContextCacheSize(): int
    {
        $size = 0;
        $contextTypes = ['user', 'module', 'tenant', 'time'];
        
        foreach ($contextTypes as $type) {
            $contexts = $this->getCommonContexts($type);
            foreach ($contexts as $context) {
                $cacheKey = "context_{$type}_{$context['id']}";
                if (Cache::has($cacheKey)) {
                    $size += strlen(serialize(Cache::get($cacheKey)));
                }
            }
        }
        
        return $size;
    }

    /**
     * Get high usage cache items for TTL optimization
     */
    private function getHighUsageCacheItems(): array
    {
        return AIPromptCache::where('hit_count', '>', 100)
            ->where('created_at', '>=', now()->subDay())
            ->select('cache_key as key', 'hit_count')
            ->orderByDesc('hit_count')
            ->limit(20)
            ->get()
            ->toArray();
    }

    /**
     * Reduce cache TTLs during high memory usage
     */
    private function reduceCacheTTLs(): void
    {
        $keys = ['ai_features_all', 'ai_prompts_optimized', 'usage_statistics_dashboard'];
        
        foreach ($keys as $key) {
            if (Cache::has($key)) {
                $data = Cache::get($key);
                Cache::put($key, $data, now()->addMinutes(30)); // Reduce to 30 minutes
            }
        }
    }

    /**
     * Prioritize read caches during high CPU usage
     */
    private function prioritizeReadCaches(): void
    {
        // Move write-heavy caches to longer TTLs and prioritize read-only caches
        $readOnlyKeys = ['ai_features_all', 'ai_prompts_optimized'];
        
        foreach ($readOnlyKeys as $key) {
            if (Cache::has($key)) {
                $data = Cache::get($key);
                Cache::put($key, $data, now()->addHours(8)); // Extend read-only caches
            }
        }
    }

    /**
     * Get unique ID for job deduplication
     */
    public function uniqueId(): string
    {
        return 'cache_warmup_' . md5(serialize($this->warmupConfig) . $this->priority);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Cache Warmup job failed completely', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'config' => $this->warmupConfig,
            'processed_items' => $this->processedItems,
            'total_items' => $this->totalItems
        ]);

        // Notify system administrators about cache warming failure
        // This could trigger alerts or fallback cache strategies
    }
}