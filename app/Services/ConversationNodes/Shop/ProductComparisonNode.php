<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Product Comparison Node
 *
 * Compares two products and shows differences + advantages
 */
class ProductComparisonNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        try {
            // Extract product IDs/slugs from message or context
            $productIds = $this->extractProductIds($userMessage, $conversation);

            if (count($productIds) < 2) {
                return $this->failure('Need at least 2 products to compare');
            }

            // Fetch products
            $products = $this->fetchProducts($productIds);

            if (count($products) < 2) {
                return $this->failure('Could not find products for comparison');
            }

            // Compare products
            $comparison = $this->compareProducts($products[0], $products[1]);

            // Store in context
            $conversation->addToContext('comparison_result', $comparison);

            $this->log('info', 'Products compared', [
                'conversation_id' => $conversation->id,
                'product_1' => $products[0]['title'],
                'product_2' => $products[1]['title'],
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                $comparison,
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Product comparison failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Product comparison failed: ' . $e->getMessage());
        }
    }

    protected function extractProductIds(string $message, AIConversation $conversation): array
    {
        $ids = [];

        // Check context first (from previous search)
        $contextProducts = $conversation->context_data['searched_products'] ?? [];
        if (count($contextProducts) >= 2) {
            return array_slice(array_column($contextProducts, 'id'), 0, 2);
        }

        // Try to extract product codes from message (F4, CPD18TVL, etc.)
        preg_match_all('/\b([A-Z]{1,3}\d{1,3}[A-Z]*\d*[A-Z]*)\b/i', $message, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $code) {
                $product = \Modules\Shop\App\Models\ShopProduct::where('title', 'LIKE', "%{$code}%")
                    ->orWhere('sku', $code)
                    ->first();

                if ($product) {
                    $ids[] = $product->id;
                }
            }
        }

        return $ids;
    }

    protected function fetchProducts(array $ids): array
    {
        $products = \Modules\Shop\App\Models\ShopProduct::whereIn('id', $ids)
            ->with(['category', 'media'])
            ->get();

        return $products->map(function($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'base_price' => $product->base_price ?? 0,
                'description' => strip_tags($product->description ?? ''),
                'category' => $product->category->title ?? '',
                'features' => $this->extractFeatures($product),
                'url' => "/shop/product/{$product->slug}",
            ];
        })->toArray();
    }

    protected function extractFeatures($product): array
    {
        // Extract features from product_features JSON or description
        $features = [];

        if (isset($product->product_features) && is_array($product->product_features)) {
            foreach ($product->product_features as $key => $value) {
                $features[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
            }
        }

        return $features;
    }

    protected function compareProducts(array $product1, array $product2): array
    {
        // Find differences
        $differences = [];

        // Price comparison
        if ($product1['base_price'] !== $product2['base_price']) {
            $cheaper = $product1['base_price'] < $product2['base_price'] ? $product1 : $product2;
            $expensive = $product1['base_price'] > $product2['base_price'] ? $product1 : $product2;

            $differences[] = [
                'aspect' => 'price',
                'product_1' => [
                    'name' => $product1['title'],
                    'value' => number_format($product1['base_price'], 0, ',', '.') . ' TL',
                    'advantage' => $product1['id'] === $cheaper['id'],
                ],
                'product_2' => [
                    'name' => $product2['title'],
                    'value' => number_format($product2['base_price'], 0, ',', '.') . ' TL',
                    'advantage' => $product2['id'] === $cheaper['id'],
                ],
            ];
        }

        // Category comparison
        if ($product1['category'] !== $product2['category']) {
            $differences[] = [
                'aspect' => 'category',
                'product_1' => ['name' => $product1['title'], 'value' => $product1['category']],
                'product_2' => ['name' => $product2['title'], 'value' => $product2['category']],
            ];
        }

        return [
            'product_1' => $product1,
            'product_2' => $product2,
            'differences' => $differences,
            'common_features' => $this->findCommonFeatures($product1, $product2),
        ];
    }

    protected function findCommonFeatures(array $product1, array $product2): array
    {
        $common = [];

        // Simple feature comparison
        $features1 = $product1['features'] ?? [];
        $features2 = $product2['features'] ?? [];

        foreach ($features1 as $feature) {
            if (in_array($feature, $features2)) {
                $common[] = $feature;
            }
        }

        return $common;
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'product_comparison';
    }

    public static function getName(): string
    {
        return 'Ürün Karşılaştır';
    }

    public static function getDescription(): string
    {
        return 'İki ürünü karşılaştırır ve farkları gösterir';
    }

    public static function getConfigSchema(): array
    {
        return [
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Karşılaştırıldı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-arrows-left-right';
    }
}
