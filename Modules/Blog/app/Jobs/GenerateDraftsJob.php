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

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $count = 100
    ) {
        $this->onQueue('blog-ai');
    }

    /**
     * Execute the job.
     */
    public function handle(BlogAIDraftGenerator $generator): void
    {
        try {
            Log::info('Blog AI Draft Generation Started', [
                'count' => $this->count,
                'tenant_id' => tenant('id'),
            ]);

            $drafts = $generator->generateDrafts($this->count);

            Log::info('Blog AI Draft Generation Completed', [
                'count' => count($drafts),
                'tenant_id' => tenant('id'),
            ]);

            // Livewire event broadcast (optional - component refresh için)
            // event(new DraftGenerationCompleted(count($drafts)));

        } catch (\Exception $e) {
            Log::error('Blog AI Draft Generation Job Failed', [
                'count' => $this->count,
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}
