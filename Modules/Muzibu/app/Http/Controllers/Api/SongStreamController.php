<?php

namespace Modules\Muzibu\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\ConvertToHLSJob;
use App\Services\SignedUrlService;
use Modules\Muzibu\App\Services\MuzibuCacheService;
use Modules\Muzibu\App\Services\DeviceService;

class SongStreamController extends Controller
{
    protected $signedUrlService;
    protected $cacheService;
    protected $deviceService;

    public function __construct(
        SignedUrlService $signedUrlService,
        MuzibuCacheService $cacheService,
        DeviceService $deviceService
    ) {
        $this->signedUrlService = $signedUrlService;
        $this->cacheService = $cacheService;
        $this->deviceService = $deviceService;
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

            // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
            // Session-based login (web) veya token-based login (sanctum)
            $user = auth('web')->user() ?? auth('sanctum')->user();

            // 🎵 YENİ MANTIK (2025-12-12):
            // - Guest (üye değil) → Direkt /register yönlendirme (0 saniye dinleme)
            // - Normal üye (premium/trial değil) → Direkt /subscription/plans yönlendirme (0 saniye dinleme)
            // - Premium/Trial üye → Sınırsız dinleme

            // 🚫 Guest kullanıcı → Kayıt olmadan dinleyemez
            if (!$user) {
                return response()->json([
                    'status' => 'unauthorized',
                    'redirect' => '/register',
                    'message' => 'Şarkı dinlemek için kayıt olmalısınız',
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'cover_url' => $song->getCoverUrl(600, 600),
                        // 🎯 AUTO-CONTEXT: Include album & genre for auto-context detection
                        'album_id' => $song->album_id,
                        'genre_id' => $song->genre_id,
                        'album_name' => $song->album ? $song->album->getTranslated('title', app()->getLocale()) : null,
                        'genre_name' => $song->genre ? $song->genre->getTranslated('title', app()->getLocale()) : null,
                    ]
                ], 401);
            }

            // 🔐 DEVICE LIMIT CHECK: DEVRE DIŞI BIRAKILDI
            // ⚠️ SORUN: Stream API 'web' middleware kullanıyor ve farklı session ID görüyor!
            // checkSession (api middleware) doğru session görüyor ama stream API yanlış görüyor.
            // Bu race condition polling ile çözülüyor (5 saniyede bir checkSession çalışıyor)
            // Device limit kontrolü polling tarafından yapılıyor, stream API'de tekrar kontrol gereksiz.
            //
            // @TODO: Stream route'u için session handling düzeltilince bu kontrol aktif edilebilir
            // Şimdilik polling yeterli - device limit aşılırsa polling yakalayacak.
            //
            // if ($this->deviceService->shouldRun()) { ... }

