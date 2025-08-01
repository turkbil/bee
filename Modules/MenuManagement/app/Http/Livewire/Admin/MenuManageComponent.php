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
    
    
    // Konfigürasyon verileri
    public $tabConfig = [];
    public $seoConfig = [];
    public $tabCompletionStatus = [];
    public $seoLimits = [];
    
    // 🚨 PERFORMANCE FIX: Cached menu with SEO
    protected $cachedMenuWithSeo = null;
    
    // SOLID Dependencies
    protected $menuService;
    
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
    }

    /**
     * Component initialization
     */
    public function mount($id = null): void
    {
        $this->menuId = $id ? (int)$id : null;
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
        // Load tabs config - sadece basic tab
        $this->tabConfig = [
            ['key' => 'basic', 'name' => 'Temel Bilgiler']
        ];
        
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
            
            
        } catch (\Exception $e) {
            logger('MenuManageComponent load error: ' . $e->getMessage());
            session()->flash('error', __('menumanagement::admin.menu_not_found'));
        }
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
        
        
        $this->currentLanguage = $language;
        
        
        $this->updateTabCompletionStatus();
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
        logger('🔍 MenuManageComponent save başladı', [
            'menuId' => $this->menuId,
            'multiLangInputs' => $this->multiLangInputs,
            'inputs' => $this->inputs
        ]);

        try {
            $this->validate([
                'multiLangInputs.*.name' => 'required|string|max:255',
                'inputs.location' => 'required|string|in:header,footer,sidebar,mobile',
            ], [
                'multiLangInputs.*.name.required' => __('menumanagement::admin.menu_name') . ' ' . __('admin.required'),
                'multiLangInputs.*.name.max' => __('menumanagement::admin.menu_name') . ' ' . __('admin.field_too_long'),
                'inputs.location.required' => __('menumanagement::admin.menu_location') . ' ' . __('admin.required'),
                'inputs.location.in' => __('menumanagement::admin.menu_location') . ' ' . __('admin.invalid_input'),
            ]);

            logger('✅ MenuManageComponent validation başarılı');
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('🚨 MenuManageComponent validation HATASI!', [
                'validation_errors' => $e->errors(),
                'multiLangInputs' => $this->multiLangInputs,
                'inputs' => $this->inputs
            ]);
            throw $e; // Re-throw validation exception
        }

        try {
            logger('🔄 Slug işleme başlıyor');
            // Process slugs using SlugHelper
            $processedSlugs = $this->processMultiLanguageSlugs(
                Menu::class,
                $this->multiLangInputs,
                $this->availableLanguages,
                $this->menuId
            );

            logger('✅ Slug işleme tamamlandı', ['processedSlugs' => $processedSlugs]);

            // Update multiLangInputs with processed slugs
            foreach ($processedSlugs as $langCode => $slugData) {
                $this->multiLangInputs[$langCode]['slug'] = $slugData['slug'];
            }


            // Prepare data for service
            $menuData = [
                'name' => $this->multiLangInputs,
                'slug' => collect($this->multiLangInputs)->map(fn($input) => $input['slug'])->toArray(),
                'description' => collect($this->multiLangInputs)->map(fn($input) => $input['description'] ?? '')->toArray(),
                'location' => $this->inputs['location'],
                'is_active' => $this->inputs['is_active'],
                'is_default' => $this->inputs['is_default'],
            ];

            logger('🔄 Service işlemi başlıyor', ['menuData' => $menuData]);

            // Save via service
            if ($this->menuId) {
                logger('🔄 Menü güncelleniyor: ' . $this->menuId);
                $result = $this->menuService->updateMenu($this->menuId, $menuData);
            } else {
                logger('🔄 Yeni menü oluşturuluyor');
                $result = $this->menuService->createMenu($menuData);
                $this->menuId = $result->data?->menu_id;
            }

            logger('✅ Service işlemi tamamlandı', ['result' => $result]);


            // Flash message and redirect
            session()->flash($result->type, $result->message);
            
            if ($result->success) {
                $this->redirect(route('admin.menumanagement.menu.manage', ['id' => $this->menuId]));
            }

        } catch (\Exception $e) {
            logger('🚨 MenuManageComponent save HATA!', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'menuId' => $this->menuId,
                'multiLangInputs' => $this->multiLangInputs,
                'inputs' => $this->inputs
            ]);
            
            session()->flash('error', __('menumanagement::admin.menu_creation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Save and Continue
     */
    public function saveAndContinue(): void
    {
        logger('🔍 MenuManageComponent saveAndContinue başladı');
        
        // Save first
        $this->save();
        
        // If successful, redirect to new menu form
        if (session()->has('success')) {
            $this->redirect(route('admin.menumanagement.menu.manage'));
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