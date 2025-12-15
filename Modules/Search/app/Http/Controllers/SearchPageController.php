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
        // 🎵 MUZIBU TENANT: Redirect to /ara (Livewire search page)
        if (tenant() && tenant()->id === 1001) {
            $searchQuery = $query ?? $request->get('q', '');
            return redirect('/ara' . ($searchQuery ? '?q=' . urlencode($searchQuery) : ''));
        }

        // 🚀 CLEAN URL REDIRECT: /search?q=F4 → /search/F4 (SEO Friendly)
        if (!$query && $request->has('q')) {
            $queryString = $request->get('q');
            if (!empty($queryString)) {
                return redirect()->route('search.show', [
                    'query' => $queryString
                ], 301); // 301 Permanent Redirect (SEO)
            }
        }

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

        // 🎯 DYNAMIC SEO META TAGS (Tenant-Aware)
        $this->generateSearchSeoMeta($searchQuery, $initialData);

        return view('search::show', [
            'query' => $searchQuery,
            'pageTitle' => $searchQuery
                ? "'{$searchQuery}' - Arama Sonuçları"
                : 'Arama',
            'initialData' => $initialData,
        ]);
    }

    /**
     * Generate dynamic SEO meta tags for search page (Tenant-Aware)
     */
    private function generateSearchSeoMeta(string $searchQuery, array $initialData): void
    {
        // Get tenant-specific settings (dynamic)
        $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name');
        $siteDescription = setting('site_description') ?: setting('site_slogan');
        $currentLocale = app()->getLocale();

        // Search results count
        $totalResults = $initialData['total'] ?? 0;

        // Build dynamic meta tags
        $metaTags = [
            // Title (Max 60 chars recommended)
            'title' => $searchQuery
                ? "'{$searchQuery}' Arama Sonuçları - {$siteName}"
                : "Arama - {$siteName}",

            // Description (Max 160 chars recommended) - Tenant-specific
            'description' => $searchQuery
                ? ($totalResults > 0
                    ? "'{$searchQuery}' için {$totalResults} sonuç bulundu. {$siteDescription}"
                    : "'{$searchQuery}' araması için sonuç bulunamadı. {$siteDescription}")
                : "Site içi arama yapın. {$siteDescription}",

            // Canonical URL (SEO için önemli)
            'canonical_url' => $searchQuery
                ? route('search.show', ['query' => $searchQuery])
                : route('search.query'),

            // Robots: Popüler aramalar index'lenebilir, diğer aramalar noindex
            'robots' => $searchQuery && $totalResults > 0 ? 'index, follow' : 'noindex, follow',

            // Open Graph
            'og_title' => $searchQuery
                ? "'{$searchQuery}' - {$siteName}"
                : "Arama - {$siteName}",
            'og_description' => $searchQuery && $totalResults > 0
                ? "{$totalResults} sonuç listeleniyor."
                : "Site içi arama",
            'og_image' => null, // Arama sayfası için özel görsel yok
            'og_type' => 'website',
            'og_locale' => str_replace('-', '_', $currentLocale),
            'og_site_name' => $siteName,
            'og_url' => url()->current(),

            // Twitter Card
            'twitter_card' => 'summary',
            'twitter_title' => $searchQuery
                ? "'{$searchQuery}' - Arama Sonuçları"
                : 'Arama',
            'twitter_description' => $searchQuery && $totalResults > 0
                ? "{$totalResults} sonuç bulundu."
                : 'Site içi arama yapın.',
            'twitter_image' => null, // Arama sayfası için özel görsel yok
            'twitter_site' => null,
            'twitter_creator' => null,
        ];

        // Schema.org WebSite + SearchAction (Google Search Box için)
        if (!$searchQuery) {
            // Sadece /search anasayfasında göster
            $metaTags['schemas'] = [
                'website' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'url' => url('/'),
                    'name' => $siteName,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => route('search.show', ['query' => '{search_term_string}'])
                        ],
                        'query-input' => 'required name=search_term_string'
                    ]
                ]
            ];
        }

        // Share to view (SeoMeta component will use this)
        view()->share('metaTags', $metaTags);
    }

    /**
     * Display popular searches page (for SEO)
     */
    public function tags()
    {
        // SEO Meta Tags for Popular Searches Page
        $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name');
        $metaTags = [
            'title' => "Popüler Aramalar - {$siteName}",
            'description' => "En çok aranan kelimeler ve popüler arama terimleri. Site içi arama yapın.",
            'canonical_url' => route('search.tags'),
            'robots' => 'index, follow', // ✅ Popüler aramalar INDEX edilebilir!
            'og_title' => "Popüler Aramalar - {$siteName}",
            'og_description' => "En çok aranan kelimeler ve popüler arama terimleri.",
            'og_type' => 'website',
            'og_url' => route('search.tags'),
            'og_site_name' => $siteName,
            'og_locale' => str_replace('-', '_', app()->getLocale()),
            'twitter_card' => 'summary',
            'twitter_title' => "Popüler Aramalar - {$siteName}",
            'twitter_description' => "En çok aranan kelimeler.",
        ];
        view()->share('metaTags', $metaTags);
        // Get all visible search queries with their counts
        $searchTags = \Modules\Search\App\Models\SearchQuery::query()
            ->where('is_hidden', false)
            ->where('is_visible_in_tags', true)
            ->whereNotNull('query')
            ->where('query', '!=', '')
            ->where('query', 'NOT LIKE', '%{%}%')
            ->selectRaw('
                query,
                MAX(slug) as slug,
                COUNT(*) as search_count,
                SUM(results_count) as total_results,
                MAX(is_popular) as is_popular
            ')
            ->groupBy('query')
            ->having('search_count', '>', 0)
            ->get()
            ->filter(function($tag) {
                return !empty(trim($tag->query)) && !str_contains($tag->query, '{');
            })
            ->values()
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
            ->whereNotNull('query')
            ->where('query', '!=', '')
            ->where('query', 'NOT LIKE', '%{%}%')
            ->selectRaw('query, MAX(slug) as slug, MAX(created_at) as last_searched')
            ->groupBy('query')
            ->orderByDesc('last_searched')
            ->limit(20)
            ->get()
            ->filter(function($search) {
                return !empty(trim($search->query)) && !str_contains($search->query, '{');
            })
            ->values();

        return view('search::tags', [
            'pageTitle' => 'Tüm Aramalar - Popüler Arama Kelimeleri',
            'searchTags' => $searchTags,
            'popularSearches' => $popularSearches,
            'recentSearches' => $recentSearches,
        ]);
    }
}
