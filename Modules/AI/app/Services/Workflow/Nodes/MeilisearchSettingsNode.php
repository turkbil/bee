<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;
use MeiliSearch\Client as MeiliClient;

class MeilisearchSettingsNode extends BaseNode
{
    public function execute(array $context): array
    {
        $userMessage = $context['user_message'] ?? '';

        // Extract search parameters
        $searchQuery = $this->extractSearchQuery($userMessage);
        $filters = $this->extractFilters($userMessage, $context);
        $limit = $this->getConfig('search_limit', 10);

        Log::info('🔍 MeilisearchSettingsNode: Searching', [
            'query' => $searchQuery,
            'filters' => $filters,
            'limit' => $limit
        ]);

        // Build Meilisearch filter string
        $filterParts = ['is_active = true'];

        // ✅ KURAL: Fiyatsız ve stoksuz ürünleri de göster
        // AI prompt'unda "Fiyat için temsilciye ulaşın" mesajı verecek

        // Not: exclude_out_of_stock config'i artık kullanılmıyor
        // Tüm ürünler gösterilecek, AI prompt'u stok durumunu açıklayacak

        // Add custom filters from context
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (str_starts_with($field, '_')) continue; // Skip hints

                if (is_array($value) && count($value) == 2 && in_array($value[0], ['<', '>', '<=', '>='])) {
                    // Range filter: ['<', 5000]
                    $filterParts[] = "{$field} {$value[0]} {$value[1]}";
                } elseif (is_array($value)) {
                    // IN filter: [1, 2, 3]
                    $filterParts[] = $field . ' IN [' . implode(', ', $value) . ']';
                } else {
                    // Equality: = "value"
                    $filterParts[] = is_numeric($value)
                        ? "{$field} = {$value}"
                        : "{$field} = \"{$value}\"";
                }
            }
        }

        $filterString = implode(' AND ', $filterParts);

        // Use raw Meilisearch client for proper filter support
        $client = new MeiliClient(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $indexName = tenancy()->initialized
            ? 'shop_products_tenant_' . tenant('id')
            : 'shop_products';

        $searchResults = $client->index($indexName)->search($searchQuery, [
            'filter' => $filterString,
            'limit' => $limit
        ]);

        // Convert SearchResult object to array
        $hits = $searchResults->getHits();

        // Convert to Eloquent models
        $productIds = collect($hits)->pluck('product_id');
        $products = ShopProduct::whereIn('product_id', $productIds)
            ->get()
            ->sortBy(function($product) use ($productIds) {
                return $productIds->search($product->product_id);
            })
            ->values();

        Log::info('✅ MeilisearchSettingsNode: Results', [
            'found' => $products->count(),
            'query' => $searchQuery,
            'filter_string' => $filterString,
            'applied_filters' => $filters,
            'total_hits' => count($hits)
        ]);

        return [
            'products' => $products,
            'products_found' => $products->count(),
            'search_query' => $searchQuery,
            'applied_filters' => $filters
        ];
    }

    /**
     * Extract search query from user message
     * Keep it simple - Meilisearch has built-in stop-words handling
     */
    protected function extractSearchQuery(string $message): string
    {
        // Just return message as-is, Meilisearch will handle the rest
        return trim($message);
    }

    /**
     * Extract filters from user message
     */
    protected function extractFilters(string $message, array $context): array
    {
        $filters = [];
        $message = mb_strtolower($message);

        // Price filters
        if (preg_match('/(\d+)\s*(?:bin|k|bin tl|tl)\s*(?:altı|altında|kadar|arası)/', $message, $matches)) {
            $maxPrice = (int)$matches[1];
            if ($maxPrice < 1000) {
                $maxPrice *= 1000; // "10 bin" -> 10000
            }
            $filters['base_price'] = ['<', $maxPrice];
        }

        // Capacity filter (ton)
        if (preg_match('/(\d+(?:\.\d+)?)\s*ton/', $message, $matches)) {
            $capacity = (float)$matches[1];
            // Store in context for AI to use
            $filters['_capacity_hint'] = $capacity;
        }

        // Brand filter
        if (str_contains($message, 'toyota')) {
            $filters['brand_name'] = 'Toyota';
        } elseif (str_contains($message, 'linde')) {
            $filters['brand_name'] = 'Linde';
        } elseif (str_contains($message, 'still')) {
            $filters['brand_name'] = 'Still';
        }

        // Electric/Manuel filter
        if (str_contains($message, 'elektrikli') || str_contains($message, 'akülü')) {
            $filters['_type_hint'] = 'electric';
        } elseif (str_contains($message, 'manuel')) {
            $filters['_type_hint'] = 'manual';
        }

        // ✅ CATEGORY BOUNDARY - Kategori tespit edildiyse SADECE o kategoriden ürün göster
        if (isset($context['detected_category'])) {
            $categorySlug = $context['detected_category'];

            // Kategori slug → title keyword mapping (tenant-agnostic)
            $categoryKeywords = [
                'transpalet' => 'transpalet',
                'forklift' => 'forklift',
                'stacker' => 'istif',
            ];

            if (isset($categoryKeywords[$categorySlug])) {
                $keyword = $categoryKeywords[$categorySlug];

                // Database'den tenant-specific category ID bul
                try {
                    $category = \Modules\Shop\App\Models\ShopCategory::where('is_active', true)
                        ->where(function($q) use ($keyword) {
                            $q->where('title->tr', 'like', '%' . $keyword . '%')
                              ->orWhere('title->en', 'like', '%' . $keyword . '%');
                        })
                        ->first();

                    if ($category) {
                        // Gerçek Meilisearch filter olarak ekle
                        $filters['category_id'] = $category->category_id;

                        Log::info('🎯 Kategori Boundary Aktif', [
                            'detected' => $categorySlug,
                            'keyword' => $keyword,
                            'category_id' => $category->category_id,
                            'category_title' => $category->getTranslated('title', 'tr')
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Category boundary lookup failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return $filters;
    }

    /**
     * Configure Meilisearch settings (one-time setup)
     */
    public static function configureMeilisearch(): void
    {
        // Get tenant-aware index name
        $indexName = tenancy()->initialized
            ? 'shop_products_tenant_' . tenant('id')
            : 'shop_products';

        $client = new MeiliClient(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index($indexName);

        // Searchable attributes (order matters - priority)
        $index->updateSearchableAttributes([
            'title',                          // Highest priority - Ürün adı
            'model_number',                   // Model numarası
            'sku',                           // Stok kodu
            'brand_name',                    // Marka adı
            'category_name',                 // Kategori adı
            'technical_specs_text',          // Teknik özellikler (voltaj, kapasite, vs.)
            'features_text',                 // Özellikler
            'highlighted_features_text',     // Öne çıkan özellikler
            'primary_specs_text',            // Ana özellikler
            'use_cases_text',                // Kullanım alanları
            'target_industries_text',        // Hedef sektörler
            'competitive_advantages_text',   // Rekabetçi avantajlar
            'accessories_text',              // Aksesuarlar
            'certifications_text',           // Sertifikalar
            'warranty_info_text',            // Garanti bilgisi
            'shipping_info_text',            // Kargo bilgisi
            'dimensions_text',               // Boyutlar
            'description',                   // Kısa açıklama
            'tags',                          // Etiketler
            'body'                           // Detaylı açıklama (Lowest priority)
        ]);

        // Filterable attributes
        $index->updateFilterableAttributes([
            'category_id',
            'brand_id',
            'base_price',
            'current_stock',
            'is_active',
            'is_featured',
            'price_on_request'
        ]);

        // Sortable attributes
        $index->updateSortableAttributes([
            'base_price',
            'current_stock',
            'created_at'
        ]);

        // Ranking rules (order matters)
        $index->updateRankingRules([
            'words',           // Number of matched words
            'typo',            // Typo tolerance
            'proximity',       // Word proximity
            'attribute',       // Attribute ranking order
            'sort',            // Custom sort
            'exactness',       // Exact match priority
            'base_price:asc'  // Cheaper products first
        ]);

        // Typo tolerance
        $index->updateTypoTolerance([
            'enabled' => true,
            'minWordSizeForTypos' => [
                'oneTypo' => 4,   // 4 harfli kelimede 1 typo
                'twoTypos' => 8   // 8 harfli kelimede 2 typo
            ]
        ]);

        Log::info('✅ Meilisearch settings configured', [
            'index' => $indexName,
            'tenant_id' => tenant('id')
        ]);
    }
}
