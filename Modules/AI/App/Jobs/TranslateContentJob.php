<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\Translation\CentralizedTranslationService;

class TranslateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 dakika timeout
    public $tries = 3; // 3 deneme hakkı
    
    private array $config;
    private string $operationId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $config, string $operationId)
    {
        $this->config = $config;
        $this->operationId = $operationId;
        $this->queue = 'translations'; // Özel queue
    }

    /**
     * Execute the job.
     */
    public function handle(CentralizedTranslationService $translationService): void
    {
        try {
            Log::info("🚀 Translation job started", [
                'operation_id' => $this->operationId,
                'items_count' => count($this->config['items'] ?? []),
                'languages' => $this->config['target_languages'] ?? []
            ]);

            // Progress başlat
            $totalLanguages = count($this->config['target_languages'] ?? []);
            $totalItems = count($this->config['items'] ?? []);
            
            cache()->put("translation_progress_{$this->operationId}", [
                'status' => 'processing',
                'progress' => 10,
                'message' => 'Çeviri işlemi başladı...',
                'total_languages' => $totalLanguages,
                'total_items' => $totalItems,
                'completed_languages' => [],
                'current_language' => null,
                'details' => []
            ], 600);

            // Progress callback ile çeviriyi yap
            $results = $translationService->translateItemsWithProgress(
                $this->config,
                function($language, $progress, $message) use ($totalLanguages, $totalItems) {
                    $currentProgress = cache()->get("translation_progress_{$this->operationId}");
                    
                    if ($currentProgress) {
                        $completedLanguages = $currentProgress['completed_languages'] ?? [];
                        
                        if ($progress === 100 && !in_array($language, $completedLanguages)) {
                            $completedLanguages[] = $language;
                        }
                        
                        cache()->put("translation_progress_{$this->operationId}", [
                            'status' => 'processing',
                            'progress' => min(90, 10 + (count($completedLanguages) / $totalLanguages * 80)),
                            'message' => $message,
                            'total_languages' => $totalLanguages,
                            'total_items' => $totalItems,
                            'completed_languages' => $completedLanguages,
                            'current_language' => $language,
                            'details' => [
                                'completed_count' => count($completedLanguages),
                                'remaining_count' => $totalLanguages - count($completedLanguages)
                            ]
                        ], 600);
                    }
                }
            );

            // Başarılı sonucu cache'e kaydet
            cache()->put("translation_progress_{$this->operationId}", [
                'status' => 'completed',
                'progress' => 100,
                'results' => $results,
                'message' => 'Çeviri tamamlandı!',
                'total_languages' => $totalLanguages,
                'total_items' => $totalItems,
                'completed_languages' => $this->config['target_languages'] ?? []
            ], 600);

            Log::info("✅ Translation job completed", [
                'operation_id' => $this->operationId,
                'results' => $results['summary'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Translation job failed", [
                'operation_id' => $this->operationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hata durumunu cache'e kaydet
            cache()->put("translation_progress_{$this->operationId}", [
                'status' => 'failed',
                'progress' => 0,
                'error' => $e->getMessage(),
                'message' => 'Çeviri başarısız: ' . $e->getMessage()
            ], 600);

            throw $e; // Retry için exception fırlat
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Translation job permanently failed", [
            'operation_id' => $this->operationId,
            'error' => $exception->getMessage()
        ]);

        cache()->put("translation_progress_{$this->operationId}", [
            'status' => 'failed',
            'progress' => 0,
            'error' => 'Çeviri işlemi başarısız oldu. Lütfen tekrar deneyin.',
            'message' => 'Çeviri başarısız'
        ], 600);
    }
}