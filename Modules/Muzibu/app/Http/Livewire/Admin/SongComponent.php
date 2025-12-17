<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Muzibu\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Muzibu\App\Services\SongService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Muzibu\App\Models\Song;
use App\Traits\HasUniversalTranslation;
use Illuminate\Support\Facades\Log;

class SongComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle, HasUniversalTranslation;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage;

    #[Url]
    public $sortField = 'song_id';

    #[Url]
    public $sortDirection = 'desc';

    // Filters
    #[Url]
    public $filterArtist = '';

    #[Url]
    public $filterGenre = '';

    #[Url]
    public $filterAlbum = '';

    #[Url]
    public $filterHls = '';

    // View mode: minimal (default) or detailed
    public bool $detailedView = false;

    private ?array $availableSiteLanguages = null;

    protected $listeners = [
        'refreshPageData' => 'refreshPageData',
        'translationCompleted' => 'handleTranslationCompleted'
    ];

    private SongService $songService;

    public function boot(SongService $songService): void
    {
        $this->songService = $songService;
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
    }

    public function refreshPageData()
    {
        $this->availableSiteLanguages = null;
        $this->songService->clearCache();
        $this->render();
    }

    public function handleTranslationCompleted($eventData)
    {
        Log::info('🎉 MUZIBU: SongComponent - TranslationCompleted event received', $eventData);

        $this->dispatch('translation-complete', [
            'success' => true,
            'sessionId' => $eventData['sessionId'] ?? null,
            'entityType' => $eventData['entityType'] ?? 'song',
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
        return \Modules\Muzibu\App\Models\Song::class;
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
    public function artists()
    {
        return \Modules\Muzibu\App\Models\Artist::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    #[Computed]
    public function genres()
    {
        return \Modules\Muzibu\App\Models\Genre::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    #[Computed]
    public function albums()
    {
        return \Modules\Muzibu\App\Models\Album::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    public function clearFilters()
    {
        $this->filterArtist = '';
        $this->filterGenre = '';
        $this->filterAlbum = '';
        $this->filterHls = '';
        $this->search = '';
        $this->resetPage();
    }

    public function updatedFilterArtist()
    {
        $this->resetPage();
    }

    public function updatedFilterGenre()
    {
        $this->resetPage();
    }

    public function updatedFilterHls()
    {
        $this->resetPage();
    }

    public function updatedFilterAlbum()
    {
        $this->resetPage();
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
            $result = $this->songService->toggleSongStatus($id);

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

    public function bulkConvertToHls(): void
    {
        if (empty($this->selectedItems)) {
            return;
        }

        try {
            // ✅ Tüm seçili şarkıları al (reconversion desteği - mevcut HLS dosyaları üzerine yazılacak)
            $songs = Song::whereIn('song_id', $this->selectedItems)
                ->whereNotNull('file_path')
                ->get();

            if ($songs->isEmpty()) {
                $this->dispatch('toast', [
                    'title' => __('admin.info'),
                    'message' => 'Seçili şarkılarda dosya bulunamadı.',
                    'type' => 'info',
                ]);
                return;
            }

            // Check how many are already converted (reconversion bilgisi)
            $alreadyConverted = $songs->where('hls_converted', true)->count();
            $newConversions = $songs->where('hls_converted', '!=', true)->count();

            $count = 0;
            foreach ($songs as $song) {
                // ✅ HLS Job kuyruğa ekle (reconversion: mevcut dosyalar üzerine yazılır)
                \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);
                $count++;
            }

            // Detaylı bilgilendirme mesajı
            $message = "{$count} şarkı HLS dönüşümü için kuyruğa eklendi.";
            if ($alreadyConverted > 0) {
                $message .= " ({$alreadyConverted} şarkı yeniden dönüştürülecek, {$newConversions} yeni dönüşüm)";
            }
            $message .= " İşlemler arka planda devam edecek.";

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success',
            ]);

            // Clear selection
            $this->selectedItems = [];
            $this->selectAll = false;

            Log::info('✅ Bulk HLS conversion queued', [
                'count' => $count,
                'already_converted' => $alreadyConverted,
                'new_conversions' => $newConversions,
                'song_ids' => $songs->pluck('song_id')->toArray(),
                'reconversion_allowed' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk HLS conversion failed', ['error' => $e->getMessage()]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        // Short cache TTL (60s) to balance freshness vs performance
        // HLS conversion job will invalidate cache when complete
        if (!app()->runningInConsole()) {
            header('Cache-Control: max-age=60, must-revalidate');
        }

        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $this->siteLocale,
            'filterArtist' => $this->filterArtist,
            'filterGenre' => $this->filterGenre,
            'filterAlbum' => $this->filterAlbum,
            'filterHls' => $this->filterHls,
        ];

        $songs = $this->songService->getPaginatedSongs($filters, (int) $this->perPage);

        return view('muzibu::admin.livewire.song-component', [
            'songs' => $songs,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }
}
