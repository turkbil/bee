<?php
// SQL dosyasını analiz et

$sqlFile = '/var/www/vhosts/muzibu.com/httpdocs/muzibu_playlists.sql';
$content = file_get_contents($sqlFile);

// INSERT satırlarını bul
preg_match_all('/\((\d+),\s*\'([^\']*)\',\s*\'([^\']*)\',\s*(\d+),\s*(\d+),/', $content, $matches, PREG_SET_ORDER);

$systemPlaylists = [];
$personalPlaylists = [];

foreach ($matches as $match) {
    $data = [
        'id' => (int)$match[1],
        'title' => $match[2],
        'slug' => $match[3],
        'user_id' => (int)$match[4],
        'system' => (int)$match[5],
    ];

    if ($data['system'] == 0) {
        $personalPlaylists[] = $data;
    } else {
        $systemPlaylists[] = $data;
    }
}

echo "=== ESKI VERİTABANI ANALİZİ ===\n\n";
echo "Toplam sistem playlist: " . count($systemPlaylists) . "\n";
echo "Toplam kişisel playlist: " . count($personalPlaylists) . "\n\n";

if (count($personalPlaylists) > 0) {
    echo "=== KİŞİSEL PLAYLİSTLER (system=0) ===\n";
    foreach ($personalPlaylists as $p) {
        echo "ID: {$p['id']} | User: {$p['user_id']} | {$p['title']}\n";
    }
} else {
    echo "Kişisel playlist bulunamadı.\n";
}

echo "\n=== USER ID DAĞILIMI ===\n";
$userCounts = [];
foreach ($matches as $match) {
    $userId = (int)$match[4];
    if (!isset($userCounts[$userId])) {
        $userCounts[$userId] = 0;
    }
    $userCounts[$userId]++;
}
arsort($userCounts);
foreach ($userCounts as $userId => $count) {
    echo "User ID $userId: $count playlist\n";
}

// Kişisel playlistleri JSON olarak kaydet
if (count($personalPlaylists) > 0) {
    file_put_contents(__DIR__ . '/tenant1001/old_personal_playlists.json', json_encode($personalPlaylists, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nKişisel playlistler kaydedildi: tenant1001/old_personal_playlists.json\n";
}
