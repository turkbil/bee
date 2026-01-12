#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Tenant 1001 context'e gir
tenancy()->initialize(Stancl\Tenancy\Database\Models\Tenant::find(1001));

use Modules\Muzibu\App\Models\Song;

echo "🎵 Toplu İşlem Sonuçları - Son 20 Şarkı\n";
echo "==========================================\n\n";

// Son 20 şarkıyı çek
$songs = Song::query()
    ->where('is_active', true)
    ->whereNull('deleted_at')
    ->orderBy('song_id', 'desc')
    ->limit(20)
    ->get();

$successCount = 0;
$failCount = 0;
$seoCount = 0;

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

        echo "✅ Song ID: {$song->song_id} | {$title}\n";
        echo "   Görsel: " . ($aiGenerated ? '🤖 AI Generated' : '👤 User Uploaded') . "\n";
        echo "   SEO: " . ($hasSEO ? '✅ VAR' : '❌ YOK') . "\n";

        if ($aiGenerated && isset($customProps['generation_params'])) {
            $params = $customProps['generation_params'];
            echo "   AI Model: " . ($params['model'] ?? 'unknown') . "\n";
            echo "   Style: " . ($params['style'] ?? 'unknown') . "\n";
            echo "   Dil: " . ($params['detected_language'] ?? 'unknown') . "\n";
        }

        if ($hasSEO) {
            $seoCount++;
        }

        echo "\n";
    } else {
        $failCount++;
        echo "❌ Song ID: {$song->song_id} | {$title}\n";
        echo "   Görsel: YOK\n";
        echo "   SEO: " . ($hasSEO ? '✅ VAR' : '❌ YOK') . "\n\n";
    }
}

echo "==========================================\n";
echo "📊 ÖZET\n";
echo "==========================================\n";
echo "Toplam: {$songs->count()}\n";
echo "Başarılı (Görsel var): {$successCount}\n";
echo "Başarısız (Görsel yok): {$failCount}\n";
echo "SEO var: {$seoCount}\n";
echo "\n";

if ($successCount > 0) {
    echo "✅ İşlem başarılı! {$successCount} şarkıya görsel üretildi.\n";
} else {
    echo "⚠️ Hiç görsel üretilmedi, log'ları kontrol edin.\n";
}

if ($seoCount > 0) {
    echo "✅ {$seoCount} şarkıya SEO bilgileri eklendi.\n";
}
