<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    /**
     * Get all genres
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $genres = DB::table('muzibu_genres')
            ->where('is_active', 1)
            ->select(['genre_id', 'title', 'slug'])
            ->get();

        $genres = $genres->map(function ($genre) {
            $genre->title = json_decode($genre->title, true);
            $genre->slug = json_decode($genre->slug, true);
            return $genre;
        });

        return response()->json($genres);
    }

    /**
     * Get songs by genre
     *
     * @param int $id
     * @return JsonResponse
     */
    public function songs(int $id): JsonResponse
    {
        $songs = DB::table('muzibu_songs')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_songs.genre_id', $id)
            ->where('muzibu_songs.is_active', 1)
            ->select([
                'muzibu_songs.song_id',
                'muzibu_songs.title as song_title',
                'muzibu_songs.slug as song_slug',
                'muzibu_songs.duration',
                'muzibu_songs.file_path',
                'muzibu_songs.hls_path',
                'muzibu_songs.hls_converted',
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title'
            ])
            ->get();

        $songs = $songs->map(function ($song) {
            $song->song_title = json_decode($song->song_title, true);
            $song->song_slug = json_decode($song->song_slug, true);
            $song->album_title = json_decode($song->album_title, true);
            $song->artist_title = json_decode($song->artist_title, true);
            return $song;
        });

        return response()->json($songs);
    }
}
