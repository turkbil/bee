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
            $featuredPlaylists = Cache::remember('sidebar_featured_playlists', 3600, function () {
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
            $newSongs = Cache::remember('sidebar_new_songs', 3600, function () {
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->with(['artist', 'album.coverMedia', 'coverMedia'])
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
                    ->get();
            });

            $view->with('newSongs', $newSongs);
        }

        // 📈 TREND SONGS - Son 7 günde en çok dinlenenler (5 dk cache)
        if (!$view->offsetExists('trendSongs')) {
            $trendSongs = Cache::remember('sidebar_trend_songs', 3600, function () {
                // Önce son 7 günü dene
                $songs = Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->where('updated_at', '>=', now()->subDays(7))
                    ->with(['artist', 'album.coverMedia', 'coverMedia'])
                    ->orderBy('play_count', 'desc')
                    ->limit(15)
                    ->get();

                // Fallback: Yeterli şarkı yoksa tüm zamanlardan al
                if ($songs->count() < 5) {
                    $songs = Song::where('is_active', 1)
                        ->whereNotNull('file_path')
                        ->whereNotNull('hls_path')
                        ->with(['artist', 'album.coverMedia', 'coverMedia'])
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
            $popularSongs = Cache::remember('sidebar_popular_songs', 3600, function () {
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    ->whereNotNull('hls_path')
                    ->with(['artist', 'album.coverMedia', 'coverMedia'])
                    ->orderBy('play_count', 'desc')
                    ->limit(15)
                    ->get();
            });

            $view->with('popularSongs', $popularSongs);
        }

        // Recent Albums - CACHED
        if (!$view->offsetExists('recentAlbums')) {
            $recentAlbums = Cache::remember('sidebar_recent_albums', 3600, function () {
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
            $genres = Cache::remember('sidebar_genres', 3600, function () {
                return Genre::where('is_active', 1)
                    ->limit(6)
                    ->get();
            });

            $view->with('genres', $genres);
        }
    }
}
