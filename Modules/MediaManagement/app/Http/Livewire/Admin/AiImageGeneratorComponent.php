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
    public bool $enhanceWithAI = true;
    public string $companyName = '';
    public bool $includeCompanyName = false;
    public ?string $generatedImageUrl = null;
    public ?int $generatedMediaId = null;
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

        // Header'daki gibi site title'ı al
        $this->companyName = settings('site_title', config('app.name'));
    }

    public function loadCredits()
    {
        $this->availableCredits = ai_get_token_balance();
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

        try {
            $service = app(AIImageGenerationService::class);

            // Site adını checkbox işaretliyse ekle
            $basePrompt = $this->prompt;
            if ($this->includeCompanyName && !empty($this->companyName)) {
                $basePrompt = $this->companyName . ' - ' . $this->prompt;
            }

            // AI ile prompt geliştirme (eğer checkbox işaretliyse)
            if ($this->enhanceWithAI) {
                $enhancer = app(AIPromptEnhancer::class);
                $finalPrompt = $enhancer->enhancePrompt($basePrompt, $this->style, $this->size);
            } else {
                // AI kapalıysa, manuel style enhancement kullan
                $finalPrompt = $this->enhancePromptWithStyle($basePrompt, $this->style);
            }

            $mediaItem = $service->generate($finalPrompt, [
                'size' => $this->size,
                'quality' => $this->quality,
            ]);

            // Get generated image URL
            $media = $mediaItem->getFirstMedia('library');
            $this->generatedImageUrl = $media ? $media->getUrl() : null;
            $this->generatedMediaId = $mediaItem->id;

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
        $this->errorMessage = null;
    }

    public function downloadImage()
    {
        if (!$this->generatedImageUrl) {
            return;
        }

        return redirect($this->generatedImageUrl);
    }

    protected function enhancePromptWithStyle(string $prompt, string $style): string
    {
        $styleEnhancements = [
            // GERÇEKÇI FOTOĞRAF STİLLERİ (En Yüksek Gerçekçilik)
            'ultra_photorealistic' => 'Ultra photorealistic, professional DSLR photography, extremely detailed, shot on Canon EOS R5 with 85mm f/1.4 lens, natural lighting, real-world quality, indistinguishable from actual photograph, perfect focus, 8K resolution, RAW photo quality, hyper-realistic textures, real materials, authentic scene, photojournalism quality',

            'studio_photography' => 'Professional studio photography, controlled lighting setup, softbox lighting, clean background, commercial photography quality, product photography standards, shot on medium format camera, Phase One IQ4, professional color grading, studio quality, advertising photography',

            'natural_light' => 'Natural light photography, golden hour lighting, outdoor scene, authentic atmosphere, environmental portrait style, natural colors, shot on Fujifilm GFX 100S, shallow depth of field, bokeh background, real-world setting, professional travel photography quality',

            'cinematic_photography' => 'Cinematic photography, film camera aesthetic, anamorphic lens, cinematic color grading, movie still quality, dramatic lighting, shot on ARRI Alexa, film grain texture, widescreen composition, Hollywood production quality, professional cinematography',

            'documentary_style' => 'Documentary photography, photojournalism style, authentic moment, real-world scene, reportage photography, candid shot, National Geographic quality, Leica M11 camera, street photography aesthetic, authentic storytelling, editorial photography',

            'commercial_photography' => 'High-end commercial photography, advertising campaign quality, professional retouching, perfect composition, luxury brand standards, shot on Hasselblad H6D-400c, commercial studio lighting, marketing photography, premium quality',

            'portrait_photography' => 'Professional portrait photography, environmental portrait, subject focus, professional headshot quality, natural expression, shot on Sony A1 with 85mm f/1.2 lens, perfect skin tones, professional portrait lighting, studio portrait standards',

            'macro_photography' => 'Macro photography, extreme close-up, highly detailed textures, shot with macro lens 100mm f/2.8, perfect focus stacking, ultra sharp details, professional macro photography, scientific photography quality, crystal clear details',

            // ARTİSTİK & DİJİTAL STİLLER
            'digital_art' => 'Modern digital art, contemporary design, clean artistic style, professional digital illustration, high-quality digital painting, trending on ArtStation, award-winning design, vibrant colors, professional graphic design',

            'illustration' => 'Professional illustration, editorial illustration style, clean design, commercial illustration quality, magazine illustration, professional graphic design, vector art quality, contemporary illustration',

            '3d_render' => 'Ultra realistic 3D render, Unreal Engine 5, ray tracing enabled, physically based rendering (PBR), professional 3D visualization, architectural visualization quality, product rendering, photorealistic materials and textures, studio lighting setup',

            'minimalist' => 'Minimalist photography, clean composition, negative space, simple elegant design, minimal elements, professional minimalist aesthetic, high-end minimalism, contemporary minimalist style'
        ];

        $enhancement = $styleEnhancements[$style] ?? $styleEnhancements['ultra_photorealistic'];

        return $prompt . '. ' . $enhancement . '. No text or minimal text in image. If text is required, use Turkish language only. Generic names, no brand names or trademarks.';
    }

    public function render()
    {
        return view('mediamanagement::admin.livewire.ai-image-generator-component');
    }
}
