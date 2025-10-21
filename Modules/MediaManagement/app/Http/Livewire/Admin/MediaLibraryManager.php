<?php

namespace Modules\MediaManagement\App\Http\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Modules\MediaManagement\App\Services\MediaService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class MediaLibraryManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /**
     * Filters & sorting
     */
    public string $search = '';
    public ?string $typeFilter = null;
    public ?string $collectionFilter = null;
    public ?string $moduleFilter = null;
    public ?string $diskFilter = null;
    public ?string $dateFilter = 'all';
    public int $perPage = 24;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    /**
     * Editing state
     */
    public ?int $editingMediaId = null;
    public array $editForm = [
        'name' => '',
        'title' => [],
        'description' => [],
        'alt_text' => [],
    ];

    /**
     * Upload state
     */
    /**
     * Detail drawer state
     */
    public ?int $detailMediaId = null;

    /**
     * Internal helpers
     */
    protected MediaService $mediaService;
    protected array $locales = [];
    protected array $allowedSorts = ['created_at', 'name', 'size'];

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => null],
        'collectionFilter' => ['except' => null],
        'moduleFilter' => ['except' => null],
        'diskFilter' => ['except' => null],
        'dateFilter' => ['except' => 'all'],
        'perPage' => ['except' => 24],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'refreshMediaLibrary' => '$refresh',
    ];

    public function boot(MediaService $mediaService): void
    {
        $this->mediaService = $mediaService;
        $this->locales = $this->resolveLocales();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCollectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModuleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDiskFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSortField(string $value): void
    {
        if (!in_array($value, $this->allowedSorts, true)) {
            $this->sortField = 'created_at';
        }
    }

    public function updatedSortDirection(string $value): void
    {
        if (!in_array($value, ['asc', 'desc'], true)) {
            $this->sortDirection = 'desc';
        }
    }

    public function toggleSort(string $field): void
    {
        if (!in_array($field, $this->allowedSorts, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = null;
        $this->collectionFilter = null;
        $this->moduleFilter = null;
        $this->diskFilter = null;
        $this->dateFilter = 'all';
        $this->perPage = 24;
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }


    public function handleUploadCompleted(int $uploadedCount, array $errors = []): void
    {
        if ($uploadedCount > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => trans_choice('mediamanagement::admin.library_upload_success', $uploadedCount, ['count' => $uploadedCount]),
                'type' => 'success',
            ]);
        }

        foreach ($errors as $error) {
            $messages = $error['errors'] ?? [];
            if (!empty($messages)) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => implode("\n", $messages),
                    'type' => 'error',
                ]);
            }
        }

        $this->resetPage();
        $this->dispatch('refreshMediaLibrary');
    }

    public function openEditModal(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if (!$media) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.media_not_found'),
                'type' => 'error',
            ]);
            return;
        }

        $this->editingMediaId = $mediaId;
        $this->editForm = [
            'name' => $media->name,
            'title' => $media->getCustomProperty('title', []),
            'description' => $media->getCustomProperty('description', []),
            'alt_text' => $media->getCustomProperty('alt_text', []),
        ];

        $this->dispatch('open-edit-modal');
    }

    public function closeEditModal(): void
    {
        $this->editingMediaId = null;
        $this->editForm = [
            'name' => '',
            'title' => [],
            'description' => [],
            'alt_text' => [],
        ];
        $this->dispatch('close-edit-modal');
    }

    public function saveMedia(): void
    {
        if (!$this->editingMediaId) {
            return;
        }

        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.title' => 'array',
            'editForm.title.*' => 'nullable|string|max:255',
            'editForm.description' => 'array',
            'editForm.description.*' => 'nullable|string|max:500',
            'editForm.alt_text' => 'array',
            'editForm.alt_text.*' => 'nullable|string|max:255',
        ]);

        $media = Media::find($this->editingMediaId);
        if (!$media) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.media_not_found'),
                'type' => 'error',
            ]);
            return;
        }

        try {
            $media->name = trim($this->editForm['name']);
            $media->setCustomProperty('title', $this->cleanTranslations($this->editForm['title'] ?? []));
            $media->setCustomProperty('description', $this->cleanTranslations($this->editForm['description'] ?? []));
            $media->setCustomProperty('alt_text', $this->cleanTranslations($this->editForm['alt_text'] ?? []));
            $media->save();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.update_success'),
                'type' => 'success',
            ]);
        } catch (Throwable $e) {
            Log::error('Media update failed', [
                'media_id' => $media->id,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.update_failed'),
                'type' => 'error',
            ]);
        }

        $this->closeEditModal();
        $this->dispatch('refreshMediaLibrary');
    }

    public function openDetails(int $mediaId): void
    {
        $this->detailMediaId = $this->detailMediaId === $mediaId ? null : $mediaId;
    }

    public function closeDetails(): void
    {
        $this->detailMediaId = null;
    }

    public function deleteMedia(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if (!$media) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.media_not_found'),
                'type' => 'error',
            ]);
            return;
        }

        try {
            $libraryItem = MediaLibraryItem::where('media_id', $mediaId)->first();
            if ($libraryItem) {
                $libraryItem->delete();
            } else {
                $media->delete();
            }

            if ($this->detailMediaId === $mediaId) {
                $this->detailMediaId = null;
            }

            if ($this->editingMediaId === $mediaId) {
                $this->closeEditModal();
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.delete_success'),
                'type' => 'success',
            ]);

            $this->resetPage();
        } catch (Throwable $e) {
            Log::error('Media delete failed', [
                'media_id' => $mediaId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.delete_failed'),
                'type' => 'error',
            ]);
        }

        $this->dispatch('refreshMediaLibrary');
    }

    protected function cleanTranslations(?array $values): array
    {
        if (empty($values)) {
            return [];
        }

        $filtered = [];
        foreach ($values as $locale => $value) {
            if ($value === null) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            $filtered[(string) $locale] = $value;
        }

        if (!empty($this->locales)) {
            $filtered = array_intersect_key($filtered, array_flip($this->locales));
        }

        return $filtered;
    }

    public function formatBytes(?int $bytes, int $precision = 2): string
    {
        $bytes = $bytes ?? 0;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($bytes <= 0) {
            return '0 B';
        }

        $pow = (int) floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    public function moduleLabel(?string $modelType): string
    {
        if (!$modelType) {
            return __('mediamanagement::admin.unlinked');
        }

        if (Str::startsWith($modelType, 'Modules\\')) {
            $parts = explode('\\', $modelType);
            $module = $parts[1] ?? null;
            $model = $parts[count($parts) - 1] ?? null;

            if ($module && $model) {
                return Str::headline($module) . ' • ' . Str::headline($model);
            }
        }

        return Str::headline(class_basename($modelType));
    }

    public function modelDisplayName(Media $media): ?string
    {
        try {
            $model = $media->model;
            if (!$model) {
                return null;
            }

            $attributes = ['title', 'name', 'label', 'headline'];
            foreach ($attributes as $attribute) {
                if (isset($model->{$attribute}) && $model->{$attribute}) {
                    $value = $model->{$attribute};
                    if (is_array($value)) {
                        $locale = $this->locales[0] ?? config('app.locale', 'tr');
                        return $value[$locale] ?? reset($value);
                    }

                    return (string) $value;
                }

                if (method_exists($model, 'getTranslation')) {
                    try {
                        $locale = $this->locales[0] ?? config('app.locale', 'tr');
                        $translated = $model->getTranslation($attribute, $locale);
                        if ($translated) {
                            return $translated;
                        }
                    } catch (Throwable) {
                        // ignore
                    }
                }
            }

            return class_basename($model) . ' #' . $model->getKey();
        } catch (Throwable $e) {
            Log::debug('Failed to resolve media model display name', [
                'media_id' => $media->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function isPreviewable(Media $media): bool
    {
        return $media->mime_type ? Str::startsWith($media->mime_type, 'image/') : false;
    }

    public function previewUrl(Media $media): ?string
    {
        if (!$this->isPreviewable($media)) {
            return null;
        }

        // Use pre-computed URLs from transform() for Livewire serialization
        if (isset($media->computed_thumb_url)) {
            return $media->computed_thumb_url;
        }

        if (function_exists('thumb')) {
            $thumbUrl = thumb($media, 'thumb');
            if ($thumbUrl) {
                return $thumbUrl;
            }
        }

        if ($media->hasGeneratedConversion('thumb')) {
            return $media->getUrl('thumb');
        }

        return $media->getUrl();
    }

    protected function resolveLocales(): array
    {
        $locales = [];

        if (function_exists('available_tenant_languages')) {
            try {
                $languages = available_tenant_languages();
                foreach ($languages as $language) {
                    if (is_array($language) && isset($language['code'])) {
                        $locales[] = $language['code'];
                    } elseif (is_object($language) && isset($language->code)) {
                        $locales[] = $language->code;
                    } elseif (is_string($language)) {
                        $locales[] = $language;
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Unable to fetch tenant languages', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($locales)) {
            $locales[] = config('app.locale', 'tr');
        }

        return array_values(array_unique($locales));
    }

    protected function applyDateFilter($query): void
    {
        $now = Carbon::now();

        switch ($this->dateFilter) {
            case '24h':
                $query->where('created_at', '>=', $now->copy()->subDay());
                break;
            case '7d':
                $query->where('created_at', '>=', $now->copy()->subDays(7));
                break;
            case '30d':
                $query->where('created_at', '>=', $now->copy()->subDays(30));
                break;
            case '90d':
                $query->where('created_at', '>=', $now->copy()->subDays(90));
                break;
            case 'year':
                $query->where('created_at', '>=', $now->copy()->startOfYear());
                break;
            default:
                // all time
                break;
        }
    }

    public function getDetailMediaProperty(): ?Media
    {
        if (!$this->detailMediaId) {
            return null;
        }

        return Media::with('model')->find($this->detailMediaId);
    }

    public function getStatsProperty(): array
    {
        $total = Media::count();
        $totalSize = Media::sum('size');
        $last30Days = Media::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Orphan media check
        // Setting modeli central DB'de olduğu için whereDoesntHave cross-database hatası verir
        // Sadece model_type null olanları orphan say
        $unlinked = Media::whereNull('model_type')
            ->orWhereNull('model_id')
            ->count();

        $mimeCounts = Media::select('mime_type', DB::raw('count(*) as aggregate'))
            ->groupBy('mime_type')
            ->pluck('aggregate', 'mime_type');

        $typeCounts = [];
        foreach (config('mediamanagement.media_types', []) as $type => $config) {
            $typeCounts[$type] = collect($config['mime_types'] ?? [])
                ->sum(fn ($mime) => $mimeCounts[$mime] ?? 0);
        }

        return [
            'total' => $total,
            'total_size' => $totalSize,
            'last_30_days' => $last30Days,
            'unlinked' => $unlinked,
            'types' => $typeCounts,
        ];
    }

    public function render()
    {
        $query = Media::query();

        if ($this->search !== '') {
            $search = Str::lower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(file_name) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(model_type) LIKE ?', ['%' . $search . '%'])
                    ->orWhere('model_id', 'like', '%' . $this->search . '%')
                    ->orWhere('uuid', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $types = config('mediamanagement.media_types', []);
            if (isset($types[$this->typeFilter])) {
                $mimeTypes = $types[$this->typeFilter]['mime_types'] ?? [];
                if (!empty($mimeTypes)) {
                    $query->whereIn('mime_type', $mimeTypes);
                }
            }
        }

        if ($this->collectionFilter) {
            $query->where('collection_name', $this->collectionFilter);
        }

        if ($this->moduleFilter) {
            $query->where('model_type', $this->moduleFilter);
        }

        if ($this->diskFilter) {
            $query->where('disk', $this->diskFilter);
        }

        $this->applyDateFilter($query);

        $sortField = in_array($this->sortField, $this->allowedSorts, true)
            ? $this->sortField
            : 'created_at';

        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDirection);

        /** @var LengthAwarePaginator $mediaItems */
        $mediaItems = $query->paginate($this->perPage);

        $mediaItems->getCollection()->transform(function (Media $media) {
            $media->detected_type = $this->mediaService->getMediaTypeFromMime($media->mime_type ?? '');
            // Tenant-aware URL generation - Livewire serialization için pre-compute
            $media->computed_url = $media->getUrl();
            $media->computed_thumb_url = $media->hasGeneratedConversion('thumb')
                ? $media->getUrl('thumb')
                : $media->getUrl();
            return $media;
        });

        /** @var Collection<int, MediaLibraryItem> $libraryItems */
        $libraryItems = MediaLibraryItem::whereIn('media_id', $mediaItems->pluck('id')->filter()->values())
            ->get()
            ->keyBy('media_id');

        $availableCollections = Media::select('collection_name')
            ->distinct()
            ->orderBy('collection_name')
            ->pluck('collection_name');

        $availableModules = Media::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        $availableDisks = Media::select('disk')
            ->distinct()
            ->orderBy('disk')
            ->pluck('disk');

        return view('mediamanagement::admin.livewire.media-library-manager', [
            'mediaItems' => $mediaItems,
            'libraryItems' => $libraryItems,
            'availableCollections' => $availableCollections,
            'availableModules' => $availableModules,
            'availableDisks' => $availableDisks,
            'mediaTypes' => config('mediamanagement.media_types', []),
            'stats' => $this->stats,
            'locales' => $this->locales,
        ]);
    }
}
