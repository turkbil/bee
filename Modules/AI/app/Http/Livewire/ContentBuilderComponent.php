<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Livewire;

use Livewire\Component;
use Modules\ThemeManagement\app\Services\ThemeAnalyzerService;
use Modules\AI\app\Services\Content\AIContentGeneratorService;
use Modules\AI\App\Jobs\AIContentGenerationJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;

/**
 * AI Content Builder Livewire Component - GLOBAL MODAL PATTERN
 * Pattern: AI Translation Component
 *
 * Artık global modal olarak çalışır, trigger button'dan açılır
 */
class ContentBuilderComponent extends Component
{
    // Module context (any module can use this)
    public string $module = 'page';

    // Kredi bilgileri
    public ?int $creditsAvailable = null;
    public int $estimatedCredits = 15;

    // Global state tracking (for AJAX mode)
    public bool $isGenerating = false;
    public ?string $currentJobId = null;
    public ?string $pageTitle = null;
    public ?string $targetField = 'body';

    // Theme preview data
    public array $themePreview = [];

    // Progress tracking
    public int $progressPercentage = 0;
    public string $progressMessage = '';

    // Queue usage
    public bool $useQueue = true;

    // Services
    private ?ThemeAnalyzerService $themeAnalyzer = null;
    private ?AIContentGeneratorService $contentGenerator = null;

    protected $listeners = [
        'openContentBuilder' => 'open',
        'closeContentBuilder' => 'close',
        'contentGenerationCompleted' => 'handleContentCompleted',
        'contentGenerationFailed' => 'handleContentFailed'
    ];

    // Livewire v3 için event listener
    public function getListeners()
    {
        return [
            'openContentBuilder' => 'open',
            'closeContentBuilder' => 'close',
            'contentGenerationCompleted' => 'handleContentCompleted',
            'contentGenerationFailed' => 'handleContentFailed'
        ];
    }

