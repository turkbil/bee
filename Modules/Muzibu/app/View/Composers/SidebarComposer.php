<?php

namespace Modules\Muzibu\app\View\Composers;

use Illuminate\View\View;
use Modules\Muzibu\app\Models\Playlist;
use Modules\Muzibu\app\Models\Song;
use Modules\Muzibu\app\Models\Album;
use Modules\Muzibu\app\Models\Genre;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Featured Playlists
        if (!$view->offsetExists('featuredPlaylists')) {
            $featuredPlaylists = Playlist::where('is_active', 1)
                ->where('is_system', 1)
                ->with(['songs', 'coverMedia'])
                ->limit(5)
                ->get();

            $view->with('featuredPlaylists', $featuredPlaylists);
        }

        // Popular Songs (by play_count or recent)
        if (!$view->offsetExists('popularSongs')) {
            $popularSongs = Song::where('is_active', 1)
                ->with(['artist'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $view->with('popularSongs', $popularSongs);
        }

        // Recent Albums
        if (!$view->offsetExists('recentAlbums')) {
            $recentAlbums = Album::where('is_active', 1)
                ->with(['artist', 'coverMedia'])
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

            $view->with('recentAlbums', $recentAlbums);
        }

        // Genres
        if (!$view->offsetExists('genres')) {
            $genres = Genre::where('is_active', 1)
                ->limit(6)
                ->get();

            $view->with('genres', $genres);
        }
    }
}
