<?php

namespace Modules\Muzibu\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Sector;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Radio;

class MuzibuDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Genres
        $genres = [
            ['title' => ['tr' => 'Pop', 'en' => 'Pop'], 'slug' => ['tr' => 'pop', 'en' => 'pop']],
            ['title' => ['tr' => 'Rock', 'en' => 'Rock'], 'slug' => ['tr' => 'rock', 'en' => 'rock']],
            ['title' => ['tr' => 'Jazz', 'en' => 'Jazz'], 'slug' => ['tr' => 'jazz', 'en' => 'jazz']],
            ['title' => ['tr' => 'Klasik', 'en' => 'Classical'], 'slug' => ['tr' => 'klasik', 'en' => 'classical']],
            ['title' => ['tr' => 'Elektronik', 'en' => 'Electronic'], 'slug' => ['tr' => 'elektronik', 'en' => 'electronic']],
        ];

        foreach ($genres as $genreData) {
            Genre::create($genreData);
        }

        // Artists
        $artists = [
            ['title' => ['tr' => 'Barış Manço', 'en' => 'Baris Manco'], 'slug' => ['tr' => 'baris-manco', 'en' => 'baris-manco']],
            ['title' => ['tr' => 'Sezen Aksu', 'en' => 'Sezen Aksu'], 'slug' => ['tr' => 'sezen-aksu', 'en' => 'sezen-aksu']],
            ['title' => ['tr' => 'Tarkan', 'en' => 'Tarkan'], 'slug' => ['tr' => 'tarkan', 'en' => 'tarkan']],
        ];

        foreach ($artists as $artistData) {
            Artist::create($artistData);
        }

        // Sectors
        $sectors = [
            ['title' => ['tr' => 'Restoran', 'en' => 'Restaurant'], 'slug' => ['tr' => 'restoran', 'en' => 'restaurant']],
            ['title' => ['tr' => 'Kafe', 'en' => 'Cafe'], 'slug' => ['tr' => 'kafe', 'en' => 'cafe']],
            ['title' => ['tr' => 'Market', 'en' => 'Market'], 'slug' => ['tr' => 'market', 'en' => 'market']],
            ['title' => ['tr' => 'Otel', 'en' => 'Hotel'], 'slug' => ['tr' => 'otel', 'en' => 'hotel']],
        ];

        foreach ($sectors as $sectorData) {
            Sector::create($sectorData);
        }

        $this->command->info('Muzibu test data seeded successfully!');
    }
}
