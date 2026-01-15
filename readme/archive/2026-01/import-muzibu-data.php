<?php
/**
 * 🎵 Muzibu Data Migration Script
 *
 * Eski muzibu.com sistemindeki verileri yeni muzibu.com sistemine import eder.
 *
 * KULLANIM:
 * curl -s https://muzibu.com/import-muzibu-data.php
 *
 * IMPORT SIRALAMA:
 * 1. Artists (43 kayıt)
 * 2. Genres (39 kayıt)
 * 3. Sectors (18 kayıt)
 * 4. Radios (99 kayıt)
 * 5. Albums (179 kayıt - artist_id mapping gerekli)
 *
 * DÖNÜŞÜMLER:
 * - title_tr → title (JSON: {"tr": "...", "en": "..."})
 * - slug → slug (JSON: {"tr": "...", "en": "..."})
 * - description_tr / bio_tr → description/bio (JSON)
 * - thumb → media_id = NULL (Spatie Media sonra manuel yüklenecek)
 * - active → is_active
 * - created → created_at + updated_at
 * - meta_* → silinecek (HasSeo trait kullanılıyor)
 *
 * @author Claude AI
 * @date 2026-01-08
 */

// Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Configuration
$sqlFile = '/var/www/vhosts/muzibu.com/httpdocs/muzibu_mayis25 (1).sql';
$mappingFile = '/var/www/vhosts/muzibu.com/httpdocs/muzibu-id-mappings.json';
$batchSize = 50; // Her batch'te kaç kayıt işlenecek

// ID Mapping Storage (eski ID → yeni ID)
$idMapping = [
    'artists' => [],
    'albums' => [],
    'genres' => [],
    'radios' => [],
    'sectors' => [],
];

// Results
$results = [
    'success' => [],
    'errors' => [],
    'skipped' => [],
];

echo "🎵 Muzibu Data Migration - Başlatıldı\n";
echo "=====================================\n\n";

