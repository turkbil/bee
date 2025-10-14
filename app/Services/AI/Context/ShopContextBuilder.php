<?php

namespace App\Services\AI\Context;

use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Http\Controllers\Front\ShopController;
use Illuminate\Support\Facades\Cache;

/**
 * Shop Context Builder
 *
 * Shop mod�l�nden AI i�in context olu_turur.
 * Product, category ve variant bilgilerini haz1rlar.
 */
class ShopContextBuilder
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * �r�n ID'sine g�re context olu_tur
     */
    public function buildProductContext(int $productId): array
    {
        $product = ShopProduct::with(['category', 'childProducts', 'parentProduct'])
            ->find($productId);

        if (!$product) {
            return [];
        }

        return [
            'page_type' => 'product',
            'current_product' => $this->formatProduct($product),
            'variants' => $this->formatVariants($product),
            'category' => $this->formatCategory($product->category),
        ];
    }

    /**
     * Kategori ID'sine g�re context olu_tur
     */
    public function buildCategoryContext(int $categoryId): array
    {
        $category = ShopCategory::with(['products', 'parent'])
            ->find($categoryId);

        if (!$category) {
            return [];
        }

        $products = $category->products()->where('is_active', true)->take(20)->get();

        return [
            'page_type' => 'category',
            'current_category' => $this->formatCategory($category),
            'products' => $products->map(fn($p) => $this->formatProduct($p))->toArray(),
            'product_count' => $category->products()->where('is_active', true)->count(),
        ];
    }

    /**
     * Genel shop bilgisi (kategori listesi vb.)
     * 🚀 CACHED: 1 saat
     */
    public function buildGeneralShopContext(): array
    {
        $tenantId = tenant('id');

        // FIX: If no tenant context, return empty (central domain için güvenlik)
        if (!$tenantId) {
            \Log::warning('⚠️ ShopContextBuilder: No tenant context found', [
                'tenant_id' => $tenantId,
                'request_url' => request()->url(),
                'request_host' => request()->getHost(),
            ]);

            return [
                'page_type' => 'shop_general',
                'categories' => [],
                'featured_products' => [],
                'all_products' => [],
                'total_products' => 0,
                'tenant_rules' => ['category_priority' => ['enabled' => false], 'faq_enabled' => false, 'token_limits' => ['products_max' => 30]],
            ];
        }

        \Log::info('✅ ShopContextBuilder: Building context for tenant', [
            'tenant_id' => $tenantId,
            'locale' => $this->locale,
        ]);

        $cacheKey = "shop_context_{$tenantId}_{$this->locale}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            // Load tenant-specific rules
            $tenantRules = $this->getTenantRules($tenantId);
            $productLimit = $tenantRules['token_limits']['products_max'] ?? 30;

            $categories = ShopCategory::whereNull('parent_id')
                ->where('is_active', true)
                ->with('children')
                ->get();

            $featuredProducts = ShopProduct::where('is_featured', true)
                ->where('is_active', true)
                ->take(10)
                ->get();

            // Get ALL active products with category priority filtering
            $allProductsQuery = ShopProduct::where('shop_products.is_active', true)
                ->select([
                    'shop_products.product_id',
                    'shop_products.sku',
                    'shop_products.title',
                    'shop_products.slug',
                    'shop_products.short_description',
                    'shop_products.category_id',
                    'shop_products.base_price',
                    'shop_products.price_on_request',
                    'shop_products.faq_data'
                ])
                ->with('category:category_id,title,slug');

            // Apply category priority if enabled (tenant-specific)
            if ($tenantRules['category_priority']['enabled'] ?? false) {
                $allProductsQuery = $this->applyCategoryPriority($allProductsQuery, $tenantRules);
            }

            $allProducts = $allProductsQuery->take($productLimit)->get();

            return [
                'page_type' => 'shop_general',
                'categories' => $categories->map(fn($c) => [
                    'id' => $c->category_id,
                    'name' => $this->translate($c->title),
                    'slug' => $this->translate($c->slug),
                    'description' => $this->sanitize($this->translate($c->description), 200),
                    'url' => $this->getCategoryUrl($c),
                    'product_count' => $c->products()->where('is_active', true)->count(),
                    'priority' => $this->getCategoryPriority($c, $tenantRules),
                    'subcategories' => $c->children->map(fn($sc) => [
                        'id' => $sc->category_id,
                        'name' => $this->translate($sc->title),
                        'description' => $this->sanitize($this->translate($sc->description), 150),
                        'url' => $this->getCategoryUrl($sc),
                        'priority' => $this->getCategoryPriority($sc, $tenantRules),
                    ])->toArray(),
                ])->toArray(),
                'featured_products' => $featuredProducts->map(fn($p) => $this->formatProduct($p))->toArray(),
                'all_products' => $allProducts->map(fn($p) => $this->formatProductSummary($p, $tenantRules))->toArray(),
                'total_products' => $allProducts->count(),
                'tenant_rules' => $tenantRules, // Include rules for AI prompt
            ];
        });
    }

    /**
     * Get tenant-specific rules from config
     */
    protected function getTenantRules(int $tenantId): array
    {
        $tenantRules = config('ai-tenant-rules', []);

        // Find tenant config by ID
        foreach ($tenantRules as $key => $rules) {
            if (isset($rules['tenant_id']) && $rules['tenant_id'] === $tenantId) {
                return $rules;
            }
        }

        // Return default rules if tenant not found
        return $tenantRules['default'] ?? [
            'category_priority' => ['enabled' => false],
            'faq_enabled' => false,
            'token_limits' => ['products_max' => 30],
        ];
    }

    /**
     * Apply category priority filtering to product query
     */
    protected function applyCategoryPriority($query, array $tenantRules)
    {
        $highPriority = $tenantRules['category_priority']['high_priority'] ?? [];
        $lowPriority = $tenantRules['category_priority']['low_priority'] ?? [];

        if (empty($highPriority) && empty($lowPriority)) {
            return $query;
        }

        // Join with categories to filter by slug
        $query->join('shop_categories', 'shop_products.category_id', '=', 'shop_categories.category_id')
            ->where('shop_categories.is_active', true);

        // Prioritize: high priority first, then others, low priority last
        $query->orderByRaw("
            CASE
                WHEN shop_categories.slug IN ('" . implode("','", $highPriority) . "') THEN 1
                WHEN shop_categories.slug IN ('" . implode("','", $lowPriority) . "') THEN 3
                ELSE 2
            END
        ");

        return $query;
    }

    /**
     * Get category priority level (for display purposes)
     */
    protected function getCategoryPriority(ShopCategory $category, array $tenantRules): string
    {
        if (!($tenantRules['category_priority']['enabled'] ?? false)) {
            return 'normal';
        }

        $slug = $this->translate($category->slug);
        $highPriority = $tenantRules['category_priority']['high_priority'] ?? [];
        $lowPriority = $tenantRules['category_priority']['low_priority'] ?? [];

        if (in_array($slug, $highPriority)) {
            return 'high';
        }

        if (in_array($slug, $lowPriority)) {
            return 'low';
        }

        return 'normal';
    }

    /**
     * Format product summary (lightweight version for listing)
     */
    protected function formatProductSummary(ShopProduct $product, array $tenantRules = []): array
    {
        $summary = [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $this->translate($product->title),
            'short_description' => $this->translate($product->short_description),
            'category' => $product->category ? $this->translate($product->category->title) : null,
            'price' => $this->formatPrice($product),
            'url' => $this->getProductUrl($product),
        ];

        // Add FAQ if enabled for this tenant
        if ($tenantRules['faq_enabled'] ?? false) {
            $faqLimit = $tenantRules['faq_limit'] ?? 10;
            $faqData = $product->faq_data;

            if (!empty($faqData) && is_array($faqData)) {
                $summary['faq'] = array_slice($faqData, 0, $faqLimit);
            }
        }

        return $summary;
    }

    /**
     * �r�n� AI i�in formatla
     */
    protected function formatProduct(ShopProduct $product): array
    {
        return [
            // Basic info
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $this->translate($product->title),
            'slug' => $this->translate($product->slug),
            'url' => $this->getProductUrl($product),
            'short_description' => $this->translate($product->short_description),
            'body' => $this->sanitize($this->translate($product->body), 500),
            'meta_title' => $this->translate($product->meta_title ?? $product->title),
            'meta_description' => $this->translate($product->meta_description ?? $product->short_description),
            'meta_keywords' => $product->meta_keywords,

            // Price info
            'price' => $this->formatPrice($product),
            'base_price' => $product->base_price,
            'compare_at_price' => $product->compare_at_price,
            'price_on_request' => $product->price_on_request,
            'currency' => $product->currency,

            // Stock & inventory
            'stock_quantity' => $product->stock_quantity ?? null,
            'stock_status' => $product->stock_status ?? null,
            'low_stock_threshold' => $product->low_stock_threshold ?? null,
            'manage_stock' => $product->manage_stock ?? false,
            'allow_backorder' => $product->allow_backorder ?? false,
            'lead_time_days' => $product->lead_time_days ?? null,

            // Technical specifications (original JSON fields)
            'technical_specs' => $product->technical_specs,
            'features' => $product->features,
            'highlighted_features' => $product->highlighted_features,
            'primary_specs' => $product->primary_specs,

            // Custom JSON fields (tenant-specific)
            'custom_technical_specs' => $product->custom_technical_specs ?? null,
            'custom_features' => $product->custom_features ?? null,
            'custom_certifications' => $product->custom_certifications ?? null,

            // Marketing content
            'use_cases' => $product->use_cases,
            'competitive_advantages' => $product->competitive_advantages,
            'target_industries' => $product->target_industries,

            // FAQ
            'faq' => $product->faq_data,

            // Additional info
            'accessories' => $product->accessories ?? null,
            'certifications' => $product->certifications,
            'warranty_info' => $product->warranty_info,
            'specifications' => $product->specifications ?? null,

            // Physical properties
            'weight' => $product->weight ?? null,
            'weight_unit' => $product->weight_unit ?? null,
            'dimensions' => $product->dimensions ?? null,
            'dimension_unit' => $product->dimension_unit ?? null,

            // Taxonomy
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id ?? null,
            'tags' => $product->tags ?? null,

            // Status flags
            'is_featured' => $product->is_featured,
            'is_master_product' => $product->is_master_product,
            'allow_reviews' => $product->allow_reviews ?? false,

            // Variant info
            'is_variant' => !empty($product->parent_product_id),
            'parent_product_id' => $product->parent_product_id,
            'variant_type' => $product->variant_type,
            'has_variants' => $product->is_master_product || ($product->childProducts && $product->childProducts->count() > 0),

            // SEO & visibility
            'visibility' => $product->visibility ?? null,
            'sort_order' => $product->sort_order ?? null,
            'published_at' => $product->published_at ?? null,
        ];
    }


    /**
     * Varyantlar1 formatla
     */
    protected function formatVariants(ShopProduct $product): array
    {
        // Eer bu bir varyant ise, karde_ varyantlar1 getir
        if ($product->parent_product_id) {
            $siblings = ShopProduct::where('parent_product_id', $product->parent_product_id)
                ->where('product_id', '!=', $product->product_id)
                ->where('is_active', true)
                ->get();

            return $siblings->map(fn($v) => [
                'id' => $v->product_id,
                'title' => $this->translate($v->title),
                'sku' => $v->sku,
                'url' => $this->getProductUrl($v),
                'variant_type' => $v->variant_type,
                'price' => $this->formatPrice($v),
                'key_differences' => $this->extractKeyDifferences($v, $product),
            ])->toArray();
        }

        // Eer master product ise, child varyantlar1 getir
        if ($product->is_master_product || $product->childProducts->count() > 0) {
            return $product->childProducts->map(fn($v) => [
                'id' => $v->product_id,
                'title' => $this->translate($v->title),
                'sku' => $v->sku,
                'url' => $this->getProductUrl($v),
                'variant_type' => $v->variant_type,
                'price' => $this->formatPrice($v),
                'key_differences' => $this->extractKeyDifferences($v, $product),
            ])->toArray();
        }

        return [];
    }

    /**
     * Kategoriyi formatla
     */
    protected function formatCategory(?ShopCategory $category): ?array
    {
        if (!$category) {
            return null;
        }

        return [
            'id' => $category->category_id,
            'name' => $this->translate($category->title),
            'slug' => $this->translate($category->slug),
            'url' => $this->getCategoryUrl($category),
            'description' => $this->sanitize($this->translate($category->description), 300),
            'product_count' => $category->products()->where('is_active', true)->count(),
        ];
    }

    /**
     * Fiyat bilgisini formatla
     */
    protected function formatPrice(ShopProduct $product): array
    {
        if ($product->price_on_request) {
            return [
                'available' => false,
                'on_request' => true,
                'message' => 'Fiyat sorunuz i�in l�tfen ileti_ime ge�in',
            ];
        }

        if ($product->base_price) {
            return [
                'available' => true,
                'amount' => $product->base_price,
                'formatted' => number_format($product->base_price, 2, ',', '.') . ' ' . ($product->currency ?? 'TRY'),
                'compare_at' => $product->compare_at_price,
            ];
        }

        return [
            'available' => false,
            'on_request' => false,
            'message' => 'Fiyat bilgisi mevcut deil',
        ];
    }

    /**
     * Varyant key differences �1kar
     */
    protected function extractKeyDifferences(ShopProduct $variant, ShopProduct $reference): ?string
    {
        // Variant type'dan fark �1kar
        if ($variant->variant_type && $reference->variant_type) {
            return "Varyant: " . str_replace('-', ' ', ucfirst($variant->variant_type));
        }

        // Short description'dan fark �1kar
        $variantDesc = $this->translate($variant->short_description);
        $referenceDesc = $this->translate($reference->short_description);

        if ($variantDesc && $variantDesc !== $referenceDesc) {
            return mb_substr($variantDesc, 0, 100);
        }

        return null;
    }

    /**
     * Product URL olu_tur
     */
    protected function getProductUrl(ShopProduct $product): string
    {
        try {
            $url = ShopController::resolveProductUrl($product, $this->locale);

            // DEBUG: Log URL to check for issues
            if (str_contains($product->sku ?? '', 'JX1')) {
                \Log::info('🔗 getProductUrl() - JX1 URL generated', [
                    'sku' => $product->sku,
                    'slug' => $this->translate($product->slug),
                    'url' => $url,
                    'method' => 'ShopController::resolveProductUrl',
                ]);
            }

            return $url;
        } catch (\Exception $e) {
            // FIX: Explicit concatenation to prevent URL merge issues
            $slug = ltrim($this->translate($product->slug), '/');
            $url = url('/shop/' . $slug);

            // DEBUG: Log fallback URL
            if (str_contains($product->sku ?? '', 'JX1')) {
                \Log::info('🔗 getProductUrl() - JX1 Fallback URL', [
                    'sku' => $product->sku,
                    'slug' => $slug,
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }

            return $url;
        }
    }

    /**
     * Category URL olu_tur
     */
    protected function getCategoryUrl(ShopCategory $category): string
    {
        $slug = ltrim($this->translate($category->slug), '/');
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $this->locale !== $defaultLocale ? '/' . $this->locale : '';

        // FIX: Explicit concatenation to prevent URL merge issues
        return url($localePrefix . '/shop/category/' . $slug);
    }

    /**
     * JSON multi-language �eviri
     */
    protected function translate($data): string
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            $defaultLocale = get_tenant_default_locale();
            return $data[$this->locale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data) ?? '';
        }

        return '';
    }

    /**
     * HTML i�erii temizle
     */
    protected function sanitize(?string $content, int $limit = 0): string
    {
        if (empty($content)) {
            return '';
        }

        $cleaned = strip_tags($content);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        if ($limit > 0 && mb_strlen($cleaned) > $limit) {
            $cleaned = mb_substr($cleaned, 0, $limit) . '...';
        }

        return $cleaned;
    }
}
