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

        Log::info('🔍 ProductSearchNode: Searching products', [
            'user_message' => $userMessage,
            'user_message_full' => $userMessage,
            'search_limit' => $searchLimit
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

            $context['products'] = collect();
            $context['products_found'] = 0;
            return $context;
        }

        // Search products by keywords
        $query = ShopProduct::query();
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('title->tr', 'LIKE', "%{$keyword}%")
                  ->orWhere('title->en', 'LIKE', "%{$keyword}%");
            }
        });

        $products = $query->limit($searchLimit)->get();

        Log::info('✅ ProductSearchNode: Found products', [
            'keywords' => $keywords,
            'count' => $products->count()
        ]);

        // Add to context
        $context['products'] = $products;
        $context['products_found'] = $products->count();

        return $context;
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
            'makine', 'makina', 'ekipman', 'araç', 'ürün'
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
