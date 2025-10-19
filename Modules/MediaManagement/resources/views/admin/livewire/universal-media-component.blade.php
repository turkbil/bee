<div class="media-management-component">
    <div class="row">
        {{-- FEATURED IMAGE - Sol Taraf --}}
        @if($hasFeautredImage)
            <div class="{{ $hasGallery ? 'col-md-6' : 'col-12' }} mb-4 order-md-1">
                @if(!$hideLabel)
                    <h5 class="mb-3">
                        {{ __('mediamanagement::admin.featured_image') }}
                        @if(!empty($existingFeaturedImage))
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-check"></i>
                            </span>
                        @endif
                    </h5>
                @endif

                <div class="row g-2 align-items-stretch" style="max-height: 125px;">
                    {{-- Thumbnail Preview - Sol taraf, sadece görsel varsa --}}
                    @if(!empty($existingFeaturedImage) || !empty($tempFeaturedImage))
                        <div class="col-lg-4">
                            <div class="position-relative">
                                @php
                                    // Session-based temp file öncelikli
                                    if (!empty($tempFeaturedImage)) {
                                        $thumbUrl = $tempFeaturedImage['thumb'] ?? $tempFeaturedImage['url'];
                                        $fullUrl = $tempFeaturedImage['url'];
                                        $isTemp = true;
                                    }
                                    // Existing image varsa thumbnail'ini kullan
                                    elseif (!empty($existingFeaturedImage)) {
                                        $thumbUrl = $existingFeaturedImage['thumb'] ?? $existingFeaturedImage['url'];
                                        $fullUrl = $existingFeaturedImage['url']; // Full size for lightbox
                                        $isTemp = false;
                                    } else {
                                        $thumbUrl = null;
                                        $fullUrl = null;
                                        $isTemp = false;
                                    }
                                @endphp

                                @if($thumbUrl)
                                    <div class="media-hover-container">
                                        <img src="{{ $thumbUrl }}"
                                             alt="Preview"
                                             class="img-thumbnail w-100"
                                             style="max-height: 125px; height: 125px; object-fit: contain;"
                                             data-fslightbox="featured-image"
                                             data-src="{{ $fullUrl }}">

                                        {{-- Edit Caption Button - Top Left --}}
                                        <button type="button"
                                                x-on:click.stop="$dispatch('open-caption-modal', {
                                                    mediaId: '{{ $isTemp ? 'temp-featured' : $existingFeaturedImage['id'] }}',
                                                    type: 'featured',
                                                    currentTitle: {{ json_encode($existingFeaturedImage['custom_properties']['title'] ?? []) }},
                                                    currentDescription: {{ json_encode($existingFeaturedImage['custom_properties']['description'] ?? []) }},
                                                    currentAltText: {{ json_encode($existingFeaturedImage['custom_properties']['alt_text'] ?? []) }}
                                                })"
                                                class="btn btn-primary position-absolute media-action-btn media-hover-btn"
                                                style="top: 8px; left: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Yazıları Düzenle">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        {{-- Delete Button - Top Right --}}
                                        <button type="button"
                                                x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').{{ $isTemp ? 'removeTempFeaturedImage' : 'deleteFeaturedImage' }}()"
                                                class="btn btn-danger position-absolute media-action-btn media-hover-btn"
                                                style="top: 8px; right: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Görseli Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Upload Area - Sağ taraf, her zaman görünür --}}
                    <div class="{{ (!empty($existingFeaturedImage) || !empty($tempFeaturedImage)) ? 'col-lg-8' : 'col-12' }}">
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
                             class="border rounded p-3 text-center border-dashed"
                             :class="{ 'border-primary bg-light': isDragging }"
                             style="cursor: pointer; display: flex; flex-direction: column; justify-content: center; height: 125px;"
                             @click="$refs.featuredInput.click()">

                            <div class="mb-2">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>

                            <p class="mb-1">{{ __('mediamanagement::admin.drag_drop_file') }}</p>
                            <p class="text-muted small mb-0">{{ __('mediamanagement::admin.max_file_size', ['size' => '10MB']) }}</p>

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
                </div>
            </div>
        @endif

        {{-- SEO OG IMAGE --}}
        @if($hasSeoOgImage)
            @php
                $seoOgConfig = config('mediamanagement.collection_templates.seo_og_image', []);
                $seoOgLabel = $seoOgConfig['label'] ?? 'Sosyal Medya Görseli';
                $seoOgHint = $seoOgConfig['recommended_size'] ?? null;
                $seoOgHasSiblings = $hasFeautredImage || $hasGallery;
                $seoOgColumnClass = $seoOgHasSiblings ? 'col-md-6' : 'col-12';
            @endphp

            <div class="{{ $seoOgColumnClass }} mb-4 order-md-1">
                <h5 class="mb-3">
                    {{ $seoOgLabel }}
                    @if($seoOgHint)
                        <small class="text-muted ms-2">{{ $seoOgHint }} önerilen</small>
                    @endif
                    @if(!empty($existingSeoOgImage))
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-check"></i>
                        </span>
                    @endif
                </h5>

                <div class="row g-2 align-items-stretch" style="max-height: 125px;">
                    @if(!empty($existingSeoOgImage) || !empty($tempSeoOgImage))
                        <div class="col-lg-4">
                            <div class="position-relative">
                                @php
                                    if (!empty($tempSeoOgImage)) {
                                        $thumbUrl = $tempSeoOgImage['thumb'] ?? $tempSeoOgImage['url'];
                                        $fullUrl = $tempSeoOgImage['url'];
                                        $seoIsTemp = true;
                                    } elseif (!empty($existingSeoOgImage)) {
                                        $thumbUrl = $existingSeoOgImage['thumb'] ?? $existingSeoOgImage['url'];
                                        $fullUrl = $existingSeoOgImage['url'];
                                        $seoIsTemp = false;
                                    } else {
                                        $thumbUrl = null;
                                        $fullUrl = null;
                                        $seoIsTemp = false;
                                    }
                                @endphp

                                @if($thumbUrl)
                                    <div class="media-hover-container">
                                        <img src="{{ $thumbUrl }}"
                                             alt="Preview"
                                             class="img-thumbnail w-100"
                                             style="max-height: 125px; height: 125px; object-fit: contain;"
                                             data-fslightbox="seo-og-image-{{ $this->getId() }}"
                                             data-src="{{ $fullUrl }}">

                                        <button type="button"
                                                x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').{{ $seoIsTemp ? 'removeTempSeoOgImage' : 'deleteSeoOgImage' }}()"
                                                class="btn btn-danger position-absolute media-action-btn media-hover-btn"
                                                style="top: 8px; right: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Görseli Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="{{ (!empty($existingSeoOgImage) || !empty($tempSeoOgImage)) ? 'col-lg-8' : 'col-12' }}">
                        <div x-data="{
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        @this.upload('seoOgImageFile', files[0]);
                                    }
                                }
                            }"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event)"
                             class="border rounded p-3 text-center border-dashed"
                             :class="{ 'border-primary bg-light': isDragging }"
                             style="cursor: pointer; display: flex; flex-direction: column; justify-content: center; height: 125px;"
                             @click="$refs.seoOgInput.click()">

                            <div class="mb-2">
                                <i class="fas fa-share-alt fa-2x text-muted"></i>
                            </div>

                            <p class="mb-1">{{ __('mediamanagement::admin.drag_drop_file') }}</p>
                            <p class="text-muted small mb-0">{{ __('mediamanagement::admin.max_file_size', ['size' => '10MB']) }}</p>

                            <input type="file"
                                   x-ref="seoOgInput"
                                   wire:model="seoOgImageFile"
                                   accept="image/jpeg,image/png,image/jpg,image/webp,image/gif"
                                   class="d-none">
                        </div>

                        @if($seoOgHint)
                            <div class="form-text mt-2">
                                <small>Önerilen boyut: {{ $seoOgHint }}</small>
                            </div>
                        @endif

                        @error('seoOgImageFile')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        {{-- GALLERY - Sağ Taraf --}}
        @if($hasGallery)
            <div class="col-md-6 mb-4 order-md-2">
                <h5 class="mb-3">
                    {{ __('mediamanagement::admin.gallery') }}
                </h5>

                {{-- Existing & Temporary Gallery Images - Sortable --}}
                @if(!empty($tempGallery) || !empty($existingGallery))
                    @php
                        $totalGalleryCount = count($tempGallery) + count($existingGallery);
                        $galleryLimit = 4;
                        $hasMoreGallery = $totalGalleryCount > $galleryLimit;
                    @endphp

                    <div x-data="{ showAllGallery: false }">
                        <div id="gallery-sortable-list" class="row g-1 mb-3">
                            {{-- Session-based Temporary uploads first --}}
                            @if(!empty($tempGallery))
                            @foreach($tempGallery as $index => $item)
                            <div class="col-6 col-lg-3 gallery-temp-item gallery-drag-handle"
                                 data-temp-index="{{ $index }}"
                                 x-show="showAllGallery || {{ $index }} < {{ $galleryLimit }}">
                                <div class="position-relative gallery-card media-hover-container">
                                    <img src="{{ $item['thumb'] ?? $item['url'] }}"
                                         alt="Preview"
                                         class="img-thumbnail w-100"
                                         style="aspect-ratio: {{ media_aspect_ratio('thumb') }}; object-fit: cover; cursor: move;"
                                         data-fslightbox="gallery"
                                         data-src="{{ $item['url'] }}">

                                    {{-- Edit Caption Button - Top Left --}}
                                    <button type="button"
                                            x-on:click.stop="$dispatch('open-caption-modal', {
                                                mediaId: 'temp-gallery-{{ $index }}',
                                                type: 'gallery',
                                                currentTitle: {{ json_encode($item['caption']['title'] ?? []) }},
                                                currentDescription: {{ json_encode($item['caption']['description'] ?? []) }},
                                                currentAltText: {{ json_encode($item['caption']['alt_text'] ?? []) }}
                                            })"
                                            class="btn btn-primary position-absolute media-action-btn media-hover-btn"
                                            style="top: 8px; left: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Yazıları Düzenle">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    {{-- Delete Button - Top Right --}}
                                    <button type="button"
                                            x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').removeTempGalleryFile({{ $index }})"
                                            class="btn btn-danger position-absolute media-action-btn media-hover-btn"
                                            style="top: 8px; right: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Görseli Sil">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Existing gallery images --}}
                    @if(!empty($existingGallery))
                        @foreach($existingGallery as $galleryIndex => $image)
                            @php
                                $actualIndex = count($tempGallery) + $galleryIndex;
                            @endphp
                            <div class="col-6 col-lg-3 gallery-item gallery-drag-handle"
                                 data-id="{{ $image['id'] }}"
                                 x-show="showAllGallery || {{ $actualIndex }} < {{ $galleryLimit }}">
                                <div class="position-relative gallery-card media-hover-container">
                                    <img src="{{ $image['thumb'] ?? $image['url'] }}"
                                         alt="{{ $image['name'] }}"
                                         class="img-thumbnail w-100"
                                         style="aspect-ratio: {{ media_aspect_ratio('thumb') }}; object-fit: contain; cursor: move;"
                                         data-fslightbox="gallery"
                                         data-src="{{ $image['url'] }}">

                                    {{-- Edit Caption Button - Top Left --}}
                                    <button type="button"
                                            x-on:click.stop="$dispatch('open-caption-modal', {
                                                mediaId: {{ $image['id'] }},
                                                type: 'gallery',
                                                currentTitle: {{ json_encode($image['custom_properties']['title'] ?? []) }},
                                                currentDescription: {{ json_encode($image['custom_properties']['description'] ?? []) }},
                                                currentAltText: {{ json_encode($image['custom_properties']['alt_text'] ?? []) }}
                                            })"
                                            class="btn btn-primary position-absolute media-action-btn media-hover-btn"
                                            style="top: 8px; left: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Yazıları Düzenle">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    {{-- Delete Button - Top Right --}}
                                    <button type="button"
                                            x-on:click.stop="window.Livewire.find('{{ $this->getId() }}').deleteMedia({{ $image['id'] }}, 'gallery')"
                                            class="btn btn-danger position-absolute media-action-btn media-hover-btn"
                                            style="top: 8px; right: 8px; padding: 0.4rem 0.65rem; z-index: 10; font-size: 0.9rem;"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Görseli Sil">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                        </div>

                        {{-- Show More / Show Less Button --}}
                        @if($hasMoreGallery)
                            <div class="text-center mb-3">
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        @click="showAllGallery = !showAllGallery; setTimeout(() => { if (typeof initGallerySortable === 'function') { initGallerySortable(); } }, 200)"
                                        x-text="showAllGallery ? 'Daha Az Göster' : 'Tümünü Göster ({{ $totalGalleryCount }} görsel)'">
                                </button>
                            </div>
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

