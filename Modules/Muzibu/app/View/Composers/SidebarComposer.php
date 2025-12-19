<?php

namespace Modules\Muzibu\app\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
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
        // Featured Playlists - CACHED
        if (!$view->offsetExists('featuredPlaylists')) {
            $featuredPlaylists = Cache::remember('sidebar_featured_playlists', 300, function () {
                return Playlist::where('is_active', 1)
                    ->where('is_system', 1)
                    ->with(['songs', 'coverMedia'])
                    ->limit(5)
                    ->get();
            });

            $view->with('featuredPlaylists', $featuredPlaylists);
        }

        // Popular Songs - CACHED
        if (!$view->offsetExists('popularSongs')) {
            $popularSongs = Cache::remember('sidebar_popular_songs', 300, function () {
                return Song::where('is_active', 1)
                    ->with(['artist'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            });

            $view->with('popularSongs', $popularSongs);
        }

        // Recent Albums - CACHED
        if (!$view->offsetExists('recentAlbums')) {
            $recentAlbums = Cache::remember('sidebar_recent_albums', 300, function () {
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
            $genres = Cache::remember('sidebar_genres', 300, function () {
                return Genre::where('is_active', 1)
                    ->limit(6)
                    ->get();
            });

            $view->with('genres', $genres);
        }
    }
}
