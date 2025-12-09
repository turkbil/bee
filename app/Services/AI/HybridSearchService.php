<?php

namespace App\Services\AI;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;
use MeiliSearch\Client as MeiliClient;

class HybridSearchService
{
    private VectorSearchService $vectorSearch;

    // Scoring weights
    private const KEYWORD_WEIGHT = 0.7;  // 70% keyword (Meilisearch)
    private const SEMANTIC_WEIGHT = 0.3; // 30% semantic (Vector)

    public function __construct(VectorSearchService $vectorSearch)
    {
        $this->vectorSearch = $vectorSearch;
    }

    /**
     * Synonym groups for power type (all mean the same thing)
     * elektrikli = li-ion = akülü = lityum
     */
    private const POWER_SYNONYMS = [
        'elektrikli' => ['li-ion', 'akülü', 'lityum', 'bataryalı'],
        'li-ion' => ['elektrikli', 'akülü', 'lityum', 'bataryalı'],
        'akülü' => ['elektrikli', 'li-ion', 'lityum', 'bataryalı'],
        'lityum' => ['elektrikli', 'li-ion', 'akülü', 'bataryalı'],
        'bataryalı' => ['elektrikli', 'li-ion', 'akülü', 'lityum'],
    ];

    /**
     * Expand query with synonyms (power types only - global)
     * Category synonyms are handled by tenant-specific services
     */
    private function expandQueryWithSynonyms(string $query): string
    {
        $lowerQuery = mb_strtolower($query);
        $expansions = [];

        // Power type synonyms (global - applies to all tenants)
        foreach (self::POWER_SYNONYMS as $term => $synonyms) {
            if (str_contains($lowerQuery, $term)) {
                $expansions = array_merge($expansions, $synonyms);
            }
        }

        if (!empty($expansions)) {
            $uniqueExpansions = array_unique($expansions);
            $expandedQuery = $query . ' ' . implode(' ', $uniqueExpansions);

            Log::info('🔄 Query expanded with power synonyms', [
                'original' => $query,
                'expanded' => $expandedQuery
            ]);

            return $expandedQuery;
        }

        return $query;
    }

