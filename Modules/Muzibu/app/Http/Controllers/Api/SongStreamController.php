<?php

namespace Modules\Muzibu\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\ConvertToHLSJob;
use App\Services\SignedUrlService;
use Modules\Muzibu\App\Services\MuzibuCacheService;

class SongStreamController extends Controller
{
    protected $signedUrlService;
    protected $cacheService;

    public function __construct(
        SignedUrlService $signedUrlService,
        MuzibuCacheService $cacheService
    ) {
        $this->signedUrlService = $signedUrlService;
        $this->cacheService = $cacheService;
    }

    /**
     * Get song stream URL (triggers lazy HLS conversion if needed)
     * 🔐 Returns SIGNED URLs for security
     *
     * @param int $songId
     * @return JsonResponse
     */
    public function stream(int $songId): JsonResponse
    {
        try {
            // 🚀 CACHE: Get song from Redis (24h TTL)
            $song = $this->cacheService->getSong($songId);

            if (!$song) {
                return response()->json(['error' => 'Song not found'], 404);
            }

            $user = auth()->user();

            // 🎵 YENİ MANTIK:
            // - Guest (üye değil) → 30 saniye preview
            // - Normal üye (premium değil) → 30 saniye preview
            // - Premium üye → Sınırsız

            // Guest kullanıcı → 30 saniye preview
            if (!$user) {
                // HLS conversion başlat (eğer gerekiyorsa)
                if ($song->needsHlsConversion()) {
                    Log::info('Muzibu Stream: HLS conversion dispatched for guest', [
                        'song_id' => $songId,
                        'title' => $song->getTranslated('title', 'en')
                    ]);
                    ConvertToHLSJob::dispatch($song);
                }

                // HLS'e dönüşmüşse HLS URL, yoksa MP3 serve endpoint
                // 🚀 CACHE: Song already from cache, no need to refresh

                if ($song->hls_converted && !empty($song->hls_path)) {
                    // 🎯 DYNAMIC PLAYLIST (4 chunk: 3 çal + 1 buffer)
                    $streamUrl = route('api.api.muzibu.songs.dynamic-playlist', ['id' => $songId]);
                    $streamType = 'hls';
                    // 🔐 MP3 fallback (signed URL, force MP3 output)
                    $fallbackUrl = $this->signedUrlService->generateStreamUrl($songId, 30, true);
                } else {
                    // 🔐 SIGNED MP3 URL (30 dakika)
                    $streamUrl = $this->signedUrlService->generateStreamUrl($songId, 30);
                    $streamType = 'mp3';
                    $fallbackUrl = null; // No fallback for MP3
                }

                return response()->json([
                    'status' => 'preview',
                    'message' => 'Kayıt olun, tam dinleyin',
                    'stream_url' => $streamUrl, // 🎯 Dynamic playlist URL
                    'stream_type' => $streamType,
                    'fallback_url' => $fallbackUrl, // 🔐 SIGNED MP3 fallback (HLS fails)
                    'preview_duration' => 30,
                    'preview_chunks' => 3,        // 3 chunk çalacak
                    'buffer_chunks' => 1,         // 1 chunk buffer
                    'total_chunks_served' => 4,  // Toplam 4 chunk yüklenecek
                    'is_premium' => false,
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                    ]
                ]);
            }

            // Normal üye (premium değil) → 30 saniye preview
            if (!$user->isPremium()) {
                // HLS conversion başlat (eğer gerekiyorsa)
                if ($song->needsHlsConversion()) {
                    Log::info('Muzibu Stream: HLS conversion dispatched for non-premium user', [
                        'song_id' => $songId,
                        'user_id' => $user->id,
                        'title' => $song->getTranslated('title', 'en')
                    ]);
                    ConvertToHLSJob::dispatch($song);
                }

                // HLS'e dönüşmüşse HLS URL, yoksa MP3 serve endpoint
                // 🚀 CACHE: Song already from cache, no need to refresh

                if ($song->hls_converted && !empty($song->hls_path)) {
                    // 🎯 DYNAMIC PLAYLIST (4 chunk: 3 çal + 1 buffer)
                    $streamUrl = route('api.api.muzibu.songs.dynamic-playlist', ['id' => $songId]);
                    $streamType = 'hls';
                    // 🔐 MP3 fallback (signed URL, force MP3 output)
                    $fallbackUrl = $this->signedUrlService->generateStreamUrl($songId, 30, true);
                } else {
                    // 🔐 SIGNED MP3 URL (30 dakika)
                    $streamUrl = $this->signedUrlService->generateStreamUrl($songId, 30);
                    $streamType = 'mp3';
                    $fallbackUrl = null; // No fallback for MP3
                }

                return response()->json([
                    'status' => 'preview',
                    'message' => 'Premium\'a geçin, sınırsız dinleyin',
                    'stream_url' => $streamUrl, // 🎯 Dynamic playlist URL
                    'stream_type' => $streamType,
                    'fallback_url' => $fallbackUrl, // 🔐 SIGNED MP3 fallback (HLS fails)
                    'preview_duration' => 30,
                    'preview_chunks' => 3,        // 3 chunk çalacak
                    'buffer_chunks' => 1,         // 1 chunk buffer
                    'total_chunks_served' => 4,  // Toplam 4 chunk yüklenecek
                    'is_premium' => false,
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                    ]
                ]);
            }

