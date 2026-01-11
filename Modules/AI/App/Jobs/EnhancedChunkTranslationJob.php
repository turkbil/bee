<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Services\EnhancedHtmlTranslationService;
use Modules\AI\App\Services\AIService;
use Exception;

/**
 * 🚀 ENHANCED CHUNK TRANSLATION JOB
 * A1 Bulk Translation + Queue + Chunk sistemi
 * 
 * Her chunk'ı bağımsız olarak işler ve sonuçları birleştirir
 */
class EnhancedChunkTranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 dakika timeout
    public $tries = 3;
    public $maxExceptions = 3;

    public function __construct(
        private string $translationId,
        private int $chunkIndex,
        private int $totalChunks,
        private string $htmlChunk,
        private string $sourceLanguage,
        private string $targetLanguage,
        private array $options = []
    ) {}

    /**
     * Job execution
     */
    public function handle(EnhancedHtmlTranslationService $translationService, AIService $aiService): void
    {
        $startTime = microtime(true);
        
        Log::info('🔄 Enhanced Chunk Translation Job started', [
            'translation_id' => $this->translationId,
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'chunk_length' => strlen($this->htmlChunk),
            'source' => $this->sourceLanguage,
            'target' => $this->targetLanguage
        ]);

        try {
            // Progress update - chunk başladı
            $progressPercentage = 10 + (($this->chunkIndex / $this->totalChunks) * 80);
            $translationService->updateProgress(
                $this->translationId,
                $progressPercentage,
                "Chunk {$this->chunkIndex + 1}/{$this->totalChunks} çevriliyor..."
            );

            // A1 style bulk translation kullan
            $translatedChunk = $translationService->translateHtmlContentBulk(
                $this->htmlChunk,
                $this->sourceLanguage,
                $this->targetLanguage,
                $this->options['context'] ?? 'enhanced_html_chunk'
            );

            // Chunk sonucunu cache'e kaydet
            $this->storeChunkResult($translatedChunk);

            // Tüm chunk'lar tamamlandı mı kontrol et
            $this->checkCompletionAndMerge($translationService);

            $executionTime = microtime(true) - $startTime;
            Log::info('✅ Enhanced Chunk Translation Job completed', [
                'translation_id' => $this->translationId,
                'chunk_index' => $this->chunkIndex,
                'execution_time' => round($executionTime, 2) . 's',
                'original_length' => strlen($this->htmlChunk),
                'translated_length' => strlen($translatedChunk)
            ]);

        } catch (Exception $e) {
            $this->handleFailure($e, $translationService);
            throw $e;
        }
    }

    /**
     * Chunk sonucunu cache'e kaydet
     */
    private function storeChunkResult(string $translatedChunk): void
    {
        $chunkKey = "chunk_{$this->translationId}_{$this->chunkIndex}";
        
        $chunkData = [
            'translation_id' => $this->translationId,
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'original_chunk' => $this->htmlChunk,
            'translated_chunk' => $translatedChunk,
            'completed_at' => now(),
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage
        ];

        Cache::put($chunkKey, $chunkData, now()->addHours(4));
        
        Log::info('💾 Chunk result stored', [
            'chunk_key' => $chunkKey,
            'chunk_index' => $this->chunkIndex,
            'translated_length' => strlen($translatedChunk)
        ]);
    }

    /**
     * Tüm chunk'ların tamamlanıp tamamlanmadığını kontrol et ve birleştir
     */
    private function checkCompletionAndMerge(EnhancedHtmlTranslationService $translationService): void
    {
        // Tamamlanan chunk'ları say
        $completedChunks = $this->getCompletedChunks();
        $completedCount = count($completedChunks);

        Log::info('🔍 Checking completion status', [
            'translation_id' => $this->translationId,
            'completed_chunks' => $completedCount,
            'total_chunks' => $this->totalChunks
        ]);

        // Progress update
        $progressPercentage = 10 + (($completedCount / $this->totalChunks) * 80);
        $translationService->updateProgress(
            $this->translationId,
            $progressPercentage,
            "Chunk {$completedCount}/{$this->totalChunks} tamamlandı"
        );

        // Tüm chunk'lar tamamlandıysa birleştir
        if ($completedCount >= $this->totalChunks) {
            $this->mergeAllChunks($translationService, $completedChunks);
        }
    }

    /**
     * Tamamlanan chunk'ları getir
     */
    private function getCompletedChunks(): array
    {
        $completedChunks = [];
        
        for ($i = 0; $i < $this->totalChunks; $i++) {
            $chunkKey = "chunk_{$this->translationId}_{$i}";
            $chunkData = Cache::get($chunkKey);
            
            if ($chunkData && isset($chunkData['translated_chunk'])) {
                $completedChunks[$i] = $chunkData;
            }
        }
        
        return $completedChunks;
    }

    /**
     * Tüm chunk'ları birleştir ve final result oluştur
     */
    private function mergeAllChunks(EnhancedHtmlTranslationService $translationService, array $completedChunks): void
    {
        Log::info('🔗 Merging all chunks', [
            'translation_id' => $this->translationId,
            'total_chunks' => count($completedChunks)
        ]);

        $translationService->updateProgress($this->translationId, 90, 'Chunk\'lar birleştiriliyor...');

        // Chunk'ları index sırasına göre sırala ve birleştir
        ksort($completedChunks);
        
        $finalTranslatedHtml = '';
        $mergeStats = [
            'original_total_length' => 0,
            'translated_total_length' => 0,
            'chunks_merged' => 0
        ];

        foreach ($completedChunks as $chunkIndex => $chunkData) {
            $finalTranslatedHtml .= $chunkData['translated_chunk'];
            
            $mergeStats['original_total_length'] += strlen($chunkData['original_chunk']);
            $mergeStats['translated_total_length'] += strlen($chunkData['translated_chunk']);
            $mergeStats['chunks_merged']++;
        }

        // Final result'ı kaydet
        $finalResult = [
            'translation_id' => $this->translationId,
            'status' => 'completed',
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage,
            'final_translated_html' => $finalTranslatedHtml,
            'merge_stats' => $mergeStats,
            'completed_at' => now(),
            'processing_method' => $this->totalChunks > 1 ? 'chunked_translation' : 'bulk_translation'
        ];

        Cache::put("final_result_{$this->translationId}", $finalResult, now()->addDays(1));

        // Progress completion
        $translationService->updateProgress($this->translationId, 100, 'Çeviri tamamlandı! 🎉');

        // Chunk cache'lerini temizle (final result varken gerekmez)
        $this->cleanupChunkCache();

        Log::info('✅ All chunks merged successfully', [
            'translation_id' => $this->translationId,
            'processing_method' => $finalResult['processing_method'],
            'merge_stats' => $mergeStats,
            'final_html_length' => strlen($finalTranslatedHtml)
        ]);
    }

    /**
     * Chunk cache'lerini temizle
     */
    private function cleanupChunkCache(): void
    {
        for ($i = 0; $i < $this->totalChunks; $i++) {
            $chunkKey = "chunk_{$this->translationId}_{$i}";
            Cache::forget($chunkKey);
        }
        
        Log::info('🧹 Chunk cache cleaned up', [
            'translation_id' => $this->translationId,
            'chunks_cleaned' => $this->totalChunks
        ]);
    }

    /**
     * Hata durumunda işlem
     */
    private function handleFailure(Exception $e, EnhancedHtmlTranslationService $translationService): void
    {
        Log::error('❌ Enhanced Chunk Translation Job failed', [
            'translation_id' => $this->translationId,
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'error' => $e->getMessage(),
            'attempt' => $this->attempts()
        ]);

        // Progress update - hata
        $translationService->updateProgress(
            $this->translationId,
            max(10, 10 + (($this->chunkIndex / $this->totalChunks) * 80)),
            "Chunk {$this->chunkIndex + 1} hatası: " . substr($e->getMessage(), 0, 100)
        );

        // Chunk failure kaydet
        $failureData = [
            'translation_id' => $this->translationId,
            'chunk_index' => $this->chunkIndex,
            'error' => $e->getMessage(),
            'failed_at' => now(),
            'attempt_number' => $this->attempts()
        ];

        Cache::put("chunk_failure_{$this->translationId}_{$this->chunkIndex}", $failureData, now()->addHours(2));
    }

    /**
     * Job failed permanently
     */
    public function failed(Exception $exception): void
    {
        Log::critical('🚫 Enhanced Chunk Translation Job failed permanently', [
            'translation_id' => $this->translationId,
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'error' => $exception->getMessage(),
            'attempts' => $this->tries
        ]);

        // Final failure durumu kaydet
        $progressData = Cache::get("enhanced_translation_{$this->translationId}", []);
        $progressData['status'] = 'failed';
        $progressData['error'] = $exception->getMessage();
        $progressData['failed_chunk'] = $this->chunkIndex;
        $progressData['failed_at'] = now();

        Cache::put("enhanced_translation_{$this->translationId}", $progressData, now()->addHours(2));
    }
}