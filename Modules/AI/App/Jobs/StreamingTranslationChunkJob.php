<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Modules\AI\app\Services\FastHtmlTranslationService;
use Exception;
use Carbon\Carbon;

class StreamingTranslationChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 dakika per chunk
    public $tries = 2;
    public $backoff = [10, 30];
    public $maxExceptions = 1;

    protected $sessionId;
    protected $chunkId;
    protected $chunk;
    protected $targetLanguage;
    protected $context;
    protected $priority;
    protected $totalChunks;
    protected $chunkIndex;

    public function __construct(
        string $sessionId,
        string $chunkId,
        array $chunk,
        string $targetLanguage,
        array $context = [],
        int $priority = 0,
        int $totalChunks = 1,
        int $chunkIndex = 0
    ) {
        $this->sessionId = $sessionId;
        $this->chunkId = $chunkId;
        $this->chunk = $chunk;
        $this->targetLanguage = $targetLanguage;
        $this->context = $context;
        $this->priority = $priority;
        $this->totalChunks = $totalChunks;
        $this->chunkIndex = $chunkIndex;

        // Priority queue assignment
        if ($priority >= 80) {
            $this->onQueue('translation-critical');
        } elseif ($priority >= 60) {
            $this->onQueue('translation-high');
        } else {
            $this->onQueue('translation');
        }
    }

    public function handle()
    {
        $startTime = microtime(true);
        
        try {
            // İşlem başladığını bildir
            $this->broadcastProgress('chunk_started', [
                'chunk_id' => $this->chunkId,
                'chunk_index' => $this->chunkIndex,
                'total_chunks' => $this->totalChunks,
                'priority' => $this->priority,
                'estimated_duration' => $this->estimateProcessingTime(),
                'started_at' => Carbon::now()->toISOString()
            ]);

            // Translation memory kontrolü
            $translatedContent = $this->checkTranslationMemory();
            
            if (!$translatedContent) {
                // AI ile çeviri yap
                $translatedContent = $this->performTranslation();
                
                // Translation memory'ye kaydet
                $this->saveToTranslationMemory($translatedContent);
            }

            // İşlem tamamlandığını bildir
            $processingTime = microtime(true) - $startTime;
            $this->broadcastProgress('chunk_completed', [
                'chunk_id' => $this->chunkId,
                'chunk_index' => $this->chunkIndex,
                'translated_content' => $translatedContent,
                'processing_time' => round($processingTime, 2),
                'completed_at' => Carbon::now()->toISOString(),
                'from_cache' => isset($fromCache) && $fromCache
            ]);

            // Session'ın tamamlanıp tamamlanmadığını kontrol et
            $this->checkSessionCompletion();

            Log::info("StreamingTranslationChunkJob completed", [
                'session_id' => $this->sessionId,
                'chunk_id' => $this->chunkId,
                'processing_time' => $processingTime,
                'priority' => $this->priority
            ]);

        } catch (Exception $e) {
            $this->handleChunkError($e);
            throw $e;
        }
    }

    protected function performTranslation(): string
    {
        $translationService = app(FastHtmlTranslationService::class);
        
        // Chunk içeriğini hazırla
        $textContent = $this->chunk['content'] ?? '';
        $htmlStructure = $this->chunk['html_structure'] ?? [];
        
        if (empty($textContent)) {
            throw new Exception("Chunk content is empty");
        }

        // Context ile birlikte çeviri yap
        $translationContext = array_merge($this->context, [
            'chunk_type' => $this->chunk['type'] ?? 'content',
            'semantic_context' => $this->chunk['semantic_context'] ?? '',
            'surrounding_text' => $this->chunk['surrounding_text'] ?? '',
            'html_tags' => $this->chunk['html_tags'] ?? []
        ]);

        // Progress callback ile real-time bildirim
        $progressCallback = function($progress) {
            $this->broadcastProgress('chunk_progress', [
                'chunk_id' => $this->chunkId,
                'progress' => $progress,
                'timestamp' => Carbon::now()->toISOString()
            ]);
        };

        return $translationService->translateWithContext(
            $textContent,
            $this->targetLanguage,
            $translationContext,
            $progressCallback
        );
    }

    protected function checkTranslationMemory(): ?string
    {
        $memoryKey = $this->generateMemoryKey();
        
        try {
            $cached = Redis::hget("translation_memory:{$this->targetLanguage}", $memoryKey);
            
            if ($cached) {
                $cachedData = json_decode($cached, true);
                
                // Cache yaşını kontrol et (7 gün)
                $cacheAge = Carbon::now()->diffInDays(Carbon::parse($cachedData['created_at']));
                
                if ($cacheAge <= 7) {
                    // Cache hit metriği
                    Redis::hincrby("translation_stats:daily:" . Carbon::today()->format('Y-m-d'), 'cache_hits', 1);
                    
                    $this->broadcastProgress('chunk_cache_hit', [
                        'chunk_id' => $this->chunkId,
                        'cache_age_days' => $cacheAge
                    ]);
                    
                    return $cachedData['content'];
                }
            }
        } catch (Exception $e) {
            Log::warning("Translation memory check failed", [
                'session_id' => $this->sessionId,
                'chunk_id' => $this->chunkId,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    protected function saveToTranslationMemory(string $translatedContent): void
    {
        $memoryKey = $this->generateMemoryKey();
        
        try {
            $memoryData = [
                'content' => $translatedContent,
                'original_content' => $this->chunk['content'] ?? '',
                'context' => $this->context,
                'chunk_type' => $this->chunk['type'] ?? 'content',
                'created_at' => Carbon::now()->toISOString(),
                'usage_count' => 1
            ];
            
            Redis::hset(
                "translation_memory:{$this->targetLanguage}",
                $memoryKey,
                json_encode($memoryData)
            );
            
            // Memory usage metriği
            Redis::hincrby("translation_stats:daily:" . Carbon::today()->format('Y-m-d'), 'memory_saves', 1);
            
        } catch (Exception $e) {
            Log::warning("Translation memory save failed", [
                'session_id' => $this->sessionId,
                'chunk_id' => $this->chunkId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function generateMemoryKey(): string
    {
        $content = $this->chunk['content'] ?? '';
        $contextHash = md5(json_encode($this->context));
        
        return md5($content . $contextHash);
    }

    protected function broadcastProgress(string $event, array $data): void
    {
        try {
            $message = [
                'event' => $event,
                'session_id' => $this->sessionId,
                'data' => $data,
                'timestamp' => Carbon::now()->toISOString()
            ];
            
            // Redis pub/sub ile real-time bildirim
            Redis::publish("streaming_translation:{$this->sessionId}", json_encode($message));
            
            // Session durumunu güncelle
            Redis::hset("streaming_session:{$this->sessionId}", $event . "_" . $this->chunkId, json_encode($data));
            
        } catch (Exception $e) {
            Log::error("Progress broadcast failed", [
                'session_id' => $this->sessionId,
                'chunk_id' => $this->chunkId,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function checkSessionCompletion(): void
    {
        try {
            // Tamamlanan chunk'ları say
            $completedCount = Redis::hlen("streaming_session_completed:{$this->sessionId}");
            Redis::hset("streaming_session_completed:{$this->sessionId}", $this->chunkId, Carbon::now()->toISOString());
            $completedCount++;
            
            if ($completedCount >= $this->totalChunks) {
                // Tüm chunk'lar tamamlandı
                $this->broadcastProgress('session_completed', [
                    'total_chunks' => $this->totalChunks,
                    'completed_chunks' => $completedCount,
                    'completion_time' => Carbon::now()->toISOString()
                ]);
                
                // Session cleanup
                $this->cleanupSession();
                
                Log::info("Streaming translation session completed", [
                    'session_id' => $this->sessionId,
                    'total_chunks' => $this->totalChunks,
                    'target_language' => $this->targetLanguage
                ]);
            }
            
        } catch (Exception $e) {
            Log::error("Session completion check failed", [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function cleanupSession(): void
    {
        try {
            // Session verilerini temizle (24 saat sonra)
            Redis::expire("streaming_session:{$this->sessionId}", 86400);
            Redis::expire("streaming_session_completed:{$this->sessionId}", 86400);
            Redis::expire("streaming_session_errors:{$this->sessionId}", 86400);
            
        } catch (Exception $e) {
            Log::warning("Session cleanup failed", [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function estimateProcessingTime(): int
    {
        $contentLength = strlen($this->chunk['content'] ?? '');
        $baseTime = 5; // 5 saniye base
        
        // İçerik uzunluğuna göre ek süre
        $additionalTime = intval($contentLength / 100); // Her 100 karakter için +1 saniye
        
        // Öncelik faktörü
        $priorityFactor = ($this->priority >= 80) ? 0.5 : 1.0;
        
        return intval(($baseTime + $additionalTime) * $priorityFactor);
    }

    protected function handleChunkError(Exception $e): void
    {
        $errorData = [
            'chunk_id' => $this->chunkId,
            'chunk_index' => $this->chunkIndex,
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'attempt' => $this->attempts(),
            'max_attempts' => $this->tries,
            'failed_at' => Carbon::now()->toISOString()
        ];
        
        // Hata durumunu bildir
        $this->broadcastProgress('chunk_failed', $errorData);
        
        // Hata geçmişine kaydet
        Redis::hset("streaming_session_errors:{$this->sessionId}", $this->chunkId, json_encode($errorData));
        
        Log::error("StreamingTranslationChunkJob failed", [
            'session_id' => $this->sessionId,
            'chunk_id' => $this->chunkId,
            'error' => $e->getMessage(),
            'attempt' => $this->attempts()
        ]);
    }

    public function failed(Exception $exception)
    {
        // Son deneme de başarısız oldu
        $this->broadcastProgress('chunk_permanently_failed', [
            'chunk_id' => $this->chunkId,
            'chunk_index' => $this->chunkIndex,
            'final_error' => $exception->getMessage(),
            'total_attempts' => $this->tries,
            'failed_permanently_at' => Carbon::now()->toISOString()
        ]);
        
        // Diğer chunk'ların devam etmesi için session'ı kontrol et
        $this->checkSessionCompletion();
    }
}