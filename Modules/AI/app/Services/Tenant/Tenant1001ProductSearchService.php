<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Sector;
use Modules\Muzibu\App\Models\Radio;

/**
 * Tenant 1001 (muzibu.com.tr) Music Search Service
 *
 * Müzik arama servisi - Şarkı, albüm, artist, playlist, radyo, genre, sector arama
 *
 * Tenant-aware: Sadece tenant 1001 (muzibu) için kullanılır
 * Database: tenant_muzibu_1528d0
 *
 * @package Modules\AI\App\Services\Tenant
 * @version 1.0
 */
class Tenant1001ProductSearchService
{
    protected string $locale;

    /**
     * Müzik arama kategorileri
     */
    private const SEARCH_CATEGORIES = [
        'songs' => 'Şarkı',
        'albums' => 'Albüm',
        'artists' => 'Sanatçı',
        'playlists' => 'Playlist',
        'radios' => 'Radyo',
        'genres' => 'Tür',
        'sectors' => 'Sektör',
    ];

    /**
     * Mood keywords mapping
     */
    private const MOOD_KEYWORDS = [
        // Mutlu/Enerji
        'happy' => ['mutluyum', 'mutlu', 'neşeli', 'eğlenceli', 'enerji', 'dans', 'parti'],
        'energetic' => ['enerji', 'hızlı', 'tempolu', 'dinamik', 'aktif', 'coşkulu'],

        // Üzgün/Sakin
        'sad' => ['üzgün', 'üzgünüm', 'kederli', 'melankolik', 'hüzünlü'],
        'calm' => ['sakin', 'dingin', 'rahatlatıcı', 'huzurlu', 'sessiz'],

        // Romantik
        'romantic' => ['romantik', 'aşk', 'sevgi', 'sevgiliye', 'aşık'],

        // Çalışma/Odaklanma
        'focus' => ['çalışırken', 'çalışma', 'konsantrasyon', 'odaklanma', 'okurken'],

        // Spor/Motivasyon
        'workout' => ['spor', 'koşu', 'antrenman', 'fitness', 'motivasyon'],
    ];

