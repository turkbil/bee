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
            'filters' => 'nullable|array',
        ]);

        try {
            $results = $this->searchService->searchAll(
                query: $validated['q'],
                perPage: $validated['per_page'] ?? 20,
                filters: $validated['filters'] ?? [],
                activeTab: $validated['type'] ?? 'all'
            );

            // Format for frontend
            $formattedResults = $this->searchService->formatResultsForDisplay(
                $results,
                $validated['q']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $formattedResults,
                    'total' => $results['total_count'],
                    'response_time' => $results['response_time'],
                    'query' => $results['query'],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Search API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında bir hata oluştu.',
            ], 500);
        }
    }

    /**
     * Get autocomplete suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $suggestions = $this->searchService->getSuggestions(
                $validated['q'],
                $validated['limit'] ?? 10
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions,
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
