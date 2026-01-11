<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\MenuManagement\App\Http\Livewire\Traits\WithBulkActionsQueue;
use Modules\MenuManagement\App\Services\MenuService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\MenuManagement\App\DataTransferObjects\MenuOperationResult;
use Modules\MenuManagement\App\Models\Menu;

#[Layout('admin.layout')]
class MenuComponent extends Component
{
    use WithPagination, WithBulkActionsQueue;

    // Bulk actions properties (WithBulkActionsQueue trait için gerekli)
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'menu_id';

    #[Url]
    public $sortDirection = 'asc';
    
    #[Url]
    public $statusFilter = 'all';
    
    #[Url]
    public $locationFilter = 'all';

    // Hibrit dil sistemi için dinamik dil listesi
    private ?array $availableSiteLanguages = null;
    
    // Event listeners - trait'ten gelen listeners ile merge edilecek
    protected function getListeners()
    {
        $traitListeners = [];
        if (method_exists($this, 'getTraitListeners')) {
            $traitListeners = $this->getTraitListeners();
        }
        
        return array_merge(
            $traitListeners,
            ['refreshMenuData' => 'refreshMenuData']
        );
    }
    
    protected function getModelClass()
    {
        return Menu::class;
    }
    
    private MenuService $menuService;
    
    public function boot(MenuService $menuService): void
    {
        $this->menuService = $menuService;
    }
    
    public function refreshMenuData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->menuService->clearCache();
        
        // Component'i yeniden render et
        $this->render();
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
        return session('admin_locale', 'tr');
    }

    #[Computed]
    public function siteLocale(): string
    {
        // Query string'den data_lang_changed parametresini kontrol et
        $dataLangChanged = request()->get('data_lang_changed');
        
        // Eğer query string'de dil değişim parametresi varsa onu kullan
        if ($dataLangChanged && in_array($dataLangChanged, $this->availableSiteLanguages)) {
            // Session'ı da güncelle (query'den gelen dili session'a yaz)
            session(['tenant_locale' => $dataLangChanged]);
            session()->save();
            
            return $dataLangChanged;
        }
        
        // 1. Kullanıcının kendi tenant_locale tercihi (en yüksek öncelik)
        if (auth()->check() && auth()->user()->tenant_locale) {
            $userLocale = auth()->user()->tenant_locale;
            
            // Session'ı da güncelle
            if (session('tenant_locale') !== $userLocale) {
                session(['tenant_locale' => $userLocale]);
            }
            
            return $userLocale;
        }
        
        // 2. Session fallback
        return session('tenant_locale', 'tr');
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
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatedLocationFilter()
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
            $result = $this->menuService->toggleMenuStatus($id);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success && $result->meta) {
                log_activity(
                    $result->data,
                    $result->meta['new_status'] ? __('admin.activated') : __('admin.deactivated')
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
    
    public function deleteMenu(int $id): void
    {
        try {
            $result = $this->menuService->deleteMenu($id);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success) {
                log_activity(
                    $result->data,
                    __('admin.deleted')
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
    
    public function duplicateMenu(int $id): void
    {
        try {
            $result = $this->menuService->duplicateMenu($id);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success) {
                log_activity(
                    $result->data,
                    __('menumanagement::admin.menu_duplicated_successfully')
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

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'location' => $this->locationFilter,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $this->siteLocale
        ];
        
        $menus = $this->menuService->getPaginatedMenus($filters, $this->perPage);
    
        return view('menumanagement::admin.livewire.menu-component', [
            'menus' => $menus,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}