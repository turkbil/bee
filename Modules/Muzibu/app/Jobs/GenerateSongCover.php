<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Muzibu\App\Models\Song;
use App\Services\Media\LeonardoAIService;
use Illuminate\Support\Facades\Log;

/**
 * 🎵 Generate Song Cover with Leonardo AI
 *
 * Şarkı için otomatik kapak görseli oluşturur
 * Queue: muzibu_my_playlist (180s timeout)
 */
class GenerateSongCover implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 180; // 3 dakika timeout (Leonardo AI bekleme süresi)
    public $tries = 1; // Sadece 1 kere dene
    public ?int $tenantId = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $songId,
        public string $songTitle,
        public ?string $artistName = null,
        public ?string $genreName = null,
        public ?int $userId = null,
        ?int $tenantId = null
    ) {
        // Save tenant context - explicitly passed or auto-detect
        $this->tenantId = $tenantId ?? tenant('id');

        // Explicit queue
        $this->onQueue('muzibu_my_playlist');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Tenant context'i restore et (eğer zaten initialize değilse)
        if ($this->tenantId && (!tenant() || tenant('id') != $this->tenantId)) {
            tenancy()->initialize($this->tenantId);
        }

        Log::info('🎵 GenerateSongCover: Job started', [
            'song_id' => $this->songId,
            'title' => $this->songTitle,
            'artist' => $this->artistName,
            'genre' => $this->genreName,
            'tenant_id' => $this->tenantId,
        ]);

        try {
            $song = Song::find($this->songId);

            if (!$song) {
                Log::warning('GenerateSongCover: Song not found', ['song_id' => $this->songId]);
                return;
            }

            Log::info('🎵 GenerateSongCover: Song found', [
                'song_id' => $song->song_id,
            ]);

            // 🎨 SERBEST HAYAL GÜCÜ: Sadece başlığı ver, AI kendi hayal etsin
            // Hiçbir yönlendirme, kısıtlama, şablon YOK
            // Leonardo AI başlığı alıp kendi yorumlasın
            $prompt = $this->songTitle;

            Log::info('🎵 Song Cover Job: Free imagination mode', [
                'song_id' => $this->songId,
                'prompt' => $prompt,
            ]);

            // Leonardo AI ile görsel üret (serbest hayal gücü modu)
            $leonardo = app(LeonardoAIService::class);
            $imageData = $leonardo->generateFreeImagination($prompt, [
                'width' => 1472,
                'height' => 832,
            ]);

            if (!$imageData) {
                throw new \Exception('Leonardo AI görsel üretemedi');
            }

            // ✅ Görseli doğrudan song'un "hero" collection'ına yükle
            // Artık MediaLibraryItem ve media_id kullanılmıyor
            $spatieMedia = null;
            if (!empty($imageData['url'])) {
                $spatieMedia = $song->addMediaFromUrl($imageData['url'])
                    ->usingName('Song Cover - ' . $this->songTitle)
                    ->withCustomProperties([
                        'generation_source' => 'ai_generated',
                        'generation_prompt' => $prompt,
                        'generation_params' => [
                            'model' => 'leonardo-lucid-origin',
                            'size' => '1472x832',
                            'style' => 'free_imagination',
                            'provider' => 'leonardo',
                            'generation_id' => $imageData['generation_id'] ?? null,
                            'tenant_id' => tenant('id'),
                            'purpose' => 'song_cover',
                            'song_id' => $this->songId,
                            'artist' => $this->artistName,
                            'genre' => $this->genreName,
                        ],
                    ])
                    ->toMediaCollection('hero');
            }

            // Kredi düş
            ai_use_credits(1, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'leonardo',
                'model' => 'lucid-origin',
                'prompt' => $prompt,
                'operation_type' => 'song_cover_free_imagination',
                'spatie_media_id' => $spatieMedia?->id,
                'song_id' => $this->songId,
                'quality' => 'hd',
                'credit_cost' => 1,
            ]);

            Log::info('🎵 Song Cover Job: AI Generation Successful! (hero collection)', [
                'song_id' => $this->songId,
                'spatie_media_id' => $spatieMedia?->id,
                'collection' => 'hero',
                'generation_id' => $imageData['generation_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('🎵 Song Cover Job: AI Generation Failed', [
                'song_id' => $this->songId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Job fail olsun ki retry olmasın
            $this->fail($e);
        }
    }
}
