<?php

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Blog\App\Services\BlogAIDraftGenerator;
use Illuminate\Support\Facades\Log;

/**
 * Generate Blog AI Drafts Job
 *
 * Queue: blog-ai
 * AI ile blog taslakları oluşturur
 */
class GenerateDraftsJob implements ShouldQueue
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
        public int $count = 5
    ) {
        // Tenant context'i kaydet (dispatch anında)
        $this->tenantId = tenant('id');

        // Explicit queue belirt - tenant_2_default yerine blog-ai kullan
        $this->onQueue('blog-ai');
    }

    /**
     * Execute the job.
     */
    public function handle(BlogAIDraftGenerator $generator): void
    {
        // Tenant context'i restore et
        if ($this->tenantId) {
            tenancy()->initialize($this->tenantId);
        }

        try {
            Log::info('Blog AI Draft Generation Started', [
                'count' => $this->count,
                'tenant_id' => $this->tenantId,
            ]);

            $drafts = $generator->generateDrafts($this->count);

            Log::info('Blog AI Draft Generation Completed', [
                'count' => count($drafts),
                'tenant_id' => $this->tenantId,
            ]);

            // Livewire event broadcast (optional - component refresh için)
            // event(new DraftGenerationCompleted(count($drafts)));

        } catch (\Exception $e) {
            Log::error('Blog AI Draft Generation Job Failed', [
                'count' => $this->count,
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}
