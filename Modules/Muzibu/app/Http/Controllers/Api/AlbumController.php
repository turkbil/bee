<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    /**
     * Get all albums with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $artistId = $request->input('artist_id');

        $query = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->select([
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.description',
                'muzibu_albums.media_id',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug'
            ])
            ->where('muzibu_albums.is_active', 1);

        if ($artistId) {
            $query->where('muzibu_albums.artist_id', $artistId);
        }

        $albums = $query->paginate($perPage);

        // Decode JSON and add song count
        $albums->getCollection()->transform(function ($album) {
            $album->album_title = json_decode($album->album_title, true);
            $album->album_slug = json_decode($album->album_slug, true);
            $album->description = json_decode($album->description, true);
            $album->artist_title = json_decode($album->artist_title, true);
            $album->artist_slug = json_decode($album->artist_slug, true);

            $songCount = DB::table('muzibu_songs')
                ->where('album_id', $album->album_id)
                ->where('is_active', 1)
                ->count();

            $album->song_count = $songCount;

            return $album;
        });

        return response()->json($albums);
    }

    /**
     * Get single album with songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Get album with artist
        $album = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_albums.album_id', $id)
            ->where('muzibu_albums.is_active', 1)
            ->select([
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.description',
                'muzibu_albums.media_id',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug'
            ])
            ->first();

        if (!$album) {
            return response()->json(['error' => 'Album not found'], 404);
        }

        // Decode JSON
        $album->album_title = json_decode($album->album_title, true);
        $album->album_slug = json_decode($album->album_slug, true);
        $album->description = json_decode($album->description, true);
        $album->artist_title = json_decode($album->artist_title, true);
        $album->artist_slug = json_decode($album->artist_slug, true);

        // Get songs
        $songs = DB::table('muzibu_songs')
            ->where('album_id', $id)
            ->where('is_active', 1)
            ->select([
                'song_id',
                'title',
                'slug',
                'duration',
                'file_path',
                'hls_path',
                'hls_converted',
                'play_count'
            ])
            ->orderBy('song_id')
            ->get();

        // Decode JSON for songs
        $songs = $songs->map(function ($song) {
            $song->title = json_decode($song->title, true);
            $song->slug = json_decode($song->slug, true);
            return $song;
        });

        $album->songs = $songs;
        $album->song_count = $songs->count();

        // Calculate total duration
        $album->total_duration = $songs->sum('duration');

        return response()->json($album);
    }

    /**
     * Get new albums
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function newReleases(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $albums = DB::table('muzibu_albums')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_albums.is_active', 1)
            ->select([
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.media_id',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug'
            ])
            ->orderBy('muzibu_albums.created_at', 'desc')
            ->limit($limit)
            ->get();

        $albums = $albums->map(function ($album) {
            $album->album_title = json_decode($album->album_title, true);
            $album->album_slug = json_decode($album->album_slug, true);
            $album->artist_title = json_decode($album->artist_title, true);
            $album->artist_slug = json_decode($album->artist_slug, true);

            $songCount = DB::table('muzibu_songs')
                ->where('album_id', $album->album_id)
                ->where('is_active', 1)
                ->count();

            $album->song_count = $songCount;

            return $album;
        });

        return response()->json($albums);
    }
}
