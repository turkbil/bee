<?php
declare(strict_types=1);

namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Services\GlobalSeoService;
use App\Services\GlobalTabService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;

#[Layout('admin.layout')]
class PortfolioManageComponent extends Component
{
    use WithFileUploads, HasSlugManagement;

    public $portfolioId;
    public $currentLanguage;
    public $availableLanguages = [];
    public $activeTab;
    
    // Çoklu dil inputs
    public $multiLangInputs = [];
    
    // Dil-neutral inputs (Portfolio özel alanları - Migration'a uygun)
    public $inputs = [
        'portfolio_category_id' => '',
        'client' => '',
        'date' => '',
        'url' => '',
        'image' => '',
        'is_active' => true,
    ];
    
    // SEO Alanları (Global SEO sistemi)
    public $seo_title = '';
    public $seo_description = '';
    public $seo_keywords = '';
    public $canonical_url = '';
    
    // SEO Cache - Tüm dillerin SEO verileri
    public $seoDataCache = [];
    
    // Konfigürasyon verileri
    public $tabConfig = [];
    public $seoConfig = [];
    public $tabCompletionStatus = [];
    public $seoLimits = [];
    
    public $studioEnabled = false;
    public $categories = [];
    
    // Image upload (Livewire WithFileUploads)
    public $temporaryImages = [];
    
