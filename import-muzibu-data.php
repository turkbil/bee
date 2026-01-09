<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🎵 Muzibu Old Data Import\n";
echo "Database: tuufi_4ekim (Central)\n\n";

// ID Mappings
$artistMapping = [];
$albumMapping = [];
$genreMapping = [];
$sectorMapping = [];
$radioMapping = [];

// Connect to old database
DB::purge('old_muzibu');
config([
    'database.connections.old_muzibu' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'muzibu_mayis25',
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ]
]);

DB::reconnect('old_muzibu');

// Test connections
try {
    $oldCount = DB::connection('old_muzibu')->table('muzibu_artists')->count();
    echo "✅ Old DB connected: {$oldCount} artists found\n";

    $newCount = DB::connection('mysql')->table('muzibu_artists')->count();
    echo "✅ New DB connected: {$newCount} artists currently\n\n";
} catch (\Exception $e) {
    echo "❌ Connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// IMPORT ARTISTS
echo "👤 Importing Artists...\n";
$oldArtists = DB::connection('old_muzibu')->table('muzibu_artists')->get();
echo "Found: {$oldArtists->count()} artists\n";

foreach ($oldArtists as $old) {
    try {
        $newId = DB::table('muzibu_artists')->insertGetId([
            'title' => json_encode(['tr' => $old->title_tr, 'en' => $old->title_tr]),
            'slug' => json_encode(['tr' => $old->slug, 'en' => $old->slug]),
            'bio' => json_encode(['tr' => $old->bio_tr ?? '', 'en' => '']),
            'media_id' => null,
            'is_active' => (bool) $old->active,
            'created_at' => $old->created,
            'updated_at' => $old->created,
        ]);

        $artistMapping[$old->id] = $newId;
        echo "  ✓ {$old->title_tr} (ID: {$old->id} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$old->title_tr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Artists imported: " . count($artistMapping) . "\n\n";

// IMPORT ALBUMS
echo "💿 Importing Albums...\n";
$oldAlbums = DB::connection('old_muzibu')->table('muzibu_albums')->get();
echo "Found: {$oldAlbums->count()} albums\n";

foreach ($oldAlbums as $old) {
    try {
        $newId = DB::table('muzibu_albums')->insertGetId([
            'title' => json_encode(['tr' => $old->title_tr, 'en' => $old->title_tr]),
            'slug' => json_encode(['tr' => $old->slug, 'en' => $old->slug]),
            'artist_id' => $artistMapping[$old->artist_id] ?? null,
            'description' => json_encode(['tr' => $old->description_tr ?? '', 'en' => '']),
            'media_id' => null,
            'is_active' => (bool) $old->active,
            'created_at' => $old->created,
            'updated_at' => $old->created,
        ]);

        $albumMapping[$old->id] = $newId;
        echo "  ✓ {$old->title_tr} (ID: {$old->id} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$old->title_tr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Albums imported: " . count($albumMapping) . "\n\n";

// IMPORT GENRES
echo "🎸 Importing Genres...\n";
$oldGenres = DB::connection('old_muzibu')->table('muzibu_genres')->get();
echo "Found: {$oldGenres->count()} genres\n";

foreach ($oldGenres as $old) {
    try {
        $newId = DB::table('muzibu_genres')->insertGetId([
            'title' => json_encode(['tr' => $old->title_tr, 'en' => $old->title_tr]),
            'slug' => json_encode(['tr' => $old->slug, 'en' => $old->slug]),
            'description' => json_encode(['tr' => $old->description_tr ?? '', 'en' => '']),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $old->created,
            'updated_at' => $old->created,
        ]);

        $genreMapping[$old->id] = $newId;
        echo "  ✓ {$old->title_tr} (ID: {$old->id} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$old->title_tr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Genres imported: " . count($genreMapping) . "\n\n";

// IMPORT SECTORS
echo "🏢 Importing Sectors...\n";
$oldSectors = DB::connection('old_muzibu')->table('muzibu_sectors')->get();
echo "Found: {$oldSectors->count()} sectors\n";

foreach ($oldSectors as $old) {
    try {
        $newId = DB::table('muzibu_sectors')->insertGetId([
            'title' => json_encode(['tr' => $old->title_tr, 'en' => $old->title_tr]),
            'slug' => json_encode(['tr' => $old->slug, 'en' => $old->slug]),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $old->created,
            'updated_at' => $old->created,
        ]);

        $sectorMapping[$old->id] = $newId;
        echo "  ✓ {$old->title_tr} (ID: {$old->id} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$old->title_tr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Sectors imported: " . count($sectorMapping) . "\n\n";

// IMPORT RADIOS
echo "📻 Importing Radios...\n";
$oldRadios = DB::connection('old_muzibu')->table('muzibu_radios')->get();
echo "Found: {$oldRadios->count()} radios\n";

foreach ($oldRadios as $old) {
    try {
        $newId = DB::table('muzibu_radios')->insertGetId([
            'title' => json_encode(['tr' => $old->title_tr, 'en' => $old->title_tr]),
            'slug' => json_encode(['tr' => $old->slug, 'en' => $old->slug]),
            'media_id' => null,
            'is_active' => true,
            'created_at' => $old->created,
            'updated_at' => $old->created,
        ]);

        $radioMapping[$old->id] = $newId;
        echo "  ✓ {$old->title_tr} (ID: {$old->id} → {$newId})\n";
    } catch (\Exception $e) {
        echo "  ✗ Error: {$old->title_tr} - " . $e->getMessage() . "\n";
    }
}

echo "✅ Radios imported: " . count($radioMapping) . "\n\n";

// SUMMARY
echo "📊 ID Mapping Summary:\n";
echo "  - Artists: " . count($artistMapping) . " mapped\n";
echo "  - Albums: " . count($albumMapping) . " mapped\n";
echo "  - Genres: " . count($genreMapping) . " mapped\n";
echo "  - Sectors: " . count($sectorMapping) . " mapped\n";
echo "  - Radios: " . count($radioMapping) . " mapped\n\n";

echo "✅ Import completed!\n";

// Save mappings to file for next imports (songs, playlists, etc.)
file_put_contents(__DIR__.'/muzibu-id-mappings.json', json_encode([
    'artists' => $artistMapping,
    'albums' => $albumMapping,
    'genres' => $genreMapping,
    'sectors' => $sectorMapping,
    'radios' => $radioMapping,
], JSON_PRETTY_PRINT));

echo "💾 ID mappings saved to: muzibu-id-mappings.json\n";
