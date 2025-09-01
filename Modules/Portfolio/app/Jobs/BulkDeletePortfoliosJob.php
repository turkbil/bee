<?php

namespace Modules\Portfolio\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Portfolio\App\Models\Portfolio;

class BulkDeletePortfoliosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;
    public array $backoff = [30, 60, 120]; // Retry delays: 30s, 1min, 2min

    protected array $portfolioIds;
    protected string $tenantId;
    protected string $userId;
    protected array $options;
    protected string $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $portfolioIds, string $tenantId, string $userId, array $options = [])
    {
        $this->portfolioIds = $portfolioIds;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
        $this->options = $options;
        $this->progressKey = "bulk_delete_portfolios_{$tenantId}_{$userId}";
        
        // Tenant-isolated queue kullan
        $this->onQueue('tenant_isolated');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $totalItems = count($this->portfolioIds);
        $processedCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            $this->updateProgress(0, 'processing', 'Portfolio silme işlemi başlatılıyor...');

            Log::info("🗑️ BULK DELETE PORTFOLIOS JOB BAŞLADI", [
                'portfolio_count' => $totalItems,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'progress_key' => $this->progressKey
            ]);

            foreach ($this->portfolioIds as $index => $portfolioId) {
                try {
                    $portfolio = Portfolio::find($portfolioId);
                    
                    if (!$portfolio) {
                        $errorCount++;
                        $errors[] = "Portfolio ID {$portfolioId} bulunamadı";
                        continue;
                    }

                    // Featured portfolio korunması (varsa)
                    if (isset($portfolio->is_featured) && $portfolio->is_featured) {
                        Log::warning("⚠️ Featured portfolio korundu", ['portfolio_id' => $portfolioId]);
                        $errorCount++;
                        $errors[] = "Featured portfolio silinemez: {$portfolio->title}";
                        continue;
                    }

                    // Portfolio silme
                    if ($this->options['force_delete'] ?? false) {
                        $portfolio->forceDelete();
                        Log::info("🗑️ Portfolio force deleted", ['portfolio_id' => $portfolioId]);
                    } else {
                        $portfolio->delete();
                        Log::info("🗑️ Portfolio soft deleted", ['portfolio_id' => $portfolioId]);
                    }

                    // Activity log
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($portfolio)
                        ->log('bulk_deleted');

                    $processedCount++;
                    
                    // Progress güncelle (her 10 item'da bir)
                    if (($index + 1) % 10 === 0 || ($index + 1) === $totalItems) {
                        $progress = (($index + 1) / $totalItems) * 90; // %90'a kadar
                        $this->updateProgress($progress, 'processing', 
                            "İşleniyor: " . ($index + 1) . "/{$totalItems}");
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Portfolio {$portfolioId}: " . $e->getMessage();
                    
                    Log::error("❌ Portfolio silme hatası", [
                        'portfolio_id' => $portfolioId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Cache temizleme
            $this->updateProgress(95, 'processing', 'Cache temizleniyor...');
            $this->clearPortfolioCache();

            // Final results
            $duration = round(microtime(true) - $startTime, 2);
            
            $this->updateProgress(100, 'completed', 'Portfolio silme işlemi tamamlandı!', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'error_messages' => $errors,
                'duration' => $duration
            ]);

            Log::info("✅ BULK DELETE PORTFOLIOS TAMAMLANDI", [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => "{$duration}s"
            ]);

        } catch (\Exception $e) {
            Log::error("💥 BULK DELETE PORTFOLIOS GENEL HATA", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->updateProgress(100, 'failed', 'Toplu silme işlemi başarısız!', [
                'error' => $e->getMessage(),
                'processed' => $processedCount,
                'total' => $totalItems
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("❌ BULK DELETE PORTFOLIOS JOB BAŞARISIZ", [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
        
        $this->updateProgress(100, 'failed', 'Portfolio silme işlemi başarısız!', [
            'error' => $exception->getMessage(),
            'failed' => true
        ]);
    }

    /**
     * Update progress in cache
     */
    private function updateProgress(int $progress, string $status, string $message, array $data = []): void
    {
        Cache::put($this->progressKey, [
            'progress' => $progress,
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'updated_at' => now()->toISOString()
        ], 3600); // 1 saat
    }

    /**
     * Clear portfolio related caches
     */
    private function clearPortfolioCache(): void
    {
        try {
            // Portfolio cache'lerini temizle
            Cache::forget('portfolios_list');
            Cache::forget('featured_portfolios');
            Cache::forget('portfolio_categories');
            
            // Pattern-based cache clearing
            $cacheKeys = ['portfolios:*', 'portfolio:*', 'featured:*'];
            foreach ($cacheKeys as $pattern) {
                if (method_exists(Cache::store(), 'deleteByPattern')) {
                    Cache::store()->deleteByPattern($pattern);
                }
            }
            
            Log::info("🧹 Portfolio cache temizlendi");
        } catch (\Exception $e) {
            Log::warning("⚠️ Portfolio cache temizleme hatası", ['error' => $e->getMessage()]);
        }
    }
}