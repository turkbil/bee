<?php

namespace App\Services\Muzibu;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * HLS Streaming Service
 *
 * MP3 → HLS conversion + AES-128 encryption
 * FFmpeg ile chunked streaming için optimize edilmiş servis
 */
class HLSService
{
    /**
     * HLS chunk süresi (saniye)
     */
    const CHUNK_DURATION = 10; // 10 saniyelik parçalar

    /**
     * HLS playlist tipi
     */
    const PLAYLIST_TYPE = 'vod'; // Video-on-demand (static)

    /**
     * Storage klasörü
     */
    const HLS_STORAGE_PATH = 'muzibu/hls';

    /**
     * MP3 dosyasını HLS formatına çevir
     *
     * @param string $mp3Path storage/muzibu/songs/song_xxx.mp3
     * @param bool $encrypt AES-128 encryption kullan?
     * @return array ['hls_path' => 'song_hash/playlist.m3u8', 'encryption_key' => 'hex', 'success' => true/false]
     */
    public function convertToHLS(string $mp3Path, bool $encrypt = true): array
    {
        try {
            // 1. Benzersiz klasör oluştur (her şarkıya özel)
            $songHash = Str::random(16);
            $hlsFolder = self::HLS_STORAGE_PATH . '/' . $songHash;
            $storagePath = storage_path('app/public/' . $hlsFolder);

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // 2. Kaynak MP3 dosyası
            $sourcePath = storage_path('app/public/' . $mp3Path);

            if (!file_exists($sourcePath)) {
                throw new \Exception("Kaynak MP3 dosyası bulunamadı: $sourcePath");
            }

            // 3. Encryption key oluştur (isteğe bağlı)
            $encryptionKey = null;
            $keyInfoFile = null;

            if ($encrypt) {
                $encryptionKey = $this->generateEncryptionKey();
                $keyInfoFile = $this->createKeyInfoFile($storagePath, $encryptionKey, $songHash);
            }

            // 4. FFmpeg komutu oluştur
            $playlistPath = $storagePath . '/playlist.m3u8';
            $segmentPattern = $storagePath . '/chunk_%03d.ts';

            $ffmpegCommand = $this->buildFFmpegCommand(
                $sourcePath,
                $playlistPath,
                $segmentPattern,
                $encrypt ? $keyInfoFile : null
            );

            // 5. FFmpeg çalıştır
            Log::info('🎵 HLS Conversion başlatılıyor', [
                'mp3' => $mp3Path,
                'hls_folder' => $hlsFolder,
                'encrypted' => $encrypt
            ]);

            $output = [];
            $returnCode = 0;
            exec($ffmpegCommand . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("FFmpeg hatası: " . implode("\n", $output));
            }

            // 6. Playlist dosyasını kontrol et
            if (!file_exists($playlistPath)) {
                throw new \Exception("Playlist dosyası oluşturulamadı");
            }

            Log::info('✅ HLS Conversion başarılı', [
                'hls_path' => $hlsFolder . '/playlist.m3u8',
                'chunks' => count(glob($storagePath . '/chunk_*.ts')),
                'encrypted' => $encrypt
            ]);

            return [
                'success' => true,
                'hls_path' => $hlsFolder . '/playlist.m3u8',
                'encryption_key' => $encryptionKey,
                'is_encrypted' => $encrypt,
                'converted_at' => now(),
            ];

        } catch (\Exception $e) {
            Log::error('❌ HLS Conversion hatası', [
                'error' => $e->getMessage(),
                'mp3' => $mp3Path
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * FFmpeg komutunu oluştur
     *
     * @param string $inputFile Kaynak MP3
     * @param string $outputPlaylist Çıktı .m3u8
     * @param string $segmentPattern Chunk pattern (chunk_%03d.ts)
     * @param string|null $keyInfoFile Encryption keyinfo dosyası
     * @return string FFmpeg komutu
     */
    protected function buildFFmpegCommand(
        string $inputFile,
        string $outputPlaylist,
        string $segmentPattern,
        ?string $keyInfoFile = null
    ): string {
        $ffmpeg = '/usr/bin/ffmpeg';

        // ⚡ ÖZEL: Orijinal bitrate'i tespit et (kalite kaybı minimize)
        $originalBitrate = $this->detectBitrate($inputFile);
        $targetBitrate = $this->calculateTargetBitrate($originalBitrate);

        $cmd = [
            $ffmpeg,
            '-i', escapeshellarg($inputFile),
            '-c:a', 'aac', // Audio codec: AAC (HLS standart)
            '-b:a', $targetBitrate, // Orijinale yakın bitrate (minimal kalite kaybı)
            '-profile:a', 'aac_low', // AAC-LC profil (en iyi kalite/uyumluluk)
            '-ar', '48000', // Sample rate: 48kHz (orijinali koru)
            '-ac', '2', // Stereo
            '-vn', // Video stream yok
            '-hls_time', self::CHUNK_DURATION, // Chunk süresi
            '-hls_list_size', 0, // Tüm chunk'ları listele
            '-hls_playlist_type', self::PLAYLIST_TYPE, // VOD
        ];

        // Encryption aktifse
        if ($keyInfoFile) {
            $cmd[] = '-hls_key_info_file';
            $cmd[] = escapeshellarg($keyInfoFile);
        }

        $cmd[] = '-hls_segment_filename';
        $cmd[] = escapeshellarg($segmentPattern);
        $cmd[] = escapeshellarg($outputPlaylist);

        return implode(' ', $cmd);
    }

    /**
     * AES-128 encryption key oluştur (16 byte = 128 bit)
     *
     * @return string Hex encoded key (32 karakter)
     */
    protected function generateEncryptionKey(): string
    {
        return bin2hex(random_bytes(16)); // 16 byte = 128 bit
    }

    /**
     * Encryption keyinfo dosyası oluştur (FFmpeg için)
     *
     * Format:
     * Line 1: Key URI (HTTP endpoint)
     * Line 2: Key file path (local file)
     * Line 3: IV (initialization vector)
     *
     * @param string $storagePath HLS klasör yolu
     * @param string $encryptionKey Hex key
     * @param string $songHash Song hash
     * @return string Keyinfo dosya yolu
     */
    protected function createKeyInfoFile(string $storagePath, string $encryptionKey, string $songHash): string
    {
        // Key dosyasını oluştur (binary format)
        $keyFilePath = $storagePath . '/enc.key';
        file_put_contents($keyFilePath, hex2bin($encryptionKey));

        // Keyinfo dosyası (FFmpeg için)
        $keyInfoPath = $storagePath . '/enc.keyinfo';

        // Key URI (HTTP endpoint - token-based auth)
        // Tenant domain kullan (ixtif.com, müzibu.com vb.)
        $domain = tenant() && tenant()->domains->count() > 0
            ? tenant()->domains->first()->domain
            : 'ixtif.com'; // Fallback
        $keyUri = "https://{$domain}/stream/key/{$songHash}";

        // IV (initialization vector) - opsiyonel, 16 byte hex
        $iv = bin2hex(random_bytes(16));

        $keyInfo = implode("\n", [
            $keyUri,        // Line 1: Key URI
            $keyFilePath,   // Line 2: Local key file path
            $iv             // Line 3: IV
        ]);

        file_put_contents($keyInfoPath, $keyInfo);

        return $keyInfoPath;
    }

    /**
     * HLS dosyalarını sil
     *
     * @param string $hlsPath HLS playlist yolu (örn: song_hash/playlist.m3u8)
     * @return bool
     */
    public function deleteHLSFiles(string $hlsPath): bool
    {
        try {
            // Klasör adını çıkar
            $folderPath = dirname($hlsPath);
            $storagePath = storage_path('app/public/' . $folderPath);

            if (file_exists($storagePath)) {
                // Tüm dosyaları sil
                $files = glob($storagePath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                // Klasörü sil
                rmdir($storagePath);

                Log::info('🗑️ HLS dosyaları silindi', ['path' => $folderPath]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ HLS dosyaları silme hatası', [
                'error' => $e->getMessage(),
                'path' => $hlsPath
            ]);

            return false;
        }
    }

    /**
     * Encryption key'i güvenli endpoint'ten sun
     *
     * @param string $songHash Song hash
     * @return string|null Binary key
     */
    public function getEncryptionKey(string $songHash): ?string
    {
        // TODO: Token validation + rate limiting ekle
        // Şimdilik sadece key dosyasını döndür

        $keyPath = storage_path('app/public/' . self::HLS_STORAGE_PATH . '/' . $songHash . '/enc.key');

        if (file_exists($keyPath)) {
            return file_get_contents($keyPath);
        }

        return null;
    }

    /**
     * FFprobe ile orijinal ses bitrate'ini tespit et
     *
     * @param string $inputFile MP3 dosya yolu
     * @return int Bitrate (bps)
     */
    protected function detectBitrate(string $inputFile): int
    {
        $ffprobe = '/usr/bin/ffprobe';

        $cmd = [
            $ffprobe,
            '-v', 'quiet',
            '-print_format', 'json',
            '-show_format',
            escapeshellarg($inputFile)
        ];

        $output = shell_exec(implode(' ', $cmd));
        $data = json_decode($output, true);

        // Bitrate'i al (bit/s)
        $bitrate = $data['format']['bit_rate'] ?? 128000;

        return (int) $bitrate;
    }

    /**
     * Orijinal bitrate'e göre hedef AAC bitrate hesapla
     *
     * Mantık: Orijinal kaliteyi ASLA düşürme, her zaman korumaya çalış
     * AAC daha verimli codec ama yüksek kaliteli kaynaklarda orijinali kullan
     *
     * @param int $originalBitrate Orijinal bitrate (bps)
     * @return string Target bitrate (k suffix)
     */
    protected function calculateTargetBitrate(int $originalBitrate): string
    {
        $kbps = round($originalBitrate / 1000);

        // Bitrate mapping (orijinale eşit veya daha yüksek)
        if ($kbps <= 128) {
            return '128k';
        } elseif ($kbps <= 160) {
            return '160k';
        } elseif ($kbps <= 192) {
            return '192k';
        } elseif ($kbps <= 256) {
            return '256k';
        } elseif ($kbps <= 320) {
            return '320k';
        } else {
            // Yüksek kalite (lossless vb.) - orijinal bitrate'i kullan
            return $kbps . 'k';
        }
    }
}
