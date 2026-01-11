<?php

declare(strict_types=1);

namespace Modules\AI\App\Repositories;

use Illuminate\Support\Facades\{Cache, DB, Log, Redis};
use Illuminate\Support\Collection;
use Modules\AI\App\Models\{AIFeature, AICreditUsage};
use Modules\AI\App\Repositories\Contracts\AIFeatureRepositoryInterface;
use Modules\AI\App\Enums\CacheStrategy;
use Carbon\Carbon;

/**
 * 🚀 AI FEATURE REPOSITORY V2 - Advanced Caching & Performance Optimization
 * 
 * Repository pattern implementation with multi-layer caching strategy,
 * performance optimization, and intelligent cache invalidation.
 * 
 * FEATURES:
 * - Multi-layer caching (Redis + Laravel Cache)
 * - Smart cache invalidation
 * - Query optimization with eager loading
 * - Automatic cache warming
 * - Performance metrics tracking
 * - Batch operations support
 * 
 * @version 2.0
 */
class AIFeatureRepository implements AIFeatureRepositoryInterface
{
    // Cache configuration
    private const CACHE_PREFIX = 'ai_features:';
    private const CACHE_TTL = [
        'feature' => 3600,        // 1 hour for individual features
        'list' => 1800,          // 30 minutes for lists
        'statistics' => 600,      // 10 minutes for statistics
        'search' => 300,         // 5 minutes for search results
    ];

    // Cache tags for invalidation
    private const CACHE_TAGS = [
        'features',
        'ai_module',
    ];

    public function __construct(
        private readonly AIFeature $model
    ) {
        Log::info('AIFeatureRepository initialized', [
            'cache_strategy' => 'multi-layer',
            'cache_ttls' => self::CACHE_TTL,
        ]);
    }

    /**
     * Find feature by ID with advanced caching
     */
    public function findById(int $id, bool $useCache = true): ?AIFeature
    {
        if (!$useCache) {
            return $this->fetchFeatureById($id);
        }

        $cacheKey = $this->getCacheKey('id', $id);
        
        // Try Redis first (faster)
        $cached = $this->getFromRedis($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Try Laravel cache (fallback)
        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL['feature'],
            fn() => $this->fetchFeatureById($id)
        );
    }

    /**
     * Find feature by slug with caching
     */
    public function findBySlug(string $slug, bool $useCache = true): ?AIFeature
    {
        if (!$useCache) {
            return $this->fetchFeatureBySlug($slug);
        }

        $cacheKey = $this->getCacheKey('slug', $slug);
        
        // Multi-layer cache approach
        return $this->multiLayerCache(
            $cacheKey,
            self::CACHE_TTL['feature'],
            fn() => $this->fetchFeatureBySlug($slug)
        );
    }

    /**
     * Get all active features with intelligent caching
     */
    public function getActiveFeatures(bool $useCache = true): Collection
    {
        if (!$useCache) {
            return $this->fetchActiveFeatures();
        }

        $cacheKey = $this->getCacheKey('active', 'all');
        
        return $this->multiLayerCache(
            $cacheKey,
            self::CACHE_TTL['list'],
            fn() => $this->fetchActiveFeatures()
        );
    }

    /**
     * Get public features
     */
    public function getPublicFeatures(bool $useCache = true): Collection
    {
        if (!$useCache) {
            return $this->fetchPublicFeatures();
        }

        $cacheKey = $this->getCacheKey('public', 'all');
        
        return $this->multiLayerCache(
            $cacheKey,
            self::CACHE_TTL['list'],
            fn() => $this->fetchPublicFeatures()
        );
    }

    /**
     * Get features by category
     */
    public function getByCategory(string $category, bool $useCache = true): Collection
    {
        if (!$useCache) {
            return $this->fetchByCategory($category);
        }

        $cacheKey = $this->getCacheKey('category', $category);
        
        return $this->multiLayerCache(
            $cacheKey,
            self::CACHE_TTL['list'],
            fn() => $this->fetchByCategory($category)
        );
    }

