<?php

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\AI\app\Services\AIService;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Illuminate\Support\Facades\Log;
use App\Helpers\SlugHelper;

/**
 * UNIVERSAL AI CONTENT COMPONENT
 * Pattern: A1 CMS Universal System
 *
 * Tüm modüller için ortak AI Content Generation Component'i
 * Content generation, translation, suggestions
 *
 * Kullanım:
 * <livewire:ai::universal-ai-content
 *     :model-id="$modelId"
 *     model-type="page"
 *     model-class="Modules\Page\App\Models\Page"
 *     :current-language="$currentLanguage"
 *     :available-languages="$availableLanguages"
 * />
 */
class UniversalAIContentComponent extends Component implements AIContentGeneratable
{
    use HasAIContentGeneration;

    // Model bilgileri
    public $modelId;
    public $modelType; // 'page', 'blog', 'product', etc.
    public $modelClass;

    // Dil yönetimi
    public $currentLanguage;
    public $availableLanguages = [];

    // AI Service
    protected $aiService;

    // Listeners
    protected $listeners = [
        'languageChanged' => 'handleLanguageChange',
        'translate-content' => 'translateContent',
        'generate-ai-content' => 'generateAIContentForField',
    ];

    public function mount($modelId = null, $modelType = 'page', $modelClass = null, $currentLanguage = 'tr', $availableLanguages = [])
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;
        $this->modelClass = $modelClass;
        $this->currentLanguage = $currentLanguage ?? 'tr';
        $this->availableLanguages = !empty($availableLanguages) ? $availableLanguages : ['tr'];

        // AI Service initialize
        $this->aiService = app(AIService::class);

