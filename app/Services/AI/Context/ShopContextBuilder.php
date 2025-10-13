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
        $cacheKey = "shop_context_{$tenantId}_{$this->locale}";

        return Cache::remember($cacheKey, 3600, function () {
            $categories = ShopCategory::whereNull('parent_id')
                ->where('is_active', true)
                ->with('children')
                ->get();

            $featuredProducts = ShopProduct::where('is_featured', true)
                ->where('is_active', true)
                ->take(10)
                ->get();

            // Get ALL active products (summary only - not full details)
            $allProducts = ShopProduct::where('is_active', true)
                ->select(['product_id', 'sku', 'title', 'slug', 'short_description', 'category_id', 'base_price', 'price_on_request'])
                ->with('category:category_id,title')
                ->get();

            return [
                'page_type' => 'shop_general',
                'categories' => $categories->map(fn($c) => [
                    'id' => $c->category_id,
                    'name' => $this->translate($c->title),
                    'slug' => $this->translate($c->slug),
                    'url' => $this->getCategoryUrl($c),
                    'product_count' => $c->products()->where('is_active', true)->count(),
                    'subcategories' => $c->children->map(fn($sc) => [
                        'id' => $sc->category_id,
                        'name' => $this->translate($sc->title),
                        'url' => $this->getCategoryUrl($sc),
                    ])->toArray(),
                ])->toArray(),
                'featured_products' => $featuredProducts->map(fn($p) => $this->formatProduct($p))->toArray(),
                'all_products' => $allProducts->map(fn($p) => $this->formatProductSummary($p))->toArray(),
                'total_products' => $allProducts->count(),
            ];
        });
    }

    /**
     * Format product summary (lightweight version for listing)
     */
    protected function formatProductSummary(ShopProduct $product): array
    {
        return [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $this->translate($product->title),
            'short_description' => $this->translate($product->short_description),
            'category' => $product->category ? $this->translate($product->category->title) : null,
            'price' => $this->formatPrice($product),
            'url' => $this->getProductUrl($product),
        ];
    }

    /**
     * �r�n� AI i�in formatla
     */
    protected function formatProduct(ShopProduct $product): array
    {
        return [
            'id' => $product->product_id,
            'sku' => $product->sku,
            'title' => $this->translate($product->title),
            'slug' => $this->translate($product->slug),
            'url' => $this->getProductUrl($product),
            'short_description' => $this->translate($product->short_description),
            'body' => $this->sanitize($this->translate($product->body), 500),

            // Fiyat bilgisi
            'price' => $this->formatPrice($product),

            // Teknik �zellikler
            'technical_specs' => $product->technical_specs,
            'features' => $product->features,
            'highlighted_features' => $product->highlighted_features,
            'primary_specs' => $product->primary_specs,

            // Pazarlama i�erii
            'use_cases' => $product->use_cases,
            'competitive_advantages' => $product->competitive_advantages,
            'target_industries' => $product->target_industries,

            // FAQ
            'faq' => $product->faq_data,

            // Ek bilgiler
            'certifications' => $product->certifications,
            'warranty_info' => $product->warranty_info,

            // Varyant bilgisi
            'is_variant' => !empty($product->parent_product_id),
            'variant_type' => $product->variant_type,
            'has_variants' => $product->is_master_product || $product->childProducts->count() > 0,
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
            return ShopController::resolveProductUrl($product, $this->locale);
        } catch (\Exception $e) {
            // FIX: Explicit concatenation to prevent URL merge issues
            $slug = ltrim($this->translate($product->slug), '/');
            return url('/shop/' . $slug);
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
