<?php

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\AI\App\Services\Content\FileAnalysisService;
use Illuminate\Support\Str;

class FileAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 dakika
    public $tries = 2;

    protected array $files;
    protected string $analysisType;
    protected string $analysisId;
    protected ?string $sessionId;

    public function __construct(array $files, string $analysisType, string $analysisId, ?string $sessionId = null)
    {
        $this->files = $files;
        $this->analysisType = $analysisType;
        $this->analysisId = $analysisId;
        $this->sessionId = $sessionId;

        // High priority queue
        $this->onQueue('ai-file-analysis');
        $this->onConnection('redis');
    }

    public function handle()
    {
        try {
            Log::info('📁 FileAnalysisJob başladı', [
                'analysis_id' => $this->analysisId,
                'file_count' => count($this->files)
            ]);

            // Cache'e başlangıç durumu yaz
            cache()->put("file_analysis_{$this->analysisId}", [
                'status' => 'processing',
                'progress' => 10,
                'message' => 'Dosyalar analiz ediliyor...'
            ], 600);

            $fileAnalysisService = app(FileAnalysisService::class);

            // Progress callback
            $progressCallback = function($progress, $message = null) {
                cache()->put("file_analysis_{$this->analysisId}", [
                    'status' => 'processing',
                    'progress' => $progress,
                    'message' => $message ?? 'İşleniyor...'
                ], 600);
            };

            // Dosyaları analiz et
            $progressCallback(30, 'PDF içeriği çıkarılıyor...');

            $uploadedFiles = [];
            foreach ($this->files as $fileData) {
                // Temporary file oluştur
                $tempPath = sys_get_temp_dir() . '/' . Str::random(40) . '.' . $fileData['extension'];
                file_put_contents($tempPath, base64_decode($fileData['content']));

                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $fileData['name'],
                    $fileData['mime_type'],
                    null,
                    true
                );

                $uploadedFiles[] = $uploadedFile;
            }

            $progressCallback(50, 'AI ile içerik zenginleştiriliyor...');

            $result = $fileAnalysisService->analyzeFiles($uploadedFiles, $this->analysisType);

            $progressCallback(90, 'Analiz tamamlanıyor...');

            // Sonucu cache'e kaydet
            cache()->put("file_analysis_{$this->analysisId}", [
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Analiz tamamlandı',
                'result' => $result
            ], 600);

            // Temp dosyaları temizle
            foreach ($uploadedFiles as $file) {
                @unlink($file->getPathname());
            }

            Log::info('✅ FileAnalysisJob tamamlandı', [
                'analysis_id' => $this->analysisId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ FileAnalysisJob hatası', [
                'analysis_id' => $this->analysisId,
                'error' => $e->getMessage()
            ]);

            cache()->put("file_analysis_{$this->analysisId}", [
                'status' => 'failed',
                'progress' => 0,
                'message' => 'Analiz başarısız',
                'error' => $e->getMessage()
            ], 600);

            throw $e;
        }
    }
}