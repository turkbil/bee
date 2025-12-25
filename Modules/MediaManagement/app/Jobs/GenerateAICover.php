<?php

namespace Modules\MediaManagement\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Media\LeonardoAIService;
use Modules\AI\App\Services\AIPromptEnhancer;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Illuminate\Support\Facades\Log;

/**
 * 🎨 Universal AI Cover Generator
 *
 * Herhangi bir model için otomatik Leonardo AI görsel üretimi
 * Queue: muzibu_my_playlist (180s timeout)
 *
 * Kullanım:
 * generate_ai_cover($model, 'Başlık', 'type'); // Helper function
 */
class GenerateAICover implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 180; // 3 dakika timeout
    public $tries = 1; // Sadece 1 kere dene
    public ?int $tenantId = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $modelClass,
        public int $modelId,
        public string $title,
        public ?string $type = null, // 'song', 'playlist', 'genre', 'blog', 'product', etc.
        public ?int $userId = null,
        ?int $tenantId = null
    ) {
        $this->tenantId = $tenantId ?? tenant('id');
        $this->onQueue('muzibu_my_playlist');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Tenant context restore
        if ($this->tenantId && (!tenant() || tenant('id') != $this->tenantId)) {
            tenancy()->initialize($this->tenantId);
        }

        Log::info('🎨 GenerateAICover (Universal): Job started', [
            'model_class' => $this->modelClass,
            'model_id' => $this->modelId,
            'title' => $this->title,
            'type' => $this->type,
            'tenant_id' => $this->tenantId,
        ]);

        try {
            // Model'i bul
            if (!class_exists($this->modelClass)) {
                throw new \Exception("Model class not found: {$this->modelClass}");
            }

            $model = $this->modelClass::find($this->modelId);
            if (!$model) {
                Log::warning('GenerateAICover: Model not found', [
                    'model_class' => $this->modelClass,
                    'model_id' => $this->modelId
                ]);
                return;
            }

            // Basit prompt oluştur
            $simplePrompt = $this->buildPrompt($this->title, $this->type);

            // AI Prompt Enhancer ile genişlet (11 Altın Kural)
            $enhancer = app(AIPromptEnhancer::class);
            $tenantContext = [
                'sector' => 'general', // ✅ Müzik teması ZORLAMA! Site zaten Muzibu ama prompt başlığa odaklanmalı
                'site_name' => setting('site_name') ?: 'Platform',
                'locale' => app()->getLocale() ?: 'tr',
            ];

            $enhancedPrompt = $enhancer->enhancePrompt(
                $simplePrompt,
                'cinematic',
                '1472x832',
                $tenantContext
            );

            Log::info('🎨 AI Prompt Enhanced (Universal)', [
                'type' => $this->type,
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
                'name' => 'AI Cover - ' . $this->title,
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
                    'content_class' => $this->modelClass,
                    'content_id' => $this->modelId,
                ],
            ]);

            // Görseli URL'den ekle
            $spatieMedia = null;
            if (!empty($imageData['url'])) {
                $spatieMedia = $mediaItem->addMediaFromUrl($imageData['url'])
                    ->toMediaCollection('library');
            }

            // Model'e media_id ata (Spatie Media ID - foreign key constraint için)
            // CRITICAL: update() yerine DB::table kullan - Observer/validation tetiklenmez!
            if ($spatieMedia) {
                $primaryKey = $model->getKeyName();
                $tableName = $model->getTable();

                \DB::table($tableName)->where($primaryKey, $model->{$primaryKey})->update([
                    'media_id' => $spatieMedia->id,
                    'updated_at' => now(),
                ]);
            }

            // AI kredi düş
            ai_use_credits(1, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'leonardo',
                'model' => 'lucid-origin',
                'prompt' => $simplePrompt,
                'enhanced_prompt' => $enhancedPrompt,
                'operation_type' => ($this->type ?: 'generic') . '_cover_auto',
                'media_id' => $mediaItem->id,
                'content_type' => $this->type,
                'content_class' => $this->modelClass,
                'content_id' => $this->modelId,
                'quality' => 'hd',
                'credit_cost' => 1,
            ]);

            Log::info('🎨 AI Cover Generated Successfully (Universal)!', [
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'media_id' => $mediaItem->id,
                'generation_id' => $imageData['generation_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('🎨 AI Cover Generation Failed (Universal)', [
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->fail($e);
        }
    }

    /**
     * Type'a göre basit prompt oluştur
     *
     * 🎨 SADECE BAŞLIK! AI kendi hayal etsin (11 Golden Rules genişletir)
     * Site zaten müzik platformu, "music" kelimesi gereksiz sınırlama yapar!
     */
    protected function buildPrompt(string $title, ?string $type): string
    {
        // ✅ SADECE BAŞLIK! AI ne hayal ederse etsin
        return $title;
    }

    /**
     * Type'tan sector algıla (Tenant context için)
     */
    protected function detectSector(?string $type): string
    {
        return match($type) {
            'song', 'playlist', 'album', 'genre', 'artist', 'radio', 'sektor', 'sector' => 'music',
            'blog' => 'content',
            'product' => 'ecommerce',
            'portfolio' => 'creative',
            default => 'general',
        };
    }
}
