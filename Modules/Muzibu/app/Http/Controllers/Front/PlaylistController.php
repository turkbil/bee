<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PlaylistController extends Controller
{
    /**
     * Display playlists list
     */
    public function index()
    {
        $playlists = DB::table('muzibu_playlists')
            ->where('is_active', 1)
            ->where('is_public', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Decode JSON for each playlist
        $playlists->getCollection()->transform(function ($playlist) {
            $playlist->title = json_decode($playlist->title, true);
            $playlist->slug = json_decode($playlist->slug, true);
            $playlist->description = json_decode($playlist->description, true);

            // Get song count
            $playlist->song_count = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlist->playlist_id)
                ->count();

            return $playlist;
        });

        return view('muzibu::themes.muzibu.playlists.index', compact('playlists'));
    }

    /**
     * Display playlist detail
     */
    public function show($id)
    {
        $playlist = DB::table('muzibu_playlists')
            ->where('playlist_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$playlist) {
            abort(404);
        }

        // Decode JSON
        $playlist->title = json_decode($playlist->title, true);
        $playlist->slug = json_decode($playlist->slug, true);
        $playlist->description = json_decode($playlist->description, true);

        // Get songs with full details
        $songs = DB::table('muzibu_playlist_song')
            ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_playlist_song.playlist_id', $id)
            ->where('muzibu_songs.is_active', 1)
            ->select([
                'muzibu_songs.song_id',
                'muzibu_songs.title as song_title',
                'muzibu_songs.slug as song_slug',
                'muzibu_songs.duration',
                'muzibu_songs.play_count',
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug',
                'muzibu_playlist_song.position'
            ])
            ->orderBy('muzibu_playlist_song.position')
            ->get();

        // Decode JSON for songs
        $songs = $songs->map(function ($song) {
            $song->song_title = json_decode($song->song_title, true);
            $song->song_slug = json_decode($song->song_slug, true);
            $song->album_title = json_decode($song->album_title, true);
            $song->album_slug = json_decode($song->album_slug, true);
            $song->artist_title = json_decode($song->artist_title, true);
            $song->artist_slug = json_decode($song->artist_slug, true);
            return $song;
        });

        return view('muzibu::themes.muzibu.playlists.show', compact('playlist', 'songs'));
    }
}
