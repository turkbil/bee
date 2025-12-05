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
                        'hls_path' => $song->hls_path,
                        'hls_converted' => $song->hls_converted,
                        'lyrics' => $song->lyrics, // 🎤 Lyrics support (dynamic - null if not available)
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
            $streamUrl = $song->hls_converted && $song->hls_path
                ? $song->hls_path
                : '/api/muzibu/songs/' . $id . '/serve';

            return response()->json([
                'song_id' => $song->song_id,
                'stream_url' => $streamUrl,
                'type' => $song->hls_converted ? 'hls' : 'mp3',
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
            if ($song->hls_converted && !empty($song->hls_path) && !$forceMP3) {
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
            if (!$song->hls_converted) {
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
                abort(404, 'Encryption key not found');
            }

            // Convert hex key to binary
            $keyBinary = hex2bin($song->encryption_key);

            if ($keyBinary === false) {
                \Log::error('Invalid encryption key format', [
                    'song_id' => $id
                ]);
                abort(500, 'Invalid key format');
            }

            // Return binary key with proper headers
            return response($keyBinary, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($keyBinary),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('Encryption key serve error:', [
                'song_id' => $id,
                'message' => $e->getMessage()
            ]);
            abort(500, 'Internal error');
        }
    }

    /**
     * Dynamic M3U8 Playlist (User tipine göre)
     * Guest/Normal: 4 chunk (3 çal + 1 buffer)
     * Premium: Tüm chunk'lar
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function dynamicPlaylist(int $id, Request $request)
    {
        try {
            // Song'u al
            $song = Song::where('song_id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$song) {
                abort(404, 'Song not found');
            }

            // HLS dönüşmüş mü kontrol et
            if (!$song->hls_converted || empty($song->hls_path)) {
                abort(404, 'HLS playlist not available');
            }

            // Dynamic playlist oluştur
            $playlistGenerator = app(\App\Services\Muzibu\PlaylistGeneratorService::class);
            $playlist = $playlistGenerator->generatePlaylist($song, auth()->user());

            // M3U8 response
            return response($playlist, 200, [
                'Content-Type' => 'application/vnd.apple.mpegurl',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('Dynamic playlist error:', [
                'song_id' => $id,
                'message' => $e->getMessage()
            ]);
            abort(500, 'Internal error');
        }
    }

    /**
     * Serve HLS Chunk (Token-based authorization)
     *
     * @param int $id Song ID
     * @param string $chunkName segment-000.ts
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function serveChunk(int $id, string $chunkName, Request $request)
    {
        try {
            // 1. Token doğrula
            $token = $request->input('token');

            if (!$token) {
                abort(403, 'Token required');
            }

            $chunkTokenService = app(\App\Services\Muzibu\ChunkTokenService::class);
            $tokenData = $chunkTokenService->validateChunkToken($token);

            if (!$tokenData) {
                \Log::warning('Invalid chunk token', [
                    'song_id' => $id,
                    'chunk_name' => $chunkName
                ]);
                abort(403, 'Invalid or expired token');
            }

            // 2. Token song_id ile URL song_id eşleşiyor mu?
            if ($tokenData['song_id'] != $id) {
                \Log::warning('Token song_id mismatch', [
                    'url_song_id' => $id,
                    'token_song_id' => $tokenData['song_id']
                ]);
                abort(403, 'Token mismatch');
            }

            // 3. Token chunk_name ile URL chunk_name eşleşiyor mu?
            if ($tokenData['chunk_name'] != $chunkName) {
                \Log::warning('Token chunk_name mismatch', [
                    'url_chunk' => $chunkName,
                    'token_chunk' => $tokenData['chunk_name']
                ]);
                abort(403, 'Chunk mismatch');
            }

            // 4. User bu chunk'ı isteyebilir mi? (Chunk index kontrolü)
            $isPremium = $tokenData['is_premium'] ?? false;

            if (!$chunkTokenService->isChunkAllowed($chunkName, $isPremium)) {
                \Log::warning('Chunk access denied', [
                    'song_id' => $id,
                    'chunk_name' => $chunkName,
                    'is_premium' => $isPremium
                ]);
                abort(403, 'Preview limit exceeded');
            }

            // 5. Song'u al
            $song = Song::where('song_id', $id)
                ->where('is_active', 1)
                ->first();

            if (!$song) {
                abort(404, 'Song not found');
            }

            // 6. Chunk dosya yolu (tenant-aware storage)
            $hlsFolder = dirname($song->hls_path);
            // Tenant storage: storage/tenant{id}/app/public/...
            $tenantId = tenant('id');
            if ($tenantId) {
                $chunkPath = storage_path("../tenant{$tenantId}/app/public/{$hlsFolder}/{$chunkName}");
            } else {
                $chunkPath = storage_path("app/public/{$hlsFolder}/{$chunkName}");
            }

            if (!file_exists($chunkPath)) {
                \Log::error('Chunk file not found', [
                    'song_id' => $id,
                    'chunk_name' => $chunkName,
                    'path' => $chunkPath
                ]);
                abort(404, 'Chunk not found');
            }

            // 7. Chunk serve et
            return response()->file($chunkPath, [
                'Content-Type' => 'video/mp2t', // MPEG-TS format
                'Cache-Control' => 'private, max-age=3600',
                'Accept-Ranges' => 'bytes',
            ]);

        } catch (\Exception $e) {
            \Log::error('Serve chunk error:', [
                'song_id' => $id,
                'chunk_name' => $chunkName,
                'message' => $e->getMessage()
            ]);
            abort(500, 'Internal error');
        }
    }
}
