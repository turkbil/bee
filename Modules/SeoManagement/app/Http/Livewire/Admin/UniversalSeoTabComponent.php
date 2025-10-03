<?php

namespace Modules\SeoManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\SeoManagement\App\Models\SeoSetting;
use Illuminate\Support\Facades\Log;

/**
 * UNIVERSAL SEO TAB COMPONENT
 * Pattern: A1 CMS Universal System
 *
 * Tüm modüller için ortak SEO Tab component'i
 * Polymorphic relationship ile çalışır
 *
 * Kullanım:
 * <livewire:seomanagement::universal-seo-tab
 *     :model-id="$pageId"
 *     model-type="page"
 *     model-class="Modules\Page\App\Models\Page"
 * />
 */
class UniversalSeoTabComponent extends Component
{
    // Model bilgileri
    public $modelId;
    public $modelType; // 'page', 'blog', 'product', etc.
    public $modelClass; // 'Modules\Page\App\Models\Page'

    // Dil ve cache
    public $currentLanguage;
    public $availableLanguages = [];
    public $seoDataCache = [];

    // AI Recommendations
    public $recommendationLoaders = [];
    public $staticAiRecommendations = [];
    public $dynamicAiRecommendations = [];
    public $staticAiAnalysis = [];
    public $dynamicAiAnalysis = [];

    protected $listeners = [
        'languageChanged' => 'handleLanguageChange',
        'refreshSeoTab' => '$refresh',
        'page-saved' => 'saveSeoData', // Parent component'ten gelen save event'i
    ];

    public function mount($modelId = null, $modelType = 'page', $modelClass = null)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;
        $this->modelClass = $modelClass;

        // Varsayılan dil bilgileri (cache'li helper'lar kullan)
        $this->currentLanguage = get_tenant_default_locale();
        $this->availableLanguages = array_column(available_tenant_languages(), 'code');

