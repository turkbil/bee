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

            // Create HLS output directory
            $outputDir = "muzibu/songs/hls/song-{$song->song_id}";

            // Use Storage facade to create directory (handles permissions correctly)
            if (!Storage::disk('public')->exists($outputDir)) {
                Storage::disk('public')->makeDirectory($outputDir, 0755, true);
            }

            $outputDirPath = Storage::disk('public')->path($outputDir);

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

            // Update song record - use direct DB query to ensure correct tenant database
            \DB::connection('tenant')->table('muzibu_songs')
                ->where('song_id', $song->song_id)
                ->update([
                    'hls_path' => $outputDir . '/playlist.m3u8',
                    'hls_converted' => 1,
                    'updated_at' => now()
                ]);

            Log::info('Muzibu HLS Conversion: Success', [
                'song_id' => $song->song_id,
                'hls_path' => $outputDir . '/playlist.m3u8',
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
