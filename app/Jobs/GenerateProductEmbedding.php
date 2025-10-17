<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\AI\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Models\ShopProduct;

class GenerateProductEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * The product ID to generate embedding for.
     *
     * @var int
     */
    protected int $productId;

    /**
     * The tenant ID (null for central database)
     *
     * @var int|null
     */
    protected ?int $tenantId = null;

    /**
     * Create a new job instance.
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;

        // Preserve tenant context
        if (tenancy()->initialized) {
            $this->tenantId = tenant()->id;
        }

        $this->onQueue(config('queue.connections.redis.queue', 'default'));
    }

    /**
     * Execute the job.
     */
    public function handle(EmbeddingService $embeddingService): void
    {
        try {
            // Initialize tenant context if needed
            if ($this->tenantId) {
                $tenant = Tenant::find($this->tenantId);
                if (!$tenant) {
                    Log::error("Tenant not found for embedding generation", [
                        'tenant_id' => $this->tenantId,
                        'product_id' => $this->productId
                    ]);
                    return;
                }
                tenancy()->initialize($tenant);
            }

            // Find the product
            $product = ShopProduct::find($this->productId);

            if (!$product) {
                Log::warning("Product not found for embedding generation", [
                    'product_id' => $this->productId,
                    'tenant_id' => $this->tenantId
                ]);
                return;
            }

            // Generate embedding
            $embedding = $embeddingService->generateProductEmbedding($product);

            // Update product with embedding
            $product->update([
                'embedding' => json_encode($embedding),
                'embedding_generated_at' => now(),
                'embedding_model' => 'text-embedding-3-small',
            ]);

            Log::info("Product embedding generated successfully", [
                'product_id' => $this->productId,
                'tenant_id' => $this->tenantId,
                'product_name' => is_array($product->title) ? ($product->title['tr'] ?? 'N/A') : $product->title,
                'dimensions' => count($embedding)
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to generate product embedding", [
                'product_id' => $this->productId,
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Product embedding generation job failed after {$this->tries} attempts", [
            'product_id' => $this->productId,
            'error' => $exception->getMessage()
        ]);
    }
}