    /**
     * Genre synonyms (Türkçe/İngilizce eşleştirme)
     */
    private const GENRE_SYNONYMS = [
        'pop' => ['pop', 'pop müzik'],
        'rock' => ['rock', 'rock müzik', 'rock and roll'],
        'jazz' => ['jazz', 'caz'],
        'classical' => ['klasik', 'klasik müzik', 'classical'],
        'hip-hop' => ['hip hop', 'hip-hop', 'rap'],
        'electronic' => ['elektronik', 'electronic', 'edm', 'dance'],
        'country' => ['country', 'country müzik'],
        'r&b' => ['r&b', 'rnb', 'rhythm and blues'],
        'reggae' => ['reggae'],
        'blues' => ['blues'],
    ];

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Kullanıcı mesajından müzik ara
     *
     * @param string $userMessage Kullanıcı mesajı
     * @param int $limit Maksimum sonuç sayısı (varsayılan: 80)
     * @return array{
     *     songs?: Collection,
     *     albums?: Collection,
     *     artists?: Collection,
     *     playlists?: Collection,
     *     radios?: Collection,
     *     genres?: Collection,
     *     sectors?: Collection,
     *     total_found: int,
     *     showing: int,
     *     detected_category?: string,
     *     detected_mood?: string
     * }
     */
    public function search(string $userMessage, int $limit = 80): array
    {
        $startTime = microtime(true);

        Log::info('🎵 Tenant1001 Music Search Started', [
            'user_message' => mb_substr($userMessage, 0, 100),
            'limit' => $limit
        ]);

        $results = [];
        $totalFound = 0;

        // 1. Kategori algıla (song, album, artist, vb.)
        $detectedCategory = $this->detectCategory($userMessage);

        // 2. Mood algıla (happy, sad, romantic, vb.)
        $detectedMood = $this->detectMood($userMessage);

        // 3. Genre algıla (pop, rock, jazz, vb.)
        $detectedGenre = $this->detectGenre($userMessage);

        // ✅ 4. Anahtar kelime çıkar (Meilisearch için)
        $searchKeywords = $this->extractKeywords($userMessage, $detectedGenre, $detectedMood);

        Log::info('🔍 Search keywords extracted', [
            'original' => $userMessage,
            'keywords' => $searchKeywords,
            'detected_genre' => $detectedGenre,
            'detected_mood' => $detectedMood,
            'detected_category' => $detectedCategory,
        ]);

        // 5. Arama yap (kategori bazlı)
        if ($detectedCategory) {
            // Spesifik kategori araması
            $categoryResults = $this->searchByCategory($detectedCategory, $searchKeywords, $limit);
            $results = [
                'songs' => $detectedCategory === 'songs' ? $categoryResults : collect(),
                'albums' => $detectedCategory === 'albums' ? $categoryResults : collect(),
                'artists' => $detectedCategory === 'artists' ? $categoryResults : collect(),
                'playlists' => $detectedCategory === 'playlists' ? $categoryResults : collect(),
                'radios' => $detectedCategory === 'radios' ? $categoryResults : collect(),
                'genres' => $detectedCategory === 'genres' ? $categoryResults : collect(),
                'sectors' => $detectedCategory === 'sectors' ? $categoryResults : collect(),
            ];
            $totalFound = $categoryResults->count();
        } else {
            // Genel arama (tüm kategorilerde)
            $results = $this->searchAll($searchKeywords, $limit);
            $totalFound = array_sum(array_map(fn($r) => $r->count(), $results));
        }

        // 6. Mood filtresi uygula (varsa)
        if ($detectedMood && isset($results['songs'])) {
            $results['songs'] = $this->filterByMood($results['songs'], $detectedMood);
        }

        // 7. Genre filtresi uygula (varsa)
        if ($detectedGenre && isset($results['songs'])) {
            $results['songs'] = $this->filterByGenre($results['songs'], $detectedGenre);
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('✅ Tenant1001 Music Search Completed', [
            'total_found' => $totalFound,
            'showing' => min($totalFound, $limit),
            'execution_time_ms' => $executionTime,
            'detected_category' => $detectedCategory,
            'detected_mood' => $detectedMood,
            'detected_genre' => $detectedGenre,
        ]);

        return array_merge($results, [
            'total_found' => $totalFound,
            'showing' => min($totalFound, $limit),
            'detected_category' => $detectedCategory,
            'detected_mood' => $detectedMood,
            'detected_genre' => $detectedGenre,
        ]);
    }

    /**
     * Tüm kategorilerde ara
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    protected function searchAll(string $query, int $limit): array
    {
        Log::info("🔍 searchAll called (Meilisearch - ALL CATEGORIES)", [
            'query' => $query,
            'limit' => $limit
        ]);

        // 🎵 HER ŞEYİ ARA: Songs, Albums, Artists, Playlists, Radios, Genres, Sectors
        return [
            'songs' => $this->searchSongs($query, $limit),
            'albums' => $this->searchAlbums($query, min($limit, 20)),
            'artists' => $this->searchArtists($query, min($limit, 20)),
            'playlists' => $this->searchPlaylists($query, min($limit, 20)),
            'radios' => $this->searchRadios($query, min($limit, 10)),
            'genres' => $this->searchGenres($query, min($limit, 10)),
            'sectors' => $this->searchSectors($query, min($limit, 10)),
        ];
    }

    /**
     * Belirli bir kategoride ara
     *
     * @param string $category
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    protected function searchByCategory(string $category, string $query, int $limit): Collection
    {
        Log::info("🔍 Searching in category: {$category}", [
            'query' => $query,
            'limit' => $limit
        ]);

        // ✅ Kategori bazında arama yap
        return match($category) {
            'songs' => $this->searchSongs($query, $limit),
            'albums' => $this->searchAlbums($query, $limit),
            'artists' => $this->searchArtists($query, $limit),
            'playlists' => $this->searchPlaylists($query, $limit),
            'radios' => $this->searchRadios($query, $limit),
            'genres' => $this->searchGenres($query, $limit),
            'sectors' => $this->searchSectors($query, $limit),
            default => collect(),
        };
    }

    /**
     * Kategori algıla (song, album, artist, vb.)
     *
     * @param string $message
     * @return string|null
     */
    protected function detectCategory(string $message): ?string
    {
        $lowerMessage = mb_strtolower($message);

        // ✅ FIX: "playlist istiyorum/öner/hazırla" → Şarkı araması isteniyor, playlist araması DEĞİL!
        // Playlist oluşturma fiilleri varsa → Kategori algılama (tüm şarkıları ara)
        $playlistCreationVerbs = ['istiyorum', 'ister', 'öner', 'hazırla', 'oluştur', 'yap', 'çıkar'];
        $hasPlaylistKeyword = str_contains($lowerMessage, 'playlist') || str_contains($lowerMessage, 'liste');
        $hasCreationVerb = false;

        foreach ($playlistCreationVerbs as $verb) {
            if (str_contains($lowerMessage, $verb)) {
                $hasCreationVerb = true;
                break;
            }
        }

        // "playlist istiyorum" gibi → Şarkı önerisi istiyor, playlist araması değil
        if ($hasPlaylistKeyword && $hasCreationVerb) {
            Log::info("🎵 Playlist CREATION detected (not search) - will search songs", [
                'message' => mb_substr($message, 0, 100)
            ]);
            return null; // Kategori yok → Tüm şarkılarda ara
        }

        $categoryMap = [
            'songs' => ['şarkı', 'parça', 'song', 'track', 'müzik'],
            'albums' => ['albüm', 'album'],
            'artists' => ['sanatçı', 'şarkıcı', 'artist', 'singer', 'müzisyen'],
            'playlists' => ['playlist', 'çalma listesi'], // "liste" kaldırıldı (çok genel)
            'radios' => ['radyo', 'radio'],
        ];

        foreach ($categoryMap as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    Log::info("🎯 Category detected: {$category}", ['keyword' => $keyword]);
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Mood algıla (happy, sad, romantic, vb.)
     *
     * @param string $message
     * @return string|null
     */
    protected function detectMood(string $message): ?string
    {
        $lowerMessage = mb_strtolower($message);

        foreach (self::MOOD_KEYWORDS as $mood => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    Log::info("😊 Mood detected: {$mood}", ['keyword' => $keyword]);
                    return $mood;
                }
            }
        }

        return null;
    }

    /**
     * Genre algıla (pop, rock, jazz, vb.)
     *
     * @param string $message
     * @return string|null
     */
    protected function detectGenre(string $message): ?string
    {
        $lowerMessage = mb_strtolower($message);

        foreach (self::GENRE_SYNONYMS as $genre => $synonyms) {
            foreach ($synonyms as $synonym) {
                if (str_contains($lowerMessage, $synonym)) {
                    Log::info("🎸 Genre detected: {$genre}", ['synonym' => $synonym]);
                    return $genre;
                }
            }
        }

        return null;
    }

    /**
     * Kullanıcı mesajından anahtar kelimeleri çıkar (Meilisearch için)
     *
     * @param string $message Kullanıcı mesajı
     * @param string|null $detectedGenre Algılanan genre
     * @param string|null $detectedMood Algılanan mood
     * @return string Meilisearch için optimize edilmiş anahtar kelimeler
     */
    protected function extractKeywords(string $message, ?string $detectedGenre, ?string $detectedMood): string
    {
        $lowerMessage = mb_strtolower($message);

        // ✅ 1. Gürültü kelimelerini çıkar (stopwords)
        $stopwords = [
            'playlist', 'oluştur', 'yap', 'hazırla', 'istiyorum', 'istiyoruz', 'lütfen',
            'şarkı', 'şarkılık', 'parça', 'müzik', 'listesi', 'çalma',
            'kaç', 'tane', 'adet', 'bir', 'iki', 'üç', 'dört', 'beş',
            'on', 'yirmi', 'otuz', 'elli', 'yüz', 'bin',
            've', 'ile', 'için', 'den', 'dan', 'ten', 'tan',
        ];

        $keywords = [];

        // ✅ 2. Genre varsa ekle
        if ($detectedGenre) {
            $keywords[] = $detectedGenre;
        }

        // ✅ 3. Mood varsa ekle
        if ($detectedMood) {
            $keywords[] = $detectedMood;
        }

        // ✅ 4. Mesajdaki anlamlı kelimeleri bul
        $words = preg_split('/\s+/', $lowerMessage);
        foreach ($words as $word) {
            // Temizle (noktalama işaretleri)
            $word = trim($word, '.,!?;:()[]{}"\'-');

            // Stopword değilse ve en az 3 karakter ise ekle
            if (strlen($word) >= 3 && !in_array($word, $stopwords) && !is_numeric($word)) {
                $keywords[] = $word;
            }
        }

        // ✅ 5. Eğer hiç keyword yoksa (çok genel sorgu), tüm mesajı döndür
        if (empty($keywords)) {
            return $message;
        }

        // ✅ 6. Keyword'leri birleştir (Meilisearch multi-word araması için)
        return implode(' ', array_unique($keywords));
    }

    /**
     * Mood'a göre filtrele
     *
     * @param Collection $songs
     * @param string $mood
     * @return Collection
     */
    protected function filterByMood(Collection $songs, string $mood): Collection
    {
        // Şimdilik tüm şarkıları döndür
        // İleride mood metadata'sı eklendiğinde filtre uygulanacak
        Log::info("🎭 Filtering by mood: {$mood}");
        return $songs;
    }

    /**
     * Genre'ye göre filtrele
     *
     * @param Collection $songs
     * @param string $genre
     * @return Collection
     */
    protected function filterByGenre(Collection $songs, string $genre): Collection
    {
        // Şimdilik tüm şarkıları döndür
        // İleride genre metadata'sı eklendiğinde filtre uygulanacak
        Log::info("🎸 Filtering by genre: {$genre}");
        return $songs;
    }

    /**
     * Şarkıları AI context formatında formatla
     *
     * @param Collection $songs
     * @param int $limit
     * @return string
     */
    public function formatSongsForContext(Collection $songs, int $limit = 80): string
    {
        $context = "**MEVCUT ŞARKILAR:**\n\n";

        $count = 0;
        foreach ($songs->take($limit) as $song) {
            $count++;
            // ✅ title array olduğu için getTranslated kullan + UTF-8 temizleme (aggressive)
            $title = $this->cleanUtf8($song->getTranslated('title', 'tr'));
            $artistName = $song->artist ? $this->cleanUtf8($song->artist->getTranslated('title', 'tr')) : 'Bilinmeyen';
            $albumName = $song->album ? $this->cleanUtf8($song->album->getTranslated('title', 'tr')) : 'Bilinmeyen';
            $genreName = $song->genre ? $this->cleanUtf8($song->genre->getTranslated('title', 'tr')) : 'Bilinmeyen';

            // Şarkı play linki
            $playUrl = url("/play/song/{$song->song_id}");

            $context .= "### {$count}. {$title}\n";
            $context .= "- Sanatçı: {$artistName}\n";
            $context .= "- Albüm: {$albumName}\n";
            $context .= "- Süre: {$song->duration} saniye\n";
            $context .= "- Tür: {$genreName}\n";
            $context .= "- Song ID: {$song->song_id}\n";
            $context .= "- **▶️ Çal:** {$playUrl}\n";
            $context .= "\n";
        }

        if ($songs->count() > $limit) {
            $context .= "\n**Not:** Toplam {$songs->count()} şarkı bulundu, ilk {$limit} tanesi gösteriliyor.\n";
        }

        return $context;
    }

    /**
     * Playlist'leri AI context formatında formatla
     *
     * @param Collection $playlists
     * @return string
     */
    public function formatPlaylistsForContext(Collection $playlists): string
    {
        $context = "\n**MEVCUT PLAYLIST'LER:**\n\n";

        foreach ($playlists as $playlist) {
            $title = $playlist->getTranslated('title', 'tr');
            $description = $playlist->getTranslated('description', 'tr');

            // Slug JSON ise dinamik locale al
            $slug = is_array($playlist->slug) ? $this->getLocaleValue($playlist->slug, 'playlist') : $playlist->slug;

            // Playlist URL oluştur
            $playlistUrl = url("/playlist/{$slug}");

            $context .= "### {$title}\n";
            if (!empty($description)) {
                $context .= "- Açıklama: {$description}\n";
            }
            $context .= "- Playlist ID: {$playlist->playlist_id}\n";
            $context .= "- **🔗 Link:** {$playlistUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Albümleri AI context formatında formatla
     *
     * @param Collection $albums
     * @return string
     */
    public function formatAlbumsForContext(Collection $albums): string
    {
        $context = "\n**MEVCUT ALBÜMLER:**\n\n";

        foreach ($albums as $album) {
            $title = $album->getTranslated('title', 'tr');
            $artistName = $album->artist ? $album->artist->getTranslated('title', 'tr') : 'Bilinmeyen';
            $slug = is_array($album->slug) ? $this->getLocaleValue($album->slug, 'album') : $album->slug;

            // Albüm linki
            $albumUrl = url("/album/{$slug}");

            $context .= "### {$title}\n";
            $context .= "- Sanatçı: {$artistName}\n";
            $context .= "- Şarkı Sayısı: {$album->songs_count}\n";
            $context .= "- Album ID: {$album->album_id}\n";
            $context .= "- **🔗 Link:** {$albumUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Sanatçıları AI context formatında formatla
     *
     * @param Collection $artists
     * @return string
     */
    public function formatArtistsForContext(Collection $artists): string
    {
        $context = "\n**MEVCUT SANATÇILAR:**\n\n";

        foreach ($artists as $artist) {
            $title = $artist->getTranslated('title', 'tr');
            $slug = is_array($artist->slug) ? $this->getLocaleValue($artist->slug, 'artist') : $artist->slug;

            // Sanatçı linki
            $artistUrl = url("/artist/{$slug}");

            $context .= "### {$title}\n";
            $context .= "- Şarkı Sayısı: {$artist->songs_count}\n";
            $context .= "- Albüm Sayısı: {$artist->albums_count}\n";
            $context .= "- Artist ID: {$artist->artist_id}\n";
            $context .= "- **🔗 Link:** {$artistUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Radyoları AI context formatında formatla
     *
     * @param Collection $radios
     * @return string
     */
    public function formatRadiosForContext(Collection $radios): string
    {
        $context = "\n**MEVCUT RADYOLAR:**\n\n";

        foreach ($radios as $radio) {
            $title = $radio->getTranslated('title', 'tr');
            $description = $radio->getTranslated('description', 'tr');

            // Slug JSON ise dinamik locale al
            $slug = is_array($radio->slug) ? $this->getLocaleValue($radio->slug, 'radio') : $radio->slug;

            // Radio URL oluştur
            $radioUrl = url("/radio/{$slug}");

            $context .= "### {$title}\n";
            if (!empty($description)) {
                $context .= "- Açıklama: {$description}\n";
            }
            $context .= "- Radio ID: {$radio->radio_id}\n";
            $context .= "- **🔗 Link:** {$radioUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Türleri AI context formatında formatla
     *
     * @param Collection $genres
     * @return string
     */
    public function formatGenresForContext(Collection $genres): string
    {
        $context = "\n**MEVCUT TÜRLER:**\n\n";

        foreach ($genres as $genre) {
            $title = $genre->getTranslated('title', 'tr');
            $slug = is_array($genre->slug) ? $this->getLocaleValue($genre->slug, 'genre') : $genre->slug;

            // Tür linki
            $genreUrl = url("/genre/{$slug}");

            $context .= "### {$title}\n";
            $context .= "- Şarkı Sayısı: {$genre->songs_count}\n";
            $context .= "- Genre ID: {$genre->genre_id}\n";
            $context .= "- **🔗 Link:** {$genreUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Sektörleri AI context formatında formatla
     *
     * @param Collection $sectors
     * @return string
     */
    public function formatSectorsForContext(Collection $sectors): string
    {
        $context = "\n**MEVCUT SEKTÖRLER:**\n\n";

        foreach ($sectors as $sector) {
            $title = $sector->getTranslated('title', 'tr');
            $description = $sector->getTranslated('description', 'tr');
            $slug = is_array($sector->slug) ? $this->getLocaleValue($sector->slug, 'sector') : $sector->slug;

            // Sektör linki
            $sectorUrl = url("/sector/{$slug}");

            $context .= "### {$title}\n";
            $context .= "- Açıklama: {$description}\n";
            $context .= "- Sector ID: {$sector->sector_id}\n";
            $context .= "- **🔗 Link:** {$sectorUrl}\n";
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Action button'ları oluştur
     *
     * @param string $type 'song', 'album', 'artist', 'playlist'
     * @param int $id
     * @param bool $isPremium
     * @return string
     */
    public static function generateActionButtons(string $type, int $id, bool $isPremium): string
    {
        $buttons = [];

        if ($isPremium) {
            // Premium kullanıcı - Tüm özellikler
            $buttons[] = "[Dinle](/play/{$type}/{$id})";
            $buttons[] = "[Favorilere Ekle](/favorite/add/{$type}/{$id})";
            $buttons[] = "[Playlist'e Ekle](/playlist/add/{$type}/{$id})";

            if ($type === 'song') {
                $buttons[] = "[Radyo Başlat](/radio/start/{$id})";
            }
        } else {
            // Free/Guest kullanıcı - Kısıtlı özellikler
            $buttons[] = "[Dinle (Reklamlı)](/play/{$type}/{$id})";
            $buttons[] = "[Premium'a Geç](/pricing)";
        }

        return implode(' ', $buttons);
    }

    /**
     * Mood bazlı playlist önerisi
     *
     * @param string $mood
     * @return array
     */
    public static function suggestPlaylistsByMood(string $mood): array
    {
        $suggestions = [
            'happy' => ['90\'lar Nostalji', 'Parti Mix', 'Enerji Bombası'],
            'sad' => ['Hüzünlü Akşamlar', 'Yalnızlık Şarkıları', 'Melankolik'],
            'romantic' => ['Aşk Şarkıları', 'Romantik Geceler', 'Sevgiliye Özel'],
            'calm' => ['Sakin Akşamlar', 'Relax Mode', 'Huzur Müziği'],
            'focus' => ['Çalışma Müziği', 'Focus Mix', 'Instrumental'],
            'workout' => ['Spor Motivasyon', 'Koşu Müziği', 'Gym Mix'],
        ];

        return $suggestions[$mood] ?? [];
    }

    /**
     * AI context için sonuçları formatla
     *
     * @param array $searchResults
     * @return string
     */
    public function buildContextForAI(array $searchResults): string
    {
        $context = "";

        // 🎵 Şarkılar varsa
        if (isset($searchResults['songs']) && $searchResults['songs']->isNotEmpty()) {
            $context .= $this->formatSongsForContext($searchResults['songs'], $searchResults['showing'] ?? 80);
        }

        // 💿 Albümler varsa
        if (isset($searchResults['albums']) && $searchResults['albums']->isNotEmpty()) {
            $context .= $this->formatAlbumsForContext($searchResults['albums']);
        }

        // 🎤 Sanatçılar varsa
        if (isset($searchResults['artists']) && $searchResults['artists']->isNotEmpty()) {
            $context .= $this->formatArtistsForContext($searchResults['artists']);
        }

        // 📋 Playlistler varsa
        if (isset($searchResults['playlists']) && $searchResults['playlists']->isNotEmpty()) {
            $context .= $this->formatPlaylistsForContext($searchResults['playlists']);
        }

        // 📻 Radyolar varsa
        if (isset($searchResults['radios']) && $searchResults['radios']->isNotEmpty()) {
            $context .= $this->formatRadiosForContext($searchResults['radios']);
        }

        // 🎸 Türler varsa
        if (isset($searchResults['genres']) && $searchResults['genres']->isNotEmpty()) {
            $context .= $this->formatGenresForContext($searchResults['genres']);
        }

        // 🏢 Sektörler varsa
        if (isset($searchResults['sectors']) && $searchResults['sectors']->isNotEmpty()) {
            $context .= $this->formatSectorsForContext($searchResults['sectors']);
        }

        // Metadata ekle
        if ($searchResults['total_found'] ?? 0 > 0) {
            $context .= "\n**TOPLAM SONUÇ:** {$searchResults['total_found']}\n";
            $context .= "**GÖSTERILEN:** {$searchResults['showing']}\n";
        }

        // 🎼 PLAYLIST OLUŞTURMA: Hiç sonuç yoksa MUTLAKA mevcut türleri göster
        $totalFound = $searchResults['total_found'] ?? 0;
        $hasSongs = isset($searchResults['songs']) && $searchResults['songs']->isNotEmpty();

        // 🎼 MEVCUT TÜRLERİ HER ZAMAN GÖSTER (Kullanıcı seçebilsin)
        $context .= $this->getAvailableGenresContext();

        // 💳 Subscription/Pricing bilgilerini ekle (her zaman)
        $context .= $this->getSubscriptionContext();

        // 👤 Kullanıcı bilgilerini ekle (authenticated user için)
        $context .= $this->getUserSubscriptionContext();

        // 📚 Bilgi Bankası (Knowledge Base) bilgilerini ekle
        $context .= $this->getKnowledgeBaseContext();

        // ✅ FINAL UTF-8 CLEANUP: Tüm context'i temizle
        return $this->cleanUtf8($context);
    }

    /**
     * Subscription/Pricing bilgilerini AI context olarak formatla
     *
     * @return string
     */
    protected function getSubscriptionContext(): string
    {
        try {
            // ✅ TENANT DATABASE'den çek (Muzibu'nun kendi planları)
            $plans = \DB::connection('tenant')
                ->table('subscription_plans')
                ->where('is_public', 1)
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->orderBy('sort_order')
                ->get(['subscription_plan_id', 'title', 'description', 'billing_cycles', 'trial_days', 'currency']);

            if ($plans->isEmpty()) {
                return "";
            }

            $context = "\n\n**💳 ÜYELİK PLANLARI VE FİYATLAR:**\n\n";

            foreach ($plans as $plan) {
                $title = json_decode($plan->title ?: '{}', true);
                $description = json_decode($plan->description ?: '{}', true);
                $billingCycles = json_decode($plan->billing_cycles ?: '{}', true);

                $planTitle = $this->getLocaleValue($title, 'Bilinmeyen Plan');
                $planDesc = $this->getLocaleValue($description, '');

                $context .= "### {$planTitle}\n";
                if (!empty($planDesc)) {
                    $context .= "- Açıklama: {$planDesc}\n";
                }

                // Billing cycles varsa
                if (!empty($billingCycles)) {
                    $context .= "- Fiyatlandırma:\n";
                    foreach ($billingCycles as $cycleKey => $cycle) {
                        $label = $cycle['label']['tr'] ?? $cycle['name']['tr'] ?? $cycleKey;
                        $price = $cycle['price'] ?? 0;
                        $durationDays = $cycle['duration_days'] ?? 0;
                        $trialDays = $cycle['trial_days'] ?? 0;

                        // Price formatı (0 ise "Ücretsiz")
                        if ($price == 0) {
                            $context .= "  • {$label}: Ücretsiz";
                        } else {
                            $context .= "  • {$label}: {$price} {$plan->currency}";
                        }

                        if ($durationDays > 0) {
                            $context .= " ({$durationDays} gün)";
                        }
                        if ($trialDays > 0) {
                            $context .= " - {$trialDays} gün ücretsiz deneme";
                        }
                        $context .= "\n";
                    }
                } else {
                    $context .= "- Fiyat bilgisi için iletişime geçin\n";
                }

                $context .= "\n";
            }

            // ✅ Subscription sayfası linki ekle
            $subscriptionUrl = url('/subscription/plans');
            $context .= "**📌 Üyelik Satın Al/Uzat:** {$subscriptionUrl}\n";
            $context .= "**NOT:** Üyelik planları hakkında detaylı bilgi için üyelik sayfasını ziyaret edebilirsiniz.\n";

            return $context;

        } catch (\Exception $e) {
            \Log::error('Subscription context error', ['error' => $e->getMessage()]);
            return "";
        }
    }

    /**
     * Kullanıcının kişisel subscription bilgilerini AI context olarak formatla
     * (Sadece authenticated user için)
     *
     * @return string
     */
    protected function getUserSubscriptionContext(): string
    {
        try {
            // ✅ Kullanıcı giriş yapmış mı kontrol et
            $user = auth()->user();

            if (!$user) {
                return ""; // Guest user → Kişisel bilgi yok
            }

            $context = "\n\n**👤 KULLANICI BİLGİLERİ:**\n\n";

            // 📝 Ad Soyad
            $firstName = $user->name ?? '';
            $lastName = $user->surname ?? '';
            $fullName = trim("{$firstName} {$lastName}");

            if (!empty($fullName)) {
                $context .= "- **Ad:** {$firstName}\n";
                if (!empty($lastName)) {
                    $context .= "- **Soyad:** {$lastName}\n";
                }
            }

            // 📧 Email
            if (!empty($user->email)) {
                $context .= "- **Email:** {$user->email}\n";
            }

            // 📱 Telefon
            if (!empty($user->phone)) {
                $context .= "- **Telefon:** {$user->phone}\n";
            }

            // 💳 Aktif Subscription Bilgileri (Central DB'den çek)
            $activeSubscription = \DB::connection('mysql')
                ->table('subscriptions')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('current_period_end', '>', now())
                ->orderBy('current_period_end', 'desc')
                ->first(['subscription_plan_id', 'current_period_end', 'status']);

            if ($activeSubscription) {
                // Plan adını çek (Tenant DB'den)
                $plan = \DB::connection('tenant')
                    ->table('subscription_plans')
                    ->where('subscription_plan_id', $activeSubscription->subscription_plan_id)
                    ->first(['title']);

                $planTitle = 'Bilinmeyen Plan';
                if ($plan && $plan->title) {
                    $titleJson = json_decode($plan->title, true);
                    $planTitle = $this->getLocaleValue($titleJson, 'Bilinmeyen Plan');
                }

                // Kalan gün hesapla
                $endDate = \Carbon\Carbon::parse($activeSubscription->current_period_end);
                $remainingDays = (int) now()->diffInDays($endDate, false); // Tam sayı

                $context .= "- **Aktif Üyelik:** {$planTitle}\n";
                $context .= "- **Bitiş Tarihi:** {$endDate->format('d.m.Y')}\n";

                if ($remainingDays > 0) {
                    $context .= "- **Kalan Süre:** {$remainingDays} gün\n";
                } elseif ($remainingDays < 0) {
                    $context .= "- **UYARI:** Üyeliğiniz " . abs($remainingDays) . " gün önce sona ermiş!\n";
                } else {
                    $context .= "- **UYARI:** Üyeliğiniz bugün sona eriyor!\n";
                }
            } else {
                $context .= "- **Aktif Üyelik:** Yok (Ücretsiz Kullanıcı)\n";
            }

            // 🎵 MÜZİK ALIŞKANLIKLARI
            $context .= "\n**🎵 MÜZİK ALIŞKANLIKLARI:**\n\n";

            // Son Çalınan Şarkı
            if (!empty($user->last_played_song_id)) {
                $lastSong = \DB::connection('tenant')
                    ->table('muzibu_songs')
                    ->where('song_id', $user->last_played_song_id)
                    ->first(['song_id', 'title']);

                if ($lastSong) {
                    $songTitle = json_decode($lastSong->title, true);
                    $songTitleTr = $this->getLocaleValue($songTitle, 'Bilinmeyen');
                    $songUrl = url("/play/song/{$lastSong->song_id}");
                    $context .= "- **Son Çalınan:** {$songTitleTr} (▶️ {$songUrl})\n";
                }
            }

            // En Çok Dinlenen Şarkılar (Top 5)
            $topSongs = \DB::connection('tenant')
                ->table('muzibu_song_plays')
                ->select('song_id', \DB::raw('COUNT(*) as play_count'))
                ->where('user_id', $user->id)
                ->groupBy('song_id')
                ->orderBy('play_count', 'desc')
                ->limit(5)
                ->get();

            if ($topSongs->count() > 0) {
                $context .= "- **En Çok Dinlediğin Şarkılar:**\n";
                foreach ($topSongs as $topSong) {
                    $song = \DB::connection('tenant')
                        ->table('muzibu_songs')
                        ->where('song_id', $topSong->song_id)
                        ->first(['song_id', 'title']);

                    if ($song) {
                        $songTitle = json_decode($song->title, true);
                        $songTitleTr = $this->getLocaleValue($songTitle, 'Bilinmeyen');
                        $songUrl = url("/play/song/{$song->song_id}");
                        $context .= "  • {$songTitleTr} ({$topSong->play_count} kez, ▶️ {$songUrl})\n";
                    }
                }
            }

            // Son Dinlenenler (Son 5)
            $recentPlays = \DB::connection('tenant')
                ->table('muzibu_song_plays')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['song_id']);

            if ($recentPlays->count() > 0) {
                $context .= "- **Son Dinlediklerin:**\n";
                foreach ($recentPlays as $recentPlay) {
                    $song = \DB::connection('tenant')
                        ->table('muzibu_songs')
                        ->where('song_id', $recentPlay->song_id)
                        ->first(['song_id', 'title']);

                    if ($song) {
                        $songTitle = json_decode($song->title, true);
                        $songTitleTr = $this->getLocaleValue($songTitle, 'Bilinmeyen');
                        $songUrl = url("/play/song/{$song->song_id}");
                        $context .= "  • {$songTitleTr} (▶️ {$songUrl})\n";
                    }
                }
            }

            // 📊 Son 7 Günde Dinleme İstatistikleri
            $last7DaysPlays = \DB::connection('tenant')
                ->table('muzibu_song_plays')
                ->where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            if ($last7DaysPlays > 0) {
                $context .= "- **Son 7 Günde Dinleme:** {$last7DaysPlays} şarkı\n";

                // Son 7 günde en çok dinlenenler
                $last7DaysTopSongs = \DB::connection('tenant')
                    ->table('muzibu_song_plays')
                    ->select('song_id', \DB::raw('COUNT(*) as play_count'))
                    ->where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('song_id')
                    ->orderBy('play_count', 'desc')
                    ->limit(3)
                    ->get();

                if ($last7DaysTopSongs->count() > 0) {
                    $context .= "- **Son 7 Günde En Çok Dinlenenler:**\n";
                    foreach ($last7DaysTopSongs as $topSong) {
                        $song = \DB::connection('tenant')
                            ->table('muzibu_songs')
                            ->where('song_id', $topSong->song_id)
                            ->first(['song_id', 'title']);

                        if ($song) {
                            $songTitle = json_decode($song->title, true);
                            $songTitleTr = $this->getLocaleValue($songTitle, 'Bilinmeyen');
                            $context .= "  • {$songTitleTr} ({$topSong->play_count} kez)\n";
                        }
                    }
                }
            }

            // 🎭 En Sevdiğin Türler (Dinleme geçmişine göre)
            $favoriteGenres = \DB::connection('tenant')
                ->table('muzibu_song_plays')
                ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
                ->join('muzibu_genre_song', 'muzibu_songs.song_id', '=', 'muzibu_genre_song.song_id')
                ->join('muzibu_genres', 'muzibu_genre_song.genre_id', '=', 'muzibu_genres.genre_id')
                ->select('muzibu_genres.genre_id', 'muzibu_genres.title', \DB::raw('COUNT(*) as play_count'))
                ->where('muzibu_song_plays.user_id', $user->id)
                ->groupBy('muzibu_genres.genre_id', 'muzibu_genres.title')
                ->orderBy('play_count', 'desc')
                ->limit(5)
                ->get();

            if ($favoriteGenres->count() > 0) {
                $context .= "- **En Sevdiğin Türler:**\n";
                foreach ($favoriteGenres as $genre) {
                    $genreTitle = json_decode($genre->title, true);
                    $genreTitleTr = $this->getLocaleValue($genreTitle, 'Bilinmeyen');
                    $context .= "  • {$genreTitleTr} ({$genre->play_count} şarkı dinledin)\n";
                }
            }

            // ✅ AI'ya özel talimat: Kullanıcıya ismiyle hitap et
            if (!empty($firstName)) {
                $context .= "\n**📌 ÖNEMLİ TALİMAT:** Kullanıcıya yanıt verirken SADECE '{$firstName}' adını kullan (soyad kullanma!). Çok samimi, güler yüzlü, esprili ve neşeli bir dil kullan. Sanki yakın bir arkadaşınmış gibi konuş. Emoji kullanabilirsin. Sıcak ve içten ol. Kullanıcının dinleme alışkanlıklarına göre kişiselleştirilmiş öneriler yap.\n";
            }

            return $context;

        } catch (\Exception $e) {
            \Log::error('User subscription context error', ['error' => $e->getMessage()]);
            return "";
        }
    }

    /**
     * Bilgi Bankası (Knowledge Base) context'i formatla
     * Muzibu hakkında sık sorulan sorular ve yanıtları
     *
     * @return string
     */
    protected function getKnowledgeBaseContext(): string
    {
        try {
            // Tenant database'den aktif bilgi bankası kayıtlarını al
            $knowledgeBase = \DB::connection('tenant')
                ->table('tenant_knowledge_base')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get(['category', 'question', 'answer']);

            if ($knowledgeBase->isEmpty()) {
                return "";
            }

            $context = "\n\n**📚 MUZİBU HAKKINDA BİLGİLER (Bilgi Bankası):**\n\n";

            $currentCategory = null;
            foreach ($knowledgeBase as $item) {
                // Kategori başlığı (değiştiyse)
                if ($currentCategory !== $item->category) {
                    $currentCategory = $item->category;
                    $context .= "\n### {$currentCategory}\n\n";
                }

                $context .= "**Soru:** {$item->question}\n";
                $context .= "**Yanıt:** {$item->answer}\n\n";
            }

            return $context;

        } catch (\Exception $e) {
            \Log::error('Knowledge base context error', [
                'error' => $e->getMessage(),
            ]);
            return "";
        }
    }

    /**
     * Mevcut müzik türlerini AI context olarak formatla
     * Kullanıcı playlist oluşturmak istediğinde türleri görebilsin
     *
     * @return string
     */
    protected function getAvailableGenresContext(): string
    {
        try {
            // Tenant database'den aktif türleri al
            $genres = \DB::connection('tenant')
                ->table('muzibu_genres')
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['genre_id', 'title', 'slug']);

            if ($genres->isEmpty()) {
                return "";
            }

            $context = "\n\n**🎼 MEVCUT MÜZİK TÜRLERİ (Playlist oluşturmak için seçebilirsiniz):**\n\n";

            foreach ($genres as $genre) {
                $title = json_decode($genre->title ?: '{}', true);
                $genreTitle = $this->getLocaleValue($title, 'Bilinmeyen');

                // Slug JSON ise dinamik locale al
                $slug = is_array($genre->slug) ? $this->getLocaleValue($genre->slug, 'genre') : $genre->slug;

                // Tür detay linki
                $genreUrl = url("/genre/{$slug}");

                $context .= "- **{$genreTitle}** (Tür ID: {$genre->genre_id}) - [Şarkıları Gör]({$genreUrl})\n";
            }

            $context .= "\n**💡 Playlist oluşturmak için:** 'Rock türünden playlist oluştur' veya 'Tür 1 ve 3'ten karışık playlist yap' diyebilirsiniz.\n";

            return $context;

        } catch (\Exception $e) {
            \Log::error('Available genres context error', ['error' => $e->getMessage()]);
            return "";
        }
    }

    /**
     * Şarkı ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchSongs(string $query, int $limit = 50): Collection
    {
        // ✅ ZORLA TENANT 1001 INITIALIZE ET!
        if (!tenant() || tenant()->id !== 1001) {
            $tenant1001 = \App\Models\Tenant::find(1001);
            if ($tenant1001) {
                tenancy()->initialize($tenant1001);
                Log::info("🔧 FORCED tenant 1001 initialization");
            }
        }

        // ✅ DEBUG: Tenant context kontrol
        $tenantId = tenant() ? tenant()->id : 'NULL';
        $tenantCentral = tenant() ? (tenant()->central ?? 'no_central_field') : 'NULL';

        Log::info("🎵 searchSongs called (Meilisearch)", [
            'query' => $query,
            'limit' => $limit,
            'tenant_id' => $tenantId,
            'tenant_central' => $tenantCentral,
        ]);

        // ✅ Query boşsa (genel sorgu) → Rastgele şarkılar getir
        if (empty(trim($query))) {
            Log::info("⚠️ Empty query detected, fetching random active songs");

            $songs = Song::where('is_active', true)
                ->inRandomOrder()
                ->take($limit)
                ->get()
                ->load(['album', 'artist', 'genre']);

            Log::info("✅ Random songs fetched: {$songs->count()} songs");

            return $songs;
        }

        // ✅ DEBUG: Song model index name kontrol
        $songModel = new Song();
        $indexName = $songModel->searchableAs();
        Log::info("🔍 Scout Index Name", [
            'index_name' => $indexName,
            'query' => $query,
        ]);

        // 🔍 Meilisearch kullan (Laravel Scout) - is_active filter KALDIR (debug için)
        $songs = Song::search($query)
            //->query(fn($builder) => $builder->where('is_active', true)) // DEBUG: Geçici kaldır
            ->take($limit)
            ->get()
            ->load(['album', 'artist', 'genre']); // ✅ sector removed - Song doesn't have direct sector relationship

        // ✅ Meilisearch 0 sonuç döndürdüyse → Rastgele şarkılar getir
        if ($songs->isEmpty()) {
            Log::info("⚠️ Meilisearch returned 0 results, fetching random active songs", [
                'query' => $query,
            ]);

            $songs = Song::where('is_active', true)
                ->inRandomOrder()
                ->take($limit)
                ->get()
                ->load(['album', 'artist', 'genre']);

            Log::info("✅ Random songs fetched as fallback: {$songs->count()} songs");
        } else {
            Log::info("✅ searchSongs found (Meilisearch): {$songs->count()} songs", [
                'query' => $query,
                'count' => $songs->count(),
                'first_song' => $songs->first() ? $this->getLocaleValue($songs->first()->title, 'NO TITLE') : 'NO SONGS',
            ]);
        }

        return $songs;
    }

    /**
     * Albüm ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchAlbums(string $query, int $limit = 50): Collection
    {
        Log::info("💿 searchAlbums called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $albums = Album::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->load(['artist', 'songs'])
            ->loadCount('songs');

        Log::info("✅ searchAlbums found (Meilisearch): {$albums->count()} albums");
        return $albums;
    }

    /**
     * Playlist ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchPlaylists(string $query, int $limit = 50): Collection
    {
        Log::info("📝 searchPlaylists called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $playlists = Playlist::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->load(['sectors']) // ✅ FIXED: Playlist doesn't have genre relationship
            ->loadCount('songs');

        Log::info("✅ searchPlaylists found (Meilisearch): {$playlists->count()} playlists");
        return $playlists;
    }

    /**
     * Sanatçı ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchArtists(string $query, int $limit = 50): Collection
    {
        Log::info("🎤 searchArtists called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $artists = Artist::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->loadCount(['songs', 'albums']);

        Log::info("✅ searchArtists found (Meilisearch): {$artists->count()} artists");
        return $artists;
    }

    /**
     * Radyo ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchRadios(string $query, int $limit = 50): Collection
    {
        Log::info("📻 searchRadios called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $radios = Radio::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->load(['sectors']); // ✅ FIXED: Radio doesn't have genre relationship

        Log::info("✅ searchRadios found (Meilisearch): {$radios->count()} radios");
        return $radios;
    }

    /**
     * Tür ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchGenres(string $query, int $limit = 50): Collection
    {
        Log::info("🎸 searchGenres called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $genres = Genre::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->loadCount(['songs']); // ✅ albums removed - Genre doesn't have albums relationship

        Log::info("✅ searchGenres found (Meilisearch): {$genres->count()} genres");
        return $genres;
    }

    /**
     * Sektör ara
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchSectors(string $query, int $limit = 50): Collection
    {
        Log::info("🏢 searchSectors called (Meilisearch)", ['query' => $query, 'limit' => $limit]);

        if (empty(trim($query))) {
            return collect();
        }

        // 🔍 Meilisearch kullan (Laravel Scout)
        $sectors = Sector::search($query)
            ->query(fn($builder) => $builder->where('is_active', true))
            ->take($limit)
            ->get()
            ->loadCount(['songs', 'albums']);

        Log::info("✅ searchSectors found (Meilisearch): {$sectors->count()} sectors");
        return $sectors;
    }

    /**
     * Genre'ye göre içerik getir
     *
     * @param int $genreId
     * @param int $limit
     * @return Collection
     */
    public function searchByGenre(int $genreId, int $limit = 50): Collection
    {
        // TODO: Database entegrasyonu
        Log::info("🎸 searchByGenre called", ['genre_id' => $genreId, 'limit' => $limit]);
        return collect();
    }

    /**
     * Sektöre göre playlist getir
     *
     * @param int $sectorId
     * @param int $limit
     * @return Collection
     */
    public function searchBySector(int $sectorId, int $limit = 50): Collection
    {
        // TODO: Database entegrasyonu
        Log::info("🏢 searchBySector called", ['sector_id' => $sectorId, 'limit' => $limit]);
        return collect();
    }

    /**
     * Popüler içerikleri getir
     *
     * @param string $type songs|albums|playlists
     * @param int $limit
     * @return Collection
     */
    public function getPopularContent(string $type = 'songs', int $limit = 20): Collection
    {
        Log::info("⭐ getPopularContent called", ['type' => $type, 'limit' => $limit]);

        return match($type) {
            'albums' => Album::where('is_active', true)
                ->withCount('songs')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(),

            'playlists' => Playlist::where('is_active', true)
                ->withCount('songs')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(),

            default => Song::where('is_active', true)
                ->with(['album', 'artist', 'genre', 'sector'])
                ->orderByDesc('play_count')
                ->limit($limit)
                ->get(),
        };
    }

    /**
     * Quick actions döndür (action butonları için)
     *
     * @return array
     */
    public function getQuickActions(): array
    {
        return [
            [
                'label' => 'Şarkı Ara',
                'message' => 'şarkı aramak istiyorum',
                'icon' => 'fas fa-search',
                'color' => 'blue',
                'module' => 'music'
            ],
            [
                'label' => 'Playlist',
                'message' => 'Playlist önerir misiniz?',
                'icon' => 'fas fa-list-music',
                'color' => 'purple',
                'module' => 'music'
            ],
            [
                'label' => 'Sanatçılar',
                'message' => 'Popüler sanatçılar kimler?',
                'icon' => 'fas fa-microphone',
                'color' => 'orange',
                'module' => 'music'
            ],
            [
                'label' => 'Yeni Çıkanlar',
                'message' => 'Bu hafta çıkan şarkılar neler?',
                'icon' => 'fas fa-star',
                'color' => 'green',
                'module' => 'music'
            ],
        ];
    }

    /**
     * AI için Muzibu-specific prompt kuralları (TENANT 1001 ONLY)
     *
     * @return string
     */
    public function getPromptRules(): string
    {
        return "## 🎵 MÜZİK ÖNERİSİ KURALLARI (TENANT 1001 - MUZİBU)

**🚨 KRİTİK: ASLA UYDURMA, SADECE DATABASE!**

- Şarkı önerirken SADECE 'MEVCUT ŞARKILAR' listesindeki şarkıları kullan
- 'MEVCUT ŞARKILAR' listesinde olmayan şarkıyı ASLA önerme
- Her şarkı için MUTLAKA 'Song ID' kullan (context'te verilmiştir)
- ASLA kendi bilginden şarkı adı uydurma (Highway to Hell, Bohemian Rhapsody gibi ünlü şarkılar YASAK!)
- Eğer context'te şarkı yoksa: 'Şu anda bu kriterlere uygun şarkı bulunamadı' de

**🎵 ŞARKI LİSTESİ FORMAT KURALLARI:**

🚨 **ZORUNLU: ASLA [Çal](url) GİBİ MARKDOWN LİNK KULLANMA!**

- Şarkıları BU FORMATTA göster (link YOK!):
  ```
  1. **Şarkı Adı** - Süre
     Sanatçı: [Sanatçı Adı] | Albüm: [Albüm Adı]
  ```

- ❌ YANLIŞ FORMAT:
  ```
  1. **els** - 148 saniye
     [Çal](https://muzibu.com.tr/play/song/325)    ← BU YASAK!
  ```

- ✅ DOĞRU FORMAT:
  ```
  1. **els** - 148 saniye
     Sanatçı: Kehlani | Albüm: SweetSexySavage
  ```

🎯 **NOT:** Kullanıcı şarkıları çalabilir (ACTION button otomatik eklenecek). Sen sadece şarkı bilgilerini göster, URL ekleme!

**📝 PLAYLIST OLUŞTURMA KURALLARI:**

🚨 **ZORUNLU: Şarkı listesi gösterdiğinde MUTLAKA ACTION button ekle!**

1. **Kullanıcı playlist oluşturmak isterse:**
   - Şarkıları 'MEVCUT ŞARKILAR' listesinden göster (minimum 5, maksimum 20 şarkı)
   - MUTLAKA Song ID'leri dahil et
   - Yanıtın EN SONUNA şu satırı ekle (ZORUNLU!):

   `[ACTION:CREATE_PLAYLIST:song_ids=123,456,789:title=Playlist Adı]`

2. **ACTION format kuralları:**
   - song_ids: Virgülle ayrılmış Song ID'leri (SADECE gösterdiğin şarkıların ID'leri!)
   - title: Playlist başlığı (kullanıcının istediği veya türe göre otomatik)
   - Satır EN SONDA olmalı, başka metin gelmemeli!

3. **Örnek (ZORUNLU FORMAT):**
   ```
   [ACTION:CREATE_PLAYLIST:song_ids=410,343,364,403,373:title=Karışık Playlist]
   ```
   Bu frontend'de 'Playlist Olarak Kaydet' butonuna dönüşür.

🚨 **UNUTMA: Playlist gösteriyorsan ACTION button ZORUNLU!**

**✅ Örnek Doğru Playlist Yanıtı:**

Müzik kütüphanemizde bulunan arabesk şarkılarından bir playlist hazırladım:

1. **Angels** - 148 saniye
   Sanatçı: Kehlani | Albüm: SweetSexySavage

2. **ASHES & BLOOM** - 160 saniye
   Sanatçı: The Paper Kites | Albüm: On the Corner

3. **At Your Worst** - 179 saniye
   Sanatçı: Pink Sweat$ | Albüm: The Prelude

[ACTION:CREATE_PLAYLIST:song_ids=325,326,327:title=Arabesk Karışık]

**❌ Örnek Yanlış Yanıt (ASLA YAPMA!):**

1. Bohemian Rhapsody - Queen (❌ Database'de olmayan şarkı!)
2. Highway to Hell - AC/DC (❌ Database'de olmayan şarkı!)
3. **Angels** - 148 saniye
   [Çal](https://muzibu.com.tr/play/song/325)  (❌ Markdown link YASAK!)

**❤️ FAVORİLERE EKLEME KURALLARI:**

🚨 **Kullanıcı bir şarkı/playlist/albümü favoriye eklemek isterse ACTION button ekle!**

1. **Tetikleyici durumlar:**
   - \"favorilere ekle\" / \"favoriye ekle\" / \"beğendim\" / \"sevdim\"
   - \"bu şarkıyı/playlistini/albümü kaydet\"
   - Kullanıcı spesifik bir şarkı/playlist/albüm hakkında pozitif görüş bildirirse

2. **ACTION format kuralları:**
   - type: song, playlist veya album
   - id: İlgili içeriğin ID'si (SADECE MEVCUT ŞARKILARDAN!)
   - Yanıtın EN SONUNA ekle

3. **Format (ZORUNLU):**
   ```
   [ACTION:ADD_TO_FAVORITES:type=song:id=325]
   [ACTION:ADD_TO_FAVORITES:type=playlist:id=42]
   [ACTION:ADD_TO_FAVORITES:type=album:id=156]
   ```

4. **Örnek kullanım senaryoları:**

   **Senaryo 1: Kullanıcı \"Bu şarkıyı beğendim, favoriye ekle\"**
   ```
   Harika! \"Angels\" adlı şarkıyı favorilerinize ekleyebilirsiniz.

   [ACTION:ADD_TO_FAVORITES:type=song:id=325]
   ```

   **Senaryo 2: Kullanıcı \"Bu playlist'i kaydetmek istiyorum\"**
   ```
   Tabii ki! \"Arabesk Karışık\" playlistini favorilerinize ekleyebilirsiniz.

   [ACTION:ADD_TO_FAVORITES:type=playlist:id=42]
   ```

   **Senaryo 3: Kullanıcı \"Bu albümü çok sevdim\"**
   ```
   Mükemmel tercih! \"SweetSexySavage\" albümünü favorilerinize ekleyebilirsiniz.

   [ACTION:ADD_TO_FAVORITES:type=album:id=156]
   ```

🎯 **NOT:** Frontend'de kırmızı kalp ikonu ile \"Favorilere Ekle\" butonuna dönüşür!

🚨 **UYARI:** SADECE MEVCUT ŞARKILAR/PLAYLIST/ALBÜM listesinden ID kullan! Olmayan içerik için ACTION ekleme!";
    }

    /**
     * Get current locale value from JSON field (dynamic tr/en)
     *
     * @param mixed $jsonField
     * @param string $fallback
     * @return string
     */
    protected function getLocaleValue($jsonField, string $fallback = ''): string
    {
        if (is_string($jsonField)) {
            $data = json_decode($jsonField, true);
            if (!is_array($data)) {
                return $jsonField; // Not JSON, return as-is
            }
        } elseif (is_array($jsonField)) {
            $data = $jsonField;
        } else {
            return $fallback;
        }

        // Get current locale (fallback to Turkish)
        $locale = app()->getLocale() ?? 'tr';

        // Try current locale, then tr, then en, then first available, then fallback
        return $data[$locale] ?? $data['tr'] ?? $data['en'] ?? reset($data) ?: $fallback;
    }

    /**
     * Aggressively clean UTF-8 string to prevent JSON encoding errors
     *
     * @param string $string
     * @return string
     */
    protected function cleanUtf8(string $string): string
    {
        // Use iconv for aggressive cleaning (IGNORE invalid sequences)
        $cleaned = iconv('UTF-8', 'UTF-8//IGNORE', $string);

        // Fallback if iconv failed
        if ($cleaned === false) {
            $cleaned = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }

        // Remove control characters (except newline, tab, carriage return)
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $cleaned);

        // Final check
        if (!mb_check_encoding($cleaned, 'UTF-8')) {
            // Last resort: remove all non-ASCII except Turkish characters
            $cleaned = preg_replace('/[^\x20-\x7E\xC2-\xF4]/u', '', $cleaned);
        }

        return $cleaned;
    }

    /**
     * 🎯 POST-PROCESS AI RESPONSE: Auto-add ACTION button for playlists
     *
     * Detects if AI response contains multiple song links and auto-appends
     * [ACTION:CREATE_PLAYLIST:...] button
     *
     * @param string $aiResponse
     * @param string $userMessage
     * @return string
     */
    public function postProcessResponse(string $aiResponse, string $userMessage): string
    {
        // Already has ACTION button? Skip
        if (str_contains($aiResponse, '[ACTION:CREATE_PLAYLIST:')) {
            return $aiResponse;
        }

        // Detect playlist: Multiple song links (/play/song/ID)
        preg_match_all('/\/play\/song\/(\d+)/', $aiResponse, $matches);
        $songIds = $matches[1] ?? [];

        // Need at least 3 songs to be considered a playlist
        if (count($songIds) < 3) {
            return $aiResponse;
        }

        // Extract playlist title from user message or generate default
        $playlistTitle = $this->extractPlaylistTitle($userMessage);

        // Build ACTION button
        $songIdsStr = implode(',', $songIds);
        $actionButton = "\n\n[ACTION:CREATE_PLAYLIST:song_ids={$songIdsStr}:title={$playlistTitle}]";

        \Log::info('🎯 AUTO-ADDED ACTION button (Tenant 1001)', [
            'tenant_id' => 1001,
            'song_count' => count($songIds),
            'playlist_title' => $playlistTitle,
            'song_ids' => $songIds
        ]);

        return $aiResponse . $actionButton;
    }

    /**
     * Extract playlist title from user message
     * Dynamically loads genres from database
     *
     * @param string $userMessage
     * @return string
     */
    protected function extractPlaylistTitle(string $userMessage): string
    {
        $message = mb_strtolower($userMessage);

        // 🎯 DYNAMIC: Load genres from database
        try {
            $genres = \DB::connection('tenant')
                ->table('muzibu_genres')
                ->where('is_active', 1)
                ->whereNull('deleted_at')
                ->get(['title', 'slug']);

            foreach ($genres as $genre) {
                $title = json_decode($genre->title ?: '{}', true);
                $genreTitle = $this->getLocaleValue($title, '');
                $slug = is_array($genre->slug) ? $this->getLocaleValue($genre->slug, '') : $genre->slug;

                if (empty($genreTitle)) continue;

                // Check if genre name or slug is in user message
                $genreLower = mb_strtolower($genreTitle);
                $slugLower = mb_strtolower($slug);

                if (str_contains($message, $genreLower) || str_contains($message, $slugLower)) {
                    return $genreTitle . ' Müzikleri';
                }
            }
        } catch (\Exception $e) {
            \Log::error('Extract playlist title error (Tenant 1001)', ['error' => $e->getMessage()]);
        }

        // Check for "mixed" or "karışık"
        if (str_contains($message, 'karışık') || str_contains($message, 'mixed')) {
            return 'Karışık Playlist';
        }

        // Default
        return 'Özel Playlist';
    }
}