            // ⚠️ 3/3 KURAL DEVRE DIŞI (Disable - Silme!)
            // if (!$user->canPlaySong()) {
            //     return response()->json([
            //         'status' => 'limit_exceeded',
            //         'message' => 'Günlük 3 şarkı limitiniz doldu',
            //         'played_today' => $user->getTodayPlayedCount(),
            //         'limit' => 3,
            //         'remaining' => 0,
            //         'is_premium' => $user->isPremium(),
            //     ], 200);
            // }

            // Check if song needs HLS conversion
            if ($song->needsHlsConversion()) {
                Log::info('Muzibu Stream: HLS conversion needed', [
                    'song_id' => $songId,
                    'title' => $song->getTranslated('title', 'en')
                ]);

                // Dispatch conversion job
                ConvertToHLSJob::dispatch($song);

                // Return original MP3 URL for now (SIGNED)
                return response()->json([
                    'status' => 'converting',
                    'message' => 'HLS conversion in progress. Playing original file.',
                    'stream_url' => $this->signedUrlService->generateStreamUrl($songId, 30), // 🔐 SIGNED URL
                    'stream_type' => 'mp3',
                    'hls_converting' => true,
                    'is_premium' => $user->isPremium(), // 🔄 Frontend sync için güncel durum
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'bitrate' => $song->getFormattedBitrate(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                    ]
                ]);
            }

            // HLS already converted - return HLS URL (SIGNED)
            return response()->json([
                'status' => 'ready',
                'message' => 'HLS stream ready',
                'stream_url' => $this->signedUrlService->generateHlsUrl($songId, 60), // 🔐 SIGNED HLS URL
                'stream_type' => 'hls',
                'fallback_url' => $this->signedUrlService->generateStreamUrl($songId, 30, true), // 🔐 SIGNED MP3 fallback (force MP3)
                'hls_converting' => false,
                'remaining' => $user->getRemainingPlays(),
                'is_premium' => $user->isPremium(), // 🔄 Frontend sync için güncel durum
                'song' => [
                    'id' => $song->song_id,
                    'title' => $song->getTranslated('title', app()->getLocale()),
                    'duration' => $song->getFormattedDuration(),
                    'bitrate' => $song->getFormattedBitrate(),
                    'cover_url' => $song->getCoverUrl(600, 600),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu Stream: Failed to get stream URL', [
                'song_id' => $songId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get stream URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check HLS conversion status
     *
     * @param int $songId
     * @return JsonResponse
     */
    public function checkConversionStatus(int $songId): JsonResponse
    {
        try {
            $song = Song::findOrFail($songId);

            return response()->json([
                'status' => 'success',
                'hls_converted' => $song->hls_converted,
                'hls_url' => $song->hls_converted ? $song->getHlsUrl() : null,
                'needs_conversion' => $song->needsHlsConversion(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check conversion status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Increment play count
     *
     * @param int $songId
     * @return JsonResponse
     */
    public function incrementPlayCount(int $songId): JsonResponse
    {
        try {
            $song = Song::findOrFail($songId);

            $userId = auth()->check() ? auth()->id() : null;
            $song->incrementPlayCount($userId);

            return response()->json([
                'status' => 'success',
                'play_count' => $song->play_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to increment play count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track listening progress (Premium system)
     * Her 5 saniyede frontend tarafından çağrılır
     *
     * @param \Illuminate\Http\Request $request
     * @param int $songId
     * @return JsonResponse
     */
    public function trackProgress(\Illuminate\Http\Request $request, int $songId): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'unauthorized'], 401);
            }

            $song = Song::findOrFail($songId);
            $userId = auth()->id();

            // 🔒 Duplicate kontrolü: Aynı kullanıcı + şarkı için son 60 saniyede kayıt var mı?
            $recentPlay = \DB::table('muzibu_song_plays')
                ->where('song_id', $songId)
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->first();

            // Eğer son 60 saniyede zaten kayıt varsa, duplicate kayıt ekleme
            if ($recentPlay) {
                return response()->json([
                    'success' => true,
                    'duplicate_prevented' => true,
                    'remaining' => -1
                ]);
            }

            // 60+ saniye dinlendi, kayıt ekle (Analytics için)
            \DB::table('muzibu_song_plays')->insert([
                'song_id' => $songId,
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->detectDevice($request),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // ⚠️ 3/3 KURAL DEVRE DIŞI - remaining her zaman -1 (sınırsız)
            // Tracking sadece analytics için yapılıyor
            return response()->json([
                'success' => true,
                'remaining' => -1  // Unlimited (3/3 rule removed)
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Failed to track progress', [
                'song_id' => $songId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'tracking_failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect device type from user agent
     */
    private function detectDevice(\Illuminate\Http\Request $request): string
    {
        $userAgent = strtolower($request->userAgent());

        if (preg_match('/mobile|android|iphone|ipod/', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/ipad|tablet/', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
