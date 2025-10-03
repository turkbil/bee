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
use Modules\Announcement\App\Services\AnnouncementService;
use Throwable;

/**
 * ✏️ Bulk Announcement Update Queue Job
 *
 * Announcement modülünün bulk güncelleme işlemleri için queue job:
 * - Toplu sayfa güncelleme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkUpdateAnnouncementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $pageIds Güncellenecek sayfa ID'leri
     * @param array $updateData Güncellenecek veriler
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (validate, etc.)
     */
    public function __construct(
        public array $pageIds,
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
    public function handle(AnnouncementService $pageService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('✏️ BULK PAGE UPDATE STARTED', [
                'announcement_ids' => $this->pageIds,
                'update_data' => $this->updateData,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->pageIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_update_pages_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->pageIds), 'starting');

            // Güvenlik kontrolü - güncellenebilir alanları kontrol et
            $allowedFields = $this->getAllowedUpdateFields();
            $filteredUpdateData = array_intersect_key($this->updateData, array_flip($allowedFields));

            if (empty($filteredUpdateData)) {
                throw new \InvalidArgumentException('Güncellenebilir geçerli alan bulunamadı');
            }

            // Her sayfa için güncelleme işlemi
            foreach ($this->pageIds as $index => $pageId) {
                try {
                    // Sayfa var mı kontrol et
                    $announcement = Announcement::find($pageId);
                    if (!$announcement) {
                        Log::warning("Sayfa bulunamadı: {$pageId}");
                        continue;
                    }

                    // Özel validasyonlar
                    if ($this->options['validate'] ?? true) {
                        $this->validateUpdateData($announcement, $filteredUpdateData);
                    }

                    // Güncelleme işlemi
                    $announcement->update($filteredUpdateData);
                    log_activity($announcement, 'toplu-güncellendi');

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->pageIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->pageIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_page' => $announcement->title
                    ]);

                    Log::info("✅ Sayfa güncellendi", [
                        'id' => $pageId,
                        'title' => $announcement->title,
                        'updated_fields' => array_keys($filteredUpdateData)
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa güncelleme hatası (ID: {$pageId}): " . $e->getMessage();

                    Log::error("❌ Sayfa güncelleme hatası", [
                        'announcement_id' => $pageId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->pageIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors,
                'updated_fields' => array_keys($filteredUpdateData)
            ]);

            Log::info('✅ BULK PAGE UPDATE COMPLETED', [
                'total_pages' => count($this->pageIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's',
                'updated_fields' => array_keys($filteredUpdateData)
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->pageIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK PAGE UPDATE FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Güncellenebilir alanları tanımla
     */
    private function getAllowedUpdateFields(): array
    {
        return [
            'is_active',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'canonical_url',
            'og_title',
            'og_description',
            'og_image',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'priority',
            'changefreq',
            'noindex',
            'nofollow',
            'schema_type',
            'published_at',
            'slug' // Homepage için kısıtlı
        ];
    }

    /**
     * Update data validasyonu
     */
    private function validateUpdateData(Announcement $announcement, array $updateData): void
    {
        // Slug benzersizlik kontrolü
        if (isset($updateData['slug'])) {
            $existingPage = Announcement::where('slug', $updateData['slug'])
                ->where('id', '!=', $announcement->id)
                ->first();

            if ($existingPage) {
                throw new \InvalidArgumentException("Slug zaten kullanımda: {$updateData['slug']}");
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

        // Published date kontrolü
        if (isset($updateData['published_at'])) {
            try {
                \Carbon\Carbon::parse($updateData['published_at']);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Geçersiz tarih formatı: {$updateData['published_at']}");
            }
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
            // Announcement cache'leri temizle
            Cache::forget('pages_list');
            Cache::forget('pages_menu_cache');
            Cache::forget('pages_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'page_*',
                'pages_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['pages'])->flush();
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
        Log::error('💥 BULK PAGE UPDATE JOB FAILED', [
            'announcement_ids' => $this->pageIds,
            'update_data' => $this->updateData,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_update_pages_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->pageIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
