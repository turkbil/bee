<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

class ProductSearchNode extends BaseNode
{
    public function execute(array $context): array
    {
        $userMessage = $context['user_message'] ?? '';
        $tenantId = $context['tenant_id'] ?? tenant('id');

        $searchLimit = $this->getConfig('search_limit', 5);
        $sortByStock = $this->getConfig('sort_by_sort', true);
        $useMeilisearch = $this->getConfig('use_meilisearch', false);

        // Get category from context (CategoryDetectionNode sets this)
        $detectedCategory = $context['detected_category'] ?? null;

        Log::info('🔍 ProductSearchNode: Searching products', [
            'user_message' => $userMessage,
            'search_limit' => $searchLimit,
            'use_meilisearch' => $useMeilisearch,
            'detected_category' => $detectedCategory,
            'tenant_id' => $tenantId
        ]);

        // Use tenant-specific search service if available
        $searchService = $this->getTenantSearchService($tenantId);

        if ($searchService) {
            // Tenant-specific search (handles keywords, categories internally)
            $result = $searchService->search($userMessage, $searchLimit, $detectedCategory);

            Log::info('✅ ProductSearchNode: Tenant-specific search completed', [
                'tenant_id' => $tenantId,
                'count' => $result['products']->count()
            ]);

            return $result;
        }

        // FALLBACK: Generic search for tenants without custom service
        $keywords = $this->extractGenericKeywords($userMessage);

        if (empty($keywords)) {
            return [
                'products' => collect(),
                'products_found' => 0
            ];
        }

        // Search with Meilisearch or MySQL
        if ($useMeilisearch) {
            $products = $this->searchWithMeilisearch($keywords, $searchLimit, $detectedCategory);
        } else {
            $products = $this->searchWithMySQL($keywords, $searchLimit, $detectedCategory);
        }

        Log::info('✅ ProductSearchNode: Generic search completed', [
            'keywords' => $keywords,
            'count' => $products->count()
        ]);

        return [
            'products' => $products,
            'products_found' => $products->count()
        ];
    }

    /**
     * Get tenant-specific search service
     */
    protected function getTenantSearchService(?int $tenantId)
    {
        if (!$tenantId) {
            return null;
        }

        // Check if tenant has custom search service
        $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}ProductSearchService";

        if (class_exists($serviceClass)) {
            return app($serviceClass);
        }

        return null;
    }

    /**
     * Search products with Meilisearch (Laravel Scout)
     */
    protected function searchWithMeilisearch(array $keywords, int $limit, ?int $categoryId = null)
    {
        $searchQuery = implode(' ', $keywords);

        Log::info('🔍 Meilisearch: Searching', [
            'query' => $searchQuery,
            'limit' => $limit,
            'category_filter' => $categoryId
        ]);

        // StockSorterNode will prioritize products with stock
        // Here we get ALL matching products (in-stock and out-of-stock)
        $search = ShopProduct::search($searchQuery);

        // Apply category filter if detected
        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        $results = $search->take($limit)->get();

        Log::info('✅ Meilisearch: Results found', [
            'count' => $results->count(),
            'filtered_by_category' => $categoryId ? 'YES' : 'NO'
        ]);

        return $results;
    }

    /**
     * Search products with MySQL (JSON query)
     */
    protected function searchWithMySQL(array $keywords, int $limit, ?int $categoryId = null)
    {
        Log::info('🔍 MySQL: Searching', [
            'keywords' => $keywords,
            'limit' => $limit,
            'category_filter' => $categoryId
        ]);

        $query = ShopProduct::query();

        // Apply category filter if detected
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Search in title (JSON column) - case-insensitive
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr'))) LIKE LOWER(?)", ["%{$keyword}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE LOWER(?)", ["%{$keyword}%"]);
            }
        });

        // StockSorterNode will prioritize products with stock
        // Here we get ALL matching products

        $results = $query->limit($limit)->get();

        Log::info('✅ MySQL: Results found', [
            'count' => $results->count(),
            'filtered_by_category' => $categoryId ? 'YES' : 'NO'
        ]);

        return $results;
    }

    /**
     * GENERIC keyword extraction (for tenants without custom service)
     *
     * Very basic - just looks for common intent words
     * Tenants should implement their own search service for better results
     */
    protected function extractGenericKeywords(string $message): array
    {
        $keywords = [];
        $message = mb_strtolower($message);

        // Generic intent detection - just check if user wants to search
        $intentKeywords = ['göster', 'listele', 'bul', 'ara', 'var mı', 'istiyorum', 'arıyorum', 'ürün'];

        foreach ($intentKeywords as $intent) {
            if (str_contains($message, $intent)) {
                // Return empty - tenant should implement custom service
                // or AI will ask user to be more specific
                return [];
            }
        }

        // Split message into words for basic search
        $words = preg_split('/\s+/', $message);
        foreach ($words as $word) {
            if (strlen($word) > 3) { // Only words longer than 3 chars
                $keywords[] = $word;
            }
        }

        return array_unique($keywords);
    }
}
