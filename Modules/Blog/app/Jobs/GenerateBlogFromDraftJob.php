<?php

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\BlogAIContentWriter;
use Modules\Blog\App\Services\BlogAIBatchProcessor;
use Illuminate\Support\Facades\Log;

/**
 * Generate Blog From Draft Job
 *
 * Queue: blog-ai
 * Seçili bir draft'tan tam blog yazısı oluşturur
 *
 * 🔧 FIX: SerializesModels trait kaldırıldı
 * Çünkü tenant model'ler serialize edilirken tenant context kayboluyor
 * Çözüm: Model yerine ID geçir, tenant context restore ettikten sonra model'i fetch et
 */
class GenerateBlogFromDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 3;
    public $timeout = 300; // 5 dakika
    public $backoff = 60; // 60 saniye retry beklemesi

    public ?int $tenantId = null; // Tenant context

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $draftId, // Model yerine ID geçir
        public ?string $batchId = null
    ) {
        // Tenant context'i kaydet (dispatch anında)
        $this->tenantId = tenant('id');

        // Explicit queue belirt - tenant_2_default yerine blog-ai kullan
        $this->onQueue('blog-ai');
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

        // Tenant context restore edildikten SONRA model'i fetch et
        $draft = BlogAIDraft::findOrFail($this->draftId);

        try {
            Log::info('Blog AI Content Generation Started', [
                'draft_id' => $draft->id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

            // Blog oluştur
            $blog = $writer->generateBlogFromDraft($draft);

            // Batch progress güncelle (eğer batch varsa)
            if ($this->batchId) {
                $batchProcessor->markCompleted($this->batchId);
            }

            Log::info('Blog AI Content Generation Completed', [
                'draft_id' => $draft->id,
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
                'draft_id' => $draft->id,
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}
