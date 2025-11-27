<?php

namespace Modules\Muzibu\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\ConvertToHLSJob;

class SongStreamController extends Controller
{
    /**
     * Get song stream URL (triggers lazy HLS conversion if needed)
     *
     * @param int $songId
     * @return JsonResponse
     */
    public function stream(int $songId): JsonResponse
    {
        try {
            $song = Song::findOrFail($songId);
            $song->refresh(); // Fresh data from database
            $user = auth()->user();

            // Guest kullanıcı → 30 saniye preview + HLS conversion başlat
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
                $song->refresh(); // Ensure fresh data from database

                if ($song->hls_converted && !empty($song->hls_path)) {
                    $tenantId = tenant() ? tenant()->id : '';
                    $streamUrl = url("/storage/tenant{$tenantId}/" . $song->hls_path);
                    $streamType = 'hls';
                } else {
                    $streamUrl = "/api/muzibu/songs/{$songId}/serve";
                    $streamType = 'mp3';
                }

                return response()->json([
                    'status' => 'preview',
                    'message' => 'Kayıt olun, tam dinleyin',
                    'stream_url' => $streamUrl,
                    'stream_type' => $streamType,
                    'preview_duration' => 30,
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                    ]
                ]);
            }

            // Premium/Trial limit kontrolü
            if (!$user->canPlaySong()) {
                return response()->json([
                    'status' => 'limit_exceeded',
                    'message' => 'Günlük 3 şarkı limitiniz doldu',
                    'played_today' => $user->getTodayPlayedCount(),
                    'limit' => 3,
                    'remaining' => 0
                ], 200); // 200 OK - JSON içinde status var
            }

            // Check if song needs HLS conversion
            if ($song->needsHlsConversion()) {
                Log::info('Muzibu Stream: HLS conversion needed', [
                    'song_id' => $songId,
                    'title' => $song->getTranslated('title', 'en')
                ]);

                // Dispatch conversion job
                ConvertToHLSJob::dispatch($song);

                // Return original MP3 URL for now
                return response()->json([
                    'status' => 'converting',
                    'message' => 'HLS conversion in progress. Playing original file.',
                    'stream_url' => "/api/muzibu/songs/{$songId}/serve",
                    'stream_type' => 'mp3',
                    'hls_converting' => true,
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'bitrate' => $song->getFormattedBitrate(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                    ]
                ]);
            }

            // HLS already converted - return HLS URL
            $tenantId = tenant() ? tenant()->id : '';
            return response()->json([
                'status' => 'ready',
                'message' => 'HLS stream ready',
                'stream_url' => url("/storage/tenant{$tenantId}/" . $song->hls_path),
                'stream_type' => 'hls',
                'hls_converting' => false,
                'remaining' => $user->getRemainingPlays(),
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

            // 60+ saniye dinlendi, kayıt ekle (JS zaten kontrol etti)
            \DB::table('muzibu_song_plays')->insert([
                'song_id' => $songId,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->detectDevice($request),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'remaining' => auth()->user()->getRemainingPlays()
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
