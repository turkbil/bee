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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            // 🚫 Guest kullanıcı → Giriş yapmadan dinleyemez
            if (!$user) {
                return response()->json([
                    'status' => 'unauthorized',
                    'redirect' => '/login',
                    'message' => trans('muzibu::front.auth.login_required'),
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

            // 🔐 DEVICE LIMIT CHECK - DISABLED (DeviceService kapalı)
            // Not: DeviceService ve timeout kapatıldı, token bazlı kontrol yapılmıyor

            // 🚫 Normal üye (premium veya trial değil) → Subscription sayfasına yönlendir
            // 🔥 FIX: isPremiumOrTrial() helper kullanılıyor
            // 🚀 SMART CACHE: 5 dakikalık cache ile balance (güvenlik vs performans)
            // Event-based invalidation: Subscription değişince cache temizlenir
            if (!$user->isPremiumOrTrial()) {
                return response()->json([
                    'status' => 'subscription_required',
                    'redirect' => '/subscription/plans',
                    'message' => trans('muzibu::front.auth.premium_required'),
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
                $mp3Url = $this->signedUrlService->generateStreamUrl($songId, 30);

                // 🔒 URL'leri şifrele - Console'da görünmesin
                $encryptedUrls = $this->encryptStreamUrls([
                    'stream_url' => $mp3Url,
                    'fallback_url' => $mp3Url,
                    'stream_type' => 'mp3',
                ], $songId, $user->id);

                return response()->json(array_merge([
                    'status' => 'converting',
                    'message' => 'HLS conversion in progress. Playing original file.',
                    // 🔒 Şifreli URL data
                    '_' => $encryptedUrls['_'],
                    '__' => $encryptedUrls['__'],
                    '___' => $encryptedUrls['___'],
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
                        // 🧪 TEST: HLS encryption info
                        'has_encryption_key' => !empty($song->encryption_key),
                        'has_encryption_iv' => !empty($song->encryption_iv),
                        'has_hls_path' => !empty($song->hls_path),
                        // 🎨 Player gradient colors
                        'color_hash' => $song->getOrGenerateColorHash(),
                    ]
                ], $this->getSubscriptionData($user)));
            }

            // 🔥 Subscription bilgilerini al
            $subscriptionData = $this->getSubscriptionData($user);

            // HLS already converted - return HLS URL (SIGNED)
            // 🔓 DeviceService kapalı - token bazlı kontrol yok, sadece user_id ile imza

            // 🔒 TTL: Şarkı süresine göre dinamik imza (uzun parçalarda timeout yaşamamak için)
            $durationSeconds = (int) ($song->duration ?? 0);
            $bufferSeconds = 300; // 5 dakika tampon
            $ttlSeconds = max(1800, min($durationSeconds + $bufferSeconds, 3600)); // min 30 dk, max 60 dk

            // User ID'yi token olarak kullan (DeviceService kapalı olduğu için)
            $hlsUrl = $this->signedUrlService->generateHlsUrl($songId, $ttlSeconds, (string) $user->id);
            $fallbackUrl = $this->signedUrlService->generateStreamUrl($songId, 30, true);

            // 🔒 URL'leri şifrele - Console'da görünmesin
            $encryptedUrls = $this->encryptStreamUrls([
                'stream_url' => $hlsUrl,
                'fallback_url' => $fallbackUrl,
                'stream_type' => 'hls',
            ], $songId, $user->id);

            return response()->json(array_merge([
                'status' => 'ready',
                'message' => 'HLS stream ready',
                // 🔒 Şifreli URL data
                '_' => $encryptedUrls['_'],
                '__' => $encryptedUrls['__'],
                '___' => $encryptedUrls['___'],
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
                    // 🧪 TEST: HLS encryption info (sadece var/yok - güvenlik için key değeri yok)
                    'has_encryption_key' => !empty($song->encryption_key),
                    'has_encryption_iv' => !empty($song->encryption_iv),
                    'has_hls_path' => !empty($song->hls_path),
                    // 🎨 Player gradient colors
                    'color_hash' => $song->getOrGenerateColorHash(),
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
     * Frontend 30 saniye sonra çağırır (play count +1, log with IP)
     *
     * @param \Illuminate\Http\Request $request
     * @param int $songId
     * @return JsonResponse
     */
    public function trackStart(\Illuminate\Http\Request $request, int $songId): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json(['error' => 'unauthorized'], 401);
            }

            $userId = $user->id;

            // 🔒 Duplicate kontrolü: Aynı kullanıcı + şarkı için son 5 saniyede kayıt var mı?
            $recentPlay = \DB::table('muzibu_song_plays')
                ->where('song_id', $songId)
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subSeconds(5))
                ->first();

            if ($recentPlay) {
                return response()->json([
                    'success' => true,
                    'play_id' => $recentPlay->id,
                    'duplicate_prevented' => true
                ]);
            }

            // 📊 HEMEN kayıt oluştur (abuse detection için)
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent($request->userAgent());

            $deviceType = 'desktop';
            if ($agent->isMobile()) {
                $deviceType = 'mobile';
            } elseif ($agent->isTablet()) {
                $deviceType = 'tablet';
            }

            // 🌐 Gelişmiş browser tespiti (jenssegers/agent yerine kendi metodumuz)
            $browserName = $this->detectBrowserFromUA($request->userAgent() ?? '');

            $playId = \DB::table('muzibu_song_plays')->insertGetId([
                'song_id' => $songId,
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $deviceType,
                'browser' => $browserName,
                'platform' => $agent->platform() ?: 'Unknown',
                'source_type' => $request->input('source_type'),
                'source_id' => $request->input('source_id'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // ⚠️ play_count ARTIRMA - 30 saniye sonra trackHit ile artırılacak

            return response()->json([
                'success' => true,
                'play_id' => $playId
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Failed to track start', [
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
     * 🎵 Track hit - 30 saniye dinledikten sonra play_count artır
     * POST /api/muzibu/songs/{id}/track-hit
     */
    public function trackHit(Request $request, int $songId): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json(['error' => 'unauthorized'], 401);
            }

            $song = Song::findOrFail($songId);
            $playId = $request->input('play_id');

            // play_id kontrolü - sadece kendi kaydını güncelleyebilir
            if ($playId) {
                $play = \DB::table('muzibu_song_plays')
                    ->where('id', $playId)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$play) {
                    return response()->json(['error' => 'play_not_found'], 404);
                }
            }

            // 🎯 play_count artır (hits için)
            $song->increment('play_count');

            Log::info('Muzibu: Hit tracked', [
                'song_id' => $songId,
                'user_id' => $user->id,
                'play_count' => $song->play_count
            ]);

            return response()->json([
                'success' => true,
                'play_count' => $song->play_count
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Failed to track hit', [
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
     * 📊 Track progress - DEPRECATED, use trackStart + trackHit
     * Kept for backwards compatibility
     */
    public function trackProgress(\Illuminate\Http\Request $request, int $songId): JsonResponse
    {
        // Redirect to trackStart + trackHit combined behavior
        return $this->trackStart($request, $songId);
    }

    /**
     * Track when song playback ends (for abuse detection)
     * Called when: song ends naturally, user skips, user pauses, tab closes
     *
     * POST /api/song/{id}/end
     * Body: { play_id, listened_duration, was_skipped }
     */
    public function trackEnd(Request $request, int $songId)
    {
        try {
            // 🔐 Use Sanctum's stateful auth (cookie-based for SPA)
            // EnsureFrontendRequestsAreStateful middleware handles session
            $user = auth('sanctum')->user();

            if (!$user) {
                Log::warning('Muzibu: trackEnd unauthorized', [
                    'song_id' => $songId,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'unauthorized'], 401);
            }

            $playId = $request->input('play_id');
            $listenedDuration = $request->input('listened_duration', 0);
            $wasSkipped = $request->boolean('was_skipped', false);

            if (!$playId) {
                return response()->json(['error' => 'play_id_required'], 400);
            }

            // Update the play record
            $updated = \DB::table('muzibu_song_plays')
                ->where('id', $playId)
                ->where('user_id', $user->id)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => now(),
                    'listened_duration' => max(0, (int) $listenedDuration),
                    'was_skipped' => $wasSkipped,
                    'updated_at' => now()
                ]);

            if ($updated) {
                Log::info('Muzibu: Play ended', [
                    'play_id' => $playId,
                    'song_id' => $songId,
                    'user_id' => $user->id,
                    'listened_duration' => $listenedDuration,
                    'was_skipped' => $wasSkipped
                ]);
            }

            return response()->json([
                'success' => true,
                'updated' => (bool) $updated
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Failed to track end', [
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
     * 🔴 SINGLE SOURCE OF TRUTH: users.subscription_expires_at
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

        // 🔴 SINGLE SOURCE OF TRUTH: users.subscription_expires_at
        $expiresAt = $user->subscription_expires_at;
        $hasPremium = $expiresAt && $expiresAt->isFuture();

        if (!$hasPremium) {
            return [
                'is_premium' => false,
                'trial_ends_at' => null,
                'subscription_ends_at' => null,
            ];
        }

        // Trial kontrolü: Aktif trial subscription var mı?
        $trialSubscription = $user->subscriptions()
            ->where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->first();

        $isTrial = $trialSubscription !== null;

        return [
            'is_premium' => true,
            'trial_ends_at' => $isTrial ? $trialSubscription->trial_ends_at->toIso8601String() : null,
            'subscription_ends_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * 🔑 Serve HLS Encryption Key
     *
     * This endpoint is called by HLS.js when playing encrypted HLS streams.
     * The playlist.m3u8 file contains: #EXT-X-KEY:METHOD=AES-128,URI="/api/muzibu/songs/{id}/key"
     *
     * @param int $songId
     * @return \Illuminate\Http\Response
     */
    public function serveKey(int $songId)
    {
        // ✅ Handle OPTIONS preflight request
        if (request()->method() === 'OPTIONS') {
            $origin = request()->header('Origin', 'https://muzibu.com.tr');
            return response('', 200, [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range, Cookie',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        try {
            // 🔐 İmza doğrulaması (DeviceService kapalı - token = user_id)
            $token = request()->query('token'); // Aslında user_id
            $expires = (int) request()->query('expires');
            $sig = request()->query('sig');
            $signatureBase = "/hls/muzibu/songs/{$songId}";
            $expectedSig = hash_hmac('sha256', "{$signatureBase}|{$token}|{$expires}", config('app.key'));

            if (!$token || !$expires || !$sig || $sig !== $expectedSig || Carbon::now()->timestamp > $expires) {
                return response()->json(['status' => 'session_terminated', 'message' => 'Oturum doğrulanamadı'], 401);
            }

            // 🔓 DeviceService kapalı - DB session kontrolü yapılmıyor

            // 🚀 Get song from cache
            $song = $this->cacheService->getSong($songId);

            if (!$song || !$song->is_active) {
                return response()->json(['error' => 'Song not found or inactive'], 404);
            }

            // 🔑 Build path to encryption key file
            // Format: storage/tenant{id}/app/public/muzibu/hls/{song_id}/enc.key
            $tenantId = tenant('id');
            $keyPath = storage_path("../tenant{$tenantId}/app/public/muzibu/hls/{$songId}/enc.key");

            // ⚠️ Check if key file exists
            if (!file_exists($keyPath)) {
                Log::warning('HLS key file not found', [
                    'song_id' => $songId,
                    'path' => $keyPath
                ]);
                return response()->json(['error' => 'Encryption key not found'], 404);
            }

            // 📦 Read binary key file
            $keyContent = file_get_contents($keyPath);

            if ($keyContent === false) {
                Log::error('Failed to read HLS key file', [
                    'song_id' => $songId,
                    'path' => $keyPath
                ]);
                return response()->json(['error' => 'Failed to read encryption key'], 500);
            }

            // ✅ Return binary key with proper headers (array syntax for reliability)
            $origin = request()->header('Origin', 'https://muzibu.com.tr');
            return response($keyContent, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($keyContent),
                'Cache-Control' => 'public, max-age=31536000',
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range, Cookie',
                'Access-Control-Allow-Credentials' => 'true',
            ]);

        } catch (\Exception $e) {
            Log::error('HLS key serving error', [
                'song_id' => $songId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * 🎵 Serve HLS files (playlist.m3u8 and segments) with CORS
     *
     * This endpoint serves HLS files through Laravel so CORS headers are applied.
     * Nginx serves static files directly without CORS, so we need this workaround.
     *
     * @param int $songId
     * @param string $filename (playlist.m3u8 or segment-XXX.ts)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function serveHls(int $songId, string $filename)
    {
        // ✅ Handle OPTIONS preflight
        $origin = request()->header('Origin', '*');

        if (request()->method() === 'OPTIONS') {
            return response('', 204, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        // 🔐 İmza doğrulaması (DeviceService kapalı - token = user_id)
        $token = request()->query('token'); // Aslında user_id
        $expires = (int) request()->query('expires');
        $sig = request()->query('sig');

        $signatureBase = "/hls/muzibu/songs/{$songId}";
        $expectedSig = hash_hmac('sha256', "{$signatureBase}|{$token}|{$expires}", config('app.key'));

        // İmza ve süre kontrolü (token = user_id, boş olamaz)
        if (!$token || !$expires || !$sig || $sig !== $expectedSig || Carbon::now()->timestamp > $expires) {
            Log::warning('🚨 HLS serve denied (validation failed)', [
                'song_id' => $songId,
                'file' => $filename,
                'token_provided' => !empty($token),
                'sig_match' => $sig === $expectedSig,
                'is_expired' => Carbon::now()->timestamp > $expires,
                'ip' => request()->ip(),
            ]);
            return response()->json([
                'status' => 'session_terminated',
                'reason' => 'expired_signature',
                'message' => 'Oturum doğrulanamadı'
            ], 401);
        }

        // 🔓 DeviceService kapalı - DB session kontrolü yapılmıyor
        // Token aslında user_id, imza doğrulandıysa URL manipüle edilmemiş demektir

        try {
            // Security: Only allow specific file types
            if (!preg_match('/^(playlist\.m3u8|segment-\d+\.ts)$/', $filename)) {
                return response()->json(['error' => 'Invalid file'], 400);
            }

            // storage_path() already includes tenant prefix in tenant context
            $filePath = storage_path("app/public/muzibu/hls/{$songId}/{$filename}");

            if (!file_exists($filePath)) {
                Log::warning('HLS file not found', [
                    'song_id' => $songId,
                    'file' => $filename,
                    'token' => $token,
                    'ip' => request()->ip(),
                ]);
                return response()->json(['error' => 'File not found'], 404);
            }

            // Determine content type
            $contentType = str_ends_with($filename, '.m3u8')
                ? 'application/vnd.apple.mpegurl'
                : 'video/mp2t';

            // For playlist.m3u8, rewrite key URLs to use API endpoint
            if ($filename === 'playlist.m3u8') {
                $content = file_get_contents($filePath);
                $query = http_build_query([
                    'expires' => $expires,
                    'token' => $token,
                    'sig' => $sig,
                ]);

                // Segment ve key satırlarına token + imza ekle
                $content = preg_replace('/(segment-\\d+\\.ts)/', '$1?' . $query, $content);
                $content = str_replace("/api/muzibu/songs/{$songId}/key", "/api/muzibu/songs/{$songId}/key?{$query}", $content);
                // Key URL is already correct (/api/muzibu/songs/{id}/key)

                Log::info('HLS playlist served', [
                    'song_id' => $songId,
                    'file' => $filename,
                    'user_id' => $token, // token = user_id
                    'ip' => request()->ip(),
                    'expires_in' => $expires - Carbon::now()->timestamp
                ]);

                return response($content, 200, [
                    'Content-Type' => $contentType,
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Range',
                    'Cache-Control' => 'no-cache', // Playlist should not be cached
                ]);
            }

            // For segment files, use BinaryFileResponse for efficient streaming
            $response = response()->file($filePath, [
                'Content-Type' => $contentType,
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);

            Log::info('HLS segment served', [
                'song_id' => $songId,
                'file' => $filename,
                'user_id' => $token, // token = user_id
                'ip' => request()->ip(),
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('HLS file serving error', [
                'song_id' => $songId,
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $filePath ?? 'unknown',
                'file_exists' => isset($filePath) ? file_exists($filePath) : false,
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    protected function getSessionTerminationMessage($user): string
    {
        $cookieToken = request()->cookie('mzb_login_token');
        $deletedReason = null;

        if ($cookieToken) {
            $cacheKey = "session_deleted_reason:{$user->id}:{$cookieToken}";
            $deletedReason = Cache::pull($cacheKey);
        }

        return match($deletedReason) {
            'lifo', 'lifo_new_device' => 'Başka bir cihazdan giriş yapıldı.',
            'manual_logout' => 'Oturumunuz kapatıldı.',
            'admin_terminated' => 'Oturumunuz yönetici tarafından sonlandırıldı.',
            default => 'Oturumunuz sonlandırıldı. Lütfen tekrar giriş yapın.',
        };
    }

    /**
     * 🌐 Gelişmiş Browser Tespiti
     *
     * User-Agent string'inden tarayıcıyı doğru tespit eder.
     * Chromium tabanlı tarayıcıları (Edge, Opera, Brave, Vivaldi, Samsung, Yandex)
     * Chrome'dan ayırt eder.
     *
     * @param string $userAgent
     * @return string Browser adı
     */
    protected function detectBrowserFromUA(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        // 🔴 ÖNCELİK SIRASI ÖNEMLİ!
        // Chromium tabanlı tarayıcılar Chrome'dan ÖNCE kontrol edilmeli

        // Edge (Edg/ veya Edge/)
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) {
            return 'Edge';
        }

        // Opera (OPR/ veya Opera/)
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }

        // Brave - Client Hints kullanabilir, ama bazılarında "Brave" geçer
        if (str_contains($ua, 'brave')) {
            return 'Brave';
        }

        // Vivaldi
        if (str_contains($ua, 'vivaldi')) {
            return 'Vivaldi';
        }

        // Samsung Internet
        if (str_contains($ua, 'samsungbrowser')) {
            return 'Samsung';
        }

        // Yandex Browser
        if (str_contains($ua, 'yabrowser') || str_contains($ua, 'yowser')) {
            return 'Yandex';
        }

        // UC Browser
        if (str_contains($ua, 'ucbrowser') || str_contains($ua, 'ubrowser')) {
            return 'UCBrowser';
        }

        // Firefox
        if (str_contains($ua, 'firefox') || str_contains($ua, 'fxios')) {
            return 'Firefox';
        }

        // Safari (Chrome içermemeli!)
        if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome') && !str_contains($ua, 'chromium')) {
            return 'Safari';
        }

        // Chrome (en son kontrol - diğerleri zaten return etti)
        if (str_contains($ua, 'chrome') || str_contains($ua, 'chromium') || str_contains($ua, 'crios')) {
            return 'Chrome';
        }

        // Internet Explorer
        if (str_contains($ua, 'msie') || str_contains($ua, 'trident')) {
            return 'IE';
        }

        // Bilinmeyen
        return 'Other';
    }

    /**
     * 🔒 XOR Encryption - URL'leri Console'da gizlemek için
     * Basit ama etkili obfuscation
     */
    private function xorEncrypt(string $data, string $key): string
    {
        $result = '';
        $keyLen = strlen($key);
        for ($i = 0; $i < strlen($data); $i++) {
            $result .= chr(ord($data[$i]) ^ ord($key[$i % $keyLen]));
        }
        return $result;
    }

    /**
     * 🔒 Stream URL'lerini şifrele
     * Console'a bakan kişi URL yerine anlamsız string görür
     */
    private function encryptStreamUrls(array $urls, int $songId, int $userId): array
    {
        // Dinamik key: songId + userId + saat (her saat değişir)
        $key = substr(md5($songId . $userId . date('YmdH') . config('app.key')), 0, 16);

        // URL'leri JSON'a çevir ve şifrele
        $jsonData = json_encode($urls);
        $encrypted = base64_encode($this->xorEncrypt($jsonData, $key));

        // Key'i de encode et (JS tarafında decode edilecek)
        $encodedKey = base64_encode($key);

        return [
            '_' => $encrypted,      // Şifreli data
            '__' => $encodedKey,    // Key (encoded)
            '___' => time(),        // Timestamp
        ];
    }
}
