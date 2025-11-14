<?php

namespace Modules\Blog\App\Services;

use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Jobs\GenerateBlogFromDraftJob;
use Illuminate\Support\Facades\Cache;

/**
 * Blog AI Batch Processor Service
 *
 * Seçili taslakları toplu olarak işler
 * Progress tracking yapar
 */
class BlogAIBatchProcessor
{
    /**
     * Seçili draft'ları işle (queue'ya gönder)
     *
     * @param array $draftIds
     * @return void
     */
    public function procesSelectedDrafts(array $draftIds): void
    {
        if (empty($draftIds)) {
            return;
        }

        // Batch ID oluştur (tracking için)
        $batchId = 'blog_ai_batch_' . time() . '_' . tenant('id');

        // Batch bilgilerini cache'e kaydet
        $this->initializeBatchStatus($batchId, count($draftIds));

        // Her draft için job dispatch et
        foreach ($draftIds as $draftId) {
            $draft = BlogAIDraft::find($draftId);

            if ($draft && !$draft->is_generated) {
                // Job dispatch
                GenerateBlogFromDraftJob::dispatch($draft, $batchId)
                    ->onQueue('blog-ai');
            }
        }
    }

    /**
     * Batch status initialize
     */
    protected function initializeBatchStatus(string $batchId, int $total): void
    {
        Cache::put($batchId, [
            'total' => $total,
            'completed' => 0,
            'failed' => 0,
            'started_at' => now(),
        ], now()->addHours(24)); // 24 saat cache
    }

    /**
     * Batch status güncelle (completed)
     */
    public function markCompleted(string $batchId): void
    {
        $status = Cache::get($batchId, [
            'total' => 0,
            'completed' => 0,
            'failed' => 0,
        ]);

        $status['completed']++;
        Cache::put($batchId, $status, now()->addHours(24));
    }

    /**
     * Batch status güncelle (failed)
     */
    public function markFailed(string $batchId): void
    {
        $status = Cache::get($batchId, [
            'total' => 0,
            'completed' => 0,
            'failed' => 0,
        ]);

        $status['failed']++;
        Cache::put($batchId, $status, now()->addHours(24));
    }

    /**
     * Batch status'u getir
     */
    public function getBatchStatus(string $batchId): array
    {
        return Cache::get($batchId, [
            'total' => 0,
            'completed' => 0,
            'failed' => 0,
        ]);
    }

    /**
     * Batch tamamlandı mı?
     */
    public function isBatchCompleted(string $batchId): bool
    {
        $status = $this->getBatchStatus($batchId);
        return ($status['completed'] + $status['failed']) >= $status['total'];
    }

    /**
     * Progress yüzdesi
     */
    public function getProgressPercentage(string $batchId): int
    {
        $status = $this->getBatchStatus($batchId);

        if ($status['total'] === 0) {
            return 0;
        }

        return (int) ((($status['completed'] + $status['failed']) / $status['total']) * 100);
    }
}
