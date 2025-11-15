<?php

namespace Modules\SeoManagement\App\Http\Livewire\Admin;

use Spatie\MediaLibrary\HasMedia;

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
    public bool $supportsSeoOgMedia = false;

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
        'announcement-saved' => 'saveSeoData',
        'blog-saved' => 'saveSeoData',
        'portfolio-saved' => 'saveSeoData',
    ];

    public function mount($modelId = null, $modelType = 'page', $modelClass = null)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;
        $this->modelClass = $modelClass;
        $this->supportsSeoOgMedia = $this->determineSeoOgMediaSupport();

        Log::info('🎬 UniversalSeoTabComponent MOUNT', [
            'modelId' => $modelId,
            'modelType' => $modelType,
            'modelClass' => $modelClass
        ]);

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
                $schemaType = is_string($seoSetting->schema_type) ? json_decode($seoSetting->schema_type, true) : $seoSetting->schema_type;
                $priorityScore = $seoSetting->priority_score ?? 5; // INTEGER - tek değer, JSON değil
                $ogTitle = is_string($seoSetting->og_titles) ? json_decode($seoSetting->og_titles, true) : $seoSetting->og_titles;
                $ogDescription = is_string($seoSetting->og_descriptions) ? json_decode($seoSetting->og_descriptions, true) : $seoSetting->og_descriptions;
                $ogImageUrl = is_string($seoSetting->og_images) ? json_decode($seoSetting->og_images, true) : $seoSetting->og_images;

                // Author & Author URL - SINGLE VALUE (NOT JSON)
                $author = $seoSetting->author; // VARCHAR - tek değer
                $authorUrl = $seoSetting->author_url; // VARCHAR - tek değer
                $authorTitle = $seoSetting->author_title; // VARCHAR - tek değer
                $authorBio = $seoSetting->author_bio; // TEXT - tek değer
                $authorImage = $seoSetting->author_image; // VARCHAR - tek değer

                $aiSuggestions = is_string($seoSetting->ai_suggestions) ? json_decode($seoSetting->ai_suggestions, true) : $seoSetting->ai_suggestions;

                Log::info('📦 UniversalSeoTab - JSON decode tamamlandı', [
                    'seoTitle' => $seoTitle,
                    'seoDescription' => $seoDescription,
                    'schemaType' => $schemaType,
                    'priorityScore' => $priorityScore
                ]);

                // Her dil için cache'e aktar
                foreach ($this->availableLanguages as $lang) {
                    // Robots meta fallback - her zaman true (belirtilmemişse)
                    $robotsMeta = $seoSetting->robots_meta ?? [];

                    $this->seoDataCache[$lang] = [
                        'seo_title' => $seoTitle[$lang] ?? '',
                        'seo_description' => $seoDescription[$lang] ?? '',
                        'schema_type' => $schemaType[$lang] ?? 'WebPage', // Fallback: WebPage
                        'priority_score' => $priorityScore, // INTEGER - tüm dillerde aynı
                        'og_title' => $ogTitle[$lang] ?? '',
                        'og_description' => $ogDescription[$lang] ?? '',
                        'og_image_url' => $ogImageUrl[$lang] ?? '',
                        'og_custom_enabled' => !empty(trim($ogTitle[$lang] ?? '')) || !empty(trim($ogDescription[$lang] ?? '')),
                        // Author - SINGLE VALUE (tüm dillerde aynı)
                        'author_name' => $author ?? '',
                        'author_url' => $authorUrl ?? '',
                        'author_title' => $authorTitle ?? '',
                        'author_bio' => $authorBio ?? '',
                        'author_image' => $authorImage ?? '',
                        // Robots Meta - Fallback her zaman TRUE
                        'robots_meta' => [
                            'index' => $robotsMeta['index'] ?? true,
                            'follow' => $robotsMeta['follow'] ?? true,
                            'archive' => $robotsMeta['archive'] ?? true,
                            'snippet' => $robotsMeta['snippet'] ?? true,
                        ],
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
                Log::warning('⚠️ UniversalSeoTab - SEO Setting BULUNAMADI, fallback değerleri çekiliyor');

                // SEO Setting yoksa modelden fallback değerleri çek
                try {
                    $model = $this->modelClass::find($this->modelId);

                    if ($model) {
                        Log::info('✅ UniversalSeoTab - Model bulundu, fallback metodları çağrılıyor');

                        foreach ($this->availableLanguages as $lang) {
                            // Geçici olarak app locale'i değiştir
                            $previousLocale = app()->getLocale();
                            app()->setLocale($lang);

                            // Fallback değerleri çek
                            $fallbackTitle = '';
                            $fallbackDescription = '';

                            if (method_exists($model, 'getSeoFallbackTitle')) {
                                $fallbackTitle = $model->getSeoFallbackTitle() ?? '';
                            }

                            if (method_exists($model, 'getSeoFallbackDescription')) {
                                $fallbackDescription = $model->getSeoFallbackDescription() ?? '';
                            }

                            // Locale'i geri al
                            app()->setLocale($previousLocale);

                            $this->seoDataCache[$lang] = $this->getEmptySeoData();
                            $this->seoDataCache[$lang]['seo_title'] = $fallbackTitle;
                            $this->seoDataCache[$lang]['seo_description'] = $fallbackDescription;
                            $this->recommendationLoaders[$lang] = false;

                            Log::info("✅ UniversalSeoTab - Fallback değerleri cache'e eklendi: {$lang}", [
                                'title' => $fallbackTitle,
                                'description' => mb_substr($fallbackDescription, 0, 50) . '...'
                            ]);
                        }
                    } else {
                        Log::warning('⚠️ UniversalSeoTab - Model bulunamadı, tamamen boş cache oluşturuluyor');
                        foreach ($this->availableLanguages as $lang) {
                            $this->seoDataCache[$lang] = $this->getEmptySeoData();
                            $this->recommendationLoaders[$lang] = false;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('❌ UniversalSeoTab - Fallback değerleri çekilemedi', [
                        'error' => $e->getMessage()
                    ]);

                    // Hata durumunda boş cache
                    foreach ($this->availableLanguages as $lang) {
                        $this->seoDataCache[$lang] = $this->getEmptySeoData();
                        $this->recommendationLoaders[$lang] = false;
                    }
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

        $this->syncOgImageFromMedia();
    }

    /**
     * Boş SEO data array döndür
     */
    private function getEmptySeoData(): array
    {
        return [
            'seo_title' => '',
            'seo_description' => '',
            'schema_type' => 'WebPage', // Fallback default
            'priority_score' => 5,
            'og_title' => '',
            'og_description' => '',
            'og_image_url' => '',
            'og_custom_enabled' => false,
            // Author - SINGLE VALUE (not per language)
            'author_name' => '',
            'author_url' => '',
            'author_title' => '',
            'author_bio' => '',
            'author_image' => '',
            // Robots Meta - Fallback her zaman TRUE
            'robots_meta' => [
                'index' => true,
                'follow' => true,
                'archive' => true,
                'snippet' => true,
            ],
        ];
    }

    /**
     * Dil değişikliğini handle et
     */
    #[On('seo-og-image-updated')]
    public function handleOgImageUpdated($payload)
    {
        $data = is_array($payload) ? $payload : [];

        if (!$this->supportsSeoOgMedia) {
            return;
        }

        $eventModelType = $data['model_type'] ?? null;
        if ($eventModelType && $eventModelType !== $this->modelType) {
            return;
        }

        $eventModelId = $data['model_id'] ?? null;
        if ($eventModelId && $this->modelId && (int) $eventModelId !== (int) $this->modelId) {
            return;
        }

        $url = $data['url'] ?? '';

        foreach ($this->availableLanguages as $lang) {
            if (!isset($this->seoDataCache[$lang])) {
                $this->seoDataCache[$lang] = $this->getEmptySeoData();
            }
            $this->seoDataCache[$lang]['og_image_url'] = $url;
        }
    }

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

    protected function syncOgImageFromMedia(): void
    {
        if (!$this->supportsSeoOgMedia) {
            return;
        }

        $mediaUrl = $this->getOgImageUrlFromMedia();
        if (!$mediaUrl) {
            return;
        }

        foreach ($this->availableLanguages as $lang) {
            if (!isset($this->seoDataCache[$lang])) {
                $this->seoDataCache[$lang] = $this->getEmptySeoData();
            }
            $this->seoDataCache[$lang]['og_image_url'] = $mediaUrl;
        }
    }

    protected function getOgImageUrlFromMedia(): ?string
    {
        if (!$this->modelId || !$this->modelClass || !class_exists($this->modelClass) || !$this->supportsSeoOgMedia) {
            return null;
        }

        try {
            $model = $this->modelClass::find($this->modelId);
            if (!$model || !method_exists($model, 'getFirstMediaUrl')) {
                return null;
            }

            $url = $model->getFirstMediaUrl('seo_og_image');
            return $url ?: null;
        } catch (\Exception $e) {
            Log::warning('UniversalSeoTabComponent getOgImageUrlFromMedia failed', [
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function determineSeoOgMediaSupport(): bool
    {
        if (!$this->modelClass || !class_exists($this->modelClass)) {
            return false;
        }

        return is_subclass_of($this->modelClass, HasMedia::class);
    }


/**
     * SEO verilerini kaydet
     * Parent component save olduğunda tetiklenir
     */
    public function saveSeoData($modelId = null)
    {
        Log::info('🎯 UniversalSeoTab - saveSeoData ÇAĞRILDI!', [
            'received_modelId' => $modelId,
            'current_modelId' => $this->modelId,
            'seoDataCache' => $this->seoDataCache
        ]);

        // Yeni oluşturulan model ID'sini güncelle
        if ($modelId) {
            $this->modelId = $modelId;
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
            $ogTitle = [];
            $ogDescription = [];
            $ogImageUrl = [];

            foreach ($this->availableLanguages as $lang) {
                $langData = $this->seoDataCache[$lang] ?? $this->getEmptySeoData();

                $seoTitle[$lang] = $langData['seo_title'] ?? '';
                $seoDescription[$lang] = $langData['seo_description'] ?? '';
                $schemaType[$lang] = $langData['schema_type'] ?? 'WebPage'; // Fallback: WebPage
                $ogTitle[$lang] = $langData['og_title'] ?? '';
                $ogDescription[$lang] = $langData['og_description'] ?? '';
                $ogImageUrl[$lang] = $langData['og_image_url'] ?? '';
            }

            // Priority Score - INTEGER tek değer (default language'den al)
            $defaultLocale = get_tenant_default_locale();
            $defaultData = $this->seoDataCache[$defaultLocale] ?? $this->getEmptySeoData();
            $priorityScore = $defaultData['priority_score'] ?? 5;

            // Author & Author URL - SINGLE VALUE (default language'den al)
            $defaultLocale = get_tenant_default_locale();
            $defaultData = $this->seoDataCache[$defaultLocale] ?? $this->getEmptySeoData();
            $author = $defaultData['author_name'] ?? '';
            $authorUrl = $defaultData['author_url'] ?? '';
            $authorTitle = $defaultData['author_title'] ?? '';
            $authorBio = $defaultData['author_bio'] ?? '';
            $authorImage = $defaultData['author_image'] ?? '';

            // AI Analysis verilerini hazırla
            $strengths = null;
            $improvements = null;
            $actionItems = null;

            // staticAiAnalysis veya dynamicAiAnalysis'ten verileri al
            foreach ($this->availableLanguages as $lang) {
                if (!empty($this->staticAiAnalysis[$lang])) {
                    $analysis = $this->staticAiAnalysis[$lang];

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
                    'schema_type' => $schemaType,
                    'priority_score' => $priorityScore,
                    'og_titles' => $ogTitle,
                    'og_descriptions' => $ogDescription,
                    'og_images' => $ogImageUrl,
                    'author' => $author,
                    'author_url' => $authorUrl,
                    'author_title' => $authorTitle,
                    'author_bio' => $authorBio,
                    'author_image' => $authorImage,
                    'ai_suggestions' => $this->staticAiRecommendations ?: null,
                    // AI Analysis Results
                    'analysis_results' => !empty($this->staticAiAnalysis) ? $this->staticAiAnalysis : null,
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