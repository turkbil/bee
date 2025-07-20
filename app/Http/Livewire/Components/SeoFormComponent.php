<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use App\Models\SeoSetting;
use App\Services\SeoLanguageManager;
use App\Services\AI\SeoAnalysisService;
use Modules\LanguageManagement\App\Models\TenantLanguage;

class SeoFormComponent extends Component
{
    public $model;
    public $availableLanguages = [];
    public $currentLanguage = 'tr';
    
    // SEO data
    public $seoData = [];
    public $slugData = [];
    public $newKeyword = '';
    public $aiAnalysis = null;
    
    public function mount($model)
    {
        $this->model = $model;
        $this->loadAvailableLanguages();
        $this->loadSeoData();
        $this->loadSlugData();
        $this->initializeSeoData();
    }
    
    protected function loadAvailableLanguages()
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
            
        if (empty($this->availableLanguages)) {
            $this->availableLanguages = ['tr'];
        }
        
        // Set default current language
        $defaultLang = session('site_default_language', 'tr');
        $this->currentLanguage = in_array($defaultLang, $this->availableLanguages) 
            ? $defaultLang 
            : $this->availableLanguages[0];
    }
    
    protected function loadSeoData()
    {
        if (!$this->model || !$this->model->exists) {
            \Log::info('🔍 SEO loadSeoData: Model yok veya mevcut değil');
            return;
        }
        
        $seoSettings = $this->model->seoSetting;
        
        \Log::info('🔍 SEO loadSeoData başlangıç', [
            'model_type' => get_class($this->model),
            'model_id' => $this->model->getKey(),
            'current_language' => $this->currentLanguage,
            'available_languages' => $this->availableLanguages,
            'has_seo_settings' => $seoSettings ? true : false
        ]);
        
        if ($seoSettings) {
            // Get current language data from multi-language fields using fallback
            $currentLang = $this->currentLanguage;
            $titles = $seoSettings->titles ?? [];
            $descriptions = $seoSettings->descriptions ?? [];
            $keywords = $seoSettings->keywords ?? [];
            
            \Log::info('🔍 SEO Raw data from database', [
                'current_lang' => $currentLang,
                'titles_raw' => $titles,
                'descriptions_raw' => $descriptions,
                'keywords_raw' => $keywords,
                'titles_type' => gettype($titles),
                'descriptions_type' => gettype($descriptions),
                'keywords_type' => gettype($keywords)
            ]);
            
            // Use HasTranslations pattern - fallback to tenant default, then tr, then first available
            $title = '';
            $description = '';
            $keywordList = [];
            
            if (is_array($titles)) {
                $title = $titles[$currentLang] ?? $this->getFallbackFromArray($titles, $currentLang);
                \Log::info('🔍 Title processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $titles[$currentLang] ?? 'YOK',
                    'final_title' => $title
                ]);
            }
            
            if (is_array($descriptions)) {
                $description = $descriptions[$currentLang] ?? $this->getFallbackFromArray($descriptions, $currentLang);
                \Log::info('🔍 Description processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $descriptions[$currentLang] ?? 'YOK',
                    'final_description' => $description
                ]);
            }
            
            if (is_array($keywords)) {
                $keywordList = $keywords[$currentLang] ?? $this->getFallbackFromArray($keywords, $currentLang, []);
                \Log::info('🔍 Keywords processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $keywords[$currentLang] ?? 'YOK',
                    'final_keywords' => $keywordList,
                    'final_keywords_type' => gettype($keywordList)
                ]);
            }
            
            // Focus keywords also multilingual
            $focusKeywords = $seoSettings->focus_keywords ?? [];
            $focusKeyword = '';
            if (is_array($focusKeywords)) {
                $focusKeyword = $focusKeywords[$currentLang] ?? $this->getFallbackFromArray($focusKeywords, $currentLang, '');
                \Log::info('🔍 Focus keyword processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $focusKeywords[$currentLang] ?? 'YOK',
                    'final_focus_keyword' => $focusKeyword
                ]);
            } else {
                // Fallback to old single focus_keyword
                $focusKeyword = $seoSettings->focus_keyword ?? '';
            }
            
            // OG fields also multilingual
            $ogTitles = $seoSettings->og_title ?? [];
            $ogDescriptions = $seoSettings->og_description ?? [];
            
            $ogTitle = '';
            $ogDescription = '';
            
            if (is_array($ogTitles)) {
                $ogTitle = $ogTitles[$currentLang] ?? $this->getFallbackFromArray($ogTitles, $currentLang, '');
                \Log::info('🔍 OG Title processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $ogTitles[$currentLang] ?? 'YOK',
                    'final_og_title' => $ogTitle
                ]);
            } else {
                // Fallback to old single og_title
                $ogTitle = $seoSettings->og_title ?? '';
            }
            
            if (is_array($ogDescriptions)) {
                $ogDescription = $ogDescriptions[$currentLang] ?? $this->getFallbackFromArray($ogDescriptions, $currentLang, '');
                \Log::info('🔍 OG Description processing', [
                    'requested_lang' => $currentLang,
                    'direct_match' => $ogDescriptions[$currentLang] ?? 'YOK',
                    'final_og_description' => $ogDescription
                ]);
            } else {
                // Fallback to old single og_description
                $ogDescription = $seoSettings->og_description ?? '';
            }
            
            // Robots meta JSON alanından verileri çek
            $robotsMeta = $seoSettings->robots_meta ?? [];
            
            $this->seoData = [
                'title' => $title,
                'description' => $description,
                'keywords' => is_array($keywordList) ? $keywordList : [],
                'focus_keyword' => $focusKeyword,
                'canonical_url' => $seoSettings->canonical_url ?? '',
                'robots_index' => $robotsMeta['index'] ?? true,
                'robots_follow' => $robotsMeta['follow'] ?? true,
                'robots_archive' => $robotsMeta['archive'] ?? true,
                'auto_optimize' => $seoSettings->auto_optimize ?? false,
                'og_title' => $ogTitle,
                'og_description' => $ogDescription,
                'og_image' => $seoSettings->og_image ?? '',
                'og_type' => $seoSettings->og_type ?? 'website',
                'twitter_card' => $seoSettings->twitter_card ?? 'summary',
                'twitter_site' => $seoSettings->twitter_site ?? '',
            ];
            
            \Log::info('🔍 Final SEO Data loaded', [
                'seo_data_title' => $this->seoData['title'],
                'seo_data_description' => $this->seoData['description'],
                'seo_data_keywords' => $this->seoData['keywords'],
                'seo_data_keywords_count' => count($this->seoData['keywords'])
            ]);
            
        } else {
            \Log::info('🔍 SEO Settings yok, boş data initialize ediliyor');
            // Initialize empty data for current language
            $this->initializeSeoData();
        }
    }
    
    protected function loadSlugData()
    {
        if (!$this->model || !$this->model->exists) {
            \Log::info('🔍 SLUG loadSlugData: Model yok, boş slug data initialize ediliyor');
            // Initialize empty slug data for all languages
            foreach ($this->availableLanguages as $lang) {
                $this->slugData[$lang] = '';
            }
            return;
        }
        
        \Log::info('🔍 SLUG loadSlugData başlangıç', [
            'model_type' => get_class($this->model),
            'model_id' => $this->model->getKey(),
            'current_language' => $this->currentLanguage,
            'available_languages' => $this->availableLanguages,
            'has_getTranslated_method' => method_exists($this->model, 'getTranslated')
        ]);
        
        // Page model'den slug verilerini al - HasTranslations trait kullanıyor
        if (method_exists($this->model, 'getTranslated')) {
            \Log::info('🔍 SLUG getTranslated metodu kullanılıyor');
            foreach ($this->availableLanguages as $lang) {
                $slugValue = $this->model->getTranslated('slug', $lang) ?? '';
                $this->slugData[$lang] = $slugValue;
                \Log::info('🔍 SLUG getTranslated result', [
                    'language' => $lang,
                    'slug_value' => $slugValue
                ]);
            }
        } else {
            // Model'de slug array olarak cast edilmiş
            $slugs = $this->model->slug ?? [];
            \Log::info('🔍 SLUG Direct array access', [
                'slugs_raw' => $slugs,
                'slugs_type' => gettype($slugs)
            ]);
            
            if (is_array($slugs)) {
                foreach ($this->availableLanguages as $lang) {
                    $slugValue = $slugs[$lang] ?? '';
                    $this->slugData[$lang] = $slugValue;
                    \Log::info('🔍 SLUG array access result', [
                        'language' => $lang,
                        'slug_value' => $slugValue
                    ]);
                }
            } else {
                // Tek dilli ise varsayılan dile ata
                $this->slugData[$this->currentLanguage] = $slugs ?? '';
                \Log::info('🔍 SLUG single language fallback', [
                    'current_language' => $this->currentLanguage,
                    'slug_value' => $slugs ?? ''
                ]);
            }
        }
        
        \Log::info('🔍 Final SLUG Data loaded', [
            'slug_data' => $this->slugData
        ]);
    }
    
    protected function initializeSeoData()
    {
        if (empty($this->seoData)) {
            $this->seoData = [
                'title' => '',
                'description' => '',
                'keywords' => [],
                'focus_keyword' => '',
                'canonical_url' => '',
                'robots_index' => true,
                'robots_follow' => true,
                'robots_archive' => true,
                'auto_optimize' => false,
                'og_title' => '',
                'og_description' => '',
                'og_image' => '',
                'og_type' => 'website',
                'twitter_card' => 'summary',
                'twitter_site' => '',
            ];
        }
        
        // Initialize new keyword string  
        if (!isset($this->newKeyword)) {
            $this->newKeyword = '';
        }
    }
    
    /**
     * Fallback sistemi - Page modeli HasTranslations trait'i ile aynı mantık
     */
    private function getFallbackFromArray(array $translations, string $requestedLocale, $default = '')
    {
        \Log::info('🔄 Fallback sistemi başlatıldı', [
            'requested_locale' => $requestedLocale,
            'available_translations' => array_keys($translations),
            'default_fallback' => $default
        ]);
        
        // 1. İstenen dil varsa döndür
        if (isset($translations[$requestedLocale]) && !empty($translations[$requestedLocale])) {
            \Log::info('✅ Direct match bulundu', [
                'locale' => $requestedLocale,
                'value' => $translations[$requestedLocale]
            ]);
            return $translations[$requestedLocale];
        }
        
        // 2. Tenant varsayılan dilini bul ve kullan
        $defaultLocale = $this->getTenantDefaultLanguage();
        if (isset($translations[$defaultLocale]) && !empty($translations[$defaultLocale])) {
            \Log::info('✅ Tenant default match bulundu', [
                'tenant_default_locale' => $defaultLocale,
                'value' => $translations[$defaultLocale]
            ]);
            return $translations[$defaultLocale];
        }
        
        // 3. Sistem varsayılanı (tr) varsa döndür
        if ($defaultLocale !== 'tr' && isset($translations['tr']) && !empty($translations['tr'])) {
            \Log::info('✅ System default (tr) match bulundu', [
                'value' => $translations['tr']
            ]);
            return $translations['tr'];
        }
        
        // 4. İlk dolu dili bul
        foreach ($translations as $locale => $content) {
            if (!empty($content)) {
                \Log::info('✅ First available match bulundu', [
                    'locale' => $locale,
                    'value' => $content
                ]);
                return $content;
            }
        }
        
        // 5. Hiçbiri yoksa default değer
        \Log::warning('⚠️ Hiçbir çeviri bulunamadı, default değer döndürülüyor', [
            'default' => $default
        ]);
        return $default;
    }
    
    /**
     * Tenant varsayılan dilini al - HasTranslations trait'i ile aynı mantık
     */
    private function getTenantDefaultLanguage(): string
    {
        try {
            // Tenant'tan varsayılan dili al
            if (function_exists('tenant') && tenant()) {
                $currentTenant = tenant();
                
                // Tenant'ın tenant_default_locale alanı varsa onu kullan
                if (isset($currentTenant->tenant_default_locale) && !empty($currentTenant->tenant_default_locale)) {
                    return $currentTenant->tenant_default_locale;
                }
            }
            
            // Tenant yoksa veya tenant_default_locale yoksa sistem varsayılanı
            return config('app.locale', 'tr');
            
        } catch (\Exception $e) {
            // Hata durumunda sistem varsayılanı
            return config('app.locale', 'tr');
        }
    }
    
    public function switchLanguage($language)
    {
        \Log::info('🔄 SEO switchLanguage çağrıldı', [
            'old_language' => $this->currentLanguage,
            'new_language' => $language,
            'available_languages' => $this->availableLanguages,
            'is_valid_language' => in_array($language, $this->availableLanguages)
        ]);
        
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
            
            \Log::info('🔄 Dil değişti, veriler yeniden yükleniyor', [
                'current_language' => $this->currentLanguage
            ]);
            
            // Dil değiştiğinde SEO ve slug verilerini yeniden yükle
            $this->loadSeoData();
            $this->loadSlugData();
            
            // Force Livewire component refresh for choices.js update
            $this->dispatch('seo-language-switched', [
                'language' => $language,
                'keywords' => $this->seoData['keywords'] ?? []
            ]);
            
            // Force re-render to update choices.js
            $this->skipRender = false;
            
            \Log::info('🔄 Dil değişimi tamamlandı', [
                'current_language' => $this->currentLanguage,
                'seo_title' => $this->seoData['title'] ?? 'YOK',
                'seo_description' => $this->seoData['description'] ?? 'YOK',
                'seo_keywords_count' => count($this->seoData['keywords'] ?? []),
                'seo_keywords_detail' => $this->seoData['keywords'] ?? [],
                'slug_data' => $this->slugData,
                'event_sent' => [
                    'language' => $language,
                    'keywords' => $this->seoData['keywords'] ?? []
                ]
            ]);
        } else {
            \Log::warning('⚠️ Geçersiz dil seçimi', [
                'requested_language' => $language,
                'available_languages' => $this->availableLanguages
            ]);
        }
    }
    
    public function addKeyword()
    {
        $keyword = trim($this->newKeyword);
        
        if (empty($keyword)) {
            return;
        }
        
        if (!isset($this->seoData['keywords'])) {
            $this->seoData['keywords'] = [];
        }
        
        if (!in_array($keyword, $this->seoData['keywords'])) {
            $this->seoData['keywords'][] = $keyword;
        }
        
        $this->newKeyword = '';
    }
    
    public function removeKeyword($index)
    {
        if (isset($this->seoData['keywords'][$index])) {
            unset($this->seoData['keywords'][$index]);
            $this->seoData['keywords'] = array_values($this->seoData['keywords']);
        }
    }
    
    public function analyzeSeo()
    {
        if (!$this->model || !$this->model->exists) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Önce kaydı kaydedin',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $seoAnalysisService = app(SeoAnalysisService::class);
            $this->aiAnalysis = $seoAnalysisService->analyzeSeoContent($this->model, $this->currentLanguage);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'SEO analizi tamamlandı',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'SEO analizi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function generateSeoSuggestions()
    {
        if (!$this->model || !$this->model->exists) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Önce kaydı kaydedin',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $seoAnalysisService = app(SeoAnalysisService::class);
            // Use fast AI analysis with reduced content
            $suggestions = $seoAnalysisService->generateQuickSuggestions($this->model, $this->currentLanguage);
            $this->aiAnalysis = $suggestions;
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'AI önerileri oluşturuldu',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Öneri oluşturma başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function autoOptimizeSeo()
    {
        if (!$this->model || !$this->model->exists) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Önce kaydı kaydedin',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            $seoAnalysisService = app(SeoAnalysisService::class);
            $seoAnalysisService->autoOptimizeSeo($this->model, $this->currentLanguage);
            
            // SEO verilerini yeniden yükle
            $this->loadSeoData();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'SEO otomatik optimizasyonu tamamlandı',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Otomatik optimizasyon başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function applySuggestion($type, $value)
    {
        $language = $this->currentLanguage;
        
        if ($type === 'title') {
            $this->seoData['titles'][$language] = $value;
        } elseif ($type === 'description') {
            $this->seoData['descriptions'][$language] = $value;
        }
        
        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Öneri uygulandı',
            'type' => 'success'
        ]);
    }
    
    /**
     * SEO verilerini kaydet
     */
    public function saveSeoData()
    {
        if (!$this->model || !$this->model->exists) {
            return false;
        }
        
        try {
            $seoSettings = $this->model->seoSetting ?? new SeoSetting();
            
            // Mevcut çok dilli verileri al
            $currentTitles = $seoSettings->titles ?? [];
            $currentDescriptions = $seoSettings->descriptions ?? [];
            $currentKeywords = $seoSettings->keywords ?? [];
            $currentFocusKeywords = $seoSettings->focus_keywords ?? [];
            $currentOgTitles = $seoSettings->og_title ?? [];
            $currentOgDescriptions = $seoSettings->og_description ?? [];
            
            // Şu anki dil için verileri güncelle
            $currentTitles[$this->currentLanguage] = $this->seoData['title'] ?? '';
            $currentDescriptions[$this->currentLanguage] = $this->seoData['description'] ?? '';
            $currentKeywords[$this->currentLanguage] = $this->seoData['keywords'] ?? [];
            $currentFocusKeywords[$this->currentLanguage] = $this->seoData['focus_keyword'] ?? '';
            $currentOgTitles[$this->currentLanguage] = $this->seoData['og_title'] ?? '';
            $currentOgDescriptions[$this->currentLanguage] = $this->seoData['og_description'] ?? '';
            
            // Robots meta JSON formatında hazırla
            $robotsMeta = [
                'index' => $this->seoData['robots_index'] ?? true,
                'follow' => $this->seoData['robots_follow'] ?? true,
                'archive' => $this->seoData['robots_archive'] ?? true,
            ];
            
            $seoSettings->fill([
                'seoable_id' => $this->model->getKey(),
                'seoable_type' => get_class($this->model),
                'titles' => $currentTitles,
                'descriptions' => $currentDescriptions,
                'keywords' => $currentKeywords,
                'focus_keyword' => $this->seoData['focus_keyword'] ?? '', // Backward compatibility
                'focus_keywords' => $currentFocusKeywords,
                'canonical_url' => $this->seoData['canonical_url'] ?? '',
                'robots_meta' => $robotsMeta,
                'auto_optimize' => $this->seoData['auto_optimize'] ?? false,
                'og_title' => $currentOgTitles,
                'og_description' => $currentOgDescriptions,
                'og_image' => $this->seoData['og_image'] ?? '',
                'og_type' => $this->seoData['og_type'] ?? 'website',
                'twitter_card' => $this->seoData['twitter_card'] ?? 'summary',
                'twitter_site' => $this->seoData['twitter_site'] ?? '',
            ]);
            
            $seoSettings->save();
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('SEO kaydetme hatası: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Parent component'ten çağrılacak
     */
    public function getSeoDataForParent()
    {
        return $this->seoData;
    }
    
    public function render()
    {
        return view('admin.components.seo-form', [
            'model' => $this->model,
            'languages' => $this->availableLanguages,
            'currentLanguage' => $this->currentLanguage,
            'seoData' => $this->seoData,
            'newKeyword' => $this->newKeyword,
            'aiAnalysis' => $this->aiAnalysis
        ]);
    }
}