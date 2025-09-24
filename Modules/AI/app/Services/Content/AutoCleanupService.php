<?php

declare(strict_types=1);

namespace Modules\AI\app\Services\Content;

use Illuminate\Support\Facades\Log;
use Modules\AI\App\Jobs\CleanupTempFilesJob;

/**
 * Auto Cleanup Service
 *
 * Tamamen otomatik cleanup sistemi - manuel işlem gerektirmez
 */
class AutoCleanupService
{
    /**
     * GLOBAL otomatik cleanup başlat
     * Her AI işleminden sonra otomatik çağrılır
     */
    public static function scheduleAutomaticCleanup(): void
    {
        try {
            // 🚀 LOW PRIORITY CLEANUP - AI queue'yu asla bloklamaz
            // 1. Hemen cleanup (1 dakika sonra) - düşük öncelik
            CleanupTempFilesJob::dispatch([], 1, 0.5)  // 30 dakika eski dosyalar
                ->onQueue('cleanup')  // Ayrı cleanup queue
                ->onConnection('database');  // Redis'i bloklamaz

            // 2. Güvenlik cleanup (10 dakika sonra) - düşük öncelik
            CleanupTempFilesJob::dispatch([], 10, 2)   // 2 saat eski dosyalar
                ->onQueue('cleanup')
                ->onConnection('database');

            // 3. Genel temizlik (1 saat sonra) - düşük öncelik
            CleanupTempFilesJob::dispatch([], 60, 24)  // 24 saat eski dosyalar
                ->onQueue('cleanup')
                ->onConnection('database');

            Log::info('🚀 LOW-PRIORITY cleanup system activated - won\'t slow down AI operations');

        } catch (\Exception $e) {
            Log::error('❌ Auto cleanup scheduling failed: ' . $e->getMessage());
        }
    }

    /**
     * Acil durum cleanup - hemen çalışır
     */
    public static function emergencyCleanup(): void
    {
        try {
            // Acil cleanup job'u sync olarak çalıştır (queue'yu bloklamaz)
            CleanupTempFilesJob::dispatchSync([], 0, 0);

            Log::info('🚨 Emergency cleanup completed SYNC - no queue impact');

        } catch (\Exception $e) {
            Log::error('❌ Emergency cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Sistem health check - temp dosya durumunu kontrol et
     */
    public static function checkTempFileHealth(): array
    {
        try {
            $storage = \Illuminate\Support\Facades\Storage::disk('local');
            $stats = [
                'pdf_analysis_files' => 0,
                'image_analysis_files' => 0,
                'total_size' => 0,
                'oldest_file_age_hours' => 0
            ];

            $tempDirs = ['temp/pdf-analysis', 'temp/image-analysis'];
            $oldestTime = null;

            foreach ($tempDirs as $dir) {
                if ($storage->exists($dir)) {
                    $files = $storage->allFiles($dir);

                    if ($dir === 'temp/pdf-analysis') {
                        $stats['pdf_analysis_files'] = count($files);
                    } else {
                        $stats['image_analysis_files'] = count($files);
                    }

                    foreach ($files as $file) {
                        $stats['total_size'] += $storage->size($file);
                        $lastModified = $storage->lastModified($file);

                        if (!$oldestTime || $lastModified < $oldestTime) {
                            $oldestTime = $lastModified;
                        }
                    }
                }
            }

            if ($oldestTime) {
                $stats['oldest_file_age_hours'] = round((time() - $oldestTime) / 3600, 2);
            }

            $stats['total_size_mb'] = round($stats['total_size'] / (1024 * 1024), 2);

            return $stats;

        } catch (\Exception $e) {
            Log::error('❌ Temp file health check failed: ' . $e->getMessage());

            return [
                'error' => $e->getMessage(),
                'pdf_analysis_files' => 0,
                'image_analysis_files' => 0,
                'total_size' => 0,
                'total_size_mb' => 0,
                'oldest_file_age_hours' => 0
            ];
        }
    }
}