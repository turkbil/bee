<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Playlist;
use App\Services\SeoMetaTagService;

class PlaylistController extends Controller
{
    /**
     * Display playlists list
     */
    public function index()
    {
        // Only show playlists with at least 1 active song
        $playlists = Playlist::with('coverMedia')
            ->where('is_active', 1)
            ->where('is_public', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(200);

        return view('themes.muzibu.playlists.index', compact('playlists'));
    }

    /**
     * Display playlist detail
     */
    public function show($slug)
    {
        $playlist = Playlist::with('coverMedia')
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = $playlist->songs()
            ->with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('muzibu_songs.is_active', 1)
            ->orderBy('muzibu_playlist_song.position')
            ->get();

        // ⭐ SEO için model'i share et (HasSeo trait otomatik çalışır)
        view()->share('currentModel', $playlist);

        return view('themes.muzibu.playlists.show', compact('playlist', 'songs'));
    }

    /**
     * 🚀 SPA API: Display playlists list (JSON)
     */
    public function apiIndex()
    {
        // Only show playlists with at least 1 active song
        $playlists = Playlist::with('coverMedia')
            ->where('is_active', 1)
            ->where('is_public', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(200);

        $html = view('themes.muzibu.partials.playlists-grid', compact('playlists'))->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => 'Popüler Playlistler - Muzibu',
                'description' => 'En popüler müzik playlistlerini keşfedin',
            ]
        ]);
    }

    /**
     * 🚀 SPA API: Display playlist detail (JSON)
     */
    public function apiShow($slug)
    {
        $playlist = Playlist::with('coverMedia')
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = $playlist->songs()
            ->with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('muzibu_songs.is_active', 1)
            ->orderBy('muzibu_playlist_song.position')
            ->get();

        $html = view('themes.muzibu.partials.playlist-detail', compact('playlist', 'songs'))->render();

        $titleJson = @json_decode($playlist->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $playlist->title;

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => $title . ' - Muzibu',
                'description' => $playlist->description ?? 'Müzik playlistini dinleyin',
            ]
        ]);
    }
}
