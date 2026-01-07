<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Services\MuzibuCacheService;

class AlbumController extends Controller
{
    public function __construct(
        private MuzibuCacheService $cacheService
    ) {
    }
    /**
     * Get all albums with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);

            //🔒 FIXED: Use Eloquent (tenant-aware) + Only albums with active songs
            $albums = Album::where('is_active', 1)
                ->whereHas('songs', function($q) {
                    $q->where('is_active', 1);
                })
                ->with('artist')
                ->paginate($perPage);

            $albums->getCollection()->transform(function ($album) {
                return [
                    'album_id' => $album->album_id,
                    'title' => $album->title,
                    'slug' => $album->slug,
                    'media_id' => $album->media_id,
                'cover_url' => $album->getCoverUrl(200, 200),
                    'artist_id' => $album->artist?->artist_id,
                    'artist_title' => $album->artist?->title,
                    'artist_slug' => $album->artist?->slug,
                    'song_count' => $album->songs()->where('is_active', 1)->count(),
                ];
            });

            return response()->json($albums);

        } catch (\Exception $e) {
            \Log::error('Album index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get single album with songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // 🚀 CACHE: Get album from Redis (24h TTL)
            $album = $this->cacheService->getAlbum($id);

            if (!$album) {
                return response()->json(['error' => 'Album not found'], 404);
            }

            $songs = $album->songs->map(function ($song) use ($album) {
                return [
                    'song_id' => $song->song_id,
                    'song_title' => $song->title,
                    'song_slug' => $song->slug,
                    'duration' => $song->duration,
                    'hls_path' => $song->hls_path,                    'lyrics' => $song->lyrics, // 🎤 Lyrics support (dynamic - null if not available)
                    'album_id' => $album->album_id,
                    'album_title' => $album->title,
                    'album_slug' => $album->slug,
                    'album_cover' => $song->getCoverUrl(120, 120), // 🎨 Song cover (fallback to album)
                    'artist_id' => $album->artist?->artist_id,
                    'artist_title' => $album->artist?->title,
                    'artist_slug' => $album->artist?->slug,
                ];
            });

            // Wrap in 'album' key for JS compatibility
            return response()->json([
                'album' => [
                    'id' => $album->album_id,
                    'album_id' => $album->album_id,
                    'title' => $album->title,
                    'slug' => $album->slug,
                    'media_id' => $album->media_id,
                    'cover_url' => $album->getCoverUrl(200, 200),
                    'artist_id' => $album->artist?->artist_id,
                    'artist_title' => $album->artist?->title,
                    'artist_slug' => $album->artist?->slug,
                    'songs' => $songs,
                    'song_count' => $songs->count(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Album show error:', ['album_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get new releases
     *
     * @return JsonResponse
     */
    public function newReleases(): JsonResponse
    {
        try {
            //🔒 FIXED: Use Eloquent (tenant-aware)
            $albums = Album::where('is_active', 1)
                ->with('artist')
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get()
                ->map(function ($album) {
                    return [
                        'album_id' => $album->album_id,
                        'title' => $album->title,
                        'slug' => $album->slug,
                        'media_id' => $album->media_id,
                'cover_url' => $album->getCoverUrl(200, 200),
                        'artist_id' => $album->artist?->artist_id,
                        'artist_title' => $album->artist?->title,
                        'artist_slug' => $album->artist?->slug,
                        'song_count' => $album->songs()->where('is_active', 1)->count(),
                    ];
                });

            return response()->json($albums);

        } catch (\Exception $e) {
            \Log::error('New releases error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
