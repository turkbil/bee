<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\app\Services\Content\AIContentGeneratorService;
use Modules\AI\App\Events\ContentGenerationCompleted;
use Modules\AI\App\Events\ContentGenerationFailed;
use Modules\AI\App\Models\AIContentJob;
use App\Models\Tenant;

/**
 * AI Content Generation Job
 *
 * Horizon üzerinden yönetilen async AI içerik üretimi job'u
 * Real-time progress tracking ve error handling ile
 */
class AIContentGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Job ayarları
    public $timeout = 600; // 10 dakika - Büyük PDF'ler için arttırıldı
    public $tries = 2; // Bir kez daha dene
    public $maxExceptions = 2;
    public $backoff = [30, 60]; // Retry arası bekle

    // Job parametreleri (GLOBAL Pattern)
    protected array $params;
    protected ?string $jobId;
    protected int $tenantId;
    protected ?int $userId;
    protected $component;
    protected ?string $sessionId;

    /**
     * AIContentGenerationJob constructor - GLOBAL PATTERN
     *
     * @param array $params AI content generation parametreleri
     * @param string|null $jobId Job ID for tracking (AI Translation pattern)
     */
    public function __construct(array $params, ?string $jobId = null)
    {
        $this->params = $params;
        $this->jobId = $jobId;
        $this->tenantId = $params['tenant_id'] ?? tenant('id') ?? 1;
        $this->userId = auth()->id();
        $this->component = $params['component'] ?? $params['module'] ?? 'page';
        $this->sessionId = $params['sessionId'] ?? session()->getId() ?? null;

        // 🚀 QUEUE AYARI: Varsayılan bağlantı yoksa sync'e düş
        $this->onQueue('ai-content');
        $connection = config('queue.default', 'sync') ?: 'sync';
        try {
            $this->onConnection($connection);
        } catch (\Throwable $e) {
            // Beklenmez ama emniyet: sync'e düş
            $this->onConnection('sync');
        }
        $this->delay(now()); // Hemen başlat
    }

    /**
     * Job execution
     */
    public function handle(): void
    {
        Log::info('🚀 AI Content Generation Job başladı', [
            'session_id' => $this->sessionId,
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'component' => $this->component,
            'prompt_preview' => substr($this->params['prompt'] ?? '', 0, 100),
            'content_type' => $this->params['content_type'] ?? 'auto',
            'page_title' => $this->params['page_title'] ?? null
        ]);

        // Job tracking kaydı oluştur/güncelle
        $jobRecord = $this->createOrUpdateJobRecord();

        try {
            // Job'u başlatıldı olarak işaretle
            $jobRecord->updateStatus('processing');

            // Progress tracking başlat
            $this->updateProgress(0, 'İşlem başlatılıyor...', $jobRecord);

            // Tenant context'ini set et (multi-tenancy için)
            if ($this->tenantId) {
                tenancy()->initialize($this->tenantId);
            }

            $this->updateProgress(10, 'Tema analizi başlıyor...', $jobRecord);

            // GLOBAL AI Content Generator Service'i başlat
            $contentGenerator = app(AIContentGeneratorService::class);

            $this->updateProgress(30, 'AI ile içerik üretiliyor...', $jobRecord);

            // İçerik üret - ana işlem
            $result = $contentGenerator->generateContent($this->params);

            $this->updateProgress(80, 'İçerik işleniyor...', $jobRecord);

            // Sonucu validate et
            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'İçerik üretimi başarısız');
            }

            $this->updateProgress(95, 'İşlem tamamlanıyor...', $jobRecord);

            // Database'e başarılı sonucu kaydet
            $jobRecord->recordSuccess(
                $result['content'],
                $result['credits_used'] ?? 15,
                $result['meta'] ?? []
            );

            // Başarılı sonucu cache'e koy
            $cacheKey = "ai_content_result_{$this->sessionId}";
            Cache::put($cacheKey, [
                'success' => true,
                'content' => $result['content'],
                'credits_used' => $result['credits_used'] ?? 15,
                'meta' => $result['meta'] ?? [],
                'generated_at' => now()->toISOString(),
                'session_id' => $this->sessionId
            ], 300); // 5 dakika cache

            // Progress cache'e de content ekle (getJobResult için)
            $this->updateProgressWithContent(100, 'İçerik başarıyla üretildi!', $result['content'], $jobRecord);

            Log::info('✅ AI Content Generation başarılı', [
                'session_id' => $this->sessionId,
                'content_length' => strlen($result['content']),
                'credits_used' => $result['credits_used'],
                'component' => $this->component
            ]);

            // Başarı event'ini fırlat
            event(new ContentGenerationCompleted(
                $this->sessionId,
                $this->component,
                $result,
                $this->tenantId,
                $this->userId
            ));

            // Tenant kredi bakiyesini güncelle
            $this->updateTenantCredits($result['credits_used'] ?? 15);

        } catch (\Exception $e) {
            $this->handleJobFailure($e, $jobRecord);
        }
    }

    /**
     * Job tracking kaydı oluştur/güncelle
     */
    private function createOrUpdateJobRecord(): AIContentJob
    {
        return AIContentJob::updateOrCreate(
            ['session_id' => $this->sessionId],
            [
                'tenant_id' => $this->tenantId,
                'user_id' => $this->userId,
                'component' => $this->component,
                'parameters' => $this->params,
                'content_type' => $this->params['content_type'] ?? null,
                'page_title' => $this->params['page_title'] ?? null,
                'status' => 'pending'
            ]
        );
    }

    /**
     * Progress tracking güncelle (hem cache hem database)
     */
    private function updateProgress(int $percentage, string $message, ?AIContentJob $jobRecord = null): void
    {
        $this->updateProgressWithContent($percentage, $message, null, $jobRecord);
    }

    /**
     * Progress tracking güncelle with content (completed için)
     */
    private function updateProgressWithContent(int $percentage, string $message, ?string $content = null, ?AIContentJob $jobRecord = null): void
    {
        $status = $percentage === 100 ? 'completed' : 'processing';
        // Eğer hata mesajı ise 'failed' olarak işaretle (frontend zaman aşımı olmasın)
        if (stripos($message, 'Hata:') === 0) {
            $status = 'failed';
        }

        $cacheData = [
            'status' => $status,
            'progress' => $percentage,
            'message' => $message,
            'updated_at' => now()->toISOString(),
            'session_id' => $this->sessionId
        ];

        // Content varsa ekle
        if ($content !== null) {
            $cacheData['content'] = $content;
            $cacheData['credits_used'] = 15; // Default credit
        }

        // Cache güncelle - hem jobId hem sessionId ile (AI Translation pattern)
        $this->safeCachePut("ai_content_job:{$this->jobId}", $cacheData, 600);
        $this->safeCachePut("ai_content_progress_{$this->jobId}", $cacheData, 300); // Frontend için
        $this->safeCachePut("ai_content_job:{$this->sessionId}", $cacheData, 600);
        $this->safeCachePut("ai_content_progress_{$this->sessionId}", $cacheData, 300); // Backward compatibility

        // Database güncelle (persistent tracking için)
        if ($jobRecord) {
            $jobRecord->updateProgress($percentage, $message);
        }

        Log::info("📊 Progress: {$percentage}% - {$message}", [
            'session_id' => $this->sessionId,
            'job_id' => $this->jobId,
            'has_content' => $content !== null
        ]);
    }

    private function safeCachePut(string $key, $value, int $ttlSeconds): void
    {
        try {
            Cache::put($key, $value, $ttlSeconds);
        } catch (\Throwable $e) {
            Log::warning('⚠️ Default cache put failed, falling back to file store', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            try {
                Cache::store('file')->put($key, $value, $ttlSeconds);
            } catch (\Throwable $e2) {
                Log::error('❌ File cache put failed', ['key' => $key, 'error' => $e2->getMessage()]);
            }
        }
    }

    /**
     * Tenant kredi bakiyesini güncelle
     */
    private function updateTenantCredits(int $creditsUsed): void
    {
        try {
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                $tenant->ai_credits_balance = max(0, $tenant->ai_credits_balance - $creditsUsed);
                $tenant->ai_last_used_at = now();
                $tenant->save();

                Log::info('💰 Tenant kredi bakiyesi güncellendi', [
                    'tenant_id' => $this->tenantId,
                    'credits_used' => $creditsUsed,
                    'new_balance' => $tenant->ai_credits_balance
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Tenant kredi güncellemesi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Job başarısızlık durumunu handle et
     */
    private function handleJobFailure(\Exception $e, ?AIContentJob $jobRecord = null): void
    {
        Log::error('❌ AI Content Generation Job başarısız', [
            'session_id' => $this->sessionId,
            'tenant_id' => $this->tenantId,
            'component' => $this->component,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'attempts' => $this->attempts()
        ]);

        // Database'e hata kaydı
        if ($jobRecord) {
            $jobRecord->recordError($e->getMessage(), true);
        }

        // Hata durumunu cache'e koy
        $errorKey = "ai_content_error_{$this->sessionId}";
        Cache::put($errorKey, [
            'success' => false,
            'error' => $e->getMessage(),
            'session_id' => $this->sessionId,
            'failed_at' => now()->toISOString(),
            'attempts' => $this->attempts()
        ], 300);

        // Progress'i hata olarak güncelle
        $this->updateProgress(0, 'Hata: ' . $e->getMessage(), $jobRecord);

        // Hata event'ini fırlat
        event(new ContentGenerationFailed(
            $this->sessionId,
            $this->component,
            $e->getMessage(),
            $this->tenantId,
            $this->userId
        ));

        throw $e; // Job'u başarısız olarak işaretle
    }

    /**
     * Job completely failed
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💥 AI Content Generation Job tamamen başarısız', [
            'session_id' => $this->sessionId,
            'tenant_id' => $this->tenantId,
            'component' => $this->component,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries
        ]);

        // Final failure cache
        $errorKey = "ai_content_error_{$this->sessionId}";
        Cache::put($errorKey, [
            'success' => false,
            'error' => $exception->getMessage(),
            'session_id' => $this->sessionId,
            'failed_at' => now()->toISOString(),
            'attempts' => $this->attempts(),
            'final_failure' => true
        ], 600); // 10 dakika cache (daha uzun)

        // Final failure event
        event(new ContentGenerationFailed(
            $this->sessionId,
            $this->component,
            $exception->getMessage(),
            $this->tenantId,
            $this->userId,
            true // final failure
        ));
    }

    /**
     * Job unique ID for Horizon monitoring
     */
    public function uniqueId(): string
    {
        return "ai_content_{$this->sessionId}";
    }

    /**
     * Job tags for Horizon filtering
     */
    public function tags(): array
    {
        return [
            'ai-content',
            "tenant:{$this->tenantId}",
            "component:{$this->component}",
            "session:{$this->sessionId}"
        ];
    }
}
