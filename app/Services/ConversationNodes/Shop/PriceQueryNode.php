<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Price Query Node
 *
 * Handles price-specific queries:
 * - "en ucuz", "en pahalı", "fiyat nedir", etc.
 * - Fetches from DATABASE (not Meilisearch - sync issue)
 * - Shows KDV HARİÇ prices
 * - Excludes specific categories (e.g., spare parts)
 */
class PriceQueryNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $excludeCategories = $this->getConfig('exclude_categories', [44]); // Yedek parça
        $limit = $this->getConfig('limit', 5);
        $showVat = $this->getConfig('show_vat', false);
        $vatRate = $this->getConfig('vat_rate', 20);

        try {
            // Detect query type
            $queryType = $this->detectQueryType($userMessage);

            // Fetch products
            $products = $this->fetchProductsByPrice($queryType, $excludeCategories, $limit);

            if (empty($products)) {
                return $this->handleNoProductsFound($queryType);
            }

            // Add VAT info if needed
            if ($showVat) {
                $products = $this->addVatPrices($products, $vatRate);
            }

            // Store in context
            $conversation->addToContext('price_query_results', $products);
            $conversation->addToContext('price_query_type', $queryType);

            $this->log('info', 'Price query executed', [
                'conversation_id' => $conversation->id,
                'query_type' => $queryType,
                'found_count' => count($products),
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                [
                    'products' => $products,
                    'query_type' => $queryType,
                    'show_vat' => $showVat,
                    'vat_rate' => $vatRate,
                    'vat_note' => 'Fiyatlarımız KDV hariçtir. KDV sonradan eklenir.',
                ],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Price query failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Price query failed: ' . $e->getMessage());
        }
    }

    protected function detectQueryType(string $message): string
    {
        $message = mb_strtolower($message);

        if (preg_match('/(en\s+ucuz|en\s+uygun|en\s+düşük)/i', $message)) {
            return 'cheapest';
        }

        if (preg_match('/(en\s+pahal[ıi]|en\s+yüksek)/i', $message)) {
            return 'expensive';
        }

        // Specific product price query
        if (preg_match('/\b([A-Z]{1,3}\d{1,3}[A-Z]*\d*[A-Z]*)\b/i', $message)) {
            return 'specific_product';
        }

        return 'general';
    }

    protected function fetchProductsByPrice(string $queryType, array $excludeCategories, int $limit): array
    {
        $query = \Modules\Shop\App\Models\ShopProduct::whereNotNull('base_price')
            ->where('base_price', '>', 0)
            ->where('is_active', true)
            ->whereNotIn('category_id', $excludeCategories)
            ->with(['category', 'media']);

        // Apply sorting based on query type
        switch ($queryType) {
            case 'cheapest':
                $query->orderBy('base_price', 'asc');
                break;
            case 'expensive':
                $query->orderBy('base_price', 'desc');
                break;
            case 'general':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('stock', 'desc');
                break;
        }

        $products = $query->limit($limit)->get();

        return $this->formatProducts($products);
    }

    protected function formatProducts($products): array
    {
        return $products->map(function($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'base_price' => $product->base_price,
                'base_price_formatted' => number_format($product->base_price, 0, ',', '.') . ' TL',
                'category' => $product->category->title ?? 'Uncategorized',
                'stock' => $product->stock ?? 0,
                'url' => "/shop/{$product->slug}",
                'image' => $product->media->first()?->getUrl() ?? null,
            ];
        })->toArray();
    }

    protected function addVatPrices(array $products, int $vatRate): array
    {
        return array_map(function($product) use ($vatRate) {
            $vatAmount = $product['base_price'] * ($vatRate / 100);
            $totalPrice = $product['base_price'] + $vatAmount;

            $product['vat_amount'] = $vatAmount;
            $product['total_price'] = $totalPrice;
            $product['total_price_formatted'] = number_format($totalPrice, 0, ',', '.') . ' TL';

            return $product;
        }, $products);
    }

    protected function handleNoProductsFound(string $queryType): array
    {
        $this->log('warning', 'No products found for price query', [
            'query_type' => $queryType,
        ]);

        $phone = settings()->get('contact_info.phone', '');
        $whatsapp = settings()->get('contact_info.whatsapp', '');

        $nextNode = $this->getConfig('no_products_next_node') ?? $this->getConfig('next_node');

        return $this->success(
            null,
            [
                'no_products_found' => true,
                'query_type' => $queryType,
                'contact_phone' => $phone,
                'contact_whatsapp' => $whatsapp,
            ],
            $nextNode
        );
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'price_query';
    }

    public static function getName(): string
    {
        return 'Fiyat Sorgusu';
    }

    public static function getDescription(): string
    {
        return 'Fiyat bazlı sorguları işler (en ucuz, en pahalı, vb.)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'exclude_categories' => [
                'type' => 'array',
                'label' => 'Hariç Tutulan Kategoriler',
                'default' => [44],
                'help' => 'Kategori ID\'leri (virgülle ayır). Örn: 44 (Yedek Parça)',
            ],
            'limit' => [
                'type' => 'number',
                'label' => 'Sonuç Limiti',
                'min' => 1,
                'max' => 20,
                'default' => 5,
            ],
            'show_vat' => [
                'type' => 'boolean',
                'label' => 'KDV Dahil Fiyat Göster',
                'default' => false,
                'help' => 'Şu anda KDV hariç gösteriliyor',
            ],
            'vat_rate' => [
                'type' => 'number',
                'label' => 'KDV Oranı (%)',
                'min' => 0,
                'max' => 100,
                'default' => 20,
                'depends_on' => 'show_vat',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
            ],
            'no_products_next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node (Ürün Yoksa)',
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
            ['id' => 'found', 'label' => 'Fiyat Bulundu'],
            ['id' => 'not_found', 'label' => 'Ürün Yok'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-currency-lira';
    }
}