{{-- SortableJS & fslightbox Scripts --}}
@if($hasGallery || $hasFeautredImage)
    @push('scripts')
        <script src="{{ asset('admin-assets/libs/fslightbox/index.js') }}"></script>
        @if($hasGallery)
        <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
        @endif
        <script>
            document.addEventListener('livewire:initialized', function() {
                // Refresh fslightbox when Livewire updates
                refreshFsLightbox();

                Livewire.hook('morph.updated', ({ el, component }) => {
                    refreshFsLightbox();
                });

                @if($hasGallery)
                initGallerySortable();

                function initGallerySortable() {
                    const container = document.getElementById('gallery-sortable-list');
                    if (!container) {
                        console.log('⚠️ Gallery container not found');
                        return;
                    }

                    // Mevcut instance'ı temizle
                    if (window.gallerySortable) {
                        window.gallerySortable.destroy();
                        window.gallerySortable = null;
                    }

                    // Tüm öğeleri say (gizli olanlar dahil)
                    const allItems = container.querySelectorAll('.gallery-item, .gallery-temp-item');
                    const visibleItems = Array.from(allItems).filter(item => {
                        return item.offsetParent !== null; // x-show ile gizlenmediyse
                    });

                    console.log('📊 Sortable Init:', {
                        total: allItems.length,
                        visible: visibleItems.length
                    });

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

                            // TÜM öğeleri al (gizli olanlar dahil) - DOM sırasına göre
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

                            console.log('🔄 Gallery Order Update:', existingItems);

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
                @endif
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

            /* fslightbox arkaplan opacity override */
            .fslightbox-container {
                background: rgba(30, 30, 30, 0.65) !important; /* Çok daha şeffaf */
            }

            /* Media caption modal - Manage page style */
            .media-caption-modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .media-caption-modal {
                max-width: 700px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            }

            .media-caption-modal .card {
                margin: 0;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
        </style>

        {{-- Caption Modal - Manage Page Style --}}
        <div x-data="captionModal"
             x-show="isOpen"
             style="display: none;"
             @click.self="close()"
             class="media-caption-modal-backdrop">
            <div class="media-caption-modal" x-ref="modal">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pencil-alt me-2"></i>
                            {{ __('mediamanagement::admin.edit_media_caption') }}
                        </h3>
                        <div class="card-actions">
                            <button type="button" class="btn-close" @click="close()"></button>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Language Tabs - Simple Button Style (No Animation) --}}
                        <div class="d-flex gap-2 mb-3 border-bottom">
                            @foreach(\App\Services\TenantLanguageProvider::getActiveLanguageCodes() as $langCode)
                                <button type="button"
                                        class="btn btn-link p-2 language-switch-btn"
                                        :class="currentLang === '{{ $langCode }}' ? 'text-primary' : 'text-muted'"
                                        style="border-top: none; border-right: none; border-left: none; border-image: initial; border-radius: 0.25rem 0.25rem 0px 0px !important; transition: all 0.15s;"
                                        :style="currentLang === '{{ $langCode }}' ? 'border-bottom: 2px solid var(--tblr-primary); --tblr-primary: #066fd1 !important; --tblr-primary-rgb: 6, 111, 209 !important;' : 'border-bottom: 2px solid transparent;'"
                                        @click.prevent.stop="currentLang = '{{ $langCode }}'">
                                    {{ strtoupper($langCode) }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Tab Content --}}
                        <div class="tab-content">
                            {{-- Form Fields --}}
                            <div class="mb-3">
                                <label class="form-label required">
                                    {{ __('mediamanagement::admin.title') }}
                                </label>
                                <input type="text"
                                       class="form-control"
                                       x-model="captionData.title[currentLang]">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('mediamanagement::admin.description') }}
                                </label>
                                <textarea class="form-control"
                                          rows="4"
                                          x-model="captionData.description[currentLang]"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('mediamanagement::admin.alt_text') }}
                                    <span class="text-muted">(SEO)</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       x-model="captionData.alt_text[currentLang]">
                            </div>

                            <div class="alert alert-info mb-0">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle fs-2 me-2"></i>
                                    </div>
                                    <div>
                                        {{ __('mediamanagement::admin.caption_helper') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-ghost-secondary" @click="close()">
                            <i class="fas fa-times me-1"></i>
                            {{ __('admin.cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary ms-2" @click="save()">
                            <i class="fas fa-save me-1"></i>
                            {{ __('admin.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('captionModal', () => ({
                    isOpen: false,
                    mediaId: null,
                    type: null,
                    currentLang: '{{ app()->getLocale() }}',
                    captionData: {
                        title: {},
                        description: {},
                        alt_text: {}
                    },

                    init() {
                        // Initialize empty language data dynamically
                        const languages = @json(\App\Services\TenantLanguageProvider::getActiveLanguageCodes());
                        languages.forEach(lang => {
                            this.captionData.title[lang] = '';
                            this.captionData.description[lang] = '';
                            this.captionData.alt_text[lang] = '';
                        });

                        // Listen for open event
                        window.addEventListener('open-caption-modal', (e) => {
                            this.open(e.detail);
                        });

                        // Listen for close event from backend
                        Livewire.on('close-caption-modal', () => {
                            this.close();
                        });
                    },

                    open(data) {
                        console.log('🎯 Caption Modal Open - Data received:', data);

                        // Bootstrap tooltip'lerini kapat ve dispose et
                        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
                            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                                const tooltip = bootstrap.Tooltip.getInstance(el);
                                if (tooltip) {
                                    tooltip.dispose();
                                }
                            });
                        }

                        // Açık olan tüm tooltip elementlerini de kapat
                        document.querySelectorAll('.tooltip').forEach(el => el.remove());

                        this.mediaId = data.mediaId;
                        this.type = data.type;

                        // Load existing data or initialize empty - CREATE NEW OBJECT EACH TIME!
                        const languages = @json(\App\Services\TenantLanguageProvider::getActiveLanguageCodes());

                        const createEmptyData = () => {
                            const obj = {};
                            languages.forEach(lang => obj[lang] = '');
                            return obj;
                        };

                        // CRITICAL FIX: Convert arrays to objects if needed
                        this.captionData.title = (Array.isArray(data.currentTitle) && data.currentTitle.length === 0)
                            ? createEmptyData()
                            : (data.currentTitle || createEmptyData());

                        this.captionData.description = (Array.isArray(data.currentDescription) && data.currentDescription.length === 0)
                            ? createEmptyData()
                            : (data.currentDescription || createEmptyData());

                        this.captionData.alt_text = (Array.isArray(data.currentAltText) && data.currentAltText.length === 0)
                            ? createEmptyData()
                            : (data.currentAltText || createEmptyData());

                        console.log('📝 Caption Data After Load:', this.captionData);

                        this.isOpen = true;

                        // Disable fslightbox when modal is open
                        document.body.style.overflow = 'hidden';
                    },

                    close() {
                        this.isOpen = false;

                        // Re-enable body scroll
                        document.body.style.overflow = '';

                        // Tooltip'leri yeniden başlat
                        setTimeout(() => {
                            if (typeof initMediaTooltips === 'function') {
                                initMediaTooltips();
                            }
                        }, 100);
                    },

                    save() {
                        console.log('💾 Caption Modal Save - mediaId:', this.mediaId);
                        console.log('💾 Caption Data:', this.captionData);

                        // Livewire 3 syntax - dispatch event to component
                        window.Livewire.find('{{ $this->getId() }}').call('saveMediaCaption', this.mediaId, this.captionData);
                    }
                }));
            });
        </script>

        {{-- Tooltip Initialization & Custom Styles --}}
        <style>
            /* Media Hover Container */
            .media-hover-container {
                position: relative;
            }

            /* Media Action Buttons - Hidden by default */
            .media-hover-btn {
                opacity: 0;
                transition: opacity 0.2s ease-in-out;
            }

            /* Show buttons on container hover */
            .media-hover-container:hover .media-hover-btn {
                opacity: 1;
            }

            /* Primary Button - Normal */
            .media-action-btn.btn-primary {
                background-color: var(--tblr-primary);
                border-color: var(--tblr-primary);
                color: white;
                transition: all 0.2s ease-in-out;
            }

            /* Primary Button - Hover (ters renk) */
            .media-action-btn.btn-primary:hover {
                background-color: white !important;
                border-color: var(--tblr-primary) !important;
                color: var(--tblr-primary) !important;
            }

            /* Danger Button - Normal */
            .media-action-btn.btn-danger {
                background-color: var(--tblr-danger);
                border-color: var(--tblr-danger);
                color: white;
                transition: all 0.2s ease-in-out;
            }

            /* Danger Button - Hover (ters renk) */
            .media-action-btn.btn-danger:hover {
                background-color: white !important;
                border-color: var(--tblr-danger) !important;
                color: var(--tblr-danger) !important;
            }

            /* Gallery Drag & Drop Cursors */
            .gallery-drag-handle {
                cursor: grab;
            }

            .gallery-drag-handle:active {
                cursor: grabbing;
            }

            .gallery-drag-handle.sortable-chosen {
                cursor: grabbing;
                opacity: 1;
                margin: 0 !important;
                padding: 0 !important;
            }

            .gallery-drag-handle.sortable-chosen .gallery-card {
                margin: 0 !important;
                padding: 0 !important;
            }

            .gallery-drag-handle.sortable-chosen img {
                border-color: var(--tblr-primary) !important;
                border-width: 2px !important;
            }

            .gallery-drag-handle.sortable-ghost {
                opacity: 0.3;
            }

            /* Gallery Item - Remove padding for tighter layout */
            .gallery-card {
                padding: 0;
                margin: 0;
            }

            /* Gallery Thumbnail - Match featured image height */
            .gallery-card img {
                max-height: 125px;
                min-height: 125px;
                height: 125px;
            }
        </style>

        <script>
            // Initialize Bootstrap tooltips for media buttons
            document.addEventListener('livewire:navigated', function() {
                initMediaTooltips();
            });

            document.addEventListener('DOMContentLoaded', function() {
                initMediaTooltips();
            });

            // Debounce timer for tooltip re-initialization
            // Global tooltip timer (avoid duplicate declaration)
            window.tooltipInitTimer = window.tooltipInitTimer || null;

            // Re-initialize after Livewire updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                // Sadece media component içindeki morph'larda çalış
                if (!el.closest('[wire\\:id="{{ $this->getId() }}"]')) {
                    return;
                }

                // Debounce: Hızlı ard arda güncellmelerde sadece son çağrıyı işle
                clearTimeout(window.tooltipInitTimer);
                window.tooltipInitTimer = setTimeout(() => {
                    initMediaTooltips();
                }, 200);
            });

            function initMediaTooltips() {
                // Bootstrap kontrolü - sessiz fail
                if (typeof bootstrap === 'undefined' || typeof bootstrap.Tooltip === 'undefined') {
                    return;
                }

                // Sadece bu component içindeki tooltip'leri init et
                const componentEl = document.querySelector('[wire\\:id="{{ $this->getId() }}"]');
                if (!componentEl) return;

                const tooltipTriggerList = componentEl.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(el => {
                    try {
                        // Destroy existing tooltip if any
                        const existingTooltip = bootstrap.Tooltip.getInstance(el);
                        if (existingTooltip) {
                            existingTooltip.dispose();
                        }
                        // Create new tooltip
                        new bootstrap.Tooltip(el);
                    } catch (error) {
                        // Sessiz fail - spam önleme
                    }
                });
            }
        </script>
    @endpush
@endif
