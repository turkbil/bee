<div>
    @php
        View::share(
            'pretitle',
            $songId ? __('muzibu::admin.song.edit_song_pretitle') : __('muzibu::admin.song.new_song_pretitle'),
        );
    @endphp


    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="song_active_tab">

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
                                                placeholder="{{ __('muzibu::admin.song.title_field') }}">
                                            <label>
                                                {{ __('muzibu::admin.song.title_field') }}
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
                                                {{ __('admin.song_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    {{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Album, Genre, Duration Seçimleri (Sadece ilk dilde göster) -->
                                @if($lang === get_tenant_default_locale())
                                <div class="row mb-4">
                                    <div class="col-12 col-md-4">
                                        <div class="form-floating">
                                            <select wire:model="inputs.album_id"
                                                class="form-control @error('inputs.album_id') is-invalid @enderror"
                                                id="album_select">
                                                <option value="">{{ __('muzibu::admin.song.select_album') }}</option>
                                                @if(isset($this->activeAlbums))
                                                    @foreach($this->activeAlbums as $album)
                                                        <option value="{{ $album->album_id }}">
                                                            {{ $album->getTranslated('title', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label for="album_select">
                                                {{ __('muzibu::admin.song.album') }}
                                            </label>
                                            @error('inputs.album_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <div class="form-floating">
                                            <select wire:model="inputs.genre_id"
                                                class="form-control @error('inputs.genre_id') is-invalid @enderror"
                                                id="genre_select">
                                                <option value="">{{ __('muzibu::admin.song.select_genre') }}</option>
                                                @if(isset($this->activeGenres))
                                                    @foreach($this->activeGenres as $genre)
                                                        <option value="{{ $genre->genre_id }}">
                                                            {{ $genre->getTranslated('title', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label for="genre_select">
                                                {{ __('muzibu::admin.song.genre') }}
                                                <span class="required-star">★</span>
                                            </label>
                                            @error('inputs.genre_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                                @endif

                            </div>
                        @endforeach

                        {{-- ŞARKI DOSYASI & SÜRE - Tabler.io Design --}}
                        <div class="row mb-4">
                            {{-- Şarkı Dosyası Yükleme --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label required" style="font-size: 0.875rem;">
                                        <i class="fa fa-music me-1" style="font-size: 0.875rem;"></i>
                                        {{ __('muzibu::admin.song.audio_file') }}
                                    </label>

                                    @if($inputs['file_path'] ?? null)
                                        {{-- Mevcut Şarkı --}}
                                        <div class="card mb-3">
                                            <div class="card-status-top bg-success"></div>
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <span class="avatar avatar-lg" style="background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xNCAzdjRhMSAxIDAgMCAwIDEgMWg0Ii8+PHBhdGggZD0iTTE3IDIxaC0xMGEyIDIgMCAwIDEgLTIgLTJ2LTE0YTIgMiAwIDAgMSAyIC0yaDdsNSA1djExYTIgMiAwIDAgMSAtMiAyeiIvPjxwYXRoIGQ9Ik0xMiAxMWwwIDYiLz48cGF0aCBkPSJNOSAxNGw2IDAiLz48L3N2Zz4=)"></span>
                                                    </div>
                                                    <div class="col">
                                                        <div class="fw-bold">{{ $inputs['file_path'] }}</div>
                                                        <div class="text-secondary">
                                                            <i class="fa fa-clock icon-sm"></i>
                                                            {{ isset($inputs['duration']) && $inputs['duration'] > 0 ? gmdate('i:s', $inputs['duration']) : '00:00' }}
                                                            <span class="text-muted">({{ $inputs['duration'] ?? 0 }} saniye)</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button
                                                            wire:click="removeAudio"
                                                            wire:confirm="{{ __('muzibu::admin.song.remove_audio_confirm') }}"
                                                            class="btn btn-icon btn-ghost-danger"
                                                            type="button">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                {{-- Audio Player --}}
                                                <div class="mt-3">
                                                    <audio controls class="w-100">
                                                        <source src="{{ asset('storage/muzibu/songs/' . $inputs['file_path']) }}" type="audio/mpeg">
                                                    </audio>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Upload Input --}}
                                    <input
                                        type="file"
                                        wire:model="audioFile"
                                        class="form-control @error('audioFile') is-invalid @enderror"
                                        accept="audio/mp3,audio/wav,audio/flac,audio/m4a,audio/ogg,audio/mpeg">

                                    @error('audioFile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small class="form-hint">
                                        {{ __('muzibu::admin.song.supported_formats') }}: MP3, WAV, FLAC, M4A, OGG
                                        <span class="text-muted">•</span>
                                        {{ __('muzibu::admin.song.max_size') }}: 100MB
                                    </small>

                                    {{-- Upload Progress --}}
                                    <div wire:loading wire:target="audioFile" class="progress progress-sm mt-2">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Süre (Duration) --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" style="font-size: 0.875rem;">
                                        <i class="fa fa-clock me-1" style="font-size: 0.875rem;"></i>
                                        {{ __('muzibu::admin.song.duration') }}
                                    </label>
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            wire:model="inputs.duration"
                                            class="form-control @error('inputs.duration') is-invalid @enderror"
                                            placeholder="0"
                                            min="0">
                                        <span class="input-group-text">{{ __('muzibu::admin.song.seconds') }}</span>
                                        @error('inputs.duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-hint">
                                        {{ __('muzibu::admin.song.duration_manual_help') }}
                                    </small>
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
                                    'fieldName' => 'lyrics',
                                    'label' => __('muzibu::admin.song.lyrics'),
                                    'placeholder' => __('muzibu::admin.song.lyrics_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif ve Öne Çıkan - sadece bir kere -->
                        <div class="row mb-3 mt-4">
                            <div class="col-12 col-md-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1"
                                        {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('muzibu::admin.song.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('muzibu::admin.song.inactive') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_featured" name="is_featured" wire:model="inputs.is_featured"
                                        value="1"
                                        {{ isset($inputs['is_featured']) && $inputs['is_featured'] ? 'checked' : '' }} />

                                    <div class="state p-warning p-on ms-2">
                                        <label>{{ __('muzibu::admin.song.featured') }}</label>
                                    </div>
                                    <div class="state p-off ms-2">
                                        <label>{{ __('muzibu::admin.song.not_featured') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$songId" model-type="song"
                            model-class="Modules\Muzibu\App\Models\Song" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.song" :model-id="$songId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $songId ?? 'null' }};
            window.currentModuleName = 'song';
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

        @include('ai::admin.components.universal-ai-content-scripts')
    @endpush
</div>
