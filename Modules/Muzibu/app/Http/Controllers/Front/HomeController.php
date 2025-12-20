<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\{Playlist, Album, Song, Genre};

class HomeController extends Controller
{
    public function index(): View
    {
        try {
            //🔒 FIXED: Use Eloquent (tenant-aware) + Eager Loading
            // Only show playlists with at least 1 active song
            $featuredPlaylists = Playlist::where('is_active', 1)
                ->where('is_system', 1)
                ->whereHas('songs', function($q) {
                    $q->where('is_active', 1);
                })
                ->withCount(['songs' => function($q) {
                    $q->where('is_active', 1);
                }])
                ->with(['songs' => function($q) {
                    $q->where('is_active', 1);
                }, 'coverMedia'])
                ->limit(10)
                ->get();

            // Only show albums with at least 1 active song
            $newReleases = Album::where('is_active', 1)
                ->whereHas('songs', function($q) {
                    $q->where('is_active', 1);
                })
                ->withCount(['songs' => function($q) {
                    $q->where('is_active', 1);
                }])
                ->with(['artist', 'songs' => function($q) {
                    $q->where('is_active', 1);
                }, 'coverMedia'])
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            // Only show songs that have HLS ready
            $popularSongs = Song::where('is_active', 1)
                ->whereNotNull('file_path') // CRITICAL: Skip songs without files
                ->whereNotNull('hls_path') // 🔥 CRITICAL: Only HLS-ready songs
                ->with(['album.artist', 'album.coverMedia', 'coverMedia']) // Load cover media for songs and albums
                ->orderBy('play_count', 'desc')
                ->limit(20) // Limit to 20 popular songs
                ->get();

            // Only show genres with at least 1 active song
            $genres = Genre::where('is_active', 1)
                ->whereHas('songs', function($q) {
                    $q->where('is_active', 1);
                })
                ->with(['songs' => function($q) {
                    $q->where('is_active', 1);
                }, 'iconMedia'])
                ->get();

            // Get user's personal playlists (if logged in) - only with active songs
            $userPlaylists = auth()->check()
                ? Playlist::where('user_id', auth()->id())
                    ->where('is_active', 1)
                    ->whereHas('songs', function($q) {
                        $q->where('is_active', 1);
                    })
                    ->withCount(['songs' => function($q) {
                        $q->where('is_active', 1);
                    }])
                    ->with(['songs' => function($q) {
                        $q->where('is_active', 1);
                    }, 'coverMedia'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                : collect([]);

            return view('themes.muzibu.index', compact(
                'featuredPlaylists',
                'newReleases',
                'popularSongs',
                'genres',
                'userPlaylists'
            ));

        } catch (\Exception $e) {
            \Log::error('HomeController error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Return empty data on error
            return view('themes.muzibu.index', [
                'featuredPlaylists' => collect([]),
                'newReleases' => collect([]),
                'popularSongs' => collect([]),
                'genres' => collect([]),
                'userPlaylists' => collect([]),
            ]);
        }
    }
}
