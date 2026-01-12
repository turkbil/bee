<?php
/**
 * 🎵 Muzibu Songs Data Migration Script (30,000+ Kayıt İçin Optimize Edilmiş)
 *
 * KULLANIM:
 * php public/import-muzibu-songs.php
 *
 * ÖZELLİKLER:
 * - Stream-based parsing (düşük memory kullanımı)
 * - Batch insert (her 500 kayıtta bir)
 * - Progress tracking
 * - Memory ve time limit otomatik artırma
 *
 * @author Claude AI
 * @date 2026-01-08
 */

// Memory ve time limit artır
ini_set('memory_limit', '512M');
set_time_limit(3600); // 1 saat

// Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Configuration
$sqlFile = '/var/www/vhosts/muzibu.com.tr/httpdocs/muzibu_songs.sql'; // Yeni SQL dosyası
$mappingFile = '/var/www/vhosts/muzibu.com.tr/httpdocs/muzibu-id-mappings.json';
$batchSize = 500; // Her batch'te kaç kayıt

echo "🎵 Muzibu Songs Migration - Başlatıldı\n";
echo "=====================================\n\n";

// SQL dosyası kontrolü
if (!file_exists($sqlFile)) {
    die("❌ SQL dosyası bulunamadı: $sqlFile\n\n⚠️ Lütfen önce Songs tablosu SQL export'unu bu konuma kopyalayın!\n");
}

echo "✅ SQL dosyası bulundu: " . number_format(filesize($sqlFile)) . " byte\n\n";

// ID mapping oku (önceki import'tan)
if (!file_exists($mappingFile)) {
    die("❌ ID mapping dosyası bulunamadı!\n\nÖnce diğer tabloları import edin: php public/import-muzibu-data.php\n");
}

$idMapping = json_decode(file_get_contents($mappingFile), true);
echo "✅ ID mapping yüklendi:\n";
echo "   - Artists: " . count($idMapping['artists']) . " mapping\n";
echo "   - Albums: " . count($idMapping['albums']) . " mapping\n";
echo "   - Genres: " . count($idMapping['genres']) . " mapping\n\n";

/**
 * JSON çeviri helper
 */
