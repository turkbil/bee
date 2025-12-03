<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

/**
 * Shop-Cart Bridge Service
 *
 * Provides integration between Shop and Cart modules
 * Prepares product data for cart operations
 */
class ShopCartBridge
{
    /**
     * Prepare product for adding to cart
     *
     * @param ShopProduct $product
     * @param int $quantity
     * @return array Cart options array
     */
    public function prepareProductForCart(ShopProduct $product, int $quantity = 1): array
    {
        // Stok kontrolü
        $this->validateStock($product, $quantity);

        // Display bilgileri
        $displayInfo = $this->getProductDisplayInfo($product);

        // Fiyat bilgileri
        $priceInfo = $this->getProductPriceInfo($product);

        // Vergi bilgileri
        $taxInfo = $this->getProductTaxInfo($product);

        // Merge all data
        return array_merge($displayInfo, $priceInfo, $taxInfo);
    }

    /**
     * Validate product stock
     *
     * @param ShopProduct $product
     * @param int $quantity
     * @throws \Exception
     */
    protected function validateStock(ShopProduct $product, int $quantity): void
    {
        // Stok takibi aktif mi?
        if (!$product->stock_tracking) {
            return; // Stok takibi yok, geç
        }

        // Stok yeterli mi?
        if ($product->current_stock < $quantity) {
            // Backorder izni var mı?
            if (!$product->allow_backorder) {
                throw new \Exception(
                    "Stok yetersiz. Mevcut stok: {$product->current_stock}, İstenen: {$quantity}"
                );
            }

            // Backorder için log
            Log::warning('Product added to cart with backorder', [
                'product_id' => $product->product_id,
                'stock' => $product->current_stock,
                'requested' => $quantity,
            ]);
        }
    }

    /**
     * Get product display information
     *
     * @param ShopProduct $product
     * @return array
     */
    protected function getProductDisplayInfo(ShopProduct $product): array
    {
        // 🎯 Sadece hero koleksiyonu kullan
        $itemImage = $product->hasMedia('hero')
            ? thumb($product->getFirstMedia('hero'), 200, 200, ['quality' => 85, 'format' => 'webp'])
            : null;

        // 🔍 DEBUG: Görsel nereden çekiliyor?
        Log::info('🛒 ShopCartBridge - Görsel Bilgisi', [
            'product_id' => $product->product_id,
            'product_title' => $product->getTranslated('title', app()->getLocale()),
            'has_hero' => $product->hasMedia('hero'),
            'hero_count' => $product->getMedia('hero')->count(),
            'all_media_count' => $product->getMedia()->count(),
            'all_collections' => $product->getMedia()->pluck('collection_name')->unique()->values()->toArray(),
            'generated_image_url' => $itemImage,
        ]);

        return [
            'item_title' => $product->getTranslated('title', app()->getLocale()),
            'item_image' => $itemImage,
            'item_sku' => $product->sku,
        ];
    }

    /**
     * Get product price information
     *
     * @param ShopProduct $product
     * @return array
     */
    protected function getProductPriceInfo(ShopProduct $product): array
    {
        return [
            'unit_price' => $product->final_price,
            'currency' => $product->currency ?? 'TRY',
            'discount_amount' => 0, // TODO: Kampanya sistemi eklenince hesaplanacak
        ];
    }

    /**
     * Get product tax information
     *
     * @param ShopProduct $product
     * @return array
     */
    protected function getProductTaxInfo(ShopProduct $product): array
    {
        // Ürün bazlı KDV oranı
        $taxRate = $product->tax_rate ?? 20.0; // Default %20 KDV

        // TODO: Kategori bazlı KDV sistemi (gelecekte)
        // if ($product->category && $product->category->tax_rate) {
        //     $taxRate = $product->category->tax_rate;
        // }

        return [
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate product availability
     *
     * @param ShopProduct $product
     * @return array ['in_stock', 'can_order', 'available_quantity']
     */
    public function calculateAvailability(ShopProduct $product): array
    {
        if (!$product->stock_tracking) {
            return [
                'in_stock' => true,
                'can_order' => true,
                'available_quantity' => 9999, // Sınırsız
            ];
        }

        $inStock = $product->current_stock > 0;
        $canOrder = $inStock || $product->allow_backorder;

        return [
            'in_stock' => $inStock,
            'can_order' => $canOrder,
            'available_quantity' => $product->current_stock,
            'lead_time_days' => $product->lead_time_days,
        ];
    }

    /**
     * Check if product can be added to cart
     *
     * @param ShopProduct $product
     * @param int $quantity
     * @return bool
     */
    public function canAddToCart(ShopProduct $product, int $quantity = 1): bool
    {
        // Ürün aktif mi?
        if (!$product->is_active) {
            return false;
        }

        // Stok kontrolü
        if ($product->stock_tracking) {
            if ($product->current_stock < $quantity && !$product->allow_backorder) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get cart item error messages
     *
     * @param ShopProduct $product
     * @param int $quantity
     * @return array
     */
    public function getCartItemErrors(ShopProduct $product, int $quantity = 1): array
    {
        $errors = [];

        if (!$product->is_active) {
            $errors[] = 'Ürün şu anda satışta değil.';
        }

        if ($product->stock_tracking) {
            if ($product->current_stock < $quantity) {
                if (!$product->allow_backorder) {
                    $errors[] = "Stok yetersiz. Mevcut stok: {$product->current_stock}";
                } else {
                    $errors[] = "Ürün şu anda stokta yok. Sipariş verebilirsiniz, tedarik süresi: {$product->lead_time_days} gün.";
                }
            }
        }

        return $errors;
    }
}
