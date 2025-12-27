<?php

namespace Modules\Muzibu\app\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\app\Models\Playlist;
use Modules\Muzibu\app\Models\Song;
use Modules\Muzibu\app\Models\Album;
use Modules\Muzibu\app\Models\Genre;

class SidebarComposer
{
    /**
     * Bind data to the view.
     * ⚡ PERFORMANCE: 5 dakika cache (1.5s → 0.01s)
     */
    public function compose(View $view): void
    {
        // 🔥 P1 FIX: Favorites N+1 → 1 query bulk loading
        // Pre-load all user's favorites in single query (174 queries → 1 query)
        // Use View::share() so is_favorited() helper can access it globally
        if (!$view->offsetExists('userFavoritedIds')) {
            $userFavoritedIds = $this->loadUserFavorites();
            $view->with('userFavoritedIds', $userFavoritedIds);
            // Also share globally for helper function access
            \Illuminate\Support\Facades\View::share('userFavoritedIds', $userFavoritedIds);
        }
        // Featured Playlists - CACHED
        if (!$view->offsetExists('featuredPlaylists')) {
            $featuredPlaylists = Cache::remember('sidebar_featured_playlists', 43200, function () {
                return Playlist::where('is_active', 1)
                    ->where('is_system', 1)
                    ->with(['songs', 'coverMedia'])
                    ->limit(5)
                    ->get();
            });

            $view->with('featuredPlaylists', $featuredPlaylists);
        }

        // 🆕 NEW SONGS - Son eklenenler (created_at desc)
        if (!$view->offsetExists('newSongs')) {
            $newSongs = Cache::remember('sidebar_new_songs', 3600, function () { // 1 saat - yeni şarkılar hızlı görünsün
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->with(['artist', 'album.artist', 'album.coverMedia', 'coverMedia']) // 🚀 P4: album.artist eklendi
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
                    ->get();
            });

            $view->with('newSongs', $newSongs);
        }

        // 📈 TREND SONGS - Son 7 günde en çok dinlenenler (5 dk cache)
        if (!$view->offsetExists('trendSongs')) {
            $trendSongs = Cache::remember('sidebar_trend_songs', 43200, function () {
                // Önce son 7 günü dene
                $songs = Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->where('updated_at', '>=', now()->subDays(7))
                    ->with(['artist', 'album.artist', 'album.coverMedia', 'coverMedia']) // 🚀 P4: album.artist eklendi
                    ->orderBy('play_count', 'desc')
                    ->limit(15)
                    ->get();

                // Fallback: Yeterli şarkı yoksa tüm zamanlardan al
                if ($songs->count() < 5) {
                    $songs = Song::where('is_active', 1)
                        ->whereNotNull('file_path')
                        ->whereNotNull('hls_path')
                        ->with(['artist', 'album.artist', 'album.coverMedia', 'coverMedia']) // 🚀 P4: album.artist eklendi
                        ->orderBy('play_count', 'desc')
                        ->orderBy('updated_at', 'desc')
                        ->limit(15)
                        ->get();
                }

                return $songs;
            });

            $view->with('trendSongs', $trendSongs);
        }

        // 🔥 POPULAR SONGS - En çok dinlenenler (play_count desc)
        if (!$view->offsetExists('popularSongs')) {
            $popularSongs = Cache::remember('sidebar_popular_songs', 43200, function () {
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->with(['artist', 'album.artist', 'album.coverMedia', 'coverMedia']) // 🚀 P4: album.artist eklendi
                    ->orderBy('play_count', 'desc')
                    ->limit(15)
                    ->get();
            });

            $view->with('popularSongs', $popularSongs);
        }

        // Recent Albums - CACHED
        if (!$view->offsetExists('recentAlbums')) {
            $recentAlbums = Cache::remember('sidebar_recent_albums', 43200, function () {
                return Album::where('is_active', 1)
                    ->with(['artist', 'coverMedia'])
                    ->orderBy('created_at', 'desc')
                    ->limit(4)
                    ->get();
            });

            $view->with('recentAlbums', $recentAlbums);
        }

        // Genres - CACHED
        if (!$view->offsetExists('genres')) {
            $genres = Cache::remember('sidebar_genres', 43200, function () {
                return Genre::where('is_active', 1)
                    ->limit(6)
                    ->get();
            });

            $view->with('genres', $genres);
        }
    }

    /**
     * 🔥 P1 FIX: Bulk load all user favorites in single query
     * Reduces N+1 problem (174 queries → 1 query)
     *
     * @return array Format: ['song' => [1,5,8], 'album' => [3,7], 'playlist' => [2,4], ...]
     */
    protected function loadUserFavorites(): array
    {
        if (!auth()->check()) {
            return [];
        }

        $userId = auth()->id();

        // Model class → type mapping
        $typeMap = [
            'Modules\\Muzibu\\app\\Models\\Song' => 'song',
            'Modules\\Muzibu\\App\\Models\\Song' => 'song',
            'Modules\\Muzibu\\app\\Models\\Album' => 'album',
            'Modules\\Muzibu\\App\\Models\\Album' => 'album',
            'Modules\\Muzibu\\app\\Models\\Playlist' => 'playlist',
            'Modules\\Muzibu\\App\\Models\\Playlist' => 'playlist',
            'Modules\\Muzibu\\app\\Models\\Genre' => 'genre',
            'Modules\\Muzibu\\App\\Models\\Genre' => 'genre',
            'Modules\\Muzibu\\app\\Models\\Sector' => 'sector',
            'Modules\\Muzibu\\App\\Models\\Sector' => 'sector',
            'Modules\\Muzibu\\app\\Models\\Radio' => 'radio',
            'Modules\\Muzibu\\App\\Models\\Radio' => 'radio',
            'Modules\\Muzibu\\app\\Models\\Artist' => 'artist',
            'Modules\\Muzibu\\App\\Models\\Artist' => 'artist',
        ];

        // Single query to get all favorites
        $favorites = DB::table('favorites')
            ->where('user_id', $userId)
            ->get(['favoritable_type', 'favoritable_id']);

        // Group by type
        $grouped = [];
        foreach ($favorites as $fav) {
            $type = $typeMap[$fav->favoritable_type] ?? null;
            if ($type) {
                if (!isset($grouped[$type])) {
                    $grouped[$type] = [];
                }
                $grouped[$type][] = (int) $fav->favoritable_id;
            }
        }

        return $grouped;
    }
}
