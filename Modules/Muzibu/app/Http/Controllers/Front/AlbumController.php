<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_albums.is_active', 1)
            ->select([
                'muzibu_albums.album_id',
                'muzibu_albums.title',
                'muzibu_albums.slug',
                'muzibu_albums.media_id',
                'muzibu_albums.created_at',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug'
            ])
            ->orderBy('muzibu_albums.created_at', 'desc')
            ->paginate(20);

        $albums->getCollection()->transform(function ($album) {
            $album->title = json_decode($album->title, true);
            $album->slug = json_decode($album->slug, true);
            $album->artist_title = json_decode($album->artist_title, true);
            $album->artist_slug = json_decode($album->artist_slug, true);

            $album->song_count = DB::table('muzibu_songs')
                ->where('album_id', $album->album_id)
                ->where('is_active', 1)
                ->count();

            return $album;
        });

        return view('muzibu::themes.muzibu.albums.index', compact('albums'));
    }

    public function show($id)
    {
        $album = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_albums.album_id', $id)
            ->where('muzibu_albums.is_active', 1)
            ->select([
                'muzibu_albums.*',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug',
                'muzibu_artists.artist_id'
            ])
            ->first();

        if (!$album) {
            abort(404);
        }

        $album->title = json_decode($album->title, true);
        $album->slug = json_decode($album->slug, true);
        $album->description = json_decode($album->description, true);
        $album->artist_title = json_decode($album->artist_title, true);
        $album->artist_slug = json_decode($album->artist_slug, true);

        $songs = DB::table('muzibu_songs')
            ->where('album_id', $id)
            ->where('is_active', 1)
            ->orderBy('track_number')
            ->get();

        $songs = $songs->map(function ($song) {
            $song->title = json_decode($song->title, true);
            $song->slug = json_decode($song->slug, true);
            return $song;
        });

        return view('muzibu::themes.muzibu.albums.show', compact('album', 'songs'));
    }
}
