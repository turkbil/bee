<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Portfolio\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActionsQueue};
use Modules\Portfolio\App\Services\PortfolioService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use App\Traits\HasUniversalTranslation;
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class PortfolioComponent extends Component
{
    use WithPagination, WithBulkActionsQueue, InlineEditTitle, HasUniversalTranslation;

    private PortfolioService $portfolioService;

    public function boot(PortfolioService $portfolioService): void
    {
        $this->portfolioService = $portfolioService;
    }

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'portfolio_id';

    #[Url]
    public $sortDirection = 'desc';

    // Bulk actions properties (WithBulkActionsQueue trait için gerekli)
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    // Hibrit dil sistemi için dinamik dil listesi
    private ?array $availableSiteLanguages = null;

    // Event listeners
    protected $listeners = [
        'refreshPortfolioData' => 'refreshPortfolioData',
        'translationCompleted' => 'handleTranslationCompleted'
    ];

    public function refreshPortfolioData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->portfolioService->clearCache();

        // Component'i yeniden render et
        $this->render();
    }
    
    /**
     * Handle translation completed event from backend
     */
    public function handleTranslationCompleted($eventData)
    {
        \Log::info('🎉 PortfolioComponent - TranslationCompleted event received', $eventData);

        // Frontend'e completion event'ini dispatch et
        $this->dispatch('translation-complete', [
            'success' => true,
            'sessionId' => $eventData['sessionId'] ?? null,
            'entityType' => $eventData['entityType'] ?? 'portfolio',
            'entityId' => $eventData['entityId'] ?? null,
            'successCount' => $eventData['success'] ?? 0,
            'failedCount' => $eventData['failed'] ?? 0,
            'message' => 'Çeviri başarıyla tamamlandı!',
            'timestamp' => now()->toISOString()
        ]);

        // Sayfayı yenile
        $this->dispatch('refreshPortfolioData');

        \Log::info('✅ Frontend completion event dispatched');
    }

    protected function getModelClass()
    {
        return \Modules\Portfolio\App\Models\Portfolio::class;
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
            $result = $this->portfolioService->togglePortfolioStatus($id);

            $this->dispatch('toast', [
                'title' => $result['success'] ? __('admin.success') : __('admin.' . $result['type']),
                'message' => $result['message'],
                'type' => $result['type'],
            ]);

            if ($result['success'] && isset($result['data'])) {
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

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $this->siteLocale
        ];

        $portfolios = $this->portfolioService->getPaginatedPortfolios($filters, $this->perPage);

        return view('portfolio::admin.livewire.portfolio-component', [
            'portfolios' => $portfolios,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}