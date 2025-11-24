<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    /**
     * Get recently played songs for current user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $limit = $request->input('limit', 20);

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $songs = DB::table('muzibu_song_plays')
            ->join('muzibu_songs', 'muzibu_song_plays.song_id', '=', 'muzibu_songs.song_id')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_song_plays.user_id', $userId)
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
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug',
                'muzibu_song_plays.created_at as played_at'
            ])
            ->orderBy('muzibu_song_plays.created_at', 'desc')
            ->limit($limit)
            ->get();

        // Decode JSON and remove duplicates
        $uniqueSongs = collect([]);
        $seenSongIds = [];

        foreach ($songs as $song) {
            if (!in_array($song->song_id, $seenSongIds)) {
                $song->song_title = json_decode($song->song_title, true);
                $song->song_slug = json_decode($song->song_slug, true);
                $song->album_title = json_decode($song->album_title, true);
                $song->album_slug = json_decode($song->album_slug, true);
                $song->artist_title = json_decode($song->artist_title, true);
                $song->artist_slug = json_decode($song->artist_slug, true);

                $uniqueSongs->push($song);
                $seenSongIds[] = $song->song_id;
            }
        }

        return response()->json($uniqueSongs->values());
    }

    /**
     * Get popular songs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);

        $songs = DB::table('muzibu_songs')
            ->join('muzibu_albums', 'muzibu_songs.album_id', '=', 'muzibu_albums.album_id')
            ->join('muzibu_artists', 'muzibu_albums.artist_id', '=', 'muzibu_artists.artist_id')
            ->where('muzibu_songs.is_active', 1)
            ->select([
                'muzibu_songs.song_id',
                'muzibu_songs.title as song_title',
                'muzibu_songs.slug as song_slug',
                'muzibu_songs.duration',
                'muzibu_songs.file_path',
                'muzibu_songs.hls_path',
                'muzibu_songs.hls_converted',
                'muzibu_songs.play_count',
                'muzibu_albums.album_id',
                'muzibu_albums.title as album_title',
                'muzibu_albums.slug as album_slug',
                'muzibu_albums.media_id as album_cover',
                'muzibu_artists.artist_id',
                'muzibu_artists.title as artist_title',
                'muzibu_artists.slug as artist_slug'
            ])
            ->orderBy('muzibu_songs.play_count', 'desc')
            ->limit($limit)
            ->get();

        $songs = $songs->map(function ($song) {
            $song->song_title = json_decode($song->song_title, true);
            $song->song_slug = json_decode($song->song_slug, true);
            $song->album_title = json_decode($song->album_title, true);
            $song->album_slug = json_decode($song->album_slug, true);
            $song->artist_title = json_decode($song->artist_title, true);
            $song->artist_slug = json_decode($song->artist_slug, true);
            return $song;
        });

        return response()->json($songs);
    }

    /**
     * Track song play
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function trackPlay(Request $request, int $id): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if song exists
        $song = DB::table('muzibu_songs')
            ->where('song_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        // Insert play record
        DB::table('muzibu_song_plays')->insert([
            'user_id' => $userId,
            'song_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Increment play count
        DB::table('muzibu_songs')
            ->where('song_id', $id)
            ->increment('play_count');

        return response()->json(['success' => true]);
    }

    /**
     * Get song stream URL
     *
     * @param int $id
     * @return JsonResponse
     */
    public function stream(int $id): JsonResponse
    {
        $song = DB::table('muzibu_songs')
            ->where('song_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$song) {
            return response()->json(['error' => 'Song not found'], 404);
        }

        // Generate stream URL - Hardcode URL yerine relative path kullan
        $streamUrl = $song->hls_converted && $song->hls_path
            ? $song->hls_path
            : '/api/muzibu/songs/' . $id . '/serve';

        return response()->json([
            'song_id' => $song->song_id,
            'stream_url' => $streamUrl,
            'type' => $song->hls_converted ? 'hls' : 'mp3',
            'duration' => $song->duration,
        ]);
    }

    /**
     * Serve MP3 file
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function serve(int $id)
    {
        $song = DB::table('muzibu_songs')
            ->where('song_id', $id)
            ->where('is_active', 1)
            ->first();

        if (!$song || !$song->file_path) {
            abort(404, 'Song not found');
        }

        $filePath = $song->file_path;

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
