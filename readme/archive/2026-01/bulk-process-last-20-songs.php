#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Tenant 1001 context'e gir
tenancy()->initialize(Stancl\Tenancy\Database\Models\Tenant::find(1001));

use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\ConvertSongToHLS;
use Modules\Muzibu\App\Jobs\GenerateSongCover;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

echo "🎵 Muzibu Toplu İşlem Sistemi - Son 20 Şarkı\n";
echo "==========================================\n\n";

// Son 20 şarkıyı çek
$songs = Song::query()
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->orderBy('song_id', 'desc')
    ->limit(20)
    ->get();

echo "Toplam şarkı: " . $songs->count() . "\n\n";

$stats = [
    'hls_needed' => 0,
    'visual_needed' => 0,
    'both_needed' => 0,
    'complete' => 0,
    'hls_dispatched' => 0,
    'visual_dispatched' => 0,
];

foreach ($songs as $song) {
    $title = is_array($song->title)
        ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
        : $song->title;

    $hasHLS = !empty($song->hls_path);
    $hasVisual = $song->hasMedia('hero');
    $hasSEO = $song->seoSetting !== null;

    echo "Song ID: {$song->song_id} | {$title}\n";
    echo "  HLS: " . ($hasHLS ? '✅ VAR' : '❌ YOK') . "\n";
    echo "  Görsel: " . ($hasVisual ? '✅ VAR' : '❌ YOK') . "\n";
    echo "  SEO: " . ($hasSEO ? '✅ VAR' : '❌ YOK') . "\n";

    // İşlem gerekli mi?
    if (!$hasHLS && !$hasVisual) {
        echo "  ⚙️ Aksiyon: HEM HLS HEM GÖRSEL gerekli\n";
        $stats['both_needed']++;

        // HLS dispatch
        dispatch(new ConvertSongToHLS($song->song_id))->onQueue('muzibu_hls');
        $stats['hls_dispatched']++;
        echo "  ✅ HLS job dispatched (muzibu_hls)\n";

        // Visual dispatch
        dispatch(new GenerateSongCover($song->song_id))->onQueue('muzibu_my_playlist');
        $stats['visual_dispatched']++;
        echo "  ✅ Visual job dispatched (muzibu_my_playlist)\n";

    } elseif (!$hasHLS) {
        echo "  ⚙️ Aksiyon: SADECE HLS gerekli\n";
        $stats['hls_needed']++;

        // HLS dispatch
        dispatch(new ConvertSongToHLS($song->song_id))->onQueue('muzibu_hls');
        $stats['hls_dispatched']++;
        echo "  ✅ HLS job dispatched (muzibu_hls)\n";

    } elseif (!$hasVisual) {
        echo "  ⚙️ Aksiyon: SADECE GÖRSEL gerekli\n";
        $stats['visual_needed']++;

        // Visual dispatch
        dispatch(new GenerateSongCover($song->song_id))->onQueue('muzibu_my_playlist');
        $stats['visual_dispatched']++;
        echo "  ✅ Visual job dispatched (muzibu_my_playlist)\n";

    } else {
        echo "  ✅ Aksiyon: Eksiksiz, işlem gerekmez\n";
        $stats['complete']++;

        // SEO kontrolü yap yine de
        if (!$hasSEO) {
            echo "  ⚠️ SEO yok ama görsel var, manuel SEO trigger gerekebilir\n";
        }
    }

    echo "\n";
}

echo "\n==========================================\n";
echo "📊 İSTATİSTİKLER\n";
echo "==========================================\n";
echo "Sadece HLS gerekli: {$stats['hls_needed']}\n";
echo "Sadece Görsel gerekli: {$stats['visual_needed']}\n";
echo "Hem HLS hem Görsel gerekli: {$stats['both_needed']}\n";
echo "Eksiksiz: {$stats['complete']}\n";
echo "\n";
echo "HLS job dispatched: {$stats['hls_dispatched']}\n";
echo "Visual job dispatched: {$stats['visual_dispatched']}\n";
echo "Toplam job: " . ($stats['hls_dispatched'] + $stats['visual_dispatched']) . "\n";
echo "\n";
echo "✅ İşlem tamamlandı! Horizon'u kontrol edin.\n";
echo "🔗 Horizon: https://muzibu.com.tr/horizon\n";
