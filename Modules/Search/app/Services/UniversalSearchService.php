<?php

declare(strict_types=1);

namespace Modules\Search\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
        string $activeTab = 'all',
        bool $logQuery = true
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
        if ($logQuery) {
            $this->logSearchQuery($query, $totalCount, $responseTime, $filters, $activeTab);
        }

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
        if (!method_exists($modelClass, 'search')) {
            return $this->fallbackSearchInModel($modelClass, $query, $filters, $limit, $page);
        }

        try {
            $searchQuery = $modelClass::search($query);

            if (!empty($filters['is_active'])) {
                $searchQuery->where('is_active', true);
            }

            if (!empty($filters['category_id'])) {
                $searchQuery->where('category_id', $filters['category_id']);
            }

            if (!empty($filters['brand_id'])) {
                $searchQuery->where('brand_id', $filters['brand_id']);
            }

            $paginator = $searchQuery->paginate($limit, 'page', $page);

            if ($paginator->total() === 0) {
                return $this->fallbackSearchInModel($modelClass, $query, $filters, $limit, $page);
            }

            return [
                'items' => collect($paginator->items()),
                'count' => $paginator->count(),
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ];
        } catch (\Throwable $e) {
            \Log::warning("Scout search failed in {$modelClass}: " . $e->getMessage());
            return $this->fallbackSearchInModel($modelClass, $query, $filters, $limit, $page);
        }
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
            $rawSuggestions = SearchQuery::query()
                ->where('query', 'LIKE', "{$partial}%")
                ->where('results_count', '>', 0)
                // Sadece kısa kelimeleri al (ürün isimleri çok uzun)
                ->whereRaw('CHAR_LENGTH(query) BETWEEN 2 AND 50')
                // Test verilerini filtrele
                ->where('query', 'NOT LIKE', '%OBSERVER TEST%')
                ->where('query', 'NOT LIKE', '%İXTİF CPD%')
                ->selectRaw('query, COUNT(*) as count')
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit($limit * 6) // Fazladan al, filtrelemede kullanılacak
                ->get()
                ->map(function ($row) {
                    return [
                        'query' => trim($row->query),
                        'count' => (int) $row->count,
                    ];
                })
                ->filter(fn($row) => $row['query'] !== '');

            $normalized = $rawSuggestions->unique('query')->values();

            $filtered = $normalized
                ->reject(function ($row) use ($normalized) {
                    $query = $row['query'];
                    $length = Str::length($query);
                    if ($length < 2) {
                        return true;
                    }

                    return $normalized->contains(function ($other) use ($row, $length, $query) {
                        if ($other['query'] === $query) {
                            return false;
                        }

                        return Str::startsWith($other['query'], $query)
                            && Str::length($other['query']) > $length
                            && $other['count'] >= $row['count'];
                    });
                })
                ->sortByDesc('count')
                ->take($limit);

            return $filtered
                ->pluck('query')
                ->values();
        });
    }


    protected function fallbackSearchInModel(
        string $modelClass,
        string $query,
        array $filters = [],
        int $limit = 20,
        int $page = 1
    ): array {
        $builder = $modelClass::query();
        $locale = app()->getLocale();
        $patterns = $this->buildLikePatterns($query);

        if ($modelClass === ShopProduct::class) {
            $builder->where(function (Builder $sub) use ($patterns, $locale) {
                $this->applyLikeConditions($sub, [
                    ['column' => 'title', 'json' => true],
                    ['column' => 'slug', 'json' => true],
                    ['column' => 'short_description', 'json' => true],
                ], $patterns, $locale);

                $this->applyLikeConditions($sub, [
                    ['column' => 'sku'],
                    ['column' => 'model_number'],
                ], $patterns);
            });

            if (!empty($filters['is_active'])) {
                $builder->where('is_active', true);
            }

            if (!empty($filters['category_id'])) {
                $builder->where('category_id', $filters['category_id']);
            }

            if (!empty($filters['brand_id'])) {
                $builder->where('brand_id', $filters['brand_id']);
            }
        } elseif ($modelClass === ShopCategory::class) {
            $builder->where(function (Builder $sub) use ($patterns, $locale) {
                $this->applyLikeConditions($sub, [
                    ['column' => 'title', 'json' => true],
                    ['column' => 'slug', 'json' => true],
                ], $patterns, $locale);
            });
        } elseif ($modelClass === ShopBrand::class) {
            $builder->where(function (Builder $sub) use ($patterns, $locale) {
                $this->applyLikeConditions($sub, [
                    ['column' => 'title', 'json' => true],
                    ['column' => 'slug', 'json' => true],
                ], $patterns, $locale);
            });
        } else {
            return $this->emptySearchResult();
        }

        $paginator = $builder->paginate($limit, ['*'], 'page', $page);

        return [
            'items' => collect($paginator->items()),
            'count' => $paginator->count(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    protected function applyLikeConditions(Builder $builder, array $columns, array $patterns, ?string $locale = null): void
    {
        $first = true;

        foreach ($columns as $columnConfig) {
            $column = $columnConfig['column'];
            $isJson = $columnConfig['json'] ?? false;
            $expression = $this->accentInsensitiveExpression($column, $isJson, $locale);

            foreach ($patterns as $pattern) {
                $binding = '%' . $pattern . '%';
                if ($first) {
                    $builder->whereRaw("$expression LIKE ?", [$binding]);
                    $first = false;
                } else {
                    $builder->orWhereRaw("$expression LIKE ?", [$binding]);
                }
            }
        }
    }

    protected function accentInsensitiveExpression(string $column, bool $isJson = false, ?string $locale = null): string
    {
        if ($isJson) {
            $locale = $locale ?: app()->getLocale();
            $expression = "JSON_UNQUOTE(JSON_EXTRACT($column, '$.\"{$locale}\"'))";
        } else {
            $expression = $column;
        }

        $expression = "LOWER($expression)";
        foreach ($this->accentMap as $accent => $plain) {
            $safeAccent = str_replace("'", "''", $accent);
            $safePlain = str_replace("'", "''", $plain);
            $expression = "REPLACE($expression, '$safeAccent', '$safePlain')";
        }

        return $expression;
    }

    protected function normalizeQuery(string $query): string
    {
        return strtr(Str::lower($query), $this->accentMap);
    }

    protected function buildLikePatterns(string $query): array
    {
        $patterns = [
            Str::lower($query),
            $this->normalizeQuery($query),
        ];

        return array_values(array_unique(array_filter($patterns)));
    }

    protected function emptySearchResult(): array
    {
        return [
            'items' => collect([]),
            'count' => 0,
            'total' => 0,
            'current_page' => 1,
            'last_page' => 1,
        ];
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
                    'is_master_product' => $item->is_master_product ?? null,
                    'product_badge' => $this->resolveProductBadge($item),
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
        if (method_exists($item, 'featuredImageUrl')) {
            $featured = $item->featuredImageUrl();
            if ($featured) {
                return $featured;
            }
            $featuredThumb = $item->featuredImageUrl('thumb');
            if ($featuredThumb) {
                return $featuredThumb;
            }
        }

        if (method_exists($item, 'getFirstMediaUrl')) {
            $collections = [
                'featured_image',
                'gallery',
                'category_image',
                'brand_logo',
                'seo_og_image',
            ];
            $conversions = ['thumb', 'medium', 'small'];

            foreach ($collections as $collection) {
                $url = $item->getFirstMediaUrl($collection);
                if ($url) {
                    return $url;
                }

                foreach ($conversions as $conversion) {
                    $converted = $item->getFirstMediaUrl($collection, $conversion);
                    if ($converted) {
                        return $converted;
                    }
                }
            }

            $default = $item->getFirstMediaUrl();
            if ($default) {
                return $default;
            }
        }

        $gallery = $item->media_gallery ?? null;
        if (!empty($gallery)) {
            if (is_string($gallery)) {
                $decoded = json_decode($gallery, true);
                $gallery = json_last_error() === JSON_ERROR_NONE ? $decoded : $gallery;
            }

            if (is_array($gallery)) {
                foreach ($gallery as $mediaItem) {
                    if (empty($mediaItem)) {
                        continue;
                    }
                    if (is_array($mediaItem) && !empty($mediaItem['url'])) {
                        $normalized = $this->normalizeMediaUrl($mediaItem['url']);
                        if ($normalized) {
                            return $normalized;
                        }
                    } elseif (is_string($mediaItem)) {
                        $normalized = $this->normalizeMediaUrl($mediaItem);
                        if ($normalized) {
                            return $normalized;
                        }
                    }
                }
            } elseif (is_string($gallery)) {
                $normalized = $this->normalizeMediaUrl($gallery);
                if ($normalized) {
                    return $normalized;
                }
            }
        }

        if ($item instanceof ShopProduct && !empty($item->parent_product_id)) {
            $parent = $item->relationLoaded('parentProduct')
                ? $item->parentProduct
                : $item->parentProduct()->first();

            if ($parent) {
                $parentImage = $this->getItemImage($parent);
                if ($parentImage) {
                    return $parentImage;
                }
            }
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

    protected function resolveProductBadge($item): ?string
    {
        if ($item instanceof ShopProduct && $item->isVariant()) {
            return 'Varyant';
        }

        return null;
    }

    protected function normalizeMediaUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['//'])) {
            return request()->getScheme() . ':' . $path;
        }

        $cleanPath = ltrim($path, '/');
        return url($cleanPath);
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
