<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Product Search Node
 *
 * 🚨 KRITIK: HALÜSİNASYON YASAK!
 * - SADECE veritabanındaki ürünleri göster
 * - ASLA dünyadan örnek verme
 * - Ürün yoksa müşteri temsilcisine yönlendir
 *
 * Searches products using Meilisearch (with DB fallback)
 * Applies stock sorting: Featured → High Stock → Normal
 */
class ProductSearchNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $searchLimit = $this->getConfig('search_limit', 3);
        $useMeilisearch = $this->getConfig('use_meilisearch', true);
        $sortByStock = $this->getConfig('sort_by_stock', true);

        try {
            // Extract search query from user message
            $searchQuery = $this->extractSearchQuery($userMessage, $conversation);

            // Search products
            $products = $useMeilisearch
                ? $this->searchWithMeilisearch($searchQuery, $searchLimit)
                : $this->searchWithDatabase($searchQuery, $searchLimit);

            // Apply stock sorting if enabled
            if ($sortByStock) {
                $products = $this->applySockSorting($products);
            }

            // Limit results
            $products = array_slice($products, 0, $searchLimit);

            // 🚨 HALÜSİNASYON CHECK: Ürün yoksa yönlendir
            if (empty($products)) {
                \Log::emergency('🚨 ProductSearch: NO PRODUCTS FOUND', [
                    'query' => $searchQuery,
                    'user_message' => $userMessage,
                ]);
                return $this->handleNoProductsFound($searchQuery);
            }

            // 🚨 SONNET DEBUG: Log products found
            \Log::emergency('🚨 ProductSearch: PRODUCTS FOUND', [
                'query' => $searchQuery,
                'count' => count($products),
                'products' => array_map(fn($p) => [
                    'id' => $p['id'],
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'price' => $p['base_price'],
                ], $products),
            ]);

            // Store products in conversation context
            $conversation->addToContext('searched_products', $products);
            $conversation->addToContext('search_query', $searchQuery);

            $this->log('info', 'Products found', [
                'conversation_id' => $conversation->id,
                'query' => $searchQuery,
                'found_count' => count($products),
                'method' => $useMeilisearch ? 'meilisearch' : 'database',
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                [
                    'products' => $products,
                    'search_query' => $searchQuery,
                    'found_count' => count($products),
                ],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Product search failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->failure('Product search failed: ' . $e->getMessage());
        }
    }

    protected function extractSearchQuery(string $userMessage, AIConversation $conversation): string
    {
        // Remove common question words
        $query = preg_replace('/\b(istiyorum|arıyorum|almak|var\s*mı|göster|bul)\b/i', '', $userMessage);

        // Clean up
        $query = trim($query);

        return $query;
    }

    protected function searchWithMeilisearch(string $query, int $limit): array
    {
        try {
            // Use Meilisearch (assuming ProductSearchService exists)
            if (class_exists('\App\Services\AI\ProductSearchService')) {
                $service = app(\App\Services\AI\ProductSearchService::class);
                $results = $service->searchProducts($query, ['limit' => $limit * 2]); // Get more for sorting

                return $this->formatProducts($results);
            }
        } catch (\Exception $e) {
            $this->log('warning', 'Meilisearch failed, falling back to DB', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to database
        return $this->searchWithDatabase($query, $limit);
    }

    protected function searchWithDatabase(string $query, int $limit): array
    {
        $locale = app()->getLocale();

        $products = \Modules\Shop\App\Models\ShopProduct::where('is_active', true)
            ->where(function($q) use ($query, $locale) {
                // JSON search for title and body (multilingual fields)
                $q->where("title->{$locale}", 'LIKE', "%{$query}%")
                  ->orWhere("body->{$locale}", 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'media'])
            ->limit($limit * 2)
            ->get();

        return $this->formatProducts($products);
    }

    protected function formatProducts($products): array
    {
        return collect($products)->map(function($product) {
            // Get translated title
            $title = is_array($product->title)
                ? ($product->title[app()->getLocale()] ?? $product->title['en'] ?? 'Untitled')
                : $product->title;

            // Get translated slug
            $slug = is_array($product->slug)
                ? ($product->slug[app()->getLocale()] ?? $product->slug['en'] ?? 'unknown')
                : $product->slug;

            // Get translated category title
            $categoryTitle = 'Uncategorized';
            if ($product->category) {
                $categoryTitle = is_array($product->category->title)
                    ? ($product->category->title[app()->getLocale()] ?? $product->category->title['en'] ?? 'Uncategorized')
                    : $product->category->title;
            }

            // Get description safely
            $description = null;
            if (!empty($product->body)) {
                $description = is_array($product->body)
                    ? ($product->body[app()->getLocale()] ?? $product->body['en'] ?? null)
                    : $product->body;
                if ($description) {
                    $description = strip_tags(substr($description, 0, 200));
                }
            }

            return [
                'id' => $product->product_id ?? $product->id,
                'title' => $title,
                'slug' => $slug,
                'base_price' => $product->base_price ?? 0,
                'currency' => $product->currency ?? 'TRY',  // Currency ekle
                'description' => $description,
                'category' => $categoryTitle,
                'category_id' => $product->category_id,
                'stock' => $product->current_stock ?? 0,
                'is_featured' => $product->is_featured ?? false,
                'image' => $product->media->first()?->getUrl() ?? null,
                'url' => "/shop/{$slug}",
            ];
        })->toArray();
    }

    protected function applySockSorting(array $products): array
    {
        // Sort: Featured → High Stock (>10) → Normal Stock
        usort($products, function($a, $b) {
            // 1. Featured first
            if ($a['is_featured'] && !$b['is_featured']) return -1;
            if (!$a['is_featured'] && $b['is_featured']) return 1;

            // 2. High stock (>10)
            $aHighStock = $a['stock'] > 10;
            $bHighStock = $b['stock'] > 10;
            if ($aHighStock && !$bHighStock) return -1;
            if (!$aHighStock && $bHighStock) return 1;

            // 3. Stock amount
            return $b['stock'] - $a['stock'];
        });

        return $products;
    }

    /**
     * 🚨 HALÜSİNASYON YASAK Handler
     */
    protected function handleNoProductsFound(string $query): array
    {
        $this->log('warning', 'No products found - redirecting to support', [
            'query' => $query,
        ]);

        // Get contact info from settings
        $phone = settings()->get('contact_info.phone', '');
        $whatsapp = settings()->get('contact_info.whatsapp', '');

        $nextNode = $this->getConfig('no_products_next_node') ?? $this->getConfig('next_node');

        return $this->success(
            null,
            [
                'no_products_found' => true,
                'search_query' => $query,
                'contact_phone' => $phone,
                'contact_whatsapp' => $whatsapp,
                'support_message' => "Bu özellikte ürünümüz şu anda bulunmamaktadır. Müşteri temsilcilerimiz size yardımcı olabilir.",
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
        return 'product_search';
    }

    public static function getName(): string
    {
        return 'Ürün Ara';
    }

    public static function getDescription(): string
    {
        return 'Meilisearch/DB ile ürün arar (HALÜSİNASYON YASAK!)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'search_limit' => [
                'type' => 'number',
                'label' => 'Sonuç Limiti',
                'min' => 1,
                'max' => 10,
                'default' => 3,
                'help' => 'Kaç ürün gösterilsin',
            ],
            'use_meilisearch' => [
                'type' => 'boolean',
                'label' => 'Meilisearch Kullan',
                'default' => true,
                'help' => 'Kapalıysa direkt DB sorgusu',
            ],
            'sort_by_stock' => [
                'type' => 'boolean',
                'label' => 'Stok Sıralaması',
                'default' => true,
                'help' => 'Featured → Yüksek Stok → Normal',
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node (Ürün Bulunca)',
            ],
            'no_products_next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node (Ürün Yoksa)',
                'help' => 'Boş bırakılırsa normal next_node kullanılır',
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
            ['id' => 'found', 'label' => 'Ürün Bulundu'],
            ['id' => 'not_found', 'label' => 'Ürün Yok'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-search';
    }
}
