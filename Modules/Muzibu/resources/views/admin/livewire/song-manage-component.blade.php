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

                        {{-- ŞARKI DOSYASI YÜKLEME - Modern Card Tasarım --}}
                        <div class="mb-4">
                            <div class="row g-3">
                                {{-- Sol: Audio Upload --}}
                                <div class="col-12 col-lg-8">
                                    <div class="card shadow-sm border-0 bg-light">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="card-title mb-0">
                                                <i class="ti ti-music me-2"></i>
                                                {{ __('muzibu::admin.song.audio_file') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            @if($inputs['file_path'] ?? null)
                                                {{-- Mevcut Şarkı Preview --}}
                                                <div class="alert alert-success mb-3 position-relative">
                                                    <div class="d-flex align-items-start">
                                                        <div class="me-3">
                                                            <div class="avatar avatar-lg bg-success-lt">
                                                                <i class="ti ti-file-music fs-1"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <h4 class="mb-0">
                                                                    <i class="ti ti-circle-check-filled text-success me-1"></i>
                                                                    {{ __('muzibu::admin.song.current_file') }}
                                                                </h4>
                                                                {{-- Kaldır Butonu --}}
                                                                <button
                                                                    wire:click="removeAudio"
                                                                    wire:confirm="{{ __('muzibu::admin.song.remove_audio_confirm') }}"
                                                                    class="btn btn-sm btn-ghost-danger"
                                                                    type="button">
                                                                    <i class="ti ti-x"></i>
                                                                    {{ __('muzibu::admin.song.remove') }}
                                                                </button>
                                                            </div>
                                                            <p class="text-muted mb-2">
                                                                <i class="ti ti-file me-1"></i>
                                                                <strong>{{ $inputs['file_path'] }}</strong>
                                                            </p>
                                                            @if($inputs['duration'] ?? 0)
                                                                <div class="mb-3">
                                                                    <span class="badge bg-blue-lt">
                                                                        <i class="ti ti-clock me-1"></i>
                                                                        {{ gmdate('i:s', $inputs['duration']) }}
                                                                        <span class="text-muted">({{ $inputs['duration'] }} saniye)</span>
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            {{-- Audio Player --}}
                                                            <audio controls class="w-100" style="max-width: 100%; height: 40px;">
                                                                <source src="{{ asset('storage/muzibu/songs/' . $inputs['file_path']) }}" type="audio/mpeg">
                                                                Your browser does not support the audio element.
                                                            </audio>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Upload Input --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="ti ti-upload me-1"></i>
                                                    {{ $inputs['file_path'] ?? null ? __('muzibu::admin.song.change_audio') : __('muzibu::admin.song.upload_audio') }}
                                                </label>
                                                <input
                                                    type="file"
                                                    wire:model="audioFile"
                                                    class="form-control form-control-lg @error('audioFile') is-invalid @enderror"
                                                    accept="audio/mp3,audio/wav,audio/flac,audio/m4a,audio/ogg,audio/mpeg"
                                                >
                                                @error('audioFile')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Upload Progress --}}
                                            <div wire:loading wire:target="audioFile">
                                                <div class="alert alert-info d-flex align-items-center">
                                                    <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                                                    <div>
                                                        <strong>{{ __('muzibu::admin.song.uploading_audio') }}</strong>
                                                        <p class="mb-0 small">{{ __('muzibu::admin.song.please_wait') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Format Info --}}
                                            <div class="text-muted small">
                                                <i class="ti ti-info-circle me-1"></i>
                                                <strong>{{ __('muzibu::admin.song.supported_formats') }}:</strong>
                                                MP3, WAV, FLAC, M4A, OGG
                                                <span class="mx-2">•</span>
                                                <strong>{{ __('muzibu::admin.song.max_size') }}:</strong> 100MB
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sağ: Duration Display --}}
                                <div class="col-12 col-lg-4">
                                    <div class="card shadow-sm border-0 bg-gradient-primary text-white h-100">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                            <div class="mb-3">
                                                <i class="ti ti-clock fs-1 opacity-75"></i>
                                            </div>
                                            <h4 class="mb-2">
                                                {{ __('muzibu::admin.song.duration') }}
                                            </h4>
                                            <div class="display-4 fw-bold mb-2">
                                                {{ isset($inputs['duration']) && $inputs['duration'] > 0 ? gmdate('i:s', $inputs['duration']) : '00:00' }}
                                            </div>
                                            <small class="opacity-75 mb-3">
                                                {{ isset($inputs['duration']) && $inputs['duration'] > 0 ? $inputs['duration'] . ' ' . __('muzibu::admin.song.seconds') : __('muzibu::admin.song.no_duration') }}
                                            </small>
                                            <div class="badge bg-white text-primary">
                                                <i class="ti ti-robot me-1"></i>
                                                {{ __('muzibu::admin.song.auto_calculated') }}
                                            </div>
                                        </div>
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
