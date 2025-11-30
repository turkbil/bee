<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Song;

class ConvertToHLSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $songId;
    protected $tenantId;

    /**
     * Create a new job instance.
     */
    public function __construct(Song $song)
    {
        $this->songId = $song->song_id;
        $this->tenantId = tenant() ? tenant()->id : null;
        $this->onQueue('hls'); // HLS conversion queue
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Initialize tenant context
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            // Get fresh song instance with correct tenant connection
            $song = Song::findOrFail($this->songId);

            Log::info('Muzibu HLS Conversion: Starting', [
                'song_id' => $song->song_id,
                'tenant_id' => $this->tenantId,
                'title' => $song->getTranslated('title', 'en')
            ]);

            // Check if file exists (file_path is already absolute path)
            $inputPath = $song->file_path;
            if (!file_exists($inputPath)) {
                throw new \Exception('Audio file not found: ' . $inputPath);
            }

            // Create HLS output directory (tenant-aware storage structure)
            // Path: storage/tenant{id}/app/public/muzibu/hls/{song_id}/
            $tenantStoragePath = storage_path('../tenant' . tenant()->id . '/app/public/muzibu/hls/' . $song->song_id);

            // Create directory with correct permissions
            if (!file_exists($tenantStoragePath)) {
                mkdir($tenantStoragePath, 0755, true);
            }

            // HLS segment settings
            $playlistPath = $tenantStoragePath . '/playlist.m3u8';
            $segmentPattern = $tenantStoragePath . '/segment-%03d.ts';

            // 🔐 AES-128 Encryption setup
            $encryptionKey = bin2hex(random_bytes(16)); // 128-bit key
            $encryptionIV = bin2hex(random_bytes(16));  // 128-bit IV

            // Save encryption key to database
            \DB::connection('tenant')->table('muzibu_songs')
                ->where('song_id', $song->song_id)
                ->update([
                    'encryption_key' => $encryptionKey,
                    'encryption_iv' => $encryptionIV,
                    'updated_at' => now()
                ]);

            // Create key file for FFmpeg
            $keyFilePath = $tenantStoragePath . '/enc.key';
            file_put_contents($keyFilePath, hex2bin($encryptionKey));

            // Create key info file for FFmpeg
            // Format: key_url\nkey_file_path\niv
            // Use tenant-specific domain for key URL
            $tenantDomain = tenant()->domains()->first()->domain;
            $keyUrl = "https://{$tenantDomain}/api/muzibu/songs/{$song->song_id}/key";
            $keyInfoPath = $tenantStoragePath . '/enc.keyinfo';
            file_put_contents($keyInfoPath, "{$keyUrl}\n{$keyFilePath}\n{$encryptionIV}");

            // 🎵 ULTIMATE EDITION: Get original bitrate (preserve quality)
            // If bitrate not set, default to 256kbps
            $bitrate = $song->bitrate ?: 256;

            // 🔊 ULTIMATE EDITION Audio Filters:
            // 1. Loudnorm: Loudness normalization (LUFS-based)
            // 2. Stereotools: Stereo widening
            // 3. Equalizer: Bass boost (+1dB @ 100Hz) + Treble cut (-2dB @ 8kHz)
            // 4. Lowpass: 14kHz filter (remove ultra-high frequencies)
            $audioFilters = implode(',', [
                'loudnorm=I=-16:TP=-1.5:LRA=11',           // Loudness normalization
                'stereotools=mlev=1.2',                    // Stereo widening
                'equalizer=f=100:t=q:w=1:g=1',             // Bass boost +1dB
                'equalizer=f=8000:t=q:w=1:g=-2',           // Treble cut -2dB
                'lowpass=f=14000'                           // Low-pass 14kHz
            ]);

            // FFmpeg command for HLS conversion with AES-128 encryption + Ultimate Edition filters
            // Options:
            // -map 0:a = only audio stream (skip album art/video)
            // -c:a aac = AAC encoding (required for filters)
            // -b:a {bitrate}k = preserve original bitrate
            // -af = audio filters (Ultimate Edition)
            // -start_number 0 = start segment numbering from 0
            // -hls_time 10 = 10 second segments
            // -hls_list_size 0 = include all segments in playlist
            // -hls_key_info_file = encryption key info
            // -f hls = output format HLS
            $command = sprintf(
                'ffmpeg -i %s -map 0:a -c:a aac -b:a %dk -af %s -start_number 0 -hls_time 10 -hls_list_size 0 -hls_key_info_file %s -f hls %s 2>&1',
                escapeshellarg($inputPath),
                $bitrate,
                escapeshellarg($audioFilters),
                escapeshellarg($keyInfoPath),
                escapeshellarg($playlistPath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $output));
            }

            // Verify playlist file was created
            if (!file_exists($playlistPath)) {
                throw new \Exception('HLS playlist file was not created');
            }

            // Update song record - use direct DB query to ensure correct tenant database
            $relativePath = 'muzibu/hls/' . $song->song_id . '/playlist.m3u8';
            \DB::connection('tenant')->table('muzibu_songs')
                ->where('song_id', $song->song_id)
                ->update([
                    'hls_path' => $relativePath,
                    'hls_converted' => 1,
                    'is_encrypted' => 1,
                    'updated_at' => now()
                ]);

            // 🚀 CACHE: Invalidate song cache after HLS conversion
            try {
                $cacheService = app(\Modules\Muzibu\App\Services\MuzibuCacheService::class);
                $cacheService->invalidateSong($song->song_id);
                Log::info('Muzibu HLS Conversion: Cache invalidated', ['song_id' => $song->song_id]);
            } catch (\Exception $cacheException) {
                // Log but don't fail the job if cache invalidation fails
                Log::warning('Muzibu HLS Conversion: Cache invalidation failed', [
                    'song_id' => $song->song_id,
                    'error' => $cacheException->getMessage()
                ]);
            }

            Log::info('Muzibu HLS Conversion: Success', [
                'song_id' => $song->song_id,
                'hls_path' => $relativePath,
                'tenant_id' => $this->tenantId
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu HLS Conversion: Failed', [
                'song_id' => $this->songId,
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Muzibu HLS Conversion: Job Failed Permanently', [
            'song_id' => $this->songId,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);

        // Mark as failed in database (optional)
        // You could add a 'hls_conversion_failed' field if needed
    }
}
