<?php

namespace App\Listeners;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;
use Illuminate\Support\Facades\Log;

/**
 * Media Upload Ownership Fix Listener
 *
 * Spatie Media Library dosya yüklediğinde otomatik ownership düzeltir
 * Sorun: Plesk default group "psacln" veriyor → Nginx symlink reddettiyor
 * Çözüm: Her upload'da otomatik "psaserv" group'u ata
 */
class MediaUploadedListener
{
    /**
     * Handle the event
     */
    public function handle(MediaHasBeenAdded $event): void
    {
        $media = $event->media;

        try {
            // Ana dosyayı düzelt
            $this->fixOwnership($media->getPath());

            // Conversions (thumbnails) için de düzelt
            foreach ($media->getGeneratedConversions() as $conversionName => $generated) {
                if ($generated) {
                    $conversionPath = $media->getPath($conversionName);
                    $this->fixOwnership($conversionPath);
                }
            }

            Log::debug("✅ Media ownership fixed", [
                'media_id' => $media->id,
                'path' => $media->getPath(),
                'group' => 'psaserv'
            ]);

        } catch (\Throwable $e) {
            // Hata olsa bile upload devam etsin
            Log::warning("⚠️ Media ownership fix failed (non-critical)", [
                'media_id' => $media->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fix file ownership (group + permissions)
     */
    protected function fixOwnership(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        // Group değiştir: psacln → psaserv
        if (function_exists('posix_getgrnam')) {
            $groupInfo = @posix_getgrnam('psaserv');
            if ($groupInfo !== false) {
                @chgrp($path, $groupInfo['gid']);
            }
        }

        // Permissions ayarla: 0664 (rw-rw-r--)
        @chmod($path, 0664);

        // Directory ise 0775 (rwxrwxr-x)
        if (is_dir($path)) {
            @chmod($path, 0775);
        }
    }
}
