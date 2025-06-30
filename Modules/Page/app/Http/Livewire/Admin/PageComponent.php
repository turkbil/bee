<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Page\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Page\App\Models\Page;
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
    
    public function refreshPageData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        
        \Log::info('🔄 PageComponent refreshPageData çağrıldı', [
            'new_site_locale' => $this->getSiteLocale(),
            'session_site_locale' => session('site_locale')
        ]);
        
        // Component'i yeniden render et
        $this->render();
    }

    protected function getModelClass()
    {
        return Page::class;
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
     * Admin arayüzü için admin_locale, sayfa içerikleri için site_locale kullan
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
            session(['site_locale' => $dataLangChanged]);
            session()->save();
            
            \Log::info('🎯 Site locale query string\'den güncellendi', [
                'query_param' => $dataLangChanged,
                'updated_session' => session('site_locale')
            ]);
            
            return $dataLangChanged;
        }
        
        $siteLocale = session('site_locale', 'tr');
        
        \Log::info('🔍 PageComponent getSiteLocale debug', [
            'session_site_locale' => session('site_locale'),
            'session_admin_locale' => session('admin_locale'),
            'query_data_lang_changed' => $dataLangChanged,
            'return_value' => $siteLocale,
            'all_session_data' => session()->all()
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
        $page = Page::where('page_id', $id)->first();
    
        if ($page) {
            // Eğer ana sayfa ise pasif yapılmasına izin verme
            if ($page->is_homepage && $page->is_active) {
                $this->dispatch('toast', [
                    'title' => __('admin.warning'),
                    'message' => __('page::messages.homepage_cannot_be_deactivated'),
                    'type' => 'warning',
                ]);
                return;
            }
            $page->update(['is_active' => !$page->is_active]);
            
            log_activity(
                $page,
                $page->is_active ? __('admin.activated') : __('admin.deactivated')
            );
    
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __($page->is_active ? 'page::messages.page_activated' : 'page::messages.page_deactivated', ['title' => $page->getTranslated('title', $this->getSiteLocale()) ?? $page->getTranslated('title', 'tr')]),
                'type' => $page->is_active ? 'success' : 'warning',
            ]);
        }
    }

    public function render()
    {
        $siteLanguages = $this->getAvailableSiteLanguages();
        $currentSiteLocale = $this->getSiteLocale();
        
        // Debug log - veri dili kontrolü
        \Log::info('📊 PageComponent render çağrıldı', [
            'current_site_locale' => $currentSiteLocale,
            'session_site_locale' => session('site_locale'),
            'session_admin_locale' => session('admin_locale'),
            'available_site_languages' => $siteLanguages,
            'request_query_params' => request()->query()
        ]);
        
        $query = Page::where(function ($query) use ($siteLanguages) {
            if (!empty($this->search)) {
                // Dinamik dil arama - site_languages tablosundan
                $searchTerm = '%' . $this->search . '%';
                foreach ($siteLanguages as $lang) {
                    $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$lang}')) LIKE ?", [$searchTerm])
                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$lang}')) LIKE ?", [$searchTerm]);
                }
            }
        });
    
        // Sorting: JSON field'lar için özel sıralama - site dilini kullan
        if ($this->sortField === 'title') {
            $pages = $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$currentSiteLocale}')) {$this->sortDirection}")
                ->paginate($this->perPage);
        } else {
            $pages = $query->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        }
    
        return view('page::admin.livewire.page-component', [
            'pages' => $pages,
            'currentSiteLocale' => $currentSiteLocale,
            'siteLanguages' => $siteLanguages,
        ]);
    }
}