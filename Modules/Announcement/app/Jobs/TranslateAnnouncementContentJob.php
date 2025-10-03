<?php

namespace Modules\Announcement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Announcement\App\Models\Announcement;
use Modules\AI\App\Services\AIService;

class TranslateAnnouncementContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600; // 10 dakika - çeviri uzun sürebilir
    public array $backoff = [60, 120, 300]; // Retry delays: 1min, 2min, 5min

    protected array $data;
    protected int $pageId;
    protected string $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $pageId)
    {
        $this->data = $data;
        $this->pageId = $pageId;
        $this->progressKey = "page_translation_progress_{$pageId}_" . uniqid();

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

            Log::info("🔄 QUEUE PAGE ÇEVİRİ BAŞLADI", [
                'announcement_id' => $this->pageId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages,
                'fields' => $fields,
                'progress_key' => $this->progressKey
            ]);

            // Sayfayı bul
            $announcement = Announcement::findOrFail($this->pageId);

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
                        $sourceText = $announcement->getTranslated($field, $sourceLanguage);

                        if (empty($sourceText)) {
                            $currentOperation++;
                            continue;
                        }

                        // Mevcut çeviri kontrolü
                        $existingTranslation = $announcement->getTranslated($field, $targetLanguage);
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
                            $currentData = $announcement->{$field};
                            if (is_string($currentData)) {
                                $currentData = json_decode($currentData, true) ?: [];
                            }

                            // Çeviriyi ekle
                            $currentData[$targetLanguage] = $translatedText;

                            // Güncelle
                            $announcement->update([$field => $currentData]);

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
                        'announcement_id' => $this->pageId,
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
                    'announcement_id' => $this->pageId,
                    'translated_count' => $translatedCount,
                    'messages' => $messages,
                    'success' => true
                ]);

                Log::info("🎉 QUEUE PAGE ÇEVİRİ TAMAMLANDI", [
                    'announcement_id' => $this->pageId,
                    'translated_count' => $translatedCount,
                    'messages' => $messages
                ]);
            } else {
                $this->updateProgress(100, 'Çeviri yapılacak içerik bulunamadı', true, [
                    'announcement_id' => $this->pageId,
                    'success' => false,
                    'message' => 'Çeviri yapılacak içerik bulunamadı'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("💥 QUEUE PAGE ÇEVİRİ GENEL HATA", [
                'announcement_id' => $this->pageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress(100, 'Çeviri hatası: ' . $e->getMessage(), true, [
                'announcement_id' => $this->pageId,
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
        Log::error("❌ QUEUE PAGE ÇEVİRİ JOB BAŞARISIZ", [
            'announcement_id' => $this->pageId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->updateProgress(100, 'Çeviri işlemi başarısız oldu', true, [
            'announcement_id' => $this->pageId,
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
            'announcement_id' => $this->pageId,
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
