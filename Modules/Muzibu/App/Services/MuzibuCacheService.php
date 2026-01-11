<?php

namespace Modules\Muzibu\App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Sector;

/**
 * Muzibu Redis Cache Service
 *
 * Manages caching for Muzibu module to reduce database load
 * TTL Strategy:
 * - Song metadata: 24 hours (rarely changes)
 * - Playlists: 1 hour (frequently updated)
 * - Albums: 24 hours
 * - Genres/Sectors: 1 week (static data)
 */
class MuzibuCacheService
{
    // Cache key prefixes (tenant-aware)
    const PREFIX_SONG = 'muzibu:song:';
    const PREFIX_PLAYLIST = 'muzibu:playlist:';
    const PREFIX_ALBUM = 'muzibu:album:';
    const PREFIX_GENRE = 'muzibu:genre:';
    const PREFIX_SECTOR = 'muzibu:sector:';
    const PREFIX_FEATURED_PLAYLISTS = 'muzibu:featured_playlists';
    const PREFIX_POPULAR_SONGS = 'muzibu:popular_songs';

    // Cache TTL (in seconds)
    const TTL_SONG = 86400;        // 24 hours
    const TTL_PLAYLIST = 3600;     // 1 hour
    const TTL_ALBUM = 86400;       // 24 hours
    const TTL_GENRE = 604800;      // 1 week
    const TTL_SECTOR = 604800;     // 1 week
    const TTL_FEATURED = 3600;     // 1 hour
    const TTL_POPULAR = 1800;      // 30 minutes

    /**
     * Get cache key with tenant prefix
     */
    private function getCacheKey(string $prefix, $id): string
    {
        $tenantId = tenant() ? tenant()->id : 'default';
        return "{$prefix}{$tenantId}:{$id}";
    }

    /**
     * ==========================================
     * SONG CACHE
     * ==========================================
     */