            // 🚫 Normal üye (premium veya trial değil) → Subscription sayfasına yönlendir
            // 🔥 FIX: isPremiumOrTrial() helper kullanılıyor
            // 🚀 SMART CACHE: 5 dakikalık cache ile balance (güvenlik vs performans)
            // Event-based invalidation: Subscription değişince cache temizlenir
            if (!$user->isPremiumOrTrial()) {
                return response()->json([
                    'status' => 'subscription_required',
                    'redirect' => '/subscription/plans',
                    'message' => 'Şarkı dinlemek için premium üyelik gereklidir',
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'cover_url' => $song->getCoverUrl(600, 600),
                        // 🎯 AUTO-CONTEXT: Include album & genre for auto-context detection
                        'album_id' => $song->album_id,
                        'genre_id' => $song->genre_id,
                        'album_name' => $song->album ? $song->album->getTranslated('title', app()->getLocale()) : null,
                        'genre_name' => $song->genre ? $song->genre->getTranslated('title', app()->getLocale()) : null,
                    ]
                ], 402);
            }

            // Check if song needs HLS conversion
            if ($song->needsHlsConversion()) {
                Log::info('Muzibu Stream: HLS conversion needed', [
                    'song_id' => $songId,
                    'title' => $song->getTranslated('title', 'en')
                ]);

                // Dispatch conversion job
                ConvertToHLSJob::dispatch($song);

                // Return original MP3 URL for now (SIGNED)
                return response()->json(array_merge([
                    'status' => 'converting',
                    'message' => 'HLS conversion in progress. Playing original file.',
                    'stream_url' => $this->signedUrlService->generateStreamUrl($songId, 30), // 🔐 SIGNED URL
                    'stream_type' => 'mp3',
                    'hls_converting' => true,
                    'song' => [
                        'id' => $song->song_id,
                        'title' => $song->getTranslated('title', app()->getLocale()),
                        'duration' => $song->getFormattedDuration(),
                        'bitrate' => $song->getFormattedBitrate(),
                        'cover_url' => $song->getCoverUrl(600, 600),
                        // 🎯 AUTO-CONTEXT: Include album & genre for auto-context detection
                        'album_id' => $song->album_id,
                        'genre_id' => $song->genre_id,
                        'album_name' => $song->album ? $song->album->getTranslated('title', app()->getLocale()) : null,
                        'genre_name' => $song->genre ? $song->genre->getTranslated('title', app()->getLocale()) : null,
                    ]
                ], $this->getSubscriptionData($user)));
            }

            // 🔥 Subscription bilgilerini al
            $subscriptionData = $this->getSubscriptionData($user);

            // HLS already converted - return HLS URL (SIGNED)
            return response()->json(array_merge([
                'status' => 'ready',
                'message' => 'HLS stream ready',
                'stream_url' => $this->signedUrlService->generateHlsUrl($songId, 60), // 🔐 SIGNED HLS URL
                'stream_type' => 'hls',
                'fallback_url' => $this->signedUrlService->generateStreamUrl($songId, 30, true), // 🔐 SIGNED MP3 fallback (force MP3)
                'hls_converting' => false,
                'song' => [
                    'id' => $song->song_id,
                    'title' => $song->getTranslated('title', app()->getLocale()),
                    'duration' => $song->getFormattedDuration(),
                    'bitrate' => $song->getFormattedBitrate(),
                    'cover_url' => $song->getCoverUrl(600, 600),
                    // 🎯 AUTO-CONTEXT: Include album & genre for auto-context detection
                    'album_id' => $song->album_id,
                    'genre_id' => $song->genre_id,
                    'album_name' => $song->album ? $song->album->getTranslated('title', app()->getLocale()) : null,
                    'genre_name' => $song->genre ? $song->genre->getTranslated('title', app()->getLocale()) : null,
                ]
            ], $subscriptionData));

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
                'status' => 'success',                'hls_url' => !empty($song->hls_path) ? $song->getHlsUrl() : null,
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

            // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
            $user = auth('web')->user() ?? auth('sanctum')->user();
            $userId = $user?->id;
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
            // 🔥 FIX: Hem web hem sanctum guard'ı kontrol et
            $user = auth('web')->user() ?? auth('sanctum')->user();

            if (!$user) {
                return response()->json(['error' => 'unauthorized'], 401);
            }

            $song = Song::findOrFail($songId);
            $userId = $user->id;

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
                    'duplicate_prevented' => true
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

            // 🔥 FIX: songs.play_count'u da artır
            $song->increment('play_count');

            Log::info('Muzibu: Play tracked', [
                'song_id' => $songId,
                'user_id' => $userId,
                'play_count' => $song->play_count
            ]);

            return response()->json([
                'success' => true,
                'play_count' => $song->play_count
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

    /**
     * Get subscription data for user (trial, premium, dates)
     * 🔥 Frontend'e subscription bilgileri gönder
     */
    protected function getSubscriptionData($user): array
    {
        if (!$user) {
            return [
                'is_premium' => false,
                'trial_ends_at' => null,
                'subscription_ends_at' => null,
            ];
        }

        // Aktif subscription var mı?
        $subscription = $user->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            })
            ->first();

        if (!$subscription) {
            return [
                'is_premium' => false,
                'trial_ends_at' => null,
                'subscription_ends_at' => null,
            ];
        }

        // Trial mı yoksa premium mı?
        $isTrial = $subscription->has_trial
            && $subscription->trial_ends_at
            && $subscription->trial_ends_at->isFuture();

        return [
            'is_premium' => true,
            'trial_ends_at' => $isTrial ? $subscription->trial_ends_at->toIso8601String() : null,
            'subscription_ends_at' => $subscription->current_period_end ? $subscription->current_period_end->toIso8601String() : null,
        ];
    }
}
