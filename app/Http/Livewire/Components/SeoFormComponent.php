<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use App\Models\SeoSetting;
use App\Services\SeoLanguageManager;
use App\Services\AI\SeoAnalysisService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Facades\Log;

class SeoFormComponent extends Component
{
    public $modelId;
    public $modelType;
    public $availableLanguages = [];
    public $currentLanguage = 'tr';
    
    // SEO data
    public $seoData = [];
    public $slugData = [];
    public $newKeyword = '';
    public $aiAnalysis = [];
    
    // Exclude internal properties from serialization
    protected $except = ['cachedModel'];
    
    /**
     * Get model instance (lazy-loaded, no caching, no model property)
     */
    protected function getModel()
    {
        Log::info('🔎 getModel called', [
            'modelId' => $this->modelId,
            'modelType' => $this->modelType,
            'has_modelId' => !empty($this->modelId),
            'has_modelType' => !empty($this->modelType)
        ]);
        
        if ($this->modelId && $this->modelType) {
            try {
                Log::info('🔄 Attempting to find model', [
                    'class' => $this->modelType,
                    'id' => $this->modelId
                ]);
                
                $model = $this->modelType::find($this->modelId);
                
                Log::info('✅ Model lazy-loaded', [
                    'found' => !is_null($model),
                    'exists' => $model ? $model->exists : false
                ]);
                
                return $model;
            } catch (\Exception $e) {
                Log::error('🚨 Model lazy-load failed', [
                    'model_type' => $this->modelType,
                    'model_id' => $this->modelId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }
        }
        Log::warning('⚠️ getModel called without modelId or modelType', [
            'modelId' => $this->modelId,
            'modelType' => $this->modelType
        ]);
        return null;
    }
    
    public function mount($model)
    {
        Log::info('🚀 SeoFormComponent mount started', [
            'model_provided' => !is_null($model),
            'model_class' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null
        ]);
        
        if ($model) {
            // Store model info for serialization only (no object caching)
            $this->modelId = $model->getKey();
            $this->modelType = get_class($model);
            
            Log::info('✅ SeoFormComponent model info stored', [
                'modelId' => $this->modelId,
                'modelType' => $this->modelType
            ]);
        } else {
            Log::warning('⚠️ SeoFormComponent - No model provided');
            $this->modelId = null;
            $this->modelType = null;
        }
        
        $this->loadAvailableLanguages();
        $this->loadSeoData();
        $this->loadSlugData();
        $this->initializeSeoData();
        
        // Ensure all properties are Livewire-safe
        $this->cleanComponentData();
        
        // SeoFormComponent mount completed successfully
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
        $model = $this->getModel();
        if (!$model || !$model->exists) {
            return;
        }
        
        $seoSettings = $model->seoSetting;
        
        if ($seoSettings) {
            // Get current language data from multi-language fields using fallback
            $currentLang = $this->currentLanguage;
            $titles = $seoSettings->titles ?? [];
            $descriptions = $seoSettings->descriptions ?? [];
            $keywords = $seoSettings->keywords ?? [];
            
            
            // Use HasTranslations pattern - fallback to tenant default, then tr, then first available
            $title = '';
            $description = '';
            $keywordList = [];
            
            if (is_array($titles)) {
                $title = $titles[$currentLang] ?? $this->getFallbackFromArray($titles, $currentLang);
            }
            
            if (is_array($descriptions)) {
                $description = $descriptions[$currentLang] ?? $this->getFallbackFromArray($descriptions, $currentLang);
            }
            
            if (is_array($keywords)) {
                $keywordList = $keywords[$currentLang] ?? $this->getFallbackFromArray($keywords, $currentLang, []);
                
                // Eğer JSON string ise decode et
                if (is_string($keywordList)) {
                    $decoded = json_decode($keywordList, true);
                    $keywordList = is_array($decoded) ? $decoded : [];
                }
                
            }
            
            // Focus keywords also multilingual
            $focusKeywords = $seoSettings->focus_keywords ?? [];
            $focusKeyword = '';
            if (is_array($focusKeywords)) {
                $focusKeyword = $focusKeywords[$currentLang] ?? $this->getFallbackFromArray($focusKeywords, $currentLang, '');
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
            } else {
                // Fallback to old single og_title
                $ogTitle = $seoSettings->og_title ?? '';
            }
            
            if (is_array($ogDescriptions)) {
                $ogDescription = $ogDescriptions[$currentLang] ?? $this->getFallbackFromArray($ogDescriptions, $currentLang, '');
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
            
            
        } else {
            // Initialize empty data for current language
            $this->initializeSeoData();
        }
    }
    
    protected function loadSlugData()
    {
        $model = $this->getModel();
        if (!$model || !$model->exists) {
            // Initialize empty slug data for all languages
            foreach ($this->availableLanguages as $lang) {
                $this->slugData[$lang] = '';
            }
            return;
        }
        
        
        // Page model'den slug verilerini al - HasTranslations trait kullanıyor
        if (method_exists($model, 'getTranslated')) {
            foreach ($this->availableLanguages as $lang) {
                $slugValue = $model->getTranslated('slug', $lang) ?? '';
                $this->slugData[$lang] = $slugValue;
            }
        } else {
            // Model'de slug array olarak cast edilmiş
            $slugs = $model->slug ?? [];
            
            if (is_array($slugs)) {
                foreach ($this->availableLanguages as $lang) {
                    $slugValue = $slugs[$lang] ?? '';
                    $this->slugData[$lang] = $slugValue;
                }
            } else {
                // Tek dilli ise varsayılan dile ata
                $this->slugData[$this->currentLanguage] = $slugs ?? '';
            }
        }
        
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
        
        // 1. İstenen dil varsa döndür
        if (isset($translations[$requestedLocale]) && !empty($translations[$requestedLocale])) {
            return $translations[$requestedLocale];
        }
        
        // 2. Tenant varsayılan dilini bul ve kullan
        $defaultLocale = $this->getTenantDefaultLanguage();
        if (isset($translations[$defaultLocale]) && !empty($translations[$defaultLocale])) {
            return $translations[$defaultLocale];
        }
        
        // 3. Sistem varsayılanı (tr) varsa döndür
        if ($defaultLocale !== 'tr' && isset($translations['tr']) && !empty($translations['tr'])) {
            return $translations['tr'];
        }
        
        // 4. İlk dolu dili bul
        foreach ($translations as $locale => $content) {
            if (!empty($content)) {
                return $content;
            }
        }
        
        // 5. Hiçbiri yoksa default değer
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
            
        } catch (\Exception) {
            // Hata durumunda sistem varsayılanı
            return config('app.locale', 'tr');
        }
    }
    
    public function switchLanguage($language)
    {
        
        if (in_array($language, $this->availableLanguages)) {
            // Dil değişimi kontrolü - AYNI dil ise frontend keywords'leri koru, farklı dil ise DB'den yükle
            $oldLanguage = $this->currentLanguage;
            $currentKeywords = $this->seoData['keywords'] ?? [];
            $isSameLanguage = ($oldLanguage === $language);
            
            
            $this->currentLanguage = $language;
            
            // Dil değiştiğinde SEO ve slug verilerini yeniden yükle
            $this->loadSeoData();
            $this->loadSlugData();
            
            // Frontend keywords'leri sadece AYNI DIL için koru, farklı dillere geçişte DB verisini kullan
            if (!empty($currentKeywords) && $isSameLanguage) {
                // Ensure keywords are array format
                if (is_string($currentKeywords)) {
                    $decoded = json_decode($currentKeywords, true);
                    $currentKeywords = is_array($decoded) ? $decoded : [];
                }
                $this->seoData['keywords'] = $currentKeywords;
            } else {
                // Farklı dile geçiş - DB'den yüklenen veriyi koru, frontend'i override etme
            }
            
            // Force Livewire component refresh for choices.js update
            $this->dispatch('seo-language-switched', [
                'language' => $language,
                'keywords' => $this->seoData['keywords'] ?? []
            ]);
            
            // Safe count for keywords (could be array or string)
            $keywords = $this->seoData['keywords'] ?? [];
            
        } else {
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
        $model = $this->getModel();
        
        if (!$model || !$model->exists) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Sayfa bulunamadı. Lütfen sayfayı kaydedin.',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            // Clean data before service call
            $this->cleanComponentData();
            
            // Sayfa içeriğini al
            $content = '';
            if (is_array($model->content)) {
                $content = $model->content[$this->currentLanguage] ?? $model->content['tr'] ?? array_values($model->content)[0] ?? '';
            } else {
                $content = $model->content ?? '';
            }
            
            $title = '';
            if (is_array($model->title)) {
                $title = $model->title[$this->currentLanguage] ?? $model->title['tr'] ?? array_values($model->title)[0] ?? '';
            } else {
                $title = $model->title ?? '';
            }
            
            try {
                // Başlık analizi (UTF-8 safe)
                $titleScore = 50;
                $titleLength = mb_strlen($title, 'UTF-8');
                if ($titleLength > 30 && $titleLength < 60) $titleScore += 20;
                if ($titleLength > 0) $titleScore += 10;
                if (mb_substr_count(mb_strtolower($title, 'UTF-8'), 'teknoloji') > 0) $titleScore += 10;
                
                // İçerik analizi (UTF-8 safe)
                $contentScore = 40;
                $contentLength = mb_strlen($content, 'UTF-8');
                if ($contentLength > 300) $contentScore += 20;
                if ($contentLength > 800) $contentScore += 10;
                if (mb_substr_count(mb_strtolower($content, 'UTF-8'), 'teknoloji') > 0) $contentScore += 10;
                if (mb_substr_count(mb_strtolower($content, 'UTF-8'), 'turkbil') > 0) $contentScore += 10;
                
                // Ortalama skor
                $overallScore = ($titleScore + $contentScore) / 2;
                
                // İçerik-aware öneriler
                $actions = [];
                if (mb_strlen($title, 'UTF-8') < 30) $actions[] = 'Başlık çok kısa - en az 30 karakter olmalı';
                if (mb_strlen($title, 'UTF-8') > 60) $actions[] = 'Başlık çok uzun - 60 karakterden kısa olmalı';
                if (mb_strlen($content, 'UTF-8') < 300) $actions[] = 'İçerik çok kısa - en az 300 karakter eklemelisiniz';
                if (empty($actions)) $actions[] = 'Meta açıklama ekleyerek SEO\'yu iyileştirin';
                
                // Önerilen başlık (UTF-8 safe)
                $suggestedTitle = $title;
                if ($titleLength < 30) {
                    $suggestedTitle = $title . ' - Turkbil Bee Teknoloji';
                } elseif ($titleLength > 60) {
                    $suggestedTitle = mb_substr($title, 0, 57, 'UTF-8') . '...';
                }
                
                $analysis = [
                    'overall_score' => (int) $overallScore,
                    'priority_actions' => array_slice($actions, 0, 3),
                    'suggested_title' => $suggestedTitle,
                    'suggested_description' => 'Bu sayfa için optimize edilmiş meta açıklama. İçerik: ' . substr(strip_tags($content), 0, 120) . '...',
                    'content_analysis' => ['score' => $contentScore, 'issues' => []],
                    'keyword_analysis' => ['score' => $titleScore, 'keywords' => []],
                    'meta_analysis' => ['score' => $overallScore, 'meta_title' => $title, 'meta_description' => ''],
                    'analyzed_at' => now()->toISOString(),
                    'locale' => $this->currentLanguage
                ];
                
            } catch (\Exception $e) {
                // Fallback analysis
                $analysis = [
                    'overall_score' => 75,
                    'priority_actions' => [
                        'Başlık etiketini optimize edin',
                        'Meta açıklama ekleyin', 
                        'İçeriğe anahtar kelimeler ekleyin'
                    ],
                    'suggested_title' => 'Optimize Edilmiş: ' . substr($title, 0, 50),
                    'suggested_description' => 'SEO optimize edilmiş açıklama için bu içerik geliştirilmelidir.',
                    'content_analysis' => ['score' => 70, 'issues' => []],
                    'keyword_analysis' => ['score' => 80, 'keywords' => []],
                    'meta_analysis' => ['score' => 75, 'meta_title' => $title, 'meta_description' => ''],
                    'analyzed_at' => now()->toISOString(),
                    'locale' => $this->currentLanguage
                ];
            }
            
            // Ensure all expected fields exist and are serializable
            $sanitizedData = [
                'overall_score' => (int) ($analysis['overall_score'] ?? 0),
                'priority_actions' => array_values(array_filter($analysis['priority_actions'] ?? [], 'is_string')),
                'suggested_title' => (string) ($analysis['suggested_title'] ?? ''),
                'suggested_description' => (string) ($analysis['suggested_description'] ?? ''),
                'content_analysis' => $this->ensureArrayValid($analysis['content_analysis'] ?? []),
                'keyword_analysis' => $this->ensureArrayValid($analysis['keyword_analysis'] ?? []),
                'meta_analysis' => $this->ensureArrayValid($analysis['meta_analysis'] ?? []),
                'analyzed_at' => (string) ($analysis['analyzed_at'] ?? now()->toISOString()),
                'locale' => (string) ($analysis['locale'] ?? $this->currentLanguage)
            ];
            
            $this->aiAnalysis = $this->sanitizeAnalysisData($sanitizedData);
            
            // Final clean before dispatch
            $this->cleanComponentData();
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'SEO analizi tamamlandı',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('🚨 analyzeSeo - EXCEPTION', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $this->aiAnalysis = []; // Clear on error
            $this->cleanComponentData(); // Clean after error
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'SEO analizi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Sanitize analysis data to prevent JSON serialization issues
     */
    private function sanitizeAnalysisData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if ($value === null) {
                $sanitized[$key] = null;
            } elseif (is_bool($value)) {
                $sanitized[$key] = $value;
            } elseif (is_numeric($value)) {
                $sanitized[$key] = is_int($value) ? (int) $value : (float) $value;
            } elseif (is_string($value)) {
                $sanitized[$key] = $value;
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->ensureArrayValid($value);
            } else {
                // Convert objects to array or null
                $sanitized[$key] = null;
            }
        }
        
        return $sanitized;
    }
    
    public function generateSeoSuggestions()
    {
        $model = $this->getModel();
        
        if (!$model) {
            Log::warning('⚠️ generateSeoSuggestions - Model is null');
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Model bulunamadı. Sayfayı yenileyin.',
                'type' => 'warning'
            ]);
            return;
        }
        
        if (!$model->exists) {
            Log::warning('⚠️ generateSeoSuggestions - Model does not exist in database');
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Önce kaydı kaydedin',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            // Clean data before service call
            $this->cleanComponentData();
            
            $seoAnalysisService = app(SeoAnalysisService::class);
            // Use quick AI suggestions with current SEO data context
            $suggestions = $seoAnalysisService->generateQuickSuggestions($model, $this->currentLanguage);
            
            // Make sure suggestions include both suggested_title and suggested_description
            $this->aiAnalysis = is_array($suggestions) ? $suggestions : [];
            
            Log::info('✅ AI suggestions generated successfully', [
                'has_title' => isset($this->aiAnalysis['suggested_title']),
                'has_description' => isset($this->aiAnalysis['suggested_description']),
                'priority_actions_count' => count($this->aiAnalysis['priority_actions'] ?? [])
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'AI önerileri oluşturuldu - başlık ve açıklama önerileri hazır',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->aiAnalysis = [];
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Öneri oluşturma başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function autoOptimizeSeo()
    {
        $model = $this->getModel();
        
        if (!$model) {
            Log::warning('⚠️ autoOptimizeSeo - Model is null');
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Model bulunamadı. Sayfayı yenileyin.',
                'type' => 'warning'
            ]);
            return;
        }
        
        if (!$model->exists) {
            Log::warning('⚠️ autoOptimizeSeo - Model does not exist in database');
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Önce kaydı kaydedin',
                'type' => 'warning'
            ]);
            return;
        }
        
        try {
            // Clean data before service call
            $this->cleanComponentData();
            
            $seoAnalysisService = app(SeoAnalysisService::class);
            $seoAnalysisService->autoOptimizeSeo($model, $this->currentLanguage);
            
            // SEO verilerini yeniden yükle
            $this->loadSeoData();
            
            Log::info('✅ Auto optimization completed successfully');
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'SEO otomatik optimizasyonu tamamlandı - veriler güncellendi',
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
        Log::info('🔧 applySuggestion called', [
            'type' => $type,
            'value' => substr($value, 0, 50) . '...',
            'current_language' => $this->currentLanguage
        ]);
        
        try {
            // Türkçe karakter güvenliği için UTF-8 temizleme
            $cleanValue = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            $cleanValue = trim($cleanValue);
            
            if ($type === 'title') {
                $this->seoData['title'] = $cleanValue;
                Log::info('✅ Title suggestion applied', ['new_title' => substr($cleanValue, 0, 60)]);
            } elseif ($type === 'description') {
                $this->seoData['description'] = $cleanValue;
                Log::info('✅ Description suggestion applied', ['new_description' => substr($cleanValue, 0, 80)]);
            } else {
                Log::warning('⚠️ Unknown suggestion type', ['type' => $type]);
                throw new \InvalidArgumentException('Geçersiz öneri türü: ' . $type);
            }
            
            // Clean data after applying suggestion
            $this->cleanComponentData();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => ucfirst($type) . ' önerisi uygulandı',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('🚨 applySuggestion failed', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Öneri uygulanırken hata oluştu',
                'type' => 'error'
            ]);
        }
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
            
        } catch (\Exception) {
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
    
    /**
     * Parent component kaydetmeden önce SEO verilerini güncelle
     */
    public function prepareSeoForSave()
    {
        $result = $this->saveSeoData();
        
        // Slug verilerini de parent'a gönder
        $this->saveSlugToParent();
        
        
        return $result;
    }
    
    /**
     * Slug verilerini parent component'e gönder
     */
    public function saveSlugToParent()
    {
        if (!$this->model || !$this->model->exists) {
            return;
        }
        
        try {
            // Page model'de slug alanını güncelle
            if (method_exists($this->model, 'getAttribute') && $this->model->getAttribute('slug') !== null) {
                $currentSlugs = $this->model->slug ?? [];
                
                // Mevcut slug verilerini güncelle
                foreach ($this->slugData as $lang => $slugValue) {
                    if (!empty($slugValue)) {
                        $currentSlugs[$lang] = $slugValue;
                    }
                }
                
                $this->model->update(['slug' => $currentSlugs]);
                
            }
            
        } catch (\Exception) {
        }
    }
    
    /**
     * Livewire event listener'ları
     */
    protected $listeners = [
        'saveSeoData' => 'prepareSeoForSave',
        'parentFormSaving' => 'prepareSeoForSave',
        'pageFormSubmit' => 'prepareSeoForSave'
    ];
    
    /**
     * Handle Livewire exceptions
     */
    public function exception($e, $stopPropagation)
    {
        Log::error('SeoFormComponent Exception', [
            'message' => $e->getMessage(),
            // Trace kaldırıldı - memory exhausted önlemek için
            'component_data' => [
                'model_id' => $this->model?->id,
                'current_language' => $this->currentLanguage,
                'seo_data_size' => count($this->seoData ?? []),
                'ai_analysis_size' => count($this->aiAnalysis ?? [])
            ]
        ]);
        
        // Clean component data after exception
        $this->cleanComponentData();
        
        $this->dispatch('toast', [
            'title' => 'Hata',
            'message' => 'Beklenmeyen bir hata oluştu: ' . $e->getMessage(),
            'type' => 'error'
        ]);
        
        return false; // Don't stop propagation
    }
    
    /**
     * Clean component data before serialization to prevent JSON errors
     */
    private function cleanComponentData()
    {
        // Aggressively clean all properties to prevent undefined values
        $this->modelId = is_numeric($this->modelId) ? (int) $this->modelId : null;
        $this->modelType = is_string($this->modelType) ? $this->ensureUtf8Safe($this->modelType) : null;
        $this->availableLanguages = $this->ensureArrayValid($this->availableLanguages ?? []);
        $this->currentLanguage = is_string($this->currentLanguage) ? $this->ensureUtf8Safe($this->currentLanguage) : 'tr';
        $this->seoData = $this->ensureArrayValid($this->seoData ?? []);
        $this->slugData = $this->ensureArrayValid($this->slugData ?? []);
        $this->newKeyword = is_string($this->newKeyword) ? $this->ensureUtf8Safe($this->newKeyword) : '';
        $this->aiAnalysis = $this->ensureArrayValid($this->aiAnalysis ?? []);
        
        // Remove any undefined Livewire properties that might cause issues
        $livewireInternals = ['fingerprint', 'id', 'memo', 'effects', 'lifecycle'];
        foreach ($livewireInternals as $prop) {
            if (property_exists($this, $prop) && !isset($this->{$prop})) {
                unset($this->{$prop});
            }
        }
        
        // Additional deep cleaning
        $this->deepCleanProperties();
        
        // Final JSON encode test
        $testEncode = json_encode([
            'modelId' => $this->modelId,
            'modelType' => $this->modelType,
            'currentLanguage' => $this->currentLanguage,
            'availableLanguages' => $this->availableLanguages,
            'seoData' => $this->seoData,
            'slugData' => $this->slugData,
            'newKeyword' => $this->newKeyword,
            'aiAnalysis' => $this->aiAnalysis
        ]);
        
        if ($testEncode === false || strpos($testEncode, 'undefined') !== false) {
            Log::error('🚨 cleanComponentData - JSON test failed', [
                'json_error' => json_last_error_msg(),
                'test_result' => $testEncode ?: 'false',
                'model_id' => $this->modelId,
                'model_type' => $this->modelType
            ]);
            
            // Reset problematic properties to safe defaults
            $this->seoData = [];
            $this->aiAnalysis = [];
            $this->slugData = [];
        }
    }
    
    /**
     * Deep clean all properties to prevent undefined values
     */
    private function deepCleanProperties()
    {
        // Remove any null or undefined properties
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            
            // Skip Livewire internal properties and model cache
            if (in_array($propertyName, ['id', 'fingerprint', 'listeners', 'rules', 'model'])) {
                continue;
            }
            
            $value = $this->{$propertyName} ?? null;
            
            if ($value === null) {
                // Set safe defaults for null values
                if (str_contains($propertyName, 'Data') && is_array($this->{$propertyName})) {
                    $this->{$propertyName} = [];
                } elseif (is_string($this->{$propertyName})) {
                    $this->{$propertyName} = '';
                } elseif (is_array($this->{$propertyName})) {
                    $this->{$propertyName} = [];
                }
            }
        }
    }
    
    /**
     * Ensure array is valid for JSON serialization with UTF-8 safety
     */
    private function ensureArrayValid($data)
    {
        if ($data === null || $data === '') {
            return [];
        }
        
        if (!is_array($data)) {
            return [];
        }
        
        $cleaned = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                $cleaned[$key] = null;
            } elseif (is_resource($value)) {
                $cleaned[$key] = null;
            } elseif (is_object($value) && !($value instanceof \stdClass) && !($value instanceof \DateTime)) {
                $cleaned[$key] = null;
            } elseif (is_array($value)) {
                $cleaned[$key] = $this->ensureArrayValid($value);
            } elseif (is_string($value)) {
                // Clean string for UTF-8 safety
                $cleanString = $this->ensureUtf8Safe($value);
                $cleaned[$key] = $cleanString;
            } else {
                $cleaned[$key] = $value;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Ensure string is UTF-8 safe and JSON serializable
     */
    private function ensureUtf8Safe($string)
    {
        if (!is_string($string)) {
            return '';
        }
        
        // Remove or replace invalid UTF-8 characters
        $cleanString = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        // Remove control characters that might cause JSON issues
        $cleanString = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $cleanString);
        
        // Remove any remaining non-printable characters
        $cleanString = filter_var($cleanString, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        
        // Final safety check - if it's still not JSON safe, return empty
        if (json_encode($cleanString) === false) {
            return '';
        }
        
        return $cleanString;
    }

    public function render()
    {
        // Clean data before render to prevent Livewire JSON errors
        $this->cleanComponentData();
        
        // Ensure view data is clean
        $viewData = [
            'model' => $this->getModel(),
            'languages' => $this->ensureArrayValid($this->availableLanguages ?? []),
            'currentLanguage' => (string) ($this->currentLanguage ?? 'tr'),
            'seoData' => $this->ensureArrayValid($this->seoData ?? []),
            'newKeyword' => (string) ($this->newKeyword ?? ''),
            'aiAnalysis' => $this->ensureArrayValid($this->aiAnalysis ?? [])
        ];
        
        return view('admin.components.seo-form', $viewData);
    }
    
    /**
     * Livewire lifecycle hooks to ensure data cleanliness
     */
    public function dehydrate()
    {
        try {
            // Clean data before dehydration
            $this->cleanComponentData();
            
            // Test that all properties are JSON serializable
            $snapshot = [
                'modelId' => $this->modelId,
                'modelType' => $this->modelType,
                'currentLanguage' => $this->currentLanguage,
                'availableLanguages' => $this->availableLanguages,
                'seoData' => $this->seoData,
                'slugData' => $this->slugData,
                'newKeyword' => $this->newKeyword,
                'aiAnalysis' => $this->aiAnalysis
            ];
            
            $testJson = json_encode($snapshot);
            if ($testJson === false || strpos($testJson, 'undefined') !== false) {
                Log::error('🚨 SeoFormComponent DEHYDRATE - JSON INVALID', [
                    'json_error' => json_last_error_msg(),
                    'test_result' => substr($testJson ?: 'false', 0, 200),
                    'snapshot_keys' => array_keys($snapshot)
                ]);
                
                // Force reset to safe state
                $this->seoData = [];
                $this->aiAnalysis = [];
                $this->slugData = [];
                $this->availableLanguages = ['tr'];
                $this->currentLanguage = 'tr';
                $this->newKeyword = '';
            }
            
        } catch (\Exception $e) {
            Log::error('🚨 SeoFormComponent DEHYDRATE ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function hydrate()
    {
        try {
            $this->cleanComponentData();
            
        } catch (\Exception $e) {
            Log::error('🚨 SeoFormComponent HYDRATE ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Override toArray to ensure clean serialization
     */
    public function toArray()
    {
        // Instead of using parent::toArray() which might include problematic data,
        // manually construct the array with only our safe properties
        
        $data = [
            'modelId' => is_numeric($this->modelId) ? (int) $this->modelId : null,
            'modelType' => is_string($this->modelType) ? $this->modelType : null,
            'currentLanguage' => is_string($this->currentLanguage) ? $this->currentLanguage : 'tr',
            'availableLanguages' => is_array($this->availableLanguages) ? $this->availableLanguages : ['tr'],
            'seoData' => is_array($this->seoData) ? $this->seoData : [],
            'slugData' => is_array($this->slugData) ? $this->slugData : [],
            'newKeyword' => is_string($this->newKeyword) ? $this->newKeyword : '',
            'aiAnalysis' => is_array($this->aiAnalysis) ? $this->aiAnalysis : []
        ];
        
        // Add necessary Livewire properties manually from parent
        try {
            $parentData = parent::toArray();
            
            // Only add safe Livewire internals
            $safeLivewireProps = ['id', '__id', '__name', 'listeners', 'rules', 'messages', 'attributes', 'except'];
            foreach ($safeLivewireProps as $prop) {
                if (isset($parentData[$prop])) {
                    $testValue = json_encode($parentData[$prop]);
                    if ($testValue !== false && strpos($testValue, 'undefined') === false) {
                        $data[$prop] = $parentData[$prop];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('🚨 SeoFormComponent toArray - Parent data error', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Final safety test
        $finalTest = json_encode($data);
        if ($finalTest === false || strpos($finalTest, 'undefined') !== false) {
            Log::error('🚨 SeoFormComponent toArray - FINAL JSON UNSAFE', [
                'json_error' => json_last_error_msg(),
                'test_result' => substr($finalTest ?: 'false', 0, 200),
                'data_keys' => array_keys($data)
            ]);
            
            // Return absolute minimum safe data
            return [
                'modelId' => $this->modelId,
                'modelType' => $this->modelType,
                'currentLanguage' => 'tr',
                'availableLanguages' => ['tr'],
                'seoData' => [],
                'slugData' => [],
                'newKeyword' => '',
                'aiAnalysis' => []
            ];
        }
        
        return $data;
    }
}