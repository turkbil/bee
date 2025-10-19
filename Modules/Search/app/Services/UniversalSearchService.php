<?php

declare(strict_types=1);

namespace Modules\Search\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Search\App\Models\SearchQuery;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class UniversalSearchService
{
    /**
     * Searchable models configuration
     */
    protected array $searchableModels = [
        'products' => ShopProduct::class,
        'categories' => ShopCategory::class,
        'brands' => ShopBrand::class,
        // 'pages' => \Modules\Page\App\Models\Page::class, // Eklenebilir
    ];

    /**
     * Search across all modules
     */
    public function searchAll(
        string $query,
        int $perPage = 20,
        int $page = 1,
        array $filters = [],
        string $activeTab = 'all'
    ): array {
        $startTime = microtime(true);

        $results = [];
        $totalCount = 0;

        // Hangi modüllerde aranacak?
        $modelsToSearch = $activeTab === 'all'
            ? $this->searchableModels
            : [$activeTab => $this->searchableModels[$activeTab] ?? null];

        foreach ($modelsToSearch as $key => $modelClass) {
            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }

            try {
                $modelResults = $this->searchInModel($modelClass, $query, $filters, $perPage, $page);
                $results[$key] = $modelResults;
                $totalCount += $modelResults['total'];
            } catch (\Exception $e) {
                \Log::error("Search error in {$modelClass}: " . $e->getMessage());
                $results[$key] = ['items' => collect([]), 'count' => 0, 'total' => 0];
            }
        }

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        // Log search query
        $this->logSearchQuery($query, $totalCount, $responseTime, $filters, $activeTab);

