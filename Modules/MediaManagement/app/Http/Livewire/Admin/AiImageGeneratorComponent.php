<?php

namespace Modules\MediaManagement\App\Http\Livewire\Admin;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Services\AIImageGenerationService;
use Modules\AI\App\Services\AIPromptEnhancer;

#[Layout('admin.layout')]
class AiImageGeneratorComponent extends Component
{
    public string $prompt = '';
    public string $size = '1792x1024';
    public string $quality = 'hd';
    public string $style = 'ultra_photorealistic';
    public bool $enhanceWithAI = true; // OpenAI GPT-4 yaratıcı enhancement
    public ?string $generatedImageUrl = null;
    public ?int $generatedMediaId = null;
    public ?string $revisedPrompt = null; // DALL-E GPT-4'ün geliştirdiği prompt
    public bool $isGenerating = false;
    public ?string $errorMessage = null;
    public int $availableCredits = 0;
    public $history = [];

    protected $rules = [
        'prompt' => 'required|string|min:10|max:1000',
        'size' => 'required|in:1024x1024,1024x1792,1792x1024',
        'quality' => 'required|in:standard,hd',
        'style' => 'required|in:ultra_photorealistic,studio_photography,natural_light,cinematic_photography,documentary_style,commercial_photography,portrait_photography,macro_photography,digital_art,illustration,3d_render,minimalist',
    ];

    public function mount()
    {
        $this->loadCredits();
        $this->loadHistory();
    }

    public function loadCredits()
    {
        $this->availableCredits = ai_get_credit_balance();
    }

    public function loadHistory()
    {
        $service = app(AIImageGenerationService::class);
        $this->history = $service->getHistory(10);
    }

    public function generate()
    {
        $this->validate();

        $this->isGenerating = true;
        $this->errorMessage = null;
        $this->generatedImageUrl = null;
        $this->revisedPrompt = null;

        try {
            $service = app(AIImageGenerationService::class);

            // İKİ MOD:
            // 1. Yaratıcı Mod (enhanceWithAI = true): OpenAI GPT-4 ile ultra detaylı, yaratıcı promptlar
            // 2. Basit Mod (enhanceWithAI = false): AA.pdf kurallarına göre basit builder
            if ($this->enhanceWithAI) {
                // YARATICI MOD: OpenAI GPT-4 ile ultra detaylı enhancement
                $enhancer = app(AIPromptEnhancer::class);
                $finalPrompt = $enhancer->enhancePrompt($this->prompt, $this->style, $this->size);
            } else {
                // BASİT MOD: AA.pdf kurallarına göre basit builder
                $finalPrompt = $this->buildPromptFromAAPDF($this->prompt, $this->style);
            }

            $imageData = $service->generateWithRevision($finalPrompt, [
                'size' => $this->size,
                'quality' => $this->quality,
            ]);

            // Get generated image URL and revised prompt
            $mediaItem = $imageData['mediaItem'];
            $media = $mediaItem->getFirstMedia('library');
            $this->generatedImageUrl = $media ? $media->getUrl() : null;
            $this->generatedMediaId = $mediaItem->id;
            $this->revisedPrompt = $imageData['revised_prompt'] ?? null; // DALL-E GPT-4'ün geliştirdiği

            // Reload credits and history
            $this->loadCredits();
            $this->loadHistory();

            session()->flash('success', 'Görsel başarıyla oluşturuldu!');

        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isGenerating = false;
        }
    }

    public function resetForm()
    {
        $this->prompt = '';
        $this->generatedImageUrl = null;
        $this->generatedMediaId = null;
        $this->revisedPrompt = null;
        $this->errorMessage = null;
    }

    public function downloadImage()
    {
        if (!$this->generatedImageUrl) {
            return;
        }

        return redirect($this->generatedImageUrl);
    }

