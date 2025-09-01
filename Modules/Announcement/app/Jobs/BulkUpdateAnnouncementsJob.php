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
 * ✏️ Bulk Announcement Update Queue Job
 * 
 * Announcement modülünün bulk güncelleme işlemleri için queue job:
 * - Toplu duyuru güncelleme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Page modülünden kopya alınmış template
 */
class BulkUpdateAnnouncementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $announcementIds Güncellenecek duyuru ID'leri
     * @param array $updateData Güncellenecek veriler
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (validate, etc.)
     */
    public function __construct(
        public array $announcementIds,
        public array $updateData,
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
            Log::info('✏️ BULK ANNOUNCEMENT UPDATE STARTED', [
                'announcement_ids' => $this->announcementIds,
                'update_data' => $this->updateData,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->announcementIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_update_announcements_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->announcementIds), 'starting');

            // Güvenlik kontrolü - güncellenebilir alanları kontrol et
            $allowedFields = $this->getAllowedUpdateFields();
            $filteredUpdateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($filteredUpdateData)) {
                throw new \InvalidArgumentException('Güncellenebilir geçerli alan bulunamadı');
            }

            // Her duyuru için güncelleme işlemi
            foreach ($this->announcementIds as $index => $announcementId) {
                try {
                    // Duyuru var mı kontrol et
                    $announcement = Announcement::find($announcementId);
                    if (!$announcement) {
                        Log::warning("Duyuru bulunamadı: {$announcementId}");
                        continue;
                    }

                    // Özel validasyonlar
                    if ($this->options['validate'] ?? true) {
                        $this->validateUpdateData($announcement, $filteredUpdateData);
                    }

                    // Güncelleme işlemi
                    $oldData = $announcement->toArray();
                    $announcement->update($filteredUpdateData);
                    
                    // Activity log
                    activity()
                        ->performedOn($announcement)
                        ->causedBy(auth()->id())
                        ->withProperties([
                            'old' => $oldData,
                            'new' => $filteredUpdateData,
                            'bulk_operation' => true
                        ])
                        ->log('bulk_updated');

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->announcementIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->announcementIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_announcement' => $announcement->title
                    ]);

                    Log::info("✅ Duyuru güncellendi", [
                        'id' => $announcementId,
                        'title' => $announcement->title,
                        'updated_fields' => array_keys($filteredUpdateData)
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Duyuru güncelleme hatası (ID: {$announcementId}): " . $e->getMessage();
                    
                    Log::error("❌ Duyuru güncelleme hatası", [
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
                'error_messages' => $errors,
                'updated_fields' => array_keys($filteredUpdateData)
            ]);

            Log::info('✅ BULK ANNOUNCEMENT UPDATE COMPLETED', [
                'total_announcements' => count($this->announcementIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's',
                'updated_fields' => array_keys($filteredUpdateData)
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->announcementIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK ANNOUNCEMENT UPDATE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Güncellenebilir alanları tanımla - Announcement modülüne özel
     */
    private function getAllowedUpdateFields(): array
    {
        return [
            'is_active',
            'is_important',
            'is_featured', 
            'priority',
            'type', // announcement_type
            'start_date',
            'end_date',
            'published_at',
            'slug',
            'meta_title',
            'meta_description',
            'target_audience', // admin, user, all
            'display_location', // header, sidebar, modal
            'auto_hide'
        ];
    }

    /**
     * Update data validasyonu - Announcement modülüne özel
     */
    private function validateUpdateData(Announcement $announcement, array $updateData): void
    {
        // Slug benzersizlik kontrolü
        if (isset($updateData['slug'])) {
            $existingAnnouncement = Announcement::where('slug', $updateData['slug'])
                ->where('id', '!=', $announcement->id)
                ->first();
                
            if ($existingAnnouncement) {
                throw new \InvalidArgumentException("Slug zaten kullanımda: {$updateData['slug']}");
            }
        }

        // Tarih doğrulama
        if (isset($updateData['start_date']) || isset($updateData['end_date'])) {
            $startDate = isset($updateData['start_date']) ? 
                \Carbon\Carbon::parse($updateData['start_date']) : 
                $announcement->start_date;
                
            $endDate = isset($updateData['end_date']) ? 
                \Carbon\Carbon::parse($updateData['end_date']) : 
                $announcement->end_date;

            if ($endDate && $startDate && $endDate->lt($startDate)) {
                throw new \InvalidArgumentException('Bitiş tarihi başlangıç tarihinden önce olamaz');
            }
        }

        // Priority kontrolü
        if (isset($updateData['priority']) && ($updateData['priority'] < 1 || $updateData['priority'] > 10)) {
            throw new \InvalidArgumentException('Priority 1-10 arasında olmalıdır');
        }

        // Type kontrolü
        if (isset($updateData['type'])) {
            $allowedTypes = ['info', 'warning', 'success', 'danger', 'maintenance'];
            if (!in_array($updateData['type'], $allowedTypes)) {
                throw new \InvalidArgumentException('Geçersiz duyuru tipi: ' . $updateData['type']);
            }
        }

        // Target audience kontrolü
        if (isset($updateData['target_audience'])) {
            $allowedAudiences = ['admin', 'user', 'all'];
            if (!in_array($updateData['target_audience'], $allowedAudiences)) {
                throw new \InvalidArgumentException('Geçersiz hedef kitle: ' . $updateData['target_audience']);
            }
        }

        // Display location kontrolü
        if (isset($updateData['display_location'])) {
            $allowedLocations = ['header', 'sidebar', 'modal', 'banner'];
            if (!in_array($updateData['display_location'], $allowedLocations)) {
                throw new \InvalidArgumentException('Geçersiz görüntüleme konumu: ' . $updateData['display_location']);
            }
        }

        // Meta title uzunluk kontrolü
        if (isset($updateData['meta_title']) && strlen($updateData['meta_title']) > 60) {
            throw new \InvalidArgumentException("Meta title çok uzun (max 60 karakter)");
        }

        // Meta description uzunluk kontrolü
        if (isset($updateData['meta_description']) && strlen($updateData['meta_description']) > 160) {
            throw new \InvalidArgumentException("Meta description çok uzun (max 160 karakter)");
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
            Cache::forget('announcements_important');
            Cache::forget('announcements_featured');
            
            // Pattern-based cache temizleme
            $patterns = [
                'announcement_*',
                'announcements_*',
                'recent_announcements_*',
                'active_announcements_*'
            ];
            
            foreach ($patterns as $pattern) {
                Cache::tags(['announcements'])->flush();
            }

            Log::info('🗑️ Announcement caches cleared after bulk update');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK ANNOUNCEMENT UPDATE JOB FAILED', [
            'announcement_ids' => $this->announcementIds,
            'update_data' => $this->updateData,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_update_announcements_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->announcementIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}