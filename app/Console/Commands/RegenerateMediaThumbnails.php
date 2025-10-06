<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegenerateMediaThumbnails extends Command
{
    protected $signature = 'media:regenerate-thumbnails {--force : Force regenerate even if conversions exist}';
    protected $description = 'Regenerate thumbnails for all media with ASCII-safe filenames';

    public function handle()
    {
        $this->info('🔄 Starting thumbnail regeneration...');

        $allMedia = Media::all();
        $this->info("📊 Found {$allMedia->count()} media items");

        $progressBar = $this->output->createProgressBar($allMedia->count());
        $progressBar->start();

        $success = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($allMedia as $media) {
            try {
                // 1. Dosya adını ASCII-safe yap
                $oldFileName = $media->file_name;
                $newFileName = $this->sanitizeFileName($oldFileName);

                if ($oldFileName !== $newFileName) {
                    // Eski dosya yolunu al
                    $oldPath = $media->getPath();
                    $disk = Storage::disk($media->disk);

                    // Dosya mevcut mu kontrol et
                    if ($disk->exists($media->getPathRelativeToRoot())) {
                        // Yeni dosya adını kaydet
                        $media->file_name = $newFileName;
                        $media->save();

                        $this->newLine();
                        $this->info("📝 Renamed: {$oldFileName} → {$newFileName}");
                    }
                }

                // 2. Thumbnail'leri yeniden oluştur
                if ($this->option('force') || !$media->hasGeneratedConversion('thumb')) {
                    $media->clearGeneratedConversions();
                    $media->save();

                    $this->newLine();
                    $this->info("✅ Regenerated: {$media->file_name} (ID: {$media->id})");
                    $success++;
                } else {
                    $skipped++;
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Failed: {$media->file_name} - {$e->getMessage()}");
                $failed++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Sonuçları göster
        $this->info('✨ Thumbnail regeneration completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $success],
                ['Skipped', $skipped],
                ['Failed', $failed],
                ['Total', $allMedia->count()],
            ]
        );

        return 0;
    }

    /**
     * Dosya adını ASCII-safe hale getir
     */
    protected function sanitizeFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

        // Türkçe karakterleri değiştir
        $turkishChars = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
        $englishChars = ['s', 'S', 'i', 'I', 'g', 'G', 'u', 'U', 'o', 'O', 'c', 'C'];
        $safeName = str_replace($turkishChars, $englishChars, $nameWithoutExtension);

        // Laravel slug helper
        $safeName = Str::slug($safeName, '-', 'en');

        if (empty($safeName)) {
            $safeName = 'file-' . uniqid();
        }

        return "{$safeName}.{$extension}";
    }
}
