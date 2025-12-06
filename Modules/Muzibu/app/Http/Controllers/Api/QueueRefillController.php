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
            ]);

            $type = $context['type'];
            $id = $context['id'] ?? null;
            $offset = $context['offset'] ?? 0;
            $limit = $context['limit'] ?? 15;

            $songs = match($type) {
                'genre' => $this->getGenreSongs($id, $offset, $limit),
                'album' => $this->getAlbumSongs($id, $offset, $limit),
                'playlist' => $this->getPlaylistSongs($id, $offset, $limit),
                'user_playlist' => $this->getUserPlaylistSongs($id, $offset, $limit),
                'sector' => $this->getSectorSongs($id, $offset, $limit),
                'radio' => $this->getRadioSongs($id, $offset, $limit),
                'popular' => $this->getPopularSongs($offset, $limit),
                'recent' => $this->getRecentSongs($offset, $limit, $context['subType'] ?? null),
                'favorites' => $this->getFavoriteSongs($offset, $limit),
                'artist' => $this->getArtistSongs($id, $offset, $limit),
                'search' => $this->getSearchSongs($id, $offset, $limit),
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

            return response()->json([
                'success' => true,
                'context' => $context,
                'songs' => $songs,
                'count' => count($songs),
                'transition' => $transitionSuggestion, // Frontend will auto-update context
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
     */
    private function getGenreSongs(int $genreId, int $offset, int $limit): array
    {
        $genre = Genre::find($genreId);
        if (!$genre) {
            return [];
        }

        // Get total count first
        $totalCount = $genre->songs()->where('is_active', 1)->count();
        
        if ($totalCount === 0) {
            return [];
        }

        // ♾️ INFINITE LOOP: If offset exceeds total, wrap around (başa sar)
        $actualOffset = $offset % $totalCount;

        $songs = $genre->songs()
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->skip($actualOffset)
            ->take($limit)
            ->get();

        // If we didn't get enough songs, wrap around and get from beginning
        if ($songs->count() < $limit && $actualOffset > 0) {
            $remaining = $limit - $songs->count();
            $moreSongs = $genre->songs()
                ->where('is_active', 1)
                ->with(['album.artist'])
                ->take($remaining)
                ->get();
            
            $songs = $songs->merge($moreSongs);
        }

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by album
     */
    private function getAlbumSongs(int $albumId, int $offset, int $limit): array
    {
        $album = Album::find($albumId);
        if (!$album) {
            return [];
        }

        $songs = $album->songs()
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->orderBy('song_id', 'asc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by playlist
     */
    private function getPlaylistSongs(int $playlistId, int $offset, int $limit): array
    {
        $playlist = Playlist::find($playlistId);
        if (!$playlist) {
            return [];
        }

        $songs = $playlist->songs()
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by user playlist
     */
    private function getUserPlaylistSongs(int $playlistId, int $offset, int $limit): array
    {
        // User playlists use same table, just filter by user
        return $this->getPlaylistSongs($playlistId, $offset, $limit);
    }

    /**
     * Get songs by sector (sector playlists - infinite)
     */
    private function getSectorSongs(int $sectorId, int $offset, int $limit): array
    {
        $sector = Sector::find($sectorId);
        if (!$sector) {
            return [];
        }

        // Get all playlists in this sector
        $playlists = $sector->playlists()->where('is_active', 1)->get();
        
        if ($playlists->isEmpty()) {
            return [];
        }

        // Collect all songs from all playlists
        $allSongs = collect();
        foreach ($playlists as $playlist) {
            $playlistSongs = $playlist->songs()
                ->where('is_active', 1)
                ->with(['album.artist'])
                ->get();
            $allSongs = $allSongs->merge($playlistSongs);
        }

        // Apply offset and limit
        $songs = $allSongs->skip($offset)->take($limit);

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by radio (radio playlists - infinite)
     */
    private function getRadioSongs(int $radioId, int $offset, int $limit): array
    {
        $radio = Radio::find($radioId);
        if (!$radio) {
            return [];
        }

        // Radios have assigned playlists
        $playlists = $radio->playlists()->where('is_active', 1)->get();
        
        if ($playlists->isEmpty()) {
            return [];
        }

        // Collect all songs from all playlists
        $allSongs = collect();
        foreach ($playlists as $playlist) {
            $playlistSongs = $playlist->songs()
                ->where('is_active', 1)
                ->with(['album.artist'])
                ->get();
            $allSongs = $allSongs->merge($playlistSongs);
        }

        // Shuffle for radio feel
        $allSongs = $allSongs->shuffle();

        // Apply offset and limit
        $songs = $allSongs->skip($offset)->take($limit);

        return $this->formatSongs($songs);
    }

    /**
     * Get popular songs
     */
    private function getPopularSongs(int $offset, int $limit): array
    {
        // Most played songs (from play count or rating)
        $songs = Song::where('is_active', 1)
            ->with(['album.artist'])
            ->orderBy('play_count', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get recent songs (continues backward from last ID)
     */
    private function getRecentSongs(int $offset, int $limit, ?string $subType = null): array
    {
        // Recently added songs (newest first)
        $query = Song::where('is_active', 1)
            ->with(['album.artist'])
            ->orderBy('created_at', 'desc');

        $songs = $query->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get favorite songs
     */
    private function getFavoriteSongs(int $offset, int $limit): array
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

        $songs = Song::whereIn('song_id', $favoriteSongIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by artist
     */
    private function getArtistSongs(int $artistId, int $offset, int $limit): array
    {
        // Get albums by artist, then songs
        $albumIds = Album::where('artist_id', $artistId)->pluck('album_id');

        if ($albumIds->isEmpty()) {
            return [];
        }

        $songs = Song::whereIn('album_id', $albumIds)
            ->where('is_active', 1)
            ->with(['album.artist'])
            ->skip($offset)
            ->take($limit)
            ->get();

        return $this->formatSongs($songs);
    }

    /**
     * Get songs by search
     */
    private function getSearchSongs(int $songId, int $offset, int $limit): array
    {
        // For search, get the selected song's album songs
        $song = Song::find($songId);
        if (!$song || !$song->album_id) {
            return [];
        }

        return $this->getAlbumSongs($song->album_id, $offset, $limit);
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
                'hls_path' => $song->hls_path,                'lyrics' => $song->lyrics,
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
}
