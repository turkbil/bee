<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    public function index()
    {
        $genres = DB::table('muzibu_genres')
            ->where('is_active', 1)
            ->get();

        $genres = $genres->map(function ($genre) {
            $genre->title = json_decode($genre->title, true);
            $genre->slug = json_decode($genre->slug, true);

            $genre->song_count = DB::table('muzibu_songs')
                ->where('genre_id', $genre->genre_id)
                ->where('is_active', 1)
                ->count();

            return $genre;
        });

        return view('muzibu::themes.muzibu.genres.index', compact('genres'));
    }

    public function show($id)
    {
        $genre = DB::table('muzibu_genres')
            ->where('genre_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$genre) {
            abort(404);
        }

        $genre->title = json_decode($genre->title, true);
        $genre->slug = json_decode($genre->slug, true);

        $songs = DB::table('muzibu_songs')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_songs.genre_id', $id)
            ->where('muzibu_songs.is_active', 1)
            ->select([
                'muzibu_songs.song_id',
                'muzibu_songs.title as song_title',
                'muzibu_songs.duration',
                'muzibu_songs.play_count',
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title'
            ])
            ->orderBy('muzibu_songs.play_count', 'desc')
            ->paginate(50);

        $songs->getCollection()->transform(function ($song) {
            $song->song_title = json_decode($song->song_title, true);
            $song->album_title = json_decode($song->album_title, true);
            $song->artist_title = json_decode($song->artist_title, true);
            return $song;
        });

        return view('muzibu::themes.muzibu.genres.show', compact('genre', 'songs'));
    }
}
