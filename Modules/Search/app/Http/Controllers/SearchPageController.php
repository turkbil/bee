<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class SearchPageController extends Controller
{
    /**
     * Display search results page
     */
    public function show(Request $request, ?string $query = null)
    {
        // Get query from URL parameter or route parameter
        $searchQuery = $query ?? $request->get('q', '');

        // Decode URL-encoded query
        $searchQuery = urldecode($searchQuery);

        // Convert slug format to normal text (optional)
        // forklift-yedek-parca -> forklift yedek parça
        if ($query) {
            $searchQuery = str_replace('-', ' ', $searchQuery);
        }

        $perPage = max(1, min((int) $request->get('per_page', 12), 100));
        $pageNumber = max(1, (int) $request->get('page', 1));

        $initialData = [
            'items' => [],
            'total' => 0,
            'response_time' => 0,
            'page' => $pageNumber,
            'per_page' => $perPage,
            'last_page' => 1,
        ];

        if (mb_strlen($searchQuery) >= 2) {
            try {
                $searchService = app(\Modules\Search\App\Services\UniversalSearchService::class);
                $searchResults = $searchService->searchAll(
                    query: $searchQuery,
                    perPage: $perPage,
                    page: $pageNumber,
                    filters: [],
                    activeTab: 'all'
                );

                $formatted = $searchService->formatResultsForDisplay($searchResults, $searchQuery);

                $initialData['items'] = $formatted->values();
                $initialData['total'] = $searchResults['total_count'];
                $initialData['response_time'] = $searchResults['response_time'];

                $pages = collect($searchResults['results'] ?? [])->map(function ($data) {
                    return [
                        'current' => $data['current_page'] ?? 1,
                        'last' => $data['last_page'] ?? 1,
                    ];
                });

                $initialData['last_page'] = max(1, $pages->max('last') ?? 1);
                $initialData['page'] = min($initialData['last_page'], max(1, $pages->max('current') ?? $pageNumber));
            } catch (\Throwable $e) {
                \Log::error('Search page prefetch failed: ' . $e->getMessage(), [
                    'query' => $searchQuery,
                    'tenant' => tenant('id') ?? 'central',
                ]);
            }
        }

        return view('search::show', [
            'query' => $searchQuery,
            'pageTitle' => $searchQuery
                ? "'{$searchQuery}' - Arama Sonuçları"
                : 'Arama',
            'initialData' => $initialData,
        ]);
    }

    /**
     * Display popular searches page (for SEO)
     */
    public function tags()
    {
        // Get all visible search queries with their counts
        $searchTags = \Modules\Search\App\Models\SearchQuery::query()
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
        $popularSearches = \Modules\Search\App\Models\SearchQuery::getMarkedPopular(10);

        // Recent searches (last 20)
        $recentSearches = \Modules\Search\App\Models\SearchQuery::query()
            ->where('is_hidden', false)
            ->selectRaw('query, MAX(created_at) as last_searched')
            ->groupBy('query')
            ->orderByDesc('last_searched')
            ->limit(20)
            ->get();

        return view('search::tags', [
            'pageTitle' => 'Tüm Aramalar - Popüler Arama Kelimeleri',
            'searchTags' => $searchTags,
            'popularSearches' => $popularSearches,
            'recentSearches' => $recentSearches,
        ]);
    }
}
