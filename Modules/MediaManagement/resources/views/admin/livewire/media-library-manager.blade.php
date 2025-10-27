<div class="media-library-manager" x-data="mediaLibraryUploader('{{ route('admin.mediamanagement.library.upload', [], false) }}', '{{ csrf_token() }}', '{{ $this->getId() }}')">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('admin.mediamanagement.thumbmaker-guide') }}" class="btn btn-ghost-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>
            </svg>
            Thumbmaker Kılavuzu
        </a>
    </div>

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

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7"/><line x1="21" y1="21" x2="15" y2="15"/></svg>
                        </span>
                        <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="{{ __('mediamanagement::admin.search_placeholder') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select class="form-select" wire:model="typeFilter">
                        <option value="">{{ __('mediamanagement::admin.all_types') }}</option>
                        @foreach($mediaTypes as $typeKey => $typeConfig)
                            <option value="{{ $typeKey }}">{{ $typeConfig['label'] ?? \Illuminate\Support\Str::headline($typeKey) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select class="form-select" wire:model="collectionFilter">
                        <option value="">{{ __('mediamanagement::admin.all_collections') }}</option>
                        @foreach($availableCollections as $collection)
                            <option value="{{ $collection }}">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $collection)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select class="form-select" wire:model="dateFilter">
                        <option value="all">{{ __('mediamanagement::admin.date_all') }}</option>
                        <option value="24h">Son 24 Saat</option>
                        <option value="7d">Son 7 Gün</option>
                        <option value="30d">Son 30 Gün</option>
                        <option value="90d">Son 90 Gün</option>
                        <option value="year">Bu Yıl</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3">
                    <select class="form-select" wire:model="perPage">
                        @foreach([12, 24, 48, 96] as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3">
                    <button class="btn btn-icon" type="button" wire:click="resetFilters" title="Filtreleri Sıfırla">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Advanced Filters -->
            <div class="collapse mt-3" id="advancedFilters">
                <div class="border-top pt-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('mediamanagement::admin.model') }}</label>
                            <select class="form-select" wire:model="moduleFilter">
                                <option value="">{{ __('mediamanagement::admin.all_models') }}</option>
                                @foreach($availableModules as $module)
                                    <option value="{{ $module }}">{{ $this->moduleLabel($module) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('mediamanagement::admin.disk') }}</label>
                            <select class="form-select" wire:model="diskFilter">
                                <option value="">{{ __('mediamanagement::admin.all_disks') }}</option>
                                @foreach($availableDisks as $disk)
                                    <option value="{{ $disk }}">{{ \Illuminate\Support\Str::upper($disk) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @if($availableModules->count() > 0 || $availableDisks->count() > 0)
                <div class="mt-2">
                    <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5"/>
                        </svg>
                        Gelişmiş Filtreler
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if($mediaItems->count())
        @php
        $previewableMedia = $mediaItems->filter(function($media) {
            return $this->isPreviewable($media);
        })->map(function($media) {
            $isVideo = $this->isVideo($media);
            return [
                'url' => $isVideo ? $media->getUrl() : thumb($media, 1920, 1920, ['quality' => 90]),
                'thumb' => $isVideo ? $media->getUrl() : thumb($media, 400, 400, ['quality' => 80, 'scale' => 1]),
                'name' => $media->name,
                'id' => $media->id,
                'isVideo' => $isVideo,
                'mimeType' => $media->mime_type
            ];
        })->values()->toArray();
        @endphp
        <div x-data="{
            showLightbox: false,
            currentIndex: 0,
            mediaList: @js($previewableMedia),
            get currentMedia() {
                return this.mediaList[this.currentIndex] || {};
            },
            openLightbox(index) {
                this.currentIndex = index;
                this.showLightbox = true;
                document.body.style.overflow = 'hidden';
            },
            closeLightbox() {
                this.showLightbox = false;
                document.body.style.overflow = '';
            },
            nextMedia() {
                if (this.currentIndex < this.mediaList.length - 1) {
                    this.currentIndex++;
                }
            },
            prevMedia() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            }
        }"
        @keydown.escape.window="if(showLightbox) { closeLightbox(); }"
        @keydown.arrow-right.window="if(showLightbox) { nextMedia(); }"
        @keydown.arrow-left.window="if(showLightbox) { prevMedia(); }">
            <div class="row row-cards g-2">
                @php $previewableIndex = 0; @endphp
                @foreach($mediaItems as $media)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6" x-data="{ copied: false }">
                        <div class="card card-sm h-100" wire:key="media-card-{{ $media->id }}">
                            <!-- Thumbnail Preview -->
                            <div class="ratio ratio-1x1 card-img-top position-relative"
                                 @if($this->isPreviewable($media))
                                 style="cursor: zoom-in;"
                                 @click="openLightbox({{ $previewableIndex }})"
                                 @php $previewableIndex++; @endphp
                                 @endif>
                                @if($this->isPreviewable($media))
                                    <img src="{{ thumb($media, 400, 400, ['quality' => 80, 'scale' => 1]) }}"
                                         alt="{{ $media->name }}"
                                         class="object-fit-cover w-100 h-100"
                                         loading="lazy">
                                    <div class="position-absolute top-0 end-0 m-1">
                                        <span class="badge badge-sm bg-dark bg-opacity-75">
                                            {{ $this->formatBytes($media->size) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-secondary bg-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        </svg>
                                        <div class="fw-bold text-uppercase small mt-1">{{ strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) }}</div>
                                        <div class="badge badge-sm bg-azure-lt text-dark mt-1">{{ $this->formatBytes($media->size) }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Compact Card Body -->
                            <div class="card-body p-2">
                                <div class="text-truncate small" title="{{ $media->name ?? $media->file_name }}">
                                    <strong>{{ \Illuminate\Support\Str::limit($media->name ?? $media->file_name, 20) }}</strong>
                                </div>
                                <div class="text-muted" style="font-size: 0.625rem;">
                                    {{ optional($media->created_at)->format('d.m.Y') }}
                                </div>
                            </div>

                            <!-- Compact Footer -->
                            <div class="card-footer p-1">
                                <div class="btn-list justify-content-center">
                                    <button class="btn btn-sm btn-icon"
                                            @click.stop="navigator.clipboard.writeText('{{ addslashes($media->computed_url ?? $media->getUrl()) }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                            title="URL Kopyala"
                                            :class="copied ? 'btn-success' : 'btn-ghost-secondary'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-ghost-primary"
                                            wire:click="openEditModal({{ $media->id }})"
                                            title="{{ __('admin.edit') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/>
                                        </svg>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-ghost-danger"
                                            @click.prevent="if(confirm('{{ __('mediamanagement::admin.confirm_delete') }}')) { $wire.deleteMedia({{ $media->id }}) }"
                                            title="{{ __('admin.delete') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Global Lightbox Modal -->
            <template x-if="showLightbox">
                <div @click="closeLightbox()"
                     class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                     style="z-index: 9999; background: rgba(0,0,0,0.95); cursor: pointer;">

                    <!-- Close Button -->
                    <button @click.stop="closeLightbox()"
                            class="btn btn-icon btn-light position-absolute top-0 end-0 m-3"
                            style="z-index: 10001;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>

                    <!-- Previous Button -->
                    <button @click.stop="prevMedia()"
                            x-show="currentIndex > 0"
                            class="btn btn-icon btn-light position-absolute start-0 top-50 translate-middle-y ms-3"
                            style="z-index: 10001;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18"/>
                        </svg>
                    </button>

                    <!-- Next Button -->
                    <button @click.stop="nextMedia()"
                            x-show="currentIndex < mediaList.length - 1"
                            class="btn btn-icon btn-light position-absolute end-0 top-50 translate-middle-y me-3"
                            style="z-index: 10001;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18"/>
                        </svg>
                    </button>

                    <!-- Media Container -->
                    <div class="d-flex flex-column align-items-center justify-content-center" style="max-width: 90%; max-height: 90vh;" @click.stop>
                        <!-- Image -->
                        <template x-if="!currentMedia.isVideo">
                            <img :src="currentMedia.url"
                                 :alt="currentMedia.name"
                                 class="img-fluid"
                                 style="max-width: 100%; max-height: 85vh; object-fit: contain; cursor: default;">
                        </template>

                        <!-- Video -->
                        <template x-if="currentMedia.isVideo">
                            <video controls
                                   class="img-fluid"
                                   style="max-width: 100%; max-height: 85vh; object-fit: contain; cursor: default;"
                                   preload="metadata">
                                <source :src="currentMedia.url" :type="currentMedia.mimeType">
                                Tarayıcınız video oynatmayı desteklemiyor.
                            </video>
                        </template>

                        <!-- Media Info -->
                        <div class="text-white mt-3 text-center">
                            <div class="fw-bold" x-text="currentMedia.name"></div>
                            <div class="text-muted small">
                                <span x-text="`${currentIndex + 1} / ${mediaList.length}`"></span>
                                <span x-show="currentMedia.isVideo" class="ms-2 badge bg-primary">Video</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
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
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('mediamanagement::admin.title_single') }}
                                        <span class="text-muted small">(Görünen Ad)</span>
                                    </label>
                                    <input type="text" class="form-control" wire:model.defer="editForm.name">
                                    @error('editForm.name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Dosya Adı
                                        <span class="text-muted small">(Slug-friendly)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" wire:model.defer="editForm.file_name" placeholder="ornek-dosya">
                                        <span class="input-group-text">.{{ $editForm['extension'] ?? '' }}</span>
                                    </div>
                                    @error('editForm.file_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Türkçe karakterler otomatik temizlenecek</div>
                                </div>
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
