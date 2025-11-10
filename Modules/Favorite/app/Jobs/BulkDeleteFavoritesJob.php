<?php

declare(strict_types=1);

namespace Modules\Favorite\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Favorite\App\Models\Favorite;
use Modules\Favorite\App\Services\FavoriteService;
use Throwable;

/**
 * 🗑️ Bulk Favorite Delete Queue Job
 *
 * Favorite modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteFavoritesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $favoriteIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $favoriteIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(FavoriteService $favoriteService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK FAVORITE DELETE STARTED', [
                'favorite_ids' => $this->favoriteIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->favoriteIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_favorites_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->favoriteIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->favoriteIds as $index => $favoriteId) {
                try {
                    // Sayfa var mı kontrol et
                    $favorite = Favorite::find($favoriteId);
                    if (!$favorite) {
                        Log::warning("Sayfa bulunamadı: {$favoriteId}");
                        continue;
                    }

                    // Homefavorite kontrolü - ana sayfa silinemesin (disabled for favorites)
                    // Favorites don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $favorite->forceDelete();
                        log_activity($favorite, 'kalıcı-silindi');
                    } else {
                        $favorite->delete();
                        log_activity($favorite, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->favoriteIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->favoriteIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_favorite' => $favorite->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $favoriteId,
                        'title' => $favorite->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$favoriteId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'favorite_id' => $favoriteId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->favoriteIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK FAVORITE DELETE COMPLETED', [
                'total_favorites' => count($this->favoriteIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->favoriteIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK FAVORITE DELETE FAILED', [
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
            // Favorite cache'leri temizle
            Cache::forget('favorites_list');
            Cache::forget('favorites_menu_cache');
            Cache::forget('favorites_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'favorite_*',
                'favorites_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['favorites'])->flush();
            }

            Log::info('🗑️ Favorite caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK FAVORITE DELETE JOB FAILED', [
            'favorite_ids' => $this->favoriteIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_favorites_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->favoriteIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
