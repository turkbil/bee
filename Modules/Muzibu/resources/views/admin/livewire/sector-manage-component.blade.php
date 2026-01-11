<div>
    @php
        View::share(
            'pretitle',
            $sectorId ? __('muzibu::admin.sector.edit_sector_pretitle') : __('muzibu::admin.sector.new_sector_pretitle'),
        );
    @endphp


    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="sector_active_tab">

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
                                                placeholder="{{ __('muzibu::admin.sector.title_field') }}">
                                            <label>
                                                {{ __('muzibu::admin.sector.title_field') }}
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
                                                {{ __('admin.sector_url_slug') }}
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
                                wire:id="sector-media-component"
                                :model-id="$sectorId"
                                model-type="sector"
                                model-class="Modules\Muzibu\App\Models\Sector"
                                :collections="['hero']"
                                :key="'universal-media-' . ($sectorId ?? 'new')"
                            />
                        </div>

                        <!-- Dual Listboxes: Radyolar + Çalma Listeleri (yan yana) -->
                        <div class="row g-3 mb-4 mt-4">
                            <!-- Radyolar (sol taraf - col-6) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('muzibu::admin.sector.radios') }}</label>
                                <div class="dual-listbox-wrapper">
                                    {{-- Search input with float label --}}
                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control"
                                                placeholder="Radyo ara..."
                                                wire:model.live.debounce.300ms="radioSearch"
                                                id="radio-search">
                                            <label for="radio-search">
                                                <i class="fa-solid fa-magnifying-glass me-2"></i>
                                                Radyo Ara
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-5">
                                            <label class="form-label small">Tüm Radyolar</label>
                                            <div class="listbox" id="available-radios">
                                                @foreach($this->availableRadios as $radio)
                                                    <div class="listbox-item"
                                                        data-value="{{ $radio->radio_id }}"
                                                        data-title="{{ strtolower($radio->getTranslated('title', app()->getLocale())) }}">
                                                        {{ $radio->getTranslated('title', app()->getLocale()) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-2 d-flex align-items-center justify-content-center">
                                            <div class="transfer-buttons">
                                                <button type="button" class="btn btn-sm btn-primary mb-2" onclick="transferRadiosRight()">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="transferRadiosLeft()">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-5">
                                            <label class="form-label small">Seçilen Radyolar</label>
                                            <div class="listbox" id="selected-radios">
                                                @foreach($this->selectedRadios as $radio)
                                                    <div class="listbox-item"
                                                        data-value="{{ $radio->radio_id }}"
                                                        data-title="{{ strtolower($radio->getTranslated('title', app()->getLocale())) }}">
                                                        {{ $radio->getTranslated('title', app()->getLocale()) }}
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

                            <!-- Çalma Listeleri (sağ taraf - col-6) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('muzibu::admin.sector.playlists') }}</label>
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
                                    'label' => __('muzibu::admin.sector.content'),
                                    'placeholder' => __('muzibu::admin.sector.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3 mt-4">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('muzibu::admin.sector.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('muzibu::admin.sector.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$sectorId" model-type="sector"
                            model-class="Modules\Muzibu\App\Models\Sector" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.sector" :model-id="$sectorId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $sectorId ?? 'null' }};
            window.currentModuleName = 'sector';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔍 DEBUG: Livewire Component Verileri
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const component = @this;

                    console.log('🔍 ========== SECTOR MANAGE DEBUG ==========');
                    console.log('📊 Sector ID:', {{ $sectorId ?? 'null' }});
                    console.log('🌍 Current Language:', component.currentLanguage);
                    console.log('🌍 Available Languages:', component.availableLanguages);

                    // multiLangInputs detaylı
                    const multiLang = JSON.parse(JSON.stringify(component.multiLangInputs));
                    console.log('📝 multiLangInputs.tr:', multiLang.tr);
                    console.log('   ├─ title:', multiLang.tr?.title || '🔴 BOŞ');
                    console.log('   ├─ slug:', multiLang.tr?.slug || '🔴 BOŞ');
                    console.log('   └─ description:', multiLang.tr?.description ? multiLang.tr.description.substring(0, 100) + '...' : '🔴 BOŞ');

                    // inputs detaylı
                    const inputs = JSON.parse(JSON.stringify(component.inputs));
                    console.log('⚙️ inputs:', inputs);
                    console.log('   ├─ is_active:', inputs.is_active);
                    console.log('   ├─ radio_ids (count):', inputs.radio_ids?.length || 0);
                    console.log('   ├─ radio_ids (values):', inputs.radio_ids);
                    console.log('   ├─ playlist_ids (count):', inputs.playlist_ids?.length || 0);
                    console.log('   └─ playlist_ids (values):', inputs.playlist_ids);

                    // TinyMCE Editor kontrolü
                    setTimeout(() => {
                        const editors = document.querySelectorAll('.tinymce-editor');
                        console.log('📝 TinyMCE Editor sayısı:', editors.length);
                        editors.forEach((editor, index) => {
                            console.log(`  Editor ${index}:`, {
                                id: editor.id,
                                wireModel: editor.getAttribute('wire:model'),
                                value: editor.value?.substring(0, 100) + '...'
                            });
                        });
                    }, 500);

                    // Dual Listbox kontrolü
                    const selectedRadios = document.querySelectorAll('#selected-radios .listbox-item');
                    const selectedPlaylists = document.querySelectorAll('#selected-playlists .listbox-item');
                    console.log('📻 Selected Radios (DOM):', selectedRadios.length);
                    console.log('🎵 Selected Playlists (DOM):', selectedPlaylists.length);

                    console.log('🔍 ========================================');

                    // ✅ Dual listbox'u yeniden initialize et (çift tıklama için)
                    if (typeof window.initializeDualListbox === 'function') {
                        window.initializeDualListbox();
                        console.log('✅ Dual listbox initialized (double-click enabled)');
                    }
                }, 1000);
            });

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

            // 🎯 SECTOR-SPECIFIC DUAL LISTBOX FUNCTIONS
            function transferRadiosRight() {
                dualListboxTransfer('available-radios', 'selected-radios', 'right', updateLivewireRadios);
            }

            function transferRadiosLeft() {
                dualListboxTransfer('available-radios', 'selected-radios', 'left', updateLivewireRadios);
            }

            function transferPlaylistsRight() {
                dualListboxTransfer('available-playlists', 'selected-playlists', 'right', updateLivewirePlaylists);
            }

            function transferPlaylistsLeft() {
                dualListboxTransfer('available-playlists', 'selected-playlists', 'left', updateLivewirePlaylists);
            }

            // 🔄 Update Livewire (Sector-specific)
            function updateLivewireRadios() {
                const selectedValues = getDualListboxValues('selected-radios');
                @this.set('inputs.radio_ids', selectedValues);
            }

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
