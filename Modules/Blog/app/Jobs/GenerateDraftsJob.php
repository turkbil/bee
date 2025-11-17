<?php

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Blog\App\Services\CategoryBasedDraftGenerator;
use Illuminate\Support\Facades\Log;

/**
 * Generate Blog AI Drafts Job
 *
 * Queue: blog-ai
 * AI ile blog taslakları oluşturur
 * ✅ YENİ: Category-based draft generation kullanır
 */
class GenerateDraftsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 900; // 15 dakika - CategoryBasedDraftGenerator için artırıldı
    public $backoff = 60; // 60 saniye retry beklemesi

    public ?int $tenantId = null; // Tenant context (default null for backwards compatibility)

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $count = 25  // Category-based generator always generates 25 (5 groups × 5 drafts)
    ) {
        // Tenant context'i kaydet (dispatch anında)
        $this->tenantId = tenant('id');

        // Explicit queue belirt - tenant_2_default yerine blog-ai kullan
        $this->onQueue('blog-ai');
    }

    /**
     * Execute the job.
     */
    public function handle(CategoryBasedDraftGenerator $generator): void
    {
        // Tenant context'i restore et (eğer zaten initialize değilse)
        if ($this->tenantId && (!tenant() || tenant('id') != $this->tenantId)) {
            tenancy()->initialize($this->tenantId);
        }

        try {
            Log::info('🚀 Category-Based Draft Generation Started', [
                'expected_count' => 25, // Always 25 (5 groups × 5)
                'tenant_id' => $this->tenantId,
                'method' => 'category-based',
            ]);

            // Category-based generator - count parametresi yok, sabit 25 draft üretir
            $drafts = $generator->generateDrafts();

            Log::info('✅ Category-Based Draft Generation Completed', [
                'actual_count' => count($drafts),
                'tenant_id' => $this->tenantId,
            ]);

            // Livewire event broadcast (optional - component refresh için)
            // event(new DraftGenerationCompleted(count($drafts)));

        } catch (\Exception $e) {
            Log::error('❌ Category-Based Draft Generation Job Failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
                'trace' => $e->getTraceAsString(),
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}
