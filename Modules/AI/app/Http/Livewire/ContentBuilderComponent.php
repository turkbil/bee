<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Livewire;

use Livewire\Component;
use Modules\ThemeManagement\app\Services\ThemeAnalyzerService;
use Modules\ThemeManagement\app\Services\AIContentGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

/**
 * AI Content Builder Livewire Component
 *
 * Rich editör yanında kullanılacak AI içerik üretici
 */
class ContentBuilderComponent extends Component
{
    // Component durumu
    public bool $isOpen = false;
    public string $panelPosition = 'right'; // right veya modal

    // Form inputları
    public string $userPrompt = '';
    public string $contentType = 'auto';
    public string $contentLength = 'medium';
    public string $customInstructions = '';
    public string $selectedTemplate = '';

    // Tema ve içerik bilgileri
    public array $themePreview = [];
    public array $availableTemplates = [];
    public string $generatedContent = '';
    public bool $isGenerating = false;

    // Kredi bilgileri
    public int $creditsAvailable = 0;
    public int $estimatedCredits = 5;

    // Sayfa bilgileri
    public ?int $pageId = null;
    public ?string $pageTitle = null;
    public ?string $targetField = 'body';

    // Services
    private ?ThemeAnalyzerService $themeAnalyzer = null;
    private ?AIContentGeneratorService $contentGenerator = null;

    protected $listeners = [
        'openContentBuilder' => 'open',
        'closeContentBuilder' => 'close'
    ];

    // Livewire v3 için event listener
    public function getListeners()
    {
        return [
            'openContentBuilder' => 'open',
            'closeContentBuilder' => 'close'
        ];
    }

    public function boot()
    {
        $this->initializeServices();
    }

    public function mount(?int $pageId = null, ?string $pageTitle = null, ?string $targetField = 'body')
    {
        $this->pageId = $pageId;
        $this->pageTitle = $pageTitle;
        $this->targetField = $targetField;

        $this->initializeServices();
        $this->loadThemePreview();
        $this->loadTemplates();
        $this->loadCredits();
    }

    private function initializeServices(): void
    {
        if (!$this->themeAnalyzer) {
            $this->themeAnalyzer = new ThemeAnalyzerService();
        }
        if (!$this->contentGenerator) {
            $this->contentGenerator = new AIContentGeneratorService();
        }
    }

    private function loadThemePreview(): void
    {
        try {
            $tenantId = tenant('id') ?? 1;
            $this->themePreview = $this->themeAnalyzer->getThemePreview($tenantId);
        } catch (\Exception $e) {
            Log::error('Theme preview load error: ' . $e->getMessage());
            $this->themePreview = [
                'theme_name' => 'Default',
                'framework' => 'tailwind',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#6B7280'
            ];
        }
    }

    private function loadTemplates(): void
    {
        $this->availableTemplates = $this->contentGenerator->getTemplates();
    }

    private function loadCredits(): void
    {
        try {
            $tenant = Tenant::find(tenant('id') ?? 1);
            $this->creditsAvailable = (int) ($tenant->ai_credits_balance ?? 0);
        } catch (\Exception $e) {
            $this->creditsAvailable = 0;
        }
    }

    public function open(array $params = []): void
    {
        $this->isOpen = true;

        if (isset($params['pageId'])) {
            $this->pageId = $params['pageId'];
        }

        if (isset($params['pageTitle'])) {
            $this->pageTitle = $params['pageTitle'];
        }

        if (isset($params['targetField'])) {
            $this->targetField = $params['targetField'];
        }

        // Service'lerin yüklü olduğundan emin ol
        $this->initializeServices();

        // Tema bilgilerini yenile
        $this->loadThemePreview();
        $this->loadCredits();

        $this->dispatch('content-builder-opened');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->resetForm();
        $this->dispatch('content-builder-closed');
    }

    public function selectTemplate(string $templateKey): void
    {
        $this->selectedTemplate = $templateKey;
        $this->contentType = $templateKey;

        // Şablon seçildiğinde tahmini krediyi güncelle
        if (isset($this->availableTemplates[$templateKey])) {
            $this->estimatedCredits = $this->availableTemplates[$templateKey]['credits'];
        }
    }

    public function updatePrompt(): void
    {
        // Prompt değiştiğinde içerik tipini otomatik tespit et
        if (empty($this->selectedTemplate) && $this->contentType === 'auto') {
            $this->detectContentType();
        }

        // Tahmini krediyi güncelle
        $this->updateEstimatedCredits();
    }

