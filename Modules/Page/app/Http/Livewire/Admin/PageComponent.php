<?php

declare(strict_types=1);

namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Page\App\Services\PageService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Page\App\DataTransferObjects\PageOperationResult;
use Modules\AI\App\Services\AIService;
use Modules\Page\App\Models\Page;

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
    private ?array $availableSiteLanguages = null;
    
    // Event listeners
    protected $listeners = ['refreshPageData' => 'refreshPageData'];
    
    private PageService $pageService;
    private AIService $aiService;
    
    public function boot(PageService $pageService, AIService $aiService): void
    {
        $this->pageService = $pageService;
        $this->aiService = $aiService;
    }
    
    public function refreshPageData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->pageService->clearCache();
        
        // Component'i yeniden render et
        $this->render();
    }

    protected function getModelClass()
    {
        return \Modules\Page\App\Models\Page::class;
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
            $result = $this->pageService->togglePageStatus($id);
            
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

    /**
     * AI Translation for modal calls - JavaScript'den çağrılabilir
     * Modal'dan gelen çağrıları işler
     */
    public function translateFromModal(int $pageId, string $sourceLanguage, array $targetLanguages): void
    {
        $this->translateContent($pageId, $sourceLanguage, $targetLanguages);
        
        // Modal'ı kapat ve sayfayı yenile
        $this->dispatch('closeTranslationModal');
        $this->dispatch('refreshComponent');
    }

    /**
     * AI Translation for single page
     * Adapted from PageManageComponent with simplifications
     */
    public function translateContent(int $pageId, string $sourceLanguage, array $targetLanguages): void
    {
        // Execution time'ı artır (çeviri işlemi uzun sürebilir)
        set_time_limit(0); // No time limit
        ini_set('max_execution_time', 0);
        
        try {
            \Log::info("🔄 PAGE LISTING ÇEVİRİ BAŞLADI", [
                'page_id' => $pageId,
                'source' => $sourceLanguage, 
                'targets' => $targetLanguages
            ]);

            // Sayfayı bul
            $page = Page::findOrFail($pageId);

            $translatedCount = 0;
            $messages = [];

            foreach ($targetLanguages as $targetLanguage) {
                if ($sourceLanguage === $targetLanguage) {
                    continue;
                }

                try {
                    // Tüm alanları çevir
                    $fieldsToTranslate = ['title', 'body'];
                    
                    foreach ($fieldsToTranslate as $field) {
                        $sourceText = $page->getTranslated($field, $sourceLanguage);
                        
                        if (empty($sourceText)) {
                            continue;
                        }

                        // AI çeviri yap
                        $translatedText = $this->aiService->translateText($sourceText, $sourceLanguage, $targetLanguage);
                        
                        if (!empty($translatedText) && $translatedText !== $sourceText) {
                            // Mevcut veriyi al
                            $currentData = $page->{$field};
                            if (is_string($currentData)) {
                                $currentData = json_decode($currentData, true) ?: [];
                            }
                            
                            // Çeviriyi ekle
                            $currentData[$targetLanguage] = $translatedText;
                            
                            // Güncelle
                            $page->update([$field => $currentData]);
                            
                            \Log::info("✅ Çeviri başarılı", [
                                'field' => $field,
                                'target' => $targetLanguage,
                                'original' => substr($sourceText, 0, 100),
                                'translated' => substr($translatedText, 0, 100)
                            ]);
                        }
                    }

                    $translatedCount++;
                    $messages[] = strtoupper($targetLanguage) . ' çevirisi';

                } catch (\Exception $e) {
                    \Log::error("❌ Çeviri hatası", [
                        'page_id' => $pageId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                    
                    $this->dispatch('toast', [
                        'title' => 'Çeviri Hatası',
                        'message' => strtoupper($targetLanguage) . ' çevirisi başarısız: ' . $e->getMessage(),
                        'type' => 'error'
                    ]);
                }
            }

            if ($translatedCount > 0) {
                $this->dispatch('toast', [
                    'title' => 'Çeviri Tamamlandı',
                    'message' => implode(', ', $messages) . ' başarıyla kaydedildi',
                    'type' => 'success'
                ]);

                \Log::info("🎉 PAGE LISTING ÇEVİRİ TAMAMLANDI", [
                    'page_id' => $pageId,
                    'translated_count' => $translatedCount,
                    'messages' => $messages
                ]);
            }

        } catch (\Exception $e) {
            \Log::error("💥 PAGE LISTING ÇEVİRİ GENEL HATA", [
                'page_id' => $pageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Çeviri Sistemi Hatası',
                'message' => 'Çeviri işlemi başarısız oldu: ' . $e->getMessage(),
                'type' => 'error'
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
        
        $pages = $this->pageService->getPaginatedPages($filters, $this->perPage);
    
        return view('page::admin.livewire.page-component', [
            'pages' => $pages,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}