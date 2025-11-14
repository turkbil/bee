<?php

namespace Modules\MediaManagement\App\Http\Livewire\Admin;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Services\AIImageGenerationService;

#[Layout('admin.layout')]
class AiImageGeneratorComponent extends Component
{
    public string $prompt = '';
    public string $size = '1792x1024';
    public string $quality = 'hd';
    public string $companyName = '';
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
    ];

    public function mount()
    {
        $this->loadCredits();
        $this->loadHistory();

        // Settings'ten site adını al (kısa - prompt için uygun)
        $this->companyName = setting('site_name', '');
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

            // Site adını otomatik ekle (varsa)
            $finalPrompt = $this->prompt;
            if (!empty($this->companyName)) {
                $finalPrompt = $this->companyName . ' - ' . $this->prompt;
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

    public function render()
    {
        return view('mediamanagement::admin.livewire.ai-image-generator-component');
    }
}
