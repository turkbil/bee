<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;
use App\Services\AI\EmbeddingService;

class GenerateProductEmbeddings extends Command
{
    protected $signature = 'products:generate-embeddings {--limit=10} {--force}';
    protected $description = 'Generate embeddings for products using OpenAI';

    public function handle(EmbeddingService $embeddingService): int
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        // Get products without embeddings (or all if force)
        $query = ShopProduct::query()->where('is_active', true);

        if (!$force) {
            $query->whereNull('embedding');
        }

        $products = $query->limit($limit)->get();

        if ($products->isEmpty()) {
            $this->info('No products to process.');
            return self::SUCCESS;
        }

        $this->info("🚀 Generating embeddings for {$products->count()} products...");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                $embedding = $embeddingService->generateProductEmbedding($product);

                $product->update([
                    'embedding' => json_encode($embedding),
                    'embedding_generated_at' => now(),
                    'embedding_model' => 'text-embedding-3-small',
                ]);

                $success++;
                $bar->advance();

                // Rate limit: 3000 requests/min = ~20ms delay
                usleep(20000); // 20ms

            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("❌ Failed for product {$product->product_id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Done! Success: {$success}, Failed: {$failed}");

        return self::SUCCESS;
    }
}
