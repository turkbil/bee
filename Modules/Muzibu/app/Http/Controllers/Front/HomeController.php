<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display Muzibu home page with Spotify-style layout
     */
    public function index()
    {
        // Get featured playlists
        $featuredPlaylists = DB::table('muzibu_playlists')
            ->where('is_active', 1)
            ->where('is_system', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get new releases (recent albums)
        $newReleases = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_albums.is_active', 1)
            ->select([
                'muzibu_albums.album_id',
                'muzibu_albums.title',
                'muzibu_albums.slug',
                'muzibu_albums.media_id',
                'muzibu_albums.created_at',
                'muzibu_artists.title as artist_title'
            ])
            ->orderBy('muzibu_albums.created_at', 'desc')
            ->limit(10)
            ->get();

        // Get popular songs
        $popularSongs = DB::table('muzibu_songs')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_songs.is_active', 1)
            ->select([
                'muzibu_songs.song_id',
                'muzibu_songs.title as song_title',
                'muzibu_songs.slug as song_slug',
                'muzibu_songs.duration',
                'muzibu_songs.play_count',
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title'
            ])
            ->orderBy('muzibu_songs.play_count', 'desc')
            ->limit(20)
            ->get();

        // Get genres
        $genres = DB::table('muzibu_genres')
            ->where('is_active', 1)
            ->select(['genre_id', 'title', 'slug'])
            ->get();

        // Use view from global themes path
        return view('muzibu::themes.muzibu.home', compact(
            'featuredPlaylists',
            'newReleases',
            'popularSongs',
            'genres'
        ));
    }
}
