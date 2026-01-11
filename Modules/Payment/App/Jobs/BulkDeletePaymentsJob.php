<?php

declare(strict_types=1);

namespace Modules\Payment\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PaymentService;
use Throwable;

/**
 * 🗑️ Bulk Payment Delete Queue Job
 *
 * Payment modülünün bulk silme işlemleri için queue job:
 * - Toplu sayfa silme işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Cache temizleme ve activity log
 * - Ana template job - diğer modüller bu pattern'i alacak
 */
class BulkDeletePaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;

    /**
     * @param array $paymentIds Silinecek sayfa ID'leri
     * @param string $tenantId Tenant ID (multi-tenant sistem için)
     * @param string $userId İşlemi yapan kullanıcı ID'si
     * @param array $options Ek seçenekler (force_delete, etc.)
     */
    public function __construct(
        public array $paymentIds,
        public string $tenantId,
        public string $userId,
        public array $options = []
    ) {
        $this->onQueue('tenant_isolated');
    }

    /**
     * Job execution
     */
    public function handle(PaymentService $paymentService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            Log::info('🗑️ BULK PAYMENT DELETE STARTED', [
                'payment_ids' => $this->paymentIds,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'total_count' => count($this->paymentIds)
            ]);

            // Progress tracking için cache key
            $progressKey = "bulk_delete_payments_{$this->tenantId}_{$this->userId}";
            $this->updateProgress($progressKey, 0, count($this->paymentIds), 'starting');

            // Her sayfa için silme işlemi
            foreach ($this->paymentIds as $index => $paymentId) {
                try {
                    // Sayfa var mı kontrol et
                    $payment = Payment::find($paymentId);
                    if (!$payment) {
                        Log::warning("Sayfa bulunamadı: {$paymentId}");
                        continue;
                    }

                    // Homepayment kontrolü - ana sayfa silinemesin (disabled for payments)
                    // Payments don't have homepage concept like pages

                    // Silme işlemi
                    $forceDelete = $this->options['force_delete'] ?? false;

                    if ($forceDelete) {
                        $payment->forceDelete();
                        log_activity($payment, 'kalıcı-silindi');
                    } else {
                        $payment->delete();
                        log_activity($payment, 'silindi');
                    }

                    $processedCount++;

                    // Progress güncelle
                    $progress = (int) (($index + 1) / count($this->paymentIds) * 100);
                    $this->updateProgress($progressKey, $progress, count($this->paymentIds), 'processing', [
                        'processed' => $processedCount,
                        'errors' => $errorCount,
                        'current_payment' => $payment->title
                    ]);

                    Log::info("✅ Sayfa silindi", [
                        'id' => $paymentId,
                        'title' => $payment->title,
                        'force_delete' => $forceDelete
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Sayfa silme hatası (ID: {$paymentId}): " . $e->getMessage();

                    Log::error("❌ Sayfa silme hatası", [
                        'payment_id' => $paymentId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Cache temizleme
            $this->clearPageCaches();

            // Final progress
            $duration = round(microtime(true) - $startTime, 2);
            $this->updateProgress($progressKey, 100, count($this->paymentIds), 'completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration,
                'error_messages' => $errors
            ]);

            Log::info('✅ BULK PAYMENT DELETE COMPLETED', [
                'total_payments' => count($this->paymentIds),
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => $duration . 's'
            ]);
        } catch (\Exception $e) {
            $this->updateProgress($progressKey, 0, count($this->paymentIds), 'failed', [
                'error' => $e->getMessage()
            ]);

            Log::error('💥 BULK PAYMENT DELETE FAILED', [
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
            // Payment cache'leri temizle
            Cache::forget('payments_list');
            Cache::forget('payments_menu_cache');
            Cache::forget('payments_sitemap_cache');

            // Pattern-based cache temizleme
            $patterns = [
                'payment_*',
                'payments_*',
                'sitemap_*',
                'menu_*'
            ];

            foreach ($patterns as $pattern) {
                Cache::tags(['payments'])->flush();
            }

            Log::info('🗑️ Payment caches cleared after bulk delete');
        } catch (\Exception $e) {
            Log::error('Cache temizleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job failed
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('💥 BULK PAYMENT DELETE JOB FAILED', [
            'payment_ids' => $this->paymentIds,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString()
        ]);

        // Progress'i failed olarak işaretle
        $progressKey = "bulk_delete_payments_{$this->tenantId}_{$this->userId}";
        $this->updateProgress($progressKey, 0, count($this->paymentIds), 'failed', [
            'error' => $exception?->getMessage()
        ]);
    }
}
