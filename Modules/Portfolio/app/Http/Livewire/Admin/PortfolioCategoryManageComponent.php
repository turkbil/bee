<?php

namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Services\PortfolioCategoryService;
use Illuminate\Support\Facades\Log;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class PortfolioCategoryManageComponent extends Component
{
    public $categoryId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // SOLID Dependencies
    protected $categoryService;

    /**
     * Get current category model
     */
    #[Computed]
    public function currentCategory()
    {
        if (!$this->categoryId) {
            return null;
        }

        return PortfolioCategory::query()->find($this->categoryId);
    }

    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
    ];

    // Dependency Injection Boot
    public function boot()
    {
        // CategoryService'i initialize et
        $this->categoryService = app(\Modules\Portfolio\App\Services\PortfolioCategoryService::class);

        // Layout sections
        view()->share('pretitle', __('portfolio::admin.category_management'));
        view()->share('title', __('portfolio::admin.categories'));
    }

    public function updated($propertyName)
    {
        // Tab completion status güncelleme
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        // Dependencies initialize
        $this->boot();

        // Universal Component'lerden initial data al
        $this->initializeUniversalComponents();

        // Kategori verilerini yükle
        if ($id) {
            $this->categoryId = $id;
            $this->loadCategoryData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        // Tab completion durumunu hesapla
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    /**
     * Universal Component'leri initialize et
     */
    protected function initializeUniversalComponents()
    {
        // Dil bilgileri
        $languages = available_tenant_languages();
        $this->availableLanguages = array_column($languages, 'code');
        $this->languageNames = array_column($languages, 'native_name', 'code');
        $this->currentLanguage = get_tenant_default_locale();

        // Tab bilgileri
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('portfolio_category');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('portfolio_category');
    }

    /**
     * Dil değişikliğini handle et
     */
    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('PortfolioCategoryManage - Dil değişti', [
                'new_language' => $language
            ]);
        }
    }

    /**
     * Kategori verilerini yükle
     */
    protected function loadCategoryData($id)
    {
        $formData = $this->categoryService->prepareCategoryForForm($id, $this->currentLanguage);
        $category = $formData['category'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($category) {
            // Dil-neutral alanlar
            $this->inputs = $category->only(['is_active', 'sort_order']);

            // Çoklu dil alanları
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'name' => $category->getTranslated('name', $lang, false) ?? '',
                    'description' => $category->getTranslated('description', $lang, false) ?? '',
                    'slug' => $category->getTranslated('slug', $lang, false) ?? '',
                ];
            }
        }
    }

    /**
     * Boş inputs hazırla
     */
    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'name' => '',
                'description' => '',
                'slug' => '',
            ];
        }
    }

    /**
     * Tüm form datasını al
     */
    protected function getAllFormData(): array
    {
        return array_merge(
            $this->inputs,
            $this->multiLangInputs[$this->currentLanguage] ?? []
        );
    }

    /**
     * Ana dili belirle
     */
    protected function getMainLanguage()
    {
        return get_tenant_default_locale();
    }

    protected function rules()
    {
        $rules = [
            'inputs.is_active' => 'boolean',
            'inputs.sort_order' => 'integer',
        ];

        // Çoklu dil alanları - ana dil mecburi
        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.name"] = $lang === $mainLanguage ? 'required|min:2|max:191' : 'nullable|min:2|max:191';
            $rules["multiLangInputs.{$lang}.description"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'multiLangInputs.*.name.required' => 'Kategori adı zorunludur',
        'multiLangInputs.*.name.min' => 'Kategori adı en az 2 karakter olmalıdır',
        'multiLangInputs.*.name.max' => 'Kategori adı en fazla 191 karakter olabilir',
    ];

    /**
     * Çoklu dil verilerini hazırla
     */
    protected function prepareMultiLangData(): array
    {
        $multiLangData = [];

        // Name verilerini topla
        $multiLangData['name'] = [];
        foreach ($this->availableLanguages as $lang) {
            $name = $this->multiLangInputs[$lang]['name'] ?? '';
            if (!empty($name)) {
                $multiLangData['name'][$lang] = $name;
            }
        }

        // Description verilerini topla
        $multiLangData['description'] = [];
        foreach ($this->availableLanguages as $lang) {
            $description = $this->multiLangInputs[$lang]['description'] ?? '';
            if (!empty($description)) {
                $multiLangData['description'][$lang] = $description;
            }
        }

        // Slug verilerini işle - SlugHelper toplu işlem
        $slugInputs = [];
        $nameInputs = [];
        foreach ($this->availableLanguages as $lang) {
            $slugInputs[$lang] = $this->multiLangInputs[$lang]['slug'] ?? '';
            $nameInputs[$lang] = $this->multiLangInputs[$lang]['name'] ?? '';
        }

        $multiLangData['slug'] = SlugHelper::processMultiLanguageSlugs(
            PortfolioCategory::class,
            $slugInputs,
            $nameInputs,
            'slug',
            $this->categoryId
        );

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        Log::info('SAVE METHOD BAŞLADI - Category', [
            'categoryId' => $this->categoryId,
            'redirect' => $redirect,
            'currentLanguage' => $this->currentLanguage
        ]);

        try {
            $this->validate($this->rules(), $this->messages);
            Log::info('Validation başarılı - Category');
        } catch (\Exception $e) {
            Log::error('Validation HATASI - Category', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Doğrulama Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            return;
        }

        // Çoklu dil verilerini hazırla
        $multiLangData = $this->prepareMultiLangData();

        $data = array_merge($this->inputs, $multiLangData);

        if ($this->categoryId) {
            $category = PortfolioCategory::query()->findOrFail($this->categoryId);
            $currentData = collect($category->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('portfolio::admin.category_updated'),
                    'type' => 'success'
                ];
            } else {
                $category->update($data);
                log_activity($category, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('portfolio::admin.category_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $category = PortfolioCategory::query()->create($data);
            $this->categoryId = $category->category_id;
            log_activity($category, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('portfolio::admin.category_created'),
                'type' => 'success'
            ];
        }

        Log::info('Save method tamamlanıyor - Category', [
            'categoryId' => $this->categoryId,
            'redirect' => $redirect
        ]);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.portfolio.category.index');
        }

        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET
        $this->dispatch('category-saved', categoryId: $this->categoryId);

        Log::info('Save method başarıyla tamamlandı - Category', [
            'categoryId' => $this->categoryId
        ]);

        if ($resetForm && !$this->categoryId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('portfolio::admin.livewire.category-manage-component', [
            'jsVariables' => [
                'currentCategoryId' => $this->categoryId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }
}
