<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiPdfGenerate extends Command
{
    protected $signature = 'ai:pdf-generate 
        {path : PDF dosya yolu}
        {--page=3 : Güncellenecek sayfa ID}
        {--lang=tr : Dil kodu}
        {--tenant= : Tenant ID (opsiyonel)}
        {--analysis=content_extract : Analiz tipi (content_extract|layout_preserve)}
        {--brief= : Brief (opsiyonel)}';

    protected $description = 'PDF → AI içerik üret ve Page.body alanına yaz (Tailwind/Alpine, uzun landing)';

    public function handle()
    {
        $path = $this->argument('path');
        $pageId = (int) $this->option('page');
        $lang = (string) $this->option('lang');
        $tenantId = $this->option('tenant');
        $analysisType = (string) $this->option('analysis');
        $brief = (string) ($this->option('brief') ?? '');

        if (!is_file($path)) {
            $this->error("Dosya bulunamadı: {$path}");
            return Command::FAILURE;
        }

        // Tenant context
        if ($tenantId) {
            try {
                if (function_exists('tenancy')) {
                    tenancy()->initialize($tenantId);
                    $this->info("Tenant initialized: {$tenantId}");
                }
            } catch (\Throwable $e) {
                $this->warn('Tenant initialize başarısız: ' . $e->getMessage());
            }
        }

        // Services
        $analysisService = app(\Modules\AI\app\Services\Content\FileAnalysisService::class);
        $genService = app(\Modules\AI\app\Services\Content\AIContentGeneratorService::class);

        // UploadedFile oluştur
        $uploaded = new UploadedFile($path, basename($path), 'application/pdf', null, true);

        $this->info('PDF analizi başlıyor...');
        $analysis = $analysisService->analyzeFiles([$uploaded], $analysisType);
        if (!($analysis['success'] ?? false)) {
            $this->error('PDF analizi başarısız: ' . ($analysis['error'] ?? 'bilinmiyor'));
            return Command::FAILURE;
        }

        // Async üretimi başlat
        $this->info('AI içerik üretimi başlatılıyor...');
        $jobId = $genService->startAsyncGeneration([
            'prompt' => $brief,
            'target_field' => 'body',
            'replace_existing' => true,
            'module' => 'page',
            'component' => null,
            'file_analysis' => $analysis,
            'conversion_type' => $analysisType,
        ]);

        // Poll progress (en fazla 240 sn)
        $this->line("Job ID: {$jobId}");
        $content = null;
        $attempts = 240;
        while ($attempts-- > 0) {
            $progress = Cache::get("ai_content_progress_{$jobId}");
            if (is_array($progress)) {
                $status = $progress['status'] ?? 'processing';
                $pct = $progress['progress'] ?? 0;
                $msg = $progress['message'] ?? '';
                $this->line("[{$pct}%] {$status} - {$msg}");
                if ($status === 'failed') {
                    $this->error('İşlem başarısız: ' . ($progress['error'] ?? $msg));
                    return Command::FAILURE;
                }
                if ($status === 'completed') {
                    $content = $progress['content'] ?? null;
                    // Fallback
                    if (!$content) {
                        $sessionId = Cache::get("ai_content_job_map_{$jobId}");
                        if ($sessionId) {
                            $result = Cache::get("ai_content_result_{$sessionId}");
                            $content = $result['content'] ?? $content;
                        }
                    }
                    break;
                }
            }
            usleep(500000); // 0.5s
        }

        if (!$content) {
            $this->error('İçerik alınamadı.');
            return Command::FAILURE;
        }

        // Page update
        /** @var \Modules\Page\App\Models\Page $page */
        $page = \Modules\Page\App\Models\Page::find($pageId);
        if (!$page) {
            $this->error('Sayfa bulunamadı: ' . $pageId);
            return Command::FAILURE;
        }

        $page->setTranslation('body', $lang, $content)->save();
        $this->info("✅ İçerik yazıldı: page_id={$pageId}, lang={$lang}, length=" . strlen($content));

        return Command::SUCCESS;
    }
}

