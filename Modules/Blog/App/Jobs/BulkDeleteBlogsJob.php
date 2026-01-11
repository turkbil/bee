<?php

declare(strict_types=1);

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Services\BlogService;
use Throwable;

/**
 * 🗑️ Bulk Blog Delete Queue Job
 *
 * Blog modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteBlogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $blogIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $blogIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(BlogService $blogService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK BLOG DELETE STARTED', [
                'blog_ids' => $this->blogIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->blogIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_blogs_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->blogIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->blogIds as $index => $blogId) {
                try {
                    // Sayfa var mı kontrol et
                    $blog = Blog::find($blogId);
                    if (!$blog) {
                        Log::warning("Sayfa bulunamadı: {$blogId}");
                        continue;
                    }

                    // Homeblog kontrolü - ana sayfa silinemesin (disabled for blogs)
                    // Blogs don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $blog->forceDelete();
                        log_activity($blog, 'kalıcı-silindi');
                    } else {
                        $blog->delete();
                        log_activity($blog, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->blogIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->blogIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_blog' => $blog->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $blogId,
                        'title' => $blog->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$blogId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'blog_id' => $blogId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->blogIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK BLOG DELETE COMPLETED', [
                'total_blogs' => count($this->blogIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->blogIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK BLOG DELETE FAILED', [
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
            // Blog cache'leri temizle
            Cache::forget('blogs_list');
            Cache::forget('blogs_menu_cache');
            Cache::forget('blogs_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'blog_*',
                'blogs_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['blogs'])->flush();
            }

            Log::info('🗑️ Blog caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK BLOG DELETE JOB FAILED', [
            'blog_ids' => $this->blogIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_blogs_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->blogIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
