<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Sector;

class SectorController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $sectors = Sector::where('is_active', 1)->get()->map(function ($sector) {
                return [
                    'sector_id' => $sector->sector_id,
                    'title' => $sector->title,
                    'slug' => $sector->slug,
                    'playlist_count' => $sector->playlists()->count(),
                    'cover_url' => $sector->getIconUrl(200, 200),
                ];
            });
            return response()->json($sectors);
        } catch (\Exception $e) {
            \Log::error('Sector index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function playlists(int $id): JsonResponse
    {
        try {
            $sector = Sector::find($id);
            if (!$sector) {
                return response()->json(['error' => 'Sector not found'], 404);
            }

            $playlists = $sector->playlists()->where('is_active', 1)->get()->map(function ($playlist) {
                return [
                    'playlist_id' => $playlist->playlist_id,
                    'title' => $playlist->title,
                    'slug' => $playlist->slug,
                    'media_id' => $playlist->media_id,
                    'song_count' => $playlist->songs()->count(),
                    'cover_url' => $playlist->getCoverUrl(200, 200),
                ];
            });

            return response()->json($playlists);
        } catch (\Exception $e) {
            \Log::error('Sector playlists error:', ['sector_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function songs(int $id): JsonResponse
    {
        try {
            $sector = Sector::find($id);
            if (!$sector) {
                return response()->json(['error' => 'Sector not found'], 404);
            }

            // Get all songs from all playlists in this sector
            $songs = collect();
            foreach ($sector->playlists()->where('is_active', 1)->get() as $playlist) {
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
                    'cover_url' => $song->getCoverUrl(120, 120), // ✅ Sidebar için cover (120x120)
                    'album_cover' => $song->getCoverUrl(120, 120), // ✅ Compatibility
                    'album_id' => $album?->album_id,
                    'album_title' => $album?->title,
                    'artist_id' => $artist?->artist_id,
                    'artist_title' => $artist?->title,
                    'is_favorite' => false, // TODO: Auth check
                ];
            })->values();

            return response()->json([
                'sector' => [
                    'sector_id' => $sector->sector_id,
                    'title' => $sector->title,
                ],
                'songs' => $songs
            ]);
        } catch (\Exception $e) {
            \Log::error('Sector songs error:', ['sector_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