        Log::info('🤖 UniversalAIContent mounted', [
            'model_id' => $this->modelId,
            'model_type' => $this->modelType,
            'current_language' => $this->currentLanguage
        ]);
    }

    /**
     * Dil değişikliğini handle et
     */
    public function handleLanguageChange($language)
    {
        $this->currentLanguage = $language;

        Log::info('🔄 UniversalAIContent dil değişti', [
            'new_language' => $language
        ]);
    }

    /**
     * AI İÇERİK ÇEVİRİ SİSTEMİ - KAYNAK DİLİ HEDEF DİLLERE ÇEVIR
     */
    public function translateContent($data)
    {
        $sourceLanguage = $data['sourceLanguage'] ?? $this->currentLanguage;
        $targetLanguages = $data['targetLanguages'] ?? [];
        $fields = $data['fields'] ?? ['title', 'body'];
        $overwriteExisting = $data['overwriteExisting'] ?? false;
        $sourceData = $data['sourceData'] ?? [];

        Log::info('🚀 Universal AI Translation başlatıldı', [
            'source_language' => $sourceLanguage,
            'target_languages' => $targetLanguages,
            'fields' => $fields,
            'overwrite' => $overwriteExisting
        ]);

        // Validasyon kontrolleri
        if (empty($targetLanguages)) {
            $this->dispatchTranslationResult([
                'success' => false,
                'message' => 'Hedef dil seçiniz',
                'type' => 'warning'
            ]);
            return;
        }

        // Kaynak dili kontrol et
        if (!in_array($sourceLanguage, $this->availableLanguages)) {
            $this->dispatchTranslationResult([
                'success' => false,
                'message' => "Kaynak dil ({$sourceLanguage}) aktif değil",
                'type' => 'error'
            ]);
            return;
        }

        // Hedef dilleri kontrol et
        $validTargetLanguages = array_intersect($targetLanguages, $this->availableLanguages);
        if (empty($validTargetLanguages)) {
            $this->dispatchTranslationResult([
                'success' => false,
                'message' => 'Geçerli hedef dil bulunamadı',
                'type' => 'error'
            ]);
            return;
        }

        // Kaynak dil verilerinin var olduğunu kontrol et
        if (empty(array_filter($sourceData))) {
            $this->dispatchTranslationResult([
                'success' => false,
                'message' => "Kaynak dil ({$sourceLanguage}) verileri bulunamadı",
                'type' => 'warning'
            ]);
            return;
        }

        try {
            $translatedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $results = [];

            foreach ($validTargetLanguages as $targetLang) {
                if ($targetLang === $sourceLanguage) {
                    Log::info("⏭️ Kaynak dil atlandı: {$targetLang}");
                    continue;
                }

                foreach ($fields as $field) {
                    $sourceText = $sourceData[$field] ?? '';
                    if (empty(trim($sourceText))) {
                        Log::info("⏭️ Boş kaynak alan atlandı: {$targetLang}.{$field}");
                        continue;
                    }

                    // Parent component'ten mevcut veri kontrolü yapılacak
                    // Bu component sadece çeviri yapar, veri kaydı parent'ta

                    try {
                        Log::info("🔄 Çeviri başlatılıyor: {$sourceLanguage} -> {$targetLang} [{$field}]");

                        $translatedText = $this->aiService->translateText(
                            $sourceText,
                            $sourceLanguage,
                            $targetLang,
                            [
                                'context' => $field === 'body' ? 'html_content' : 'title',
                                'max_length' => $field === 'title' ? 255 : null,
                                'preserve_html' => $field === 'body'
                            ]
                        );

                        if (!empty(trim($translatedText))) {
                            $translatedCount++;
                            $results[] = [
                                'language' => $targetLang,
                                'field' => $field,
                                'success' => true,
                                'translated_text' => $translatedText,
                                'original' => substr($sourceText, 0, 100) . (strlen($sourceText) > 100 ? '...' : ''),
                                'translated' => substr($translatedText, 0, 100) . (strlen($translatedText) > 100 ? '...' : '')
                            ];

                            Log::info("✅ Çeviri başarılı: {$targetLang}.{$field}", [
                                'source_length' => strlen($sourceText),
                                'translated_length' => strlen($translatedText)
                            ]);
                        } else {
                            throw new \Exception('Boş çeviri sonucu');
                        }

                    } catch (\Exception $e) {
                        $errorCount++;
                        $results[] = [
                            'language' => $targetLang,
                            'field' => $field,
                            'success' => false,
                            'error' => $e->getMessage()
                        ];

                        Log::error("❌ Çeviri hatası: {$targetLang}.{$field}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Parent component'e çeviri sonuçlarını gönder
            $this->dispatchTranslationResult([
                'success' => $translatedCount > 0,
                'message' => $this->buildTranslationMessage($translatedCount, $skippedCount, $errorCount),
                'type' => $translatedCount > 0 ? 'success' : 'error',
                'results' => $results,
                'translated_count' => $translatedCount,
                'error_count' => $errorCount
            ]);

            Log::info('🏁 Universal AI Translation tamamlandı', [
                'translated_fields' => $translatedCount,
                'errors' => $errorCount
            ]);

        } catch (\Exception $e) {
            $this->dispatchTranslationResult([
                'success' => false,
                'message' => 'Çeviri işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);

            Log::error('🚨 Universal AI Translation Error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Çeviri sonuç mesajı oluştur
     */
    private function buildTranslationMessage(int $translated, int $skipped, int $errors): string
    {
        $messages = [];
        if ($translated > 0) {
            $messages[] = "{$translated} alan çevrildi";
        }
        if ($skipped > 0) {
            $messages[] = "{$skipped} alan atlandı";
        }
        if ($errors > 0) {
            $messages[] = "{$errors} hata";
        }

        return !empty($messages) ? implode(', ', $messages) : 'İşlem tamamlandı';
    }

    /**
     * Parent component'e çeviri sonuçlarını gönder
     */
    private function dispatchTranslationResult(array $result)
    {
        // Parent component'e sonuçları gönder
        $this->dispatch('translation-completed', $result);

        // Toast mesajı da gönder
        $this->dispatch('toast', [
            'title' => $result['success'] ? 'Çeviri Tamamlandı' : 'Çeviri Hatası',
            'message' => $result['message'],
            'type' => $result['type'] ?? 'info'
        ]);
    }

    /**
     * AI Content Generation
     */
    public function generateAIContentForField($data)
    {
        $prompt = $data['prompt'] ?? '';
        $targetField = $data['targetField'] ?? 'body';
        $contentType = $data['contentType'] ?? $this->modelType;

        if (empty($prompt)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Prompt boş olamaz',
                'type' => 'error'
            ]);
            return;
        }

        try {
            Log::info('🚀 Universal AI Content Generation başlatıldı', [
                'model_type' => $this->modelType,
                'target_field' => $targetField,
                'prompt_length' => strlen($prompt)
            ]);

            $params = [
                'prompt' => $prompt,
                'target_field' => $targetField,
                'content_type' => $contentType,
                'length' => 'long',
                'tenant_id' => tenant('id'),
            ];

            $result = $this->generateAIContent($params);

            if ($result['success']) {
                // Parent component'e içeriği gönder
                $this->dispatch('ai-content-generated', [
                    'success' => true,
                    'content' => $result['content'],
                    'target_field' => $targetField,
                    'language' => $this->currentLanguage
                ]);

                $this->dispatch('toast', [
                    'title' => 'AI İçerik Üretildi',
                    'message' => 'İçerik başarıyla üretildi',
                    'type' => 'success'
                ]);

                Log::info('✅ Universal AI Content Generation başarılı', [
                    'content_length' => strlen($result['content'])
                ]);

            } else {
                $this->dispatch('toast', [
                    'title' => 'AI İçerik Hatası',
                    'message' => $result['error'] ?? 'İçerik üretilemedi',
                    'type' => 'error'
                ]);

                Log::error('❌ Universal AI Content Generation hatası', [
                    'error' => $result['error'] ?? 'unknown'
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Sistem Hatası',
                'message' => 'AI içerik üretimi sırasında hata oluştu',
                'type' => 'error'
            ]);

            Log::error('❌ Universal AI Content Generation exception', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // =================================
    // AI TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return $this->modelType;
    }

    public function getTargetFields(array $params): array
    {
        $defaultFields = [
            'title' => 'string',
            'body' => 'html',
            'content' => 'html',
            'description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $defaultFields[$params['target_field']] ?? 'html'];
        }

        return $defaultFields;
    }

    public function getModuleInstructions(): string
    {
        return "Universal AI Content Generation için genel talimatlar. Modül: {$this->modelType}";
    }

    public function render()
    {
        return view('ai::admin.livewire.universal-ai-content-component');
    }
}