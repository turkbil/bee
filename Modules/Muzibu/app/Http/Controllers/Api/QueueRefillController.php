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
                'type' => 'required|string|in:genre,album,playlist,user_playlist,sector,radio,popular,recent,favorites,artist,search,song',
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

            // 🎯 TRANSITION AWARE: album, playlist, artist, popular, favorites transition döndürebilir
            // 📋 DOCUMENT: https://ixtif.com/readme/2025/12/05/context-based-infinite-queue/v4/
            // Genre-Based (7): Album, Playlist, Artist, Search, Popular, Favorites, Genre → Genre'ye geç
            // Self-Contained (3): Sector, Radio, Recent → Kendi havuzunda döner
            $result = match($type) {
                'genre' => ['songs' => $this->getGenreSongs($id, $offset, $limit, $excludeSongIds), 'transition' => null],
                'album' => $this->getAlbumSongs($id, $offset, $limit, $excludeSongIds),
                'playlist' => $this->getPlaylistSongs($id, $offset, $limit, $excludeSongIds),
                'user_playlist' => $this->getUserPlaylistSongs($id, $offset, $limit, $excludeSongIds),
                'sector' => ['songs' => $this->getSectorSongs($id, $offset, $limit, $excludeSongIds), 'transition' => null],
                'radio' => ['songs' => $this->getRadioSongs($id, $offset, $limit, $excludeSongIds), 'transition' => null],
                'popular' => $this->getPopularSongs($offset, $limit, $excludeSongIds), // 🔄 Genre transition
                'recent' => ['songs' => $this->getRecentSongs($offset, $limit, $context['subType'] ?? null, $excludeSongIds), 'transition' => null],
                'favorites' => $this->getFavoriteSongs($offset, $limit, $excludeSongIds), // 🔄 Genre transition
                'artist' => $this->getArtistSongs($id, $offset, $limit, $excludeSongIds),
                'search' => $this->getSearchSongs($id, $offset, $limit, $excludeSongIds),
                'song' => $this->getSongContext($id, $limit, $excludeSongIds), // 🎵 Tek şarkı → Albüm/Genre'ye geç
                default => ['songs' => [], 'transition' => null],
            };

            // Sonuçları ayır
            $songs = $result['songs'] ?? $result; // Eski format uyumluluğu
            $transitionSuggestion = $result['transition'] ?? null;

            // 🔄 FALLBACK: Eğer hala boşsa ve genre değilse, popüler genre'ye geç
            if (empty($songs) && $type !== 'genre' && !$transitionSuggestion) {
                $fallbackGenre = Genre::withCount(['songs' => function($query) {
                    $query->where('is_active', 1);
                }])
                ->where('is_active', 1)
                ->orderBy('songs_count', 'desc')
                ->first();

                if ($fallbackGenre && $fallbackGenre->songs_count > 0) {
                    $genreTitle = $this->extractTitle($fallbackGenre, 'Tür');

                    $transitionSuggestion = [
                        'type' => 'genre',
                        'id' => $fallbackGenre->genre_id,
                        'name' => $genreTitle,
                        'reason' => 'Şarkı bulunamadı, popüler türe geçiliyor'
                    ];

                    // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                    $songs = $this->getGenreSongs($fallbackGenre->genre_id, 0, $limit, $excludeSongIds);
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
                'transition' => $transitionSuggestion, // 🎯 Frontend context'i güncelleyecek!
                'explanation' => $explanation,
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
        return $this->getGenreSongsOnly($genreId, $limit, $excludeSongIds);
    }

    /**
     * 🎯 HELPER: Model title'ını çıkar (düz string, JSON veya array olabilir)
     */
    private function extractTitle($model, string $default = 'Unknown'): string
    {
        if (!$model || !isset($model->title)) {
            return $default;
        }

        $title = $model->title;

        // Already array (Laravel cast)
        if (is_array($title)) {
            return $title['tr'] ?? $title['en'] ?? $default;
        }

        // Plain string (not JSON)
        if (is_string($title) && !str_starts_with(trim($title), '{')) {
            return $title;
        }

        // JSON string - try to decode
        $decoded = json_decode($title, true);
        if (is_array($decoded)) {
            return $decoded['tr'] ?? $decoded['en'] ?? $default;
        }

        return $title ?: $default;
    }

    /**
     * 🎯 HELPER: Genre şarkılarını al (transition için kullanılır)
     * Sadece şarkı dizisi döner (wrapper yok)
     */
    private function getGenreSongsOnly(int $genreId, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Genre song IDs (5 min TTL)
        $cacheKey = "genre_{$genreId}_song_ids";
        $songIds = \Cache::remember($cacheKey, 300, function () use ($genreId) {
            $genre = Genre::find($genreId);
            if (!$genre) return [];

            return $genre->songs()
                ->where('is_active', 1)
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
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
     * 🎲 SHUFFLE: Albüm şarkıları karışık sırayla
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getAlbumSongs(int $albumId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $album = Album::with('artist')->find($albumId);
        if (!$album) return ['songs' => [], 'transition' => null];

        // 🔥 CACHE: Album song IDs (5 min TTL)
        $cacheKey = "album_{$albumId}_song_ids";
        $albumSongIds = \Cache::remember($cacheKey, 300, function () use ($album) {
            return $album->songs()
                ->where('is_active', 1)
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($albumSongIds)) {
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 SHUFFLE: Exclude sonrası shuffle
        $remainingIds = array_diff($albumSongIds, $excludeSongIds);

        // 🔄 Album bitti → Genre'ye geç
        if (empty($remainingIds)) {
            $albumGenreId = $album->songs()->where('is_active', 1)->value('genre_id');

            if ($albumGenreId) {
                $genre = Genre::find($albumGenreId);
                // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                $genreSongs = $this->getGenreSongsOnly($albumGenreId, $limit, $excludeSongIds);

                $genreTitle = $this->extractTitle($genre, 'Tür');
                $albumTitle = $this->extractTitle($album, 'Albüm');
                return [
                    'songs' => $genreSongs,
                    'transition' => [
                        'type' => 'genre',
                        'id' => $albumGenreId,
                        'name' => $genreTitle,
                        'reason' => "Albüm '{$albumTitle}' bitti, {$genreTitle} türünde devam ediliyor"
                    ]
                ];
            }
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 Shuffle ve seç
        $remainingIds = array_values($remainingIds);
        shuffle($remainingIds);
        $selectedIds = array_slice($remainingIds, 0, $limit);

        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get()
            ->sortBy(fn($song) => array_search($song->song_id, $selectedIds))
            ->values();

        return ['songs' => $this->formatSongs($songs), 'transition' => null];
    }

    /**
     * Get songs by playlist (SYSTEM PLAYLISTS)
     * 🔄 TRANSITION: Playlist biter → Genre'ye geç
     * 🎲 SHUFFLE: Sistem playlist'leri karışık sırayla oynar
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getPlaylistSongs(int $playlistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $playlist = Playlist::find($playlistId);
        if (!$playlist) return ['songs' => [], 'transition' => null];

        // 🔥 CACHE: Playlist song IDs (5 min TTL)
        $cacheKey = "playlist_{$playlistId}_song_ids";
        $playlistSongIds = \Cache::remember($cacheKey, 300, function () use ($playlistId) {
            return DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlistId)
                ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
                ->where('muzibu_songs.is_active', 1)
                // ->whereNotNull('muzibu_songs.hls_path') // GEÇİCİ
                ->pluck('muzibu_playlist_song.song_id')
                ->toArray();
        });

        if (empty($playlistSongIds)) {
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 SHUFFLE: Exclude sonrası shuffle
        $remainingIds = array_diff($playlistSongIds, $excludeSongIds);

        // 🔄 Playlist bitti → Genre'ye geç
        if (empty($remainingIds)) {
            $anySong = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlistId)
                ->first();

            if ($anySong) {
                $song = Song::with('genre')->find($anySong->song_id);
                if ($song && $song->genre_id) {
                    $genre = $song->genre;
                    // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                    $genreSongs = $this->getGenreSongsOnly($song->genre_id, $limit, $excludeSongIds);
                    $playlistTitle = $this->extractTitle($playlist, 'Playlist');
                    $genreTitle = $this->extractTitle($genre, 'Tür');

                    return [
                        'songs' => $genreSongs,
                        'transition' => [
                            'type' => 'genre',
                            'id' => $song->genre_id,
                            'name' => $genreTitle,
                            'reason' => "Playlist '{$playlistTitle}' bitti, {$genreTitle} türünde devam ediliyor"
                        ]
                    ];
                }
            }
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 Shuffle ve seç
        $remainingIds = array_values($remainingIds);
        shuffle($remainingIds);
        $selectedIds = array_slice($remainingIds, 0, $limit);

        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get()
            ->sortBy(fn($song) => array_search($song->song_id, $selectedIds))
            ->values();

        return ['songs' => $this->formatSongs($songs), 'transition' => null];
    }

    /**
     * Get songs by user playlist (USER-CREATED PLAYLISTS)
     * 🔄 TRANSITION: Playlist biter → Genre'ye geç
     * 🎯 ORDERED: Kullanıcı playlist'leri SIRALI oynar (position sırası)
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getUserPlaylistSongs(int $playlistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $playlist = Playlist::find($playlistId);
        if (!$playlist) return ['songs' => [], 'transition' => null];

        // 🎯 ORDERED: Pivot sırasıyla al (position → song_id)
        $pivotData = DB::table('muzibu_playlist_song')
            ->where('playlist_id', $playlistId)
            ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
            ->where('muzibu_songs.is_active', 1)
            // ->whereNotNull('muzibu_songs.hls_path') // GEÇİCİ
            ->when(!empty($excludeSongIds), function ($query) use ($excludeSongIds) {
                $query->whereNotIn('muzibu_playlist_song.song_id', $excludeSongIds);
            })
            ->orderBy('muzibu_playlist_song.position', 'asc')
            ->orderBy('muzibu_playlist_song.song_id', 'asc')
            ->take($limit)
            ->pluck('muzibu_playlist_song.song_id')
            ->toArray();

        // 🔄 Playlist bitti → Genre'ye geç
        if (empty($pivotData)) {
            $anySong = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlistId)
                ->first();

            if ($anySong) {
                $song = Song::with('genre')->find($anySong->song_id);
                if ($song && $song->genre_id) {
                    $genre = $song->genre;
                    // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                    $genreSongs = $this->getGenreSongsOnly($song->genre_id, $limit, $excludeSongIds);
                    $playlistTitle = $this->extractTitle($playlist, 'Playlist');
                    $genreTitle = $this->extractTitle($genre, 'Tür');

                    return [
                        'songs' => $genreSongs,
                        'transition' => [
                            'type' => 'genre',
                            'id' => $song->genre_id,
                            'name' => $genreTitle,
                            'reason' => "Playlist '{$playlistTitle}' bitti, {$genreTitle} türünde devam ediliyor"
                        ]
                    ];
                }
            }
            return ['songs' => [], 'transition' => null];
        }

        // 🎯 ORDERED: Pivot sırasına göre fetch
        $songs = Song::whereIn('song_id', $pivotData)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get()
            ->sortBy(fn($song) => array_search($song->song_id, $pivotData))
            ->values();

        return ['songs' => $this->formatSongs($songs), 'transition' => null];
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
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
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
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
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
     * 🔄 TRANSITION: Popular biter → Son dinlenen şarkının genre'sine geç
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getPopularSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        // 🔥 CACHE: Top 100 popular song IDs (5 min TTL)
        $cacheKey = 'popular_song_ids_top100';
        $popularSongIds = \Cache::remember($cacheKey, 300, function () {
            return Song::where('is_active', 1)
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
                ->orderBy('play_count', 'desc')
                ->take(100)
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($popularSongIds)) {
            return ['songs' => [], 'transition' => null];
        }

        // Exclude sonrası kalan şarkı sayısını kontrol et
        $remainingIds = array_diff($popularSongIds, $excludeSongIds);

        // 🔄 Popular tükendi → Son dinlenen şarkının genre'sine geç
        if (count($remainingIds) < $limit && !empty($excludeSongIds)) {
            // Son dinlenen şarkının genre'sini bul (excludeSongIds[0] = en son dinlenen)
            $lastPlayedSongId = $excludeSongIds[0] ?? null;
            if ($lastPlayedSongId) {
                $lastSong = Song::with('genre')->find($lastPlayedSongId);
                if ($lastSong && $lastSong->genre_id) {
                    $genre = $lastSong->genre;
                    // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                    $genreSongs = $this->getGenreSongsOnly($lastSong->genre_id, $limit, $excludeSongIds);
                    $genreTitle = $this->extractTitle($genre, 'Tür');

                    return [
                        'songs' => $genreSongs,
                        'transition' => [
                            'type' => 'genre',
                            'id' => $lastSong->genre_id,
                            'name' => $genreTitle,
                            'reason' => "Popüler şarkılar bitti, {$genreTitle} türünde devam ediliyor"
                        ]
                    ];
                }
            }
        }

        return ['songs' => $this->getRandomSongsFromIds($popularSongIds, $limit, $excludeSongIds), 'transition' => null];
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
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
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
     * 🎯 ORDERED: Favoriler ekleme sırasına göre (created_at desc → en yenisi önce)
     * 🔄 TRANSITION: Favorites biter → Son dinlenen şarkının genre'sine geç
     * Note: User-specific, no cache for ordered queries
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getFavoriteSongs(int $offset, int $limit, array $excludeSongIds = []): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return ['songs' => [], 'transition' => null];
        }

        // 🎯 ORDERED: Favorilere eklenme sırasına göre (en yeni önce)
        $favoriteData = DB::table('favorites')
            ->where('user_id', $userId)
            ->where('favoritable_type', Song::class)
            ->join('muzibu_songs', 'favorites.favoritable_id', '=', 'muzibu_songs.song_id')
            ->where('muzibu_songs.is_active', 1)
            // ->whereNotNull('muzibu_songs.hls_path') // GEÇİCİ
            ->when(!empty($excludeSongIds), function ($query) use ($excludeSongIds) {
                $query->whereNotIn('favorites.favoritable_id', $excludeSongIds);
            })
            ->orderBy('favorites.created_at', 'desc') // En yeni favori önce
            ->take($limit)
            ->pluck('favorites.favoritable_id')
            ->toArray();

        // 🔄 Favorites bitti → Son dinlenen şarkının genre'sine geç
        if (empty($favoriteData)) {
            // Tüm favorileri (exclude dahil) kontrol et
            $anyFavorite = DB::table('favorites')
                ->where('user_id', $userId)
                ->where('favoritable_type', Song::class)
                ->first();

            if ($anyFavorite && !empty($excludeSongIds)) {
                $lastPlayedSongId = $excludeSongIds[0] ?? null;
                if ($lastPlayedSongId) {
                    $lastSong = Song::with('genre')->find($lastPlayedSongId);
                    if ($lastSong && $lastSong->genre_id) {
                        $genre = $lastSong->genre;
                        // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                        $genreSongs = $this->getGenreSongsOnly($lastSong->genre_id, $limit, $excludeSongIds);
                        $genreTitle = $this->extractTitle($genre, 'Tür');

                        return [
                            'songs' => $genreSongs,
                            'transition' => [
                                'type' => 'genre',
                                'id' => $lastSong->genre_id,
                                'name' => $genreTitle,
                                'reason' => "Favori şarkılar bitti, {$genreTitle} türünde devam ediliyor"
                            ]
                        ];
                    }
                }
            }
            return ['songs' => [], 'transition' => null];
        }

        // 🎯 ORDERED: Fetch songs in favorite order
        $songs = Song::whereIn('song_id', $favoriteData)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get()
            ->sortBy(fn($song) => array_search($song->song_id, $favoriteData))
            ->values();

        return ['songs' => $this->formatSongs($songs), 'transition' => null];
    }

    /**
     * Get songs by artist
     * 🎲 SHUFFLE: Sanatçı şarkıları karışık sırayla oynar
     * 🔄 TRANSITION: Artist biter → Genre'ye geç
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getArtistSongs(int $artistId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        $artist = \Modules\Muzibu\App\Models\Artist::find($artistId);
        if (!$artist) return ['songs' => [], 'transition' => null];

        // 🔥 CACHE: Artist song IDs (5 min TTL)
        $cacheKey = "artist_{$artistId}_song_ids";
        $artistSongIds = \Cache::remember($cacheKey, 300, function () use ($artistId) {
            return Song::whereHas('album', function ($query) use ($artistId) {
                    $query->where('artist_id', $artistId);
                })
                ->where('is_active', 1)
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($artistSongIds)) {
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 SHUFFLE: Exclude sonrası shuffle
        $remainingIds = array_diff($artistSongIds, $excludeSongIds);

        // 🔄 Artist bitti → Genre'ye geç
        if (empty($remainingIds)) {
            $anySong = Song::whereIn('song_id', $artistSongIds)
                ->where('is_active', 1)
                ->with('genre')
                ->first();

            if ($anySong && $anySong->genre_id) {
                $genre = $anySong->genre;
                // 🐛 FIX: exclude_song_ids'i genre'ye de geçir (duplicate önleme)
                $genreSongs = $this->getGenreSongsOnly($anySong->genre_id, $limit, $excludeSongIds);
                $artistTitle = $this->extractTitle($artist, 'Sanatçı');
                $genreTitle = $this->extractTitle($genre, 'Tür');

                return [
                    'songs' => $genreSongs,
                    'transition' => [
                        'type' => 'genre',
                        'id' => $anySong->genre_id,
                        'name' => $genreTitle,
                        'reason' => "Sanatçı '{$artistTitle}' şarkıları bitti, {$genreTitle} türünde devam ediliyor"
                    ]
                ];
            }
            return ['songs' => [], 'transition' => null];
        }

        // 🎲 Shuffle ve seç
        $remainingIds = array_values($remainingIds);
        shuffle($remainingIds);
        $selectedIds = array_slice($remainingIds, 0, $limit);

        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get()
            ->sortBy(fn($song) => array_search($song->song_id, $selectedIds))
            ->values();

        return ['songs' => $this->formatSongs($songs), 'transition' => null];
    }

    /**
     * Get songs by search
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getSearchSongs(int $songId, int $offset, int $limit, array $excludeSongIds = []): array
    {
        // For search, get the selected song's album songs
        $song = Song::find($songId);
        if (!$song || !$song->album_id) {
            return ['songs' => [], 'transition' => null];
        }

        return $this->getAlbumSongs($song->album_id, $offset, $limit, $excludeSongIds);
    }

    /**
     * Get songs for single song context
     * 🎵 Tek şarkıya tıklandığında: Albüm varsa albümden, yoksa genre'den devam
     *
     * Davranış:
     * - Albüm varsa → Albüm şarkılarını shuffle (albüm context gibi)
     * - Albüm yoksa → Genre şarkılarını shuffle
     * - İkisi de yoksa → Popüler şarkılar
     *
     * @return array ['songs' => array, 'transition' => array|null]
     */
    private function getSongContext(int $songId, int $limit, array $excludeSongIds = []): array
    {
        $song = Song::with(['album.artist', 'genre'])->find($songId);

        if (!$song) {
            return ['songs' => [], 'transition' => null];
        }

        // 🎵 Albüm varsa → Albüm context'ine geç (shuffle)
        if ($song->album_id) {
            $albumTitle = $this->extractTitle($song->album, 'Albüm');
            $albumSongs = $this->getAlbumSongs($song->album_id, 0, $limit, $excludeSongIds);

            // Transition bilgisi ekle - kullanıcı neye geçtiğini görsün
            if (!isset($albumSongs['transition']) || !$albumSongs['transition']) {
                $albumSongs['transition'] = [
                    'type' => 'album',
                    'id' => $song->album_id,
                    'name' => $albumTitle,
                    'reason' => "'{$song->title}' şarkısının albümünden devam ediliyor"
                ];
            }

            return $albumSongs;
        }

        // 🎵 Genre varsa → Genre context'ine geç
        if ($song->genre_id) {
            $genreTitle = $this->extractTitle($song->genre, 'Tür');
            $genreSongs = $this->getGenreSongsOnly($song->genre_id, $limit, $excludeSongIds);

            return [
                'songs' => $genreSongs,
                'transition' => [
                    'type' => 'genre',
                    'id' => $song->genre_id,
                    'name' => $genreTitle,
                    'reason' => "'{$song->title}' şarkısının türünden devam ediliyor"
                ]
            ];
        }

        // 🎵 Hiçbiri yoksa → Popüler şarkılar
        return $this->getPopularSongs(0, $limit, $excludeSongIds);
    }

    /**
     * 🚀 OPTIMIZED HELPER: Get random songs from cached IDs
     * Uses PHP shuffle instead of ORDER BY RAND() (10x faster)
     *
     * @param array $songIds All available song IDs
     * @param int $limit Number of songs to return
     * @param array $excludeIds Songs to exclude (already played)
     * @return array Formatted songs
     *
     * 🎯 EXCLUDE LOGIC:
     * - Normalde son dinlenenler hariç tutulur (tekrar gelmesin)
     * - Şarkılar tükenirse (count < limit), orijinal havuza dön (♾️ sonsuz döngü)
     * - "şarkılar bittiyse çıkarabilir" - tükenince tekrar gelebilir
     */
    private function getRandomSongsFromIds(array $songIds, int $limit, array $excludeIds = []): array
    {
        // 💾 Orijinal havuzu sakla (exclude öncesi)
        $originalPool = $songIds;
        $originalCount = count($originalPool);

        // 🚫 Son dinlenenleri hariç tut
        if (!empty($excludeIds)) {
            $songIds = array_values(array_diff($songIds, $excludeIds));
        }

        $afterExcludeCount = count($songIds);

        // 🔄 INFINITE LOOP: Şarkılar tükendiyse orijinal havuza dön
        // "şarkılar bittiyse çıkarabilir" - tükenince tekrar gelebilir
        if ($afterExcludeCount < $limit && $originalCount >= $limit) {
            \Log::info('🔄 SONGS EXHAUSTED - Allowing replay (infinite loop)', [
                'after_exclude' => $afterExcludeCount,
                'needed' => $limit,
                'original_pool' => $originalCount,
                'excluded_count' => count($excludeIds),
            ]);
            $songIds = $originalPool; // ♾️ Orijinal havuza dön (exclude'lar dahil)
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

        // 🔧 FIX: whereIn sırayı korumaz! Shuffle sırasına göre sırala
        $songs = $songs->sortBy(function ($song) use ($selectedIds) {
            return array_search($song->song_id, $selectedIds);
        })->values();

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
                $algorithm = "🎲 Karışık oynatma (sistem playlist, bitince genre'ye geç)";
                break;

            case 'user_playlist':
                $playlist = Playlist::find($id);
                $sourceName = $playlist?->title ?? "Playlist #{$id}";
                $totalSongs = $playlist?->songs()->where('is_active', 1)->count() ?? 0;
                $algorithm = "📋 Sıralı oynatma (kullanıcı playlist, bitince genre'ye geç)";
                break;

            case 'album':
                $album = Album::with('artist')->find($id);
                $sourceName = $album?->title ?? "Albüm #{$id}";
                $totalSongs = $album?->songs()->where('is_active', 1)->count() ?? 0;
                $algorithm = "🎲 Karışık oynatma (albüm, bitince genre'ye geç)";
                break;

            case 'artist':
                $sourceName = "Sanatçı #{$id}";
                $algorithm = "🎲 Karışık oynatma (sanatçı, bitince genre'ye geç)";
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
                $algorithm = "📋 Sıralı oynatma (eklenme sırasına göre, bitince genre'ye geç)";
                break;

            case 'recent':
                $sourceName = 'Son Eklenenler';
                $totalSongs = Song::where('is_active', 1)->count();
                $algorithm = "🎲 Rastgele seçim (son 200'den, son 300 hariç)";
                break;

            case 'song':
                $song = Song::with('album')->find($id);
                $sourceName = $song?->title ?? "Şarkı #{$id}";
                $algorithm = "🎵 Tek şarkı (albüm/genre'ye geçiş)";
                break;

            case 'search':
                $sourceName = 'Arama Sonucu';
                $algorithm = "🔍 Arama (albüm şarkılarına geçiş)";
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
                        // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
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
     *
     * 🎯 EXCLUDE LOGIC: Son dinlenenler hariç, tükenirse tekrar gelebilir
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
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($songIds)) {
            return [];
        }

        // 💾 Orijinal havuzu sakla
        $originalPool = $songIds;
        $originalCount = count($originalPool);

        // 🚫 Son dinlenenleri hariç tut
        if (!empty($excludeIds)) {
            $songIds = array_values(array_diff($songIds, $excludeIds));
        }

        $afterExcludeCount = count($songIds);

        // 🔄 INFINITE LOOP: Tükendiyse orijinal havuza dön
        if ($afterExcludeCount < $limit && $originalCount >= $limit) {
            $songIds = $originalPool;
        }

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

        // 🔧 FIX: whereIn sırayı korumaz! Shuffle sırasına göre sırala
        $songs = $songs->sortBy(function ($song) use ($selectedIds) {
            return array_search($song->song_id, $selectedIds);
        })->values();

        return $this->formatSongs($songs);
    }

    /**
     * 🚀 CACHED POPULAR: Guest users get cached popular songs
     * ⚠️ NOT: Cache içinde shuffle var - her request'te farklı sıra için cache'i kısa tut
     */
    private function getCachedPopularSongs(int $limit): array
    {
        // 🔥 Top 50 ID'leri cache'le (5 min), shuffle her request'te
        $cacheKey = 'popular_song_ids_top50';
        $topSongIds = \Cache::remember($cacheKey, 300, function () {
            return Song::where('is_active', 1)
                // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, file_path ile çalışıyor
                ->orderBy('play_count', 'desc')
                ->take(50)
                ->pluck('song_id')
                ->toArray();
        });

        if (empty($topSongIds)) {
            return [];
        }

        // 🎲 Her request'te shuffle (cache dışında!)
        shuffle($topSongIds);
        $selectedIds = array_slice($topSongIds, 0, $limit);

        $songs = Song::whereIn('song_id', $selectedIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->get();

        // 🔧 FIX: Shuffle sırasına göre sırala
        $songs = $songs->sortBy(function ($song) use ($selectedIds) {
            return array_search($song->song_id, $selectedIds);
        })->values();

        return $this->formatSongs($songs);
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
