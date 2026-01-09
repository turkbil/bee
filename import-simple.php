<?php

echo "🎵 Muzibu Data Import (Simple)\n\n";

// Load .env
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

// DB Connection
$host = $env['DB_HOST'] ?? 'localhost';
$database = $env['DB_DATABASE'] ?? 'tuufi_4ekim';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to: {$database}\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = '/var/www/vhosts/muzibu.com.tr/httpdocs/muzibu_mayis25 (1).sql';
if (!file_exists($sqlFile)) {
    die("❌ SQL file not found\n");
}

$sqlContent = file_get_contents($sqlFile);
echo "✅ SQL file loaded\n\n";

// ID Mappings
$artistMapping = [];
$albumMapping = [];
$genreMapping = [];
$sectorMapping = [];
$radioMapping = [];

// Parse function
function extractInsertData($sql, $tableName) {
    $pattern = "/INSERT INTO `{$tableName}`[^V]*VALUES\s*(.+?);/is";
    preg_match($pattern, $sql, $matches);

    if (empty($matches[1])) {
        return [];
    }

    $valuesStr = $matches[1];

    // Split rows by ),(
    $rows = [];
    $depth = 0;
    $currentRow = '';
    $inString = false;
    $stringChar = null;

    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $char = $valuesStr[$i];
        $prev = $i > 0 ? $valuesStr[$i-1] : '';

        // Handle strings
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

// IMPORT ARTISTS
echo "👤 Importing Artists...\n";
$artistRows = extractInsertData($sqlContent, 'muzibu_artists');
echo "Found: " . count($artistRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_artists (title, slug, bio, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, NULL, ?, ?, ?)");

foreach ($artistRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 7) continue;

    $oldId = (int)$vals[0];
    $title = json_encode(['tr' => $vals[1], 'en' => $vals[1]]);
    $slug = json_encode(['tr' => $vals[2], 'en' => $vals[2]]);
    $bio = json_encode(['tr' => html_entity_decode($vals[3]), 'en' => '']);
    $active = (int)$vals[6];
    $created = $vals[5];

    try {
        $stmt->execute([$title, $slug, $bio, $active, $created, $created]);
        $newId = $pdo->lastInsertId();
        $artistMapping[$oldId] = $newId;
        echo "  ✓ {$vals[1]} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "✅ Artists: " . count($artistMapping) . "\n\n";

// IMPORT GENRES
echo "🎸 Importing Genres...\n";
$genreRows = extractInsertData($sqlContent, 'muzibu_genres');
echo "Found: " . count($genreRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_genres (title, slug, description, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, NULL, 1, ?, ?)");

foreach ($genreRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 6) continue;

    $oldId = (int)$vals[0];
    $title = json_encode(['tr' => $vals[1], 'en' => $vals[1]]);
    $slug = json_encode(['tr' => $vals[2], 'en' => $vals[2]]);
    $desc = json_encode(['tr' => html_entity_decode($vals[3]), 'en' => '']);
    $created = $vals[5];

    try {
        $stmt->execute([$title, $slug, $desc, $created, $created]);
        $newId = $pdo->lastInsertId();
        $genreMapping[$oldId] = $newId;
        echo "  ✓ {$vals[1]} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "✅ Genres: " . count($genreMapping) . "\n\n";

// IMPORT SECTORS
echo "🏢 Importing Sectors...\n";
$sectorRows = extractInsertData($sqlContent, 'muzibu_sectors');
echo "Found: " . count($sectorRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_sectors (title, slug, media_id, is_active, created_at, updated_at) VALUES (?, ?, NULL, 1, ?, ?)");

foreach ($sectorRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 5) continue;

    $oldId = (int)$vals[0];
    $title = json_encode(['tr' => $vals[1], 'en' => $vals[1]]);
    $slug = json_encode(['tr' => $vals[2], 'en' => $vals[2]]);
    $created = $vals[4];

    try {
        $stmt->execute([$title, $slug, $created, $created]);
        $newId = $pdo->lastInsertId();
        $sectorMapping[$oldId] = $newId;
        echo "  ✓ {$vals[1]} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "✅ Sectors: " . count($sectorMapping) . "\n\n";

// IMPORT RADIOS
echo "📻 Importing Radios...\n";
$radioRows = extractInsertData($sqlContent, 'muzibu_radios');
echo "Found: " . count($radioRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_radios (title, slug, media_id, is_active, created_at, updated_at) VALUES (?, ?, NULL, 1, ?, ?)");

foreach ($radioRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 5) continue;

    $oldId = (int)$vals[0];
    $title = json_encode(['tr' => $vals[1], 'en' => $vals[1]]);
    $slug = json_encode(['tr' => $vals[2], 'en' => $vals[2]]);
    $created = $vals[4];

    try {
        $stmt->execute([$title, $slug, $created, $created]);
        $newId = $pdo->lastInsertId();
        $radioMapping[$oldId] = $newId;
        echo "  ✓ {$vals[1]} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "✅ Radios: " . count($radioMapping) . "\n\n";

// IMPORT ALBUMS
echo "💿 Importing Albums...\n";
$albumRows = extractInsertData($sqlContent, 'muzibu_albums');
echo "Found: " . count($albumRows) . " rows\n";

$stmt = $pdo->prepare("INSERT INTO muzibu_albums (title, slug, artist_id, description, media_id, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?, ?)");

foreach ($albumRows as $row) {
    $vals = parseValues($row);
    if (count($vals) < 8) continue;

    $oldId = (int)$vals[0];
    $title = json_encode(['tr' => $vals[1], 'en' => $vals[1]]);
    $slug = json_encode(['tr' => $vals[2], 'en' => $vals[2]]);
    $artistId = strtoupper($vals[3]) === 'NULL' ? null : ($artistMapping[(int)$vals[3]] ?? null);
    $desc = json_encode(['tr' => html_entity_decode($vals[4]), 'en' => '']);
    $active = (int)$vals[7];
    $created = $vals[6];

    try {
        $stmt->execute([$title, $slug, $artistId, $desc, $active, $created, $created]);
        $newId = $pdo->lastInsertId();
        $albumMapping[$oldId] = $newId;
        echo "  ✓ {$vals[1]} ({$oldId} → {$newId})\n";
    } catch (PDOException $e) {
        echo "  ✗ Error: {$vals[1]} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Albums: " . count($albumMapping) . "\n\n";

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

echo "💾 Mappings saved to: muzibu-id-mappings.json\n";
echo "✅ All done!\n";
