<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Services\PlaylistService;
use Modules\Muzibu\App\Services\MuzibuCacheService;
use Illuminate\Support\Facades\Log;

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

            //🔒 FIXED: Use Eloquent (tenant-aware) + Only playlists with active songs
            $query = Playlist::where('is_active', 1)
                ->whereHas('songs', function($q) {
                    $q->where('is_active', 1);
                });

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
                    'song_count' => $playlist->songs()->where('is_active', 1)->count(),
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
                    'hls_path' => $song->hls_path,                    'lyrics' => $song->lyrics, // 🎤 Lyrics support (dynamic - null if not available)
                    'album_id' => $album?->album_id,
                    'album_title' => $album?->title,
                    'album_slug' => $album?->slug,
                    'album_cover' => $song->getCoverUrl(120, 120), // 🎨 Song cover (fallback to album)
                    'artist_id' => $artist?->artist_id,
                    'artist_title' => $artist?->title,
                    'artist_slug' => $artist?->slug,
                    'position' => $song->pivot->position ?? 0,
                ];
            });

            // Wrap in 'playlist' key for JS compatibility
            return response()->json([
                'playlist' => [
                    'id' => $playlist->playlist_id,
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
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Playlist show error:', ['playlist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get featured playlists
     * 🚀 OPTIMIZED: Use songs_count column instead of N+1 count query
     *
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        try {
            // 🚀 CACHE: Featured playlists rarely change (5 min TTL)
            $cacheKey = 'muzibu_featured_playlists_' . tenant()->id;

            $playlists = \Cache::remember($cacheKey, 300, function () {
                // 🔥 OPTIMIZED: Use songs_count column (cached in DB) instead of count() query
                // BEFORE: 10 count queries (N+1) = 1470ms
                // AFTER: 0 count queries = ~50ms
                return Playlist::where('is_active', 1)
                    ->where('is_system', 1)
                    ->where('songs_count', '>', 0) // Use cached column
                    ->orderBy('songs_count', 'desc')
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
                            'song_count' => $playlist->songs_count, // Use cached column
                        ];
                    });
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
            $userId = auth()->id();
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

            $userId = auth()->id();
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
     * Quick create playlist with songs + AI cover image generation
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

            $userId = auth()->id();

            // Create playlist
            $result = $this->playlistService->createPlaylistWithSongs(
                $request->all(),
                $userId
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            // ✅ AI Cover job'u PlaylistService içinde otomatik dispatch ediliyor (muzibu_generate_ai_cover)
            // Burada tekrar dispatch etmeye GEREK YOK!

            return response()->json($result, 201);

        } catch (\Exception $e) {
            Log::error('Quick create playlist error:', ['message' => $e->getMessage()]);
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
            \Log::info('[PlaylistController] addSong request', [
                'playlist_id' => $id,
                'song_id' => $request->input('song_id'),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            $request->validate([
                'song_id' => 'required|integer|exists:muzibu_songs,song_id',
            ]);

            $userId = auth()->id();
            $result = $this->playlistService->addSongToPlaylist(
                $id,
                $request->input('song_id'),
                $userId
            );

            \Log::info('[PlaylistController] addSong result', [
                'result' => $result,
            ]);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('[PlaylistController] Add song exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Şarkı ekleme başarısız: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add all songs from album to playlist
     */
    public function addAlbum(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'album_id' => 'required|integer|exists:muzibu_albums,album_id',
            ]);

            $userId = auth()->id();
            $albumId = $request->input('album_id');

            // Get album with active songs
            $album = Album::with(['songs' => function($query) {
                $query->where('is_active', 1);
            }])->findOrFail($albumId);

            $songs = $album->songs;

            if ($songs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Albümde aktif şarkı bulunamadı',
                ], 400);
            }

            // Add all songs to playlist
            $addedCount = 0;
            $skippedCount = 0;

            foreach ($songs as $song) {
                $result = $this->playlistService->addSongToPlaylist(
                    $id,
                    $song->song_id,
                    $userId
                );

                if ($result['success']) {
                    $addedCount++;
                } else {
                    $skippedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'added_count' => $addedCount,
                'skipped_count' => $skippedCount,
                'total_songs' => $songs->count(),
                'message' => "{$addedCount} şarkı playliste eklendi" . ($skippedCount > 0 ? " ({$skippedCount} zaten mevcuttu)" : ""),
            ]);

        } catch (\Exception $e) {
            Log::error('Add album to playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Albüm ekleme başarısız',
            ], 500);
        }
    }

    /**
     * Remove song from playlist
     */
    public function removeSong(int $id, int $songId): JsonResponse
    {
        try {
            $userId = auth()->id();
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

            $userId = auth()->id();
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
     * Copy playlist to user's library (different URL format for context menu)
     * POST /api/muzibu/playlists/{id}/copy
     */
    public function copy(Request $request, int $id): JsonResponse
    {
        try {
            $userId = auth()->id();

            // Get custom title from request or use default
            $customTitle = $request->input('title');

            $result = $this->playlistService->clonePlaylist($id, $userId, $customTitle);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            \Log::error('Playlist copy error:', ['playlist_id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Playlist kopyalama başarısız',
            ], 500);
        }
    }

    /**
     * Update user playlist
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_public' => 'nullable|boolean',
            ]);

            $userId = auth()->id();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Playlist bulunamadı',
                ], 404);
            }

            // Sadece playlist sahibi güncelleyebilir
            if ($playlist->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu playlist\'i güncelleyemezsiniz',
                ], 403);
            }

            // Sistem playlistleri güncellenemez
            if ($playlist->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem playlistleri güncellenemez',
                ], 403);
            }

            // Playlist'i güncelle
            $playlist->title = $request->input('title');
            $playlist->description = $request->input('description');

            if ($request->has('is_public')) {
                $playlist->is_public = $request->input('is_public');
            }

            $playlist->save();

            return response()->json([
                'success' => true,
                'message' => 'Playlist güncellendi',
                'data' => [
                    'playlist_id' => $playlist->playlist_id,
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'is_public' => $playlist->is_public,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Update playlist error:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Playlist güncelleme başarısız',
            ], 500);
        }
    }

    /**
     * Delete user playlist
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $userId = auth()->id();
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

    /**
     * 🤖 AI: Playlist Oluştur (AI Assistant için optimize edilmiş)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function aiCreate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'song_ids' => 'required|array|min:30|max:200', // İşyeri için tüm gün (30-200 şarkı = 2-12 saat)
                'song_ids.*' => 'required|integer|exists:songs,id',
                'mood' => 'nullable|string|max:50',
                'is_public' => 'nullable|boolean',
            ]);

            $userId = auth('sanctum')->id() ?? auth('web')->id();

            // Playlist oluştur
            $playlist = Playlist::create([
                'playlist_title' => ['tr' => $validated['name'], 'en' => $validated['name']],
                'playlist_description' => ['tr' => $validated['description'] ?? '', 'en' => $validated['description'] ?? ''],
                'playlist_type' => 'user',
                'user_id' => $userId,
                'is_active' => true,
                'is_public' => $validated['is_public'] ?? false,
                'is_system' => false,
                'play_count' => 0,
            ]);

            // Şarkıları ekle (cache count'ları da güncelle)
            $playlist->attachManySongsWithCache($validated['song_ids']);

            \Log::info('🤖 AI Playlist Created', [
                'playlist_id' => $playlist->id,
                'name' => $validated['name'],
                'song_count' => count($validated['song_ids']),
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'playlist_id' => $playlist->id,
                    'name' => $playlist->title,
                    'slug' => $playlist->slug,
                    'song_count' => count($validated['song_ids']),
                    'play_url' => route('muzibu.playlist.show', $playlist->slug),
                    'message' => "✅ '{$playlist->title}' playlist'i oluşturuldu!"
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Playlist Create Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Playlist oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🤖 AI: Playlist'e Toplu Şarkı Ekle
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function aiAddSongs(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'song_ids' => 'required|array|min:1',
                'song_ids.*' => 'required|integer|exists:songs,id',
            ]);

            $playlist = Playlist::findOrFail($id);
            $userId = auth('sanctum')->id() ?? auth('web')->id();

            // Sadece playlist sahibi ekleyebilir
            if ($playlist->user_id && $playlist->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu playlist\'e ekleme yetkiniz yok',
                ], 403);
            }

            // Mevcut şarkıları al
            $existingSongIds = $playlist->songs()->pluck('id')->toArray();

            // Yeni şarkıları filtrele (duplicate check)
            $newSongIds = collect($validated['song_ids'])
                ->diff($existingSongIds)
                ->values()
                ->toArray();

            // Yeni şarkıları ekle (cache count'ları da güncelle)
            if (!empty($newSongIds)) {
                $playlist->attachManySongsWithCache($newSongIds);
            }

            \Log::info('🤖 AI Songs Added to Playlist', [
                'playlist_id' => $playlist->id,
                'requested_count' => count($validated['song_ids']),
                'added_count' => count($newSongIds),
                'duplicate_count' => count($validated['song_ids']) - count($newSongIds),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'added_count' => count($newSongIds),
                    'duplicate_count' => count($validated['song_ids']) - count($newSongIds),
                    'total_songs' => $playlist->songs()->count(),
                    'message' => count($newSongIds) > 0
                        ? "✅ " . count($newSongIds) . " şarkı eklendi!"
                        : "ℹ️ Tüm şarkılar zaten playlist'te"
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Add Songs Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Şarkılar eklenirken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🎯 Create playlist from AI ACTION button
     * Route: POST /api/muzibu/ai/playlist/create
     * Middleware: auth:sanctum (must be authenticated)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createFromAI(Request $request): JsonResponse
    {
        try {
            // 1. Validate
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'song_ids' => 'required|array|min:1',
                'song_ids.*' => 'required|integer|exists:muzibu_songs,song_id',
            ]);

            $userId = auth()->id();

            // 🔒 Günlük limit kontrolü (max 20 playlist/gün)
            $todayCount = Playlist::where('user_id', $userId)
                ->where('is_system', false)
                ->whereDate('created_at', today())
                ->count();

            if ($todayCount >= 20) {
                return response()->json([
                    'success' => false,
                    'message' => 'Günlük playlist oluşturma limitine ulaştınız. Maksimum 20 playlist/gün oluşturabilirsiniz.',
                ], 429); // 429 Too Many Requests
            }

            // 2. Create playlist for authenticated user
            $playlist = Playlist::create([
                'title' => ['tr' => $validated['title']],
                'slug' => ['tr' => \Str::slug($validated['title'])],
                'description' => ['tr' => 'AI tarafından oluşturulmuş playlist'],
                'user_id' => $userId,
                'is_active' => true,
                'is_public' => false, // Default: Private
                'is_system' => false,
            ]);

            // 3. Attach songs (cache count'ları da güncelle)
            $playlist->attachManySongsWithCache($validated['song_ids']);

            // 4. 🎨 AI Cover Generation (Background) - Universal Helper
            $firstSong = Song::with('album.artist')->find($validated['song_ids'][0] ?? null);
            $titleContext = $playlist->title;

            if ($firstSong) {
                $titleContext .= " featuring " . $firstSong->title;
            }

            muzibu_generate_ai_cover($playlist, $titleContext, 'playlist');

            // 5. Check if user is premium (from central database)
            $isPremium = $this->checkUserPremium($userId);

            // 6. Return response
            \Log::info('🎯 Playlist created from AI ACTION button', [
                'user_id' => $userId,
                'playlist_id' => $playlist->playlist_id,
                'title' => $validated['title'],
                'song_count' => count($validated['song_ids']),
                'is_premium' => $isPremium,
            ]);

            return response()->json([
                'success' => true,
                'playlist_id' => $playlist->playlist_id,
                'playlist_url' => url("/playlist/" . ($playlist->slug['tr'] ?? $playlist->slug)),
                'can_play' => $isPremium,
                'message' => $isPremium
                    ? 'Playlist kaydedildi ve dinlemeye hazır!'
                    : 'Playlist kaydedildi! Premium ile dinleyebilirsiniz.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Create playlist from AI error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Playlist oluşturulurken hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if user has active premium subscription
     * 🔴 TEK KAYNAK: users.subscription_expires_at (tenant database)
     *
     * @param int $userId
     * @return bool
     */
    protected function checkUserPremium(int $userId): bool
    {
        try {
            // 🔴 TEK KAYNAK: users.subscription_expires_at
            $user = \App\Models\User::find($userId);
            return $user ? $user->isPremium() : false;
        } catch (\Exception $e) {
            \Log::error('Premium check error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