    /**
     * Hybrid search: Meilisearch + Vector
     */
    public function search(string $query, ?int $categoryId = null, int $limit = 10): array
    {
        // Expand query with power type synonyms
        $expandedQuery = $this->expandQueryWithSynonyms($query);

        Log::info('🔍 Hybrid search started', [
            'query' => $query,
            'expanded_query' => $expandedQuery,
            'category_id' => $categoryId,
        ]);

        // 1. KEYWORD SEARCH (Meilisearch) - Fast, typo-tolerant
        Log::info('🔍 Meilisearch (raw client) search starting', [
            'query' => $query,
            'tenant_id' => tenant('id'),
            'tenant_initialized' => tenancy()->initialized,
        ]);

        // ✅ RAW MEILISEARCH CLIENT (comparison operators için gerekli)
        $client = new MeiliClient(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $indexName = tenancy()->initialized
            ? 'shop_products_tenant_' . tenant('id')
            : 'shop_products';

        // Build filter string
        $filterParts = ['is_active = true'];  // ✅ Sadece aktif ürünler (fiyatsız/stoksuz da göster)

        if ($categoryId) {
            $filterParts[] = "category_id = {$categoryId}";
        }

        $filterString = implode(' AND ', $filterParts);

        $searchResults = $client->index($indexName)->search($expandedQuery, [
            'filter' => $filterString,
            'limit' => 50
        ]);

        // Convert Meilisearch hits to Eloquent models
        $hits = $searchResults->getHits();
        $productIds = collect($hits)->pluck('product_id');
        $keywordResults = ShopProduct::whereIn('product_id', $productIds)
            ->get()
            ->sortBy(function($product) use ($productIds) {
                return $productIds->search($product->product_id);
            })
            ->values();

        Log::info('🔍 Scout search completed', [
            'query' => $query,
            'results_count' => $keywordResults->count(),
            'first_result' => $keywordResults->first()?->title ?? null,
        ]);

        // 2. SEMANTIC SEARCH (Vector) - Meaning-based
        // Only if we have products with embeddings
        $semanticResults = [];
        $hasEmbeddings = ShopProduct::whereNotNull('embedding')->exists();

        if ($hasEmbeddings) {
            try {
                $semanticData = $this->vectorSearch->search($query, 50);
                $semanticResults = collect($semanticData)->pluck('product')->toArray();
            } catch (\Exception $e) {
                Log::warning('Semantic search failed', ['error' => $e->getMessage()]);
            }
        }

        // 3. SCORE COMBINATION
        $hybridScores = [];

        // Keyword scores (position-based: first = 1.0, last = 0)
        foreach ($keywordResults as $index => $product) {
            $keywordScore = 1 - ($index / max(1, count($keywordResults)));

            $hybridScores[$product->product_id] = [
                'product_id' => $product->product_id,
                'keyword_score' => $keywordScore,
                'semantic_score' => 0,
            ];
        }

        // Semantic scores
        foreach ($semanticResults as $index => $productData) {
            $productId = $productData['product_id'] ?? null;
            if (!$productId) continue;

            $semanticScore = 1 - ($index / max(1, count($semanticResults)));

            if (isset($hybridScores[$productId])) {
                $hybridScores[$productId]['semantic_score'] = $semanticScore;
            } else {
                $hybridScores[$productId] = [
                    'product_id' => $productId,
                    'keyword_score' => 0,
                    'semantic_score' => $semanticScore,
                ];
            }
        }

        // 4. CALCULATE HYBRID SCORE
        foreach ($hybridScores as $productId => &$scores) {
            $scores['hybrid_score'] =
                ($scores['keyword_score'] * self::KEYWORD_WEIGHT) +
                ($scores['semantic_score'] * self::SEMANTIC_WEIGHT);
        }

        // 5. SORT BY HYBRID SCORE
        uasort($hybridScores, fn($a, $b) => $b['hybrid_score'] <=> $a['hybrid_score']);

        // 6. GET TOP N PRODUCTS WITH PRIORITY SORTING
        $topProductIds = array_slice(array_keys($hybridScores), 0, $limit * 2); // Get more for homepage injection

        $topProducts = ShopProduct::whereIn('product_id', $topProductIds)
            ->with(['category:category_id,sort_order', 'media'])
            ->get();

        // 6.1 HOMEPAGE PRODUCTS INJECTION
        // If category is detected, inject homepage products from that category at the top
        if ($categoryId) {
            $homepageProducts = ShopProduct::where('category_id', $categoryId)
                ->where('is_active', true)
                ->where('show_on_homepage', true)
                ->with(['category:category_id,sort_order', 'media'])
                ->get();

            // Merge homepage products with search results (homepage first, avoid duplicates)
            $existingIds = $topProducts->pluck('product_id')->toArray();
            foreach ($homepageProducts as $hp) {
                if (!in_array($hp->product_id, $existingIds)) {
                    $topProducts->prepend($hp);
                    // Add to hybridScores for sorting
                    $hybridScores[$hp->product_id] = [
                        'product_id' => $hp->product_id,
                        'keyword_score' => 0.5, // Medium score
                        'semantic_score' => 0,
                        'hybrid_score' => 0.5,
                    ];
                }
            }

            Log::info('🏠 Homepage products injected', [
                'category_id' => $categoryId,
                'homepage_count' => $homepageProducts->count(),
            ]);
        }

        // ✅ KRİTİK SIRALAMA (Kullanıcı isteğine göre öncelik)
        // 1. show_on_homepage = 1 EN ÖNCE (homepage sıralaması)
        // 2. Kategori sort_order
        // 3. Stoklu ürünler
        // 4. Fiyatlı ürünler
        // 5. Hybrid score
        $topProducts = $topProducts->sort(function ($a, $b) use ($hybridScores) {
            // 1. Homepage ürünleri EN ÖNCE
            $homepageA = $a->show_on_homepage ?? 0;
            $homepageB = $b->show_on_homepage ?? 0;
            if ($homepageA !== $homepageB) {
                return $homepageB <=> $homepageA; // DESC (1 önce)
            }

            // 2. Kategori sıralaması (aynı kategorideki ürünler için)
            if ($a->category_id === $b->category_id) {
                $sortA = $a->sort_order ?? 999;
                $sortB = $b->sort_order ?? 999;
                if ($sortA !== $sortB) {
                    return $sortA <=> $sortB; // ASC (küçük önce)
                }
            }

            // 3. Stoklu ürünler önce
            $stockA = ($a->current_stock ?? 0) > 0 ? 1 : 0;
            $stockB = ($b->current_stock ?? 0) > 0 ? 1 : 0;
            if ($stockA !== $stockB) {
                return $stockB <=> $stockA; // DESC (stoklu önce)
            }

            // 4. Fiyatlı ürünler önce
            $priceA = ($a->base_price ?? 0) > 0 ? 1 : 0;
            $priceB = ($b->base_price ?? 0) > 0 ? 1 : 0;
            if ($priceA !== $priceB) {
                return $priceB <=> $priceA; // DESC (fiyatlı önce)
            }

            // 5. Hybrid score
            $scoreA = $hybridScores[$a->product_id]['hybrid_score'] ?? 0;
            $scoreB = $hybridScores[$b->product_id]['hybrid_score'] ?? 0;
            return $scoreB <=> $scoreA; // DESC
        })->values(); // Sort complete

        // 🔥 KATEGORİ FİLTRESİ - Semantic search farklı kategoriden ürün getirmiş olabilir
        // Eğer kategori belirtilmişse, sadece o kategorideki ürünleri tut
        if ($categoryId) {
            $topProducts = $topProducts->filter(function ($product) use ($categoryId) {
                return $product->category_id == $categoryId;
            })->values();
        }

        // Apply limit after filtering
        $topProducts = $topProducts->take($limit);

        Log::info('✅ Hybrid search completed', [
            'keyword_results' => count($keywordResults),
            'semantic_results' => count($semanticResults),
            'hybrid_results' => count($topProducts),
            'category_filtered' => $categoryId ? 'YES' : 'NO',
            'top_product' => $topProducts->first()?->title['tr'] ?? null,
        ]);

        return $topProducts->map(function ($product) use ($hybridScores) {
            return [
                'product' => $product->toArray(),
                'scores' => $hybridScores[$product->product_id] ?? [],
            ];
        })->toArray();
    }
}
