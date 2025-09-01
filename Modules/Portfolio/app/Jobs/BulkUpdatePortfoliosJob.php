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

class BulkUpdatePortfoliosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika
    public int $maxExceptions = 3;
    public array $backoff = [30, 60, 120];

    protected array $portfolioIds;
    protected array $updateData;
    protected string $tenantId;
    protected string $userId;
    protected array $options;
    protected string $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $portfolioIds, array $updateData, string $tenantId, string $userId, array $options = [])
    {
        $this->portfolioIds = $portfolioIds;
        $this->updateData = $updateData;
        $this->tenantId = $tenantId;
        $this->userId = $userId;
        $this->options = $options;
        $this->progressKey = "bulk_update_portfolios_{$tenantId}_{$userId}";
        
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
            $this->updateProgress(0, 'processing', 'Portfolio güncelleme işlemi başlatılıyor...');

            Log::info("✏️ BULK UPDATE PORTFOLIOS JOB BAŞLADI", [
                'portfolio_count' => $totalItems,
                'update_data' => $this->updateData,
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId
            ]);

            // Güvenlik kontrolü - sadece izin verilen alanları güncelle
            $allowedFields = [
                'is_active', 'status', 'featured', 'sort_order', 
                'portfolio_category_id', 'visibility', 'priority'
            ];
            
            $validatedData = array_intersect_key($this->updateData, array_flip($allowedFields));
            
            if (empty($validatedData)) {
                throw new \Exception('Güncellenecek geçerli alan bulunamadı');
            }

            foreach ($this->portfolioIds as $index => $portfolioId) {
                try {
                    $portfolio = Portfolio::find($portfolioId);
                    
                    if (!$portfolio) {
                        $errorCount++;
                        $errors[] = "Portfolio ID {$portfolioId} bulunamadı";
                        continue;
                    }

                    // Portfolio güncelleme
                    $oldData = $portfolio->toArray();
                    $portfolio->update($validatedData);
                    
                    // Activity log
                    activity()
                        ->causedBy($this->userId)
                        ->performedOn($portfolio)
                        ->withProperties([
                            'old' => array_intersect_key($oldData, $validatedData),
                            'new' => $validatedData
                        ])
                        ->log('bulk_updated');

                    $processedCount++;
                    
                    // Progress güncelle
                    if (($index + 1) % 10 === 0 || ($index + 1) === $totalItems) {
                        $progress = (($index + 1) / $totalItems) * 90;
                        $this->updateProgress($progress, 'processing', 
                            "Güncelleniyor: " . ($index + 1) . "/{$totalItems}");
                    }

                    Log::info("✅ Portfolio güncellendi", [
                        'portfolio_id' => $portfolioId,
                        'changes' => $validatedData
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Portfolio {$portfolioId}: " . $e->getMessage();
                    
                    Log::error("❌ Portfolio güncelleme hatası", [
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
            
            $this->updateProgress(100, 'completed', 'Portfolio güncelleme işlemi tamamlandı!', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'error_messages' => $errors,
                'duration' => $duration,
                'updated_fields' => array_keys($validatedData)
            ]);

            Log::info("✅ BULK UPDATE PORTFOLIOS TAMAMLANDI", [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'duration' => "{$duration}s"
            ]);

        } catch (\Exception $e) {
            Log::error("💥 BULK UPDATE PORTFOLIOS GENEL HATA", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->updateProgress(100, 'failed', 'Toplu güncelleme işlemi başarısız!', [
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
        Log::error("❌ BULK UPDATE PORTFOLIOS JOB BAŞARISIZ", [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
        
        $this->updateProgress(100, 'failed', 'Portfolio güncelleme işlemi başarısız!', [
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
        ], 3600);
    }

    /**
     * Clear portfolio related caches
     */
    private function clearPortfolioCache(): void
    {
        try {
            Cache::forget('portfolios_list');
            Cache::forget('featured_portfolios');
            Cache::forget('portfolio_categories');
            
            Log::info("🧹 Portfolio cache temizlendi");
        } catch (\Exception $e) {
            Log::warning("⚠️ Portfolio cache temizleme hatası", ['error' => $e->getMessage()]);
        }
    }
}