<div>
    @php
        View::share(
            'pretitle',
            $announcementId ? __('announcement::admin.edit_page_pretitle') : __('announcement::admin.new_page_pretitle'),
        );
    @endphp

    @include('announcement::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="announcement_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $announcementId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'announcement', 'id' => $announcementId]) }}"
                            target="_blank" class="btn btn-outline-primary"
                            style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i
                                class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('announcement::admin.studio.editor') }}
                        </a>
                    </li>
                @endif

                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>

            <div class="card-body">
                <div class="tab-content" id="contentTabContent">

                    <!-- TEMEL BİLGİLER TAB - NO FADE for instant switching -->
                    <div class="tab-pane show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('announcement::admin.title_field') }}">
                                            <label>
                                                {{ __('announcement::admin.title_field') }}
                                                @if ($lang === get_tenant_default_locale())
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug" maxlength="255"
                                                placeholder="sayfa-url-slug">
                                            <label>
                                                {{ __('admin.page_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- İçerik editörü - AI button artık global component'te --}}
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('announcement::admin.content'),
                                    'placeholder' => __('announcement::admin.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ - Fotoğraf Ekleme --}}
                        <div class="mb-4 mt-4">
                            <hr class="mb-4">
                            <h4 class="mb-3">
                                <i class="fas fa-images me-2"></i>{{ __('announcement::admin.media_management') }}
                            </h4>

                            @livewire('mediamanagement::universal-media', [
                                'modelId' => $announcementId,
                                'modelType' => 'announcement',
                                'modelClass' => 'Modules\Announcement\App\Models\Announcement',
                                'collections' => ['featured_image', 'gallery'],
                                'sortable' => true,
                                'setFeaturedFromGallery' => true,
                            ])
                        </div>

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('announcement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('announcement::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$announcementId" model-type="announcement"
                            model-class="Modules\Announcement\App\Models\Announcement" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.announcement" :model-id="$announcementId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $announcementId ?? 'null' }};
            window.currentModuleName = 'announcement';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔥 TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('restore-active-tab', () => {
                    console.log('🔄 Tab restore tetiklendi (validation error)');

                    // forceTabRestore fonksiyonu tab-system.blade.php'de tanımlı
                    if (typeof window.forceTabRestore === 'function') {
                        setTimeout(() => {
                            window.forceTabRestore();
                        }, 100);
                    } else {
                        console.warn('⚠️ forceTabRestore fonksiyonu bulunamadı');
                    }
                });
            });
        </script>

        {{-- 🌍 UNIVERSAL SYSTEMS --}}
        @include('languagemanagement::admin.components.universal-language-scripts', [
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => $availableLanguages,
        ])

        @include('seomanagement::admin.components.universal-seo-scripts', [
            'availableLanguages' => $availableLanguages,
        ])

        @include('ai::admin.components.universal-ai-content-scripts')

        {{-- 📸 SORTABLE.JS - Gallery Sorting --}}
        <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
        <script>
            document.addEventListener('livewire:initialized', function() {
                initGallerySortable();

                // Livewire update sonrası tekrar init
                Livewire.hook('morph.updated', () => {
                    initGallerySortable();
                });

                function initGallerySortable() {
                    const container = document.getElementById('gallery-sortable-list');
                    if (!container) {
                        return;
                    }

                    // Mevcut sortable'ı temizle
                    if (window.gallerySortable) {
                        window.gallerySortable.destroy();
                        window.gallerySortable = null;
                    }

                    // Yeni sortable oluştur
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

                            // Yeni sırayı topla
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

                            // Livewire'a gönder
                            if (items.length > 0) {
                                @this.call('updateGalleryOrder', items);
                            }
                        }
                    });

                    console.log('✅ Gallery sortable initialized');
                }
            });
        </script>

        {{-- Sortable Ghost Styles --}}
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
</div>
