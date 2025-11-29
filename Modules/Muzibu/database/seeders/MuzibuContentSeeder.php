<?php

namespace Modules\Muzibu\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Muzibu\App\Models\{Artist, Album, Song, Playlist, Genre, Sector};
use Illuminate\Support\Facades\DB;

class MuzibuContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎵 Muzibu içerik oluşturuluyor...');

        // Genres ve Sectors varsa kullan
        $popGenre = Genre::where('slug->tr', 'pop')->first();
        $rockGenre = Genre::where('slug->tr', 'rock')->first();
        $rapGenre = Genre::where('slug->tr', 'rap-hip-hop')->first();
        
        // Artists oluştur
        $artists = [
            ['title' => ['tr' => 'Tarkan'], 'slug' => ['tr' => 'tarkan'], 'is_active' => true],
            ['title' => ['tr' => 'Sezen Aksu'], 'slug' => ['tr' => 'sezen-aksu'], 'is_active' => true],
            ['title' => ['tr' => 'Barış Manço'], 'slug' => ['tr' => 'baris-manco'], 'is_active' => true],
            ['title' => ['tr' => 'Ajda Pekkan'], 'slug' => ['tr' => 'ajda-pekkan'], 'is_active' => true],
            ['title' => ['tr' => 'Sertab Erener'], 'slug' => ['tr' => 'sertab-erener'], 'is_active' => true],
        ];

        foreach ($artists as $artistData) {
            $artist = Artist::create($artistData);
            
            // Her sanatçı için 2 albüm
            for ($i = 1; $i <= 2; $i++) {
                $album = Album::create([
                    'artist_id' => $artist->artist_id,
                    'title' => ['tr' => $artistData['title']['tr'] . ' - Albüm ' . $i],
                    'slug' => ['tr' => $artistData['slug']['tr'] . '-album-' . $i],
                    'is_active' => true,
                ]);

                // Her albüm için 5 şarkı
                for ($j = 1; $j <= 5; $j++) {
                    Song::create([
                        'album_id' => $album->album_id,
                        'genre_id' => $popGenre?->genre_id ?? 1,
                        'title' => ['tr' => 'Şarkı ' . (($i-1)*5 + $j)],
                        'slug' => ['tr' => 'sarki-' . (($i-1)*5 + $j)],
                        'duration' => rand(180, 300),
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('✅ Artists: 5, Albums: 10, Songs: 50 oluşturuldu');

        // Sistem Playlistleri
        $playlists = [
            [
                'title' => ['tr' => 'Top 50 Türkiye'], 
                'slug' => ['tr' => 'top-50-turkiye'],
                'description' => ['tr' => 'En çok dinlenen 50 şarkı'],
                'is_system' => true,
                'is_public' => true,
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Çalışırken Dinle'], 
                'slug' => ['tr' => 'calisirken-dinle'],
                'description' => ['tr' => 'Odaklanmak için sakin müzikler'],
                'is_system' => true,
                'is_public' => true,
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Sabah Motivasyonu'], 
                'slug' => ['tr' => 'sabah-motivasyonu'],
                'description' => ['tr' => 'Güne enerjik başlayın'],
                'is_system' => true,
                'is_public' => true,
                'is_active' => true,
            ],
            [
                'title' => ['tr' => '90\'lar Nostalji'],
                'slug' => ['tr' => '90lar-nostalji'],
                'description' => ['tr' => '90\'ların unutulmaz şarkıları'],
                'is_system' => true,
                'is_public' => true,
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'Chill Beats'], 
                'slug' => ['tr' => 'chill-beats'],
                'description' => ['tr' => 'Rahatlatıcı ritimler'],
                'is_system' => true,
                'is_public' => true,
                'is_active' => true,
            ],
        ];

        foreach ($playlists as $playlistData) {
            $playlist = Playlist::create($playlistData);
            
            // Her playlist'e rastgele 10 şarkı ekle
            $songs = Song::inRandomOrder()->limit(10)->get();
            foreach ($songs as $index => $song) {
                DB::table('muzibu_playlist_song')->insert([
                    'playlist_id' => $playlist->playlist_id,
                    'song_id' => $song->song_id,
                    'position' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Playlists: 5 sistem playlist oluşturuldu');
        $this->command->info('🎉 Tüm Muzibu içerikleri başarıyla oluşturuldu!');
    }
}