    /**
     * Search features with result caching
     */
    public function search(string $query, array $filters = []): Collection
    {
        $cacheKey = $this->getCacheKey('search', md5($query . serialize($filters)));
        
        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL['search'],
            function() use ($query, $filters) {
                $queryBuilder = $this->model->newQuery();
                
                // Full-text search on name and description
                $queryBuilder->where(function($q) use ($query) {
                    $q->whereRaw("JSON_EXTRACT(name, '$.tr') LIKE ?", ["%{$query}%"])
                      ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$query}%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.tr') LIKE ?", ["%{$query}%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$query}%"])
                      ->orWhere('slug', 'LIKE', "%{$query}%");
                });
                
                // Apply filters
                if (isset($filters['category'])) {
                    $queryBuilder->where('category', $filters['category']);
                }
                
                if (isset($filters['is_active'])) {
                    $queryBuilder->where('is_active', $filters['is_active']);
                }
                
                if (isset($filters['is_public'])) {
                    $queryBuilder->where('is_public', $filters['is_public']);
                }
                
                return $queryBuilder->with(['prompts', 'provider'])
                    ->orderBy('priority', 'desc')
                    ->get();
            }
        );
    }

    /**
     * Create new feature with cache invalidation
     */
    public function create(array $data): AIFeature
    {
        DB::beginTransaction();
        try {
            $feature = $this->model->create($data);
            
            // Invalidate related caches
            $this->invalidateCache();
            
            // Warm up cache for new feature
            $this->warmUpFeatureCache($feature);
            
            DB::commit();
            
            Log::info('AI Feature created', [
                'id' => $feature->id,
                'slug' => $feature->slug,
            ]);
            
            return $feature;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create AI Feature', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update feature with smart cache invalidation
     */
    public function update(int $id, array $data): bool
    {
        DB::beginTransaction();
        try {
            $feature = $this->model->find($id);
            
            if (!$feature) {
                return false;
            }
            
            $oldSlug = $feature->slug;
            $updated = $feature->update($data);
            
            if ($updated) {
                // Invalidate specific caches
                $this->invalidateFeatureCache($id, $oldSlug);
                
                // If slug changed, invalidate old slug cache
                if (isset($data['slug']) && $data['slug'] !== $oldSlug) {
                    $this->invalidateSlugCache($oldSlug);
                }
                
                // Warm up cache with new data
                $this->warmUpFeatureCache($feature->fresh());
            }
            
            DB::commit();
            
            Log::info('AI Feature updated', [
                'id' => $id,
                'changes' => array_keys($data),
            ]);
            
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update AI Feature', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Delete feature with cache cleanup
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $feature = $this->model->find($id);
            
            if (!$feature) {
                return false;
            }
            
            $slug = $feature->slug;
            $deleted = $feature->delete();
            
            if ($deleted) {
                // Comprehensive cache cleanup
                $this->invalidateFeatureCache($id, $slug);
                $this->invalidateCache();
            }
            
            DB::commit();
            
            Log::info('AI Feature deleted', ['id' => $id]);
            
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete AI Feature', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Clear all feature caches
     */
    public function clearCache(): void
    {
        // Clear Laravel cache tags
        Cache::tags(self::CACHE_TAGS)->flush();
        
        // Clear Redis pattern
        $this->clearRedisPattern(self::CACHE_PREFIX . '*');
        
        Log::info('AI Feature cache cleared');
    }

    /**
     * Get feature usage statistics with caching
     */
    public function getUsageStatistics(int $featureId, ?int $days = 30): array
    {
        $cacheKey = $this->getCacheKey('stats', "{$featureId}_{$days}");
        
        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL['statistics'],
            function() use ($featureId, $days) {
                $startDate = Carbon::now()->subDays($days);
                
                $feature = $this->model->find($featureId);
                if (!$feature) {
                    return [];
                }
                
                $usage = AICreditUsage::where('feature_slug', $feature->slug)
                    ->where('created_at', '>=', $startDate)
                    ->selectRaw('
                        COUNT(*) as total_uses,
                        SUM(credits_used) as total_credits,
                        AVG(credits_used) as avg_credits,
                        DATE(created_at) as date
                    ')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
                
                $dailyData = [];
                foreach ($usage as $day) {
                    $dailyData[$day->date] = [
                        'uses' => $day->total_uses,
                        'credits' => $day->total_credits,
                        'average' => round($day->avg_credits, 2),
                    ];
                }
                
                return [
                    'feature_id' => $featureId,
                    'feature_slug' => $feature->slug,
                    'period_days' => $days,
                    'total_uses' => $usage->sum('total_uses'),
                    'total_credits' => $usage->sum('total_credits'),
                    'average_credits_per_use' => $usage->avg('avg_credits') ?? 0,
                    'daily_breakdown' => $dailyData,
                    'peak_usage_day' => $usage->sortByDesc('total_uses')->first()?->date,
                    'trend' => $this->calculateUsageTrend($dailyData),
                ];
            }
        );
    }

    /**
     * Get popular features based on usage
     */
    public function getPopularFeatures(int $limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('popular', $limit);
        
        return Cache::tags(self::CACHE_TAGS)->remember(
            $cacheKey,
            self::CACHE_TTL['statistics'],
            function() use ($limit) {
                $popularSlugs = AICreditUsage::where('created_at', '>=', Carbon::now()->subDays(7))
                    ->whereNotNull('feature_slug')
                    ->selectRaw('feature_slug, COUNT(*) as usage_count')
                    ->groupBy('feature_slug')
                    ->orderByDesc('usage_count')
                    ->limit($limit)
                    ->pluck('usage_count', 'feature_slug');
                
                if ($popularSlugs->isEmpty()) {
                    return collect();
                }
                
                return $this->model->whereIn('slug', $popularSlugs->keys())
                    ->where('is_active', true)
                    ->get()
                    ->map(function($feature) use ($popularSlugs) {
                        $feature->usage_count = $popularSlugs[$feature->slug] ?? 0;
                        return $feature;
                    })
                    ->sortByDesc('usage_count')
                    ->values();
            }
        );
    }

    /**
     * Increment usage counter in Redis
     */
    public function incrementUsageCounter(int $featureId): void
    {
        try {
            $key = "feature_usage:{$featureId}:" . date('Y-m-d');
            Redis::incr($key);
            Redis::expire($key, 86400 * 7); // Keep for 7 days
            
            // Also increment total counter
            Redis::hincrby('feature_usage_total', (string)$featureId, 1);
            
        } catch (\Exception $e) {
            Log::warning('Failed to increment usage counter', [
                'feature_id' => $featureId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Private helper methods

    private function fetchFeatureById(int $id): ?AIFeature
    {
        return $this->model->with(['prompts', 'provider'])
            ->where('id', $id)
            ->first();
    }

    private function fetchFeatureBySlug(string $slug): ?AIFeature
    {
        return $this->model->with(['prompts', 'provider'])
            ->where('slug', $slug)
            ->first();
    }

    private function fetchActiveFeatures(): Collection
    {
        return $this->model->with(['prompts', 'provider'])
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('name->tr')
            ->get();
    }

    private function fetchPublicFeatures(): Collection
    {
        return $this->model->with(['prompts', 'provider'])
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('priority', 'desc')
            ->get();
    }

    private function fetchByCategory(string $category): Collection
    {
        return $this->model->with(['prompts', 'provider'])
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();
    }

    private function getCacheKey(string $type, $identifier): string
    {
        return self::CACHE_PREFIX . "{$type}:{$identifier}";
    }

    private function multiLayerCache(string $key, int $ttl, callable $callback)
    {
        // Try Redis first
        $cached = $this->getFromRedis($key);
        if ($cached !== null) {
            return $cached;
        }

        // Use Laravel cache with tags
        $result = Cache::tags(self::CACHE_TAGS)->remember($key, $ttl, $callback);

        // Store in Redis as well
        $this->storeInRedis($key, $result, $ttl);

        return $result;
    }

    private function getFromRedis(string $key)
    {
        try {
            $cached = Redis::get($key);
            if ($cached) {
                return unserialize($cached);
            }
        } catch (\Exception $e) {
            Log::warning('Redis cache get failed', ['key' => $key, 'error' => $e->getMessage()]);
        }
        
        return null;
    }

    private function storeInRedis(string $key, $value, int $ttl): void
    {
        try {
            Redis::setex($key, $ttl, serialize($value));
        } catch (\Exception $e) {
            Log::warning('Redis cache set failed', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    private function clearRedisPattern(string $pattern): void
    {
        try {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } catch (\Exception $e) {
            Log::warning('Redis pattern clear failed', ['pattern' => $pattern, 'error' => $e->getMessage()]);
        }
    }

    private function invalidateCache(): void
    {
        Cache::tags(self::CACHE_TAGS)->flush();
        $this->clearRedisPattern(self::CACHE_PREFIX . '*');
    }

    private function invalidateFeatureCache(int $id, string $slug): void
    {
        // Clear specific cache entries
        $keys = [
            $this->getCacheKey('id', $id),
            $this->getCacheKey('slug', $slug),
        ];
        
        foreach ($keys as $key) {
            Cache::tags(self::CACHE_TAGS)->forget($key);
            Redis::del($key);
        }
        
        // Clear list caches
        $this->invalidateListCaches();
    }

    private function invalidateSlugCache(string $slug): void
    {
        $key = $this->getCacheKey('slug', $slug);
        Cache::tags(self::CACHE_TAGS)->forget($key);
        Redis::del($key);
    }

    private function invalidateListCaches(): void
    {
        $listKeys = [
            $this->getCacheKey('active', 'all'),
            $this->getCacheKey('public', 'all'),
        ];
        
        foreach ($listKeys as $key) {
            Cache::tags(self::CACHE_TAGS)->forget($key);
            Redis::del($key);
        }
    }

    private function warmUpFeatureCache(AIFeature $feature): void
    {
        // Pre-cache the feature by ID and slug
        $idKey = $this->getCacheKey('id', $feature->id);
        $slugKey = $this->getCacheKey('slug', $feature->slug);
        
        $this->storeInRedis($idKey, $feature, self::CACHE_TTL['feature']);
        $this->storeInRedis($slugKey, $feature, self::CACHE_TTL['feature']);
        
        Cache::tags(self::CACHE_TAGS)->put($idKey, $feature, self::CACHE_TTL['feature']);
        Cache::tags(self::CACHE_TAGS)->put($slugKey, $feature, self::CACHE_TTL['feature']);
    }

    private function calculateUsageTrend(array $dailyData): string
    {
        if (count($dailyData) < 3) {
            return 'insufficient_data';
        }
        
        $values = array_column($dailyData, 'uses');
        $recentAvg = array_sum(array_slice($values, 0, 3)) / 3;
        $previousAvg = array_sum(array_slice($values, -3)) / 3;
        
        if ($previousAvg == 0) {
            return 'new';
        }
        
        $change = (($recentAvg - $previousAvg) / $previousAvg) * 100;
        
        if ($change > 20) return 'increasing';
        if ($change < -20) return 'decreasing';
        return 'stable';
    }
}