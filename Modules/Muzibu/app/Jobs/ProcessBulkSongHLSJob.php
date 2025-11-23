<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Muzibu\App\Models\Song;
use App\Services\Muzibu\HLSService;
use Illuminate\Support\Facades\Log;

class ProcessBulkSongHLSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 dakika

    /**
     * The song ID to process
     *
     * @var int
     */
    protected $songId;

    /**
     * The tenant ID for multi-tenant support
     *
     * @var int|null
     */
    protected $tenantId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $songId)
    {
        $this->songId = $songId;
        $this->tenantId = tenant('id'); // Mevcut tenant'ı kaydet
        $this->onQueue('hls'); // HLS kuyruğuna gönder
    }

    /**
     * Execute the job.
     */
    public function handle(HLSService $hlsService): void
    {
        Log::info('🎵 HLS Job başlatılıyor', ['song_id' => $this->songId, 'tenant_id' => $this->tenantId]);

        try {
            // Tenant context'i initialize et
            if ($this->tenantId) {
                tenancy()->initialize($this->tenantId);
            }

            $song = Song::find($this->songId);

            if (!$song) {
                Log::error('❌ HLS Job - Song bulunamadı', ['song_id' => $this->songId]);
                return;
            }

            if (empty($song->file_path)) {
                Log::error('❌ HLS Job - Dosya yolu boş', ['song_id' => $this->songId]);
                return;
            }

            // HLS dönüşümü yap (tenant storage path kullan)
            // file_path formatı: "song_xxx.mp3" veya "muzibu/songs/song_xxx.mp3"
            $filePath = $song->file_path;
            if (!str_starts_with($filePath, 'muzibu/songs/')) {
                $filePath = 'muzibu/songs/' . $filePath;
            }

            // songId'yi 3. parametre olarak geç (lazy conversion için deterministik path)
            $hlsResult = $hlsService->convertToHLS($filePath, true, $this->songId);

            if ($hlsResult['success']) {
                // Song'u güncelle
                $song->update([
                    'hls_path' => $hlsResult['hls_path'],
                    'encryption_key' => $hlsResult['encryption_key'],
                    'is_encrypted' => $hlsResult['is_encrypted'],
                    'hls_converted_at' => $hlsResult['converted_at'],
                    'hls_converted' => true,
                ]);

                Log::info('✅ HLS Job tamamlandı', [
                    'song_id' => $this->songId,
                    'hls_path' => $hlsResult['hls_path'],
                    'encrypted' => $hlsResult['is_encrypted']
                ]);
            } else {
                Log::warning('⚠️ HLS Job başarısız', [
                    'song_id' => $this->songId,
                    'error' => $hlsResult['error'] ?? 'unknown'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ HLS Job hatası', [
                'song_id' => $this->songId,
                'error' => $e->getMessage()
            ]);

            throw $e; // Retry için exception'ı tekrar fırlat
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💀 HLS Job tamamen başarısız', [
            'song_id' => $this->songId,
            'error' => $exception->getMessage()
        ]);
    }
}
