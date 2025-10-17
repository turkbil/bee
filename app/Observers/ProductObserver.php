<?php

namespace App\Observers;

use Modules\Shop\App\Models\ShopProduct;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the ShopProduct "created" event.
     */
    public function created(ShopProduct $product): void
    {
        if (!config('ai.enabled', false)) {
            return;
        }

        Log::info('ProductObserver: Product created', ['product_id' => $product->product_id]);

        // Dispatch embedding generation job
        \App\Jobs\GenerateProductEmbedding::dispatch($product->product_id)
            ->onQueue('default');
    }

    /**
     * Handle the ShopProduct "updated" event.
     */
    public function updated(ShopProduct $product): void
    {
        if (!config('ai.enabled', false)) {
            return;
        }

        // Embedding için önemli alanlar
        $importantFields = [
            'title',
            'short_description',
            'body',
            'features',
            'technical_specs',
            'use_cases',
            'competitive_advantages',
            'highlighted_features',
        ];

        // Önemli alanlardan biri değiştiyse embedding yenile
        if ($product->wasChanged($importantFields)) {
            Log::info('ProductObserver: Important fields changed, dispatching embedding job', [
                'product_id' => $product->product_id,
                'changed_fields' => array_keys($product->getChanges())
            ]);

            \App\Jobs\GenerateProductEmbedding::dispatch($product->product_id)
                ->onQueue('default')
                ->delay(now()->addSeconds(5)); // 5 saniye gecikme
        }
    }

    /**
     * Handle the ShopProduct "deleted" event.
     */
    public function deleted(ShopProduct $product): void
    {
        // Embedding cleanup if needed
    }

    /**
     * Handle the ShopProduct "restored" event.
     */
    public function restored(ShopProduct $product): void
    {
        // Re-generate embedding if needed
        if (config('ai.enabled', false)) {
            \App\Jobs\GenerateProductEmbedding::dispatch($product->product_id)
                ->onQueue('default');
        }
    }

    /**
     * Handle the ShopProduct "force deleted" event.
     */
    public function forceDeleted(ShopProduct $product): void
    {
        // Cleanup
    }
}
