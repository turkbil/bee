<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\Muzibu\App\Services\MuzibuCategoryService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Muzibu\App\Models\MuzibuCategory;

#[Layout('admin.layout')]
class MuzibuCategoryComponent extends Component
{
    public $currentLanguage = 'tr';
    public $availableLanguages = [];
    public $multiLangInputs = [];
    public $inputs = [];

    // Tab Configuration
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // Category form data
    public $title = '';
    public $slug = '';
    public $description = '';
    public $sort_order = 0;
    public $is_active = true;
    public $parent_id = null;
    public $editingCategoryId = null;

    // Search Property
    public $search = '';

    private MuzibuCategoryService $categoryService;

    // Livewire Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'tab-changed' => 'handleTabChange',
        'switchLanguage' => 'switchLanguage',
        'js-language-sync' => 'handleJavaScriptLanguageSync',
        'updateOrder' => 'updateOrder',
        'categoryDeleted' => 'handleCategoryDeleted',
        'refreshPage' => '$refresh'
    ];

    public function boot(MuzibuCategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    public function mount(): void
    {
        // Initialize available languages for global language switcher
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        // Set default current language
        $this->currentLanguage = $this->availableLanguages[0] ?? 'tr';

        // Initialize tab configuration
        $this->tabConfig = [
            ['name' => __('muzibu::admin.new_category'), 'icon' => 'fas fa-plus']
        ];

        $this->tabCompletionStatus = [0 => true];

        $this->initializeFormData();
    }

    #[Computed]
    public function availableLanguages()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function categories()
    {
        // Kategori listesi - hiyerarşik sıralama ile
        $query = MuzibuCategory::withCount('muzibus');

        // Search filter
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                // JSON title alanında arama
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr'))) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE ?", ["%{$search}%"])
                  // Slug arama
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr'))) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.en'))) LIKE ?", ["%{$search}%"]);
            });
        }

        $items = $query->get();

        // Hiyerarşik sıralama
        return $this->buildHierarchicalList($items);
    }

    /**
     * Kategorileri hiyerarşik olarak sırala
     * Parent kategoriler ve onların altında childları
     */
    private function buildHierarchicalList($categories)
    {
        $result = collect([]);

        // Parent kategorileri al (parent_id = null)
        $parents = $categories->whereNull('parent_id')->sortBy('sort_order')->values();

        foreach ($parents as $parent) {
            // Parent'ı ekle
            $result->push($parent);

            // Bu parent'ın childlarını recursive olarak ekle
            $this->addChildren($result, $categories, $parent->category_id);
        }

        return $result;
    }

    /**
     * Recursive olarak child kategorileri ekle
     */
    private function addChildren($result, $allCategories, $parentId)
    {
        $children = $allCategories->where('parent_id', $parentId)->sortBy('sort_order')->values();

        foreach ($children as $child) {
            $result->push($child);

            // Bu child'ın da childları varsa onları da ekle
            $this->addChildren($result, $allCategories, $child->category_id);
        }
    }

    /**
     * Dropdown için hiyerarşik kategori listesi
     * Depth level'a göre "─" prefix ekler
     */
    #[Computed]
    public function hierarchicalCategories()
    {
        $categories = MuzibuCategory::get();

        // Hiyerarşik sıralama
        $sorted = $this->buildHierarchicalList($categories);

        return $sorted->map(function($category) {
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

    public function addCategory(): void
    {
        // Validation rules
        $validationRules = [];

        // Multi-language title validation
        foreach ($this->multiLangInputs as $lang => $data) {
            if (!empty($data['title'])) {
                $validationRules["multiLangInputs.{$lang}.title"] = 'required|string|max:255';
            }
        }

        // En az bir dil dolu olmalı
        $hasContent = false;
        foreach ($this->multiLangInputs as $data) {
            if (!empty($data['title'])) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent) {
            $this->addError('multiLangInputs', 'En az bir dil için kategori adı girilmelidir.');
            return;
        }

        $this->validate($validationRules);

        try {
            // Title array oluştur
            $titleArray = [];
            $slugArray = [];
            $descriptionArray = [];

            foreach ($this->multiLangInputs as $lang => $data) {
                if (!empty($data['title'])) {
                    $titleArray[$lang] = $data['title'];
                }
                if (!empty($data['slug'])) {
                    $slugArray[$lang] = $data['slug'];
                }
                if (!empty($data['description'])) {
                    $descriptionArray[$lang] = $data['description'];
                }
            }

            // Yeni kategori her zaman listenin sonuna eklensin (max sort_order + 1)
            $maxOrder = MuzibuCategory::max('sort_order') ?? -1;
            $sortOrder = $maxOrder + 1;

            $data = [
                'title' => $titleArray,
                'slug' => $slugArray,
                'description' => $descriptionArray,
                'sort_order' => $sortOrder,
                'is_active' => $this->is_active,
                'parent_id' => $this->parent_id,
            ];

            $result = $this->categoryService->createCategory($data);

            if ($result['success']) {
                $this->dispatch('toast', [
                    'title' => __('admin.success'),
                    'message' => $result['message'],
                    'type' => 'success'
                ]);

                $this->resetForm();

                // Manuel sortable refresh
                $this->dispatch('refresh-sortable');
            } else {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => $result['message'],
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed') . ': ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function editCategory(int $categoryId): void
    {
        try {
            $category = MuzibuCategory::findOrFail($categoryId);

            // Form alanlarını doldur
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang]['title'] = $category->getTranslated('title', $lang) ?? '';
                $this->multiLangInputs[$lang]['slug'] = $category->getTranslated('slug', $lang) ?? '';
                $this->multiLangInputs[$lang]['description'] = $category->getTranslated('description', $lang) ?? '';
            }

            $this->sort_order = $category->sort_order;
            $this->is_active = $category->is_active;
            $this->editingCategoryId = $categoryId;

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.category_loaded_for_editing'),
                'type' => 'info',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.category_load_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function deleteCategory($categoryId): void
    {
        try {
            $result = $this->categoryService->deleteCategory($categoryId);

            $this->dispatch('toast', [
                'title' => $result['success'] ? __('admin.success') : __('admin.error'),
                'message' => $result['message'],
                'type' => $result['success'] ? 'success' : 'error'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function toggleCategoryStatus(int $categoryId): void
    {
        try {
            $result = $this->categoryService->toggleCategoryStatus($categoryId);

            $this->dispatch('toast', [
                'title' => $result['success'] ? __('admin.success') : __('admin.error'),
                'message' => $result['message'],
                'type' => $result['type'],
            ]);

            if ($result['success']) {
                $this->dispatch('refresh-sortable');
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.category_status_update_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function updateOrder($list = null)
    {
        try {
            \Log::info('🚨 Category UPDATEORDER METODU ÇAĞRILDI!', [
                'received_list' => $list,
                'list_type' => gettype($list),
                'timestamp' => now()->format('H:i:s')
            ]);

            $items = $list;

            if (!is_array($items) || empty($items)) {
                \Log::error('❌ updateOrder: Geçersiz items parametresi', ['items' => $items]);
                return;
            }

            // Her kategori için sort_order ve parent_id güncelle
            foreach ($items as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    $updateData = ['sort_order' => $item['order']];

                    // Parent ID kontrolü - null ise null yap, değer varsa value'sunu kullan
                    if (array_key_exists('parentId', $item)) {
                        // JavaScript'ten gelen string'i int'e çevir
                        $parentId = $item['parentId'] === null ? null : (int)$item['parentId'];
                        $updateData['parent_id'] = $parentId;
                    }

                    MuzibuCategory::where('category_id', $item['id'])
                        ->update($updateData);
                }
            }

            $this->categoryService->clearCache();

            // Livewire computed property cache'ini temizle
            unset($this->categories);

            \Log::info('✅ Category drag-drop başarılı', ['updated_items' => count($items)]);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.category_order_updated'),
                'type' => 'success',
                'duration' => 3000
            ]);

            // Manuel sortable refresh
            $this->dispatch('refresh-sortable');

        } catch (\Exception $e) {
            \Log::error('🚨 Category updateOrder exception', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function openDeleteModal(int $categoryId, string $title)
    {
        // Global modal'ı tetikle
        $this->dispatch('showCategoryDeleteModal',
            module: 'muzibu',
            id: $categoryId,
            title: $title
        );
    }

    private function initializeFormData(): void
    {
        $this->multiLangInputs = [];
        foreach ($this->availableLanguages as $languageCode) {
            $this->multiLangInputs[$languageCode] = [
                'title' => '',
                'slug' => '',
                'description' => ''
            ];
        }

        $this->resetForm();
    }

    private function resetForm(): void
    {
        foreach ($this->availableLanguages as $languageCode) {
            $this->multiLangInputs[$languageCode] = [
                'title' => '',
                'slug' => '',
                'description' => ''
            ];
        }

        $this->sort_order = 0;
        $this->is_active = true;
        $this->parent_id = null;
    }

    /**
     * Language switcher method - MenuManagement pattern
     */
    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $oldLanguage = $this->currentLanguage;
            $this->currentLanguage = $language;

            // Session'a kaydet
            session(['category_manage_language' => $language]);

            // KRİTİK FİX: Dil değişince form verilerini kontrol et ve initialize et
            if (!isset($this->multiLangInputs[$language]) || empty($this->multiLangInputs[$language])) {
                $this->multiLangInputs[$language] = [
                    'title' => '',
                    'slug' => '',
                    'description' => ''
                ];
            }

            \Log::info('🎯 MuzibuCategoryComponent switchLanguage', [
                'old_language' => $oldLanguage,
                'new_language' => $language,
                'current_language' => $this->currentLanguage,
                'is_successfully_changed' => $this->currentLanguage === $language,
                'form_data_ready' => isset($this->multiLangInputs[$language])
            ]);

            // JavaScript'e dil değişikliğini bildir
            $this->dispatch('language-switched', [
                'language' => $language,
                'editorId' => "editor_{$language}",
                'content' => $this->multiLangInputs[$language]['title'] ?? ''
            ]);
        }
    }

    // JavaScript Language Sync Handler
    public function handleJavaScriptLanguageSync($data)
    {
        $jsLanguage = $data['language'] ?? '';
        $oldLanguage = $this->currentLanguage;

        \Log::info('🚨 Category handleJavaScriptLanguageSync', [
            'js_language' => $jsLanguage,
            'current_language' => $this->currentLanguage,
            'data' => $data
        ]);

        if (in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage) {
            $this->currentLanguage = $jsLanguage;

            // JavaScript'e confirmation gönder
            $this->dispatch('language-sync-completed', [
                'language' => $jsLanguage,
                'oldLanguage' => $oldLanguage,
                'success' => true
            ]);
        }
    }

    // Tab Change Handler
    public function handleTabChange($data)
    {
        \Log::info('🔄 Category Tab değişti', [
            'tab_data' => $data,
            'current_language' => $this->currentLanguage
        ]);
    }

    public function handleCategoryDeleted()
    {
        // Sadece component'i refresh et, sayfa yenileme YOK
        // Livewire otomatik olarak categories listesini güncelleyecek
    }

    public function render()
    {
        return view('muzibu::admin.livewire.category-component', [
            'categories' => $this->categories,
            'availableLanguages' => $this->availableLanguages
        ]);
    }
}
