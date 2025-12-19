<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Sector;
use Modules\Muzibu\App\Models\Radio;

/**
 * Queue Refill Controller
 * 
 * Context-based infinite queue system
 * Returns songs based on play context (genre, album, playlist, etc.)
 */
class QueueRefillController extends Controller
{
    /**
     * Refill queue based on context
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function refill(Request $request): JsonResponse
    {
        try {
            $context = $request->validate([
                'type' => 'required|string|in:genre,album,playlist,user_playlist,sector,radio,popular,recent,favorites,artist,search',
                'id' => 'nullable|integer',
                'offset' => 'nullable|integer|min:0',
                'limit' => 'nullable|integer|min:1|max:50',
                'subType' => 'nullable|string',
                'source' => 'nullable|string',
                'exclude_song_ids' => 'nullable|array', // 🎯 Son çalınan şarkıları exclude et
                'exclude_song_ids.*' => 'integer',
            ]);

            $type = $context['type'];
            $id = $context['id'] ?? null;
            $offset = $context['offset'] ?? 0;
            $limit = $context['limit'] ?? 15;
            $excludeSongIds = $context['exclude_song_ids'] ?? []; // 🎯 Exclude list

            // 🎯 PERFORMANCE: Limit exclude list to 500 max (SQL whereNotIn performance)
            if (count($excludeSongIds) > 500) {
                $excludeSongIds = array_slice($excludeSongIds, 0, 500);
                \Log::info('⚠️ Exclude list trimmed to 500 (performance)', [
                    'original_count' => count($context['exclude_song_ids'] ?? [])
                ]);
            }

            $songs = match($type) {
                'genre' => $this->getGenreSongs($id, $offset, $limit, $excludeSongIds),
                'album' => $this->getAlbumSongs($id, $offset, $limit, $excludeSongIds),
                'playlist' => $this->getPlaylistSongs($id, $offset, $limit, $excludeSongIds),
                'user_playlist' => $this->getUserPlaylistSongs($id, $offset, $limit, $excludeSongIds),
                'sector' => $this->getSectorSongs($id, $offset, $limit, $excludeSongIds),
                'radio' => $this->getRadioSongs($id, $offset, $limit, $excludeSongIds),
                'popular' => $this->getPopularSongs($offset, $limit, $excludeSongIds),
                'recent' => $this->getRecentSongs($offset, $limit, $context['subType'] ?? null, $excludeSongIds),
                'favorites' => $this->getFavoriteSongs($offset, $limit, $excludeSongIds),
                'artist' => $this->getArtistSongs($id, $offset, $limit, $excludeSongIds),
                'search' => $this->getSearchSongs($id, $offset, $limit, $excludeSongIds),
                default => [],
            };

            // 🔄 CONTEXT TRANSITION: Eğer queue boş ve genre değilse, fallback öner
            $transitionSuggestion = null;
            if (empty($songs) && $type !== 'genre') {
                // Get most popular genre as fallback
                $fallbackGenre = Genre::withCount(['songs' => function($query) {
                    $query->where('is_active', 1);
                }])
                ->where('is_active', 1)
                ->orderBy('songs_count', 'desc')
                ->first();

                if ($fallbackGenre && $fallbackGenre->songs_count > 0) {
                    $transitionSuggestion = [
                        'type' => 'genre',
                        'id' => $fallbackGenre->genre_id,
                        'name' => $fallbackGenre->title,
                        'reason' => 'Current context empty - transitioning to popular genre for infinite music'
                    ];

                    // Auto-fill with genre songs (infinite loop guaranteed)
                    $songs = $this->getGenreSongs($fallbackGenre->genre_id, 0, $limit);
                }
            }

            // 🧪 DEBUG: Şarkı seçim açıklaması
            $explanation = $this->getSelectionExplanation($type, $id, $offset, count($songs));

            return response()->json([
                'success' => true,
                'context' => $context,
                'songs' => $songs,
                'count' => count($songs),
                'transition' => $transitionSuggestion, // Frontend will auto-update context
                'explanation' => $explanation, // 🧪 Debug için açıklama
            ]);

        } catch (\Exception $e) {
            \Log::error('Queue refill error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get songs by genre (INFINITE LOOP - başa sarar + HER ZAMAN SQL random)
     * 🎵 Aynı tarz, farklı sıra - SQL seviyesinde inRandomOrder()
     */
    private function getGenreSongs(int $genreId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $genre = Genre::find($genreId);
        if (!$genre) {
            return [];
        }

        // 🎯 Toplam şarkı sayısı (exclude öncesi)
        $totalSongsBeforeExclude = $genre->songs()->where('is_active', 1)->count();

        // 🎲 SQL SEVİYESİNDE RANDOM: inRandomOrder() kullan (her query farklı sonuç)
        $songs = $genre->songs()
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), function($query) use ($excludeSongIds) {
                // 🎯 Exclude: Son çalınan şarkıları hariç tut
                $query->whereNotIn('song_id', $excludeSongIds);
            })
            ->with(['album.artist'])
            ->inRandomOrder() // SQL: ORDER BY RAND()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse (exclude çok fazlaysa), exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            \Log::info('🔄 Genre exhausted with exclude, retrying without exclude (reset loop)', [
                'genre_id' => $genreId,
                'excluded_count' => count($excludeSongIds),
                'got' => $songs->count(),
                'needed' => $limit
            ]);

            $songs = $genre->songs()
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        if ($songs->isEmpty()) {
            return [];
        }

        \Log::info('🎲 Genre SQL random selection (inRandomOrder, infinite loop)', [
            'genre_id' => $genreId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by album
     * 🔄 TRANSITION: Album biter → Genre'ye geç (PLAN v4)
     * 🎲 SQL RANDOM: inRandomOrder() ile her query farklı
     */
    private function getAlbumSongs(int $albumId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $album = Album::with('songs')->find($albumId);
        if (!$album) {
            return [];
        }

        // 🎯 Toplam şarkı sayısı (exclude öncesi)
        $totalSongsBeforeExclude = $album->songs()->where('is_active', 1)->count();

        // 🎲 SQL SEVİYESİNDE RANDOM
        $songs = $album->songs()
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), function($query) use ($excludeSongIds) {
                $query->whereNotIn('muzibu_songs.song_id', $excludeSongIds);
            })
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = $album->songs()
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Album songs bitti mi?
        if ($songs->isEmpty()) {
            // ✅ TRANSITION: Album → Genre (son şarkının genre'sine geç)
            $lastSong = $album->songs()
                ->where('is_active', 1)
                ->orderBy('song_id', 'desc')
                ->first();

            if ($lastSong && $lastSong->genre_id) {
                \Log::info('🔄 Context Transition: Album → Genre', [
                    'album_id' => $albumId,
                    'genre_id' => $lastSong->genre_id
                ]);

                return $this->getGenreSongs($lastSong->genre_id, 0, $limit, $excludeSongIds);
            }

            return [];
        }

        \Log::info('🎲 Album SQL random selection', [
            'album_id' => $albumId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by playlist
     * 🔄 TRANSITION: Playlist biter → Genre'ye geç (son 5 şarkının en çok genre'si)
     * 🎲 SQL RANDOM: inRandomOrder() ile her query farklı
     */
    private function getPlaylistSongs(int $playlistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $playlist = Playlist::find($playlistId);
        if (!$playlist) {
            return [];
        }

        // 🎲 SQL SEVİYESİNDE RANDOM: Önce song ID'leri al (pivot table issue çözümü)
        $songIds = DB::table('muzibu_playlist_song')
            ->where('playlist_id', $playlistId)
            ->pluck('song_id')
            ->unique();

        if ($songIds->isEmpty()) {
            return [];
        }

        $totalSongsBeforeExclude = $songIds->count();

        // SQL random ile şarkıları çek
        $songs = Song::whereIn('song_id', $songIds)
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $songIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Playlist songs bitti mi?
        if ($songs->isEmpty()) {
            // ✅ TRANSITION: Playlist → Genre (son 5 şarkının en çok genre'si)
            $lastSongs = Song::whereIn('song_id', $songIds)
                ->where('is_active', 1)
                ->orderBy('song_id', 'desc')
                ->take(5)
                ->get();

            if ($lastSongs->isNotEmpty()) {
                // En çok kullanılan genre'yi bul
                $genreCounts = $lastSongs->groupBy('genre_id')->map->count();
                $mostCommonGenreId = $genreCounts->sortDesc()->keys()->first();

                if ($mostCommonGenreId) {
                    \Log::info('🔄 Context Transition: Playlist → Genre', [
                        'playlist_id' => $playlistId,
                        'genre_id' => $mostCommonGenreId,
                        'genre_count' => $genreCounts[$mostCommonGenreId]
                    ]);

                    return $this->getGenreSongs($mostCommonGenreId, 0, $limit, $excludeSongIds);
                }
            }

            return [];
        }

        \Log::info('🎲 Playlist SQL random selection', [
            'playlist_id' => $playlistId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by user playlist
     */
    private function getUserPlaylistSongs(int $playlistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // User playlists use same table, just filter by user
        return $this->getPlaylistSongs($playlistId, $offset, $limit, $excludeSongIds);
    }

    /**
     * Get songs by sector (sector playlists - infinite)
     * ♾️ SELF-LOOP: Sector kendi içinde infinite loop (Genre'ye GEÇMİYOR!)
     * 🎲 SQL RANDOM: inRandomOrder() ile her query farklı
     */
    private function getSectorSongs(int $sectorId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $sector = Sector::find($sectorId);
        if (!$sector) {
            return [];
        }

        // Get all playlist IDs in this sector
        $playlistIds = $sector->playlists()->where('muzibu_playlists.is_active', 1)->pluck('muzibu_playlists.playlist_id');

        if ($playlistIds->isEmpty()) {
            return [];
        }

        // 🎲 SQL SEVİYESİNDE RANDOM: Tüm playlist'lerdeki song ID'leri al
        $songIds = DB::table('muzibu_playlist_song')
            ->whereIn('playlist_id', $playlistIds)
            ->pluck('song_id')
            ->unique();

        if ($songIds->isEmpty()) {
            return [];
        }

        $totalSongsBeforeExclude = $songIds->count();

        // SQL random ile şarkıları çek
        $songs = Song::whereIn('song_id', $songIds)
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $songIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        if ($songs->isEmpty()) {
            return [];
        }

        \Log::info('🎲 Sector SQL random selection (infinite loop)', [
            'sector_id' => $sectorId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by radio (radio playlists - infinite)
     * ♾️ SELF-LOOP: Radio kendi içinde infinite loop (Genre'ye GEÇMİYOR!)
     * 🎲 SHUFFLE: Her refill'de rastgele şarkılar
     */
    private function getRadioSongs(int $radioId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $radio = Radio::find($radioId);
        if (!$radio) {
            return [];
        }

        // Get all playlist IDs in this radio
        $playlistIds = $radio->playlists()->where('muzibu_playlists.is_active', 1)->pluck('muzibu_playlists.playlist_id');

        if ($playlistIds->isEmpty()) {
            return [];
        }

        // 🎲 SQL SEVİYESİNDE RANDOM
        $songIds = DB::table('muzibu_playlist_song')
            ->whereIn('playlist_id', $playlistIds)
            ->pluck('song_id')
            ->unique();

        if ($songIds->isEmpty()) {
            return [];
        }

        $totalSongsBeforeExclude = $songIds->count();

        // SQL random ile şarkıları çek
        $songs = Song::whereIn('song_id', $songIds)
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $songIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        if ($songs->isEmpty()) {
            return [];
        }

        \Log::info('🎲 Radio SQL random selection (infinite loop)', [
            'radio_id' => $radioId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get popular songs
     * 🔄 TRANSITION: Popular biter → Album → Genre
     * 🎲 SQL RANDOM: Top 100'den inRandomOrder() ile
     */
    private function getPopularSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🎲 Top 100 song ID'lerini al (play_count sıralı) - sadece HLS olanlar
        $popularSongIds = Song::where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->orderBy('play_count', 'desc')
            ->take(100)
            ->pluck('song_id');

        if ($popularSongIds->isEmpty()) {
            return [];
        }

        // SQL random ile şarkıları çek
        $songs = Song::whereIn('song_id', $popularSongIds)
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $popularSongIds)
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Popular songs bitti mi?
        if ($songs->isEmpty()) {
            // ✅ TRANSITION: Popular → Album → Genre
            $lastSong = Song::where('is_active', 1)
                ->orderBy('play_count', 'desc')
                ->first();

            if ($lastSong && $lastSong->album_id) {
                \Log::info('🔄 Context Transition: Popular → Album', [
                    'album_id' => $lastSong->album_id
                ]);

                return $this->getAlbumSongs($lastSong->album_id, 0, $limit, $excludeSongIds);
            }

            return [];
        }

        \Log::info('🎲 Popular SQL random selection (Top 100)', [
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get recent songs (continues backward from last ID)
     * ♾️ SELF-LOOP: Recent geriye doğru infinite loop
     * 🎲 SQL RANDOM: Son 200'den inRandomOrder() ile
     */
    private function getRecentSongs(int $offset, int $limit, ?string $subType = null, array $excludeSongIds = []): array
    {
        // 🎲 Son 200 song ID'lerini al (created_at sıralı) - sadece HLS olanlar
        $recentSongIds = Song::where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->pluck('song_id');

        if ($recentSongIds->isEmpty()) {
            return [];
        }

        // SQL random ile şarkıları çek
        $songs = Song::whereIn('song_id', $recentSongIds)
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $recentSongIds)
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // ♾️ INFINITE LOOP: Recent boşsa exclude'sız başa sar
        if ($songs->isEmpty()) {
            if (!empty($excludeSongIds)) {
                \Log::info('🔄 Recent exhausted with exclude, retrying without exclude (infinite loop)', [
                    'excluded_count' => count($excludeSongIds)
                ]);

                return $this->getRecentSongs($offset, $limit, $subType, []);
            }

            return [];
        }

        \Log::info('🎲 Recent SQL random selection (last 200 songs)', [
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get favorite songs
     * 🔄 TRANSITION: Favorites biter → Album → Genre
     * 🎲 SQL RANDOM: inRandomOrder() ile her query farklı
     */
    private function getFavoriteSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return [];
        }

        // Get user's favorited songs
        $favoriteSongIds = DB::table('favorites')
            ->where('user_id', $userId)
            ->where('favoritable_type', Song::class)
            ->pluck('favoritable_id');

        if ($favoriteSongIds->isEmpty()) {
            return [];
        }

        $totalSongsBeforeExclude = $favoriteSongIds->count();

        // 🎲 SQL SEVİYESİNDE RANDOM
        $songs = Song::whereIn('song_id', $favoriteSongIds)
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('song_id', $favoriteSongIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Favorites bitti mi?
        if ($songs->isEmpty()) {
            // ✅ TRANSITION: Favorites → Album → Genre
            $lastSong = Song::whereIn('song_id', $favoriteSongIds)
                ->where('is_active', 1)
                ->orderBy('song_id', 'desc')
                ->first();

            if ($lastSong && $lastSong->album_id) {
                \Log::info('🔄 Context Transition: Favorites → Album', [
                    'album_id' => $lastSong->album_id
                ]);

                return $this->getAlbumSongs($lastSong->album_id, 0, $limit, $excludeSongIds);
            }

            return [];
        }

        \Log::info('🎲 Favorites SQL random selection', [
            'total_favorites' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by artist
     * 🔄 TRANSITION: Artist biter → Album → Genre
     * 🎲 SQL RANDOM: inRandomOrder() ile her query farklı
     */
    private function getArtistSongs(int $artistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // Get albums by artist, then songs
        $albumIds = Album::where('artist_id', $artistId)->pluck('album_id');

        if ($albumIds->isEmpty()) {
            return [];
        }

        $totalSongsBeforeExclude = Song::whereIn('album_id', $albumIds)
            ->where('is_active', 1)
            ->count();

        // 🎲 SQL SEVİYESİNDE RANDOM
        $songs = Song::whereIn('album_id', $albumIds)
            ->where('is_active', 1)
            ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
            ->when(!empty($excludeSongIds), fn($q) => $q->whereNotIn('song_id', $excludeSongIds))
            ->with(['album.artist'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // Yeterli şarkı gelmezse, exclude'sız tekrar dene
        if ($songs->count() < $limit && !empty($excludeSongIds)) {
            $songs = Song::whereIn('album_id', $albumIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Artist songs bitti mi?
        if ($songs->isEmpty()) {
            // ✅ TRANSITION: Artist → Album → Genre
            $lastSong = Song::whereIn('album_id', $albumIds)
                ->where('is_active', 1)
                ->orderBy('song_id', 'desc')
                ->first();

            if ($lastSong && $lastSong->album_id) {
                \Log::info('🔄 Context Transition: Artist → Album', [
                    'artist_id' => $artistId,
                    'album_id' => $lastSong->album_id
                ]);

                return $this->getAlbumSongs($lastSong->album_id, 0, $limit, $excludeSongIds);
            }

            return [];
        }

        \Log::info('🎲 Artist SQL random selection', [
            'artist_id' => $artistId,
            'total_songs' => $totalSongsBeforeExclude,
            'excluded' => count($excludeSongIds),
            'returned' => $songs->count()
        ]);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by search
     */
    private function getSearchSongs(int $songId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // For search, get the selected song's album songs
        $song = Song::find($songId);
        if (!$song || !$song->album_id) {
            return [];
        }

        return $this->getAlbumSongs($song->album_id, $offset, $limit, $excludeSongIds);
    }

    /**
     * Format songs to consistent JSON structure
     */
    private function formatSongs($songs): array
    {
        return $songs->map(function ($song) {
            $album = $song->album;
            $artist = $album?->artist;

            return [
                'song_id' => $song->song_id,
                'song_title' => $song->title,
                'song_slug' => $song->slug,
                'duration' => $song->duration,
                'file_path' => $song->file_path,
                'hls_path' => $song->hls_path,
                'lyrics' => $song->lyrics,
                'album_id' => $album?->album_id,
                'album_title' => $album?->title,
                'album_slug' => $album?->slug,
                'album_cover' => $album?->media_id,
                'artist_id' => $artist?->artist_id,
                'artist_title' => $artist?->title,
                'artist_slug' => $artist?->slug,
            ];
        })->values()->toArray();
    }

    /**
     * 🧪 DEBUG: Şarkı seçim mantığını açıkla
     */
    private function getSelectionExplanation(string $type, ?int $id, int $offset, int $count): array
    {
        $sourceName = 'Bilinmiyor';
        $totalSongs = 0;
        $algorithm = '';

        switch ($type) {
            case 'playlist':
                $playlist = Playlist::find($id);
                $sourceName = $playlist?->title ?? "Playlist #{$id}";
                $totalSongs = $playlist?->songs()->where('is_active', 1)->count() ?? 0;
                $algorithm = "🎲 Rastgele seçim (son 300 şarkı hariç)";
                break;

            case 'album':
                $album = Album::with('artist')->find($id);
                $sourceName = $album?->title ?? "Albüm #{$id}";
                $totalSongs = $album?->songs()->where('is_active', 1)->count() ?? 0;
                $algorithm = "🎲 Rastgele seçim (son 300 şarkı hariç)";
                break;

            case 'genre':
                $genre = Genre::find($id);
                $sourceName = $genre?->title ?? "Tür #{$id}";
                $totalSongs = $genre?->songs()->where('is_active', 1)->count() ?? 0;
                $algorithm = "🎲 Rastgele seçim (♾️ sonsuz döngü, son 300 hariç)";
                break;

            case 'sector':
                $sector = Sector::find($id);
                $sourceName = $sector?->title ?? "Sektör #{$id}";
                $algorithm = "🎲 Rastgele seçim (♾️ sonsuz döngü, son 300 hariç)";
                break;

            case 'radio':
                $radio = Radio::find($id);
                $sourceName = $radio?->title ?? "Radyo #{$id}";
                $algorithm = "🎲 Rastgele seçim (♾️ radyo modu, son 300 hariç)";
                break;

            case 'popular':
                $sourceName = 'Popüler Şarkılar';
                $totalSongs = Song::where('is_active', 1)->count();
                $algorithm = "🎲 Rastgele seçim (Top 100'den, son 300 hariç)";
                break;

            case 'favorites':
                $sourceName = 'Favorilerim';
                $algorithm = "🎲 Rastgele seçim (favorilerden, son 300 hariç)";
                break;

            case 'recent':
                $sourceName = 'Son Eklenenler';
                $totalSongs = Song::where('is_active', 1)->count();
                $algorithm = "🎲 Rastgele seçim (son 200'den, son 300 hariç)";
                break;

            default:
                $algorithm = "Varsayılan sıralama";
        }

        return [
            'kaynak' => $sourceName,
            'kaynak_tipi' => $type,
            'toplam_sarki' => $totalSongs,
            'baslangic' => $offset + 1,
            'alinan' => $count,
            'algoritma' => $algorithm,
        ];
    }
}
