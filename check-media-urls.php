#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Tenant 1001 context'e gir
tenancy()->initialize(1001);

use Modules\Muzibu\App\Models\Song;

echo "🔍 Media Durumu Kontrolü - Son 5 Şarkı\n";
echo "==========================================\n\n";

$songs = Song::orderBy('song_id', 'desc')->limit(5)->get();

foreach ($songs as $song) {
    $title = is_array($song->title)
        ? ($song->title['tr'] ?? $song->title['en'] ?? 'No Title')
        : $song->title;

    $media = $song->getFirstMedia('hero');

    if ($media) {
        $url = $media->getUrl();
        $path = $media->getPath();
        $exists = file_exists($path);
        $owner = $exists ? posix_getpwuid(fileowner($path))['name'] : 'N/A';
        $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

        echo "Song ID: {$song->song_id} | {$title}\n";
        echo "  Dosya: " . ($exists ? "✅ VAR" : "❌ YOK") . "\n";
        echo "  Path: {$path}\n";
        echo "  URL: {$url}\n";
        echo "  Owner: {$owner}\n";
        echo "  Perms: {$perms}\n";
        echo "\n";

        // URL'yi test et
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "  HTTP Status: " . ($httpCode == 200 ? "✅ 200 OK" : "❌ {$httpCode}") . "\n";
        echo "\n";
    } else {
        echo "Song ID: {$song->song_id} | {$title}\n";
        echo "  ❌ Medya kaydı YOK\n\n";
    }
}
