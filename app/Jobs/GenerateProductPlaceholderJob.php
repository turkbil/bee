<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AI\ProductPlaceholderService;
use Illuminate\Support\Facades\Log;

/**
 * Generate Product Placeholder Job
 *
 * Arka planda AI placeholder generate eder ve DB'ye kaydeder
 *
 * KULLANIM:
 * GenerateProductPlaceholderJob::dispatch($productId);
 */
class GenerateProductPlaceholderJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Job timeout (seconds)
     * AI API çağrısı zaman alabilir
     */
    public $timeout = 120;

    /**
     * Maximum retry attempts
     */
    public $tries = 2;

    /**
     * Product ID for placeholder generation
     */
    protected string $productId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(ProductPlaceholderService $service): void
    {
        Log::info('🔄 Queue: Generating placeholder for product', [
            'product_id' => $this->productId,
            'job_id' => $this->job->getJobId(),
        ]);

        try {
            // Force generate (bypass cache, always generate new)
            $result = $service->forceGenerate($this->productId);

            if ($result['success']) {
                Log::info('✅ Queue: Placeholder generated successfully', [
                    'product_id' => $this->productId,
                    'conversation_count' => count($result['conversation']),
                    'job_id' => $this->job->getJobId(),
                ]);
            } else {
                Log::error('❌ Queue: Placeholder generation failed', [
                    'product_id' => $this->productId,
                    'error' => $result['error'] ?? 'Unknown error',
                    'job_id' => $this->job->getJobId(),
                ]);

                // Fail the job to trigger retry
                $this->fail(new \Exception($result['error'] ?? 'Placeholder generation failed'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Queue: Exception during placeholder generation', [
                'product_id' => $this->productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'job_id' => $this->job->getJobId(),
            ]);

            // Fail and retry
            $this->fail($e);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Queue: Placeholder job failed after all retries', [
            'product_id' => $this->productId,
            'error' => $exception->getMessage(),
            'job_id' => $this->job?->getJobId(),
        ]);

        // Burada isteğe bağlı olarak admin'e bildirim gönderilebilir
    }
}
