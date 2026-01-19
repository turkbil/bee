<?php

declare(strict_types=1);

namespace Modules\Service\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Service\App\Models\Service;
use Modules\Service\App\Services\ServiceService;
use Throwable;

/**
 * 🗑️ Bulk Service Delete Queue Job
 *
 * Service modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeleteServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $serviceIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $serviceIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(ServiceService $serviceService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK SERVICE DELETE STARTED', [
                'service_ids' => $this->serviceIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->serviceIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_services_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->serviceIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->serviceIds as $index => $serviceId) {
                try {
                    // Sayfa var mı kontrol et
                    $service = Service::find($serviceId);
                    if (!$service) {
                        Log::warning("Sayfa bulunamadı: {$serviceId}");
                        continue;
                    }

                    // Homeservice kontrolü - ana sayfa silinemesin (disabled for services)
                    // Services don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $service->forceDelete();
                        log_activity($service, 'kalıcı-silindi');
                    } else {
                        $service->delete();
                        log_activity($service, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->serviceIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->serviceIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_service' => $service->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $serviceId,
                        'title' => $service->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$serviceId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'service_id' => $serviceId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->serviceIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK SERVICE DELETE COMPLETED', [
                'total_services' => count($this->serviceIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->serviceIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK SERVICE DELETE FAILED', [
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
            // Service cache'leri temizle
            Cache::forget('services_list');
            Cache::forget('services_menu_cache');
            Cache::forget('services_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'service_*',
                'services_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['services'])->flush();
            }

            Log::info('🗑️ Service caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK SERVICE DELETE JOB FAILED', [
            'service_ids' => $this->serviceIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_services_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->serviceIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
