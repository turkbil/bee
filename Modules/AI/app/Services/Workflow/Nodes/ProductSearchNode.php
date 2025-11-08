<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

class ProductSearchNode extends BaseNode
{
    public function execute(array $context): array
    {
        $userMessage = $context['user_message'] ?? '';

        $searchLimit = $this->getConfig('search_limit', 5);
        $sortByStock = $this->getConfig('sort_by_stock', true);
        $useMeilisearch = $this->getConfig('use_meilisearch', false);

        Log::info('🔍 ProductSearchNode: Searching products', [
            'user_message' => $userMessage,
            'search_limit' => $searchLimit,
            'use_meilisearch' => $useMeilisearch
        ]);

        // Extract keywords from user message
        $keywords = $this->extractKeywords($userMessage);

        Log::info('🔍 ProductSearchNode: Keywords extracted', [
            'keywords' => $keywords,
            'count' => count($keywords)
        ]);

        // If no product keywords found, don't search (user is just chatting)
        if (empty($keywords)) {
            Log::info('🔍 ProductSearchNode: No product keywords found, skipping search', [
                'user_message' => $userMessage
            ]);

            return [
                'products' => collect(),
                'products_found' => 0
            ];
        }

        // Search with Meilisearch or MySQL
        if ($useMeilisearch) {
            $products = $this->searchWithMeilisearch($keywords, $searchLimit);
        } else {
            $products = $this->searchWithMySQL($keywords, $searchLimit);
        }

        Log::info('✅ ProductSearchNode: Found products', [
            'keywords' => $keywords,
            'count' => $products->count()
        ]);

        // Return only new keys (FlowExecutor will merge with context)
        return [
            'products' => $products,
            'products_found' => $products->count()
        ];
    }

    /**
     * Search products with Meilisearch (Laravel Scout)
     */
    protected function searchWithMeilisearch(array $keywords, int $limit)
    {
        $searchQuery = implode(' ', $keywords);

        Log::info('🔍 Meilisearch: Searching', [
            'query' => $searchQuery,
            'limit' => $limit
        ]);

        $results = ShopProduct::search($searchQuery)
            ->where('current_stock', '>', 0)
            ->take($limit)
            ->get();

        Log::info('✅ Meilisearch: Results found', [
            'count' => $results->count()
        ]);

        return $results;
    }

    /**
     * Search products with MySQL (JSON query)
     */
    protected function searchWithMySQL(array $keywords, int $limit)
    {
        Log::info('🔍 MySQL: Searching', [
            'keywords' => $keywords,
            'limit' => $limit
        ]);

        $query = ShopProduct::query();

        // Search in title (JSON column)
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) LIKE ?", ["%{$keyword}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$keyword}%"]);
            }
        });

        // Only products with stock
        $query->where('current_stock', '>', 0);

        $results = $query->limit($limit)->get();

        Log::info('✅ MySQL: Results found', [
            'count' => $results->count()
        ]);

        return $results;
    }

    /**
     * Extract keywords from user message
     */
    protected function extractKeywords(string $message): array
    {
        // Common Turkish keywords for products
        $keywords = [];
        $message = mb_strtolower($message);

        // Product type keywords - genişletilmiş liste
        $productTypes = [
            'transpalet', 'forklift', 'istif', 'istif makinesi',
            'akülü', 'elektrikli', 'manuel', 'palet', 'platform',
            'kaldırıcı', 'yük', 'depo', 'lojistik', 'taşıyıcı',
            'makine', 'makina', 'ekipman', 'araç', 'ürün',
            'ixtif', 'cpd', 'ept', 'çatal', 'ton'
        ];

        // Intent keywords - bunlar da ürün araması tetikler
        $intentKeywords = ['göster', 'listele', 'bak', 'var mı', 'lazım', 'istiyorum', 'arıyorum'];
        $hasIntent = false;
        foreach ($intentKeywords as $intent) {
            if (str_contains($message, $intent)) {
                $hasIntent = true;
                break;
            }
        }

        foreach ($productTypes as $type) {
            if (str_contains($message, $type)) {
                $keywords[] = $type;
            }
        }

        // Eğer intent var ama keyword yoksa, genel ürün ara
        if ($hasIntent && empty($keywords)) {
            $keywords[] = 'transpalet'; // Default olarak transpalet göster
        }

        return array_unique($keywords);
    }
}
