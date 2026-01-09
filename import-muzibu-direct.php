<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🎵 Muzibu Data Import (Direct SQL Parse)\n";
echo "Source: muzibu_mayis25 (1).sql\n";
echo "Target: tuufi_4ekim database\n\n";

$sqlFile = '/var/www/vhosts/muzibu.com.tr/httpdocs/muzibu_mayis25 (1).sql';

if (!file_exists($sqlFile)) {
    die("❌ SQL file not found: {$sqlFile}\n");
}

// ID Mappings
$artistMapping = [];
$albumMapping = [];
$genreMapping = [];
$sectorMapping = [];
$radioMapping = [];

$sqlContent = file_get_contents($sqlFile);

// Parse INSERT statements for each table
function parseInserts($sql, $tableName) {
    preg_match_all(
        "/INSERT INTO `{$tableName}`.*?VALUES\s+(.*?);/is",
        $sql,
        $matches
    );

    if (empty($matches[1])) {
        return [];
    }

    $valuesString = $matches[1][0];

    // Split by "),(" to get individual rows
    $rows = [];
    preg_match_all("/\((.*?)\)(?:,|\s*$)/s", $valuesString, $rowMatches);

    return $rowMatches[1] ?? [];
}

function parseRow($rowString) {
    $values = [];
    $inQuote = false;
    $current = '';
    $quoteChar = null;

    for ($i = 0; $i < strlen($rowString); $i++) {
        $char = $rowString[$i];

        if (!$inQuote && ($char === "'" || $char === '"')) {
            $inQuote = true;
            $quoteChar = $char;
            continue;
        }

        if ($inQuote && $char === $quoteChar) {
            // Check if escaped
            if ($i + 1 < strlen($rowString) && $rowString[$i + 1] === $quoteChar) {
                $current .= $char;
                $i++; // Skip next quote
                continue;
            }
            $inQuote = false;
            $quoteChar = null;
            continue;
        }

        if (!$inQuote && $char === ',') {
            $values[] = trim($current);
            $current = '';
            continue;
        }

        $current .= $char;
    }

    if ($current !== '') {
        $values[] = trim($current);
    }

    return $values;
}

// IMPORT ARTISTS
echo "👤 Importing Artists...\n";
$artistRows = parseInserts($sqlContent, 'muzibu_artists');
echo "Found: " . count($artistRows) . " artists\n";

