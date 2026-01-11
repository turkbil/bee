<?php

// Set content type to plain text
header('Content-Type: text/plain; charset=utf-8');

echo "🎵 Muzibu Data Import\n";
echo str_repeat('=', 50) . "\n\n";

// Load .env
$envFile = __DIR__ . '/../../../../.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// DB Connection
$host = $env['DB_HOST'] ?? 'localhost';
$database = $env['DB_DATABASE'] ?? 'tuufi_4ekim';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

echo "Database: {$database}\n";
echo "Host: {$host}\n\n";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = __DIR__ . '/../../../../muzibu_mayis25 (1).sql';
if (!file_exists($sqlFile)) {
    die("❌ SQL file not found\n");
}

$sqlContent = file_get_contents($sqlFile);
echo "✅ SQL file loaded (" . number_format(strlen($sqlContent)) . " bytes)\n\n";

// ID Mappings
$artistMapping = [];
$albumMapping = [];
$genreMapping = [];
$sectorMapping = [];
$radioMapping = [];

// Parse INSERT statements
function extractInsertData($sql, $tableName) {
    $pattern = "/INSERT INTO `{$tableName}`[^V]*VALUES\s*(.+?);/is";
    if (!preg_match($pattern, $sql, $matches)) {
        return [];
    }

    $valuesStr = $matches[1];
    $rows = [];
    $depth = 0;
    $currentRow = '';
    $inString = false;
    $stringChar = null;

    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $char = $valuesStr[$i];
        $prev = $i > 0 ? $valuesStr[$i-1] : '';

        if (($char === "'" || $char === '"') && $prev !== '\\') {
            if (!$inString) {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === $stringChar) {
                $inString = false;
                $stringChar = null;
            }
        }

        if (!$inString) {
            if ($char === '(') {
                $depth++;
                if ($depth === 1) {
                    $currentRow = '';
                    continue;
                }
            }

            if ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    $rows[] = $currentRow;
                    $currentRow = '';
                    continue;
                }
            }
        }

        if ($depth > 0) {
            $currentRow .= $char;
        }
    }

    return $rows;
}

function parseValues($rowString) {
    $values = [];
    $current = '';
    $inString = false;
    $stringChar = null;

    for ($i = 0; $i < strlen($rowString); $i++) {
        $char = $rowString[$i];
        $prev = $i > 0 ? $rowString[$i-1] : '';

        if (($char === "'" || $char === '"') && $prev !== '\\') {
            if (!$inString) {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === $stringChar) {
                $inString = false;
                $stringChar = null;
            }
            continue;
        }

        if (!$inString && $char === ',') {
            $values[] = $current;
            $current = '';
            continue;
        }

        $current .= $char;
    }

    if ($current !== '') {
        $values[] = $current;
    }

    return array_map('trim', $values);
}

// 1. IMPORT ARTISTS
echo "👤 ARTISTS\n";
echo str_repeat('-', 50) . "\n";

