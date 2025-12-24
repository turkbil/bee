<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Media\LeonardoAIService;
use Modules\AI\App\Services\AIPromptEnhancer;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Illuminate\Support\Facades\Log;

/**
 * 🎨 Generate Generic Muzibu Cover with Leonardo AI
 *
 * Genre/Sektor/Album/Radio/Playlist için universal görsel üretimi
 * Queue: muzibu_my_playlist (180s timeout)
 */
class GenerateGenericMuzibyCover implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 180; // 3 dakika timeout (Leonardo AI bekleme süresi)
    public $tries = 1; // Sadece 1 kere dene
    public ?int $tenantId = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $type, // 'genre', 'sektor', 'album', 'radio', 'playlist'
        public int $modelId,
        public string $title,
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

        Log::info('🎨 GenerateGenericMuzibyCover: Job started', [
            'type' => $this->type,
            'model_id' => $this->modelId,
            'title' => $this->title,
            'tenant_id' => $this->tenantId,
        ]);

        try {
            // Model class'ını belirle
            $modelClass = $this->getModelClass($this->type);
            if (!$modelClass) {
                throw new \Exception("Invalid type: {$this->type}");
            }

            // Model'i bul
            $model = $modelClass::find($this->modelId);
            if (!$model) {
                Log::warning('GenerateGenericMuzibyCover: Model not found', [
                    'type' => $this->type,
                    'model_id' => $this->modelId
                ]);
                return;
            }

            // Prompt oluştur (Type'a göre özelleştirilmiş)
            $simplePrompt = $this->buildPrompt($this->type, $this->title);

            // AI Prompt Enhancer ile 11 Altın Kural uygula
            $enhancer = app(AIPromptEnhancer::class);

            // Tenant context
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

            Log::info('🎨 Generic Cover Job: AI Prompt Enhanced', [
                'type' => $this->type,
                'model_id' => $this->modelId,
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
                'name' => ucfirst($this->type) . ' Cover - ' . $this->title,
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
                    'purpose' => $this->type . '_cover',
                    'content_type' => $this->type,
                    'content_id' => $this->modelId,
                ],
            ]);

            // Görseli URL'den ekle
            $spatieMedia = null;
            if (!empty($imageData['url'])) {
                $spatieMedia = $mediaItem->addMediaFromUrl($imageData['url'])
                    ->toMediaCollection('library');
            }

            // Model'e media ID'yi ata (Spatie Media ID - foreign key constraint için)
            if ($spatieMedia) {
                $model->update([
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
                'operation_type' => $this->type . '_cover_auto',
                'media_id' => $mediaItem->id,
                'content_type' => $this->type,
                'content_id' => $this->modelId,
                'quality' => 'hd',
                'credit_cost' => 1,
            ]);

            Log::info('🎨 Generic Cover Job: AI Generation Successful!', [
                'type' => $this->type,
                'model_id' => $this->modelId,
                'media_id' => $mediaItem->id,
                'generation_id' => $imageData['generation_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('🎨 Generic Cover Job: AI Generation Failed', [
                'type' => $this->type,
                'model_id' => $this->modelId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Job fail olsun ki retry olmasın
            $this->fail($e);
        }
    }

    /**
     * Type'a göre model class döndür
     */
    protected function getModelClass(string $type): ?string
    {
        return match($type) {
            'genre' => \Modules\Muzibu\App\Models\Genre::class,
            'sektor' => \Modules\Muzibu\App\Models\Sector::class,
            'album' => \Modules\Muzibu\App\Models\Album::class,
            'radio' => \Modules\Muzibu\App\Models\Radio::class,
            'playlist' => \Modules\Muzibu\App\Models\Playlist::class,
            default => null,
        };
    }

    /**
     * Type'a göre basit prompt oluştur
     *
     * 🎨 SADECE BAŞLIK! AI kendi hayal etsin (11 Golden Rules genişletir)
     * Site zaten müzik platformu, "music" kelimesi gereksiz sınırlama yapar!
     */
    protected function buildPrompt(string $type, string $title): string
    {
        // ✅ SADECE BAŞLIK! AI ne hayal ederse etsin
        return $title;
    }
}
