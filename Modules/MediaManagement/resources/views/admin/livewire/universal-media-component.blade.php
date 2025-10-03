<div class="media-management-component">
    <div class="row">
        {{-- FEATURED IMAGE --}}
        @if($hasFeautredImage)
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">
                    <i class="ti ti-photo me-2"></i>{{ __('mediamanagement::admin.featured_image') }}
                    @if(!empty($existingFeaturedImage))
                        <span class="badge bg-success ms-2">
                            <i class="ti ti-check"></i>
                        </span>
                    @endif
                </h5>

                {{-- Upload Area with Preview - Always visible --}}
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
                     class="border rounded p-3 text-center position-relative border-dashed"
                     :class="{ 'border-primary bg-light': isDragging }"
                     style="cursor: pointer; min-height: 120px;"
                     @click="$refs.featuredInput.click()">

                    {{-- Thumbnail Preview (if uploaded) - Overlays upload area --}}
                    @if(!empty($existingFeaturedImage) || !empty($tempFeaturedImage))
                        <div class="row g-2">
                            <div class="col-6 col-md-4 mx-auto">
                                <div class="position-relative">
                                    @php
                                        // Session-based temp file öncelikli
                                        if (!empty($tempFeaturedImage)) {
                                            $imageUrl = $tempFeaturedImage['url'];
                                            $isTemp = true;
                                        }
                                        // Existing image varsa onun URL'ini kullan
                                        elseif (!empty($existingFeaturedImage)) {
                                            $imageUrl = $existingFeaturedImage['thumb'] ?? $existingFeaturedImage['url'];
                                            $isTemp = false;
                                        } else {
                                            $imageUrl = null;
                                            $isTemp = false;
                                        }
                                    @endphp

                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}"
                                             alt="Preview"
                                             class="img-thumbnail w-100"
                                             style="aspect-ratio: 1; object-fit: cover;"
                                             @click.stop>

                                        <button type="button"
                                                x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').{{ $isTemp ? 'removeTempFeaturedImage' : 'deleteFeaturedImage' }}()"
                                                class="btn btn-danger btn-sm position-absolute"
                                                style="top: 8px; right: 8px; padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Upload Instructions (if no image) --}}
                        <div class="mb-2">
                            <i class="fas fa-image fa-2x text-muted"></i>
                        </div>

                        <p class="mb-1">{{ __('mediamanagement::admin.drag_drop_file') }}</p>
                        <p class="text-muted small mb-0">{{ __('mediamanagement::admin.max_file_size', ['size' => '10MB']) }}</p>
                    @endif

                    <input type="file"
                           x-ref="featuredInput"
                           wire:model="featuredImageFile"
                           accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                           class="d-none">
                </div>

                @error('featuredImageFile')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        @endif

        {{-- GALLERY --}}
        @if($hasGallery)
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">
                    <i class="ti ti-photo-up me-2"></i>{{ __('mediamanagement::admin.gallery') }}
                </h5>

                {{-- Existing & Temporary Gallery Images - Sortable --}}
                @if(!empty($tempGallery) || !empty($existingGallery))
                    <div id="gallery-sortable-list" class="row g-2 mb-3">
                        {{-- Session-based Temporary uploads first --}}
                        @if(!empty($tempGallery))
                        @foreach($tempGallery as $index => $item)
                            <div class="col-6 col-md-4 gallery-temp-item" data-temp-index="{{ $index }}">
                                <div class="position-relative gallery-card" style="cursor: grab;">
                                    <img src="{{ $item['url'] }}"
                                         alt="Preview"
                                         class="img-thumbnail w-100"
                                         style="aspect-ratio: 1; object-fit: cover;">

                                    {{-- Drag Handle - Top Left --}}
                                    <div class="position-absolute d-flex gap-1" style="top: 8px; left: 8px;">
                                        <button type="button"
                                                class="btn btn-secondary btn-sm gallery-drag-handle"
                                                style="padding: 0.25rem 0.5rem; cursor: grab;"
                                                title="{{ __('mediamanagement::admin.drag_to_reorder') }}">
                                            <i class="fas fa-grip-vertical"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Button - Top Right --}}
                                    <button type="button"
                                            x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').removeTempGalleryFile({{ $index }})"
                                            class="btn btn-danger btn-sm position-absolute"
                                            style="top: 8px; right: 8px; padding: 0.25rem 0.5rem;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Existing gallery images --}}
                    @if(!empty($existingGallery))
                        @foreach($existingGallery as $image)
                            <div class="col-6 col-md-4 gallery-item"
                                 data-id="{{ $image['id'] }}">
                                <div class="position-relative gallery-card"
                                     style="cursor: grab;">
                                    <img src="{{ $image['thumb'] }}"
                                         alt="{{ $image['name'] }}"
                                         class="img-thumbnail w-100"
                                         style="aspect-ratio: 1; object-fit: cover;">

                                    {{-- Action Buttons - Always visible --}}
                                    <div class="position-absolute d-flex gap-1"
                                         style="top: 8px; left: 8px;">
                                        {{-- Drag Handle --}}
                                        <button type="button"
                                                class="btn btn-secondary btn-sm gallery-drag-handle"
                                                style="padding: 0.25rem 0.5rem; cursor: grab;"
                                                title="{{ __('mediamanagement::admin.drag_to_reorder') }}">
                                            <i class="fas fa-grip-vertical"></i>
                                        </button>

                                        @if($setFeaturedFromGallery && $hasFeautredImage)
                                            <button type="button"
                                                    x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').setFeaturedFromGallery({{ $image['id'] }})"
                                                    class="btn btn-success btn-sm"
                                                    style="padding: 0.25rem 0.5rem;"
                                                    title="{{ __('mediamanagement::admin.set_as_featured') }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Delete Button - Top Right --}}
                                    <button type="button"
                                            x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').deleteMedia({{ $image['id'] }}, 'gallery')"
                                            class="btn btn-danger btn-sm position-absolute"
                                            style="top: 8px; right: 8px; padding: 0.25rem 0.5rem;"
                                            title="{{ __('mediamanagement::admin.delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    </div>
                @endif

                {{-- Upload Area - Always visible --}}
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
                     class="border rounded p-3 text-center border-dashed"
                     :class="{ 'border-primary bg-light': isDragging }"
                     style="cursor: pointer; min-height: 120px;"
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

                @error('galleryFiles.*')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        @endif
    </div>
</div>

{{-- SortableJS Scripts - Only if gallery exists --}}
@if($hasGallery)
    @push('scripts')
        <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
        <script>
            document.addEventListener('livewire:initialized', function() {
                initGallerySortable();

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

                            // Existing (saved) gallery items
                            const existingItems = [];
                            const existingElements = Array.from(container.querySelectorAll('.gallery-item'));

                            existingElements.forEach((item, index) => {
                                const id = item.getAttribute('data-id');
                                if (id) {
                                    existingItems.push({
                                        id: parseInt(id),
                                        order: index + 1
                                    });
                                }
                            });

                            if (existingItems.length > 0) {
                                Livewire.dispatch('update-gallery-order', { items: existingItems });
                            }

                            // Temporary (unsaved) gallery items
                            const tempItems = [];
                            const tempElements = Array.from(container.querySelectorAll('.gallery-temp-item'));

                            tempElements.forEach((item, index) => {
                                const tempIndex = item.getAttribute('data-temp-index');
                                if (tempIndex !== null) {
                                    tempItems.push({
                                        index: parseInt(tempIndex)
                                    });
                                }
                            });

                            if (tempItems.length > 0) {
                                Livewire.dispatch('update-temp-gallery-order', { items: tempItems });
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
