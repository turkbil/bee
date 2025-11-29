<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Playlist;

class PlaylistController extends Controller
{
    /**
     * Display playlists list
     */
    public function index()
    {
        $playlists = Playlist::where('is_active', 1)
            ->where('is_public', 1)
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('themes.muzibu.playlists.index', compact('playlists'));
    }

    /**
     * Display playlist detail
     */
    public function show($slug)
    {
        $playlist = Playlist::where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = $playlist->songs()
            ->with('artist')
            ->where('muzibu_songs.is_active', 1)
            ->orderBy('muzibu_playlist_song.position')
            ->get();

        return view('themes.muzibu.playlists.show', compact('playlist', 'songs'));
    }
}
