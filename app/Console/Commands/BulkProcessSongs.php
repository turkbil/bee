<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\GenerateSongCover;
use Modules\Muzibu\App\Jobs\ConvertToHLSJob;
use Modules\Muzibu\App\Jobs\GenerateSongSEO;

class BulkProcessSongs extends Command
{
    protected $signature = 'muzibu:bulk-process {limit=20}';
    protected $description = 'Bulk process songs: HLS + AI Cover + SEO';

    public function handle()
    {
        // Initialize tenant context (muzibu.com.tr = tenant 1001)
        tenancy()->initialize(1001);

        $limit = (int) $this->argument('limit');

        $this->info("🎵 Muzibu Toplu İşlem Sistemi");
        $this->info("==========================================");
        $this->newLine();

        // Son N şarkıyı çek
        $songs = Song::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('song_id', 'desc')
            ->limit($limit)
            ->get();

        $this->info("Toplam şarkı: " . $songs->count());
        $this->newLine();

        $stats = [
            'hls_needed' => 0,
            'visual_needed' => 0,
            'both_needed' => 0,
            'complete' => 0,
            'hls_dispatched' => 0,
            'visual_dispatched' => 0,
            'seo_dispatched' => 0,
        ];

        $progressBar = $this->output->createProgressBar($songs->count());
        $progressBar->start();

        foreach ($songs as $song) {
            $title = is_array($song->title)
                ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
                : $song->title;

            $hasHLS = !empty($song->hls_path);
            $hasVisual = $song->hasMedia('hero');
            $hasSEO = $song->seoSetting !== null;

            // İşlem gerekli mi? (HLS + Görsel + SEO)
            if (!$hasHLS && !$hasVisual) {
                $stats['both_needed']++;

                // HLS dispatch (mevcut ConvertToHLSJob kullan - Song objesi alır)
                ConvertToHLSJob::dispatch($song);
                $stats['hls_dispatched']++;

                // Visual dispatch
                $this->dispatchVisualJob($song);
                $stats['visual_dispatched']++;

            } elseif (!$hasHLS) {
                $stats['hls_needed']++;

                // HLS dispatch (mevcut ConvertToHLSJob kullan - Song objesi alır)
                ConvertToHLSJob::dispatch($song);
                $stats['hls_dispatched']++;

            } elseif (!$hasVisual) {
                $stats['visual_needed']++;

                // Visual dispatch
                $this->dispatchVisualJob($song);
                $stats['visual_dispatched']++;

            } else {
                $stats['complete']++;
            }

            // SEO dispatch (her şarkı için, varsa skip)
            if (!$hasSEO) {
                $this->dispatchSEOJob($song);
                $stats['seo_dispatched']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("==========================================");
        $this->info("📊 İSTATİSTİKLER");
        $this->info("==========================================");
        $this->table(
            ['Durum', 'Sayı'],
            [
                ['Sadece HLS gerekli', $stats['hls_needed']],
                ['Sadece Görsel gerekli', $stats['visual_needed']],
                ['Hem HLS hem Görsel gerekli', $stats['both_needed']],
                ['Eksiksiz', $stats['complete']],
                ['', ''],
                ['HLS job dispatched', $stats['hls_dispatched']],
                ['Visual job dispatched', $stats['visual_dispatched']],
                ['SEO job dispatched', $stats['seo_dispatched']],
                ['Toplam job', $stats['hls_dispatched'] + $stats['visual_dispatched'] + $stats['seo_dispatched']],
            ]
        );

        $this->newLine();
        $this->info("✅ İşlem tamamlandı! Horizon'u kontrol edin.");
        $this->info("🔗 Horizon: https://muzibu.com.tr/horizon");

        return Command::SUCCESS;
    }

    /**
     * Dispatch visual generation job with song data
     */
    protected function dispatchVisualJob(Song $song): void
    {
        // Get song title
        $title = is_array($song->title)
            ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
            : $song->title;

        // Get artist name (if exists)
        $artistName = null;
        if ($song->album && $song->album->artists && $song->album->artists->isNotEmpty()) {
            $artistName = $song->album->artists->first()->name ?? null;
        }

        // Get genre name (if exists)
        $genreName = null;
        if ($song->genre) {
            $genreName = is_array($song->genre->name)
                ? ($song->genre->name['tr'] ?? $song->genre->name['en'] ?? null)
                : $song->genre->name;
        }

        // Dispatch job
        dispatch(new GenerateSongCover(
            songId: $song->song_id,
            songTitle: $title,
            artistName: $artistName,
            genreName: $genreName,
            userId: null,
            tenantId: tenant('id')
        ))->onQueue('muzibu_my_playlist');
    }

    /**
     * Dispatch SEO generation job with song data
     */
    protected function dispatchSEOJob(Song $song): void
    {
        // Get song title
        $title = is_array($song->title)
            ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
            : $song->title;

        // Get artist name (if exists)
        $artistName = null;
        if ($song->album && $song->album->artists && $song->album->artists->isNotEmpty()) {
            $artistName = $song->album->artists->first()->name ?? null;
        }

        // Get album name (if exists)
        $albumName = null;
        if ($song->album) {
            $albumName = is_array($song->album->name)
                ? ($song->album->name['tr'] ?? $song->album->name['en'] ?? null)
                : $song->album->name;
        }

        // Get genre name (if exists)
        $genreName = null;
        if ($song->genre) {
            $genreName = is_array($song->genre->name)
                ? ($song->genre->name['tr'] ?? $song->genre->name['en'] ?? null)
                : $song->genre->name;
        }

        // Dispatch job
        dispatch(new GenerateSongSEO(
            songId: $song->song_id,
            tenantId: tenant('id'),
            songTitle: $title,
            artistName: $artistName,
            albumName: $albumName,
            genreName: $genreName
        ))->onQueue('muzibu_seo');
    }
}
