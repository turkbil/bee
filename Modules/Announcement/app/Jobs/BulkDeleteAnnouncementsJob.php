<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Announcement\App\Models\Announcement;
use Throwable;

/**
 * 🗑️ Bulk Announcement Delete Queue Job
 * 
 * Announcement modülünün bulk silme işlemleri için queue job:
 * - Toplu duyuru silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Page modülünden kopya alınmış template
 */
class BulkDeleteAnnouncementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $announcementIds Silinecek duyuru ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $announcementIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK ANNOUNCEMENT DELETE STARTED', [
                'announcement_ids' => $this->announcementIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->announcementIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_announcements_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->announcementIds), 'starting');

            // Her duyuru için silme işlemi
            foreach ($this->announcementIds as $index => $announcementId) {
                try {
                    // Duyuru var mı kontrol et
                    $announcement = Announcement::find($announcementId);
                    if (!$announcement) {
                        Log::warning("Duyuru bulunamadı: {$announcementId}");
                        continue;
                    }

                    // Önemli duyuru kontrolü - önemli duyurular için uyarı
                    if (isset($announcement->is_important) && $announcement->is_important) {
                        Log::warning("Önemli duyuru siliniyor: {$announcement->title}");
                    }

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;
                    
                    if ($forceDelete) {
                        $announcement->forceDelete();
                    } else {
                        $announcement->delete();
                    }

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->announcementIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->announcementIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_announcement' => $announcement->title
                    ]);

                    Log::info("✅ Duyuru silindi", [
                        'id' => $announcementId,
                        'title' => $announcement->title,
                        'force_delete' => $forceDelete
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Duyuru silme hatası (ID: {$announcementId}): " . $e->getMessage();
                    
                    Log::error("❌ Duyuru silme hatası", [
                        'announcement_id' => $announcementId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearAnnouncementCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->announcementIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK ANNOUNCEMENT DELETE COMPLETED', [
                'total_announcements' => count($this->announcementIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->announcementIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK ANNOUNCEMENT DELETE FAILED', [
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
    private function clearAnnouncementCaches(): void
    {
        try {
            // Announcement cache'leri temizle
            Cache::forget('announcements_list');
            Cache::forget('announcements_active');
            Cache::forget('announcements_recent');
            
            // Pattern-based cache temizleme
            $patterns = [
                'announcement_*',
                'announcements_*',
                'recent_announcements_*'
            ];
            
            foreach ($patterns as $pattern) {
                Cache::tags(['announcements'])->flush();
            }

            Log::info('🗑️ Announcement caches cleared after bulk delete');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK ANNOUNCEMENT DELETE JOB FAILED', [
            'announcement_ids' => $this->announcementIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_announcements_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->announcementIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}