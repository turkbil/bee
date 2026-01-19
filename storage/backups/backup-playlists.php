<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Tenant initialize
tenancy()->initialize(1001);

// Sadece kişisel playlistlerin yedeğini al (is_system=false)
$playlists = DB::connection('tenant')->table('muzibu_playlists')->where('is_system', false)->get();

$filename = __DIR__ . '/muzibu_playlists_personal_backup_' . date('Y-m-d_H-i-s') . '.json';
file_put_contents($filename, json_encode($playlists, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Yedek alındı: " . $filename . PHP_EOL;
echo "Toplam kişisel playlist: " . $playlists->count() . PHP_EOL;
