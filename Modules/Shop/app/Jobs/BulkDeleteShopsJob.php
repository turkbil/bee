<?php

declare(strict_types=1);

namespace Modules\Shop\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopService;
use Throwable;

/**
 * 🗑️ Bulk Shop Delete Queue Job
 *
 * Shop modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteShopsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $shopIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $shopIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(ShopService $shopService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK SHOP DELETE STARTED', [
                'shop_ids' => $this->shopIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->shopIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_shops_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->shopIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->shopIds as $index => $shopId) {
                try {
                    // Sayfa var mı kontrol et
                    $shop = Shop::find($shopId);
                    if (!$shop) {
                        Log::warning("Sayfa bulunamadı: {$shopId}");
                        continue;
                    }

                    // Homeshop kontrolü - ana sayfa silinemesin (disabled for shops)
                    // Shops don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $shop->forceDelete();
                        log_activity($shop, 'kalıcı-silindi');
                    } else {
                        $shop->delete();
                        log_activity($shop, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->shopIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->shopIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_shop' => $shop->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $shopId,
                        'title' => $shop->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$shopId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'shop_id' => $shopId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->shopIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK SHOP DELETE COMPLETED', [
                'total_shops' => count($this->shopIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->shopIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK SHOP DELETE FAILED', [
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
            // Shop cache'leri temizle
            Cache::forget('shops_list');
            Cache::forget('shops_menu_cache');
            Cache::forget('shops_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'shop_*',
                'shops_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['shops'])->flush();
            }

            Log::info('🗑️ Shop caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK SHOP DELETE JOB FAILED', [
            'shop_ids' => $this->shopIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_shops_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->shopIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
