<?php

declare(strict_types=1);

namespace Modules\Page\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Services\PageService;
use Throwable;

/**
 * 🗑️ Bulk Page Delete Queue Job
 * 
 * Page modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeletePagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $pageIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $pageIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(PageService $pageService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK PAGE DELETE STARTED', [
                'page_ids' => $this->pageIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->pageIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_pages_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->pageIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->pageIds as $index => $pageId) {
                try {
                    // Sayfa var mı kontrol et
                    $page = Page::find($pageId);
                    if (!$page) {
                        Log::warning("Sayfa bulunamadı: {$pageId}");
                        continue;
                    }

                    // Homepage kontrolü - ana sayfa silinemesin
                    if ($page->is_homepage) {
                        $errors[] = "Ana sayfa silinemez: {$page->title}";
                        $errorCount++;
                        continue;
                    }

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;
                    
                    if ($forceDelete) {
                        $page->forceDelete();
                    } else {
                        $page->delete();
                    }

                    $processedCount++;
                    
                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->pageIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->pageIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_page' => $page->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $pageId,
                        'title' => $page->title,
                        'force_delete' => $forceDelete
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$pageId}): " . $e->getMessage();
                    
                    Log::error("❌ Sayfa silme hatası", [
                        'page_id' => $pageId,
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
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK PAGE DELETE COMPLETED', [
                'total_pages' => count($this->pageIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->pageIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK PAGE DELETE FAILED', [
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
            // Page cache'leri temizle
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

            Log::info('🗑️ Page caches cleared after bulk delete');
            
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK PAGE DELETE JOB FAILED', [
            'page_ids' => $this->pageIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_pages_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->pageIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}