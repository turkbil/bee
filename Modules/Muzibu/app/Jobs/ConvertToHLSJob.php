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

    protected Song $song;

    /**
     * Create a new job instance.
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Muzibu HLS Conversion: Starting', [
                'song_id' => $this->song->song_id,
                'title' => $this->song->getTranslated('title', 'en')
            ]);

            // Check if file exists
            $inputPath = Storage::disk('public')->path($this->song->file_path);
            if (!file_exists($inputPath)) {
                throw new \Exception('Audio file not found: ' . $this->song->file_path);
            }

            // Create HLS output directory
            $outputDir = "muzibu/songs/hls/song-{$this->song->song_id}";
            $outputDirPath = Storage::disk('public')->path($outputDir);

            if (!is_dir($outputDirPath)) {
                mkdir($outputDirPath, 0755, true);
            }

            // HLS segment settings
            $playlistPath = $outputDirPath . '/playlist.m3u8';
            $segmentPattern = $outputDirPath . '/segment-%03d.ts';

            // FFmpeg command for HLS conversion
            // Options:
            // -map 0:a = only audio stream (skip album art/video)
            // -c copy = no re-encoding (fast)
            // -start_number 0 = start segment numbering from 0
            // -hls_time 10 = 10 second segments
            // -hls_list_size 0 = include all segments in playlist
            // -f hls = output format HLS
            $command = sprintf(
                'ffmpeg -i %s -map 0:a -c copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls %s 2>&1',
                escapeshellarg($inputPath),
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

            // Update song record
            $this->song->update([
                'hls_path' => $outputDir . '/playlist.m3u8',
                'hls_converted' => true
            ]);

            Log::info('Muzibu HLS Conversion: Success', [
                'song_id' => $this->song->song_id,
                'hls_path' => $this->song->hls_path
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu HLS Conversion: Failed', [
                'song_id' => $this->song->song_id,
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
            'song_id' => $this->song->song_id,
            'error' => $exception->getMessage()
        ]);

        // Mark as failed in database (optional)
        // You could add a 'hls_conversion_failed' field if needed
    }
}
