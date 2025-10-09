<?php

declare(strict_types=1);

namespace Modules\Blog\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Blog\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Blog\App\Services\BlogService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Blog\App\DataTransferObjects\BlogOperationResult;
use App\Traits\HasUniversalTranslation;
use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class BlogComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle, HasUniversalTranslation;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage;

    #[Url]
    public $sortField = 'blog_id';

    #[Url]
    public $sortDirection = 'desc';

    // Bulk actions properties (WithBulkActions trait için gerekli)
    // Note: Bu properties WithBulkActions trait'inde de tanımlı ama Livewire component'te
    // override edilmesi gerekiyor. Trait'teki default değerler component'e taşınmalı.

    // Hibrit dil sistemi için dinamik dil listesi
    private ?array $availableSiteLanguages = null;

    // Event listeners
    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
        'translationCompleted' => 'handleTranslationCompleted',
        'itemDeleted' => '$refresh',
        'bulkItemsDeleted' => '$refresh',
        'resetSelectAll' => 'resetSelectAll',
        'removeFromSelected' => 'removeFromSelected'
    ];

    private BlogService $blogService;

    public function boot(BlogService $blogService): void
    {
        $this->blogService = $blogService;
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
    }

    public function refreshPageData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->blogService->clearCache();

        // Component'i yeniden render et
        $this->render();
    }

    /**
     * Handle translation completed event from backend
     */
    public function handleTranslationCompleted($eventData)
    {
        \Log::info('🎉 NURU: BlogComponent - TranslationCompleted event received', $eventData);

        // Frontend'e completion event'ini dispatch et
        $this->dispatch('translation-complete', [
            'success' => true,
            'sessionId' => $eventData['sessionId'] ?? null,
            'entityType' => $eventData['entityType'] ?? 'blog',
            'entityId' => $eventData['entityId'] ?? null,
            'successCount' => $eventData['success'] ?? 0,
            'failedCount' => $eventData['failed'] ?? 0,
            'message' => 'Çeviri başarıyla tamamlandı!',
            'timestamp' => now()->toISOString()
        ]);

        // JavaScript'e direkt completion sinyali gönder
        $this->js('
            console.log("🎉 Translation completed - dispatching to modal");
            if (window.handleTranslationCompletion) {
                window.handleTranslationCompletion({
                    success: ' . ($eventData['success'] ?? 0) . ',
                    failed: ' . ($eventData['failed'] ?? 0) . ',
                    sessionId: "' . ($eventData['sessionId'] ?? '') . '"
                });
            }
        ');

        // Sayfayı yenile
        $this->dispatch('refreshPageData');

        \Log::info('✅ NURU: Frontend completion event dispatched');
    }

    protected function getModelClass()
    {
        return \Modules\Blog\App\Models\Blog::class;
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
            $result = $this->blogService->toggleBlogStatus($id);

            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
                'message' => $result->message,
                'type' => $result->type,
            ]);

            if ($result->success && $result->meta) {
                log_activity(
                    $result->data,
                    $result->meta['new_status'] ? 'etkinleştirildi' : 'devre-dışı'
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

        $blogs = $this->blogService->getPaginatedBlogs($filters, (int) $this->perPage);

        return view('blog::admin.livewire.blog-component', [
            'blogs' => $blogs,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }

    /**
     * 🌍 MODAL Bridge: JavaScript'den çağrılan çeviri metodu
     * Modal'dan gelen çeviri işlemlerini TranslateBlogJob'a yönlendirir
     */
    public function translateFromModal(array $data): array
    {
        try {
            Log::info('🚀 Translation modal ASYNC çeviri başlatıldı', [
                'blog_id' => $data['entityId'] ?? null,
                'source_language' => $data['sourceLanguage'] ?? null,
                'target_languages' => $data['targetLanguages'] ?? [],
                'user_id' => auth()->id()
            ]);

            // Veriyi standard translateContent formatına dönüştür
            $translationData = [
                'sourceLanguage' => $data['sourceLanguage'] ?? 'tr',
                'targetLanguages' => $data['targetLanguages'] ?? [],
                'fields' => ['title', 'body'], // Sabit alanlar
                'overwriteExisting' => $data['overwriteExisting'] ?? true
            ];

            // TranslateBlogJob kullanarak async çeviri başlat
            $blogId = $data['entityId'] ?? null;
            if (!$blogId) {
                throw new \Exception('Blog ID bulunamadı');
            }

            // Session ID oluştur (UUID v4 - globally unique)
            $sessionId = Str::uuid()->toString();

            // Job'u kuyruğa ekle
            Log::info('📦 TranslateBlogJob kuyruğa ekleniyor', [
                'blog_id' => $blogId,
                'source' => $translationData['sourceLanguage'],
                'targets' => $translationData['targetLanguages'],
                'queue_system' => 'tenant_isolated'
            ]);

            $job = \Modules\Blog\App\Jobs\TranslateBlogJob::dispatch(
                [$blogId], // Array olarak gönder
                $translationData['sourceLanguage'],
                $translationData['targetLanguages'],
                'balanced', // quality
                $translationData, // options
                $sessionId // operationId
            )->onQueue('tenant_isolated');

            Log::info('✅ TranslateBlogJob başarıyla kuyruğa eklendi', [
                'session_id' => $sessionId,
                'blog_id' => $blogId
            ]);

            // JavaScript'e translationQueued event'ini dispatch et
            $this->dispatch('translationQueued', [
                'sessionId' => $sessionId,
                'blogId' => $blogId,
                'success' => true,
                'message' => 'Çeviri kuyruğa başarıyla eklendi'
            ]);

            // JavaScript'e session ID döndür
            return [
                'success' => true,
                'session_id' => $sessionId,
                'message' => 'Çeviri kuyruğa başarıyla eklendi'
            ];
        } catch (\Exception $e) {
            Log::error('❌ Modal çeviri başlatma hatası', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
