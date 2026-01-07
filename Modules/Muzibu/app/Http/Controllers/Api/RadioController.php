<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Radio;

class RadioController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $radios = Radio::where('is_active', 1)->get()->map(function ($radio) {
                return [
                    'radio_id' => $radio->radio_id,
                    'title' => $radio->title,
                    'slug' => $radio->slug,
                    'stream_url' => $radio->stream_url,
                    'logo_url' => $radio->getLogoUrl(200, 200),
                ];
            });
            return response()->json($radios);
        } catch (\Exception $e) {
            \Log::error('Radio index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function songs(int $id): JsonResponse
    {
        try {
            $radio = Radio::find($id);
            if (!$radio) {
                return response()->json(['error' => 'Radio not found'], 404);
            }

            // Get all songs from all playlists associated with this radio
            $songs = collect();
            foreach ($radio->playlists()->where('is_active', 1)->get() as $playlist) {
                $playlistSongs = $playlist->songs()->where('is_active', 1)->with('album.artist')->get();
                $songs = $songs->merge($playlistSongs);
            }

            // Remove duplicates and map to API format
            $songs = $songs->unique('song_id')->map(function ($song) {
                $album = $song->album;
                $artist = $album?->artist;
                return [
                    'song_id' => $song->song_id,
                    'song_title' => $song->title,
                    'song_slug' => $song->slug,
                    'duration' => $song->duration,
                    'hls_path' => $song->hls_path,
                    'album_id' => $album?->album_id,
                    'album_title' => $album?->title,
                    'artist_id' => $artist?->artist_id,
                    'artist_title' => $artist?->title,
                ];
            })->values();

            return response()->json([
                'radio' => [
                    'radio_id' => $radio->radio_id,
                    'title' => $radio->title,
                ],
                'songs' => $songs
            ]);
        } catch (\Exception $e) {
            \Log::error('Radio songs error:', ['radio_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
