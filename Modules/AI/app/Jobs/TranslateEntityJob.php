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

            // Progress güncelleme
            broadcast(new TranslationProgressUpdated(
                $this->sessionId,
                10,
                "Çeviri başlatıldı..."
            ));

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
    }
}