    // Cached portfolio with SEO
    protected $cachedPortfolioWithSeo = null;
    
    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'tab-changed' => 'handleTabChange',
        'seo-keywords-updated' => 'updateSeoKeywords',
        'seo-field-updated' => 'handleSeoFieldUpdate',
        'switchLanguage' => 'switchLanguage',
        'js-language-sync' => 'handleJavaScriptLanguageSync',
    ];

    public function mount($id = null)
    {
        // 1. Tab configuration yükle
        $this->initializeTabConfiguration();
        
        // 2. Dilleri yükle
        $this->loadAvailableLanguages();
        
        // 3. Kategorileri yükle
        $this->loadCategories();
        
        // 4. Studio kontrolü
        $this->checkStudioAvailability();
        
        // 5. SEO konfigürasyonu
        $this->initializeSeoConfiguration();
        
        // 6. Portfolio data yükle (eğer edit mode)
        if ($id) {
            $this->portfolioId = $id;
            $this->loadPortfolioData($id);
        } else {
            $this->initializeEmptyInputs();
        }
        
        // 7. Tab completion durumunu hesapla
        $this->calculateTabCompletion();
    }
    
    /**
     * Tab konfigürasyonunu başlat (Page pattern)
     */
    protected function initializeTabConfiguration(): void
    {
        $this->tabConfig = config('portfolio.tabs.tabs', [
            [
                'key' => 'basic',
                'name' => 'Temel Bilgiler',
                'icon' => 'fas fa-briefcase',
                'required_fields' => ['title']
            ],
            [
                'key' => 'seo',
                'name' => 'SEO',
                'icon' => 'fas fa-search',
                'required_fields' => ['seo_title']
            ]
        ]);
    }
    
    /**
     * Available languages yükle (Page pattern)
     */
    protected function loadAvailableLanguages(): void
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
            
        if (empty($this->availableLanguages)) {
            $this->availableLanguages = ['tr'];
        }
        
        // Default language ayarla
        $defaultLang = session('site_default_language', 'tr');
        $this->currentLanguage = in_array($defaultLang, $this->availableLanguages) 
            ? $defaultLang 
            : $this->availableLanguages[0];
    }
    
    /**
     * Kategorileri yükle
     */
    protected function loadCategories(): void
    {
        $this->categories = PortfolioCategory::where('is_active', true)
            ->orderBy('portfolio_category_id')
            ->get();
    }
    
    /**
     * Studio availability check
     */
    protected function checkStudioAvailability(): void
    {
        $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
    }
    
    /**
     * SEO konfigürasyonu başlat (Page pattern)
     */
    protected function initializeSeoConfiguration(): void
    {
        $this->seoConfig = [
            'model_type' => Portfolio::class,
            'model_id' => $this->portfolioId,
        ];
        
        $this->seoLimits = [
            'seo_title' => ['min' => 30, 'max' => 60],
            'seo_description' => ['min' => 120, 'max' => 160],
        ];
    }
    
    /**
     * Portfolio data yükle (edit mode)
     */
    protected function loadPortfolioData(string|int $id): void
    {
        $portfolio = Portfolio::with('seoSetting')->find($id);
        
        if (!$portfolio) {
            abort(404, 'Portfolio bulunamadı');
        }
        
        // Portfolio özel alanları
        $this->inputs = [
            'portfolio_category_id' => $portfolio->portfolio_category_id,
            'client' => $portfolio->client,
            'date' => $portfolio->date,
            'url' => $portfolio->url,
            'image' => $portfolio->image,
            'is_active' => $portfolio->is_active,
        ];
        
        // Çoklu dil alanları yükle
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => $portfolio->getTranslation('title', $lang) ?? '',
                'slug' => $portfolio->getTranslation('slug', $lang) ?? '',
                'body' => $portfolio->getTranslation('body', $lang) ?? '',
            ];
        }
        
        // SEO data yükle (Global SEO sistemi)
        $this->loadSeoData($portfolio);
    }
    
    /**
     * SEO data yükle (Page pattern)
     */
    protected function loadSeoData(Portfolio $portfolio): void
    {
        // Her dil için boş SEO cache oluştur
        foreach ($this->availableLanguages as $lang) {
            $this->seoDataCache[$lang] = [
                'seo_title' => '',
                'seo_description' => '',
                'seo_keywords' => '',
                'canonical_url' => '',
            ];
        }
        
        // Eğer SEO kaydı varsa yükle
        if ($portfolio->seoSetting) {
            foreach ($this->availableLanguages as $lang) {
                $this->seoDataCache[$lang] = [
                    'seo_title' => $portfolio->seoSetting->getTranslation('titles', $lang) ?? '',
                    'seo_description' => $portfolio->seoSetting->getTranslation('descriptions', $lang) ?? '',
                    'seo_keywords' => $this->processKeywordsForDisplay($portfolio->seoSetting->getTranslation('keywords', $lang)),
                    'canonical_url' => $portfolio->seoSetting->canonical_url ?? '',
                ];
            }
        }
        
        // Current language için properties set et
        $currentSeoData = $this->seoDataCache[$this->currentLanguage] ?? [];
        $this->seo_title = $currentSeoData['seo_title'] ?? '';
        $this->seo_description = $currentSeoData['seo_description'] ?? '';
        $this->seo_keywords = $currentSeoData['seo_keywords'] ?? '';
        $this->canonical_url = $currentSeoData['canonical_url'] ?? '';
    }
    
    /**
     * Boş inputs initialize et
     */
    protected function initializeEmptyInputs(): void
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'slug' => '',
                'body' => '',
            ];
            
            $this->seoDataCache[$lang] = [
                'seo_title' => '',
                'seo_description' => '',
                'seo_keywords' => '',
                'canonical_url' => '',
            ];
        }
    }
    
    /**
     * Tab completion hesapla (Page pattern)
     */
    protected function calculateTabCompletion(): void
    {
        $this->tabCompletionStatus = [];
        
        foreach ($this->tabConfig as $tab) {
            $isComplete = true;
            
            if ($tab['key'] === 'basic') {
                // Temel bilgiler için en az bir dilde title gerekli
                $hasTitle = false;
                foreach ($this->availableLanguages as $lang) {
                    if (!empty($this->multiLangInputs[$lang]['title'])) {
                        $hasTitle = true;
                        break;
                    }
                }
                $isComplete = $hasTitle && !empty($this->inputs['portfolio_category_id']);
            }
            
            if ($tab['key'] === 'seo') {
                // SEO için en az bir dilde seo_title gerekli
                $hasSeoTitle = false;
                foreach ($this->availableLanguages as $lang) {
                    if (!empty($this->seoDataCache[$lang]['seo_title'])) {
                        $hasSeoTitle = true;
                        break;
                    }
                }
                $isComplete = $hasSeoTitle;
            }
            
            $this->tabCompletionStatus[$tab['key']] = $isComplete;
        }
    }
    
    /**
     * Language switch handler (Page pattern)
     */
    public function switchLanguage($language): void
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
            
            // SEO data sync for current language
            $currentSeoData = $this->seoDataCache[$language] ?? [];
            $this->seo_title = $currentSeoData['seo_title'] ?? '';
            $this->seo_description = $currentSeoData['seo_description'] ?? '';
            $this->seo_keywords = $currentSeoData['seo_keywords'] ?? '';
            $this->canonical_url = $currentSeoData['canonical_url'] ?? '';
        }
    }
    
    /**
     * SEO field update handler (Page pattern)
     */
    public function handleSeoFieldUpdate($data): void
    {
        $field = $data['field'] ?? null;
        $value = $data['value'] ?? '';
        $language = $data['language'] ?? $this->currentLanguage;
        
        if (!isset($this->seoDataCache[$language])) {
            $this->seoDataCache[$language] = [
                'seo_title' => '',
                'seo_description' => '',
                'seo_keywords' => '',
                'canonical_url' => ''
            ];
        }
        
        $this->seoDataCache[$language][$field] = $value;
        
        // Current language ise property'yi de güncelle
        if ($language === $this->currentLanguage) {
            $this->{$field} = $value;
        }
        
        $this->calculateTabCompletion();
    }
    
    /**
     * Slug management processing (Page pattern)
     */
    protected function processSlugManagement(): array
    {
        return $this->processMultiLanguageSlugs(
            Portfolio::class,
            $this->multiLangInputs,
            $this->availableLanguages,
            $this->portfolioId
        );
    }
    
    /**
     * Save method (Page pattern uyumlu)
     */
    public function save($redirect = false, $resetForm = false)
    {
        // Slug processing
        $processedSlugs = $this->processSlugManagement();
        
        // Merge processed slugs
        foreach ($processedSlugs as $lang => $slug) {
            $this->multiLangInputs[$lang]['slug'] = $slug;
        }
        
        try {
            // Portfolio data hazırla
            $portfolioData = [
                'portfolio_category_id' => $this->inputs['portfolio_category_id'],
                'client' => $this->inputs['client'],
                'date' => $this->inputs['date'],
                'url' => $this->inputs['url'],
                'image' => $this->inputs['image'],
                'is_active' => $this->inputs['is_active'],
            ];
            
            // Multi-language fields ekle
            foreach (['title', 'slug', 'body'] as $field) {
                $portfolioData[$field] = [];
                foreach ($this->availableLanguages as $lang) {
                    $portfolioData[$field][$lang] = $this->multiLangInputs[$lang][$field] ?? '';
                }
            }
            
            if ($this->portfolioId) {
                // Update
                $portfolio = Portfolio::find($this->portfolioId);
                $portfolio->update($portfolioData);
                $message = __('portfolio::admin.portfolio_updated');
            } else {
                // Create
                $portfolio = Portfolio::create($portfolioData);
                $this->portfolioId = $portfolio->portfolio_id;
                $message = __('portfolio::admin.portfolio_created');
            }
            
            // SEO data kaydet (Global SEO sistem)
            $this->saveSeoData($portfolio);
            
            $toast = [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success'
            ];
            
        } catch (\Exception $e) {
            $toast = [
                'title' => __('admin.error'),
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
        
        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.portfolio.index');
        }
        
        $this->dispatch('toast', $toast);
        
        if ($resetForm && !$this->portfolioId) {
            $this->reset();
            $this->mount();
        }
    }
    
    /**
     * SEO data save (Global SEO sistemi)
     */
    protected function saveSeoData(Portfolio $portfolio): void
    {
        $seoData = [];
        
        foreach (['seo_title', 'seo_description', 'seo_keywords'] as $field) {
            $seoData[$field] = [];
            foreach ($this->availableLanguages as $lang) {
                $seoData[$field][$lang] = $this->seoDataCache[$lang][$field] ?? '';
            }
        }
        
        $seoData['canonical_url'] = $this->seoDataCache[$this->currentLanguage]['canonical_url'] ?? '';
        
        $portfolio->updateSeoForLanguage($this->currentLanguage, $seoData);
    }
    
    /**
     * Image remove handler
     */
    public function removeImage($imageKey = 'image'): void
    {
        if (isset($this->temporaryImages[$imageKey])) {
            unset($this->temporaryImages[$imageKey]);
        } else {
            // Existing image remove
            $this->inputs['image'] = '';
        }
    }
    
    /**
     * Keywords processing for display (string to array conversion)
     */
    protected function processKeywordsForDisplay($keywords): string
    {
        if (empty($keywords)) {
            return '';
        }
        
        // JSON string ise decode et
        if (is_string($keywords)) {
            $decoded = json_decode($keywords, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return implode(', ', $decoded);
            }
            return $keywords;
        }
        
        // Array ise string'e çevir
        if (is_array($keywords)) {
            return implode(', ', $keywords);
        }
        
        return (string) $keywords;
    }
    
    /**
     * Current portfolio accessor - Universal SEO Component için
     */
    public function currentPortfolio()
    {
        if (!$this->portfolioId) {
            return null;
        }
        
        return Portfolio::with('seoSetting')->find($this->portfolioId);
    }
    
    public function render()
    {
        return view('portfolio::admin.livewire.portfolio-manage-component');
    }
}