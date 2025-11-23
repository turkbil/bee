<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Muzibu\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Muzibu\App\Services\GenreService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Muzibu\App\Models\Genre;
use App\Traits\HasUniversalTranslation;
use Illuminate\Support\Facades\Log;

class GenreComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle, HasUniversalTranslation;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage;

    #[Url]
    public $sortField = 'genre_id';

    #[Url]
    public $sortDirection = 'desc';

    // View mode: minimal (default) or detailed
    public bool $detailedView = false;

    private ?array $availableSiteLanguages = null;

    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
        'translationCompleted' => 'handleTranslationCompleted'
    ];

    private GenreService $genreService;

    public function boot(GenreService $genreService): void
    {
        $this->genreService = $genreService;
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
    }

    public function refreshPageData()
    {
        $this->availableSiteLanguages = null;
        $this->genreService->clearCache();
        $this->render();
    }

    public function handleTranslationCompleted($eventData)
    {
        Log::info('🎉 MUZIBU: GenreComponent - TranslationCompleted event received', $eventData);

        $this->dispatch('translation-complete', [
            'success' => true,
            'sessionId' => $eventData['sessionId'] ?? null,
            'entityType' => $eventData['entityType'] ?? 'genre',
            'entityId' => $eventData['entityId'] ?? null,
            'successCount' => $eventData['success'] ?? 0,
            'failedCount' => $eventData['failed'] ?? 0,
            'message' => 'Çeviri başarıyla tamamlandı!',
            'timestamp' => now()->toISOString()
        ]);

        $this->dispatch('refreshPageData');
        Log::info('✅ MUZIBU: Frontend completion event dispatched');
    }

    protected function getModelClass()
    {
        return \Modules\Muzibu\App\Models\Genre::class;
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
        $dataLangChanged = request()->get('data_lang_changed');

        if ($dataLangChanged && in_array($dataLangChanged, $this->availableSiteLanguages)) {
            session(['tenant_locale' => $dataLangChanged]);
            session()->save();
            return $dataLangChanged;
        }

        if (auth()->check() && auth()->user()->tenant_locale) {
            $userLocale = auth()->user()->tenant_locale;
            if (session('tenant_locale') !== $userLocale) {
                session(['tenant_locale' => $userLocale]);
            }
            return $userLocale;
        }

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
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $result = $this->genreService->toggleGenreStatus($id);

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

        $genres = $this->genreService->getPaginatedGenres($filters, (int) $this->perPage);

        return view('muzibu::admin.livewire.genre-component', [
            'genres' => $genres,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}
