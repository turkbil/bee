<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\MenuManagement\App\Models\Menu;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;

#[Layout('admin.layout')]
class MenuManageComponent extends Component
{
    use WithFileUploads, HasSlugManagement;

    public $menuId;
    public $currentLanguage;
    public $availableLanguages = [];
    public $activeTab;
    
    // Çoklu dil inputs
    public $multiLangInputs = [];
    
    // Dil-neutral inputs
    public $inputs = [
        'location' => 'header',
        'is_active' => true,
        'is_default' => false,
    ];
    
    // SEO Alanları
    public $seo_title = '';
    public $seo_description = '';
    public $seo_keywords = '';
    public $canonical_url = '';
   
    // SEO Cache - Tüm dillerin SEO verileri (Performance Optimization)
    public $seoDataCache = [];
    
    // JavaScript için tüm dillerin SEO verileri (Blade exposure)
    public $allLanguagesSeoData = [];
    
    // Konfigürasyon verileri
    public $tabConfig = [];
    public $seoConfig = [];
    public $tabCompletionStatus = [];
    public $seoLimits = [];
    
    // 🚨 PERFORMANCE FIX: Cached menu with SEO
    protected $cachedMenuWithSeo = null;
    
    // SOLID Dependencies
    protected $menuService;
    protected $seoRepository;
    
