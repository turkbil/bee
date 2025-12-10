<?php

namespace App\Http\Controllers\Streaming;

use App\Http\Controllers\Controller;
use App\Services\Muzibu\HLSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Jobs\ProcessBulkSongHLSJob;

/**
 * Muzik Streaming Controller
 *
 * HLS streaming endpoints:
 * - /stream/key/{songHash} → Encryption key
 * - /stream/play/{songHash}/playlist.m3u8 → HLS playlist
 * - /stream/play/{songHash}/chunk_xxx.ts → HLS chunks
 */
class MuzikStreamController extends Controller
{
    protected $hlsService;

    public function __construct(HLSService $hlsService)
    {
        $this->hlsService = $hlsService;
    }

    /**
     * Encryption key endpoint
     *
     * @param string $songHash
     * @return \Illuminate\Http\Response
     */
    public function getEncryptionKey(string $songHash, Request $request)
    {
        // CORS preflight (OPTIONS request)
        if ($request->isMethod('options')) {
            return response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        try {
            // TODO: Token validation + rate limiting ekle
            // TODO: User authentication kontrol et

            // Debug: Tenant bilgisi
            $tenantId = tenant() ? tenant()->id : 'NO_TENANT';
            Log::info('🔒 Key request', ['hash' => $songHash, 'tenant' => $tenantId]);

            $keyData = $this->hlsService->getEncryptionKey($songHash);

            if (!$keyData) {
                Log::warning('🔒 Encryption key bulunamadı', ['hash' => $songHash, 'tenant' => $tenantId]);
                return response('Not Found', 404);
            }

            Log::info('🔑 Encryption key served', ['hash' => $songHash, 'size' => strlen($keyData)]);

            return response($keyData, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($keyData),
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range',
                'Access-Control-Expose-Headers' => 'Content-Length',
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Encryption key error', [
                'hash' => $songHash,
                'error' => $e->getMessage()
            ]);

            return response('Server Error', 500);
        }
    }

    /**
     * HLS playlist veya chunk serve et
     *
     * @param string $songHash
     * @param string $filename playlist.m3u8 veya chunk_001.ts
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamFile(string $songHash, string $filename, Request $request)
    {
        // CORS preflight (OPTIONS request)
        if ($request->isMethod('options')) {
            return response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Range',
                'Access-Control-Max-Age' => '86400',
            ]);
        }

        try {
            // 🎯 SUBSCRIPTION ACCESS CHECK (Phase 5)
            // Frontend'de 30 saniye kontrolü yapılıyor, burada sadece log
            if (auth()->check()) {
                $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
                $access = $subscriptionService->checkUserAccess(auth()->user());

                Log::info('🎵 Stream access check', [
                    'user_id' => auth()->id(),
                    'status' => $access['status'],
                    'is_trial' => $access['is_trial'] ?? false,
                    'song_hash' => $songHash,
                    'file' => $filename,
                ]);

                // Not: Frontend'de hls.js 30 saniye kontrolü yapıyor
                // Backend'de hard limit yok - frontend sorumlu
            } else {
                Log::info('🎵 Guest stream', ['song_hash' => $songHash, 'file' => $filename]);
            }
            // Access check sonu
            // Dosya yolu (tenant-aware)
            $tenantId = tenant() ? tenant()->id : null;

            if ($tenantId) {
                // Tenant-aware path
                $filePath = storage_path('../tenant' . $tenantId . '/app/public/' . HLSService::HLS_STORAGE_PATH . '/' . $songHash . '/' . $filename);
            } else {
                // Central/fallback path
                $filePath = storage_path('app/public/' . HLSService::HLS_STORAGE_PATH . '/' . $songHash . '/' . $filename);
            }

            Log::info('🎵 Stream file request', [
                'tenant' => $tenantId,
                'song_hash' => $songHash,
                'filename' => $filename,
                'path' => $filePath,
                'exists' => file_exists($filePath)
            ]);

            // Dosya var mı kontrol
            if (!file_exists($filePath)) {
                // LAZY CONVERSION: HLS yoksa ve playlist isteniyorsa, background job tetikle
                if ($filename === 'playlist.m3u8' && is_numeric($songHash)) {
                    $songId = (int) $songHash;
                    $song = Song::find($songId);

                    if ($song && $song->file_path && empty($song->hls_path)) {
                        // Response döndükten sonra HLS conversion başlat
                        // Bu sayede kullanıcı hemen MP3 dinleyebilir
                        ProcessBulkSongHLSJob::dispatchAfterResponse($songId);

                        Log::info('🔄 Lazy HLS conversion tetiklendi', [
                            'song_id' => $songId,
                            'file' => $song->file_path
                        ]);

                        // 404 döndür - client MP3'e fallback yapacak
                        // Bir sonraki dinlemede HLS hazır olacak
                        return response()->json([
                            'status' => 'processing',
                            'message' => 'HLS conversion in progress'
                        ], 404);
                    }
                }

                Log::warning('📂 HLS dosya bulunamadı', [
                    'hash' => $songHash,
                    'file' => $filename
                ]);
                return response('Not Found', 404);
            }

            // MIME type belirle
            $mimeType = $this->getMimeType($filename);

            // Playlist için özel işlem (.m3u8)
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'm3u8') {
                return $this->streamPlaylist($filePath);
            }

            // Chunk dosyası stream et (.ts)
            return $this->streamChunk($filePath, $mimeType);

        } catch (\Exception $e) {
            Log::error('❌ Streaming error', [
                'hash' => $songHash,
                'file' => $filename,
                'error' => $e->getMessage()
            ]);

            return response('Server Error', 500);
        }
    }

    /**
     * Playlist dosyasını stream et (.m3u8)
     *
     * @param string $filePath
     * @return \Illuminate\Http\Response
     */
    protected function streamPlaylist(string $filePath)
    {
        $content = file_get_contents($filePath);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache, must-revalidate', // Cache bypass (kalite güncellemeleri için)
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Chunk dosyasını stream et (.ts)
     *
     * @param string $filePath
     * @param string $mimeType
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function streamChunk(string $filePath, string $mimeType)
    {
        $fileSize = filesize($filePath);

        return response()->stream(function () use ($filePath) {
            $stream = fopen($filePath, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-cache, must-revalidate', // Cache bypass (kalite güncellemeleri için)
            'Accept-Ranges' => 'bytes',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * MIME type belirle
     *
     * @param string $filename
     * @return string
     */
    protected function getMimeType(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return match($extension) {
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts' => 'video/mp2t',
            'key' => 'application/octet-stream',
            default => 'application/octet-stream',
        };
    }
}