    /**
     * AA.pdf kurallarına göre basit prompt builder
     * DALL-E 3 zaten GPT-4 ile otomatik enhance ediyor, biz sadece doğru yapıyı kuruyoruz
     */
    protected function buildPromptFromAAPDF(string $userPrompt, string $style): string
    {
        // AA.pdf formülü: Photo of + Subject + View/Framing + Background + Lighting + Camera + Lens + Natural Texture

        // ÇEKİM AÇISI (KRİTİK!) - DALL-E 3 eğitiminde fotoğraf ALT metinlerinden öğrenmiş
        $viewAngles = [
            'side view',
            'front view',
            '3/4 angle view',
            'wide shot showing full subject',
            'medium shot',
            'full equipment view',
            'complete view from all sides',
            'environmental shot',
        ];

        // Kamera ve lens çeşitleri (AA.pdf'den)
        $cameras = [
            'Canon EOS R5 with 85mm f/1.4 lens',
            'Sony A7 III with 50mm f/1.8 lens',
            'Nikon D810 with 24-70mm f/2.8 lens',
            'Fujifilm GFX 100 with 63mm f/2.8 lens',
            'Leica M10 with 50mm f/1.4 lens',
        ];

        // Işıklandırma teknikleri (AA.pdf'den)
        $lightings = [
            'golden hour natural lighting',
            'soft window light',
            'professional studio lighting with softbox',
            'Rembrandt lighting setup',
            'natural ambient lighting',
        ];

        // MEKÂN/BACKGROUND
        $backgrounds = [
            'clean white background',
            'industrial warehouse background',
            'modern studio environment',
            'neutral gray background',
            'outdoor natural setting',
            'professional showroom background',
        ];

        // Style'a göre ek özellikler
        $styleAdditions = [
            'ultra_photorealistic' => 'professional photography, 8K resolution, RAW photo quality, hyper-realistic textures, natural imperfections, visible pores and weathered surfaces',
            'studio_photography' => 'controlled studio environment, clean background, commercial photography quality, professional color grading',
            'natural_light' => 'outdoor scene, authentic atmosphere, shallow depth of field, bokeh background',
            'cinematic_photography' => 'cinematic color grading, movie still quality, dramatic composition, film grain texture',
            'documentary_style' => 'authentic moment, candid shot, photojournalism style, editorial photography',
            'commercial_photography' => 'advertising quality, perfect composition, luxury brand standards, premium retouching',
            'portrait_photography' => 'environmental portrait, subject focus, natural expression, perfect skin tones',
            'macro_photography' => 'extreme close-up, highly detailed textures, ultra sharp details, focus stacking',
            'digital_art' => 'modern digital art, contemporary design, professional illustration, vibrant colors',
            'illustration' => 'editorial illustration style, clean design, professional graphic design',
            '3d_render' => 'Unreal Engine 5, ray tracing, PBR materials, architectural visualization quality',
            'minimalist' => 'minimalist composition, negative space, clean design, simple elegant aesthetic',
        ];

        // Random seçimler (çeşitlilik için)
        $viewAngle = $viewAngles[array_rand($viewAngles)];
        $camera = $cameras[array_rand($cameras)];
        $lighting = $lightings[array_rand($lightings)];
        $background = $backgrounds[array_rand($backgrounds)];
        $addition = $styleAdditions[$style] ?? $styleAdditions['ultra_photorealistic'];

        // AA.pdf CRITICAL RULE: "photo of" kullan, "photorealistic" kelimesini ASLA kullanma!
        $photoPrefix = 'Photo of';

        // DOĞAL DOKU (gerçekçilik için kritik)
        $naturalTexture = 'natural imperfections, visible surface texture, authentic material finish, realistic wear patterns';

        // 🚨 ABSOLUTE TEXT BAN (AA.pdf kuralı)
        $textBan = 'ABSOLUTELY NO text, NO labels, NO captions, NO annotations, NO blue boxes, NO text overlays, NO UI elements, NO numbered labels, NO arrows with text, NO infographics, NO presentation elements, NO diagrams, NO charts, NO brand names, NO trademarks, NO written words of any kind. Pure photograph only, clean product catalog style without any text elements whatsoever';

        // 🔥 Final prompt assembly (AA.pdf %100 doğru formül)
        // Photo of → Subject → View Angle → Background → Lighting → Camera → Lens → Natural Texture + Text Ban
        return "{$photoPrefix} {$userPrompt}, {$viewAngle}, {$background}, {$addition}, {$lighting}, shot on {$camera}, {$naturalTexture}. {$textBan}";
    }

    public function render()
    {
        return view('mediamanagement::admin.livewire.ai-image-generator-component');
    }
}