$artistRows = extractInsertData($sqlContent, 'muzibu_artists');
echo "Found: " . count($artistRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_artists (title, slug, bio, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, NULL, ?, ?, ?)");

foreach ($artistRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 7) continue;

    $oldId = (int)$vals[0];
    $titleTr = $vals[1];
    $slug = $vals[2];
    $bioTr = $vals[3];
    $active = (int)$vals[6];
    $created = $vals[5];

    $title = json_encode(['tr' => $titleTr, 'en' => $titleTr]);
    $slugJson = json_encode(['tr' => $slug, 'en' => $slug]);
    $bio = json_encode(['tr' => html_entity_decode($bioTr), 'en' => '']);

    try {
        $stmt->execute([$title, $slugJson, $bio, $active, $created, $created]);
        $newId = $pdo->lastInsertId();
        $artistMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ {$titleTr}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Imported: " . count($artistMapping) . "\n\n";

// 2. IMPORT GENRES
echo "🎸 GENRES\n";
echo str_repeat('-', 50) . "\n";

$genreRows = extractInsertData($sqlContent, 'muzibu_genres');
echo "Found: " . count($genreRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_genres (title, slug, description, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, NULL, 1, ?, ?)");

foreach ($genreRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 6) continue;

    $oldId = (int)$vals[0];
    $titleTr = $vals[1];
    $slug = $vals[2];
    $descTr = $vals[3];
    $created = $vals[5];

    $title = json_encode(['tr' => $titleTr, 'en' => $titleTr]);
    $slugJson = json_encode(['tr' => $slug, 'en' => $slug]);
    $desc = json_encode(['tr' => html_entity_decode($descTr), 'en' => '']);

    try {
        $stmt->execute([$title, $slugJson, $desc, $created, $created]);
        $newId = $pdo->lastInsertId();
        $genreMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ {$titleTr}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Imported: " . count($genreMapping) . "\n\n";

// 3. IMPORT SECTORS
echo "🏢 SECTORS\n";
echo str_repeat('-', 50) . "\n";

$sectorRows = extractInsertData($sqlContent, 'muzibu_sectors');
echo "Found: " . count($sectorRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_sectors (title, slug, media_id, is_active, created_at, updated_at) VALUES (?, ?, NULL, 1, ?, ?)");

foreach ($sectorRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 5) continue;

    $oldId = (int)$vals[0];
    $titleTr = $vals[1];
    $slug = $vals[2];
    $created = $vals[4];

    $title = json_encode(['tr' => $titleTr, 'en' => $titleTr]);
    $slugJson = json_encode(['tr' => $slug, 'en' => $slug]);

    try {
        $stmt->execute([$title, $slugJson, $created, $created]);
        $newId = $pdo->lastInsertId();
        $sectorMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ {$titleTr}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Imported: " . count($sectorMapping) . "\n\n";

// 4. IMPORT RADIOS
echo "📻 RADIOS\n";
echo str_repeat('-', 50) . "\n";

$radioRows = extractInsertData($sqlContent, 'muzibu_radios');
echo "Found: " . count($radioRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_radios (title, slug, media_id, is_active, created_at, updated_at) VALUES (?, ?, NULL, 1, ?, ?)");

foreach ($radioRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 5) continue;

    $oldId = (int)$vals[0];
    $titleTr = $vals[1];
    $slug = $vals[2];
    $created = $vals[4];

    $title = json_encode(['tr' => $titleTr, 'en' => $titleTr]);
    $slugJson = json_encode(['tr' => $slug, 'en' => $slug]);

    try {
        $stmt->execute([$title, $slugJson, $created, $created]);
        $newId = $pdo->lastInsertId();
        $radioMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ {$titleTr}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Imported: " . count($radioMapping) . "\n\n";

// 5. IMPORT ALBUMS
echo "💿 ALBUMS\n";
echo str_repeat('-', 50) . "\n";

$albumRows = extractInsertData($sqlContent, 'muzibu_albums');
echo "Found: " . count($albumRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_albums (title, slug, artist_id, description, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?, ?)");

foreach ($albumRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 8) continue;

    $oldId = (int)$vals[0];
    $titleTr = $vals[1];
    $slug = $vals[2];
    $artistId = strtoupper($vals[3]) === 'NULL' ? null : ($artistMapping[(int)$vals[3]] ?? null);
    $descTr = $vals[4];
    $active = (int)$vals[7];
    $created = $vals[6];

    $title = json_encode(['tr' => $titleTr, 'en' => $titleTr]);
    $slugJson = json_encode(['tr' => $slug, 'en' => $slug]);
    $desc = json_encode(['tr' => html_entity_decode($descTr), 'en' => '']);

    try {
        $stmt->execute([$title, $slugJson, $artistId, $desc, $active, $created, $created]);
        $newId = $pdo->lastInsertId();
        $albumMapping[$oldId] = $newId;
        echo "  ✓ {$titleTr} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ {$titleTr}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Imported: " . count($albumMapping) . "\n\n";

// SUMMARY
echo str_repeat('=', 50) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat('=', 50) . "\n";
echo "  ✅ Artists:  " . count($artistMapping) . "\n";
echo "  ✅ Genres:   " . count($genreMapping) . "\n";
echo "  ✅ Sectors:  " . count($sectorMapping) . "\n";
echo "  ✅ Radios:   " . count($radioMapping) . "\n";
echo "  ✅ Albums:   " . count($albumMapping) . "\n";
echo str_repeat('=', 50) . "\n\n";

// Save ID mappings
$mappingFile = __DIR__ . '/../../../../muzibu-id-mappings.json';
file_put_contents($mappingFile, json_encode([
    'artists' => $artistMapping,
    'albums' => $albumMapping,
    'genres' => $genreMapping,
    'sectors' => $sectorMapping,
    'radios' => $radioMapping,
], JSON_PRETTY_PRINT));

echo "💾 ID mappings saved: muzibu-id-mappings.json\n";
echo "✅ IMPORT COMPLETED!\n";
