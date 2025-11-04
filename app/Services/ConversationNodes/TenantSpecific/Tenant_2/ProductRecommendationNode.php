<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Product Recommendation Node (İxtif.com Specific)
 *
 * Fetches and recommends products based on:
 * 1. show_on_homepage = 1 (priority)
 * 2. High stock_quantity
 * 3. Category filter (if detected)
 */
class ProductRecommendationNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // Get category from context
        $detectedCategory = $conversation->getContext('detected_category');
        $categoryLocked = $conversation->getContext('category_locked', false);

        // Get config
        $limit = $this->getConfig('limit', 5);
        $includePrice = $this->getConfig('include_price', true);

        // Fetch products
        $products = $this->fetchProducts($conversation->tenant_id, $detectedCategory, $categoryLocked, $limit);

        if ($products->isEmpty()) {
            $prompt = "Üzgünüm, şu anda bu kategoride ürün bulunamadı. Başka bir kategori sorabilir misiniz?";

            return $this->success(
                $prompt,
                ['products_found' => false],
                $this->getConfig('no_products_node')
            );
        }

        // Build prompt with product list
        $prompt = $this->buildProductPrompt($products, $includePrice);

        $this->log('info', 'Products recommended', [
            'conversation_id' => $conversation->id,
            'category' => $detectedCategory,
            'product_count' => $products->count(),
        ]);

        return $this->success(
            $prompt,
            [
                'products_found' => true,
                'products' => $products->toArray(),
                'product_count' => $products->count(),
            ],
            $this->getConfig('next_node')
        );
    }

    protected function fetchProducts(int $tenantId, ?string $category, bool $categoryLocked, int $limit)
    {
        $query = \DB::table('shop_products')
            ->where('tenant_id', $tenantId)
            ->where('is_active', 1)
            ->where('status', 1);

        // Category filter
        if ($categoryLocked && $category) {
            $query->whereExists(function ($q) use ($category) {
                $q->select(\DB::raw(1))
                    ->from('shop_categories')
                    ->whereColumn('shop_categories.id', 'shop_products.category_id')
                    ->where('shop_categories.slug', 'like', "%{$category}%");
            });
        }

        // İXTİF ÖZEL: Öncelik sıralaması
        // 1. show_on_homepage = 1
        // 2. stock_quantity DESC (yüksek stok)
        // 3. sort_order ASC
        $products = $query
            ->orderByRaw('CASE WHEN show_on_homepage = 1 THEN 0 ELSE 1 END')
            ->orderBy('stock_quantity', 'DESC')
            ->orderBy('sort_order', 'ASC')
            ->limit($limit)
            ->get();

        return $products;
    }

    protected function buildProductPrompt($products, bool $includePrice): string
    {
        $lines = ["İşte size en çok tercih edilen ürünlerimiz:\n"];

        foreach ($products as $product) {
            $title = json_decode($product->title ?? '{}', true);
            $titleText = $title['tr'] ?? $title['en'] ?? 'Ürün';

            $line = "• {$titleText}";

            if ($includePrice && $product->base_price > 0) {
                $line .= " - {$product->base_price} {$product->currency}";
            }

            $lines[] = $line;
        }

        $lines[] = "\nHangi ürün hakkında daha fazla bilgi almak istersiniz?";

        return implode("\n", $lines);
    }

    public function validate(): bool
    {
        return true; // No required config
    }

    public static function getType(): string
    {
        return 'product_recommendation';
    }

    public static function getName(): string
    {
        return 'Ürün Önerme (İxtif)';
    }

    public static function getDescription(): string
    {
        return 'Anasayfa + stok öncelikli ürün önerisi yapar (İxtif.com özel)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'limit' => [
                'type' => 'number',
                'label' => 'Maksimum Ürün Sayısı',
                'min' => 1,
                'max' => 20,
                'default' => 5,
            ],
            'include_price' => [
                'type' => 'boolean',
                'label' => 'Fiyat Göster',
                'default' => true,
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
            ],
            'no_products_node' => [
                'type' => 'node_select',
                'label' => 'Ürün Yoksa',
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
            ['id' => 'output_success', 'label' => 'Ürünler Bulundu'],
            ['id' => 'output_no_products', 'label' => 'Ürün Bulunamadı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'ixtif_ecommerce';
    }

    public static function getIcon(): string
    {
        return 'ti ti-shopping-cart';
    }
}
