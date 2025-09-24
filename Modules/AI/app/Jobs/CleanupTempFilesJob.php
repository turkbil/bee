<?php

declare(strict_types=1);

namespace Modules\AI\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * AUTOMATIC Cleanup Temp Files Job
 *
 * Tüm AI temp dosyalarını otomatik olarak temizler
 * Manuel işlem gerektirmez - tamamen otomatik
 */
class CleanupTempFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 🚀 PERFORMANCE OPTIMIZATIONS
    public $timeout = 60;           // Quick cleanup - max 1 minute
    public $tries = 2;              // Max 2 attempts
    public $maxExceptions = 2;      // Fail fast
    public $backoff = [30];         // 30 second delay between retries
    public $deleteWhenMissingModels = true; // Auto cleanup if models missing

    protected array $specificPaths;
    protected int $delayMinutes;
    protected int $olderThanHours;

    /**
     * Create a new job instance
     */
    public function __construct(array $specificPaths = [], int $delayMinutes = 5, int $olderThanHours = 1)
    {
        $this->specificPaths = $specificPaths;
        $this->delayMinutes = $delayMinutes;
        $this->olderThanHours = $olderThanHours;

        // Job'u belirtilen süre sonra çalıştır
        $this->delay(now()->addMinutes($delayMinutes));
    }

    /**
     * Execute the job - TAMAMEN OTOMATIK CLEANUP
     */
    public function handle(): void
    {
        Log::info('🚀 AUTOMATIC temp files cleanup started', [
            'specific_paths_count' => count($this->specificPaths),
            'delay_minutes' => $this->delayMinutes,
            'older_than_hours' => $this->olderThanHours
        ]);

        $deletedCount = 0;
        $totalSize = 0;

        // 1. Belirli dosyaları temizle (eğer varsa)
        if (!empty($this->specificPaths)) {
            [$specificDeleted, $specificSize] = $this->cleanupSpecificFiles();
            $deletedCount += $specificDeleted;
            $totalSize += $specificSize;
        }

        // 2. Tüm temp directory'leri otomatik temizle
        [$autoDeleted, $autoSize] = $this->cleanupAllTempDirectories();
        $deletedCount += $autoDeleted;
        $totalSize += $autoSize;

        Log::info('✨ AUTOMATIC cleanup completed', [
            'total_deleted' => $deletedCount,
            'total_size_freed' => $this->formatBytes($totalSize)
        ]);
    }

    /**
     * Belirli dosyaları temizle
     */
    private function cleanupSpecificFiles(): array
    {
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($this->specificPaths as $filePath) {
            try {
                if (Storage::disk('local')->exists($filePath)) {
                    $fileSize = Storage::disk('local')->size($filePath);
                    $totalSize += $fileSize;

                    Storage::disk('local')->delete($filePath);
                    $deletedCount++;

                    Log::debug('✅ Specific file deleted: ' . $filePath);
                }
            } catch (\Exception $e) {
                Log::error('❌ Failed to delete specific file: ' . $filePath, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [$deletedCount, $totalSize];
    }

    /**
     * Tüm temp directory'leri otomatik temizle
     */
    private function cleanupAllTempDirectories(): array
    {
        $tempDirs = [
            'temp/pdf-analysis',
            'temp/image-analysis',
            'temp/ai-files',
            'temp'
        ];

        $deletedCount = 0;
        $totalSize = 0;
        $cutoffTime = now()->subHours($this->olderThanHours)->timestamp;

        foreach ($tempDirs as $dir) {
            try {
                if (!Storage::disk('local')->exists($dir)) {
                    continue;
                }

                $files = Storage::disk('local')->allFiles($dir);

                foreach ($files as $file) {
                    try {
                        $lastModified = Storage::disk('local')->lastModified($file);

                        // Belirtilen saatten eski dosyaları sil
                        if ($lastModified < $cutoffTime) {
                            $fileSize = Storage::disk('local')->size($file);
                            $totalSize += $fileSize;

                            Storage::disk('local')->delete($file);
                            $deletedCount++;

                            Log::debug('✅ Auto-deleted old temp file: ' . $file, [
                                'age_hours' => round((time() - $lastModified) / 3600, 2)
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('⚠️ Failed to process temp file: ' . $file, [
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Boş directory'leri temizle
                $remainingFiles = Storage::disk('local')->allFiles($dir);
                if (empty($remainingFiles) && $dir !== 'temp') {
                    Storage::disk('local')->deleteDirectory($dir);
                    Log::debug('🗂️ Removed empty temp directory: ' . $dir);
                }

            } catch (\Exception $e) {
                Log::warning('⚠️ Failed to process temp directory: ' . $dir, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [$deletedCount, $totalSize];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($bytes, 1024));

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💥 AUTOMATIC cleanup job failed', [
            'specific_paths' => $this->specificPaths,
            'older_than_hours' => $this->olderThanHours,
            'error' => $exception->getMessage()
        ]);

        // Hata durumunda 30 dakika sonra tekrar dene (yavaşlamayı önlemek için)
        self::dispatch([], 30, $this->olderThanHours)
            ->onQueue('cleanup')
            ->onConnection('database')
            ->delay(now()->addMinutes(30));

        Log::info('🔄 Retry cleanup job scheduled for 30 minutes later (low priority)');
    }
}