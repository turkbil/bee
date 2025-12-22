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

            // 🔐 DEVICE LIMIT CHECK: login_token ile doğrula (kick edilen cihaz stream yapamasın)
            if ($this->deviceService->shouldRun()) {
                $cookieToken = request()->cookie('mzb_login_token');
                $sessionExists = $this->deviceService->sessionExists($user);

                \Log::info('🔐 stream device check', [
                    'user_id' => $user->id,
                    'session_id' => substr(session()->getId() ?: 'N/A', 0, 20) . '...',
                    'cookie_token' => $cookieToken ? substr($cookieToken, 0, 16) . '...' : 'NULL',
                    'session_exists' => $sessionExists,
                ]);

                if (!$sessionExists) {
                    $reason = 'session_missing';
                    // Kullanıcıyı çıkış yaptır ve mesaj döndür
                    auth('web')->logout();
                    if (request()->hasSession()) {
                        request()->session()->invalidate();
                        request()->session()->regenerateToken();
                    }

                    $sessionCookie = config('session.cookie', 'laravel_session');

                    \Log::warning('🔐 stream blocked (session missing)', [
                        'user_id' => $user->id,
                        'cookie_token' => $cookieToken ? substr($cookieToken, 0, 16) . '...' : 'NULL',
                        'reason' => $reason,
                    ]);

                    return response()->json([
                        'status' => 'session_terminated',
                        'reason' => $reason,
                        'redirect' => '/login',
                        'message' => $this->getSessionTerminationMessage($user),
                    ], 401)
                    ->withCookie(cookie()->forget($sessionCookie))
                    ->withCookie(cookie()->forget('XSRF-TOKEN'));
                }
            }

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
                        // 🧪 TEST: HLS encryption info
                        'has_encryption_key' => !empty($song->encryption_key),
                        'has_encryption_iv' => !empty($song->encryption_iv),
                        'has_hls_path' => !empty($song->hls_path),
                    ]
                ], $this->getSubscriptionData($user)));
            }

            // 🔥 Subscription bilgilerini al
            $subscriptionData = $this->getSubscriptionData($user);

            // HLS already converted - return HLS URL (SIGNED + token-bound)
            $cookieName = 'mzb_login_token';
            $loginToken = request()->cookie($cookieName);

            if ($this->deviceService->shouldRun()) {
                // Cookie yoksa DB'den token bul ve yeniden cookie yaz
                if (!$loginToken) {
                    $sessionRow = \Illuminate\Support\Facades\DB::table('user_active_sessions')
                        ->where('user_id', $user->id)
                        ->orderByDesc('last_activity')
                        ->first();

                    if ($sessionRow && $sessionRow->login_token) {
                        $loginToken = $sessionRow->login_token;
                        $lifetime = (int) setting('auth_session_lifetime', 525600);
                        cookie()->queue(cookie($cookieName, $loginToken, $lifetime, '/', null, true, true, false, 'Lax'));
                    } else {
                        return response()->json([
                            'status' => 'session_terminated',
                            'message' => 'Oturum bulunamadı. Lütfen tekrar giriş yapın.'
                        ], 401);
                    }
                }
            }

            // 🔒 TTL: Şarkı süresine göre dinamik imza (uzun parçalarda timeout yaşamamak için)
            $durationSeconds = (int) ($song->duration ?? 0);
            $bufferSeconds = 180; // 3 dakika tampon
            $ttlSeconds = max(480, min($durationSeconds + $bufferSeconds, 1800)); // min 8 dk, max 30 dk

            $hlsUrl = $this->signedUrlService->generateHlsUrl($songId, $ttlSeconds, $loginToken);

            return response()->json(array_merge([
                'status' => 'ready',
                'message' => 'HLS stream ready',
                'stream_url' => $hlsUrl, // 🔐 SIGNED HLS URL (token + expires + sig)
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
                    // 🧪 TEST: HLS encryption info (sadece var/yok - güvenlik için key değeri yok)
                    'has_encryption_key' => !empty($song->encryption_key),
                    'has_encryption_iv' => !empty($song->encryption_iv),
                    'has_hls_path' => !empty($song->hls_path),
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

            // 🔒 Duplicate kontrolü: Aynı kullanıcı + şarkı için son 30 saniyede kayıt var mı?
            $recentPlay = \DB::table('muzibu_song_plays')
                ->where('song_id', $songId)
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subSeconds(30))
                ->first();

            // Eğer son 30 saniyede zaten kayıt varsa, duplicate kayıt ekleme
            if ($recentPlay) {
                return response()->json([
                    'success' => true,
                    'duplicate_prevented' => true
                ]);
            }

            // 30+ saniye dinlendi, kayıt ekle (Analytics için)
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
            // 🔐 Token + imza doğrulaması (HLS ile aynı imza)
            $token = request()->query('token');
            $expires = (int) request()->query('expires');
            $sig = request()->query('sig');
            $signatureBase = "/hls/muzibu/songs/{$songId}";
            $expectedSig = hash_hmac('sha256', "{$signatureBase}|{$token}|{$expires}", config('app.key'));

            if (!$token || !$expires || !$sig || $sig !== $expectedSig || Carbon::now()->timestamp > $expires) {
                return response()->json(['status' => 'session_terminated', 'message' => 'Oturum doğrulanamadı'], 401);
            }

            $sessionRow = DB::table('user_active_sessions')
                ->where('login_token', $token)
                ->first();

            if (!$sessionRow) {
                return response()->json(['status' => 'session_terminated', 'message' => 'Oturum bulunamadı'], 401);
            }

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

        // 🔐 Token + imza doğrulaması
        $token = request()->query('token');
        $expires = (int) request()->query('expires');
        $sig = request()->query('sig');

        $signatureBase = "/hls/muzibu/songs/{$songId}";
        $expectedSig = hash_hmac('sha256', "{$signatureBase}|{$token}|{$expires}", config('app.key'));

        if (!$token || !$expires || !$sig || $sig !== $expectedSig || Carbon::now()->timestamp > $expires) {
            Log::warning('HLS serve denied (invalid signature)', [
                'song_id' => $songId,
                'file' => $filename,
                'token_prefix' => $token ? substr($token, 0, 12) : 'NULL',
                'expires_in' => $expires ? $expires - Carbon::now()->timestamp : null,
                'ip' => request()->ip(),
            ]);
            return response()->json([
                'status' => 'session_terminated',
                'reason' => 'expired_signature',
                'message' => 'Oturum doğrulanamadı'
            ], 401);
        }

        // Token DB'de geçerli mi? (LIFO sonrası silindiyse 401)
        $sessionRow = DB::table('user_active_sessions')
            ->where('login_token', $token)
            ->first();

        if (!$sessionRow) {
            Log::warning('HLS serve denied (token not found)', [
                'song_id' => $songId,
                'file' => $filename,
                'token_prefix' => substr($token, 0, 12),
                'ip' => request()->ip(),
            ]);
            return response()->json([
                'status' => 'session_terminated',
                'reason' => 'session_missing',
                'message' => 'Oturum bulunamadı'
            ], 401);
        }

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
                    'token_prefix' => substr($token, 0, 12),
                    'session_id' => substr($sessionRow->session_id ?? '', 0, 20),
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
                    'token_prefix' => substr($token, 0, 12),
                    'session_id' => substr($sessionRow->session_id ?? '', 0, 20),
                    'ip' => request()->ip(),
                    'user_agent' => substr(request()->userAgent() ?? '', 0, 80),
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
                'token_prefix' => substr($token, 0, 12),
                'session_id' => substr($sessionRow->session_id ?? '', 0, 20),
                'ip' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 80),
                'expires_in' => $expires - Carbon::now()->timestamp
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('HLS file serving error', [
                'song_id' => $songId,
                'filename' => $filename,
                'error' => $e->getMessage()
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
}
