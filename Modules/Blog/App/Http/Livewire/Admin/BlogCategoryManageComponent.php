<?php

namespace Modules\Blog\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Blog\App\Services\BlogCategoryService;
use Illuminate\Support\Facades\Log;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class BlogCategoryManageComponent extends Component
{
    public $categoryId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
        'parent_id' => null,
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

        return BlogCategory::query()->find($this->categoryId);
    }

    /**
     * Dropdown için hiyerarşik kategori listesi
     */
    #[Computed]
    public function hierarchicalCategories()
    {
        $categories = BlogCategory::orderBy('sort_order', 'asc')
            ->orderBy('category_id', 'asc')
            ->get();

        return $categories->map(function($category) {
            $depth = $category->depth_level ?? 0;
            $prefix = str_repeat('─', $depth);
            if ($depth > 0) {
                $prefix .= ' ';
            }

            return [
                'id' => $category->category_id,
                'title' => $prefix . $category->getTranslated('title', app()->getLocale()),
                'depth' => $depth,
                'parent_id' => $category->parent_id
            ];
        });
    }

    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
    ];

    // Dependency Injection Boot
    public function boot()
    {
        $this->categoryService = app(\Modules\Blog\App\Services\BlogCategoryService::class);

        view()->share('pretitle', __('blog::admin.category_management'));
        view()->share('title', __('blog::admin.categories'));
    }

    public function updated($propertyName)
    {
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        $this->boot();
        $this->initializeUniversalComponents();

        if ($id) {
            $this->categoryId = $id;
            $this->loadCategoryData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    /**
     * Universal Component'leri initialize et
     */
    protected function initializeUniversalComponents()
    {
        $languages = available_tenant_languages();
        $this->availableLanguages = array_column($languages, 'code');
        $this->languageNames = array_column($languages, 'native_name', 'code');
        $this->currentLanguage = get_tenant_default_locale();

        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('blog_category');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('blog_category');
    }

    /**
     * Dil değişikliğini handle et
     */
    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('BlogCategoryManage - Dil değişti', [
                'new_language' => $language
            ]);
        }
    }

    /**
     * Kategori verilerini yükle
     */
    protected function loadCategoryData($id)
    {
        $category = BlogCategory::find($id);

        if ($category) {
            // Dil-neutral alanlar
            $this->inputs = $category->only(['is_active', 'parent_id']);

            // Çoklu dil alanları
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $category->getTranslated('title', $lang, false) ?? '',
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
                'title' => '',
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
            'inputs.parent_id' => 'nullable|exists:blog_categories,category_id',
        ];

        // Çoklu dil alanları - ana dil mecburi
        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:2|max:191' : 'nullable|min:2|max:191';
            $rules["multiLangInputs.{$lang}.description"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'multiLangInputs.*.title.required' => 'Kategori başlığı zorunludur',
        'multiLangInputs.*.title.min' => 'Kategori başlığı en az 2 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Kategori başlığı en fazla 191 karakter olabilir',
    ];

    /**
     * Çoklu dil verilerini hazırla
     */
    protected function prepareMultiLangData(): array
    {
        $multiLangData = [];

        // Title verilerini topla
        $multiLangData['title'] = [];
        foreach ($this->availableLanguages as $lang) {
            $title = $this->multiLangInputs[$lang]['title'] ?? '';
            if (!empty($title)) {
                $multiLangData['title'][$lang] = $title;
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

        // Slug verilerini işle
        $slugInputs = [];
        $titleInputs = [];
        foreach ($this->availableLanguages as $lang) {
            $slugInputs[$lang] = $this->multiLangInputs[$lang]['slug'] ?? '';
            $titleInputs[$lang] = $this->multiLangInputs[$lang]['title'] ?? '';
        }

        $multiLangData['slug'] = SlugHelper::processMultiLanguageSlugs(
            BlogCategory::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->categoryId
        );

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        try {
            $this->validate($this->rules(), $this->messages);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Doğrulama Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            return;
        }

        $multiLangData = $this->prepareMultiLangData();
        $data = array_merge($this->inputs, $multiLangData);

        if ($this->categoryId) {
            $category = BlogCategory::query()->findOrFail($this->categoryId);
            $category->update($data);
            log_activity($category, 'güncellendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('blog::admin.category_updated'),
                'type' => 'success'
            ];
        } else {
            $category = BlogCategory::query()->create($data);
            $this->categoryId = $category->category_id;
            log_activity($category, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('blog::admin.category_created'),
                'type' => 'success'
            ];
        }

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.blog.category.index');
        }

        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET
        $this->dispatch('category-saved', categoryId: $this->categoryId);

        if ($resetForm && !$this->categoryId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('blog::admin.livewire.blog-category-manage-component', [
            'jsVariables' => [
                'currentCategoryId' => $this->categoryId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    /**
     * Kategori silme
     */
    public function deleteCategory()
    {
        if (!$this->categoryId) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('blog::admin.category_not_found'),
                'type' => 'error'
            ]);
            return;
        }

        try {
            $result = $this->categoryService->deleteCategory($this->categoryId);

            $this->dispatch('toast', [
                'title' => $result['success'] ? __('admin.success') : __('admin.error'),
                'message' => $result['message'],
                'type' => $result['success'] ? 'success' : 'error'
            ]);

            if ($result['success']) {
                return redirect()->route('admin.blog.category.index');
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }
}
