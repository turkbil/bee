<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Services\MuzibuCacheService;

class ArtistController extends Controller
{
    public function __construct(
        private MuzibuCacheService $cacheService
    ) {
    }

    /**
     * Get all artists with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);

            // Only show artists with at least 1 active album
            $artists = Artist::where('is_active', 1)
                ->whereHas('albums', function($q) {
                    $q->where('is_active', 1);
                })
                ->with('photoMedia')
                ->paginate($perPage);

            $artists->getCollection()->transform(function ($artist) {
                return [
                    'artist_id' => $artist->artist_id,
                    'title' => $artist->title,
                    'slug' => $artist->slug,
                    'bio' => $artist->bio,
                    'media_id' => $artist->media_id,
                    'photo_url' => $artist->getPhotoUrl(200, 200),
                    'album_count' => $artist->albums()->where('is_active', 1)->count(),
                    'song_count' => $artist->songs()->where('is_active', 1)->count(),
                ];
            });

            return response()->json($artists);

        } catch (\Exception $e) {
            \Log::error('Artist index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get single artist with albums and songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $artist = Artist::with(['photoMedia'])
                ->where('artist_id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$artist) {
                return response()->json(['error' => 'Artist not found'], 404);
            }

            // Get albums
            $albums = Album::with(['coverMedia'])
                ->where('artist_id', $artist->artist_id)
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($album) {
                    return [
                        'album_id' => $album->album_id,
                        'title' => $album->title,
                        'slug' => $album->slug,
                        'media_id' => $album->media_id,
                        'cover_url' => $album->getCoverUrl(200, 200),
                        'song_count' => $album->songs()->where('is_active', 1)->count(),
                    ];
                });

            // Get songs
            $songs = Song::with(['coverMedia', 'album'])
                ->whereHas('album', function($q) use ($artist) {
                    $q->where('artist_id', $artist->artist_id);
                })
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($song) {
                    return [
                        'song_id' => $song->song_id,
                        'title' => $song->title,
                        'slug' => $song->slug,
                        'duration' => $song->duration,
                        'file_path' => $song->file_path,
                        'hls_path' => $song->hls_path,
                        'album_id' => $song->album?->album_id,
                        'album_title' => $song->album?->title,
                        'album_slug' => $song->album?->slug,
                    ];
                });

            return response()->json([
                'artist_id' => $artist->artist_id,
                'title' => $artist->title,
                'slug' => $artist->slug,
                'bio' => $artist->bio,
                'media_id' => $artist->media_id,
                'photo_url' => $artist->getPhotoUrl(400, 400),
                'albums' => $albums,
                'album_count' => $albums->count(),
                'songs' => $songs,
                'song_count' => $songs->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Artist show error:', ['artist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get artist albums
     *
     * @param int $id
     * @return JsonResponse
     */
    public function albums(int $id): JsonResponse
    {
        try {
            $artist = Artist::where('artist_id', $id)->where('is_active', 1)->first();

            if (!$artist) {
                return response()->json(['error' => 'Artist not found'], 404);
            }

            $albums = Album::with(['coverMedia'])
                ->where('artist_id', $artist->artist_id)
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($album) {
                    return [
                        'album_id' => $album->album_id,
                        'title' => $album->title,
                        'slug' => $album->slug,
                        'media_id' => $album->media_id,
                        'cover_url' => $album->getCoverUrl(200, 200),
                        'song_count' => $album->songs()->where('is_active', 1)->count(),
                    ];
                });

            return response()->json($albums);

        } catch (\Exception $e) {
            \Log::error('Artist albums error:', ['artist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get artist songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function songs(int $id): JsonResponse
    {
        try {
            $artist = Artist::where('artist_id', $id)->where('is_active', 1)->first();

            if (!$artist) {
                return response()->json(['error' => 'Artist not found'], 404);
            }

            $songs = Song::with(['coverMedia', 'album'])
                ->whereHas('album', function($q) use ($artist) {
                    $q->where('artist_id', $artist->artist_id);
                })
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($song) {
                    return [
                        'song_id' => $song->song_id,
                        'title' => $song->title,
                        'slug' => $song->slug,
                        'duration' => $song->duration,
                        'file_path' => $song->file_path,
                        'hls_path' => $song->hls_path,
                        'album_id' => $song->album?->album_id,
                        'album_title' => $song->album?->title,
                        'album_slug' => $song->album?->slug,
                    ];
                });

            return response()->json($songs);

        } catch (\Exception $e) {
            \Log::error('Artist songs error:', ['artist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
