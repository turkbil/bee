<?php

declare(strict_types=1);

namespace Modules\Search\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchQuery extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'query',
        'searchable_type',
        'results_count',
        'filters_applied',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'locale',
        'referrer_url',
    ];

    protected $casts = [
        'filters_applied' => 'array',
        'results_count' => 'integer',
        'response_time_ms' => 'integer',
    ];

    /**
     * Get the user who performed the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all clicks for this search query
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(SearchClick::class);
    }

    /**
     * Scope: Get searches with no results
     */
    public function scopeNoResults($query)
    {
        return $query->where('results_count', 0);
    }

    /**
     * Scope: Get searches by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Get searches by locale
     */
    public function scopeByLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope: Get searches by searchable type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('searchable_type', $type);
    }

    /**
     * Get popular searches
     */
    public static function getPopularSearches(int $limit = 10, int $days = 30)
    {
        return static::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('results_count', '>', 0)
            ->selectRaw('query, COUNT(*) as search_count, SUM(results_count) as total_results')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get zero-result searches (for improvement suggestions)
     */
    public static function getZeroResultSearches(int $limit = 20, int $days = 30)
    {
        return static::query()
            ->noResults()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('query, COUNT(*) as attempt_count')
            ->groupBy('query')
            ->orderByDesc('attempt_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get average response time
     */
    public static function getAverageResponseTime(int $days = 30): float
    {
        return (float) static::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');
    }

    /**
     * Get click-through rate for this query
     */
    public function getClickThroughRate(): float
    {
        $totalSearches = static::where('query', $this->query)->count();
        $clickedSearches = static::where('query', $this->query)
            ->has('clicks')
            ->distinct()
            ->count();

        return $totalSearches > 0 ? ($clickedSearches / $totalSearches) * 100 : 0;
    }
}