    private function detectContentType(): void
    {
        $prompt = strtolower($this->userPrompt);

        $patterns = [
            'hero' => ['hero', 'başlangıç', 'giriş'],
            'features' => ['özellik', 'feature', 'avantaj'],
            'pricing' => ['fiyat', 'paket', 'pricing'],
            'about' => ['hakkımızda', 'hakkında', 'about'],
            'contact' => ['iletişim', 'contact', 'ulaş']
        ];

        foreach ($patterns as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($prompt, $keyword)) {
                    $this->contentType = $type;
                    return;
                }
            }
        }
    }

    private function updateEstimatedCredits(): void
    {
        $baseCredits = 5;

        // Uzunluğa göre ayarla
        $lengthMultiplier = match($this->contentLength) {
            'short' => 0.7,
            'long' => 1.5,
            default => 1.0
        };

        // İçerik tipine göre ayarla
        if (in_array($this->contentType, ['pricing', 'team', 'features'])) {
            $baseCredits = 10;
        } elseif (in_array($this->contentType, ['hero', 'cta'])) {
            $baseCredits = 3;
        }

        $this->estimatedCredits = (int) ceil($baseCredits * $lengthMultiplier);
    }

    public function generateContent(): void
    {
        Log::info('🚀 ContentBuilderComponent::generateContent başladı', [
            'userPrompt' => $this->userPrompt,
            'contentType' => $this->contentType,
            'pageTitle' => $this->pageTitle,
            'targetField' => $this->targetField,
            'creditsAvailable' => $this->creditsAvailable,
            'estimatedCredits' => $this->estimatedCredits
        ]);

        // Validasyon
        if ($this->creditsAvailable < $this->estimatedCredits) {
            Log::warning('⚠️ Yetersiz kredi', [
                'available' => $this->creditsAvailable,
                'required' => $this->estimatedCredits
            ]);
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Yetersiz kredi bakiyesi!'
            );
            return;
        }

        $this->isGenerating = true;
        $this->generatedContent = '';
        Log::info('🔄 İçerik üretimi başlatıldı...');

        try {
            // Parametreleri hazırla
            $params = [
                'tenant_id' => tenant('id') ?? 1,
                'prompt' => $this->userPrompt ?: $this->getDefaultPrompt(),
                'content_type' => $this->contentType,
                'length' => $this->contentLength,
                'custom_instructions' => $this->customInstructions,
                'page_title' => $this->pageTitle
            ];

            Log::info('📄 Parametreler hazırlandı', $params);

            // Şablon kullanılıyorsa
            if (!empty($this->selectedTemplate)) {
                Log::info('🎭 Şablon kullanılarak üretiliyor', ['template' => $this->selectedTemplate]);
                $result = $this->contentGenerator->generateFromTemplate($this->selectedTemplate, $params);
            } else {
                Log::info('🤖 Doğrudan AI ile üretiliyor');
                $result = $this->contentGenerator->generateContent($params);
            }

            Log::info('📦 Service\'ten dönen sonuç', [
                'success' => $result['success'] ?? false,
                'has_content' => isset($result['content']) && !empty($result['content']),
                'content_length' => isset($result['content']) ? strlen($result['content']) : 0,
                'credits_used' => $result['credits_used'] ?? 0,
                'error' => $result['error'] ?? null
            ]);

            if ($result['success']) {
                $this->generatedContent = $result['content'];
                $this->creditsAvailable -= $result['credits_used'];

                Log::info('🎆 İçerik başarıyla üretildi', [
                    'generatedContentLength' => strlen($this->generatedContent),
                    'first_500_chars' => substr($this->generatedContent, 0, 500),
                    'targetField' => $this->targetField
                ]);

                // İçeriği doğrudan editöre ekle - ESKİ İÇERİĞİ SİL
                Log::info('📝 Editöre içerik gönderiliyor...', [
                    'field' => $this->targetField,
                    'contentLength' => strlen($this->generatedContent)
                ]);

                $this->dispatch('replaceContentInEditor',
                    field: $this->targetField,
                    content: $this->generatedContent
                );

                Log::info('✅ replaceContentInEditor event\'i dispatch edildi');

                // Başarı mesajı göster
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'İçerik üretildi ve editöre eklendi! Eski içerik silindi.'
                );

                // Formu temizle
                $this->generatedContent = '';
                $this->userPrompt = '';
                $this->estimatedCredits = 5;

                Log::info('🎉 İşlem tamamlandı, form temizlendi');
            } else {
                throw new \Exception($result['error'] ?? 'İçerik üretimi başarısız');
            }

        } catch (\Exception $e) {
            Log::error('❌ Content generation error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('show-toast',
                type: 'error',
                message: 'İçerik üretilirken hata oluştu: ' . $e->getMessage()
            );
        } finally {
            $this->isGenerating = false;
            Log::info('🎬 generateContent metodu tamamlandı');
        }
    }

    public function insertContent(): void
    {
        if (empty($this->generatedContent)) {
            return;
        }

        // İçeriği editöre ekle - doğrudan JavaScript'e gönder
        $this->dispatch('insertContentToEditor',
            field: $this->targetField,
            content: $this->generatedContent,
            immediate: true
        );

        // Başarı mesajı
        $this->dispatch('show-toast',
            type: 'success',
            message: 'İçerik editöre eklendi!'
        );

        // Formu temizle ama paneli kapatma (kullanıcı devam edebilsin)
        $this->generatedContent = '';
        $this->estimatedCredits = 5;
    }

    public function regenerate(): void
    {
        // Aynı parametrelerle yeniden üret
        $this->generateContent();
    }

    private function getDefaultPrompt(): string
    {
        // Boş prompt durumunda sayfa başlığına göre içerik üret
        if ($this->pageTitle) {
            return "{$this->pageTitle} sayfası için profesyonel içerik üret";
        }

        return match($this->contentType) {
            'hero' => 'Etkileyici bir hero section oluştur',
            'features' => 'Ürün veya hizmet özelliklerini listele',
            'pricing' => 'Fiyatlandırma tabloları oluştur',
            'about' => 'Hakkımızda içeriği oluştur',
            'contact' => 'İletişim bölümü oluştur',
            default => 'Profesyonel web içeriği oluştur'
        };
    }

    private function resetForm(): void
    {
        $this->userPrompt = '';
        $this->contentType = 'auto';
        $this->contentLength = 'medium';
        $this->customInstructions = '';
        $this->selectedTemplate = '';
        $this->generatedContent = '';
        $this->estimatedCredits = 5;
    }

    public function toggleAdvancedSettings(): void
    {
        $this->dispatch('toggle-advanced-settings');
    }

    public function render()
    {
        return view('ai::livewire.content-builder-component', [
            'themePreview' => $this->themePreview,
            'templates' => $this->availableTemplates,
            'creditsAvailable' => $this->creditsAvailable,
            'estimatedCredits' => $this->estimatedCredits
        ]);
    }
}