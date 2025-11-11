<?php

namespace App\Http\Controllers\Streaming;

use App\Http\Controllers\Controller;
use App\Services\Muzibu\HLSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    public function getEncryptionKey(string $songHash)
    {
        try {
            // TODO: Token validation + rate limiting ekle
            // TODO: User authentication kontrol et

            $keyData = $this->hlsService->getEncryptionKey($songHash);

            if (!$keyData) {
                Log::warning('🔒 Encryption key bulunamadı', ['hash' => $songHash]);
                return response('Not Found', 404);
            }

            Log::info('🔑 Encryption key served', ['hash' => $songHash]);

            return response($keyData, 200, [
                'Content-Type' => 'application/octet-stream',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
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
    public function streamFile(string $songHash, string $filename)
    {
        try {
            // Dosya yolu
            $filePath = storage_path('app/public/' . HLSService::HLS_STORAGE_PATH . '/' . $songHash . '/' . $filename);

            // Dosya var mı kontrol
            if (!file_exists($filePath)) {
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
            'Cache-Control' => 'public, max-age=3600',
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
            'Cache-Control' => 'public, max-age=31536000', // 1 yıl cache (chunks değişmez)
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
