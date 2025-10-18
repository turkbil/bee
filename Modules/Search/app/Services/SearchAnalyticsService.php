<?php

declare(strict_types=1);

namespace Modules\Search\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Search\App\Models\SearchQuery;
use Modules\Search\App\Models\SearchClick;

class SearchAnalyticsService
{
    /**
     * Get dashboard overview statistics
     */
    public function getDashboardStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_searches' => $this->getTotalSearches($days),
            'unique_queries' => $this->getUniqueQueries($days),
            'avg_response_time' => $this->getAverageResponseTime($days),
            'zero_result_count' => $this->getZeroResultCount($days),
            'total_clicks' => $this->getTotalClicks($days),
            'avg_ctr' => $this->getAverageClickThroughRate($days),
            'searches_with_clicks' => $this->getSearchesWithClicks($days),
        ];
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $days = 30, int $limit = 10): Collection
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('results_count', '>', 0)
            ->selectRaw('query, COUNT(*) as search_count, AVG(results_count) as avg_results')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get zero-result searches
     */
    public function getZeroResultSearches(int $days = 30, int $limit = 20): Collection
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('results_count', 0)
            ->selectRaw('query, COUNT(*) as attempt_count')
            ->groupBy('query')
            ->orderByDesc('attempt_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get search trends (daily)
     */
    public function getSearchTrends(int $days = 30): Collection
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get module distribution
     */
    public function getModuleDistribution(int $days = 30): Collection
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('searchable_type')
            ->selectRaw('searchable_type, COUNT(*) as count')
            ->groupBy('searchable_type')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(int $days = 30): array
    {
        $queries = SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('response_time_ms')
            ->get();

        if ($queries->isEmpty()) {
            return [
                'avg' => 0,
                'min' => 0,
                'max' => 0,
                'median' => 0,
            ];
        }

        $responseTimes = $queries->pluck('response_time_ms')->sort()->values();

        return [
            'avg' => round($responseTimes->avg(), 2),
            'min' => $responseTimes->min(),
            'max' => $responseTimes->max(),
            'median' => $responseTimes->median(),
        ];
    }

    /**
     * Get click position analytics
     */
    public function getClickPositionAnalytics(int $days = 30): Collection
    {
        return SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('click_position, COUNT(*) as clicks')
            ->groupBy('click_position')
            ->orderBy('click_position')
            ->get();
    }

    /**
     * Get new tab statistics
     */
    public function getNewTabStatistics(int $days = 30): array
    {
        $total = SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        if ($total === 0) {
            return [
                'new_tab' => 0,
                'same_tab' => 0,
                'new_tab_percentage' => 0,
            ];
        }

        $newTab = SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('opened_in_new_tab', true)
            ->count();

        return [
            'new_tab' => $newTab,
            'same_tab' => $total - $newTab,
            'new_tab_percentage' => round(($newTab / $total) * 100, 2),
        ];
    }

    /**
     * Get locale distribution
     */
    public function getLocaleDistribution(int $days = 30): Collection
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('locale, COUNT(*) as count')
            ->groupBy('locale')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Helper: Get total searches
     */
    protected function getTotalSearches(int $days): int
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Helper: Get unique queries
     */
    protected function getUniqueQueries(int $days): int
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->distinct('query')
            ->count('query');
    }

    /**
     * Helper: Get average response time
     */
    protected function getAverageResponseTime(int $days): float
    {
        return (float) SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');
    }

    /**
     * Helper: Get zero result count
     */
    protected function getZeroResultCount(int $days): int
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('results_count', 0)
            ->count();
    }

    /**
     * Helper: Get total clicks
     */
    protected function getTotalClicks(int $days): int
    {
        return SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    /**
     * Helper: Get searches with clicks
     */
    protected function getSearchesWithClicks(int $days): int
    {
        return SearchQuery::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->has('clicks')
            ->distinct()
            ->count();
    }

    /**
     * Helper: Get average CTR
     */
    protected function getAverageClickThroughRate(int $days): float
    {
        $totalSearches = $this->getTotalSearches($days);

        if ($totalSearches === 0) {
            return 0.0;
        }

        $searchesWithClicks = $this->getSearchesWithClicks($days);

        return round(($searchesWithClicks / $totalSearches) * 100, 2);
    }

    /**
     * Export search data to CSV
     */
    public function exportToCsv(int $days = 30): string
    {
        $queries = SearchQuery::query()
            ->with('clicks')
            ->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at')
            ->get();

        $csv = "Query,Results Count,Response Time (ms),Clicks,Created At,Locale,User ID\n";

        foreach ($queries as $query) {
            $csv .= sprintf(
                "\"%s\",%d,%d,%d,\"%s\",\"%s\",%s\n",
                str_replace('"', '""', $query->query),
                $query->results_count,
                $query->response_time_ms ?? 0,
                $query->clicks->count(),
                $query->created_at->format('Y-m-d H:i:s'),
                $query->locale,
                $query->user_id ?? 'Guest'
            );
        }

        return $csv;
    }
}
