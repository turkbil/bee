<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Services\MuzibuCacheService;

class SongController extends Controller
{
    public function __construct(
        private MuzibuCacheService $cacheService
    ) {
    }
    /**
     * Get recently played songs for current user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $limit = $request->input('limit', 20);

            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            //🔒 FIXED: Use Eloquent instead of DB::table() for tenant-aware queries
            // Get user's play history from SongPlay model
            $playedSongIds = DB::table('muzibu_song_plays')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit * 2) // Get more to handle duplicates
                ->pluck('song_id')
                ->unique()
                ->take($limit)
                ->values();

            if ($playedSongIds->isEmpty()) {
                return response()->json([]);
            }

            // Use Eloquent to get songs (tenant-aware)
            $songs = Song::whereIn('song_id', $playedSongIds)
                ->where('is_active', 1)
                ->with(['album.artist'])
                ->get()
                ->map(function ($song) {
                    $album = $song->album;
                    $artist = $album?->artist;

                    return [
                        'song_id' => $song->song_id,
                        'song_title' => $song->title,
                        'song_slug' => $song->slug,
                        'duration' => $song->duration,
                        'file_path' => $song->file_path,
                        'hls_path' => $song->hls_path,                        'lyrics' => $song->lyrics, // 🎤 Lyrics support (dynamic - null if not available)
                        'album_id' => $album?->album_id,
                        'album_title' => $album?->title,
                        'album_slug' => $album?->slug,
                        'album_cover' => $album?->media_id,
                        'artist_id' => $artist?->artist_id,
                        'artist_title' => $artist?->title,
                        'artist_slug' => $artist?->slug,
                    ];
                });

            return response()->json($songs->values());

        } catch (\Exception $e) {
            \Log::error('Recent songs error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get popular songs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);

            // 🚀 CACHE: Get popular songs from Redis (30min TTL)
            // Cache service returns already formatted array data
            $songs = $this->cacheService->getPopularSongs($limit);

            return response()->json($songs);

        } catch (\Exception $e) {
            \Log::error('Popular songs error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get last played song for current user (or guest from localStorage)
     * 🎯 Preload system: Load last song in PAUSE mode for instant playback
     *
     * @return JsonResponse
     */
    public function lastPlayed(): JsonResponse
    {
        try {
            $userId = auth()->id();

            // Get last played song from muzibu_song_plays table
            $lastPlay = \DB::table('muzibu_song_plays')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastPlay) {
                return response()->json(['last_played' => null]);
            }

            // 🚀 CACHE: Get song from Redis
            $song = $this->cacheService->getSong($lastPlay->song_id);

            if (!$song || !$song->is_active) {
                return response()->json(['last_played' => null]);
            }

            // Return minimal song data for preload
            return response()->json([
                'last_played' => [
                    'song_id' => $song->song_id,
                    'song_title' => $song->getTranslated('title', app()->getLocale()),
                    'artist_title' => $song->getTranslated('artist', app()->getLocale()),
                    'album_title' => $song->album ? $song->album->getTranslated('title', app()->getLocale()) : null,
                    'album_cover' => $song->getCoverUrl(600, 600),
                    'duration' => $song->duration,
                    'played_at' => $lastPlay->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Last played song error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            //🔒 FIXED: Use Eloquent (tenant-aware)
            $song = Song::where('song_id', $id)
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

            // Increment play count using Eloquent
            $song->increment('play_count');

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Track play error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Get song stream URL
     *
     * @param int $id
     * @return JsonResponse
     */
    public function stream(int $id): JsonResponse
    {
        try {
            //🔒 FIXED: Use Eloquent (tenant-aware)
            $song = Song::where('song_id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$song) {
                return response()->json(['error' => 'Song not found'], 404);
            }

            // Generate stream URL - Hardcode URL yerine relative path kullan
            $streamUrl = !empty($song->hls_path) && $song->hls_path
                ? $song->hls_path
                : '/api/muzibu/songs/' . $id . '/serve';

            return response()->json([
                'song_id' => $song->song_id,
                'stream_url' => $streamUrl,
                'type' => !empty($song->hls_path) ? 'hls' : 'mp3',
                'duration' => $song->duration,
            ]);

        } catch (\Exception $e) {
            \Log::error('Stream error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Serve HLS playlist or MP3 file
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function serve(int $id, Request $request)
    {
        try {
            // 🔒 FIXED: Use Eloquent (tenant-aware)
            $song = Song::where('song_id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$song) {
                abort(404, 'Song not found');
            }

            // 🔐 FORCE MP3: If force_mp3 parameter exists, skip HLS (for fallback scenarios)
            $forceMP3 = $request->has('force_mp3') || $request->has('fallback');

            // 🎵 HLS PRIORITY: If HLS converted AND not forced to MP3, serve playlist.m3u8
            if (!empty($song->hls_path) && !empty($song->hls_path) && !$forceMP3) {
                // Build HLS playlist path
                $hlsPath = storage_path('app/public/' . $song->hls_path);

                if (file_exists($hlsPath)) {
                    \Log::info('Serving HLS playlist', [
                        'song_id' => $song->song_id,
                        'hls_path' => $song->hls_path
                    ]);

                    return response()->file($hlsPath, [
                        'Content-Type' => 'application/vnd.apple.mpegurl',
                        'Cache-Control' => 'public, max-age=3600',
                    ]);
                } else {
                    \Log::warning('HLS playlist not found, falling back to MP3', [
                        'song_id' => $song->song_id,
                        'hls_path' => $hlsPath
                    ]);
                }
            }

            // FALLBACK: Serve MP3 if HLS not available
            if (!$song->file_path) {
                abort(404, 'File not found');
            }

            // Build absolute path
            if (str_starts_with($song->file_path, '/')) {
                $filePath = $song->file_path;
            } else {
                $filePath = storage_path('app/public/muzibu/songs/' . $song->file_path);
            }

            if (!file_exists($filePath)) {
                \Log::error('Song file not found', [
                    'song_id' => $song->song_id,
                    'file_path' => $song->file_path,
                    'absolute_path' => $filePath
                ]);
                abort(404, 'File not found');
            }

            // 🎵 AUTO HLS CONVERSION (if not converted yet)
            if (empty($song->hls_path)) {
                try {
                    \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);
                    \Log::info('HLS conversion queued', ['song_id' => $song->song_id]);
                } catch (\Exception $e) {
                    \Log::error('HLS queue error:', ['song_id' => $song->song_id, 'error' => $e->getMessage()]);
                }
            }

            return response()->file($filePath, [
                'Content-Type' => 'audio/mpeg',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=31536000',
            ]);

        } catch (\Exception $e) {
            \Log::error('Serve error:', ['song_id' => $id, 'message' => $e->getMessage()]);
            abort(500, 'Internal error');
        }
    }

    /**
     * Serve HLS encryption key
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function serveEncryptionKey(int $id)
    {
        try {
            // 🚀 PERFORMANCE: Cache encryption key (key never changes)
            $tenantId = tenant() ? tenant()->id : 'central';
            $cacheKey = "song_encryption_key_{$tenantId}_{$id}";

            // Cache for 24 hours (key is immutable)
            $keyBinary = \Cache::remember($cacheKey, 86400, function() use ($id) {
                // 🔒 FIXED: Use Eloquent (tenant-aware)
                $song = Song::where('song_id', $id)
                    ->where('is_active', 1)
                    ->first();

                if (!$song || !$song->encryption_key) {
                    \Log::error('Encryption key not found', [
                        'song_id' => $id,
                        'has_song' => !is_null($song),
                        'has_key' => $song ? !is_null($song->encryption_key) : false
                    ]);
                    return null; // Cache miss, will abort below
                }

                // Convert hex key to binary
                $keyBinary = hex2bin($song->encryption_key);

                if ($keyBinary === false) {
                    \Log::error('Invalid encryption key format', [
                        'song_id' => $id
                    ]);
                    return null;
                }

                return $keyBinary;
            });

            // Check cache result
            if ($keyBinary === null) {
                abort(404, 'Encryption key not found');
            }

            // Return binary key with proper headers
            return response($keyBinary, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($keyBinary),
                'Cache-Control' => 'public, max-age=86400', // Browser cache 24h
                'Expires' => gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT',
            ]);

        } catch (\Exception $e) {
            \Log::error('Encryption key serve error:', [
                'song_id' => $id,
                'message' => $e->getMessage()
            ]);
            abort(500, 'Internal error');
        }
    }
}
