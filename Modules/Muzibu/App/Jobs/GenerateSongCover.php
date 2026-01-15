<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Services\MuzibuLeonardoAIService;
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
    public $tries = 3; // 3 kere dene (retry)
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

            // ✅ KULLANICI GÖRSELİ KONTROLÜ: Hero varsa AI üretme!
            // Kullanıcının yüklediği görsel daha değerli, AI onu ezmemeli
            if ($song->hasMedia('hero')) {
                Log::info('🎵 GenerateSongCover: SKIPPED - Hero already exists (user uploaded)', [
                    'song_id' => $this->songId,
                    'existing_media_id' => $song->getFirstMedia('hero')?->id,
                ]);
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

            // Muzibu Leonardo AI ile görsel üret (müzik platformu optimized!)
            // 🔴 GEÇİCİ (2026-01-14): 512x512 (~2 token) - kredi tasarrufu
            // Orijinal: 'width' => 1280, 'height' => 800 (8 token/görsel)
            $leonardo = app(MuzibuLeonardoAIService::class);
            $imageData = $leonardo->generateFreeImagination($prompt, [
                'width' => 512,   // 🔴 GEÇİCİ - Orijinal: 1280
                'height' => 768,  // 🔴 GEÇİCİ - Orijinal: 800
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

                // 🔐 FIX PERMISSION: Horizon root olarak çalıştığı için dosya root:root oluyor
                // Web server'ın (tuufi.com_) dosyaya erişebilmesi için ownership düzelt
                if ($spatieMedia) {
                    $filePath = $spatieMedia->getPath();
                    $dirPath = dirname($filePath);

                    // Dosya ve klasör ownership'ini düzelt
                    @exec("sudo chown -R tuufi.com_:psaserv " . escapeshellarg($dirPath));
                    @exec("sudo chmod 755 " . escapeshellarg($dirPath));
                    @exec("sudo chmod 644 " . escapeshellarg($filePath));

                    Log::info('🔐 GenerateSongCover: Fixed file permissions', [
                        'file_path' => $filePath,
                        'media_id' => $spatieMedia->id,
                    ]);
                }
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