        return [
            'results' => $results,
            'total_count' => $totalCount,
            'response_time' => $responseTime,
            'query' => $query,
            'filters' => $filters,
        ];
    }

    /**
     * Search in specific model using Scout
     */
    protected function searchInModel(
        string $modelClass,
        string $query,
        array $filters = [],
        int $limit = 20,
        int $page = 1
    ): array {
        $searchQuery = $modelClass::search($query);

        // Apply filters
        if (!empty($filters['is_active'])) {
            $searchQuery->where('is_active', true);
        }

        if (!empty($filters['category_id'])) {
            $searchQuery->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $searchQuery->where('brand_id', $filters['brand_id']);
        }

        // Use paginate() for pagination support
        $paginator = $searchQuery->paginate($limit, 'page', $page);

        return [
            'items' => collect($paginator->items()),
            'count' => $paginator->count(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    /**
     * Get autocomplete suggestions - ONLY from popular searches
     */
    public function getSuggestions(string $partial, int $limit = 10): Collection
    {
        if (strlen($partial) < 2) {
            return collect([]);
        }

        // Cache suggestions for 1 hour
        return Cache::remember("suggestions:{$partial}", 3600, function () use ($partial, $limit) {
            // Get ONLY from popular searches (user-typed queries)
            // Ürün isimlerini keyword olarak gösterme!
            $popularSearches = SearchQuery::query()
                ->where('query', 'LIKE', "{$partial}%")
                ->where('results_count', '>', 0)
                // Sadece kısa kelimeleri al (ürün isimleri çok uzun)
                ->whereRaw('CHAR_LENGTH(query) <= 50')
                // Test verilerini filtrele
                ->where('query', 'NOT LIKE', '%OBSERVER TEST%')
                ->where('query', 'NOT LIKE', '%İXTİF CPD%')
                ->selectRaw('query, COUNT(*) as count')
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit($limit * 2) // Fazladan al, filtrelenecek
                ->pluck('query');

            return $popularSearches
                ->unique()
                ->filter(fn($q) => !empty(trim($q))) // Boş olanları filtrele
                ->take($limit)
                ->values();
        });
    }

    /**
     * Search in specific module only
     */
    public function searchInModule(
        string $moduleName,
        string $query,
        array $filters = [],
        int $perPage = 20
    ) {
        $modelClass = $this->searchableModels[$moduleName] ?? null;

        if (!$modelClass) {
            throw new \Exception("Module '{$moduleName}' is not searchable");
        }

        $startTime = microtime(true);
        $results = $this->searchInModel($modelClass, $query, $filters, $perPage);
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        // Log search
        $this->logSearchQuery($query, $results['count'], $responseTime, $filters, $moduleName);

        return $results;
    }

    /**
     * Log search query for analytics
     */
    protected function logSearchQuery(
        string $query,
        int $resultsCount,
        int $responseTime,
        array $filters = [],
        string $type = 'all'
    ): void {
        try {
            SearchQuery::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'query' => $query,
                'searchable_type' => $type !== 'all' ? $type : null,
                'results_count' => $resultsCount,
                'filters_applied' => !empty($filters) ? $filters : null,
                'response_time_ms' => $responseTime,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'locale' => app()->getLocale(),
                'referrer_url' => request()->headers->get('referer'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log search query: ' . $e->getMessage());
        }
    }

    /**
     * Get available searchable modules
     */
    public function getAvailableModules(): array
    {
        return array_keys($this->searchableModels);
    }

    /**
     * Format results for frontend display
     */
    public function formatResultsForDisplay(array $results, string $query): Collection
    {
        $formatted = collect([]);

        foreach ($results['results'] as $type => $data) {
            foreach ($data['items'] as $item) {
                $formatted->push([
                    'id' => $item->getKey(),
                    'type' => $type,
                    'type_label' => $this->getTypeLabel($type),
                    'title' => $this->getItemTitle($item),
                    'description' => $this->getItemDescription($item),
                    'url' => $this->getItemUrl($item, $type),
                    'image' => $this->getItemImage($item),
                    'price' => $this->getItemPrice($item),
                    'highlighted_title' => $this->highlightQuery($this->getItemTitle($item), $query),
                    'highlighted_description' => $this->highlightQuery($this->getItemDescription($item), $query),
                ]);
            }
        }

        return $formatted;
    }

    /**
     * Helper: Get item title
     */
    protected function getItemTitle($item): string
    {
        $title = $item->title ?? $item->name ?? '';
        return is_array($title) ? ($title[app()->getLocale()] ?? '') : $title;
    }

    /**
     * Helper: Get item description
     */
    protected function getItemDescription($item): string
    {
        $desc = $item->short_description ?? $item->description ?? '';
        $desc = is_array($desc) ? ($desc[app()->getLocale()] ?? '') : $desc;
        return strip_tags($desc);
    }

    /**
     * Helper: Get item URL
     */
    protected function getItemUrl($item, string $type): string
    {
        try {
            // Get slug for current locale
            $slug = is_array($item->slug ?? null)
                ? ($item->slug[app()->getLocale()] ?? $item->slug['tr'] ?? null)
                : ($item->slug ?? null);

            if (!$slug) {
                return '#';
            }

            // Generate URL based on type
            // Products: /shop/{slug}
            // Categories: /shop/category/{slug}
            // Brands: /shop/brand/{slug}
            return match ($type) {
                'products' => route('shop.show', $slug),
                'categories' => route('shop.category', $slug),
                'brands' => route('shop.brand', $slug),
                default => '#',
            };
        } catch (\Exception $e) {
            \Log::warning('Search URL generation failed: ' . $e->getMessage());
            return '#';
        }
    }

    /**
     * Helper: Get item image
     */
    protected function getItemImage($item): ?string
    {
        if (method_exists($item, 'getFirstMediaUrl')) {
            return $item->getFirstMediaUrl() ?: null;
        }
        return null;
    }

    /**
     * Helper: Get item price
     */
    protected function getItemPrice($item): ?string
    {
        if (isset($item->base_price) && $item->base_price > 0) {
            return number_format($item->base_price, 2) . ' ' . ($item->currency ?? 'TRY');
        }
        return null;
    }

    /**
     * Helper: Get type label
     */
    protected function getTypeLabel(string $type): string
    {
        return match ($type) {
            'products' => 'Ürün',
            'categories' => 'Kategori',
            'brands' => 'Marka',
            'pages' => 'Sayfa',
            default => ucfirst($type),
        };
    }

    /**
     * Helper: Highlight search query in text
     */
    protected function highlightQuery(string $text, string $query): string
    {
        if (empty($text) || empty($query)) {
            return $text;
        }

        return preg_replace(
            '/(' . preg_quote($query, '/') . ')/iu',
            '<mark class="bg-yellow-200">$1</mark>',
            $text
        );
    }
}
