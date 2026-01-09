<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Muzibu\App\Models\Song;

class CheckBulkResults extends Command
{
    protected $signature = 'muzibu:check-results {limit=20}';
    protected $description = 'Check bulk processing results';

    public function handle()
    {
        $limit = (int) $this->argument('limit');

        $this->info("🎵 Toplu İşlem Sonuçları - Son {$limit} Şarkı");
        $this->info("==========================================");
        $this->newLine();

        // Son N şarkıyı çek
        $songs = Song::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('song_id', 'desc')
            ->limit($limit)
            ->get();

        $successCount = 0;
        $failCount = 0;
        $seoCount = 0;
        $aiGeneratedCount = 0;

        foreach ($songs as $song) {
            $title = is_array($song->title)
                ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
                : $song->title;

            $hasVisual = $song->hasMedia('hero');
            $hasSEO = $song->seoSetting !== null;

            if ($hasVisual) {
                $successCount++;
                $media = $song->getFirstMedia('hero');
                $customProps = $media?->custom_properties ?? [];
                $aiGenerated = isset($customProps['generation_source']) && $customProps['generation_source'] === 'ai_generated';

                if ($aiGenerated) {
                    $aiGeneratedCount++;
                }

                $this->info("✅ Song ID: {$song->song_id} | {$title}");
                $this->line("   Görsel: " . ($aiGenerated ? '🤖 AI Generated' : '👤 User Uploaded'));
                $this->line("   SEO: " . ($hasSEO ? '✅ VAR' : '❌ YOK'));

                if ($aiGenerated && isset($customProps['generation_params'])) {
                    $params = $customProps['generation_params'];
                    $this->line("   AI Model: " . ($params['model'] ?? 'unknown'));
                    $this->line("   Style: " . ($params['style'] ?? 'unknown'));
                    $this->line("   Dil: " . ($params['detected_language'] ?? 'unknown'));
                }

                if ($hasSEO) {
                    $seoCount++;
                    $seoTitleTr = $song->seoSetting->titles['tr'] ?? null;
                    if ($seoTitleTr) {
                        $this->line("   SEO Başlık: " . substr($seoTitleTr, 0, 60) . "...");
                    }
                }

                $this->newLine();
            } else {
                $failCount++;
                $this->error("❌ Song ID: {$song->song_id} | {$title}");
                $this->line("   Görsel: YOK");
                $this->line("   SEO: " . ($hasSEO ? '✅ VAR' : '❌ YOK'));
                $this->newLine();
            }
        }

        $this->info("==========================================");
        $this->info("📊 ÖZET");
        $this->info("==========================================");
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['Toplam şarkı', $songs->count()],
                ['Görsel var', $successCount],
                ['Görsel yok', $failCount],
                ['AI ile üretilmiş', $aiGeneratedCount],
                ['SEO var', $seoCount],
                ['Başarı oranı', $songs->count() > 0 ? round(($successCount / $songs->count()) * 100, 2) . '%' : '0%'],
            ]
        );

        $this->newLine();

        if ($successCount > 0) {
            $this->info("✅ İşlem başarılı! {$aiGeneratedCount} şarkıya AI ile görsel üretildi.");
        } else {
            $this->warn("⚠️ Hiç görsel üretilmedi, log'ları kontrol edin.");
        }

        if ($seoCount > 0) {
            $this->info("✅ {$seoCount} şarkıya SEO bilgileri eklendi.");
        }

        return Command::SUCCESS;
    }
}
