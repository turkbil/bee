<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PlaylistController extends Controller
{
    /**
     * Get all playlists with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $sectorId = $request->input('sector_id');
        $isFeatured = $request->input('is_featured');

        $query = DB::table('muzibu_playlists')
            ->select([
                'playlist_id',
                'title',
                'slug',
                'description',
                'media_id',
                'is_system',
                'is_public',
                'is_active'
            ])
            ->where('is_active', 1);

        // Filter by sector
        if ($sectorId) {
            $query->join('muzibu_playlist_sector', 'muzibu_playlists.playlist_id', '=', 'muzibu_playlist_sector.playlist_id')
                ->where('muzibu_playlist_sector.sector_id', $sectorId);
        }

        // Get paginated results
        $playlists = $query->paginate($perPage);

        // Add song count to each playlist
        $playlists->getCollection()->transform(function ($playlist) {
            $songCount = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlist->playlist_id)
                ->count();

            $playlist->song_count = $songCount;

            // Decode JSON fields
            $playlist->title = json_decode($playlist->title, true);
            $playlist->slug = json_decode($playlist->slug, true);
            $playlist->description = json_decode($playlist->description, true);

            return $playlist;
        });

        return response()->json($playlists);
    }

    /**
     * Get single playlist with songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Get playlist
        $playlist = DB::table('muzibu_playlists')
            ->where('playlist_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$playlist) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        // Decode JSON fields
        $playlist->title = json_decode($playlist->title, true);
        $playlist->slug = json_decode($playlist->slug, true);
        $playlist->description = json_decode($playlist->description, true);

        // Get songs in playlist
        $songs = DB::table('muzibu_playlist_song')
            ->join('muzibu_songs', 'muzibu_playlist_song.song_id', '=', 'muzibu_songs.song_id')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_playlist_song.playlist_id', $id)
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
                'muzibu_albums.slug as album_slug',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug',
                'muzibu_playlist_song.position'
            ])
            ->orderBy('muzibu_playlist_song.position')
            ->get();

        // Decode JSON fields for each song
        $songs = $songs->map(function ($song) {
            $song->song_title = json_decode($song->song_title, true);
            $song->song_slug = json_decode($song->song_slug, true);
            $song->album_title = json_decode($song->album_title, true);
            $song->album_slug = json_decode($song->album_slug, true);
            $song->artist_title = json_decode($song->artist_title, true);
            $song->artist_slug = json_decode($song->artist_slug, true);
            return $song;
        });

        $playlist->songs = $songs;
        $playlist->song_count = $songs->count();

        return response()->json($playlist);
    }

    /**
     * Get featured playlists
     *
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        $playlists = DB::table('muzibu_playlists')
            ->where('is_active', 1)
            ->where('is_system', 1)
            ->limit(10)
            ->get();

        $playlists = $playlists->map(function ($playlist) {
            $songCount = DB::table('muzibu_playlist_song')
                ->where('playlist_id', $playlist->playlist_id)
                ->count();

            $playlist->song_count = $songCount;
            $playlist->title = json_decode($playlist->title, true);
            $playlist->slug = json_decode($playlist->slug, true);
            $playlist->description = json_decode($playlist->description, true);

            return $playlist;
        });

        return response()->json($playlists);
    }
}