        // SEO cache'ini yükle
        $this->loadSeoDataCache();
    }

    /**
     * SEO verilerini cache'e yükle
     */
    public function loadSeoDataCache()
    {
        Log::info('🔍 UniversalSeoTab - loadSeoDataCache başlıyor', [
            'modelId' => $this->modelId,
            'modelClass' => $this->modelClass,
            'availableLanguages' => $this->availableLanguages
        ]);

        if (!$this->modelId || !$this->modelClass) {
            // Yeni kayıt - boş cache
            Log::info('⚠️ UniversalSeoTab - Model bilgileri eksik, boş cache oluşturuluyor');
            foreach ($this->availableLanguages as $lang) {
                $this->seoDataCache[$lang] = $this->getEmptySeoData();
                $this->recommendationLoaders[$lang] = false;
            }
            return;
        }

        try {
            // SEO Setting'i yükle (polymorphic)
            Log::info('🔍 UniversalSeoTab - Veritabanından SEO verisi aranıyor', [
                'seoable_type' => $this->modelClass,
                'seoable_id' => $this->modelId
            ]);

            $seoSetting = SeoSetting::where('seoable_type', $this->modelClass)
                ->where('seoable_id', $this->modelId)
                ->first();

            Log::info('🔍 UniversalSeoTab - Veritabanı sorgusu tamamlandı', [
                'seoSetting_found' => $seoSetting ? 'EVET' : 'HAYIR',
                'seoSetting_id' => $seoSetting->id ?? null
            ]);

            if ($seoSetting) {
                Log::info('✅ UniversalSeoTab - SEO Setting bulundu, decode ediliyor', [
                    'raw_titles' => $seoSetting->titles,
                    'raw_descriptions' => $seoSetting->descriptions
                ]);

                // JSON decode - DOĞRU KOLON İSİMLERİ!
                $seoTitle = is_string($seoSetting->titles) ? json_decode($seoSetting->titles, true) : $seoSetting->titles;
                $seoDescription = is_string($seoSetting->descriptions) ? json_decode($seoSetting->descriptions, true) : $seoSetting->descriptions;
                $schemaType = is_string($seoSetting->schema_types) ? json_decode($seoSetting->schema_types, true) : $seoSetting->schema_types;
                $priorityScore = is_string($seoSetting->priority_scores) ? json_decode($seoSetting->priority_scores, true) : $seoSetting->priority_scores;
                $ogTitle = is_string($seoSetting->og_titles) ? json_decode($seoSetting->og_titles, true) : $seoSetting->og_titles;
                $ogDescription = is_string($seoSetting->og_descriptions) ? json_decode($seoSetting->og_descriptions, true) : $seoSetting->og_descriptions;
                $ogImageUrl = is_string($seoSetting->og_images) ? json_decode($seoSetting->og_images, true) : $seoSetting->og_images;
                $authorName = is_string($seoSetting->author_names) ? json_decode($seoSetting->author_names, true) : $seoSetting->author_names;
                $authorUrl = is_string($seoSetting->author_urls) ? json_decode($seoSetting->author_urls, true) : $seoSetting->author_urls;
                $aiSuggestions = is_string($seoSetting->ai_suggestions) ? json_decode($seoSetting->ai_suggestions, true) : $seoSetting->ai_suggestions;

                Log::info('📦 UniversalSeoTab - JSON decode tamamlandı', [
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $seoDescription,
                    'schemaType' => $schemaType,
                    'priorityScore' => $priorityScore
                ]);

                // Her dil için cache'e aktar
                foreach ($this->availableLanguages as $lang) {
                    $this->seoDataCache[$lang] = [
                        'seo_title' => $seoTitle[$lang] ?? '',
                        'seo_description' => $seoDescription[$lang] ?? '',
                        'schema_type' => $schemaType[$lang] ?? 'WebPage',
                        'priority_score' => $priorityScore[$lang] ?? 5,
                        'og_title' => $ogTitle[$lang] ?? '',
                        'og_description' => $ogDescription[$lang] ?? '',
                        'og_image_url' => $ogImageUrl[$lang] ?? '',
                        'og_custom_enabled' => !empty(trim($ogTitle[$lang] ?? '')) || !empty(trim($ogDescription[$lang] ?? '')),
                        'author_name' => $authorName[$lang] ?? '',
                        'author_url' => $authorUrl[$lang] ?? '',
                    ];

                    Log::info("📝 UniversalSeoTab - Cache'e dil eklendi: {$lang}", [
                        'seoDataCache' => $this->seoDataCache[$lang]
                    ]);

                    // AI Recommendations
                    if (!empty($aiSuggestions[$lang])) {
                        $this->staticAiRecommendations[$lang] = $aiSuggestions[$lang];
                    }

                    $this->recommendationLoaders[$lang] = false;
                }

                Log::info('✅ UniversalSeoTab - TÜM DİLLER için cache hazır', [
                    'final_seoDataCache' => $this->seoDataCache
                ]);
            } else {
                Log::warning('⚠️ UniversalSeoTab - SEO Setting BULUNAMADI, boş cache oluşturuluyor');
                // SEO Setting yoksa boş cache
                foreach ($this->availableLanguages as $lang) {
                    $this->seoDataCache[$lang] = $this->getEmptySeoData();
                    $this->recommendationLoaders[$lang] = false;
                }
            }
        } catch (\Exception $e) {
            Log::error('UniversalSeoTabComponent loadSeoDataCache error: ' . $e->getMessage());

            // Hata durumunda boş cache
            foreach ($this->availableLanguages as $lang) {
                $this->seoDataCache[$lang] = $this->getEmptySeoData();
                $this->recommendationLoaders[$lang] = false;
            }
        }
    }

    /**
     * Boş SEO data array döndür
     */
    private function getEmptySeoData(): array
    {
        return [
            'seo_title' => '',
            'seo_description' => '',
            'schema_type' => 'WebPage',
            'priority_score' => 5,
            'og_title' => '',
            'og_description' => '',
            'og_image_url' => '',
            'og_custom_enabled' => false,
            'author_name' => '',
            'author_url' => '',
        ];
    }

    /**
     * Dil değişikliğini handle et
     */
    public function handleLanguageChange($language)
    {
        $this->currentLanguage = $language;
    }

    /**
     * SEO cache'ini parent component'e gönder
     * Parent component bu method'u çağırarak SEO verilerini alır
     */
    public function getSeoDataCache(): array
    {
        return $this->seoDataCache;
    }

    /**
     * SEO verilerini kaydet
     * Parent component save olduğunda tetiklenir
     */
    #[On('page-saved')]
    public function saveSeoData($pageId = null)
    {
        Log::info('🎯 UniversalSeoTab - saveSeoData ÇAĞRILDI!', [
            'received_pageId' => $pageId,
            'current_modelId' => $this->modelId
        ]);

        // Yeni oluşturulan model ID'sini güncelle
        if ($pageId) {
            $this->modelId = $pageId;
        }

        // Model yüklü değilse kaydetme
        if (!$this->modelId || !$this->modelClass) {
            Log::warning('⚠️ UniversalSeoTab - Model bilgileri eksik, kaydetme yapılamadı', [
                'modelId' => $this->modelId,
                'modelClass' => $this->modelClass
            ]);
            return;
        }

        Log::info('💾 UniversalSeoTab - SEO verileri kaydediliyor', [
            'modelId' => $this->modelId,
            'modelType' => $this->modelType,
            'modelClass' => $this->modelClass,
            'seoDataCache' => $this->seoDataCache
        ]);

        try {
            // Multi-lang JSON data hazırla
            $seoTitle = [];
            $seoDescription = [];
            $schemaType = [];
            $priorityScore = [];
            $ogTitle = [];
            $ogDescription = [];
            $ogImageUrl = [];
            $authorName = [];
            $authorUrl = [];

            foreach ($this->availableLanguages as $lang) {
                $langData = $this->seoDataCache[$lang] ?? $this->getEmptySeoData();

                $seoTitle[$lang] = $langData['seo_title'] ?? '';
                $seoDescription[$lang] = $langData['seo_description'] ?? '';
                $schemaType[$lang] = $langData['schema_type'] ?? 'WebPage';
                $priorityScore[$lang] = $langData['priority_score'] ?? 5;
                $ogTitle[$lang] = $langData['og_title'] ?? '';
                $ogDescription[$lang] = $langData['og_description'] ?? '';
                $ogImageUrl[$lang] = $langData['og_image_url'] ?? '';
                $authorName[$lang] = $langData['author_name'] ?? '';
                $authorUrl[$lang] = $langData['author_url'] ?? '';
            }

            // AI Analysis verilerini hazırla
            $detailedScores = null;
            $strengths = null;
            $improvements = null;
            $actionItems = null;

            // staticAiAnalysis veya dynamicAiAnalysis'ten verileri al
            foreach ($this->availableLanguages as $lang) {
                if (!empty($this->staticAiAnalysis[$lang])) {
                    $analysis = $this->staticAiAnalysis[$lang];

                    // Detaylı skorları topla
                    if (isset($analysis['detailed_scores'])) {
                        $detailedScores[$lang] = $analysis['detailed_scores'];
                    }

                    // Strengths topla
                    if (isset($analysis['strengths'])) {
                        $strengths[$lang] = $analysis['strengths'];
                    }

                    // Improvements topla
                    if (isset($analysis['improvements'])) {
                        $improvements[$lang] = $analysis['improvements'];
                    }

                    // Action items topla
                    if (isset($analysis['action_items'])) {
                        $actionItems[$lang] = $analysis['action_items'];
                    }
                }
            }

            // Polymorphic relationship ile kaydet - DOĞRU KOLON İSİMLERİ!
            $seoSetting = SeoSetting::updateOrCreate(
                [
                    'seoable_type' => $this->modelClass,
                    'seoable_id' => $this->modelId,
                ],
                [
                    'titles' => $seoTitle,
                    'descriptions' => $seoDescription,
                    'schema_types' => $schemaType,
                    'priority_scores' => $priorityScore,
                    'og_titles' => $ogTitle,
                    'og_descriptions' => $ogDescription,
                    'og_images' => $ogImageUrl,
                    'author_names' => $authorName,
                    'author_urls' => $authorUrl,
                    'ai_suggestions' => $this->staticAiRecommendations ?: null,
                    // AI Analysis Results
                    'analysis_results' => !empty($this->staticAiAnalysis) ? $this->staticAiAnalysis : null,
                    'detailed_scores' => $detailedScores,
                    'strengths' => $strengths,
                    'improvements' => $improvements,
                    'action_items' => $actionItems,
                    'analysis_date' => !empty($this->staticAiAnalysis) ? now() : null,
                ]
            );

            Log::info('✅ UniversalSeoTab - SEO verileri başarıyla kaydedildi', [
                'seoSettingId' => $seoSetting->id ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('❌ UniversalSeoTab - SEO kaydetme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function render()
    {
        return view('seomanagement::admin.livewire.universal-seo-tab-component', [
            'seoDataCache' => $this->seoDataCache,
            'currentLanguage' => $this->currentLanguage,
            'availableLanguages' => $this->availableLanguages,
            'staticAiRecommendations' => $this->staticAiRecommendations,
            'dynamicAiRecommendations' => $this->dynamicAiRecommendations,
            'staticAiAnalysis' => $this->staticAiAnalysis,
            'dynamicAiAnalysis' => $this->dynamicAiAnalysis,
            'recommendationLoaders' => $this->recommendationLoaders,
        ]);
    }
}