    /**
     * Get song by ID (cached)
     */
    public function getSong(int $songId): ?Song
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_SONG, $songId);

        return Cache::remember($cacheKey, self::TTL_SONG, function () use ($songId) {
            return Song::with(['album.artist'])
                ->where('song_id', $songId)
                ->where('is_active', 1)
                ->first();
        });
    }

    /**
     * Get multiple songs by IDs (cached)
     */
    public function getSongs(array $songIds): array
    {
        $songs = [];

        foreach ($songIds as $songId) {
            $song = $this->getSong($songId);
            if ($song) {
                $songs[] = $song;
            }
        }

        return $songs;
    }

    /**
     * Get popular songs (cached)
     */
    public function getPopularSongs(int $limit = 20): array
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_POPULAR_SONGS, "limit:{$limit}");

        return Cache::remember($cacheKey, self::TTL_POPULAR, function () use ($limit) {
            return Song::where('is_active', 1)
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist', 'album.media']) // ✅ Include media for cover URLs
                ->orderBy('play_count', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate song cache
     */
    public function invalidateSong(int $songId): void
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_SONG, $songId);
        Cache::forget($cacheKey);

        // Also invalidate popular songs cache (play count might have changed)
        $this->invalidatePopularSongs();
    }

    /**
     * Invalidate popular songs cache
     */
    public function invalidatePopularSongs(): void
    {
        $tenantId = tenant() ? tenant()->id : 'default';
        $pattern = self::PREFIX_POPULAR_SONGS . $tenantId . ':*';

        // Delete all popular songs cache keys for this tenant
        Cache::store('redis')->getRedis()->del(
            Cache::store('redis')->getRedis()->keys($pattern)
        );
    }

    /**
     * ==========================================
     * PLAYLIST CACHE
     * ==========================================
     */

    /**
     * Get playlist by ID (cached)
     */
    public function getPlaylist(int $playlistId): ?Playlist
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_PLAYLIST, $playlistId);

        return Cache::remember($cacheKey, self::TTL_PLAYLIST, function () use ($playlistId) {
            return Playlist::with(['songs' => function($q) {
                    $q->where('is_active', 1);
                }, 'songs.album.artist'])
                ->where('playlist_id', $playlistId)
                ->where('is_active', 1)
                ->first();
        });
    }

    /**
     * Get featured playlists (cached)
     */
    public function getFeaturedPlaylists(int $limit = 10): array
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_FEATURED_PLAYLISTS, "limit:{$limit}");

        return Cache::remember($cacheKey, self::TTL_FEATURED, function () use ($limit) {
            return Playlist::where('is_active', 1)
                ->where('is_featured', 1)
                ->orderBy('sort_order', 'asc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate playlist cache
     */
    public function invalidatePlaylist(int $playlistId): void
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_PLAYLIST, $playlistId);
        Cache::forget($cacheKey);

        // Also invalidate featured playlists if this was featured
        $this->invalidateFeaturedPlaylists();
    }

    /**
     * Invalidate featured playlists cache
     */
    public function invalidateFeaturedPlaylists(): void
    {
        $tenantId = tenant() ? tenant()->id : 'default';
        $pattern = self::PREFIX_FEATURED_PLAYLISTS . $tenantId . ':*';

        Cache::store('redis')->getRedis()->del(
            Cache::store('redis')->getRedis()->keys($pattern)
        );
    }

    /**
     * ==========================================
     * ALBUM CACHE
     * ==========================================
     */

    /**
     * Get album by ID (cached)
     */
    public function getAlbum(int $albumId): ?Album
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_ALBUM, $albumId);

        return Cache::remember($cacheKey, self::TTL_ALBUM, function () use ($albumId) {
            return Album::with(['artist', 'songs' => function($q) {
                    $q->where('is_active', 1);
                }])
                ->where('album_id', $albumId)
                ->where('is_active', 1)
                ->first();
        });
    }

    /**
     * Invalidate album cache
     */
    public function invalidateAlbum(int $albumId): void
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_ALBUM, $albumId);
        Cache::forget($cacheKey);
    }

    /**
     * ==========================================
     * GENRE CACHE
     * ==========================================
     */

    /**
     * Get all genres (cached)
     */
    public function getAllGenres(): array
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_GENRE, 'all');

        return Cache::remember($cacheKey, self::TTL_GENRE, function () {
            return Genre::where('is_active', 1)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate all genres cache
     */
    public function invalidateGenres(): void
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_GENRE, 'all');
        Cache::forget($cacheKey);
    }

    /**
     * ==========================================
     * SECTOR CACHE
     * ==========================================
     */

    /**
     * Get all sectors (cached)
     */
    public function getAllSectors(): array
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_SECTOR, 'all');

        return Cache::remember($cacheKey, self::TTL_SECTOR, function () {
            return Sector::where('is_active', 1)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate all sectors cache
     */
    public function invalidateSectors(): void
    {
        $cacheKey = $this->getCacheKey(self::PREFIX_SECTOR, 'all');
        Cache::forget($cacheKey);
    }

    /**
     * ==========================================
     * UTILITY METHODS
     * ==========================================
     */

    /**
     * Flush all Muzibu cache for current tenant
     */
    public function flushAll(): void
    {
        $tenantId = tenant() ? tenant()->id : 'default';
        $pattern = "muzibu:*:{$tenantId}:*";

        Cache::store('redis')->getRedis()->del(
            Cache::store('redis')->getRedis()->keys($pattern)
        );
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $tenantId = tenant() ? tenant()->id : 'default';

        $redis = Cache::store('redis')->getRedis();

        return [
            'tenant_id' => $tenantId,
            'total_keys' => count($redis->keys("muzibu:*:{$tenantId}:*")),
            'song_keys' => count($redis->keys(self::PREFIX_SONG . "{$tenantId}:*")),
            'playlist_keys' => count($redis->keys(self::PREFIX_PLAYLIST . "{$tenantId}:*")),
            'album_keys' => count($redis->keys(self::PREFIX_ALBUM . "{$tenantId}:*")),
        ];
    }
}
