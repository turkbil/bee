<?php

namespace Modules\MediaManagement\App\Http\Livewire\Admin;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Media\LeonardoAIService;
use Modules\AI\App\Services\AIPromptEnhancer;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class AiImageGeneratorComponent extends Component
{
    public string $prompt = '';
    public string $size = '1472x832'; // Leonardo destekli boyut (yatay)
    public string $quality = 'hd';
    public string $style = 'cinematic'; // Leonardo style
    public bool $enhanceWithAI = true; // OpenAI GPT-4 yaratıcı enhancement
    public bool $useSiteSettings = true; // Tenant ayarlarını prompt'a ekle
    public ?string $generatedImageUrl = null;
    public ?int $generatedMediaId = null;
    public ?string $revisedPrompt = null; // OpenAI'ın geliştirdiği prompt
    public bool $isGenerating = false;
    public ?string $errorMessage = null;
    public float $availableCredits = 0;
    public $history = [];

    protected $rules = [
        'prompt' => 'required|string|min:10|max:4000',
        'size' => 'required|in:1024x1024,832x1472,1472x832',
        'quality' => 'required|in:standard,hd',
        'style' => 'required|in:cinematic,cinematic_closeup,dynamic,film,hdr,moody,stock_photo,vibrant,neutral',
    ];

    public function mount()
    {
        $this->loadCredits();
        $this->loadHistory();
    }

    public function loadCredits()
    {
        // Tenant context'i al - önce tenant() helper, sonra auth user'dan
        $tenantId = tenant('id');
        if (!$tenantId && auth()->check()) {
            $tenantId = auth()->user()->tenant_id;
        }

        $this->availableCredits = ai_get_credit_balance($tenantId ? (string) $tenantId : null);
    }

    public function loadHistory()
    {
        $this->history = MediaLibraryItem::where('generation_source', 'ai_generated')
            ->where('created_by', auth()->id())
            ->latest()
            ->limit(10)
            ->get();
    }

    public function generate()
    {
        $this->validate();

        $this->isGenerating = true;
        $this->errorMessage = null;
        $this->generatedImageUrl = null;
        $this->revisedPrompt = null;

        try {
            // Credit kontrolü
            $creditCost = $this->quality === 'standard' ? 0.5 : 1;
            if (!ai_can_use_credits($creditCost)) {
                throw new Exception('Yetersiz kredi. Gerekli: ' . $creditCost);
            }

            // ADIM 1: OpenAI GPT-4 ile prompt geliştirme
            if ($this->enhanceWithAI) {
                $enhancer = app(AIPromptEnhancer::class);

                // Tenant context'i al (useSiteSettings açıksa)
                $tenantContext = $this->useSiteSettings ? $this->getTenantContext() : null;

                $finalPrompt = $enhancer->enhancePrompt($this->prompt, $this->style, $this->size, $tenantContext);
                $this->revisedPrompt = $finalPrompt; // Kullanıcıya göster
            } else {
                $finalPrompt = $this->buildBasicPrompt($this->prompt, $this->style);
            }

            Log::info('🎨 AI Generator: Prompt ready', [
                'original' => $this->prompt,
                'enhanced' => substr($finalPrompt, 0, 200) . '...',
                'style' => $this->style,
            ]);

            // ADIM 2: Leonardo AI ile görsel üretme
            $leonardo = app(LeonardoAIService::class);

            // Boyut parse
            [$width, $height] = explode('x', $this->size);

            $imageData = $leonardo->generateFromPrompt($finalPrompt, [
                'width' => (int) $width,
                'height' => (int) $height,
                'style' => $this->style,
            ]);

            if (!$imageData) {
                throw new Exception('Leonardo AI görsel üretemedi. Lütfen tekrar deneyin.');
            }

            // ADIM 3: MediaLibraryItem oluştur
            $mediaItem = $this->createMediaItem($imageData, $finalPrompt);

            $this->generatedImageUrl = $imageData['url'];
            $this->generatedMediaId = $mediaItem->id;

            // ADIM 4: Kredi düş
            ai_use_credits($creditCost, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'leonardo',
                'model' => 'lucid-origin',
                'prompt' => $this->prompt,
                'enhanced_prompt' => $finalPrompt,
                'operation_type' => 'manual',
                'media_id' => $mediaItem->id,
                'quality' => $this->quality,
                'credit_cost' => $creditCost,
            ]);

            // Reload credits and history
            $this->loadCredits();
            $this->loadHistory();

            session()->flash('success', 'Görsel başarıyla oluşturuldu! (Leonardo AI)');

        } catch (Exception $e) {
            Log::error('AI Generator Error', ['error' => $e->getMessage()]);
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * MediaLibraryItem oluştur
     */
    protected function createMediaItem(array $imageData, string $prompt): MediaLibraryItem
    {
        $tenantId = tenant('id');

        $mediaItem = MediaLibraryItem::create([
            'name' => 'AI Generated - ' . substr($this->prompt, 0, 50),
            'type' => 'image',
            'created_by' => auth()->id(),
            'generation_source' => 'ai_generated',
            'generation_prompt' => $prompt,
            'generation_params' => [
                'model' => 'leonardo-lucid-origin',
                'size' => $this->size,
                'quality' => $this->quality,
                'style' => $this->style,
                'provider' => 'leonardo',
                'generation_id' => $imageData['generation_id'] ?? null,
                'tenant_id' => $tenantId,
            ],
        ]);

        // Görseli URL'den ekle
        if (!empty($imageData['url'])) {
            $mediaItem->addMediaFromUrl($imageData['url'])
                ->toMediaCollection('library');
        }

        return $mediaItem;
    }

    /**
     * Basit prompt builder (OpenAI enhancement olmadan)
     */
    protected function buildBasicPrompt(string $userPrompt, string $style): string
    {
        $styleDescriptions = [
            'cinematic' => 'cinematic photography, dramatic lighting, movie still quality',
            'cinematic_closeup' => 'cinematic close-up shot, shallow depth of field, dramatic',
            'dynamic' => 'dynamic action shot, motion blur, energetic composition',
            'film' => 'film photography aesthetic, natural grain, analog look',
            'hdr' => 'HDR photography, high dynamic range, vivid details',
            'moody' => 'moody atmosphere, dramatic shadows, emotional lighting',
            'stock_photo' => 'professional stock photography, clean composition, commercial quality',
            'vibrant' => 'vibrant colors, saturated, eye-catching',
            'neutral' => 'neutral tones, balanced exposure, professional',
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['cinematic'];

        return "Professional photograph of {$userPrompt}. {$styleDesc}. High resolution, sharp details, no text or labels.";
    }

    /**
     * Tenant context'ini al (Site ayarları)
     */
    protected function getTenantContext(): array
    {
        $context = [];

        try {
            // Site adı
            $siteName = setting('site_name');
            if ($siteName) {
                $context['site_name'] = $siteName;
            }

            // Site sektörü (AI için özel ayar)
            $sector = setting('ai_image_sector') ?? setting('site_sector');
            if ($sector) {
                $context['sector'] = $sector;
            }

            // Tenant'a özel prompt enhancement
            $enhancement = setting('ai_image_prompt_enhancement');
            if ($enhancement) {
                $context['prompt_enhancement'] = $enhancement;
            }

            // Tenant ID'ye göre varsayılan sektör ayarları
            $tenantId = tenant('id');
            if ($tenantId && empty($context['sector'])) {
                $context['sector'] = $this->getDefaultSectorByTenant($tenantId);
            }

            // Dil/kültür context'i
            $context['locale'] = app()->getLocale();
            $context['country'] = 'Turkey';

        } catch (\Exception $e) {
            Log::warning('Tenant context alınamadı', ['error' => $e->getMessage()]);
        }

        return $context;
    }

    /**
     * Tenant ID'ye göre varsayılan sektör
     */
    protected function getDefaultSectorByTenant(?int $tenantId): string
    {
        return match ($tenantId) {
            2, 3 => 'industrial_equipment', // ixtif.com
            1001 => 'music_platform', // muzibu.com
            default => 'general_business',
        };
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

    public function render()
    {
        return view('mediamanagement::admin.livewire.ai-image-generator-component');
    }
}
