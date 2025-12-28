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
            // 🎯 DEBUG: Log incoming request
            \Log::info('🎯 QUEUE REFILL REQUEST', [
                'input' => $request->all(),
                'user_agent' => $request->userAgent(),
            ]);

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

            // 🎯 DEBUG: Log result
            \Log::info('🎯 QUEUE REFILL RESULT', [
                'type' => $type,
                'id' => $id,
                'songs_count' => count($songs),
                'transition' => $transitionSuggestion,
            ]);

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
     * Get songs by genre (INFINITE LOOP - başa sarar)
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getGenreSongs(int $genreId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Genre song IDs (5 min TTL)
        $cacheKey = "genre_{$genreId}_song_ids";
        $songIds = \Cache::remember($cacheKey, 300, function () use ($genreId) {
            $genre = Genre::find($genreId);
            if (!$genre) return [];

            return $genre->songs()
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($songIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($songIds, $limit, $excludeSongIds);
    }

    /**
     * Get songs by album
     * 🔄 TRANSITION: Album biter → Genre'ye geç
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getAlbumSongs(int $albumId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Album song IDs (5 min TTL)
        $cacheKey = "album_{$albumId}_song_ids";
        $albumSongIds = \Cache::remember($cacheKey, 300, function () use ($albumId) {
            $album = Album::find($albumId);
            if (!$album) return [];

            return $album->songs()
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($albumSongIds)) {
            return [];
        }

        // Check if album exhausted
        $remainingIds = array_diff($albumSongIds, $excludeSongIds);
        if (count($remainingIds) <= 1) {
            // Transition to genre
            $lastSong = Song::whereIn('song_id', $albumSongIds)->first();
            if ($lastSong && $lastSong->genre_id) {
                return $this->getGenreSongs($lastSong->genre_id, 0, $limit, []);
            }
        }

        return $this->getRandomSongsFromIds($albumSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get songs by playlist
     * 🔄 TRANSITION: Playlist biter → Genre'ye geç
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getPlaylistSongs(int $playlistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Playlist song IDs (5 min TTL)
        $cacheKey = "playlist_{$playlistId}_song_ids";
        $playlistSongIds = \Cache::remember($cacheKey, 300, function () use ($playlistId) {
            $pivotIds = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlistId)
                ->pluck('song_id')
                ->unique()
                ->toArray();

            if (empty($pivotIds)) return [];

            return Song::whereIn('song_id', $pivotIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($playlistSongIds)) {
            return [];
        }

        // Check if playlist exhausted
        $remainingIds = array_diff($playlistSongIds, $excludeSongIds);
        if (count($remainingIds) <= 1) {
            // Transition to most common genre
            $lastSong = Song::whereIn('song_id', $playlistSongIds)->first();
            if ($lastSong && $lastSong->genre_id) {
                return $this->getGenreSongs($lastSong->genre_id, 0, $limit, []);
            }
        }

        return $this->getRandomSongsFromIds($playlistSongIds, $limit, $excludeSongIds);
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
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getSectorSongs(int $sectorId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Sector song IDs (5 min TTL)
        $cacheKey = "sector_{$sectorId}_song_ids";
        $sectorSongIds = \Cache::remember($cacheKey, 300, function () use ($sectorId) {
            $sector = Sector::find($sectorId);
            if (!$sector) return [];

            $playlistIds = $sector->playlists()
                ->where('muzibu_playlists.is_active', 1)
                ->pluck('muzibu_playlists.playlist_id');

            if ($playlistIds->isEmpty()) return [];

            $pivotIds = DB::table('muzibu_playlist_song')
                ->whereIn('playlist_id', $playlistIds)
                ->pluck('song_id')
                ->unique()
                ->toArray();

            return Song::whereIn('song_id', $pivotIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($sectorSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($sectorSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get songs by radio (radio playlists - infinite)
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getRadioSongs(int $radioId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Radio song IDs (5 min TTL)
        $cacheKey = "radio_{$radioId}_song_ids";
        $radioSongIds = \Cache::remember($cacheKey, 300, function () use ($radioId) {
            $radio = Radio::find($radioId);
            if (!$radio) return [];

            $playlistIds = $radio->playlists()
                ->where('muzibu_playlists.is_active', 1)
                ->pluck('muzibu_playlists.playlist_id');

            if ($playlistIds->isEmpty()) return [];

            $pivotIds = DB::table('muzibu_playlist_song')
                ->whereIn('playlist_id', $playlistIds)
                ->pluck('song_id')
                ->unique()
                ->toArray();

            return Song::whereIn('song_id', $pivotIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($radioSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($radioSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get popular songs
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getPopularSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Top 100 popular song IDs (5 min TTL)
        $cacheKey = 'popular_song_ids_top100';
        $popularSongIds = \Cache::remember($cacheKey, 300, function () {
            return Song::where('is_active', 1)
                ->whereNotNull('hls_path')
                ->orderBy('play_count', 'desc')
                ->take(100)
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($popularSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($popularSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get recent songs
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getRecentSongs(int $offset, int $limit, ?string $subType = null, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Recent 200 song IDs (5 min TTL)
        $cacheKey = 'recent_song_ids_200';
        $recentSongIds = \Cache::remember($cacheKey, 300, function () {
            return Song::where('is_active', 1)
                ->whereNotNull('hls_path')
                ->orderBy('created_at', 'desc')
                ->take(200)
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($recentSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($recentSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get favorite songs
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     * Note: User-specific, so shorter cache (1 min)
     */
    private function getFavoriteSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return [];
        }

        // 🔥 CACHE: User favorite song IDs (1 min TTL - user specific)
        $cacheKey = "user_{$userId}_favorite_song_ids";
        $favoriteSongIds = \Cache::remember($cacheKey, 60, function () use ($userId) {
            $favoriteIds = DB::table('favorites')
                ->where('user_id', $userId)
                ->where('favoritable_type', Song::class)
                ->pluck('favoritable_id')
                ->toArray();

            if (empty($favoriteIds)) return [];

            return Song::whereIn('song_id', $favoriteIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($favoriteSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($favoriteSongIds, $limit, $excludeSongIds);
    }

    /**
     * Get songs by artist
     * 🚀 OPTIMIZED: PHP shuffle instead of ORDER BY RAND()
     */
    private function getArtistSongs(int $artistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Artist song IDs (5 min TTL)
        $cacheKey = "artist_{$artistId}_song_ids";
        $artistSongIds = \Cache::remember($cacheKey, 300, function () use ($artistId) {
            $albumIds = Album::where('artist_id', $artistId)->pluck('album_id')->toArray();
            if (empty($albumIds)) return [];

            return Song::whereIn('album_id', $albumIds)
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($artistSongIds)) {
            return [];
        }

        return $this->getRandomSongsFromIds($artistSongIds, $limit, $excludeSongIds);
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
     * 🚀 OPTIMIZED HELPER: Get random songs from cached IDs
     * Uses PHP shuffle instead of ORDER BY RAND() (10x faster)
     *
     * @param array $songIds All available song IDs
     * @param int $limit Number of songs to return
     * @param array $excludeIds Songs to exclude (already played)
     * @return array Formatted songs
     */
    private function getRandomSongsFromIds(array $songIds, int $limit, array $excludeIds = []): array
    {
        // Exclude already played songs
        if (!empty($excludeIds)) {
            $songIds = array_values(array_diff($songIds, $excludeIds));
        }

        // If not enough songs after exclude, reset (infinite loop)
        if (count($songIds) < $limit) {
            // Get original IDs from cache or use current
            $songIds = array_values($songIds); // Reset to available
        }

        if (empty($songIds)) {
            return [];
        }

        // 🎲 PHP shuffle (FAST!) instead of ORDER BY RAND() (SLOW!)
        shuffle($songIds);
        $selectedIds = array_slice($songIds, 0, $limit);

        // Fetch songs in single query with eager loading
        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get();

        return $this->formatSongs($songs);
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
                'album_cover' => $song->getCoverUrl(120, 120),
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

    /**
     * Get initial queue on page load
     * 🚀 INSTANT LOAD: Sayfa açılır açılmaz queue hazır
     * 🔥 OPTIMIZED: 2232ms → ~200ms (cache + optimized random)
     *
     * - Login user: Son dinlenen şarkı + genre'sinden 14 şarkı
     * - Guest: Popüler şarkılardan 15 şarkı (cached)
     */
    public function initialQueue(Request $request): JsonResponse
    {
        try {
            $limit = 15;
            $userId = auth()->id();
            $songs = [];
            $context = null;

            if ($userId) {
                // 🎵 LOGIN USER: Son dinlenen şarkıyı al (optimized single query)
                $lastPlay = DB::table('muzibu_song_plays')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->select('song_id')
                    ->first();

                if ($lastPlay) {
                    $lastSong = Song::with(['album.artist', 'genre'])
                        ->where('is_active', 1)
                        ->whereNotNull('hls_path')
                        ->find($lastPlay->song_id);

                    if ($lastSong) {
                        // İlk şarkı: Son dinlenen
                        $songs[] = $this->formatSingleSong($lastSong);

                        // Context: Son dinlenen şarkının genre'si
                        if ($lastSong->genre_id) {
                            $context = [
                                'type' => 'genre',
                                'id' => $lastSong->genre_id,
                                'name' => $lastSong->genre?->title ?? 'Müzik',
                            ];

                            // 🚀 OPTIMIZED: Use fast random instead of ORDER BY RAND()
                            $genreSongs = $this->getFastRandomGenreSongs(
                                $lastSong->genre_id,
                                $limit - 1,
                                [$lastSong->song_id]
                            );
                            $songs = array_merge($songs, $genreSongs);
                        }
                    }
                }
            }

            // Şarkı bulunamadıysa veya guest ise: Popüler şarkılar (CACHED)
            if (empty($songs)) {
                $songs = $this->getCachedPopularSongs($limit);
                $context = [
                    'type' => 'popular',
                    'id' => null,
                    'name' => 'Popüler',
                ];
            }

            return response()->json([
                'success' => true,
                'songs' => $songs,
                'context' => $context,
                'count' => count($songs),
            ]);

        } catch (\Exception $e) {
            \Log::error('Initial queue error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'songs' => [],
            ], 500);
        }
    }

    /**
     * 🚀 FAST RANDOM: PHP shuffle instead of ORDER BY RAND()
     * ORDER BY RAND() = O(n log n) + full table scan
     * PHP shuffle = O(n) on cached IDs
     */
    private function getFastRandomGenreSongs(int $genreId, int $limit, array $excludeIds = []): array
    {
        // 🔥 CACHE: Genre song IDs (5 min TTL)
        $cacheKey = "genre_{$genreId}_song_ids";

        $songIds = \Cache::remember($cacheKey, 300, function () use ($genreId) {
            $genre = Genre::find($genreId);
            if (!$genre) return [];

            return $genre->songs()
                ->where('is_active', 1)
                ->whereNotNull('hls_path')
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($songIds)) {
            return [];
        }

        // Exclude IDs
        $songIds = array_diff($songIds, $excludeIds);

        if (empty($songIds)) {
            return [];
        }

        // 🎲 PHP shuffle (FAST!) instead of ORDER BY RAND() (SLOW!)
        shuffle($songIds);
        $selectedIds = array_slice($songIds, 0, $limit);

        // Fetch songs in single query
        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * 🚀 CACHED POPULAR: Guest users get cached popular songs
     */
    private function getCachedPopularSongs(int $limit): array
    {
        $cacheKey = 'initial_queue_popular_songs';

        return \Cache::remember($cacheKey, 300, function () use ($limit) {
            // 🔥 Top 50'den random 15 al (ORDER BY play_count, sonra PHP shuffle)
            $topSongIds = Song::where('is_active', 1)
                ->whereNotNull('hls_path')
                ->orderBy('play_count', 'desc')
                ->take(50)
                ->pluck('song_id')
                ->toArray();

            if (empty($topSongIds)) {
                return [];
            }

            shuffle($topSongIds);
            $selectedIds = array_slice($topSongIds, 0, $limit);

            $songs = Song::whereIn('song_id', $selectedIds)
                ->where('is_active', 1)
                ->with(['album.artist'])
                ->get();

            return $this->formatSongs($songs);
        });
    }

    /**
     * Format single song (helper)
     */
    private function formatSingleSong(Song $song): array
    {
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
            'album_cover' => $song->getCoverUrl(120, 120),
            'artist_id' => $artist?->artist_id,
            'artist_title' => $artist?->title,
            'artist_slug' => $artist?->slug,
        ];
    }
}
