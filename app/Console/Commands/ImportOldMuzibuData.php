<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\app\Models\Album;
use Modules\Muzibu\app\Models\Artist;
use Modules\Muzibu\app\Models\Genre;
use Modules\Muzibu\app\Models\Radio;
use Modules\Muzibu\app\Models\Sector;

class ImportOldMuzibuData extends Command
{
    protected $signature = 'muzibu:import-old-data {--table=all}';
    protected $description = 'Import old Muzibu data from muzibu_mayis25 database';

    private array $artistMapping = [];
    private array $albumMapping = [];
    private array $genreMapping = [];
    private array $sectorMapping = [];
    private array $radioMapping = [];

    public function handle()
    {
        $this->info('🎵 Muzibu Old Data Import Started');
        $this->info('Database: tuufi_4ekim (Central - Muzibu moved here)');

        // Connect to old database
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

        DB::purge('old_muzibu');
        DB::reconnect('old_muzibu');

        $table = $this->option('table');

        if ($table === 'all' || $table === 'artists') {
            $this->importArtists();
        }

        if ($table === 'all' || $table === 'albums') {
            $this->importAlbums();
        }

        if ($table === 'all' || $table === 'genres') {
            $this->importGenres();
        }

        if ($table === 'all' || $table === 'sectors') {
            $this->importSectors();
        }

        if ($table === 'all' || $table === 'radios') {
            $this->importRadios();
        }

        $this->info('✅ Import completed!');
        $this->showMappingSummary();
    }

    private function importArtists()
    {
        $this->info('👤 Importing Artists...');

        $oldArtists = DB::connection('old_muzibu')
            ->table('muzibu_artists')
            ->get();

        $this->info("Found {$oldArtists->count()} artists");

        foreach ($oldArtists as $old) {
            $newArtist = Artist::create([
                'title' => [
                    'tr' => $old->title_tr,
                    'en' => $old->title_tr,
                ],
                'slug' => [
                    'tr' => $old->slug,
                    'en' => $old->slug,
                ],
                'bio' => [
                    'tr' => $old->bio_tr ?? '',
                    'en' => '',
                ],
                'media_id' => null, // Thumb will be uploaded later
                'is_active' => (bool) $old->active,
                'created_at' => $old->created,
                'updated_at' => $old->created,
            ]);

            $this->artistMapping[$old->id] = $newArtist->artist_id;
            $this->line("  - {$old->title_tr} (ID: {$old->id} → {$newArtist->artist_id})");
        }

        $this->info("✅ Artists imported: {$oldArtists->count()}");
    }

    private function importAlbums()
    {
        $this->info('💿 Importing Albums...');

        $oldAlbums = DB::connection('old_muzibu')
            ->table('muzibu_albums')
            ->get();

        $this->info("Found {$oldAlbums->count()} albums");

        foreach ($oldAlbums as $old) {
            $newAlbum = Album::create([
                'title' => [
                    'tr' => $old->title_tr,
                    'en' => $old->title_tr,
                ],
                'slug' => [
                    'tr' => $old->slug,
                    'en' => $old->slug,
                ],
                'artist_id' => $this->artistMapping[$old->artist_id] ?? null,
                'description' => [
                    'tr' => $old->description_tr ?? '',
                    'en' => '',
                ],
                'media_id' => null, // Thumb will be uploaded later
                'is_active' => (bool) $old->active,
                'created_at' => $old->created,
                'updated_at' => $old->created,
            ]);

            $this->albumMapping[$old->id] = $newAlbum->album_id;
            $this->line("  - {$old->title_tr} (ID: {$old->id} → {$newAlbum->album_id})");
        }

        $this->info("✅ Albums imported: {$oldAlbums->count()}");
    }

    private function importGenres()
    {
        $this->info('🎸 Importing Genres...');

        $oldGenres = DB::connection('old_muzibu')
            ->table('muzibu_genres')
            ->get();

        $this->info("Found {$oldGenres->count()} genres");

        foreach ($oldGenres as $old) {
            $newGenre = Genre::create([
                'title' => [
                    'tr' => $old->title_tr,
                    'en' => $old->title_tr,
                ],
                'slug' => [
                    'tr' => $old->slug,
                    'en' => $old->slug,
                ],
                'description' => [
                    'tr' => $old->description_tr ?? '',
                    'en' => '',
                ],
                'media_id' => null, // Thumb will be uploaded later
                'is_active' => true, // Old table doesn't have active field
                'created_at' => $old->created,
                'updated_at' => $old->created,
            ]);

            $this->genreMapping[$old->id] = $newGenre->genre_id;
            $this->line("  - {$old->title_tr} (ID: {$old->id} → {$newGenre->genre_id})");
        }

        $this->info("✅ Genres imported: {$oldGenres->count()}");
    }

    private function importSectors()
    {
        $this->info('🏢 Importing Sectors...');

        $oldSectors = DB::connection('old_muzibu')
            ->table('muzibu_sectors')
            ->get();

        $this->info("Found {$oldSectors->count()} sectors");

        foreach ($oldSectors as $old) {
            $newSector = Sector::create([
                'title' => [
                    'tr' => $old->title_tr,
                    'en' => $old->title_tr,
                ],
                'slug' => [
                    'tr' => $old->slug,
                    'en' => $old->slug,
                ],
                'media_id' => null, // Thumb will be uploaded later
                'is_active' => true, // Old table doesn't have active field
                'created_at' => $old->created,
                'updated_at' => $old->created,
            ]);

            $this->sectorMapping[$old->id] = $newSector->sector_id;
            $this->line("  - {$old->title_tr} (ID: {$old->id} → {$newSector->sector_id})");
        }

        $this->info("✅ Sectors imported: {$oldSectors->count()}");
    }

    private function importRadios()
    {
        $this->info('📻 Importing Radios...');

        $oldRadios = DB::connection('old_muzibu')
            ->table('muzibu_radios')
            ->get();

        $this->info("Found {$oldRadios->count()} radios");

        foreach ($oldRadios as $old) {
            $newRadio = Radio::create([
                'title' => [
                    'tr' => $old->title_tr,
                    'en' => $old->title_tr,
                ],
                'slug' => [
                    'tr' => $old->slug,
                    'en' => $old->slug,
                ],
                'media_id' => null, // Thumb will be uploaded later
                'is_active' => true, // Old table doesn't have active field
                'created_at' => $old->created,
                'updated_at' => $old->created,
            ]);

            $this->radioMapping[$old->id] = $newRadio->radio_id;
            $this->line("  - {$old->title_tr} (ID: {$old->id} → {$newRadio->radio_id})");
        }

        $this->info("✅ Radios imported: {$oldRadios->count()}");
    }

    private function showMappingSummary()
    {
        $this->newLine();
        $this->info('📊 ID Mapping Summary:');
        $this->table(
            ['Table', 'Old → New IDs'],
            [
                ['Artists', count($this->artistMapping) . ' mapped'],
                ['Albums', count($this->albumMapping) . ' mapped'],
                ['Genres', count($this->genreMapping) . ' mapped'],
                ['Sectors', count($this->sectorMapping) . ' mapped'],
                ['Radios', count($this->radioMapping) . ' mapped'],
            ]
        );
    }
}
