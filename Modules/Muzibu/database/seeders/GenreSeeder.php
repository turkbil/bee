<?php

namespace Modules\Muzibu\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Muzibu\App\Models\Genre;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            [
                'title' => ['tr' => 'Pop', 'en' => 'Pop'],
                'slug' => ['tr' => 'pop', 'en' => 'pop'],
                'description' => ['tr' => 'Popüler müzik türü', 'en' => 'Popular music genre'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Rock', 'en' => 'Rock'],
                'slug' => ['tr' => 'rock', 'en' => 'rock'],
                'description' => ['tr' => 'Rock müzik', 'en' => 'Rock music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Rap / Hip-Hop', 'en' => 'Rap / Hip-Hop'],
                'slug' => ['tr' => 'rap-hip-hop', 'en' => 'rap-hip-hop'],
                'description' => ['tr' => 'Rap ve Hip-Hop müzik', 'en' => 'Rap and Hip-Hop music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Arabesk', 'en' => 'Arabesk'],
                'slug' => ['tr' => 'arabesk', 'en' => 'arabesk'],
                'description' => ['tr' => 'Türk arabesk müziği', 'en' => 'Turkish arabesk music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Türk Halk Müziği', 'en' => 'Turkish Folk Music'],
                'slug' => ['tr' => 'turk-halk-muzigi', 'en' => 'turkish-folk-music'],
                'description' => ['tr' => 'Geleneksel Türk halk müziği', 'en' => 'Traditional Turkish folk music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Klasik Müzik', 'en' => 'Classical Music'],
                'slug' => ['tr' => 'klasik-muzik', 'en' => 'classical-music'],
                'description' => ['tr' => 'Klasik müzik eserleri', 'en' => 'Classical music works'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Jazz', 'en' => 'Jazz'],
                'slug' => ['tr' => 'jazz', 'en' => 'jazz'],
                'description' => ['tr' => 'Jazz müzik', 'en' => 'Jazz music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Elektronik / Dance', 'en' => 'Electronic / Dance'],
                'slug' => ['tr' => 'elektronik-dance', 'en' => 'electronic-dance'],
                'description' => ['tr' => 'Elektronik dans müziği', 'en' => 'Electronic dance music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'R&B / Soul', 'en' => 'R&B / Soul'],
                'slug' => ['tr' => 'rb-soul', 'en' => 'rb-soul'],
                'description' => ['tr' => 'R&B ve Soul müzik', 'en' => 'R&B and Soul music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Türkü', 'en' => 'Turkish Folk Song'],
                'slug' => ['tr' => 'turku', 'en' => 'turkish-folk-song'],
                'description' => ['tr' => 'Türk türküleri', 'en' => 'Turkish folk songs'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Metal', 'en' => 'Metal'],
                'slug' => ['tr' => 'metal', 'en' => 'metal'],
                'description' => ['tr' => 'Heavy metal müzik', 'en' => 'Heavy metal music'],
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Alternative', 'en' => 'Alternative'],
                'slug' => ['tr' => 'alternative', 'en' => 'alternative'],
                'description' => ['tr' => 'Alternatif müzik', 'en' => 'Alternative music'],
                'is_active' => true,
            ],
        ];

        foreach ($genres as $genre) {
            Genre::create($genre);
        }

        $this->command->info('✅ Genres (Türler) oluşturuldu: ' . count($genres));
    }
}
