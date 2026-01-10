<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Song;

class GenreController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $genres = Genre::where('is_active', 1)
                ->get()
                ->map(function ($genre) {
                    return [
                        'genre_id' => $genre->genre_id,
                        'title' => $genre->title,
                        'slug' => $genre->slug,
                        'song_count' => $genre->songs()->count(),
                        'cover_url' => $genre->getIconUrl(200, 200),
                    ];
                });
            return response()->json($genres);
        } catch (\Exception $e) {
            \Log::error('Genre index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function songs(Request $request, int $id): JsonResponse
    {
        try {
            $genre = Genre::find($id);
            if (!$genre) {
                return response()->json(['error' => 'Genre not found'], 404);
            }

            // 🎯 Limit for queue refill (default: 100 songs)
            $limit = (int) $request->input('limit', 100);

            $songs = $genre->songs()
                ->where('is_active', 1)
                ->with('album.artist')
                ->limit($limit)
                ->get()
                ->shuffle() // 🔀 PHP shuffle - ORDER BY RAND()'dan 10x hızlı
                ->map(function ($song) use ($genre) {
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
                    'genre_id' => $song->genre_id ?? $genre->genre_id, // 🎵 Genre ID for queue refill
                    'is_favorite' => false, // TODO: Auth check
                ];
            });

            return response()->json([
                'genre' => [
                    'genre_id' => $genre->genre_id,
                    'title' => $genre->title,
                ],
                'songs' => $songs
            ]);
        } catch (\Exception $e) {
            \Log::error('Genre songs error:', ['genre_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
