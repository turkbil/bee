<div x-data="{ isMediaUploading: false }">
    @php
        View::share(
            'pretitle',
            $radioId ? __('muzibu::admin.radio.edit_radio_pretitle') : __('muzibu::admin.radio.new_radio_pretitle'),
        );
    @endphp


    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="radio_active_tab">

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
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating mb-3 mb-md-0">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('muzibu::admin.radio.title_field') }}">
                                            <label>
                                                {{ __('muzibu::admin.radio.title_field') }}
                                                @if ($lang === get_tenant_default_locale())
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug"
                                                id="slug_{{ $lang }}"
                                                maxlength="255"
                                                placeholder="sayfa-url-slug">
                                            <label for="slug_{{ $lang }}">
                                                {{ __('admin.radio_url_slug') }}
                                                <small class="ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="">
                                                    {{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ --}}
                        <div class="mb-4">
                            <livewire:mediamanagement::universal-media
                                wire:id="radio-media-component"
                                :model-id="$radioId"
                                model-type="radio"
                                model-class="Modules\Muzibu\App\Models\Radio"
                                :collections="['hero']"
                                :key="'universal-media-' . ($radioId ?? 'new')"
                            />
                        </div>

                        <!-- Çalma Listeleri (Dual Listbox) -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('muzibu::admin.radio.playlists') }}</label>
                                <div class="dual-listbox-wrapper">
                                    {{-- Search input with float label --}}
                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control"
                                                placeholder="Çalma listesi ara..."
                                                wire:model.live.debounce.300ms="playlistSearch"
                                                id="playlist-search">
                                            <label for="playlist-search">
                                                <i class="fa-solid fa-magnifying-glass me-2"></i>
                                                Çalma Listesi Ara
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-5">
                                            <label class="form-label small">Tüm Çalma Listeleri</label>
                                            <div class="listbox" id="available-playlists">
                                                @foreach($this->availablePlaylists as $playlist)
                                                    <div class="listbox-item"
                                                        data-value="{{ $playlist->playlist_id }}"
                                                        data-title="{{ strtolower($playlist->getTranslated('title', app()->getLocale())) }}">
                                                        {{ $playlist->getTranslated('title', app()->getLocale()) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-2 d-flex align-items-center justify-content-center">
                                            <div class="transfer-buttons">
                                                <button type="button" class="btn btn-sm btn-primary mb-2" onclick="transferPlaylistsRight()">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="transferPlaylistsLeft()">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-5">
                                            <label class="form-label small">Seçilen Çalma Listeleri</label>
                                            <div class="listbox" id="selected-playlists">
                                                @foreach($this->selectedPlaylists as $playlist)
                                                    <div class="listbox-item"
                                                        data-value="{{ $playlist->playlist_id }}"
                                                        data-title="{{ strtolower($playlist->getTranslated('title', app()->getLocale())) }}">
                                                        {{ $playlist->getTranslated('title', app()->getLocale()) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <small class="">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            Listeye tıklayıp seç, ok tuşları ile taşı
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                {{-- İçerik editörü - AI button artık global component'te --}}
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'description',
                                    'label' => __('muzibu::admin.radio.content'),
                                    'placeholder' => __('muzibu::admin.radio.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Öne Çıkan & Aktif/Pasif -->
                        <div class="row mb-3 mt-4">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_featured" name="is_featured" wire:model="inputs.is_featured"
                                        value="1"
                                        {{ isset($inputs['is_featured']) && $inputs['is_featured'] ? 'checked' : '' }} />

                                    <div class="state p-warning p-on ms-2">
                                        <label>{{ __('muzibu::admin.radio.featured') }}</label>
                                    </div>
                                    <div class="state p-off ms-2">
                                        <label>{{ __('muzibu::admin.radio.not_featured') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1"
                                        {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('muzibu::admin.radio.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('muzibu::admin.radio.inactive') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$radioId" model-type="radio"
                            model-class="Modules\Muzibu\App\Models\Radio" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.muzibu.radio" :model-id="$radioId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $radioId ?? 'null' }};
            window.currentModuleName = 'radio';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔥 TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                // 🖼️ MEDIA UPLOAD STATE - Görsel yüklenirken butonları kilitle
                Livewire.on('media-upload-started', () => {
                    console.log('📸 Media upload started - locking buttons');
                    window.dispatchEvent(new CustomEvent('media-upload-started'));
                });

                Livewire.on('media-upload-completed', () => {
                    console.log('✅ Media upload completed - unlocking buttons');
                    window.dispatchEvent(new CustomEvent('media-upload-completed'));
                });

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

                // 🔄 BROWSER REDIRECT - Event işlendikten sonra yönlendir
                Livewire.on('browser', (event) => {
                    console.log('🔄 Browser event:', event);

                    if (event.action === 'redirect') {
                        const delay = event.delay || 0;
                        console.log(`🔄 Redirecting to ${event.url} after ${delay}ms`);

                        setTimeout(() => {
                            window.location.href = event.url;
                        }, delay);
                    }
                });
            });

            // 🎯 RADIO-SPECIFIC DUAL LISTBOX FUNCTIONS
            function transferPlaylistsRight() {
                dualListboxTransfer('available-playlists', 'selected-playlists', 'right', updateLivewirePlaylists);
            }

            function transferPlaylistsLeft() {
                dualListboxTransfer('available-playlists', 'selected-playlists', 'left', updateLivewirePlaylists);
            }

            // 🔄 Update Livewire (Radio-specific)
            function updateLivewirePlaylists() {
                const selectedValues = getDualListboxValues('selected-playlists');
                @this.set('inputs.playlist_ids', selectedValues);
            }
        </script>

        {{-- 🌍 UNIVERSAL SYSTEMS --}}
        @include('languagemanagement::admin.components.universal-language-scripts', [
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => $availableLanguages,
        ])

        @include('seomanagement::admin.components.universal-seo-scripts', [
            'availableLanguages' => $availableLanguages,
        ])
    @endpush
</div>