    /**
     * AI Content alma method'u (Global modal'dan çağrılır)
     */
    public function receiveAIContent(array $data): void
    {
        $content = $data['content'] ?? '';
        $targetField = $data['targetField'] ?? 'body';

        Log::info('🎯 AI Content alındı (Livewire)', [
            'targetField' => $targetField,
            'contentLength' => strlen($content),
            'module' => $this->module
        ]);

        // Content'i editöre gönder
        $this->dispatch('replaceContentInEditor', [
            'field' => $targetField,
            'content' => $content
        ]);

        // Success toast
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => '✅ AI içerik başarıyla editöre eklendi!'
        ]);
    }

    public function boot()
    {
        $this->initializeServices();
    }

    public function mount(?string $module = 'page', ?string $targetField = 'body')
    {
        $this->module = $module ?: 'page';
        $this->targetField = $targetField ?: 'body';

        $this->loadCredits();
    }

    private function initializeServices(): void
    {
        if (!$this->themeAnalyzer) {
            $this->themeAnalyzer = new ThemeAnalyzerService();
        }
        if (!$this->contentGenerator) {
            $this->contentGenerator = app(AIContentGeneratorService::class);
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

    // Template sistemi kaldırıldı - Dinamik tema analizi kullanılıyor

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

    // Template sistemi kaldırıldı

    public function updatePrompt(): void
    {
        // Content type otomatik tespit (her zaman)
        $this->detectContentType();

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
        // Hep uzun sayfa üreteceğiz - sabit kredi
        $this->estimatedCredits = 15; // Ultra uzun sayfa için sabit kredi
    }

    public function generateContent(): void
    {
        Log::info('🚀 ContentBuilderComponent::generateContent başladı (QUEUE)', [
            'userPrompt' => $this->userPrompt,
            'contentType' => $this->contentType,
            'pageTitle' => $this->pageTitle,
            'targetField' => $this->targetField,
            'creditsAvailable' => $this->creditsAvailable,
            'estimatedCredits' => $this->estimatedCredits,
            'useQueue' => $this->useQueue
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

        // Queue sistemi kullan
        if ($this->useQueue) {
            $this->generateContentWithQueue();
        } else {
            $this->generateContentSync();
        }
    }

    /**
     * Queue ile async içerik üretimi
     */
    private function generateContentWithQueue(): void
    {
        $this->isGenerating = true;
        $this->progressPercentage = 0;
        $this->progressMessage = 'İşlem queue\'ya ekleniyor...';
        $this->generatedContent = '';

        // Unique session ID oluştur
        $this->currentSessionId = 'ai_content_' . Str::uuid()->toString();

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

            Log::info('📄 Queue parametreleri hazırlandı', [
                'session_id' => $this->currentSessionId,
                'params' => $params
            ]);

            // Job'u queue'ya ekle
            AIContentGenerationJob::dispatch(
                $params,
                $this->currentSessionId,
                'ContentBuilder'
            )->onQueue('ai-content');

            $this->progressPercentage = 5;
            $this->progressMessage = 'İşlem queue\'ya eklendi, işleniyor...';

            Log::info('✅ AI Content Generation Job queue\'ya eklendi', [
                'session_id' => $this->currentSessionId
            ]);

            // Progress tracking başlat
            $this->startProgressTracking();

            $this->dispatch('show-toast',
                type: 'info',
                message: 'İçerik üretimi başlatıldı. Lütfen bekleyin...'
            );

        } catch (\Exception $e) {
            Log::error('❌ Queue job dispatch error', [
                'message' => $e->getMessage(),
                'session_id' => $this->currentSessionId
            ]);

            $this->handleGenerationError($e->getMessage());
        }
    }

    /**
     * Senkron içerik üretimi (fallback)
     */
    private function generateContentSync(): void
    {
        $this->isGenerating = true;
        $this->generatedContent = '';
        Log::info('🔄 Senkron içerik üretimi başlatıldı...');

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

            // Dinamik tema analizi ile üret
            $result = $this->contentGenerator->generateContent($params);

            if ($result['success']) {
                $this->handleSuccessfulGeneration($result);
            } else {
                throw new \Exception($result['error'] ?? 'İçerik üretimi başarısız');
            }

        } catch (\Exception $e) {
            $this->handleGenerationError($e->getMessage());
        } finally {
            $this->isGenerating = false;
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
        $this->contentLength = 'ultra_long'; // Hep uzun!
        $this->customInstructions = '';
        $this->selectedTemplate = '';
        $this->generatedContent = '';
        $this->estimatedCredits = 15; // Ultra uzun için sabit

        // Queue tracking reset
        $this->currentSessionId = null;
        $this->progressPercentage = 0;
        $this->progressMessage = '';
    }

    public function toggleAdvancedSettings(): void
    {
        $this->dispatch('toggle-advanced-settings');
    }

    /**
     * Progress tracking başlat
     */
    private function startProgressTracking(): void
    {
        if (!$this->currentSessionId) {
            return;
        }

        // JavaScript'te polling başlat
        $this->dispatch('startProgressTracking', [
            'sessionId' => $this->currentSessionId,
            'interval' => 2000 // 2 saniye
        ]);
    }

    /**
     * Progress güncelleme (JS polling'den çağrılır)
     */
    public function checkProgress(): void
    {
        if (!$this->currentSessionId) {
            return;
        }

        $progressKey = "ai_content_progress_{$this->currentSessionId}";
        $progress = Cache::get($progressKey);

        if ($progress) {
            $this->progressPercentage = $progress['percentage'] ?? 0;
            $this->progressMessage = $progress['message'] ?? '';

            // İşlem tamamlandıysa sonucu kontrol et
            if ($this->progressPercentage >= 100) {
                $this->checkResult();
            }
        }

        // Hata durumunu kontrol et
        $errorKey = "ai_content_error_{$this->currentSessionId}";
        $error = Cache::get($errorKey);

        if ($error) {
            $this->handleGenerationError($error['error'] ?? 'Bilinmeyen hata');
        }
    }

    /**
     * Sonucu kontrol et ve al
     */
    private function checkResult(): void
    {
        if (!$this->currentSessionId) {
            return;
        }

        $resultKey = "ai_content_result_{$this->currentSessionId}";
        $result = Cache::get($resultKey);

        if ($result && $result['success']) {
            $this->handleSuccessfulGeneration($result);

            // Cache'i temizle
            Cache::forget($resultKey);
            Cache::forget("ai_content_progress_{$this->currentSessionId}");
        }
    }

    /**
     * Başarılı içerik üretimi işle
     */
    private function handleSuccessfulGeneration(array $result): void
    {
        $this->generatedContent = $result['content'];
        $this->creditsAvailable -= $result['credits_used'] ?? 15;

        Log::info('🎆 İçerik başarıyla üretildi (Queue)', [
            'session_id' => $this->currentSessionId,
            'content_length' => strlen($this->generatedContent),
            'credits_used' => $result['credits_used'],
            'targetField' => $this->targetField
        ]);

        // İçeriği doğrudan editöre ekle
        $this->dispatch('replaceContentInEditor',
            field: $this->targetField,
            content: $this->generatedContent
        );

        // Başarı mesajı göster
        $this->dispatch('show-toast',
            type: 'success',
            message: 'İçerik başarıyla üretildi ve editöre eklendi!'
        );

        // Formu temizle
        $this->resetAfterSuccess();
    }

    /**
     * Hata durumunu işle
     */
    private function handleGenerationError(string $errorMessage): void
    {
        Log::error('❌ Content generation error handled', [
            'session_id' => $this->currentSessionId,
            'error' => $errorMessage
        ]);

        $this->isGenerating = false;
        $this->progressPercentage = 0;
        $this->progressMessage = '';

        $this->dispatch('show-toast',
            type: 'error',
            message: 'İçerik üretilirken hata oluştu: ' . $errorMessage
        );

        // Progress tracking'i durdur
        $this->dispatch('stopProgressTracking');

        // Cache'leri temizle
        if ($this->currentSessionId) {
            Cache::forget("ai_content_progress_{$this->currentSessionId}");
            Cache::forget("ai_content_error_{$this->currentSessionId}");
            Cache::forget("ai_content_result_{$this->currentSessionId}");
        }
    }

    /**
     * Başarılı işlem sonrası temizlik
     */
    private function resetAfterSuccess(): void
    {
        $this->generatedContent = '';
        $this->userPrompt = '';
        $this->isGenerating = false;
        $this->progressPercentage = 0;
        $this->progressMessage = '';

        // Progress tracking'i durdur
        $this->dispatch('stopProgressTracking');

        // Session'ı temizle
        $this->currentSessionId = null;

        Log::info('🎉 İşlem tamamlandı, form temizlendi (Queue)');
    }

    /**
     * Event handler: İçerik üretimi tamamlandı
     */
    public function handleContentCompleted($data): void
    {
        if ($data['session_id'] === $this->currentSessionId) {
            $this->checkResult();
        }
    }

    /**
     * Event handler: İçerik üretimi başarısız
     */
    public function handleContentFailed($data): void
    {
        if ($data['session_id'] === $this->currentSessionId) {
            $this->handleGenerationError($data['error'] ?? 'Bilinmeyen hata');
        }
    }

    /**
     * Manual progress check (JS polling için)
     */
    public function pollProgress(): array
    {
        if (!$this->currentSessionId) {
            return ['status' => 'no_session'];
        }

        $progressKey = "ai_content_progress_{$this->currentSessionId}";
        $progress = Cache::get($progressKey);

        $errorKey = "ai_content_error_{$this->currentSessionId}";
        $error = Cache::get($errorKey);

        if ($error) {
            return [
                'status' => 'error',
                'error' => $error['error'] ?? 'Bilinmeyen hata'
            ];
        }

        if ($progress) {
            return [
                'status' => 'processing',
                'percentage' => $progress['percentage'] ?? 0,
                'message' => $progress['message'] ?? ''
            ];
        }

        return ['status' => 'waiting'];
    }

    /**
     * Queue kullanımını toggle et
     */
    public function toggleQueue(): void
    {
        $this->useQueue = !$this->useQueue;

        $this->dispatch('show-toast',
            type: 'info',
            message: $this->useQueue ? 'Queue sistemi aktif' : 'Senkron işlem aktif'
        );
    }

    public function render()
    {
        return view('ai::livewire.content-builder-component', [
            'themePreview' => $this->themePreview,
            'creditsAvailable' => $this->creditsAvailable,
            'estimatedCredits' => $this->estimatedCredits,
            'progressPercentage' => $this->progressPercentage,
            'progressMessage' => $this->progressMessage,
            'useQueue' => $this->useQueue
        ]);
    }
}