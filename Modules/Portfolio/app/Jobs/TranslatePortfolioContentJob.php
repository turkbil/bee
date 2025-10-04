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
use Modules\AI\App\Services\AIService;

class TranslatePortfolioContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600; // 10 dakika - çeviri uzun sürebilir
    public array $backoff = [60, 120, 300]; // Retry delays: 1min, 2min, 5min

    protected array $data;
    protected int $portfolioId;
    protected string $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $portfolioId)
    {
        $this->data = $data;
        $this->portfolioId = $portfolioId;
        $this->progressKey = "portfolio_translation_progress_{$portfolioId}_" . uniqid();

        // Tenant-isolated queue kullan
        $this->onQueue('tenant_isolated');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->updateProgress(0, 'Çeviri işlemi başlatılıyor...');

            // Data'dan parametreleri çıkar
            $sourceLanguage = $this->data['sourceLanguage'] ?? 'tr';
            $targetLanguages = $this->data['targetLanguages'] ?? [];
            $fields = $this->data['fields'] ?? ['title', 'body'];
            $overwriteExisting = $this->data['overwriteExisting'] ?? true;

            Log::info("🔄 QUEUE PORTFOLIO ÇEVİRİ BAŞLADI", [
                'portfolio_id' => $this->portfolioId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages,
                'fields' => $fields,
                'progress_key' => $this->progressKey
            ]);

            // Sayfayı bul
            $portfolio = Portfolio::findOrFail($this->portfolioId);

            $totalOperations = count($targetLanguages) * count($fields);
            $currentOperation = 0;
            $translatedCount = 0;
            $messages = [];

            $this->updateProgress(10, 'Sayfa bulundu, çeviri başlıyor...');

            foreach ($targetLanguages as $targetLanguage) {
                if ($sourceLanguage === $targetLanguage) {
                    continue;
                }

                $this->updateProgress(
                    20 + ($currentOperation / $totalOperations) * 60,
                    "Çeviri yapılıyor: " . strtoupper($targetLanguage)
                );

                try {
                    foreach ($fields as $field) {
                        $sourceText = $portfolio->getTranslated($field, $sourceLanguage);

                        if (empty($sourceText)) {
                            $currentOperation++;
                            continue;
                        }

                        // Mevcut çeviri kontrolü
                        $existingTranslation = $portfolio->getTranslated($field, $targetLanguage);
                        if (!$overwriteExisting && !empty($existingTranslation)) {
                            $currentOperation++;
                            continue;
                        }

                        // AI çeviri yap
                        $translatedText = app(AIService::class)->translateText(
                            $sourceText,
                            $sourceLanguage,
                            $targetLanguage
                        );

                        if (!empty($translatedText) && $translatedText !== $sourceText) {
                            // Mevcut veriyi al
                            $currentData = $portfolio->{$field};
                            if (is_string($currentData)) {
                                $currentData = json_decode($currentData, true) ?: [];
                            }

                            // Çeviriyi ekle
                            $currentData[$targetLanguage] = $translatedText;

                            // Güncelle
                            $portfolio->update([$field => $currentData]);

                            Log::info("✅ Çeviri başarılı", [
                                'field' => $field,
                                'target' => $targetLanguage,
                                'original' => substr($sourceText, 0, 100),
                                'translated' => substr($translatedText, 0, 100)
                            ]);
                        }

                        $currentOperation++;
                    }

                    $translatedCount++;
                    $messages[] = strtoupper($targetLanguage) . ' çevirisi';
                } catch (\Exception $e) {
                    Log::error("❌ Çeviri hatası", [
                        'portfolio_id' => $this->portfolioId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);

                    // Hataları kaydet ama işleme devam et
                    $this->updateProgress(null, "Hata: {$targetLanguage} çevirisi başarısız");
                }
            }

            // Başarı durumu
            if ($translatedCount > 0) {
                $this->updateProgress(100, 'Çeviri tamamlandı!', true, [
                    'portfolio_id' => $this->portfolioId,
                    'translated_count' => $translatedCount,
                    'messages' => $messages,
                    'success' => true
                ]);

                Log::info("🎉 QUEUE PORTFOLIO ÇEVİRİ TAMAMLANDI", [
                    'portfolio_id' => $this->portfolioId,
                    'translated_count' => $translatedCount,
                    'messages' => $messages
                ]);
            } else {
                $this->updateProgress(100, 'Çeviri yapılacak içerik bulunamadı', true, [
                    'portfolio_id' => $this->portfolioId,
                    'success' => false,
                    'message' => 'Çeviri yapılacak içerik bulunamadı'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("💥 QUEUE PORTFOLIO ÇEVİRİ GENEL HATA", [
                'portfolio_id' => $this->portfolioId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress(100, 'Çeviri hatası: ' . $e->getMessage(), true, [
                'portfolio_id' => $this->portfolioId,
                'success' => false,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("❌ QUEUE PORTFOLIO ÇEVİRİ JOB BAŞARISIZ", [
            'portfolio_id' => $this->portfolioId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->updateProgress(100, 'Çeviri işlemi başarısız oldu', true, [
            'portfolio_id' => $this->portfolioId,
            'success' => false,
            'error' => $exception->getMessage(),
            'failed' => true
        ]);
    }

    /**
     * Update progress in cache
     */
    private function updateProgress(?int $percentage, string $message, bool $completed = false, array $data = []): void
    {
        $progress = [
            'percentage' => $percentage,
            'message' => $message,
            'completed' => $completed,
            'portfolio_id' => $this->portfolioId,
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        // 1 saat cache tutma
        Cache::put($this->progressKey, $progress, 3600);
    }

    /**
     * Get progress key for external monitoring
     */
    public function getProgressKey(): string
    {
        return $this->progressKey;
    }
}