foreach ($artistRows as $row) {
    $values = parseRow($row);

    if (count($values) < 7) continue;

    // id, title_tr, slug, bio_tr, thumb, created, active, meta_title, meta_keywords, meta_description
    $oldId = (int) $values[0];
    $titleTr = trim($values[1], "'\"");
    $slug = trim($values[2], "'\"");
    $bioTr = trim($values[3], "'\"");
    $thumb = trim($values[4], "'\"");
    $created = trim($values[5], "'\"");
    $active = (int) $values[6];

    try {
        $newId = DB::table('muzibu_artists')->insertGetId([
            'title' => json_encode(['tr' => $titleTr, 'en' => $titleTr]),
            'slug' => json_encode(['tr' => $slug, 'en' => $slug]),
            'bio' => json_encode(['tr' => html_entity_decode($bioTr), 'en' => '']),
            'media_id' => null,
            'is_active' => (bool) $active,
            'created_at' => $created,
            'updated_at' => $created,
        ]);

        $artistMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$titleTr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Artists: " . count($artistMapping) . " imported\n\n";

// IMPORT GENRES
echo "🎸 Importing Genres...\n";
$genreRows = parseInserts($sqlContent, 'muzibu_genres');
echo "Found: " . count($genreRows) . " genres\n";

foreach ($genreRows as $row) {
    $values = parseRow($row);

    if (count($values) < 6) continue;

    // id, title_tr, slug, description_tr, thumb, created, meta_*
    $oldId = (int) $values[0];
    $titleTr = trim($values[1], "'\"");
    $slug = trim($values[2], "'\"");
    $descTr = trim($values[3], "'\"");
    $thumb = trim($values[4], "'\"");
    $created = trim($values[5], "'\"");

    try {
        $newId = DB::table('muzibu_genres')->insertGetId([
            'title' => json_encode(['tr' => $titleTr, 'en' => $titleTr]),
            'slug' => json_encode(['tr' => $slug, 'en' => $slug]),
            'description' => json_encode(['tr' => html_entity_decode($descTr), 'en' => '']),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $created,
            'updated_at' => $created,
        ]);

        $genreMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$titleTr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Genres: " . count($genreMapping) . " imported\n\n";

// IMPORT SECTORS
echo "🏢 Importing Sectors...\n";
$sectorRows = parseInserts($sqlContent, 'muzibu_sectors');
echo "Found: " . count($sectorRows) . " sectors\n";

foreach ($sectorRows as $row) {
    $values = parseRow($row);

    if (count($values) < 5) continue;

    // id, title_tr, slug, thumb, created, meta_*
    $oldId = (int) $values[0];
    $titleTr = trim($values[1], "'\"");
    $slug = trim($values[2], "'\"");
    $thumb = trim($values[3], "'\"");
    $created = trim($values[4], "'\"");

    try {
        $newId = DB::table('muzibu_sectors')->insertGetId([
            'title' => json_encode(['tr' => $titleTr, 'en' => $titleTr]),
            'slug' => json_encode(['tr' => $slug, 'en' => $slug]),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $created,
            'updated_at' => $created,
        ]);

        $sectorMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$titleTr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Sectors: " . count($sectorMapping) . " imported\n\n";

// IMPORT RADIOS
echo "📻 Importing Radios...\n";
$radioRows = parseInserts($sqlContent, 'muzibu_radios');
echo "Found: " . count($radioRows) . " radios\n";

foreach ($radioRows as $row) {
    $values = parseRow($row);

    if (count($values) < 5) continue;

    // id, title_tr, slug, thumb, created, meta_*
    $oldId = (int) $values[0];
    $titleTr = trim($values[1], "'\"");
    $slug = trim($values[2], "'\"");
    $thumb = trim($values[3], "'\"");
    $created = trim($values[4], "'\"");

    try {
        $newId = DB::table('muzibu_radios')->insertGetId([
            'title' => json_encode(['tr' => $titleTr, 'en' => $titleTr]),
            'slug' => json_encode(['tr' => $slug, 'en' => $slug]),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $created,
            'updated_at' => $created,
        ]);

        $radioMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$titleTr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Radios: " . count($radioMapping) . " imported\n\n";

// IMPORT ALBUMS (Artist mapping gerekli, en son)
echo "💿 Importing Albums...\n";
$albumRows = parseInserts($sqlContent, 'muzibu_albums');
echo "Found: " . count($albumRows) . " albums\n";

foreach ($albumRows as $row) {
    $values = parseRow($row);

    if (count($values) < 8) continue;

    // id, title_tr, slug, artist_id, description_tr, thumb, created, active, meta_*
    $oldId = (int) $values[0];
    $titleTr = trim($values[1], "'\"");
    $slug = trim($values[2], "'\"");
    $artistId = trim($values[3], "'\"") === 'NULL' ? null : (int) $values[3];
    $descTr = trim($values[4], "'\"");
    $thumb = trim($values[5], "'\"");
    $created = trim($values[6], "'\"");
    $active = (int) $values[7];

    try {
        $newId = DB::table('muzibu_albums')->insertGetId([
            'title' => json_encode(['tr' => $titleTr, 'en' => $titleTr]),
            'slug' => json_encode(['tr' => $slug, 'en' => $slug]),
            'artist_id' => $artistId ? ($artistMapping[$artistId] ?? null) : null,
            'description' => json_encode(['tr' => html_entity_decode($descTr), 'en' => '']),
            'media_id' => null,
            'is_active' => (bool) $active,
            'created_at' => $created,
            'updated_at' => $created,
        ]);

        $albumMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$titleTr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Albums: " . count($albumMapping) . " imported\n\n";

// SUMMARY
echo "📊 Summary:\n";
echo "  ✅ Artists: " . count($artistMapping) . "\n";
echo "  ✅ Genres: " . count($genreMapping) . "\n";
echo "  ✅ Sectors: " . count($sectorMapping) . "\n";
echo "  ✅ Radios: " . count($radioMapping) . "\n";
echo "  ✅ Albums: " . count($albumMapping) . "\n\n";

// Save mappings
file_put_contents(__DIR__.'/muzibu-id-mappings.json', json_encode([
    'artists' => $artistMapping,
    'albums' => $albumMapping,
    'genres' => $genreMapping,
    'sectors' => $sectorMapping,
    'radios' => $radioMapping,
], JSON_PRETTY_PRINT));

echo "💾 ID mappings saved to: muzibu-id-mappings.json\n";
echo "✅ Import completed!\n";
