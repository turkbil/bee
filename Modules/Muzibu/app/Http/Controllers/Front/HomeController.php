<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Muzibu\App\Models\{Playlist, Album, Song, Genre, Radio, Sector};

class HomeController extends Controller
{
    /**
     * 🚀 OPTIMIZED: Full page caching (2089ms → ~200ms)
     * All queries are now cached for 5 minutes
     */
    public function index(): View
    {
        try {
            // 🔥 CACHE: Featured Playlists (5 min) - 8 items max
            $featuredPlaylists = Cache::remember('home_featured_playlists_v3', 300, function () {
                return Playlist::where('is_active', 1)
                    ->where('is_system', 1)
                    ->where('songs_count', '>', 0)
                    ->with(['coverMedia'])
                    ->limit(8)
                    ->get();
            });

            // 🔥 CACHE: New Releases (5 min) - 8 items max
            $newReleases = Cache::remember('home_new_releases_v3', 300, function () {
                return Album::where('is_active', 1)
                    ->where('songs_count', '>', 0)
                    ->with(['artist', 'coverMedia'])
                    ->orderBy('created_at', 'desc')
                    ->limit(8)
                    ->get();
            });

            // 🔥 CACHE: Popular Songs (5 min) - 10 items max (5+5 for two columns)
            $popularSongs = Cache::remember('home_popular_songs_v3', 300, function () {
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, MP3 ile çalışıyor
                    ->with(['album.artist', 'album.coverMedia', 'coverMedia'])
                    ->orderBy('play_count', 'desc')
                    ->limit(10)
                    ->get();
            });

            // 🔥 CACHE: New Songs (5 min) - 15 items max (for sidebar)
            $newSongs = Cache::remember('home_new_songs_v3', 300, function () {
                return Song::where('is_active', 1)
                    ->whereNotNull('file_path')
                    // ->whereNotNull('hls_path') // GEÇİCİ: HLS hazır değil, MP3 ile çalışıyor
                    ->with(['album.artist', 'album.coverMedia', 'coverMedia'])
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
                    ->get();
            });

            // 🔥 CACHE: Genres (5 min) - 8 items max
            $genres = Cache::remember('home_genres_v3', 300, function () {
                return Genre::where('is_active', 1)
                    ->where('songs_count', '>', 0)
                    ->with(['iconMedia'])
                    ->orderBy('songs_count', 'desc')
                    ->limit(8)
                    ->get();
            });

            // 🔥 CACHE: Radios (5 min) - 8 items max
            $radios = Cache::remember('home_radios_v3', 300, function () {
                return Radio::where('is_active', 1)
                    ->with(['logoMedia'])
                    ->limit(8)
                    ->get();
            });

            // 🔥 CACHE: Sectors (5 min) - 8 items max
            $sectors = Cache::remember('home_sectors_v3', 300, function () {
                return Sector::where('is_active', 1)
                    ->with(['iconMedia'])
                    ->limit(8)
                    ->get();
            });

            // User Playlists - NOT cached (user-specific, short TTL)
            $userPlaylists = auth()->check()
                ? Cache::remember('home_user_playlists_' . auth()->id(), 60, function () {
                    return Playlist::where('user_id', auth()->id())
                        ->where('is_active', 1)
                        ->where('songs_count', '>', 0)
                        ->with(['coverMedia'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                })
                : collect([]);

            return view('themes.muzibu.index', compact(
                'featuredPlaylists',
                'newReleases',
                'popularSongs',
                'newSongs',
                'genres',
                'radios',
                'sectors',
                'userPlaylists'
            ));

        } catch (\Exception $e) {
            \Log::error('HomeController error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('themes.muzibu.index', [
                'featuredPlaylists' => collect([]),
                'newReleases' => collect([]),
                'popularSongs' => collect([]),
                'newSongs' => collect([]),
                'genres' => collect([]),
                'radios' => collect([]),
                'sectors' => collect([]),
                'userPlaylists' => collect([]),
            ]);
        }
    }
}
