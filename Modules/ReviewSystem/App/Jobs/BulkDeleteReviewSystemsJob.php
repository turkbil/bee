<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use Modules\ReviewSystem\App\Services\ReviewSystemService;
use Throwable;

/**
 * 🗑️ Bulk ReviewSystem Delete Queue Job
 *
 * ReviewSystem modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteReviewSystemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $reviewsystemIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $reviewsystemIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(ReviewSystemService $reviewsystemService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK REVIEWSYSTEM DELETE STARTED', [
                'reviewsystem_ids' => $this->reviewsystemIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->reviewsystemIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_reviewsystems_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->reviewsystemIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->reviewsystemIds as $index => $reviewsystemId) {
                try {
                    // Sayfa var mı kontrol et
                    $reviewsystem = ReviewSystem::find($reviewsystemId);
                    if (!$reviewsystem) {
                        Log::warning("Sayfa bulunamadı: {$reviewsystemId}");
                        continue;
                    }

                    // Homereviewsystem kontrolü - ana sayfa silinemesin (disabled for reviewsystems)
                    // ReviewSystems don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $reviewsystem->forceDelete();
                        log_activity($reviewsystem, 'kalıcı-silindi');
                    } else {
                        $reviewsystem->delete();
                        log_activity($reviewsystem, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->reviewsystemIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->reviewsystemIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_reviewsystem' => $reviewsystem->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $reviewsystemId,
                        'title' => $reviewsystem->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$reviewsystemId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'reviewsystem_id' => $reviewsystemId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->reviewsystemIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK REVIEWSYSTEM DELETE COMPLETED', [
                'total_reviewsystems' => count($this->reviewsystemIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->reviewsystemIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK REVIEWSYSTEM DELETE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Progress tracking
     */
    private function updateProgress(string $key, int $progress, int $total, string $status, array $data = []): void
    {
        Cache::put($key, [
            'progress' => $progress,
            'total' => $total,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'data' => $data
        ], 600); // 10 dakika
    }

    /**
     * Cache temizleme
     */
    private function clearPageCaches(): void
    {
        try {
            // ReviewSystem cache'leri temizle
            Cache::forget('reviewsystems_list');
            Cache::forget('reviewsystems_menu_cache');
            Cache::forget('reviewsystems_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'reviewsystem_*',
                'reviewsystems_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['reviewsystems'])->flush();
            }

            Log::info('🗑️ ReviewSystem caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK REVIEWSYSTEM DELETE JOB FAILED', [
            'reviewsystem_ids' => $this->reviewsystemIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_reviewsystems_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->reviewsystemIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
