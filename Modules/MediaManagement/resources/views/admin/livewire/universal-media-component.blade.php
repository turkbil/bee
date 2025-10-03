<div class="media-management-component">
    <div class="row">
        {{-- FEATURED IMAGE --}}
        @if($hasFeautredImage)
            <div class="col-md-6 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-image me-2"></i>{{ __('mediamanagement::admin.featured_image') }}
                    </h4>
                    @if(!empty($existingFeaturedImage))
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>{{ __('admin.active') }}
                        </span>
                    @endif
                </div>

                @if(!empty($existingFeaturedImage))
                    {{-- Existing Featured Image --}}
                    <div class="position-relative" x-data="{ showDeleteBtn: false }">
                        <img src="{{ $existingFeaturedImage['thumb'] ?? $existingFeaturedImage['url'] }}"
                             alt="Featured Image"
                             class="img-thumbnail mb-2 rounded"
                             style="max-width: 100%; height: auto;"
                             @mouseenter="showDeleteBtn = true"
                             @mouseleave="showDeleteBtn = false">

                        <button type="button"
                                wire:click="deleteFeaturedImage"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                x-show="showDeleteBtn"
                                x-transition
                                style="display: none;">
                            <i class="fas fa-trash"></i> {{ __('mediamanagement::admin.delete') }}
                        </button>
                    </div>
                @else
                    {{-- Upload Area --}}
                    <div x-data="{
                        isDragging: false,
                        handleDrop(e) {
                            this.isDragging = false;
                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                @this.upload('featuredImageFile', files[0]);
                            }
                        }
                    }"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         class="border rounded p-4 text-center"
                         :class="{ 'border-primary bg-light': isDragging, 'border-dashed': !isDragging }"
                         style="min-height: 200px; cursor: pointer;"
                         @click="$refs.featuredInput.click()">

                        <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                        </div>

                        <p class="mb-2">{{ __('mediamanagement::admin.drag_drop_file') }}</p>
                        <p class="text-muted small mb-0">{{ __('mediamanagement::admin.max_file_size', ['size' => '10MB']) }}</p>
                        <p class="text-muted small">{{ __('mediamanagement::admin.allowed_types', ['types' => 'JPG, PNG, WEBP, GIF']) }}</p>

                        <input type="file"
                               x-ref="featuredInput"
                               wire:model="featuredImageFile"
                               accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                               class="d-none">
                    </div>

                    @if($featuredImageFile)
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-spinner fa-spin me-2"></i>{{ __('mediamanagement::admin.uploading') }}
                        </div>
                    @endif

                    @error('featuredImageFile')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        @endif

        {{-- GALLERY --}}
        @if($hasGallery)
            <div class="col-md-6 mb-4">
                <h4 class="mb-3">
                    <i class="fas fa-images me-2"></i>{{ __('mediamanagement::admin.gallery') }}
                </h4>

                {{-- Upload Area --}}
                <div x-data="{
                    isDragging: false,
                    handleDrop(e) {
                        this.isDragging = false;
                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            @this.upload('galleryFiles', files);
                        }
                    }
                }"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     class="border rounded p-4 text-center mb-3"
                     :class="{ 'border-primary bg-light': isDragging, 'border-dashed': !isDragging }"
                     style="min-height: 150px; cursor: pointer;"
                     @click="$refs.galleryInput.click()">

                    <div class="mb-2">
                        <i class="fas fa-images fa-2x text-muted"></i>
                    </div>

                    <p class="mb-1">{{ __('mediamanagement::admin.drag_drop_files') }}</p>
                    <p class="text-muted small mb-0">{{ __('mediamanagement::admin.max_file_size', ['size' => '10MB']) }}</p>

                    <input type="file"
                           x-ref="galleryInput"
                           wire:model="galleryFiles"
                           accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                           multiple
                           class="d-none">
                </div>

                @if($galleryFiles)
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin me-2"></i>{{ __('mediamanagement::admin.uploading') }}
                    </div>
                @endif

                @error('galleryFiles.*')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                {{-- Existing Gallery Images - Sortable --}}
                @if(!empty($existingGallery))
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-arrows-alt me-1"></i>{{ __('mediamanagement::admin.drag_to_reorder') }}
                        </small>
                    </div>
                    <div id="gallery-sortable-list" class="row g-2">
                        @foreach($existingGallery as $image)
                            <div class="col-6 col-md-4 gallery-item"
                                 data-id="{{ $image['id'] }}"
                                 x-data="{ showActions: false }">
                                <div class="position-relative gallery-card"
                                     @mouseenter="showActions = true"
                                     @mouseleave="showActions = false"
                                     style="cursor: grab;">
                                    <img src="{{ $image['thumb'] }}"
                                         alt="{{ $image['name'] }}"
                                         class="img-thumbnail w-100"
                                         style="aspect-ratio: 1; object-fit: cover;">

                                    {{-- Action Buttons --}}
                                    <div class="position-absolute top-0 start-0 end-0 p-2 d-flex justify-content-between"
                                         x-show="showActions"
                                         x-transition
                                         style="display: none; background: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);">

                                        {{-- Drag Handle --}}
                                        <div class="gallery-drag-handle"
                                             style="cursor: grab; color: white; font-size: 1.2rem;">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>

                                        {{-- Action Buttons Right --}}
                                        <div class="d-flex gap-1">
                                            @if($setFeaturedFromGallery && $hasFeautredImage)
                                                <button type="button"
                                                        wire:click="setFeaturedFromGallery({{ $image['id'] }})"
                                                        class="btn btn-success btn-sm"
                                                        style="padding: 0.25rem 0.5rem;"
                                                        title="{{ __('mediamanagement::admin.set_as_featured') }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            @endif
                                            <button type="button"
                                                    wire:click="deleteMedia({{ $image['id'] }}, 'gallery')"
                                                    class="btn btn-danger btn-sm"
                                                    style="padding: 0.25rem 0.5rem;"
                                                    title="{{ __('mediamanagement::admin.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Image Info --}}
                                    <div class="position-absolute bottom-0 start-0 end-0 p-1 text-center"
                                         x-show="showActions"
                                         x-transition
                                         style="display: none; background: rgba(0,0,0,0.6); color: white; font-size: 0.7rem;">
                                        {{ $image['size'] ?? '' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light text-center">
                        <i class="fas fa-image text-muted fa-2x mb-2"></i>
                        <p class="mb-0 text-muted">{{ __('mediamanagement::admin.no_files') }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- SortableJS Scripts - Only if gallery exists --}}
@if($hasGallery && !empty($existingGallery))
    @push('scripts')
        <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
        <script>
            document.addEventListener('livewire:initialized', function() {
                initGallerySortable();

                Livewire.hook('morph.updated', () => {
                    initGallerySortable();
                });

                function initGallerySortable() {
                    const container = document.getElementById('gallery-sortable-list');
                    if (!container) return;

                    if (window.gallerySortable) {
                        window.gallerySortable.destroy();
                        window.gallerySortable = null;
                    }

                    window.gallerySortable = new Sortable(container, {
                        animation: 200,
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        handle: '.gallery-drag-handle',
                        forceFallback: true,

                        onStart: function(evt) {
                            evt.item.style.opacity = '0.5';
                        },

                        onEnd: function(evt) {
                            evt.item.style.opacity = '1';

                            const items = [];
                            const allItems = Array.from(container.querySelectorAll('.gallery-item'));

                            allItems.forEach((item, index) => {
                                const id = item.getAttribute('data-id');
                                if (id) {
                                    items.push({
                                        id: parseInt(id),
                                        order: index + 1
                                    });
                                }
                            });

                            if (items.length > 0) {
                                @this.call('updateGalleryOrder', items);
                            }
                        }
                    });

                    console.log('✅ Gallery sortable initialized');
                }
            });
        </script>

        <style>
            .sortable-ghost {
                opacity: 0.4;
                background: #f8f9fa;
            }

            .sortable-drag {
                opacity: 1;
            }

            .gallery-card:active {
                cursor: grabbing !important;
            }

            .gallery-drag-handle:active {
                cursor: grabbing !important;
            }
        </style>
    @endpush
@endif
