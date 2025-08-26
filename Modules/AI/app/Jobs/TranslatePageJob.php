<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\FastHtmlTranslationService;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use App\Services\TenantQueueService;

class TranslatePageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 🔥 5 dakika MAX - Tenant izolasyonu için kısa
    public $tries = 2; // 2 kez dene
    public $backoff = [15, 30]; // Hızlı retry - tenant blocking önleme
    // 🎆 Queue dynamic olarak set edilecek (constructor'da)
    
    protected $pageId;
    protected $sourceLanguage;
    protected $targetLanguages; // ARRAY olarak değiştirildi
    protected $overwriteExisting;
    protected $sessionId;
    protected $userId;
    protected $tenantId;

    /**
     * 🌍 PAGE ÇEVİRİ JOB - Background Processing for Multiple Languages
     */
    public function __construct(int $pageId, string $sourceLanguage, array $targetLanguages, bool $overwriteExisting = true, ?string $sessionId = null)
    {
        $this->pageId = $pageId;
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguages = $targetLanguages;
        $this->overwriteExisting = $overwriteExisting;
        $this->sessionId = $sessionId;
        $this->userId = auth()->id(); // Current user
        $this->tenantId = tenant()?->id;
        
        // 🎆 TENANT ISOLATED QUEUE - Diğer tenant'ları etkilemez
        $this->onQueue('tenant_isolated');
        
        Log::info('🚀 Multi-Language Translation Job Created with Tenant Isolation', [
            'page_id' => $pageId,
            'source' => $sourceLanguage,
            'targets' => $targetLanguages,
            'overwrite' => $overwriteExisting,
            'session_id' => $sessionId,
            'user_id' => $this->userId,
            'tenant_id' => $this->tenantId,
            'queue' => 'tenant_isolated'
        ]);
    }

    /**
     * 🔄 Job Middleware - Aynı anda tek sayfa için tek çeviri
     */
    public function middleware()
    {
        return [
            // Aynı sayfa için overlapping engelle
            (new WithoutOverlapping("translate_page_{$this->pageId}"))->dontRelease()->expireAfter(180),
            
            // AI API rate limiting (dakikada max 20 istek)
            (new RateLimited('ai-translation'))->releaseAfter(30),
            
            // Exception throttling (5 exception sonrası 2 dakika bekle)
            (new ThrottlesExceptions(5, 2 * 60))->backoff(15)
        ];
    }

    /**
     * 🎯 Job Execution - Multiple Languages Translation
     */
    public function handle(): void
    {
        try {
            // Tenant context'i ayarla (eğer multi-tenant ise)
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            // 👤 User context oluştur (conversation tracking için)
            if ($this->userId) {
                try {
                    $user = \App\Models\User::find($this->userId);
                    if ($user) {
                        auth()->setUser($user);
                    }
                } catch (\Exception $e) {
                    Log::warning('User context kurulamadı', [
                        'user_id' => $this->userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 📊 PROGRESS TRACKING BAŞLAT - 30%'den başla
            $this->updateProgress(30, 'processing', '🚀 Sayfa çevirisi başladı...');

            Log::info('🔥 Multi-Language Translation Job Started', [
                'job_id' => $this->job->getJobId(),
                'page_id' => $this->pageId,
                'source' => $this->sourceLanguage,
                'targets' => $this->targetLanguages,
                'session_id' => $this->sessionId,
                'attempt' => $this->attempts(),
                'timeout' => $this->timeout
            ]);

            // Page model'ini bul
            $page = \Modules\Page\App\Models\Page::find($this->pageId);
            if (!$page) {
                throw new \Exception("Page with ID {$this->pageId} not found");
            }

            $translatedCount = 0;
            $errors = [];
            $translatedLanguages = [];

            // Her hedef dil için çeviri yap
            $totalLanguages = count($this->targetLanguages);
            foreach ($this->targetLanguages as $index => $targetLanguage) {
                try {
                    // 📊 PROGRESS UPDATE - Her dil için progress artır (30-80 arası)
                    $progressPercentage = 30 + (($index / $totalLanguages) * 50);
                    $this->updateProgress(
                        $progressPercentage, 
                        'processing', 
                        "🌍 {$targetLanguage} diline çeviriliyor... (" . round($progressPercentage) . "%)",
                        ['current_language' => $targetLanguage, 'total_languages' => $totalLanguages]
                    );

                    Log::info("🔄 Translating to {$targetLanguage}", [
                        'page_id' => $this->pageId,
                        'source' => $this->sourceLanguage,
                        'target' => $targetLanguage
                    ]);

                    // Kaynak dil verilerini al
                    $sourceTitle = $page->getTranslated('title', $this->sourceLanguage);
                    $sourceBody = $page->getTranslated('body', $this->sourceLanguage);

                    if (empty($sourceTitle) && empty($sourceBody)) {
                        $errors[] = "No content in source language ({$this->sourceLanguage})";
                        continue;
                    }

                    $translatedData = [];

                    // Title çevir
                    if (!empty($sourceTitle)) {
                        $translatedTitle = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceTitle,
                            $this->sourceLanguage,
                            $targetLanguage,
                            ['context' => 'page_title', 'source' => 'async_job']
                        );
                        $translatedData['title'] = $translatedTitle;
                    }

                    // Body çevir
                    if (!empty($sourceBody)) {
                        $translatedBody = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceBody,
                            $this->sourceLanguage,
                            $targetLanguage,
                            ['context' => 'page_content', 'source' => 'async_job', 'preserve_html' => true]
                        );
                        $translatedData['body'] = $translatedBody;
                    }

                    // Slug oluştur
                    if (!empty($translatedData['title'])) {
                        $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                            \Modules\Page\App\Models\Page::class,
                            $translatedData['title'],
                            $targetLanguage,
                            'slug',
                            'page_id',
                            $this->pageId
                        );
                    }

                    // Çevrilmiş verileri kaydet
                    if (!empty($translatedData)) {
                        foreach ($translatedData as $field => $value) {
                            $currentData = $page->{$field} ?? [];
                            $currentData[$targetLanguage] = $value;
                            $page->{$field} = $currentData;
                        }
                        $page->save();
                        
                        $translatedCount++;
                        $translatedLanguages[] = $targetLanguage;

                        Log::info("✅ Translation completed for {$targetLanguage}", [
                            'page_id' => $this->pageId,
                            'target' => $targetLanguage,
                            'fields' => array_keys($translatedData)
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Translation error for {$targetLanguage}: " . $e->getMessage();
                    Log::error("❌ Translation error for {$targetLanguage}", [
                        'page_id' => $this->pageId,
                        'target' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Genel sonuç
            if ($translatedCount > 0) {
                // 📊 FINAL PROGRESS UPDATE - 100% tamamlandı
                $this->updateProgress(100, 'completed', '🎉 Çeviri tamamlandı! Sayfalar güncellendi.', [
                    'translated_count' => $translatedCount,
                    'translated_languages' => $translatedLanguages,
                    'completed' => true
                ]);

                Log::info('🎉 Multi-Language Translation Job Completed Successfully', [
                    'job_id' => $this->job->getJobId(),
                    'page_id' => $this->pageId,
                    'session_id' => $this->sessionId,
                    'translated_count' => $translatedCount,
                    'translated_languages' => $translatedLanguages,
                    'errors_count' => count($errors)
                ]);

                // Livewire event dispatch et (frontend'e bildirim)
                $this->dispatchTranslationComplete();

            } else {
                throw new \Exception('No translations completed: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            Log::error('❌ Multi-Language Translation Job Failed', [
                'job_id' => $this->job->getJobId(),
                'page_id' => $this->pageId,
                'session_id' => $this->sessionId,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Frontend'e error dispatch et
            $this->dispatchTranslationError($e->getMessage());

            // Exception'ı yeniden fırlat (retry mekanizması için)
            throw $e;
        }
    }

    /**
     * 🎉 Translation completion event dispatch
     */
    private function dispatchTranslationComplete(): void
    {
        try {
            // Livewire event ile frontend'e bildir
            // Event dispatch - Translation completed
            event(new \Modules\AI\app\Events\TranslationCompleted(
                $this->sessionId,
                'page',
                $this->pageId,
                ['success' => true, 'user_id' => $this->userId]
            ));

            Log::info('📡 Translation completion event dispatched', [
                'session_id' => $this->sessionId,
                'page_id' => $this->pageId
            ]);
        } catch (\Exception $e) {
            Log::warning('⚠️ Could not dispatch completion event', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * ❌ Translation error event dispatch
     */
    private function dispatchTranslationError(string $error): void
    {
        try {
            // Livewire event ile frontend'e hata bildir
            // Event dispatch - Translation error
            event(new \Modules\AI\app\Events\TranslationError(
                $this->sessionId,
                'page',
                $this->pageId,
                $error,
                ['user_id' => $this->userId]
            ));

            Log::info('📡 Translation error event dispatched', [
                'session_id' => $this->sessionId,
                'page_id' => $this->pageId,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            Log::warning('⚠️ Could not dispatch error event', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 💀 Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💀 Multi-Language Translation Job Failed Permanently', [
            'page_id' => $this->pageId,
            'source' => $this->sourceLanguage,
            'targets' => $this->targetLanguages,
            'session_id' => $this->sessionId,
            'user_id' => $this->userId,
            'tenant_id' => $this->tenantId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Progress'i failed olarak güncelle
        $this->updateProgress(0, 'failed', '❌ Çeviri başarısız oldu: ' . $exception->getMessage(), [
            'error' => true,
            'exception' => get_class($exception)
        ]);

        // Frontend'e final error dispatch et
        $this->dispatchTranslationError($exception->getMessage());
        
        // Admin'e kritik hata bildirimi (AI API quota, server issues vb.)
        if ($this->isCriticalError($exception)) {
            $this->notifyAdminOfCriticalError($exception);
        }
    }
    
    /**
     * 🚨 Kritik hata kontrolü
     */
    private function isCriticalError(\Throwable $exception): bool
    {
        $criticalErrors = [
            'OpenAI API quota exceeded',
            'Authentication failed',
            'Service unavailable',
            'Connection timeout'
        ];
        
        foreach ($criticalErrors as $error) {
            if (str_contains($exception->getMessage(), $error)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 📧 Admin'e kritik hata bildirimi
     */
    private function notifyAdminOfCriticalError(\Throwable $exception): void
    {
        try {
            // Email notification (implement based on your mail system)
            Log::critical('🚨 Critical Translation System Error - Admin Notification Required', [
                'page_id' => $this->pageId,
                'tenant_id' => $this->tenantId,
                'error' => $exception->getMessage(),
                'time' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify admin of critical error', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 📊 PROGRESS UPDATE - Cache-based tracking sistem
     */
    private function updateProgress(float $percentage, string $status, string $message, array $additionalData = []): void
    {
        if (!$this->sessionId) {
            return;
        }

        try {
            // Cache'e progress bilgilerini yaz
            \Illuminate\Support\Facades\Cache::put("translation_progress_{$this->sessionId}", $percentage, 300);
            \Illuminate\Support\Facades\Cache::put("translation_status_{$this->sessionId}", $status, 300);
            \Illuminate\Support\Facades\Cache::put("translation_message_{$this->sessionId}", $message, 300);
            
            if (!empty($additionalData)) {
                \Illuminate\Support\Facades\Cache::put("translation_data_{$this->sessionId}", $additionalData, 300);
            }

            Log::info('📊 Translation progress updated', [
                'session_id' => $this->sessionId,
                'page_id' => $this->pageId,
                'progress' => $percentage,
                'status' => $status,
                'message' => $message,
                'additional_data' => $additionalData
            ]);

        } catch (\Exception $e) {
            Log::warning('⚠️ Progress update failed', [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🏷️ Job tags - monitoring için
     */
    public function tags(): array
    {
        return [
            'translation',
            'page',
            "page:{$this->pageId}",
            "session:{$this->sessionId}",
            "tenant:{$this->tenantId}"
        ];
    }
}