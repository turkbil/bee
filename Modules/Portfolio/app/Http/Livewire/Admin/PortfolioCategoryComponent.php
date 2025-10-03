<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Portfolio\App\Services\PortfolioCategoryService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class PortfolioCategoryComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 15;

    #[Url]
    public $sortField = 'category_id';

    #[Url]
    public $sortDirection = 'desc';

    // Bulk actions properties
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    // Inline editing
    public $editingTitleId = null;
    public $newTitle = '';

    // Hibrit dil sistemi için dinamik dil listesi
    private ?array $availableSiteLanguages = null;

    // Event listeners
    protected $listeners = [
        'refreshCategoryData' => 'refreshCategoryData',
    ];

    private PortfolioCategoryService $categoryService;

    public function boot(PortfolioCategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    public function refreshCategoryData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->categoryService->clearCache();

        // Component'i yeniden render et
        $this->render();
    }

    protected function getModelClass()
    {
        return \Modules\Portfolio\App\Models\PortfolioCategory::class;
    }

    #[Computed]
    public function availableSiteLanguages(): array
    {
        return $this->availableSiteLanguages ??= TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    #[Computed]
    public function adminLocale(): string
    {
        return session('admin_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }

    #[Computed]
    public function siteLocale(): string
    {
        // Query string'den data_lang_changed parametresini kontrol et
        $dataLangChanged = request()->get('data_lang_changed');

        // Eğer query string'de dil değişim parametresi varsa onu kullan
        if ($dataLangChanged && in_array($dataLangChanged, $this->availableSiteLanguages)) {
            // Session'ı da güncelle
            session(['tenant_locale' => $dataLangChanged]);
            session()->save();

            return $dataLangChanged;
        }

        // 1. Kullanıcının kendi tenant_locale tercihi
        if (auth()->check() && auth()->user()->tenant_locale) {
            $userLocale = auth()->user()->tenant_locale;

            // Session'ı da güncelle
            if (session('tenant_locale') !== $userLocale) {
                session(['tenant_locale' => $userLocale]);
            }

            return $userLocale;
        }

        // 2. Session fallback
        return session('tenant_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $result = $this->categoryService->toggleCategoryStatus($id);

            $this->dispatch('toast', [
                'title' => $result['success'] ? __('admin.success') : __('admin.' . $result['type']),
                'message' => $result['message'],
                'type' => $result['type'],
            ]);

            if ($result['success'] && $result['meta']) {
                log_activity(
                    $result['data'],
                    $result['meta']['new_status'] ? 'etkinleştirildi' : 'devre-dışı'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    // Inline editing methods
    public function startEditingTitle($id, $currentTitle)
    {
        $this->editingTitleId = $id;
        $this->newTitle = $currentTitle;
    }

    public function updateTitleInline()
    {
        if (!$this->editingTitleId) {
            return;
        }

        $category = PortfolioCategory::where('category_id', $this->editingTitleId)->first();

        if ($category) {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['name' => $this->newTitle],
                ['name' => 'required|string|max:191']
            );

            if ($validator->fails()) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('admin.title_validation_error'),
                    'type' => 'error',
                ]);
                return;
            }

            $currentSiteLocale = $this->siteLocale;

            // Mevcut başlık değerini kontrol et
            $currentTitle = $category->getTranslated('name', $currentSiteLocale);
            if ($currentTitle === $this->newTitle) {
                $this->editingTitleId = null;
                $this->newTitle = '';
                return;
            }

            // JSON name güncelle
            $names = is_array($category->name) ? $category->name : [];
            $oldTitle = $names[$currentSiteLocale] ?? '';
            $names[$currentSiteLocale] = \Illuminate\Support\Str::limit($this->newTitle, 191, '');
            $category->name = $names;
            $category->save();

            log_activity(
                $category,
                __('admin.title_updated'),
                ['old' => $oldTitle, 'new' => $names[$currentSiteLocale], 'locale' => $currentSiteLocale]
            );

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.title_updated_successfully'),
                'type' => 'success',
            ]);
        }

        $this->editingTitleId = null;
        $this->newTitle = '';
        $this->dispatch('refresh');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $this->siteLocale
        ];

        $categories = $this->categoryService->getPaginatedCategories($filters, $this->perPage);

        return view('portfolio::admin.livewire.category-component', [
            'categories' => $categories,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}
