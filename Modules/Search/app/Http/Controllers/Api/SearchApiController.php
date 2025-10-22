<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Search\App\Services\UniversalSearchService;
use Modules\Search\App\Services\SearchClickTracker;

class SearchApiController extends Controller
{
    public function __construct(
        protected UniversalSearchService $searchService,
        protected SearchClickTracker $clickTracker
    ) {}

    /**
     * Search API endpoint
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:500',
            'type' => 'nullable|string|in:all,products,categories,brands,pages',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'filters' => 'nullable|array',
        ]);

        try {
            $results = $this->searchService->searchAll(
                query: $validated['q'],
                perPage: (int) ($validated['per_page'] ?? 12),
                page: (int) ($validated['page'] ?? 1),
                filters: $validated['filters'] ?? [],
                activeTab: $validated['type'] ?? 'all'
            );

            // Format for frontend
            $formattedResults = $this->searchService->formatResultsForDisplay(
                $results,
                $validated['q']
            );

            $pages = collect($results['results'] ?? [])->map(function ($data) {
                return [
                    'current' => $data['current_page'] ?? 1,
                    'last' => $data['last_page'] ?? 1,
                ];
            });

            $currentPage = max(1, $pages->max('current') ?? ($validated['page'] ?? 1));
            $lastPage = max(1, $pages->max('last') ?? 1);

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $formattedResults->all(), // Collection to array
                    'total' => $results['total_count'],
                    'response_time' => $results['response_time'],
                    'query' => $results['query'],
                    'pagination' => [
                        'current_page' => min($currentPage, $lastPage),
                        'last_page' => $lastPage,
                        'per_page' => $validated['per_page'] ?? 12,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Search API error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Get hybrid autocomplete suggestions (keywords + products)
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $query = $validated['q'];
            $limit = $validated['limit'] ?? 5; // Keyword dropdown varsayılanı
            $productLimit = 6; // Grid için 2x3 ürün kartı

            // Get keyword suggestions from popular searches
            $keywordSuggestions = $this->searchService->getSuggestions($query, $limit);

            if ($keywordSuggestions->isEmpty()) {
                $keywordSuggestions = $this->searchService->getFallbackScoutSuggestions($query, $limit);

                $keywords = $keywordSuggestions->map(function ($keyword) {
                    return [
                        'type' => 'keyword',
                        'text' => $keyword,
                        'count' => null,
                    ];
                })->values()->all();
            } else {
                $keywords = $keywordSuggestions->map(function ($keyword) use ($query) {
                    // Get count for each keyword by doing a quick search
                    $count = 0;
                    try {
                        $quickSearch = $this->searchService->searchAll(
                            query: $keyword,
                            perPage: 1,
                            page: 1,
                            filters: [],
                            activeTab: 'all',
                            logQuery: false
                        );
                        $count = $quickSearch['total_count'];
                    } catch (\Exception $e) {
                        // Ignore count errors
                    }

                    return [
                        'type' => 'keyword',
                        'text' => $keyword,
                        'count' => $count,
                    ];
                })->values()->all();
            }

            // Get product suggestions (top 5 from actual search)
            $results = $this->searchService->searchAll(
                query: $query,
                perPage: $productLimit,
                page: 1,
                filters: [],
                activeTab: 'all',
                logQuery: false
            );

            $formattedResults = $this->searchService->formatResultsForDisplay(
                $results,
                $query
            );

            $products = $formattedResults
                ->filter(fn ($item) => ($item['type'] ?? null) === 'products')
                ->sortBy(fn ($item) => $item['is_variant'] ?? 0)
                ->take($productLimit)
                ->map(function ($item) {
                    return [
                        'type' => $item['type'] ?? 'product',
                        'title' => $item['title'],
                        'highlighted_title' => $item['highlighted_title'],
                        'url' => $item['url'],
                        'type_label' => $item['type_label'],
                        'price' => $item['price'],
                        'image' => $item['image'],
                        'highlighted_description' => $item['highlighted_description'],
                        'is_master_product' => $item['is_master_product'] ?? null,
                    ];
                })->values()->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'keywords' => $keywords,
                    'products' => $products,
                    'total' => $results['total_count'],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Suggestions API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Öneri alınırken hata oluştu.',
            ], 500);
        }
    }

    /**
     * Track click on search result
     */
    public function trackClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|max:500',
            'result_id' => 'required|integer',
            'result_type' => 'required|string',
            'position' => 'nullable|integer|min:0',
            'opened_in_new_tab' => 'nullable|boolean',
        ]);

        try {
            $this->clickTracker->trackClick(
                query: $validated['query'],
                resultId: $validated['result_id'],
                resultType: $validated['result_type'],
                position: $validated['position'] ?? 0,
                openedInNewTab: $validated['opened_in_new_tab'] ?? false
            );

            return response()->json([
                'success' => true,
                'message' => 'Click tracked successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Click tracking error: ' . $e->getMessage());

            // Don't fail the request for tracking errors
            return response()->json([
                'success' => true,
                'message' => 'Request processed',
            ]);
        }
    }
}
