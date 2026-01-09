<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Muzibu\App\Models\Song;
use Symfony\Component\Process\Process;

class ConvertSongToHLS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600; // 10 minutes for large files

    protected int $songId;
    protected ?int $tenantId;

    public function __construct(int $songId, ?int $tenantId = null)
    {
        $this->songId = $songId;
        $this->tenantId = $tenantId;
    }

    public function handle(): void
    {
        // Initialize tenant if provided
        if ($this->tenantId) {
            tenancy()->initialize($this->tenantId);
        }

        $song = Song::find($this->songId);

        if (!$song) {
            Log::error("HLS Conversion: Song not found", ['song_id' => $this->songId]);
            return;
        }

        // Skip if HLS already exists
        if (!empty($song->hls_path) && Storage::disk('tenant')->exists($song->hls_path)) {
            Log::info("HLS Conversion: Already exists", ['song_id' => $this->songId]);
            return;
        }

        // Get source audio file path
        $sourceMedia = $song->getFirstMedia('audio');
        if (!$sourceMedia) {
            Log::error("HLS Conversion: No audio file found", ['song_id' => $this->songId]);
            return;
        }

        $sourcePath = $sourceMedia->getPath();
        if (!file_exists($sourcePath)) {
            Log::error("HLS Conversion: Source file not found", [
                'song_id' => $this->songId,
                'path' => $sourcePath
            ]);
            return;
        }

        // Create HLS output directory
        $hlsDir = "hls/song_{$this->songId}";
        $hlsFullPath = storage_path("tenant{$this->tenantId}/app/public/{$hlsDir}");

        if (!is_dir($hlsFullPath)) {
            mkdir($hlsFullPath, 0755, true);
        }

        // HLS output files
        $playlistFile = "{$hlsFullPath}/playlist.m3u8";
        $segmentPattern = "{$hlsFullPath}/segment_%03d.ts";

        // FFmpeg command for HLS conversion
        $command = [
            'ffmpeg',
            '-i', $sourcePath,
            '-codec:', 'copy',          // Copy without re-encoding (fast)
            '-start_number', '0',
            '-hls_time', '10',          // 10 second segments
            '-hls_list_size', '0',      // Keep all segments in playlist
            '-f', 'hls',
            $playlistFile
        ];

        try {
            $process = new Process($command);
            $process->setTimeout($this->timeout);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

            // Verify playlist file was created
            if (!file_exists($playlistFile)) {
                throw new \RuntimeException("Playlist file not created");
            }

            // Update song with HLS path
            $song->hls_path = "{$hlsDir}/playlist.m3u8";
            $song->save();

            // Fix permissions
            $this->fixPermissions($hlsFullPath);

            Log::info("HLS Conversion: Success", [
                'song_id' => $this->songId,
                'hls_path' => $song->hls_path
            ]);

        } catch (\Exception $e) {
            Log::error("HLS Conversion: Failed", [
                'song_id' => $this->songId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Fix file permissions after HLS generation
     */
    protected function fixPermissions(string $path): void
    {
        try {
            // Set directory permissions: 755
            if (is_dir($path)) {
                chmod($path, 0755);
            }

            // Set file permissions: 644
            foreach (glob("{$path}/*") as $file) {
                if (is_file($file)) {
                    chmod($file, 0644);
                }
            }

            // Change ownership to tuufi.com_:psacln
            $process = new Process(['sudo', 'chown', '-R', 'tuufi.com_:psacln', $path]);
            $process->run();

        } catch (\Exception $e) {
            Log::warning("HLS Conversion: Permission fix failed", [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
    }
}
