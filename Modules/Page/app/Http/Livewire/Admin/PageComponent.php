<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Page\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Page\App\Services\PageService;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class PageComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'page_id';

    #[Url]
    public $sortDirection = 'desc';

    // Hibrit dil sistemi için dinamik dil listesi
    protected $availableSiteLanguages = null;
    
    // Event listeners
    protected $listeners = ['refreshPageData' => 'refreshPageData'];
    
    public function __construct()
    {
        $this->pageService = app(PageService::class);
    }
    
    protected PageService $pageService;
    
    public function refreshPageData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->pageService->clearCache();
        
        \Log::info('🔄 PageComponent refreshPageData çağrıldı', [
            'new_tenant_locale' => $this->getSiteLocale(),
            'session_tenant_locale' => session('tenant_locale')
        ]);
        
        // Component'i yeniden render et
        $this->render();
    }

    protected function getModelClass()
    {
        return \Modules\Page\App\Models\Page::class;
    }

    /**
     * Site dillerini dinamik olarak getir
     */
    protected function getAvailableSiteLanguages()
    {
        if ($this->availableSiteLanguages === null) {
            $this->availableSiteLanguages = TenantLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->toArray();
        }
        return $this->availableSiteLanguages;
    }

    /**
     * Admin arayüzü için admin_locale, sayfa içerikleri için tenant_locale kullan
     */
    protected function getAdminLocale()
    {
        return session('admin_locale', 'tr');
    }

    protected function getSiteLocale()
    {
        // Query string'den data_lang_changed parametresini kontrol et
        $dataLangChanged = request()->get('data_lang_changed');
        
        // Eğer query string'de dil değişim parametresi varsa onu kullan
        if ($dataLangChanged && in_array($dataLangChanged, $this->getAvailableSiteLanguages())) {
            // Session'ı da güncelle (query'den gelen dili session'a yaz)
            session(['tenant_locale' => $dataLangChanged]);
            session()->save();
            
            \Log::info('🎯 Site locale query string\'den güncellendi', [
                'query_param' => $dataLangChanged,
                'updated_session' => session('tenant_locale')
            ]);
            
            return $dataLangChanged;
        }
        
        // 1. Kullanıcının kendi tenant_locale tercihi (en yüksek öncelik)
        if (auth()->check() && auth()->user()->tenant_locale) {
            $userLocale = auth()->user()->tenant_locale;
            
            // Session'ı da güncelle
            if (session('tenant_locale') !== $userLocale) {
                session(['tenant_locale' => $userLocale]);
            }
            
            \Log::info('🔍 getSiteLocale - User tenant_locale kullanıldı', [
                'user_locale' => $userLocale,
                'session_updated' => true
            ]);
            
            return $userLocale;
        }
        
        // 2. Session fallback
        $siteLocale = session('tenant_locale', 'tr');
        
        \Log::info('🔍 PageComponent getSiteLocale debug', [
            'session_tenant_locale' => session('tenant_locale'),
            'session_admin_locale' => session('admin_locale'),
            'query_data_lang_changed' => $dataLangChanged,
            'return_value' => $siteLocale,
            'user_tenant_locale' => auth()->check() ? auth()->user()->tenant_locale : 'NOT_AUTH'
        ]);
        
        return $siteLocale;
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

    public function toggleActive($id)
    {
        $result = $this->pageService->togglePageStatus($id);
        
        $this->dispatch('toast', [
            'title' => $result['success'] ? __('admin.success') : __('admin.' . $result['type']),
            'message' => $result['message'],
            'type' => $result['type'],
        ]);
        
        if ($result['success']) {
            log_activity(
                $this->pageService->getPage($id),
                $result['new_status'] ? __('admin.activated') : __('admin.deactivated')
            );
        }
    }

    public function render()
    {
        $siteLanguages = $this->getAvailableSiteLanguages();
        $currentSiteLocale = $this->getSiteLocale();
        
        // Debug log - veri dili kontrolü
        \Log::info('📊 PageComponent render çağrıldı', [
            'current_tenant_locale' => $currentSiteLocale,
            'session_tenant_locale' => session('tenant_locale'),
            'session_admin_locale' => session('admin_locale'),
            'available_tenant_languages' => $siteLanguages,
            'request_query_params' => request()->query()
        ]);
        
        // Service kullanarak paginated data al
        $filters = [
            'search' => $this->search,
            'locales' => $siteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $currentSiteLocale
        ];
        
        $pages = $this->pageService->getPaginatedPages($filters, $this->perPage);
    
        return view('page::admin.livewire.page-component', [
            'pages' => $pages,
            'currentSiteLocale' => $currentSiteLocale,
            'siteLanguages' => $siteLanguages,
        ]);
    }
}