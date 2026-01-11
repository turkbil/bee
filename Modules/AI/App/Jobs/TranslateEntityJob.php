<?php

namespace Modules\AI\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\AI\app\Services\FastHtmlTranslationService;
use Modules\AI\app\Events\TranslationProgressUpdated;
use Modules\AI\app\Events\TranslationCompleted;

class TranslateEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 dakika
    public $tries = 3;

    public function __construct(
        public string $entityType,
        public int $entityId,
        public string $sourceLanguage,
        public array $targetLanguages,
        public string $sessionId
    ) {}

    public function handle()
    {
        try {
            Log::info("🚀 TranslateEntityJob başlatıldı", [
                'entity_type' => $this->entityType,
                'entity_id' => $this->entityId,
                'source' => $this->sourceLanguage,
                'targets' => $this->targetLanguages,
                'session_id' => $this->sessionId
            ]);

            // Progress güncelleme - Hem broadcast hem cache
            broadcast(new TranslationProgressUpdated(
                $this->sessionId,
                10,
                "Çeviri başlatıldı..."
            ));
            
            // Cache'e de yaz (JavaScript polling için)
            $this->updateProgressCache(10, 'processing', 'Çeviri başlatıldı...');

            $translationService = app(FastHtmlTranslationService::class);
            
            $totalTargets = count($this->targetLanguages);
            $completed = 0;

            foreach ($this->targetLanguages as $targetLang) {
                Log::info("🌍 Çeviri başlatılıyor: {$this->sourceLanguage} → {$targetLang}");
                
                // Progress güncelleme
                $progress = 20 + (($completed / $totalTargets) * 60);
                broadcast(new TranslationProgressUpdated(
                    $this->sessionId,
                    (int)$progress,
                    "Çeviriliyor: {$this->sourceLanguage} → {$targetLang}"
                ));
                
                // Cache'e de yaz
                $this->updateProgressCache((int)$progress, 'processing', "Çeviriliyor: {$this->sourceLanguage} → {$targetLang}");

                $result = $translationService->translateEntity(
                    $this->entityType,
                    $this->entityId,
                    $this->sourceLanguage,
                    $targetLang
                );

                if (!$result['success']) {
                    Log::error("❌ Çeviri hatası: {$this->sourceLanguage} → {$targetLang}", [
                        'error' => $result['error'] ?? 'Bilinmeyen hata'
                    ]);
                    throw new \Exception("Çeviri hatası: {$result['error']}");
                }

                $completed++;
                Log::info("✅ Çeviri tamamlandı: {$this->sourceLanguage} → {$targetLang}");
            }

            // Final progress
            broadcast(new TranslationProgressUpdated(
                $this->sessionId,
                100,
                "Tüm çeviriler tamamlandı!"
            ));
            
            // Final cache update
            $this->updateProgressCache(100, 'completed', 'Tüm çeviriler tamamlandı!');

            // Completion event
            broadcast(new TranslationCompleted(
                $this->sessionId,
                $this->entityType,
                $this->entityId,
                [
                    'success' => true,
                    'message' => 'Tüm çeviriler başarıyla tamamlandı',
                    'completed_languages' => $this->targetLanguages
                ]
            ));

            Log::info("🎉 TranslateEntityJob başarıyla tamamlandı");

        } catch (\Exception $e) {
            Log::error("💥 TranslateEntityJob hatası", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            broadcast(new TranslationCompleted(
                $this->sessionId,
                $this->entityType,
                $this->entityId,
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            ));
            
            // Error cache update
            $this->updateProgressCache(0, 'failed', 'Çeviri hatası: ' . $e->getMessage());

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("💥 TranslateEntityJob failed", [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'error' => $exception->getMessage()
        ]);

        broadcast(new TranslationCompleted(
            $this->sessionId,
            $this->entityType,
            $this->entityId,
            [
                'success' => false,
                'error' => 'Çeviri işlemi başarısız: ' . $exception->getMessage()
            ]
        ));
        
        // Failed cache update
        $this->updateProgressCache(0, 'failed', 'Çeviri işlemi başarısız: ' . $exception->getMessage());
    }
    
    /**
     * 📊 Progress Cache Update - JavaScript polling için
     */
    private function updateProgressCache(int $percentage, string $status, string $message, array $additionalData = []): void
    {
        try {
            // JavaScript'in beklediği cache key formatını kullan
            \Illuminate\Support\Facades\Cache::put("progress:{$this->sessionId}", $percentage, 300);
            \Illuminate\Support\Facades\Cache::put("status:{$this->sessionId}", $status, 300);
            \Illuminate\Support\Facades\Cache::put("message:{$this->sessionId}", $message, 300);
            
            if (!empty($additionalData)) {
                \Illuminate\Support\Facades\Cache::put("data:{$this->sessionId}", $additionalData, 300);
            }

            Log::info('📊 Translation progress cache updated', [
                'session_id' => $this->sessionId,
                'entity_id' => $this->entityId,
                'progress' => $percentage,
                'status' => $status,
                'message' => $message,
                'additional_data' => $additionalData
            ]);

        } catch (\Exception $e) {
            Log::warning('⚠️ Progress cache update failed', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }
}