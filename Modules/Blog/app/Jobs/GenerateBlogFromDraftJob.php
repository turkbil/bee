<?php

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\BlogAIContentWriter;
use Modules\Blog\App\Services\BlogAIBatchProcessor;
use Illuminate\Support\Facades\Log;

/**
 * Generate Blog From Draft Job
 *
 * Queue: blog-ai
 * Seçili bir draft'tan tam blog yazısı oluşturur
 */
class GenerateBlogFromDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 dakika
    public $backoff = 60; // 60 saniye retry beklemesi

    public ?int $tenantId = null; // Tenant context (default null for backwards compatibility)

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BlogAIDraft $draft,
        public ?string $batchId = null
    ) {
        // Tenant context'i kaydet (dispatch anında)
        $this->tenantId = tenant('id');

        // Default queue kullan (worker tarafından dinleniyor)
    }

    /**
     * Execute the job.
     */
    public function handle(BlogAIContentWriter $writer, BlogAIBatchProcessor $batchProcessor): void
    {
        // Tenant context'i restore et
        if ($this->tenantId) {
            tenancy()->initialize($this->tenantId);
        }

        try {
            Log::info('Blog AI Content Generation Started', [
                'draft_id' => $this->draft->id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

            // Blog oluştur
            $blog = $writer->generateBlogFromDraft($this->draft);

            // Batch progress güncelle (eğer batch varsa)
            if ($this->batchId) {
                $batchProcessor->markCompleted($this->batchId);
            }

            Log::info('Blog AI Content Generation Completed', [
                'draft_id' => $this->draft->id,
                'blog_id' => $blog->blog_id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

        } catch (\Exception $e) {
            // Batch progress güncelle (failed)
            if ($this->batchId) {
                $batchProcessor->markFailed($this->batchId);
            }

            Log::error('Blog AI Content Generation Job Failed', [
                'draft_id' => $this->draft->id,
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}
