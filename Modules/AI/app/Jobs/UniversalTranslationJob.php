<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AI\App\Services\UniversalTranslationService;

/**
 * Universal Translation Job
 * 
 * Registry-based çeviri sistemi için universal job
 * Herhangi bir entity'yi çevirebilir
 */
class UniversalTranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 dakika
    public $tries = 3;
    
    protected string $entityType;
    protected int $entityId;
    protected string $sourceLanguage;
    protected array $targetLanguages;
    protected string $sessionId;
    protected array $entityConfig;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $entityType,
        int $entityId,
        string $sourceLanguage,
        array $targetLanguages,
        string $sessionId,
        array $entityConfig
    ) {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguages = $targetLanguages;
        $this->sessionId = $sessionId;
        $this->entityConfig = $entityConfig;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('🚀 Universal translation job started', [
            'session_id' => $this->sessionId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'source' => $this->sourceLanguage,
            'targets' => $this->targetLanguages
        ]);

        try {
            $service = new UniversalTranslationService();
            
            $result = $service->translateEntity(
                $this->entityType,
                $this->entityId,
                $this->sourceLanguage,
                $this->targetLanguages,
                $this->entityConfig,
                $this->sessionId
            );

            if ($result) {
                \Log::info('✅ Universal translation completed successfully', [
                    'session_id' => $this->sessionId,
                    'entity_type' => $this->entityType,
                    'entity_id' => $this->entityId
                ]);
                
                // Başarı event'i fırlat
                event(new \Modules\AI\App\Events\TranslationCompleted(
                    $this->sessionId,
                    $this->entityType,
                    $this->entityId,
                    $result
                ));
                
                // Livewire event'ini de emit et
                \Livewire\Livewire::emit('translationCompleted', [
                    'sessionId' => $this->sessionId,
                    'entityType' => $this->entityType,
                    'entityId' => $this->entityId,
                    'result' => $result,
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                throw new \Exception('Translation service returned false');
            }

        } catch (\Exception $e) {
            \Log::error('❌ Universal translation job failed', [
                'session_id' => $this->sessionId,
                'entity_type' => $this->entityType,
                'entity_id' => $this->entityId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hata event'i fırlat
            event(new \Modules\AI\App\Events\TranslationFailed(
                $this->entityType,
                $this->entityId,
                $this->sessionId,
                $e->getMessage()
            ));

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('💥 Universal translation job completely failed', [
            'session_id' => $this->sessionId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Final failure event'i fırlat
        event(new \Modules\AI\App\Events\TranslationFailed(
            $this->entityType,
            $this->entityId,
            $this->sessionId,
            $exception->getMessage(),
            true // final failure
        ));
    }
}