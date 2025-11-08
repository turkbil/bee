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
     * Hybrid search: Meilisearch + Vector
     */
    public function search(string $query, ?int $categoryId = null, int $limit = 10): array
    {
        Log::info('🔍 Hybrid search started', [
            'query' => $query,
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

        $searchResults = $client->index($indexName)->search($query, [
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

        // 6. GET TOP N PRODUCTS WITH GLOBAL SORTING
        $topProductIds = array_slice(array_keys($hybridScores), 0, $limit);

        $topProducts = ShopProduct::whereIn('product_id', $topProductIds)
            ->get()
            ->sortBy(fn($p) => array_search($p->product_id, $topProductIds))
            ->values();

        // ✅ GLOBAL SORTING RULE (is_master_product → show_on_homepage → current_stock)
        // Apply secondary sort to products with same hybrid score
        $topProducts = $topProducts->sortBy([
            fn($a, $b) => array_search($a->product_id, $topProductIds) <=> array_search($b->product_id, $topProductIds), // Hybrid score first
            fn($a, $b) => ($b->is_master_product ?? 0) <=> ($a->is_master_product ?? 0),     // Then master products
            fn($a, $b) => ($b->show_on_homepage ?? 0) <=> ($a->show_on_homepage ?? 0),       // Then homepage products
            fn($a, $b) => ($b->current_stock ?? 0) <=> ($a->current_stock ?? 0),             // Then stock level
        ])->values();

        Log::info('✅ Hybrid search completed', [
            'keyword_results' => count($keywordResults),
            'semantic_results' => count($semanticResults),
            'hybrid_results' => count($topProducts),
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