// SQL dosyasını oku
if (!file_exists($sqlFile)) {
    die("❌ SQL dosyası bulunamadı: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
echo "✅ SQL dosyası okundu: " . number_format(strlen($sql)) . " byte\n\n";

/**
 * JSON çeviri helper (sadece Türkçe)
 */
function makeJson($value, $field = 'title')
{
    if (empty($value)) {
        return json_encode(['tr' => '', 'en' => ''], JSON_UNESCAPED_UNICODE);
    }

    // HTML decode
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // JSON oluştur (sadece Türkçe doldur, İngilizce boş)
    return json_encode([
        'tr' => $value,
        'en' => ''
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * INSERT INTO satırlarını parse et (İYİLEŞTİRİLMİŞ)
 */
function parseInsertStatements($sql, $tableName)
{
    // Tablo başlangıcını bul
    $startPattern = "INSERT INTO `{$tableName}` (";
    $startPos = strpos($sql, $startPattern);

    if ($startPos === false) {
        echo "   [DEBUG] '{$tableName}' için INSERT bulunamadı\n";
        return [];
    }

    // Column names'i çıkar
    $columnsStart = $startPos + strlen($startPattern);
    $columnsEnd = strpos($sql, ') VALUES', $columnsStart);
    $columnsLine = substr($sql, $columnsStart, $columnsEnd - $columnsStart);
    $columns = array_map('trim', explode(',', str_replace('`', '', $columnsLine)));

    echo "   [DEBUG] {$tableName}: " . count($columns) . " column bulundu\n";

    // VALUES kısmını bul
    $valuesStart = $columnsEnd + strlen(') VALUES');
    // Sonraki satıra geç (newline atla)
    while ($valuesStart < strlen($sql) && ($sql[$valuesStart] === "\n" || $sql[$valuesStart] === "\r" || $sql[$valuesStart] === ' ')) {
        $valuesStart++;
    }

    // Semicolon'a kadar oku
    $valuesEnd = strpos($sql, ');', $valuesStart);
    if ($valuesEnd === false) {
        echo "   [DEBUG] VALUES block sonu bulunamadı\n";
        return [];
    }

    $valuesBlock = substr($sql, $valuesStart, $valuesEnd - $valuesStart + 1);
    echo "   [DEBUG] VALUES block: " . strlen($valuesBlock) . " byte\n";

    // Her row'u parse et - basit char-by-char parsing
    $rows = [];
    $currentRow = [];
    $currentValue = '';
    $inString = false;
    $inRow = false;
    $escapeNext = false;

    for ($i = 0; $i < strlen($valuesBlock); $i++) {
        $char = $valuesBlock[$i];

        if ($escapeNext) {
            $currentValue .= $char;
            $escapeNext = false;
            continue;
        }

        if ($char === '\\') {
            $escapeNext = true;
            continue;
        }

        if ($char === "'" && !$escapeNext) {
            if ($inString) {
                // String bitti
                $inString = false;
                $currentRow[] = $currentValue;
                $currentValue = '';
            } else {
                // String başladı
                $inString = true;
            }
            continue;
        }

        if ($inString) {
            $currentValue .= $char;
            continue;
        }

        // String dışındayız
        if ($char === '(') {
            $inRow = true;
            continue;
        }

        if ($char === ')' && $inRow) {
            // Row bitti
            if ($currentValue !== '') {
                // Son value'yu ekle
                if (strtoupper(trim($currentValue)) === 'NULL') {
                    $currentRow[] = null;
                } else {
                    $currentRow[] = trim($currentValue);
                }
                $currentValue = '';
            }

            if (count($currentRow) === count($columns)) {
                $rows[] = array_combine($columns, $currentRow);
            }

            $currentRow = [];
            $inRow = false;
            continue;
        }

        if ($char === ',' && $inRow) {
            // Value ayracı
            if ($currentValue !== '') {
                if (strtoupper(trim($currentValue)) === 'NULL') {
                    $currentRow[] = null;
                } else {
                    $currentRow[] = trim($currentValue);
                }
            }
            $currentValue = '';
            continue;
        }

        if ($inRow && $char !== "\n" && $char !== "\r" && $char !== ' ' || $currentValue !== '') {
            $currentValue .= $char;
        }
    }

    echo "   [DEBUG] {$tableName}: " . count($rows) . " row parse edildi\n";
    return $rows;
}

// ==========================================
// 1. ARTISTS IMPORT
// ==========================================
echo "📌 1. ARTISTS Import başlıyor...\n";

$artistRows = parseInsertStatements($sql, 'muzibu_artists');
echo "   Toplam artist: " . count($artistRows) . "\n";

$imported = 0;
$skipped = 0;

foreach ($artistRows as $row) {
    try {
        $oldId = $row['id'];

        // JSON dönüşüm + ESKİ ID'Yİ KORU!
        $data = [
            'artist_id' => $oldId, // ⚠️ ESKİ ID'Yİ KULLAN!
            'title' => makeJson($row['title_tr']),
            'slug' => makeJson($row['slug']),
            'bio' => makeJson($row['bio_tr'] ?? ''),
            'media_id' => null,
            'is_active' => (bool) ($row['active'] ?? 1),
            'created_at' => $row['created'] ?? now(),
            'updated_at' => $row['created'] ?? now(),
        ];

        // Insert (ID'yi manuel belirtiyoruz)
        DB::table('muzibu_artists')->insert($data);

        // Mapping (ID'ler aynı kalıyor)
        $idMapping['artists'][$oldId] = $oldId;
        $imported++;

    } catch (\Exception $e) {
        $results['errors'][] = "Artist ID {$row['id']}: " . $e->getMessage();
        $skipped++;
    }
}

echo "   ✅ Import tamamlandı: $imported başarılı, $skipped hata\n\n";

// ==========================================
// 2. GENRES IMPORT
// ==========================================
echo "📌 2. GENRES Import başlıyor...\n";

$genreRows = parseInsertStatements($sql, 'muzibu_genres');
echo "   Toplam genre: " . count($genreRows) . "\n";

$imported = 0;
$skipped = 0;

foreach ($genreRows as $row) {
    try {
        $oldId = $row['id'];

        $data = [
            'genre_id' => $oldId, // ⚠️ ESKİ ID'Yİ KULLAN!
            'title' => makeJson($row['title_tr']),
            'slug' => makeJson($row['slug']),
            'description' => makeJson($row['description_tr'] ?? ''),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $row['created'] ?? now(),
            'updated_at' => $row['created'] ?? now(),
        ];

        DB::table('muzibu_genres')->insert($data);
        $idMapping['genres'][$oldId] = $oldId;
        $imported++;

    } catch (\Exception $e) {
        $results['errors'][] = "Genre ID {$row['id']}: " . $e->getMessage();
        $skipped++;
    }
}

echo "   ✅ Import tamamlandı: $imported başarılı, $skipped hata\n\n";

// ==========================================
// 3. SECTORS IMPORT
// ==========================================
echo "📌 3. SECTORS Import başlıyor...\n";

$sectorRows = parseInsertStatements($sql, 'muzibu_sectors');
echo "   Toplam sector: " . count($sectorRows) . "\n";

$imported = 0;
$skipped = 0;

foreach ($sectorRows as $row) {
    try {
        $oldId = $row['id'];

        $data = [
            'sector_id' => $oldId, // ⚠️ ESKİ ID'Yİ KULLAN!
            'title' => makeJson($row['title_tr']),
            'slug' => makeJson($row['slug']),
            'description' => json_encode(['tr' => '', 'en' => ''], JSON_UNESCAPED_UNICODE),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $row['created'] ?? now(),
            'updated_at' => $row['created'] ?? now(),
        ];

        DB::table('muzibu_sectors')->insert($data);
        $idMapping['sectors'][$oldId] = $oldId;
        $imported++;

    } catch (\Exception $e) {
        $results['errors'][] = "Sector ID {$row['id']}: " . $e->getMessage();
        $skipped++;
    }
}

echo "   ✅ Import tamamlandı: $imported başarılı, $skipped hata\n\n";

// ==========================================
// 4. RADIOS IMPORT
// ==========================================
echo "📌 4. RADIOS Import başlıyor...\n";

$radioRows = parseInsertStatements($sql, 'muzibu_radios');
echo "   Toplam radio: " . count($radioRows) . "\n";

$imported = 0;
$skipped = 0;

foreach ($radioRows as $row) {
    try {
        $oldId = $row['id'];

        $data = [
            'radio_id' => $oldId, // ⚠️ ESKİ ID'Yİ KULLAN!
            'title' => makeJson($row['title_tr']),
            'slug' => makeJson($row['slug']),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $row['created'] ?? now(),
            'updated_at' => $row['created'] ?? now(),
        ];

        DB::table('muzibu_radios')->insert($data);
        $idMapping['radios'][$oldId] = $oldId;
        $imported++;

    } catch (\Exception $e) {
        $results['errors'][] = "Radio ID {$row['id']}: " . $e->getMessage();
        $skipped++;
    }
}

echo "   ✅ Import tamamlandı: $imported başarılı, $skipped hata\n\n";

// ==========================================
// 5. ALBUMS IMPORT (artist_id mapping gerekli!)
// ==========================================
echo "📌 5. ALBUMS Import başlıyor...\n";

$albumRows = parseInsertStatements($sql, 'muzibu_albums');
echo "   Toplam album: " . count($albumRows) . "\n";

$imported = 0;
$skipped = 0;

foreach ($albumRows as $row) {
    try {
        $oldId = $row['id'];
        $oldArtistId = $row['artist_id'];

        // Artist ID artık aynı (mapping gerekmiyor!)
        // Ama yine de kontrol edelim
        if (!isset($idMapping['artists'][$oldArtistId])) {
            throw new \Exception("Artist ID mapping bulunamadı: $oldArtistId");
        }

        $artistId = $idMapping['artists'][$oldArtistId]; // Aynı ID

        $data = [
            'album_id' => $oldId, // ⚠️ ESKİ ID'Yİ KULLAN!
            'artist_id' => $artistId,
            'title' => makeJson($row['title_tr']),
            'slug' => makeJson($row['slug']),
            'description' => makeJson($row['description_tr'] ?? ''),
            'media_id' => null,
            'is_active' => (bool) ($row['active'] ?? 1),
            'created_at' => $row['created'] ?? now(),
            'updated_at' => $row['created'] ?? now(),
        ];

        DB::table('muzibu_albums')->insert($data);
        $idMapping['albums'][$oldId] = $oldId;
        $imported++;

    } catch (\Exception $e) {
        $results['errors'][] = "Album ID {$row['id']}: " . $e->getMessage();
        $skipped++;
    }
}

echo "   ✅ Import tamamlandı: $imported başarılı, $skipped hata\n\n";

// ==========================================
// SONUÇ VE MAPPING KAYDET
// ==========================================
echo "=====================================\n";
echo "🎉 TÜM TABLOLAR IMPORT EDİLDİ!\n\n";

echo "📊 Özet:\n";
echo "   Artists: " . count($idMapping['artists']) . " kayıt\n";
echo "   Genres: " . count($idMapping['genres']) . " kayıt\n";
echo "   Sectors: " . count($idMapping['sectors']) . " kayıt\n";
echo "   Radios: " . count($idMapping['radios']) . " kayıt\n";
echo "   Albums: " . count($idMapping['albums']) . " kayıt\n\n";

if (!empty($results['errors'])) {
    echo "⚠️ Hatalar:\n";
    foreach (array_slice($results['errors'], 0, 10) as $error) {
        echo "   - $error\n";
    }
    if (count($results['errors']) > 10) {
        echo "   ... ve " . (count($results['errors']) - 10) . " hata daha\n";
    }
    echo "\n";
}

// ID mapping'i kaydet
file_put_contents($mappingFile, json_encode($idMapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "✅ ID mapping kaydedildi: $mappingFile\n\n";

echo "🎵 İşlem tamamlandı!\n";
