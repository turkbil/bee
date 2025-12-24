<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Muzibu\App\Models\Song;
use App\Services\Media\LeonardoAIService;
use Modules\AI\App\Services\AIPromptEnhancer;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
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

            // 🎨 Prompt oluştur (SADECE BAŞLIK! AI kendi hayal etsin)
            // Site zaten müzik platformu, "music" kelimesi gereksiz sınırlama yapar!
            $simplePrompt = $this->songTitle;

            // Artist varsa ekle
            if ($this->artistName) {
                $simplePrompt .= " by {$this->artistName}";
            }

            // AI Prompt Enhancer ile 11 Altın Kural uygula
            $enhancer = app(AIPromptEnhancer::class);

            // Tenant context (generic - prompt zaten özelleştirildi)
            $tenantContext = [
                'sector' => 'general', // ✅ Müzik teması ZORLAMA! Başlık ne diyorsa onu üret
                'site_name' => 'Muzibu',
                'locale' => 'tr',
            ];

            $enhancedPrompt = $enhancer->enhancePrompt(
                $simplePrompt,
                'cinematic', // Style
                '1472x832',  // Size (yatay)
                $tenantContext
            );

            Log::info('🎵 Song Cover Job: AI Prompt Enhanced', [
                'song_id' => $this->songId,
                'original' => $simplePrompt,
                'enhanced_length' => strlen($enhancedPrompt),
            ]);

            // Leonardo AI ile görsel üret
            $leonardo = app(LeonardoAIService::class);
            $imageData = $leonardo->generateFromPrompt($enhancedPrompt, [
                'width' => 1472,
                'height' => 832,
                'style' => 'cinematic',
            ]);

            if (!$imageData) {
                throw new \Exception('Leonardo AI görsel üretemedi');
            }

            // MediaLibraryItem oluştur
            $mediaItem = MediaLibraryItem::create([
                'name' => 'Song Cover - ' . $this->songTitle,
                'type' => 'image',
                'created_by' => $this->userId,
                'generation_source' => 'ai_generated',
                'generation_prompt' => $enhancedPrompt,
                'generation_params' => [
                    'model' => 'leonardo-lucid-origin',
                    'size' => '1472x832',
                    'style' => 'cinematic',
                    'provider' => 'leonardo',
                    'generation_id' => $imageData['generation_id'] ?? null,
                    'tenant_id' => tenant('id'),
                    'purpose' => 'song_cover',
                    'song_id' => $this->songId,
                    'artist' => $this->artistName,
                    'genre' => $this->genreName,
                ],
            ]);

            // Görseli URL'den ekle
            $spatieMedia = null;
            if (!empty($imageData['url'])) {
                $spatieMedia = $mediaItem->addMediaFromUrl($imageData['url'])
                    ->toMediaCollection('library');
            }

            // Song'a media ID'yi ata (Spatie Media ID - foreign key constraint için)
            if ($spatieMedia) {
                $song->update([
                    'media_id' => $spatieMedia->id, // ✅ Spatie media.id (NOT media_library_items.id)
                ]);
            }

            // Kredi düş
            ai_use_credits(1, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'leonardo',
                'model' => 'lucid-origin',
                'prompt' => $simplePrompt,
                'enhanced_prompt' => $enhancedPrompt,
                'operation_type' => 'song_cover_auto',
                'media_id' => $mediaItem->id,
                'song_id' => $this->songId,
                'quality' => 'hd',
                'credit_cost' => 1,
            ]);

            Log::info('🎵 Song Cover Job: AI Generation Successful!', [
                'song_id' => $this->songId,
                'media_id' => $mediaItem->id,
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