    /**
     * Get current menu model for universal SEO component
     */
    #[Computed]
    public function currentMenu()
    {
        if (!$this->menuId) {
            return null;
        }
        
        return $this->getCachedMenuWithSeo() ?? Menu::find($this->menuId);
    }
    
    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'tab-changed' => 'handleTabChange',
        'seo-keywords-updated' => 'updateSeoKeywords',
        'seo-field-updated' => 'handleSeoFieldUpdate',
        'switchLanguage' => 'switchLanguage',
        'js-language-sync' => 'handleJavaScriptLanguageSync',
        'handleTestEvent' => 'handleTestEvent',
        'simple-test' => 'handleSimpleTest',
        'handleJavaScriptLanguageSync' => 'handleJavaScriptLanguageSync',
        'debug-test' => 'handleDebugTest',
        'set-js-language' => 'setJavaScriptLanguage',
        'set-continue-mode' => 'setContinueMode'
    ];
    
    /**
     * SEO Keywords Updated Handler
     */
    public function updateSeoKeywords($data)
    {
        $language = $data['lang'] ?? $this->currentLanguage;
        $keywords = $data['keywords'] ?? '';
        
        // seoDataCache'e kaydet
        if (!isset($this->seoDataCache[$language])) {
            $this->seoDataCache[$language] = [];
        }
        
        $this->seoDataCache[$language]['keywords'] = $keywords;
        
        // Global SEO verisini güncelle
        $this->allLanguagesSeoData[$language]['keywords'] = $keywords;
    }

    /**
     * Handle SEO field updates from Universal SEO Component
     */
    public function handleSeoFieldUpdate($data)
    {
        $field = $data['field'] ?? '';
        $value = $data['value'] ?? '';
        $language = $data['language'] ?? $this->currentLanguage;
        
        // Cache'e kaydet
        if (!isset($this->seoDataCache[$language])) {
            $this->seoDataCache[$language] = [];
        }
        
        $this->seoDataCache[$language][$field] = $value;
        
        // Global SEO verisini güncelle
        $this->allLanguagesSeoData[$language][$field] = $value;
        
        // Component property'sini güncelle
        if (property_exists($this, $field)) {
            $this->$field = $value;
        }
    }

    /**
     * Tab değişimi handler
     */
    public function handleTabChange($tabKey)
    {
        $this->activeTab = $tabKey;
        
        // Tab completion durumunu güncelle
        $this->updateTabCompletionStatus();
    }

    /**
     * JavaScript Language Sync Handler
     */
    public function handleJavaScriptLanguageSync($data)
    {
        $language = $data['language'] ?? $this->currentLanguage;
        $this->switchLanguage($language);
    }

    /**
     * Test event handlers
     */
    public function handleTestEvent($data)
    {
        session()->flash('success', 'Test event received: ' . json_encode($data));
    }

    public function handleSimpleTest()
    {
        session()->flash('success', 'Simple test received successfully!');
    }

    public function handleDebugTest($data)
    {
        logger('Debug test received:', $data);
        session()->flash('success', 'Debug test logged: ' . json_encode($data));
    }

    public function setJavaScriptLanguage($language)
    {
        $this->switchLanguage($language);
    }

    public function setContinueMode($enabled = true)
    {
        // Continue mode handling
        session(['menu_continue_mode' => $enabled]);
    }

    /**
     * Modern dependency injection (Livewire 3.5+)
     */
    public function boot(): void
    {
        $this->menuService = app(\Modules\MenuManagement\App\Services\MenuService::class);
        $this->seoRepository = app(\App\Repositories\Contracts\GlobalSeoRepositoryInterface::class);
    }

    /**
     * Component initialization
     */
    public function mount($id = null): void
    {
        $this->menuId = $id;
        $this->loadConfiguration();
        $this->initializeLanguages();
        $this->initializeInputs();
        
        if ($this->menuId) {
            $this->loadMenu();
        }
        
        $this->setActiveTab();
    }

    /**
     * Load configuration from services
     */
    private function loadConfiguration(): void
    {
        $this->tabConfig = config('menumanagement.tabs', [
            ['key' => 'basic', 'name' => 'Temel Bilgiler'],
            ['key' => 'seo', 'name' => 'SEO']
        ]);
        
        $this->seoConfig = config('menumanagement.seo', []);
        $this->activeTab = !empty($this->tabConfig) ? $this->tabConfig[0]['key'] : 'basic';
    }

    /**
     * Initialize available languages
     */
    private function initializeLanguages(): void
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $this->currentLanguage = $this->availableLanguages->first()?->code ?? 'tr';
    }

    /**
     * Initialize multilingual inputs
     */
    private function initializeInputs(): void
    {
        foreach ($this->availableLanguages as $language) {
            $this->multiLangInputs[$language->code] = [
                'name' => '',
                'slug' => '',
                'description' => '',
            ];
        }
    }

    /**
     * Load existing menu data
     */
    private function loadMenu(): void
    {
        try {
            $menu = $this->menuService->getMenu($this->menuId);
            
            if (!$menu) {
                session()->flash('error', __('menumanagement::admin.menu_not_found'));
                return;
            }
            
            // Load multilingual data
            foreach ($this->availableLanguages as $language) {
                $this->multiLangInputs[$language->code] = [
                    'name' => $menu->getTranslated('name', $language->code) ?? '',
                    'slug' => $menu->getTranslated('slug', $language->code) ?? '',
                    'description' => $menu->getTranslated('description', $language->code) ?? '',
                ];
            }
            
            // Load language-neutral data
            $this->inputs = [
                'location' => $menu->location ?? 'header',
                'is_active' => $menu->is_active ?? true,
                'is_default' => $menu->is_default ?? false,
            ];
            
            // Load SEO data
            $this->loadSeoData($menu);
            
        } catch (\Exception $e) {
            logger('MenuManageComponent load error: ' . $e->getMessage());
            session()->flash('error', __('menumanagement::admin.menu_not_found'));
        }
    }

    /**
     * Load SEO data for all languages
     */
    private function loadSeoData($menu): void
    {
        foreach ($this->availableLanguages as $language) {
            $seoData = $menu->seoSetting ? $menu->seoSetting->getSeoData($language->code) : [];
            
            $this->seoDataCache[$language->code] = [
                'title' => $seoData['title'] ?? '',
                'description' => $seoData['description'] ?? '',
                'keywords' => $seoData['keywords'] ?? '',
                'canonical_url' => $seoData['canonical_url'] ?? '',
            ];
            
            $this->allLanguagesSeoData[$language->code] = $this->seoDataCache[$language->code];
        }
        
        // Set current language SEO data
        $currentSeoData = $this->seoDataCache[$this->currentLanguage] ?? [];
        $this->seo_title = $currentSeoData['title'] ?? '';
        $this->seo_description = $currentSeoData['description'] ?? '';
        $this->seo_keywords = $currentSeoData['keywords'] ?? '';
        $this->canonical_url = $currentSeoData['canonical_url'] ?? '';
    }

    /**
     * Set active tab based on configuration
     */
    private function setActiveTab(): void
    {
        $this->activeTab = $this->tabConfig[0]['key'] ?? 'basic';
        $this->updateTabCompletionStatus();
    }

    /**
     * Update tab completion status
     */
    private function updateTabCompletionStatus(): void
    {
        foreach ($this->tabConfig as $tab) {
            $tabKey = $tab['key'];
            $this->tabCompletionStatus[$tabKey] = $this->isTabComplete($tabKey);
        }
    }

    /**
     * Check if tab is complete
     */
    private function isTabComplete(string $tabKey): bool
    {
        switch ($tabKey) {
            case 'basic':
                $currentInput = $this->multiLangInputs[$this->currentLanguage] ?? [];
                return !empty($currentInput['name']);
                
            case 'seo':
                $currentSeo = $this->seoDataCache[$this->currentLanguage] ?? [];
                return !empty($currentSeo['title']) || !empty($currentSeo['description']);
                
            default:
                return false;
        }
    }

    /**
     * Switch language
     */
    public function switchLanguage(string $language): void
    {
        if (!in_array($language, $this->availableLanguages->pluck('code')->toArray())) {
            return;
        }
        
        // Save current language data before switching
        $this->saveSeoDataToCache();
        
        $this->currentLanguage = $language;
        
        // Load SEO data for new language
        $currentSeoData = $this->seoDataCache[$language] ?? [];
        $this->seo_title = $currentSeoData['title'] ?? '';
        $this->seo_description = $currentSeoData['description'] ?? '';
        $this->seo_keywords = $currentSeoData['keywords'] ?? '';
        $this->canonical_url = $currentSeoData['canonical_url'] ?? '';
        
        $this->updateTabCompletionStatus();
    }

    /**
     * Save SEO data to cache
     */
    private function saveSeoDataToCache(): void
    {
        $this->seoDataCache[$this->currentLanguage] = [
            'title' => $this->seo_title,
            'description' => $this->seo_description,
            'keywords' => $this->seo_keywords,
            'canonical_url' => $this->canonical_url,
        ];
        
        $this->allLanguagesSeoData[$this->currentLanguage] = $this->seoDataCache[$this->currentLanguage];
    }

    /**
     * Get cached menu with SEO
     */
    private function getCachedMenuWithSeo()
    {
        if ($this->cachedMenuWithSeo === null && $this->menuId) {
            $this->cachedMenuWithSeo = Menu::with('seoSetting')->find($this->menuId);
        }
        
        return $this->cachedMenuWithSeo;
    }

    /**
     * Available site languages computed property
     */
    #[Computed]
    public function availableSiteLanguages(): array
    {
        return $this->availableLanguages->pluck('code')->toArray();
    }

    /**
     * Admin locale computed property
     */
    #[Computed]
    public function adminLocale(): string
    {
        return session('admin_locale', 'tr');
    }

    /**
     * Site locale computed property
     */
    #[Computed]
    public function siteLocale(): string
    {
        return session('tenant_locale', 'tr');
    }

    /**
     * Save menu
     */
    public function save(): void
    {
        $this->validate([
            'multiLangInputs.*.name' => 'required|string|max:255',
            'inputs.location' => 'required|string|in:header,footer,sidebar,mobile',
        ], [
            'multiLangInputs.*.name.required' => __('menumanagement::admin.menu_name') . ' ' . __('admin.required'),
            'multiLangInputs.*.name.max' => __('menumanagement::admin.menu_name') . ' ' . __('admin.field_too_long'),
            'inputs.location.required' => __('menumanagement::admin.menu_location') . ' ' . __('admin.required'),
            'inputs.location.in' => __('menumanagement::admin.menu_location') . ' ' . __('admin.invalid_input'),
        ]);

        try {
            // Process slugs using SlugHelper
            $processedSlugs = $this->processMultiLanguageSlugs(
                Menu::class,
                $this->multiLangInputs,
                $this->availableLanguages,
                $this->menuId
            );

            // Update multiLangInputs with processed slugs
            foreach ($processedSlugs as $langCode => $slugData) {
                $this->multiLangInputs[$langCode]['slug'] = $slugData['slug'];
            }

            // Save current SEO data to cache before saving
            $this->saveSeoDataToCache();

            // Prepare data for service
            $menuData = [
                'name' => $this->multiLangInputs,
                'slug' => collect($this->multiLangInputs)->map(fn($input) => $input['slug'])->toArray(),
                'description' => collect($this->multiLangInputs)->map(fn($input) => $input['description'] ?? '')->toArray(),
                'location' => $this->inputs['location'],
                'is_active' => $this->inputs['is_active'],
                'is_default' => $this->inputs['is_default'],
            ];

            // Save via service
            if ($this->menuId) {
                $result = $this->menuService->updateMenu($this->menuId, $menuData);
            } else {
                $result = $this->menuService->createMenu($menuData);
                $this->menuId = $result->data?->menu_id;
            }

            // Save SEO data
            if ($result->success && $this->menuId) {
                $this->saveSeoData();
            }

            // Flash message and redirect
            session()->flash($result->type, $result->message);
            
            if ($result->success) {
                $this->redirect(route('admin.menumanagement.manage', ['id' => $this->menuId]));
            }

        } catch (\Exception $e) {
            logger('MenuManageComponent save error: ' . $e->getMessage());
            session()->flash('error', __('menumanagement::admin.menu_creation_failed'));
        }
    }

    /**
     * Save SEO data for all languages
     */
    private function saveSeoData(): void
    {
        try {
            foreach ($this->availableLanguages as $language) {
                $seoData = $this->seoDataCache[$language->code] ?? [];
                
                if (!empty(array_filter($seoData))) {
                    $this->seoRepository->updateSeoData(
                        'menu',
                        $this->menuId,
                        $language->code,
                        $seoData
                    );
                }
            }
        } catch (\Exception $e) {
            logger('MenuManageComponent SEO save error: ' . $e->getMessage());
        }
    }

    /**
     * Render component
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('menumanagement::admin.livewire.menu-manage-component', [
            'menu' => $this->currentMenu,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}