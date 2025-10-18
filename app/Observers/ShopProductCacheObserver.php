<?php

namespace App\Observers;

use App\Services\Cache\ProductCacheService;
use Modules\Shop\App\Models\ShopProduct;

/**
 * ShopProduct Cache Observer
 *
 * Automatically invalidate product cache when products are created, updated, or deleted
 */
class ShopProductCacheObserver
{
    /**
     * Handle the ShopProduct "created" event.
     */
    public function created(ShopProduct $product): void
    {
        // Invalidate category cache (new product added)
        if ($product->category_id) {
            ProductCacheService::invalidateCategory($product->category_id, tenant('id'));
        }
    }

    /**
     * Handle the ShopProduct "updated" event.
     */
    public function updated(ShopProduct $product): void
    {
        // Invalidate this product's cache
        ProductCacheService::invalidateProduct($product->product_id, tenant('id'));

        // If category changed, invalidate both old and new categories
        if ($product->isDirty('category_id')) {
            $oldCategoryId = $product->getOriginal('category_id');

            if ($oldCategoryId) {
                ProductCacheService::invalidateCategory($oldCategoryId, tenant('id'));
            }

            if ($product->category_id) {
                ProductCacheService::invalidateCategory($product->category_id, tenant('id'));
            }
        }
        // If just updated within same category, invalidate that category
        elseif ($product->category_id) {
            ProductCacheService::invalidateCategory($product->category_id, tenant('id'));
        }
    }

    /**
     * Handle the ShopProduct "deleted" event.
     */
    public function deleted(ShopProduct $product): void
    {
        // Invalidate this product's cache
        ProductCacheService::invalidateProduct($product->product_id, tenant('id'));

        // Invalidate category cache (product removed)
        if ($product->category_id) {
            ProductCacheService::invalidateCategory($product->category_id, tenant('id'));
        }
    }

    /**
     * Handle the ShopProduct "restored" event.
     */
    public function restored(ShopProduct $product): void
    {
        // Treat as created (product is back)
        $this->created($product);
    }

    /**
     * Handle the ShopProduct "force deleted" event.
     */
    public function forceDeleted(ShopProduct $product): void
    {
        // Same as deleted
        $this->deleted($product);
    }
}