function makeJson($value)
{
    if (empty($value)) {
        return json_encode(['tr' => '', 'en' => ''], JSON_UNESCAPED_UNICODE);
    }

    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return json_encode([
        'tr' => $value,
        'en' => ''
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Stream-based SQL parser (memory-efficient)
 */
function parseSongsFromFile($filename, $tableName, $callback, $batchSize = 500)
{
    $handle = fopen($filename, 'r');
    if (!$handle) {
        throw new Exception("Dosya açılamadı: $filename");
    }

    echo "📊 SQL dosyası parse ediliyor (stream mode)...\n\n";

    // INSERT INTO satırını bul
    $foundInsert = false;
    $columns = [];

    while (($line = fgets($handle)) !== false) {
        if (strpos($line, "INSERT INTO `{$tableName}`") !== false) {
            $foundInsert = true;

            // Column names çıkar
            preg_match("/INSERT INTO `{$tableName}` \((.*?)\) VALUES/", $line, $matches);
            if (isset($matches[1])) {
                $columns = array_map('trim', explode(',', str_replace('`', '', $matches[1])));
                echo "✅ " . count($columns) . " column bulundu\n";
            }
            break;
        }
    }

    if (!$foundInsert) {
        fclose($handle);
        throw new Exception("'{$tableName}' tablosu SQL'de bulunamadı!");
    }

    // VALUES satırlarını parse et
    $buffer = '';
    $batch = [];
    $totalProcessed = 0;
    $batchCount = 0;

    while (($line = fgets($handle)) !== false) {
        $buffer .= $line;

        // Satır sonu kontrolü (row bittiyse)
        if (preg_match("/\),\s*$/", $line) || preg_match("/\);\s*$/", $line)) {
            // Row parse et
            if (preg_match_all("/\((.*?)\)/", $buffer, $matches)) {
                foreach ($matches[1] as $valueLine) {
                    // Values parse
                    preg_match_all("/'((?:[^']|'')*)'|NULL|(-?\d+)/", $valueLine, $valParts);

                    $values = [];
                    foreach ($valParts[0] as $val) {
                        if ($val === 'NULL') {
                            $values[] = null;
                        } elseif (preg_match("/^'(.*)'$/", $val, $m)) {
                            $values[] = str_replace("''", "'", $m[1]);
                        } else {
                            $values[] = $val;
                        }
                    }

                    if (count($values) === count($columns)) {
                        $batch[] = array_combine($columns, $values);
                        $totalProcessed++;

                        // Batch doldu mu?
                        if (count($batch) >= $batchSize) {
                            $batchCount++;
                            echo "   📦 Batch #{$batchCount}: {$totalProcessed} kayıt işlendi... ";
                            $callback($batch, $columns);
                            echo "✅\n";
                            $batch = [];
                        }
                    }
                }
            }

            $buffer = '';

            // SQL bitti mi? (semicolon ile biten satır)
            if (strpos($line, ');') !== false) {
                break;
            }
        }
    }

    // Son batch'i işle
    if (!empty($batch)) {
        $batchCount++;
        echo "   📦 Batch #{$batchCount} (son): {$totalProcessed} kayıt işlendi... ";
        $callback($batch, $columns);
        echo "✅\n";
    }

    fclose($handle);
    return $totalProcessed;
}

// ==========================================
// SONGS IMPORT
// ==========================================
echo "📌 SONGS Import başlıyor...\n\n";

$importedSongs = 0;
$skippedSongs = 0;
$songMapping = [];

try {
    $totalSongs = parseSongsFromFile($sqlFile, 'muzibu_songs', function($batch, $columns) use (&$importedSongs, &$skippedSongs, &$songMapping, $idMapping) {

        foreach ($batch as $row) {
            try {
                $oldId = $row['id'];
                $oldAlbumId = $row['album_id'] ?? null;
                $oldGenreId = $row['genre_id'] ?? null;

                // Album ID mapping
                $newAlbumId = null;
                if ($oldAlbumId && isset($idMapping['albums'][$oldAlbumId])) {
                    $newAlbumId = $idMapping['albums'][$oldAlbumId];
                }

                // Genre ID mapping
                $newGenreId = null;
                if ($oldGenreId && isset($idMapping['genres'][$oldGenreId])) {
                    $newGenreId = $idMapping['genres'][$oldGenreId];
                }

                // JSON dönüşüm
                $data = [
                    'album_id' => $newAlbumId,
                    'genre_id' => $newGenreId,
                    'title' => makeJson($row['title_tr']),
                    'slug' => makeJson($row['slug']),
                    'artist_name' => makeJson($row['artist_tr'] ?? ''),
                    'duration' => $row['duration'] ?? null,
                    'file_path' => $row['file'] ?? null, // Dosya kopyalama sonra yapılacak!
                    'lyrics' => makeJson($row['lyrics_tr'] ?? ''),
                    'media_id' => null, // Thumb sonra yüklenecek
                    'is_active' => (bool) ($row['active'] ?? 1),
                    'created_at' => $row['created'] ?? now(),
                    'updated_at' => $row['created'] ?? now(),
                ];

                // Insert
                $newId = DB::table('muzibu_songs')->insertGetId($data);
                $songMapping[$oldId] = $newId;
                $importedSongs++;

            } catch (\Exception $e) {
                $skippedSongs++;
                // Hata log'u (ilk 10 hata)
                if ($skippedSongs <= 10) {
                    echo "\n   ⚠️ Song ID {$row['id']}: " . $e->getMessage() . "\n";
                }
            }
        }

    }, $batchSize);

    echo "\n✅ Import tamamlandı!\n";
    echo "   Başarılı: {$importedSongs} kayıt\n";
    echo "   Hatalı: {$skippedSongs} kayıt\n\n";

} catch (\Exception $e) {
    die("❌ HATA: " . $e->getMessage() . "\n");
}

// ID mapping güncelle ve kaydet
$idMapping['songs'] = $songMapping;
file_put_contents($mappingFile, json_encode($idMapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "=====================================\n";
echo "🎉 SONGS IMPORT TAMAMLANDI!\n\n";
echo "📊 Özet:\n";
echo "   Songs: " . count($songMapping) . " kayıt\n\n";
echo "✅ ID mapping güncellendi: {$mappingFile}\n\n";

echo "⚠️ SONRAKI ADIMLAR:\n";
echo "1. Şarkı dosyalarını kopyalayın (eski path → yeni path)\n";
echo "2. Thumb resimlerini Spatie Media ile yükleyin\n";
echo "3. Playlist ve Favorites tablolarını import edin\n\n";

echo "🎵 İşlem tamamlandı!\n";
