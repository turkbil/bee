<div x-data="{ isMediaUploading: false }">
    @php
        View::share(
            'pretitle',
            $playlistId ? __('muzibu::admin.playlist.edit_playlist_pretitle') : __('muzibu::admin.playlist.new_playlist_pretitle'),
        );
    @endphp


    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="playlist_active_tab">

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
                                                placeholder="{{ __('muzibu::admin.playlist.title_field') }}">
                                            <label>
                                                {{ __('muzibu::admin.playlist.title_field') }}
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
                                                {{ __('admin.playlist_url_slug') }}
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
                                wire:id="playlist-media-component"
                                :model-id="$playlistId"
                                model-type="playlist"
                                model-class="Modules\Muzibu\App\Models\Playlist"
                                :collections="['hero']"
                                :key="'universal-media-' . ($playlistId ?? 'new')"
                            />
                        </div>

                        <!-- Dual Listboxes: Sektörler + Radyolar (yan yana) -->
                        <div class="row g-3 mb-4 mt-4">
                            <!-- Sektörler (sol taraf - col-6) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('muzibu::admin.playlist.sectors') }}</label>
                                <div class="dual-listbox-wrapper">
                                    {{-- Search input with float label --}}
                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control"
                                                placeholder="Sektör ara..."
                                                wire:model.live.debounce.300ms="sectorSearch"
                                                id="sector-search">
                                            <label for="sector-search">
                                                <i class="fa-solid fa-magnifying-glass me-2"></i>
                                                Sektör Ara
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-5">
                                            <label class="form-label small">Tüm Sektörler</label>
                                            <div class="listbox" id="available-sectors">
                                                @if(isset($this->activeSectors))
                                                    @foreach($this->activeSectors as $sector)
                                                        @if(!in_array($sector->sector_id, $inputs['sector_ids'] ?? []))
                                                            <div class="listbox-item"
                                                                data-value="{{ $sector->sector_id }}"
                                                                data-title="{{ strtolower($sector->getTranslated('title', app()->getLocale())) }}">
                                                                {{ $sector->getTranslated('title', app()->getLocale()) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-2 d-flex align-items-center justify-content-center">
                                            <div class="transfer-buttons">
                                                <button type="button" class="btn btn-sm btn-primary mb-2" onclick="transferSectorsRight()">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="transferSectorsLeft()">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-5">
                                            <label class="form-label small">Seçilen Sektörler</label>
                                            <div class="listbox" id="selected-sectors">
                                                @if(isset($this->activeSectors))
                                                    @foreach($this->activeSectors as $sector)
                                                        @if(in_array($sector->sector_id, $inputs['sector_ids'] ?? []))
                                                            <div class="listbox-item"
                                                                data-value="{{ $sector->sector_id }}"
                                                                data-title="{{ strtolower($sector->getTranslated('title', app()->getLocale())) }}">
                                                                {{ $sector->getTranslated('title', app()->getLocale()) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <small class="">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            {{ __('muzibu::admin.playlist.sectors_help') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Radyolar (sağ taraf - col-6) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('muzibu::admin.playlist.radios') }}</label>
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
                                                @if(isset($this->activeRadios))
                                                    @foreach($this->activeRadios as $radio)
                                                        @if(!in_array($radio->radio_id, $inputs['radio_ids'] ?? []))
                                                            <div class="listbox-item"
                                                                data-value="{{ $radio->radio_id }}"
                                                                data-title="{{ strtolower($radio->getTranslated('title', app()->getLocale())) }}">
                                                                {{ $radio->getTranslated('title', app()->getLocale()) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
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
                                                @if(isset($this->activeRadios))
                                                    @foreach($this->activeRadios as $radio)
                                                        @if(in_array($radio->radio_id, $inputs['radio_ids'] ?? []))
                                                            <div class="listbox-item"
                                                                data-value="{{ $radio->radio_id }}"
                                                                data-title="{{ strtolower($radio->getTranslated('title', app()->getLocale())) }}">
                                                                {{ $radio->getTranslated('title', app()->getLocale()) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
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

                        <!-- 2. Özellikler (Aktif/Pasif Hariç) -->
                        <div class="row mb-4">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_system" name="is_system" wire:model="inputs.is_system"
                                        value="1"
                                        {{ !isset($inputs['is_system']) || $inputs['is_system'] ? 'checked' : '' }} />

                                    <div class="state p-warning p-on ms-2">
                                        <label>{{ __('muzibu::admin.playlist.system_playlist') }}</label>
                                    </div>
                                    <div class="state p-off ms-2">
                                        <label>{{ __('muzibu::admin.playlist.user_playlist') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_public" name="is_public" wire:model="inputs.is_public"
                                        value="1"
                                        {{ !isset($inputs['is_public']) || $inputs['is_public'] ? 'checked' : '' }} />

                                    <div class="state p-info p-on ms-2">
                                        <label>{{ __('muzibu::admin.playlist.public') }}</label>
                                    </div>
                                    <div class="state p-off ms-2">
                                        <label>{{ __('muzibu::admin.playlist.private') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_radio" name="is_radio" wire:model="inputs.is_radio"
                                        value="1"
                                        {{ isset($inputs['is_radio']) && $inputs['is_radio'] ? 'checked' : '' }} />

                                    <div class="state p-primary p-on ms-2">
                                        <label>{{ __('muzibu::admin.playlist.radio_mode') }}</label>
                                    </div>
                                    <div class="state p-off ms-2">
                                        <label>{{ __('muzibu::admin.playlist.playlist_mode') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Açıklama -->
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
                                    'label' => __('muzibu::admin.playlist.description'),
                                    'placeholder' => __('muzibu::admin.playlist.description_placeholder'),
                                ])
                            </div>
                        @endforeach

                        <!-- 4. Aktif/Pasif -->
                        <div class="row mb-3 mt-4">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1"
                                        {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('muzibu::admin.playlist.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('muzibu::admin.playlist.inactive') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$playlistId" model-type="playlist"
                            model-class="Modules\Muzibu\App\Models\Playlist" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.muzibu.playlist" :model-id="$playlistId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $playlistId ?? 'null' }};
            window.currentModuleName = 'playlist';
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

            // 🎯 PLAYLIST-SPECIFIC DUAL LISTBOX FUNCTIONS
            function transferSectorsRight() {
                dualListboxTransfer('available-sectors', 'selected-sectors', 'right', updateLivewireSectors);
            }

            function transferSectorsLeft() {
                dualListboxTransfer('available-sectors', 'selected-sectors', 'left', updateLivewireSectors);
            }

            function transferRadiosRight() {
                dualListboxTransfer('available-radios', 'selected-radios', 'right', updateLivewireRadios);
            }

            function transferRadiosLeft() {
                dualListboxTransfer('available-radios', 'selected-radios', 'left', updateLivewireRadios);
            }

            // 🔄 Update Livewire (Playlist-specific)
            function updateLivewireSectors() {
                const selectedValues = getDualListboxValues('selected-sectors');
                @this.set('inputs.sector_ids', selectedValues);
            }

            function updateLivewireRadios() {
                const selectedValues = getDualListboxValues('selected-radios');
                @this.set('inputs.radio_ids', selectedValues);
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
