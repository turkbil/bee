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
            $featuredPlaylists = Playlist::where('is_active', 1)
                ->where('is_system', 1)
                ->with(['songs', 'coverMedia']) // Load coverMedia relationship
                ->limit(10)
                ->get();

            $newReleases = Album::where('is_active', 1)
                ->with(['artist', 'songs', 'coverMedia']) // Load coverMedia relationship
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            // Only show songs that have files uploaded
            $popularSongs = Song::where('is_active', 1)
                ->whereNotNull('file_path') // CRITICAL: Skip songs without files
                ->with(['album.artist'])
                ->orderBy('hls_converted', 'desc') // HLS songs first
                ->orderBy('play_count', 'desc')
                ->limit(20) // Limit to 20 popular songs
                ->get();

            $genres = Genre::where('is_active', 1)
                ->with(['songs', 'iconMedia']) // Load iconMedia relationship
                ->get();

            // Get user's personal playlists (if logged in)
            $userPlaylists = auth()->check()
                ? Playlist::where('user_id', auth()->id())
                    ->where('is_active', 1)
                    ->with(['songs', 'coverMedia'])
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
                'trace' => $e->getTraceAsString()
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
