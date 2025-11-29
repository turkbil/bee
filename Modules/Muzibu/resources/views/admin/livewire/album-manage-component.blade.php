<div>
    @php
        View::share(
            'pretitle',
            $albumId ? __('muzibu::admin.album.edit_album_pretitle') : __('muzibu::admin.album.new_album_pretitle'),
        );
    @endphp

    {{-- Bulk Upload Button (Only for existing albums) --}}
    @if($albumId)
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.muzibu.album.bulk-upload', ['id' => $albumId]) }}" class="btn btn-primary">
                <i class="fas fa-cloud-upload-alt me-1"></i>
                {{ __('muzibu::admin.bulk_upload.button') }}
            </a>
        </div>
    @endif

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="album_active_tab">

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
                                                placeholder="{{ __('muzibu::admin.album.title_field') }}">
                                            <label>
                                                {{ __('muzibu::admin.album.title_field') }}
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
                                                {{ __('admin.album_url_slug') }}
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

                                <!-- Sanatçı (Sadece ilk dilde göster) -->
                                @if($lang === get_tenant_default_locale())
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <select wire:model="inputs.artist_id"
                                                class="form-control @error('inputs.artist_id') is-invalid @enderror"
                                                id="artist_select">
                                                <option value="">{{ __('muzibu::admin.album.select_artist') }}</option>
                                                @if(isset($this->activeArtists))
                                                    @foreach($this->activeArtists as $artist)
                                                        <option value="{{ $artist->artist_id }}">
                                                            {{ $artist->getTranslated('title', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label for="artist_select">
                                                {{ __('muzibu::admin.album.artist') }}
                                            </label>
                                            @error('inputs.artist_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ --}}
                        <div class="mb-4">
                            <livewire:mediamanagement::universal-media
                                wire:id="album-media-component"
                                :model-id="$albumId"
                                model-type="album"
                                model-class="Modules\Muzibu\App\Models\Album"
                                :collections="['hero']"
                                :key="'universal-media-' . ($albumId ?? 'new')"
                            />
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
                                    'label' => __('muzibu::admin.album.description'),
                                    'placeholder' => __('muzibu::admin.album.description_placeholder'),
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
                                    <label>{{ __('muzibu::admin.album.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('muzibu::admin.album.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$albumId" model-type="album"
                            model-class="Modules\Muzibu\App\Models\Album" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.muzibu.album" :model-id="$albumId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $albumId ?? 'null' }};
            window.currentModuleName = 'album';
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
