<?php

declare(strict_types=1);

namespace Modules\Search\App\Services;

use Modules\Search\App\Models\SearchClick;
use Modules\Search\App\Models\SearchQuery;

class SearchClickTracker
{
    /**
     * Track a click on search result
     */
    public function trackClick(
        string $query,
        int $resultId,
        string $resultType,
        int $position = 0,
        bool $openedInNewTab = false
    ): ?SearchClick {
        try {
            // Get the most recent search query for this session
            $searchQuery = SearchQuery::query()
                ->where('session_id', session()->getId())
                ->where('query', $query)
                ->latest()
                ->first();

            if (!$searchQuery) {
                \Log::warning('Search query not found for click tracking', [
                    'query' => $query,
                    'session_id' => session()->getId(),
                ]);
                return null;
            }

            // Create click record
            return SearchClick::create([
                'search_query_id' => $searchQuery->id,
                'clicked_result_id' => $resultId,
                'clicked_result_type' => $resultType,
                'click_position' => $position,
                'opened_in_new_tab' => $openedInNewTab,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to track search click: ' . $e->getMessage(), [
                'query' => $query,
                'result_id' => $resultId,
                'result_type' => $resultType,
            ]);
            return null;
        }
    }

    /**
     * Get click-through rate for a specific query
     */
    public function getClickThroughRate(string $query, int $days = 30): float
    {
        $totalSearches = SearchQuery::query()
            ->where('query', $query)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        if ($totalSearches === 0) {
            return 0.0;
        }

        $searchesWithClicks = SearchQuery::query()
            ->where('query', $query)
            ->where('created_at', '>=', now()->subDays($days))
            ->has('clicks')
            ->distinct()
            ->count();

        return ($searchesWithClicks / $totalSearches) * 100;
    }

    /**
     * Get most clicked position for analytics
     */
    public function getMostClickedPositions(int $days = 30, int $limit = 10): array
    {
        return SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('click_position, COUNT(*) as clicks')
            ->groupBy('click_position')
            ->orderByDesc('clicks')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'position' => $item->click_position,
                    'clicks' => $item->clicks,
                ];
            })
            ->toArray();
    }

    /**
     * Get new tab vs same tab percentage
     */
    public function getNewTabPercentage(int $days = 30): array
    {
        $total = SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        if ($total === 0) {
            return [
                'new_tab' => 0,
                'same_tab' => 0,
                'new_tab_percentage' => 0,
                'same_tab_percentage' => 0,
            ];
        }

        $newTabCount = SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('opened_in_new_tab', true)
            ->count();

        $sameTabCount = $total - $newTabCount;

        return [
            'new_tab' => $newTabCount,
            'same_tab' => $sameTabCount,
            'new_tab_percentage' => ($newTabCount / $total) * 100,
            'same_tab_percentage' => ($sameTabCount / $total) * 100,
        ];
    }

    /**
     * Get most clicked items
     */
    public function getMostClickedItems(
        ?string $type = null,
        int $days = 30,
        int $limit = 10
    ): array {
        $query = SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('clicked_result_id, clicked_result_type, COUNT(*) as click_count')
            ->groupBy('clicked_result_id', 'clicked_result_type')
            ->orderByDesc('click_count')
            ->limit($limit);

        if ($type) {
            $query->where('clicked_result_type', $type);
        }

        return $query->get()->map(function ($item) {
            return [
                'id' => $item->clicked_result_id,
                'type' => $item->clicked_result_type,
                'clicks' => $item->click_count,
            ];
        })->toArray();
    }

    /**
     * Get average position of clicked results
     */
    public function getAverageClickPosition(int $days = 30): float
    {
        return (float) SearchClick::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('click_position')
            ->avg('click_position');
    }
}
