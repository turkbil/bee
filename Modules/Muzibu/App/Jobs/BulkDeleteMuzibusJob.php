<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Muzibu;
use Modules\Muzibu\App\Services\MuzibuService;
use Throwable;

/**
 * 🗑️ Bulk Muzibu Delete Queue Job
 *
 * Muzibu modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteMuzibusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $muzibuIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $muzibuIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('muzibu_isolated');
    }

    /**
     * Job execution
     */
    public function handle(MuzibuService $muzibuService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK MUZIBU DELETE STARTED', [
                'muzibu_ids' => $this->muzibuIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->muzibuIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_muzibus_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->muzibuIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->muzibuIds as $index => $muzibuId) {
                try {
                    // Sayfa var mı kontrol et
                    $muzibu = Muzibu::find($muzibuId);
                    if (!$muzibu) {
                        Log::warning("Sayfa bulunamadı: {$muzibuId}");
                        continue;
                    }

                    // Homemuzibu kontrolü - ana sayfa silinemesin (disabled for muzibus)
                    // Muzibus don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $muzibu->forceDelete();
                        log_activity($muzibu, 'kalıcı-silindi');
                    } else {
                        $muzibu->delete();
                        log_activity($muzibu, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->muzibuIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->muzibuIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_muzibu' => $muzibu->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $muzibuId,
                        'title' => $muzibu->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$muzibuId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'muzibu_id' => $muzibuId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->muzibuIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK MUZIBU DELETE COMPLETED', [
                'total_muzibus' => count($this->muzibuIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->muzibuIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK MUZIBU DELETE FAILED', [
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
            // Muzibu cache'leri temizle
            Cache::forget('muzibus_list');
            Cache::forget('muzibus_menu_cache');
            Cache::forget('muzibus_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'muzibu_*',
                'muzibus_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['muzibus'])->flush();
            }

            Log::info('🗑️ Muzibu caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK MUZIBU DELETE JOB FAILED', [
            'muzibu_ids' => $this->muzibuIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_muzibus_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->muzibuIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
