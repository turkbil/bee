<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Search\App\Models\SearchQuery;

class SearchTagsController extends Controller
{
    /**
     * Display all search tags/queries
     */
    public function index(): View
    {
        // Get all visible search queries with their counts
        $searchTags = SearchQuery::query()
            ->where('is_hidden', false)
            ->where('is_visible_in_tags', true)
            ->selectRaw('
                query,
                COUNT(*) as search_count,
                SUM(results_count) as total_results,
                MAX(is_popular) as is_popular
            ')
            ->groupBy('query')
            ->having('search_count', '>', 0)
            ->get()
            ->shuffle(); // Random order

        // Calculate font sizes based on search count
        $maxCount = $searchTags->max('search_count') ?? 1;
        $minCount = $searchTags->min('search_count') ?? 1;

        $searchTags = $searchTags->map(function ($tag) use ($maxCount, $minCount) {
            // Calculate font size (1-5 scale)
            if ($maxCount == $minCount) {
                $fontSize = 3;
            } else {
                $fontSize = 1 + (($tag->search_count - $minCount) / ($maxCount - $minCount)) * 4;
            }

            $tag->font_size = round($fontSize, 1);

            // Assign colors (8 different colors)
            $colors = [
                'from-blue-500 to-cyan-500',
                'from-purple-500 to-pink-500',
                'from-green-500 to-emerald-500',
                'from-orange-500 to-red-500',
                'from-indigo-500 to-purple-500',
                'from-pink-500 to-rose-500',
                'from-teal-500 to-green-500',
                'from-yellow-500 to-orange-500',
            ];

            $tag->color = $colors[abs(crc32($tag->query)) % count($colors)];

            return $tag;
        });

        // Popular searches (for sidebar)
        $popularSearches = SearchQuery::getMarkedPopular(10);

        // Recent searches (last 20)
        $recentSearches = SearchQuery::query()
            ->where('is_hidden', false)
            ->selectRaw('query, MAX(created_at) as last_searched')
            ->groupBy('query')
            ->orderByDesc('last_searched')
            ->limit(20)
            ->get();

        return view('search::tags', compact('searchTags', 'popularSearches', 'recentSearches'));
    }
}
