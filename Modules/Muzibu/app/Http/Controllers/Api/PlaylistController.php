<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Services\PlaylistService;
use Modules\Muzibu\App\Services\MuzibuCacheService;

class PlaylistController extends Controller
{
    public function __construct(
        private PlaylistService $playlistService,
        private MuzibuCacheService $cacheService
    ) {
    }
    /**
     * Get all playlists with pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            $sectorId = $request->input('sector_id');

            //🔒 FIXED: Use Eloquent (tenant-aware)
            $query = Playlist::where('is_active', 1);

            // Filter by sector
            if ($sectorId) {
                $query->whereHas('sectors', function ($q) use ($sectorId) {
                    $q->where('sector_id', $sectorId);
                });
            }

            $playlists = $query->paginate($perPage);

            // Transform to API format
            $playlists->getCollection()->transform(function ($playlist) {
                return [
                    'playlist_id' => $playlist->playlist_id,
                    'title' => $playlist->title,
                    'slug' => $playlist->slug,
                    'description' => $playlist->description,
                    'media_id' => $playlist->media_id,
                    'cover_url' => $playlist->getCoverUrl(200, 200),
                    'is_system' => $playlist->is_system,
                    'is_public' => $playlist->is_public,
                    'is_active' => $playlist->is_active,
                    'song_count' => $playlist->songs()->count(),
                ];
            });

            return response()->json($playlists);

        } catch (\Exception $e) {
            \Log::error('Playlist index error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get single playlist with songs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // 🚀 CACHE: Get playlist from Redis (1h TTL)
            $playlist = $this->cacheService->getPlaylist($id);

            if (!$playlist) {
                return response()->json(['error' => 'Playlist not found'], 404);
            }

            // Transform songs
            $songs = $playlist->songs->map(function ($song) {
                $album = $song->album;
                $artist = $album?->artist;

                return [
                    'song_id' => $song->song_id,
                    'song_title' => $song->title,
                    'song_slug' => $song->slug,
                    'duration' => $song->duration,
                    'file_path' => $song->file_path,
                    'hls_path' => $song->hls_path,
                    'hls_converted' => $song->hls_converted,
                    'lyrics' => $song->lyrics, // 🎤 Lyrics support (dynamic - null if not available)
                    'album_id' => $album?->album_id,
                    'album_title' => $album?->title,
                    'album_slug' => $album?->slug,
                    'artist_id' => $artist?->artist_id,
                    'artist_title' => $artist?->title,
                    'artist_slug' => $artist?->slug,
                    'position' => $song->pivot->position ?? 0,
                ];
            });

            return response()->json([
                'playlist_id' => $playlist->playlist_id,
                'title' => $playlist->title,
                'slug' => $playlist->slug,
                'description' => $playlist->description,
                'media_id' => $playlist->media_id,
                'cover_url' => $playlist->getCoverUrl(200, 200),
                'is_system' => $playlist->is_system,
                'is_public' => $playlist->is_public,
                'is_active' => $playlist->is_active,
                'songs' => $songs,
                'song_count' => $songs->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Playlist show error:', ['playlist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get featured playlists
     *
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        try {
            //🔒 FIXED: Use Eloquent (tenant-aware)
            $playlists = Playlist::where('is_active', 1)
                ->where('is_system', 1)
                ->limit(10)
                ->get()
                ->map(function ($playlist) {
                    return [
                        'playlist_id' => $playlist->playlist_id,
                        'title' => $playlist->title,
                        'slug' => $playlist->slug,
                        'description' => $playlist->description,
                        'media_id' => $playlist->media_id,
                'cover_url' => $playlist->getCoverUrl(200, 200),
                        'song_count' => $playlist->songs()->count(),
                    ];
                });

            return response()->json($playlists);

        } catch (\Exception $e) {
            \Log::error('Featured playlists error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get user playlists (auth required)
     */
    public function myPlaylists(Request $request): JsonResponse
    {
        try {
            $userId = auth('sanctum')->id();
            $perPage = $request->input('per_page', 15);

            $playlists = $this->playlistService->getUserPlaylists($userId, $perPage);

            return response()->json($playlists);
        } catch (\Exception $e) {
            \Log::error('My playlists error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Clone system playlist to user playlist
     */
    public function clone(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'playlist_id' => 'required|integer|exists:muzibu_playlists,playlist_id',
            ]);

            $userId = auth('sanctum')->id();
            $result = $this->playlistService->clonePlaylist(
                $request->input('playlist_id'),
                $userId
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('Playlist clone error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Playlist kopyalama başarısız',
            ], 500);
        }
    }

    /**
     * Quick create playlist with songs
     */
    public function quickCreate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'song_ids' => 'nullable|array',
                'song_ids.*' => 'integer|exists:muzibu_songs,song_id',
                'is_public' => 'nullable|boolean',
            ]);

            $userId = auth('sanctum')->id();
            $result = $this->playlistService->createPlaylistWithSongs(
                $request->all(),
                $userId
            );

            return response()->json($result, $result['success'] ? 201 : 400);
        } catch (\Exception $e) {
            \Log::error('Quick create playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Playlist oluşturma başarısız',
            ], 500);
        }
    }

    /**
     * Add song to playlist
     */
    public function addSong(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'song_id' => 'required|integer|exists:muzibu_songs,song_id',
            ]);

            $userId = auth('sanctum')->id();
            $result = $this->playlistService->addSongToPlaylist(
                $id,
                $request->input('song_id'),
                $userId
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('Add song to playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Şarkı ekleme başarısız',
            ], 500);
        }
    }

    /**
     * Remove song from playlist
     */
    public function removeSong(int $id, int $songId): JsonResponse
    {
        try {
            $userId = auth('sanctum')->id();
            $result = $this->playlistService->removeSongFromPlaylist(
                $id,
                $songId,
                $userId
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('Remove song from playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Şarkı çıkarma başarısız',
            ], 500);
        }
    }

    /**
     * Reorder songs in playlist
     */
    public function reorder(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'song_positions' => 'required|array',
                'song_positions.*.song_id' => 'required|integer',
                'song_positions.*.position' => 'required|integer',
            ]);

            $userId = auth('sanctum')->id();
            $result = $this->playlistService->reorderSongs(
                $id,
                $request->input('song_positions'),
                $userId
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('Reorder playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Sıralama güncelleme başarısız',
            ], 500);
        }
    }

    /**
     * Delete user playlist
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $userId = auth('sanctum')->id();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ], 404);
            }

            // Sadece playlist sahibi silebilir
            if ($playlist->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu playlist\'i silemezsiniz',
                ], 403);
            }

            // Sistem playlistleri silinemez
            if ($playlist->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem playlistleri silinemez',
                ], 403);
            }

            $playlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Playlist silindi',
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Playlist silme başarısız',
            ], 500);
        }
    }
}
