<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $results = [
            'songs' => [],
            'albums' => [],
            'playlists' => [],
            'artists' => []
        ];

        if (strlen($query) > 0) {
            // Search songs
            $songs = DB::table('muzibu_songs')
                ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
                ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
                ->where('muzibu_songs.is_active', 1)
                ->where(function($q) use ($query) {
                    $q->where('muzibu_songs.title', 'LIKE', '%' . $query . '%')
                      ->orWhere('muzibu_albums.title', 'LIKE', '%' . $query . '%')
                      ->orWhere('muzibu_artists.title', 'LIKE', '%' . $query . '%');
                })
                ->select([
                    'muzibu_songs.song_id',
                    'muzibu_songs.title as song_title',
                    'muzibu_songs.slug as song_slug',
                    'muzibu_songs.duration',
                    'muzibu_albums.album_id',
                    'muzibu_albums.title as album_title',
                    'muzibu_albums.media_id as album_cover',
                    'muzibu_artists.artist_id',
                    'muzibu_artists.title as artist_title'
                ])
                ->limit(20)
                ->get();

            foreach ($songs as $song) {
                $song->song_title = json_decode($song->song_title, true);
                $song->song_slug = json_decode($song->song_slug, true);
                $song->album_title = json_decode($song->album_title, true);
                $song->artist_title = json_decode($song->artist_title, true);
            }

            $results['songs'] = $songs;

            // Search albums
            $albums = DB::table('muzibu_albums')
                ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
                ->where('muzibu_albums.is_active', 1)
                ->where(function($q) use ($query) {
                    $q->where('muzibu_albums.title', 'LIKE', '%' . $query . '%')
                      ->orWhere('muzibu_artists.title', 'LIKE', '%' . $query . '%');
                })
                ->select([
                    'muzibu_albums.album_id',
                    'muzibu_albums.title as album_title',
                    'muzibu_albums.slug as album_slug',
                    'muzibu_albums.media_id as album_cover',
                    'muzibu_artists.artist_id',
                    'muzibu_artists.title as artist_title'
                ])
                ->limit(10)
                ->get();

            foreach ($albums as $album) {
                $album->album_title = json_decode($album->album_title, true);
                $album->album_slug = json_decode($album->album_slug, true);
                $album->artist_title = json_decode($album->artist_title, true);
            }

            $results['albums'] = $albums;

            // Search playlists
            $playlists = DB::table('muzibu_playlists')
                ->where('is_active', 1)
                ->where('is_public', 1)
                ->where('title', 'LIKE', '%' . $query . '%')
                ->select([
                    'playlist_id',
                    'title',
                    'slug',
                    'description',
                    'media_id as cover_image'
                ])
                ->limit(10)
                ->get();

            foreach ($playlists as $playlist) {
                $playlist->title = json_decode($playlist->title, true);
                $playlist->slug = json_decode($playlist->slug, true);
                $playlist->description = json_decode($playlist->description, true);
            }

            $results['playlists'] = $playlists;

            // Search artists
            $artists = DB::table('muzibu_artists')
                ->where('is_active', 1)
                ->where('title', 'LIKE', '%' . $query . '%')
                ->select([
                    'artist_id',
                    'title',
                    'slug',
                    'media_id as artist_image'
                ])
                ->limit(10)
                ->get();

            foreach ($artists as $artist) {
                $artist->title = json_decode($artist->title, true);
                $artist->slug = json_decode($artist->slug, true);
            }

            $results['artists'] = $artists;
        }

        return view('muzibu::themes.muzibu.search.index', [
            'query' => $query,
            'results' => $results
        ]);
    }
}
