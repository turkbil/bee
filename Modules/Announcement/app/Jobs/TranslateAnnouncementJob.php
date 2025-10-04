<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\AIService;
use Modules\Announcement\App\Models\Announcement;
use Throwable;

/**
 * 🚀 Announcement Translation Queue Job
 *
 * Bu job Announcement modülündeki sayfaları AI ile çevirir:
 * - Toplu çeviri işlemleri için optimize edilmiş
 * - Progress tracking ile durum takibi
 * - Token kullanımı hesaplama
 */
class TranslateAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 dakika

    public function __construct(
        public array $announcementIds,
        public string $sourceLanguage,
        public array $targetLanguages,
        public string $quality = 'balanced',
        public array $options = [],
        public string $operationId = ''
    ) {
        // Force Redis connection ve queue
        $this->onConnection('redis')->onQueue('tenant_isolated');
    }

    public function handle(): void
    {
        // 🚨 ULTRA DEBUG - Handle metoduna giriş
        Log::info('🔥🔥🔥 TRANSLATEPAGEJOB HANDLE() BAŞLADI! 🔥🔥🔥', [
            'announcementIds' => $this->announcementIds,
            'sourceLanguage' => $this->sourceLanguage,
            'targetLanguages' => $this->targetLanguages,
            'operationId' => $this->operationId,
            'queue' => $this->queue ?? 'default',
            'connection' => $this->connection ?? 'default',
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $aiService = app(AIService::class);

            // İlerleme durumunu güncelle
            $this->updateProgress('processing', 0);

            // Defensive: targetLanguages array olmalı
            $targetLanguages = is_array($this->targetLanguages) ? $this->targetLanguages : [$this->targetLanguages];
            $totalOperations = count($this->announcementIds) * count($targetLanguages);
            $processedCount = 0;
            $successCount = 0;
            $failedCount = 0;
            $totalTokensUsed = 0;

            foreach ($this->announcementIds as $announcementId) {
                $announcement = Announcement::find($announcementId);

                if (!$announcement) {
                    Log::warning("Announcement not found: {$announcementId}");
                    $failedCount++;
                    continue;
                }

                foreach ($this->targetLanguages as $targetLanguage) {
                    if ($targetLanguage === $this->sourceLanguage) {
                        continue; // Aynı dil, atla
                    }

                    try {
                        // Kaynak içeriği al
                        $title = is_array($announcement->title) ? ($announcement->title[$this->sourceLanguage] ?? '') : ($announcement->title ?? '');
                        $body = is_array($announcement->body) ? ($announcement->body[$this->sourceLanguage] ?? '') : ($announcement->body ?? '');

                        $sourceData = [
                            'title' => $title,
                            'body' => $body,
                            'excerpt' => ''
                        ];

                        // Boş içerik varsa atla
                        if (empty(trim($sourceData['title'] . $sourceData['body']))) {
                            Log::info("Empty content for announcement {$announcementId}, language {$this->sourceLanguage}");
                            $processedCount++;
                            continue;
                        }

                        // Title çeviri
                        $translatedTitle = $aiService->translateText(
                            $sourceData['title'],
                            $this->sourceLanguage,
                            $targetLanguage,
                            ['context' => 'announcement_title']
                        );

                        // Body HTML çeviri
                        $translatedBody = $aiService->translateHtml(
                            $sourceData['body'],
                            $this->sourceLanguage,
                            $targetLanguage
                        );

                        $response = [
                            'success' => true,
                            'response' => json_encode([
                                'title' => $translatedTitle,
                                'body' => $translatedBody,
                                'excerpt' => !empty($sourceData['excerpt']) ? $aiService->translateText($sourceData['excerpt'], $this->sourceLanguage, $targetLanguage, 'excerpt') : ''
                            ]),
                            'tokens_used' => 100
                        ];

                        if ($response['success'] ?? false) {
                            // Çeviri sonucunu parse et
                            $translatedData = $this->parseTranslationResponse($response['response'] ?? '');

                            // DEBUG: Çeviri sonucunu log'la
                            Log::info("🔍 AI Translation Response Debug", [
                                'announcement_id' => $announcementId,
                                'target_language' => $targetLanguage,
                                'raw_response' => substr($response['response'] ?? '', 0, 200),
                                'parsed_data' => $translatedData
                            ]);

                            // Sayfayı güncelle
                            $this->updatePageTranslation($announcement, $translatedData, $targetLanguage);

                            // 💰 KRİTİK: Her başarılı çeviri için 1 kredi düş
                            try {
                                $tenantId = (string) (tenancy()->tenant?->id ?? '1'); // String olarak cast
                                $perLanguageCost = 1.0; // Her dil = 1 kredi

                                ai_use_credits($perLanguageCost, $tenantId, [
                                    'usage_type' => 'translation',
                                    'description' => "Announcement Translation: #{$announcementId} ({$this->sourceLanguage} → {$targetLanguage})",
                                    'entity_type' => 'announcement',
                                    'entity_id' => $announcementId,
                                    'source_language' => $this->sourceLanguage,
                                    'target_language' => $targetLanguage,
                                    'provider_name' => 'announcement_translation_service',
                                    'tokens_used' => $response['tokens_used'] ?? 0
                                ]);

                                Log::info('💰 KREDİ DÜŞÜRÜLDİ: ANNOUNCEMENT ÇEVİRİ - 1 DİL = 1 KREDİ', [
                                    'announcement_id' => $announcementId,
                                    'language_pair' => "{$this->sourceLanguage} → {$targetLanguage}",
                                    'credits_deducted' => $perLanguageCost,
                                    'tenant_id' => $tenantId
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('⚠️ Kredi düşürme hatası (çeviri devam ediyor)', [
                                    'announcement_id' => $announcementId,
                                    'error' => $e->getMessage()
                                ]);
                            }

                            $successCount++;
                            $totalTokensUsed += $response['tokens_used'] ?? 0;

                            Log::info("Announcement {$announcementId} translated to {$targetLanguage} successfully");
                        } else {
                            $failedCount++;
                            Log::error("Translation failed for announcement {$announcementId} to {$targetLanguage}: " . ($response['error'] ?? 'Unknown error'));
                        }
                    } catch (Throwable $e) {
                        $failedCount++;
                        Log::error("Translation error for announcement {$announcementId} to {$targetLanguage}: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
                    }

                    $processedCount++;

                    // İlerleme durumunu güncelle
                    $this->updateProgress('processing', $processedCount, $successCount, $failedCount, $totalTokensUsed);

                    // Her işlem arası kısa bekleme (rate limiting)
                    usleep(100000); // 0.1 saniye
                }
            }

            // İşlem tamamlandı
            $this->updateProgress('completed', $processedCount, $successCount, $failedCount, $totalTokensUsed);

            Log::info("Translation job completed. Success: {$successCount}, Failed: {$failedCount}, Tokens: {$totalTokensUsed}");

            // Frontend'e completion event'ini gönder
            event(new \Modules\Announcement\App\Events\TranslationCompletedEvent([
                'sessionId' => $this->operationId,
                'announcementIds' => $this->announcementIds,
                'success' => $successCount,
                'failed' => $failedCount,
                'status' => 'completed'
            ]));
        } catch (Throwable $e) {
            $this->updateProgress('failed', $processedCount ?? 0, $successCount ?? 0, $failedCount ?? 0, $totalTokensUsed ?? 0);
            Log::error("Translation job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Çeviri prompt'unu oluştur
     */
    private function buildTranslationPrompt(array $sourceData, string $targetLanguage): string
    {
        $content = "Kaynak Dil: {$this->sourceLanguage}\n";
        $content .= "Hedef Dil: {$targetLanguage}\n\n";

        $content .= "Başlık: {$sourceData['title']}\n\n";

        if (!empty($sourceData['excerpt'])) {
            $content .= "Özet: {$sourceData['excerpt']}\n\n";
        }

        $content .= "İçerik:\n{$sourceData['body']}\n\n";

        $content .= "Çeviri Kalitesi: {$this->quality}\n";

        if ($this->options['preserve_formatting'] ?? true) {
            $content .= "HTML/Markdown formatını koru.\n";
        }

        if ($this->options['preserve_seo'] ?? true) {
            $content .= "SEO dostu çeviri yap.\n";
        }

        if ($this->options['cultural_adaptation'] ?? false) {
            $content .= "Kültürel uyarlama yap.\n";
        }

        return $content;
    }

    /**
     * AI response'unu parse et
     */
    private function parseTranslationResponse(string $response): array
    {
        // AI'dan gelen response'u JSON formatında parse etmeye çalış
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            $jsonResponse = json_decode($matches[0], true);
            if ($jsonResponse) {
                return $jsonResponse;
            }
        }

        // JSON parse edilemezse, düz metin olarak parçala
        $lines = explode("\n", $response);
        $result = [
            'title' => '',
            'body' => '',
            'excerpt' => ''
        ];

        $currentSection = '';
        foreach ($lines as $line) {
            $line = trim($line);

            if (stripos($line, 'başlık:') === 0 || stripos($line, 'title:') === 0) {
                $currentSection = 'title';
                $result['title'] = trim(substr($line, strpos($line, ':') + 1));
            } elseif (stripos($line, 'özet:') === 0 || stripos($line, 'excerpt:') === 0) {
                $currentSection = 'excerpt';
                $result['excerpt'] = trim(substr($line, strpos($line, ':') + 1));
            } elseif (stripos($line, 'içerik:') === 0 || stripos($line, 'content:') === 0) {
                $currentSection = 'body';
                $result['body'] = trim(substr($line, strpos($line, ':') + 1));
            } elseif (!empty($line) && !empty($currentSection)) {
                $result[$currentSection] .= "\n" . $line;
            }
        }

        // Temizle
        foreach ($result as $key => $value) {
            $result[$key] = trim($value);
        }

        return $result;
    }

    /**
     * Sayfa çevirisini güncelle
     */
    private function updatePageTranslation(Announcement $announcement, array $translatedData, string $targetLanguage): void
    {
        // Mevcut çevirileri al
        $currentTitle = $announcement->title ?? [];
        $currentBody = $announcement->body ?? [];
        $currentExcerpt = $announcement->excerpt ?? [];

        // JSON decode if string
        if (is_string($currentTitle)) {
            $currentTitle = json_decode($currentTitle, true) ?? [];
        }
        if (is_string($currentBody)) {
            $currentBody = json_decode($currentBody, true) ?? [];
        }
        if (is_string($currentExcerpt)) {
            $currentExcerpt = json_decode($currentExcerpt, true) ?? [];
        }

        // Yeni çevirileri ekle
        if (!empty($translatedData['title'])) {
            $currentTitle[$targetLanguage] = $translatedData['title'];
        }

        if (!empty($translatedData['body'])) {
            $currentBody[$targetLanguage] = $translatedData['body'];
        }

        if (!empty($translatedData['excerpt'])) {
            $currentExcerpt[$targetLanguage] = $translatedData['excerpt'];
        }

        // Slug oluştur (basit slug)
        $slug = [];
        if (is_array($announcement->slug)) {
            $slug = $announcement->slug;
        } elseif (is_string($announcement->slug)) {
            $slug = json_decode($announcement->slug, true) ?? [];
        }

        if (!empty($translatedData['title'])) {
            $slug[$targetLanguage] = \Str::slug($translatedData['title']);
        }

        // Güncelleme
        $announcement->update([
            'title' => $currentTitle,
            'body' => $currentBody,
            'excerpt' => $currentExcerpt,
            'slug' => $slug
        ]);

        log_activity($announcement, 'çevrildi');
    }

    /**
     * İlerleme durumunu güncelle
     */
    private function updateProgress(
        string $status,
        int $processed = 0,
        int $successCount = 0,
        int $failedCount = 0,
        int $tokensUsed = 0
    ): void {
        if (empty($this->operationId)) {
            return;
        }

        $progress = Cache::get("translation_progress_{$this->operationId}", []);

        $progress['status'] = $status;
        $progress['processed'] = $processed;
        $progress['success_count'] = $successCount;
        $progress['failed_count'] = $failedCount;
        $progress['tokens_used'] = $tokensUsed;
        $progress['updated_at'] = now();

        Cache::put("translation_progress_{$this->operationId}", $progress, 3600);
    }

    public function failed(Throwable $exception): void
    {
        $this->updateProgress('failed');
        Log::error("TranslateAnnouncementJob failed: " . $exception->getMessage());
    }
}
