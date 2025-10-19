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
        return view('search::tags', [
            'pageTitle' => 'Popüler Aramalar',
        ]);
    }
}
