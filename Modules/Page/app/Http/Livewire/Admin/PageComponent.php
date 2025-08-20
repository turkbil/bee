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
use App\Traits\HasUniversalTranslation;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class PageComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle, HasUniversalTranslation;

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
    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
        'translationCompleted' => 'handleTranslationCompleted'
    ];
    
    private PageService $pageService;
    
    public function boot(PageService $pageService): void
    {
        $this->pageService = $pageService;
    }
    
    public function refreshPageData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->pageService->clearCache();
        
        // Component'i yeniden render et
        $this->render();
    }
    
    /**
     * Handle translation completed event from backend
     */
    public function handleTranslationCompleted($eventData)
    {
        \Log::info('🎉 NURU: PageComponent - TranslationCompleted event received', $eventData);
        
        // Frontend'e completion event'ini dispatch et
        $this->dispatch('translation-complete', [
            'success' => true,
            'sessionId' => $eventData['sessionId'] ?? null,
            'entityType' => $eventData['entityType'] ?? 'page',
            'entityId' => $eventData['entityId'] ?? null,
            'message' => 'Çeviri başarıyla tamamlandı!',
            'timestamp' => now()->toISOString()
        ]);
        
        // Sayfayı yenile
        $this->dispatch('refreshPageData');
        
        \Log::info('✅ NURU: Frontend completion event dispatched');
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
     * AI Translation for single page - PageManageComponent ile aynı format
     * Unified translation method for both PageManageComponent and PageComponent
     */
    public function translateContent($data, int $pageId = null): void
    {
        // Data'dan parametreleri çıkar (PageManageComponent formatı)
        $sourceLanguage = $data['sourceLanguage'] ?? 'tr';
        $targetLanguages = $data['targetLanguages'] ?? [];
        $fields = $data['fields'] ?? ['title', 'body'];
        $overwriteExisting = $data['overwriteExisting'] ?? true;
        
        // Eğer pageId verilmemişse ve mevcut sayfa varsa onu kullan
        if (!$pageId && isset($this->pageId)) {
            $pageId = $this->pageId;
        }
        
        if (!$pageId) {
            $this->dispatch('toast', [
                'title' => 'Çeviri Hatası',
                'message' => 'Sayfa ID bulunamadı',
                'type' => 'error'
            ]);
            return;
        }

        // Execution time'ı artır (çeviri işlemi uzun sürebilir)
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        try {
            \Log::info("🔄 PAGE LISTING ÇEVİRİ BAŞLADI", [
                'page_id' => $pageId,
                'source' => $sourceLanguage, 
                'targets' => $targetLanguages,
                'fields' => $fields
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
                    foreach ($fields as $field) {
                        $sourceText = $page->getTranslated($field, $sourceLanguage);
                        
                        if (empty($sourceText)) {
                            continue;
                        }

                        // Mevcut çeviri kontrolü
                        $existingTranslation = $page->getTranslated($field, $targetLanguage);
                        if (!$overwriteExisting && !empty($existingTranslation)) {
                            continue;
                        }

                        // AI çeviri yap (Universal Translation System)
                        $translatedText = app(\Modules\AI\App\Services\AIService::class)->translateText($sourceText, $sourceLanguage, $targetLanguage);
                        
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

                // JavaScript'e çeviri tamamlandı event'ini gönder
                $this->dispatch('translation-complete', [
                    'page_id' => $pageId,
                    'translated_count' => $translatedCount,
                    'success' => true
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

    /**
     * 🌍 Translation Modal için çeviri işlemi
     * claude_ai.md uyumlu - conversation tracking ve credit deduction ile
     */
    public function translateFromModal(int $pageId, string $sourceLanguage, array $targetLanguages): void
    {
        try {
            Log::info('🌍 Translation modal çeviri başlatıldı', [
                'page_id' => $pageId,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'user_id' => auth()->id()
            ]);

            // Page'i bul
            $page = Page::find($pageId);
            if (!$page) {
                $this->dispatch('translationError', 'Page bulunamadı');
                return;
            }

            // Her hedef dil için çeviri yap
            $translatedCount = 0;
            $errors = [];

            foreach ($targetLanguages as $targetLanguage) {
                try {
                    // Kaynak dil verilerini al
                    $sourceTitle = $page->getTranslated('title', $sourceLanguage);
                    $sourceBody = $page->getTranslated('body', $sourceLanguage);

                    if (empty($sourceTitle) && empty($sourceBody)) {
                        $errors[] = "Kaynak dil ({$sourceLanguage}) verileri bulunamadı";
                        continue;
                    }

                    $translatedData = [];

                    // Title çevir
                    if (!empty($sourceTitle)) {
                        $translatedTitle = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceTitle,
                            $sourceLanguage,
                            $targetLanguage,
                            ['context' => 'page_title', 'source' => 'translation_modal']
                        );
                        $translatedData['title'] = $translatedTitle;
                    }

                    // Body çevir
                    if (!empty($sourceBody)) {
                        $translatedBody = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceBody,
                            $sourceLanguage,
                            $targetLanguage,
                            ['context' => 'page_content', 'source' => 'translation_modal', 'preserve_html' => true]
                        );
                        $translatedData['body'] = $translatedBody;
                    }

                    // Slug oluştur
                    if (!empty($translatedData['title'])) {
                        $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                            Page::class,
                            $translatedData['title'],
                            $targetLanguage,
                            'slug',
                            'page_id',
                            $pageId
                        );
                    }

                    // Çevrilmiş verileri kaydet
                    if (!empty($translatedData)) {
                        foreach ($translatedData as $field => $value) {
                            $currentData = $page->{$field} ?? [];
                            $currentData[$targetLanguage] = $value;
                            $page->{$field} = $currentData;
                        }
                        $page->save();
                        $translatedCount++;

                        Log::info('✅ Page çevirisi tamamlandı', [
                            'page_id' => $pageId,
                            'target_language' => $targetLanguage,
                            'fields' => array_keys($translatedData)
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Çeviri hatası ({$targetLanguage}): " . $e->getMessage();
                    Log::error('❌ Page çeviri hatası', [
                        'page_id' => $pageId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Session ID oluştur ve döndür
            $sessionId = 'translation_' . uniqid();
            
            // Başarı mesajı
            if ($translatedCount > 0) {
                $message = "{$translatedCount} dil için çeviri tamamlandı";
                if (!empty($errors)) {
                    $message .= ". " . count($errors) . " hata oluştu";
                }
                
                $this->dispatch('translationQueued', [
                    'sessionId' => $sessionId,
                    'success' => true,
                    'message' => $message,
                    'translatedCount' => $translatedCount,
                    'errors' => $errors
                ]);
                
                // Sayfayı yenile
                $this->dispatch('refreshPageData');
            } else {
                $this->dispatch('translationError', 'Hiçbir çeviri yapılamadı: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            Log::error('❌ Translation modal genel hatası', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Çeviri işlemi başarısız: ' . $e->getMessage());
        }
    }

    public function queueTranslation($pageId, $sourceLanguage, $targetLanguages, $overwriteExisting = true)
    {
        try {
            \Log::info("🚀 QUEUE Translation başlatıldı", [
                'page_id' => $pageId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages
            ]);

            // Job'ı kuyruğa ekle
            \Modules\AI\app\Jobs\TranslateEntityJob::dispatch(
                'page',
                $pageId,
                $sourceLanguage,
                $targetLanguages,
                $overwriteExisting
            );

            $this->dispatch('translationQueued', 'Çeviri işlemi başlatıldı!');
            
        } catch (\Exception $e) {
            \Log::error('❌ Queue translation hatası', [
                'page_id' => $pageId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Çeviri kuyruğu hatası: ' . $e->getMessage());
        }
    }
}