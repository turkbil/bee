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

                                <!-- Album, Genre Seçimleri (Sadece ilk dilde göster) -->
                                @if($lang === get_tenant_default_locale())
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
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

                                    <div class="col-12 col-md-6">
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
                                    <label class="form-label required">
                                        {{ __('muzibu::admin.song.audio_file') }}
                                    </label>

                                    {{-- CSS for Hover Effect (Global scope) --}}
                                    <style>
                                        .song-card-with-hover:hover .song-delete-btn {
                                            opacity: 1 !important;
                                        }
                                    </style>

                                    {{-- Split Layout: FilePond Upload + Current Song --}}
                                    <div class="row g-3">
                                        {{-- FilePond Upload Area - Full width if no file, half width if file exists --}}
                                        <div class="{{ ($inputs['file_path'] ?? null) ? 'col-md-6' : 'col-md-12' }}">
                                            <input
                                                type="file"
                                                class="filepond-audio"
                                                wire:model="audioFile"
                                                accept="audio/mp3,audio/wav,audio/flac,audio/m4a,audio/ogg,audio/mpeg">
                                        </div>

                                        {{-- Current Song (appears only when file is uploaded) --}}
                                        @if($inputs['file_path'] ?? null)
                                            <div class="col-md-6" wire:key="audio-card-{{ $inputs['file_path'] }}">
                                                <div class="card position-relative song-card-with-hover" style="min-height: 200px;">
                                                    {{-- X Button (Gallery Style - Hover to Show) --}}
                                                    <button
                                                        wire:click="removeAudio"
                                                        class="btn btn-icon btn-sm position-absolute song-delete-btn"
                                                        type="button"
                                                        style="top: 8px; right: 8px; z-index: 10; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.95); border: 1px solid #dee2e6; opacity: 0; transition: opacity 0.2s;">
                                                        <i class="fa fa-times text-danger"></i>
                                                    </button>

                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="avatar bg-success-lt me-3">
                                                                <i class="fa fa-music"></i>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <div class="fw-bold text-truncate" style="max-width: 180px;">
                                                                    {{ $inputs['file_path'] }}
                                                                </div>
                                                                <small class="text-muted">
                                                                    {{ isset($inputs['duration']) && $inputs['duration'] > 0 ? gmdate('i:s', $inputs['duration']) : '00:00' }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        {{-- Audio Player --}}
                                                        <div>
                                                            <audio controls class="w-100" style="height: 35px;">
                                                                <source src="{{ asset('storage/muzibu/songs/' . $inputs['file_path']) }}?v={{ time() }}" type="audio/mpeg">
                                                            </audio>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @error('audioFile')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror

                                    {{-- Upload Progress --}}
                                    <div wire:loading wire:target="audioFile" class="progress progress-sm mt-2">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- SÜRE (Duration) - Manuel düzenlenebilir --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
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
                                </div>

                                {{-- Öne Çıkan --}}
                                <div class="mt-4">
                                    <div class="pretty p-default p-curve p-toggle p-smooth">
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

                        {{-- Aktif/Pasif --}}
                        <div class="row mb-3 mt-4">
                            <div class="col-12">
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
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$songId" model-type="song"
                            model-class="Modules\Muzibu\App\Models\Song" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.muzibu.song" :model-id="$songId" />

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

        {{-- 🎵 FILEPOND AUDIO UPLOAD --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const audioInput = document.querySelector('.filepond-audio');

                if (audioInput && typeof FilePond !== 'undefined') {
                    const pond = FilePond.create(audioInput, {
                        acceptedFileTypes: ['audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/flac', 'audio/m4a', 'audio/ogg'],
                        maxFileSize: '100MB',
                        stylePanelLayout: 'compact',
                        credits: false,
                        allowRevert: true,
                        instantUpload: false,
                        labelIdle: `
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px; padding: 1.5rem;">
                                <div style="margin-bottom: 1rem;">
                                    <i class="fa fa-music" style="font-size: 48px; color: var(--tblr-muted);"></i>
                                </div>
                                <h4 class="mb-1">Şarkı Dosyasını Sürükle ve Bırak</h4>
                                <p class="text-muted mb-2">veya <span class="filepond--label-action">tıklayarak dosya seç</span></p>
                                <small class="text-muted d-block">MP3, WAV, FLAC, M4A, OGG <span class="mx-1">•</span> Max 100MB</small>
                            </div>
                        `,
                        labelFileProcessing: 'Yükleniyor',
                        labelFileProcessingComplete: 'Yükleme tamamlandı',
                        labelFileProcessingAborted: 'Yükleme iptal edildi',
                        labelFileProcessingError: 'Yükleme hatası',
                        labelTapToCancel: 'iptal',
                        labelTapToRetry: 'tekrar dene',
                        labelTapToUndo: 'geri al',
                        labelButtonRemoveItem: 'Kaldır',
                        labelButtonAbortItemLoad: 'İptal',
                        labelButtonRetryItemLoad: 'Tekrar Dene',
                        labelButtonAbortItemProcessing: 'İptal',
                        labelButtonUndoItemProcessing: 'Geri Al',
                        labelButtonRetryItemProcessing: 'Tekrar Dene',
                        labelButtonProcessItem: 'Yükle',
                        server: {
                            process: (fieldName, file, metadata, load, error, progress, abort) => {
                                // Livewire dosya upload'ı otomatik olarak çalışacak
                                load(file.name);
                            },
                            revert: (uniqueFileId, load, error) => {
                                // Remove file - Livewire handle eder
                                load();
                            }
                        },
                        onprocessfile: (error, file) => {
                            if (!error) {
                                // Upload tamamlandı - Livewire event trigger
                                console.log('✅ FilePond file processed:', file.filename);
                                // Livewire'ı refresh et ki audio player görsün
                                window.livewire.emit('refreshComponent');
                            }
                        }
                    });

                    // Livewire refresh event listener
                    document.addEventListener('livewire:load', function() {
                        Livewire.on('refreshComponent', () => {
                            pond.removeFiles();
                        });
                    });

                    console.log('✅ FilePond audio uploader initialized');
                } else {
                    console.warn('⚠️ FilePond not found or audio input not present');
                }
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
