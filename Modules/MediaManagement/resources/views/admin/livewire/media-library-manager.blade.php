<div class="media-library-manager" x-data="mediaLibraryUploader('{{ route('admin.mediamanagement.library.upload', [], false) }}', '{{ csrf_token() }}', '{{ $this->getId() }}')">
    <div class="row row-cards mb-4 g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">{{ __('mediamanagement::admin.stats_total') }}</div>
                    <div class="h2 mb-0">{{ number_format($stats['total'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">{{ __('mediamanagement::admin.stats_total_size') }}</div>
                    <div class="h2 mb-0">{{ $this->formatBytes($stats['total_size'] ?? 0, 1) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">{{ __('mediamanagement::admin.stats_last_30_days') }}</div>
                    <div class="h2 mb-0">{{ number_format($stats['last_30_days'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">{{ __('mediamanagement::admin.stats_unused') }}</div>
                    <div class="h2 mb-0">{{ number_format($stats['unlinked'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="card-title mb-0">{{ __('mediamanagement::admin.library_upload_title') }}</h3>
                <div class="text-secondary small">{{ __('mediamanagement::admin.library_upload_help') }}</div>
            </div>
            <div>
                <button class="btn btn-primary" x-on:click="triggerUpload()" :disabled="isUploading">
                    <span x-show="isUploading" class="spinner-border spinner-border-sm me-2"></span>
                    {{ __('mediamanagement::admin.upload_to_library') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div x-data="{ dropping: false }" x-on:dragover.prevent="dropping = true" x-on:dragleave.prevent="dropping = false" x-on:drop.prevent="dropping = false; handleDrop($event)" class="border border-dashed rounded-3 p-4 text-center" :class="dropping ? 'border-primary bg-primary-subtle' : ''">
                <input type="file" multiple x-ref="uploader" class="d-none" x-on:change="handleSelect($event)">
                <div class="mb-2">
                    <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                </div>
                <div class="fw-bold">{{ __('mediamanagement::admin.drag_drop_files') }}</div>
                <div class="text-secondary small">{{ __('mediamanagement::admin.library_upload_formats') }}</div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary" x-on:click="$refs.uploader.click()">
                        {{ __('mediamanagement::admin.choose_files') }}
                    </button>
                </div>
                <div class="mt-3" x-show="isUploading" x-transition>
                    <span class="spinner-border spinner-border-sm me-2"></span>{{ __('mediamanagement::admin.uploading') }}
                </div>
            </div>
            <template x-if="uploadErrors.length">
                <div class="text-danger small mt-2" x-text="uploadErrors.join('\n')"></div>
            </template>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small mb-1">{{ __('mediamanagement::admin.search_label') }}</label>
                    <input type="text" class="form-control form-control-sm" wire:model.debounce.500ms="search" placeholder="{{ __('mediamanagement::admin.search_placeholder') }}">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small mb-1">{{ __('mediamanagement::admin.media_type') }}</label>
                    <select class="form-select form-select-sm" wire:model="typeFilter">
                        <option value="">{{ __('mediamanagement::admin.all_types') }}</option>
                        @foreach($mediaTypes as $typeKey => $typeConfig)
                            <option value="{{ $typeKey }}">{{ $typeConfig['label'] ?? \Illuminate\Support\Str::headline($typeKey) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small mb-1">{{ __('mediamanagement::admin.collection') }}</label>
                    <select class="form-select form-select-sm" wire:model="collectionFilter">
                        <option value="">{{ __('mediamanagement::admin.all_collections') }}</option>
                        @foreach($availableCollections as $collection)
                            <option value="{{ $collection }}">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $collection)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-4">
                    <label class="form-label small mb-1">{{ __('mediamanagement::admin.date_range') }}</label>
                    <select class="form-select form-select-sm" wire:model="dateFilter">
                        <option value="all">{{ __('mediamanagement::admin.date_all') }}</option>
                        <option value="24h">{{ __('mediamanagement::admin.date_24h') }}</option>
                        <option value="7d">{{ __('mediamanagement::admin.date_7d') }}</option>
                        <option value="30d">{{ __('mediamanagement::admin.date_30d') }}</option>
                        <option value="90d">{{ __('mediamanagement::admin.date_90d') }}</option>
                        <option value="year">{{ __('mediamanagement::admin.date_year') }}</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3">
                    <label class="form-label small mb-1">{{ __('mediamanagement::admin.per_page') }}</label>
                    <select class="form-select form-select-sm" wire:model="perPage">
                        @foreach([12, 24, 48, 96] as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <button class="btn btn-sm btn-link" type="button" wire:click="resetFilters">
                        <i class="fas fa-rotate-left me-1"></i>{{ __('mediamanagement::admin.reset_filters') }}
                    </button>
                </div>
            </div>
            <!-- Advanced Filters Toggle -->
            <div class="collapse mt-2" id="advancedFilters" x-data="{ open: false }">
                <div class="border-top pt-2">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">{{ __('mediamanagement::admin.model') }}</label>
                            <select class="form-select form-select-sm" wire:model="moduleFilter">
                                <option value="">{{ __('mediamanagement::admin.all_models') }}</option>
                                @foreach($availableModules as $module)
                                    <option value="{{ $module }}">{{ $this->moduleLabel($module) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">{{ __('mediamanagement::admin.disk') }}</label>
                            <select class="form-select form-select-sm" wire:model="diskFilter">
                                <option value="">{{ __('mediamanagement::admin.all_disks') }}</option>
                                @foreach($availableDisks as $disk)
                                    <option value="{{ $disk }}">{{ \Illuminate\Support\Str::upper($disk) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                    <i class="fas fa-filter me-1"></i>{{ __('admin.advanced_filters') ?? 'Gelişmiş Filtreler' }}
                </button>
            </div>
        </div>
    </div>

    @if($mediaItems->count())
        <div class="row row-cards g-3" x-data="{ openLightbox: null, copied: false }">
            @foreach($mediaItems as $media)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100" wire:key="media-card-{{ $media->id }}">
                        <!-- Thumbnail Preview -->
                        <div class="ratio ratio-1x1 bg-light border-bottom position-relative"
                             style="cursor: pointer;"
                             @click="openLightbox = {{ $media->id }}">
                            @if($this->isPreviewable($media))
                                <img src="{{ $media->computed_thumb_url ?? $media->computed_url ?? $this->previewUrl($media) }}"
                                     alt="{{ $media->name }}"
                                     class="object-fit-cover w-100 h-100">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-dark bg-opacity-75">
                                        <i class="fas fa-search-plus me-1"></i>
                                        {{ $this->formatBytes($media->size) }}
                                    </span>
                                </div>
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-secondary">
                                    <i class="fas fa-file fa-3x mb-2"></i>
                                    <div class="fw-bold text-uppercase small">{{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }}</div>
                                    <div class="badge bg-azure-lt text-dark mt-2">{{ $this->formatBytes($media->size) }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- Compact Card Body -->
                        <div class="card-body p-3">
                            <div class="fw-semibold small text-truncate" title="{{ $media->name ?? $media->file_name }}">
                                {{ \Illuminate\Support\Str::limit($media->name ?? $media->file_name, 30) }}
                            </div>
                            <div class="text-secondary" style="font-size: 0.7rem;">
                                {{ optional($media->created_at)->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <!-- Compact Footer -->
                        <div class="card-footer p-2 d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-ghost-secondary"
                                        @click.stop="navigator.clipboard.writeText('{{ $media->computed_url ?? $media->getUrl() }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                        title="URL Kopyala">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-ghost-primary"
                                        wire:click="openEditModal({{ $media->id }})"
                                        title="{{ __('admin.edit') }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-ghost-danger"
                                        @click.prevent="if(confirm('{{ __('mediamanagement::admin.confirm_delete') }}')) { $wire.deleteMedia({{ $media->id }}) }"
                                        title="{{ __('admin.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="text-success small" x-show="copied" x-cloak style="font-size: 0.7rem;">
                                <i class="fas fa-check"></i> Kopyalandı
                            </div>
                        </div>

                        <!-- Lightbox Modal -->
                        @if($this->isPreviewable($media))
                            <div x-show="openLightbox === {{ $media->id }}"
                                 x-cloak
                                 @click="openLightbox = null"
                                 @keydown.escape.window="openLightbox = null"
                                 class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                 style="z-index: 9999; background: rgba(0,0,0,0.9); cursor: pointer;">
                                <button @click.stop="openLightbox = null"
                                        class="btn btn-ghost-light position-absolute top-0 end-0 m-3"
                                        style="z-index: 10000;">
                                    <i class="fas fa-times fa-2x"></i>
                                </button>
                                <img src="{{ $media->computed_url ?? $media->getUrl() }}"
                                     alt="{{ $media->name }}"
                                     class="img-fluid"
                                     @click.stop
                                     style="max-width: 90%; max-height: 90vh; object-fit: contain; cursor: default;">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $mediaItems->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-secondary py-5">
                <i class="fas fa-images fa-2x mb-2"></i>
                <div>{{ __('mediamanagement::admin.no_results') }}</div>
            </div>
        </div>
    @endif

    @if($editingMediaId)
        <div class="modal-backdrop fade show" style="z-index: 1050; opacity: 0.5;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1055;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="saveMedia">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('mediamanagement::admin.edit_media_caption') }}</h5>
                            <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('mediamanagement::admin.title_single') }}</label>
                                <input type="text" class="form-control" wire:model.defer="editForm.name">
                                @error('editForm.name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row g-3">
                                @foreach($locales as $locale)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('mediamanagement::admin.title') }} ({{ strtoupper($locale) }})</label>
                                        <input type="text" class="form-control" wire:model.defer="editForm.title.{{ $locale }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('mediamanagement::admin.alt_text') }} ({{ strtoupper($locale) }})</label>
                                        <input type="text" class="form-control" wire:model.defer="editForm.alt_text.{{ $locale }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('mediamanagement::admin.description') }} ({{ strtoupper($locale) }})</label>
                                        <textarea class="form-control" rows="2" wire:model.defer="editForm.description.{{ $locale }}"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" wire:click="closeEditModal">{{ __('admin.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveMedia">
                                <span wire:loading wire:target="saveMedia" class="spinner-border spinner-border-sm me-2"></span>
                                {{ __('admin.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    window.mediaLibraryUploader = function(uploadUrl, csrfToken, componentId) {
        return {
            copied: false,
            isUploading: false,
            uploadErrors: [],
            triggerUpload() {
                this.$refs?.uploader?.click();
            },
            handleSelect(event) {
                const files = Array.from(event?.target?.files || []);
                if (files.length) {
                    this.upload(files);
                    event.target.value = '';
                }
            },
            handleDrop(event) {
                const files = Array.from(event?.dataTransfer?.files || []);
                if (files.length) {
                    this.upload(files);
                }
            },
            upload(files, retryCount = 0) {
                const formData = new FormData();
                files.forEach(file => formData.append('files[]', file));
                formData.append('_token', csrfToken);

                if (retryCount === 0) {
                    this.isUploading = true;
                    this.uploadErrors = [];
                }

                const url = uploadUrl.startsWith('http') ? uploadUrl : `${window.location.origin}${uploadUrl}`;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    cache: 'no-store',
                    keepalive: true, // SSL handshake için
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Connection': 'keep-alive', // SSL bağlantısını koru
                    },
                })
                    .then(async response => {
                        if (!response.ok) {
                            const data = await response.json().catch(() => ({}));
                            throw data;
                        }
                        return response.json();
                    })
                    .then(data => {
                        const uploadedCount = data?.uploaded_count ?? 0;
                        const errors = data?.errors ?? [];
                        window.Livewire?.find(componentId)?.call('handleUploadCompleted', uploadedCount, errors);
                        this.isUploading = false;
                    })
                    .catch(error => {
                        console.error('Media library upload error (attempt ' + (retryCount + 1) + '):', error);

                        // SSL error detected and first attempt - auto retry once
                        if (retryCount === 0 && error?.message && (error.message.includes('Failed to fetch') || error.message.includes('NetworkError'))) {
                            console.log('🔄 SSL handshake error detected, retrying media library upload automatically...');
                            setTimeout(() => {
                                this.upload(files, 1);
                            }, 200);
                            return;
                        }

                        // Final failure
                        const errors = [];
                        if (error?.errors) {
                            Object.values(error.errors).forEach(val => {
                                if (Array.isArray(val)) {
                                    errors.push(...val);
                                } else if (typeof val === 'string') {
                                    errors.push(val);
                                }
                            });
                        } else if (error?.message) {
                            errors.push(error.message);
                        } else {
                            errors.push('{{ __('mediamanagement::admin.library_upload_failed') }}');
                        }

                        this.uploadErrors = errors;
                        window.Livewire?.find(componentId)?.call('handleUploadCompleted', 0, [{ errors }]);
                        this.isUploading = false;
                    });
            },
        };
    };
</script>
@endpush
