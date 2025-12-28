<div x-data="{ isMediaUploading: false }">
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
                {{-- ŞARKI COVER PREVIEW (Sadece Edit Modunda) --}}
                @if($songId && isset($song))
                    @php
                        $coverUrl = $song->getCoverUrl(200, 200);
                    @endphp
                    @if($coverUrl)
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="{{ $coverUrl }}" alt="Song Cover" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="col">
                                    <h5 class="mb-1">{{ __('muzibu::admin.song_cover') }}</h5>
                                    <p class="text-muted mb-0 small">
                                        @if($song->media_id)
                                            <i class="fas fa-check-circle text-success"></i> {{ __('muzibu::admin.own_cover') }}
                                        @else
                                            <i class="fas fa-info-circle text-info"></i> {{ __('muzibu::admin.album_cover') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

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

                                    {{-- Split Layout: Upload + Current Song --}}
                                    <div x-data="{
                                        isDragging: false,
                                        handleDrop(e) {
                                            this.isDragging = false;
                                            const files = e.dataTransfer.files;
                                            if (files.length > 0) {
                                                $refs.fileInput.files = files;
                                                $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                            }
                                        }
                                    }">
                                        {{-- Hidden File Input --}}
                                        <input
                                            type="file"
                                            x-ref="fileInput"
                                            wire:model="audioFile"
                                            @change="console.log('🎵 [INPUT] File selected, dispatching event'); window.dispatchEvent(new CustomEvent('media-upload-started'))"
                                            x-on:livewire-upload-finish="console.log('✅ [INPUT] Upload finished, dispatching event'); window.dispatchEvent(new CustomEvent('media-upload-completed'))"
                                            x-on:livewire-upload-error="console.log('❌ [INPUT] Upload error, dispatching event'); window.dispatchEvent(new CustomEvent('media-upload-completed'))"
                                            class="d-none"
                                            accept="audio/mp3,audio/wav,audio/flac,audio/m4a,audio/ogg,audio/mpeg">

                                        <div class="row g-3">
                                            {{-- Upload Area - Sadece şarkı yoksa göster --}}
                                            @if(!($inputs['file_path'] ?? null))
                                                <div class="col-md-12">
                                                    <div
                                                        @click="$refs.fileInput.click()"
                                                        @dragover.prevent="isDragging = true"
                                                        @dragleave.prevent="isDragging = false"
                                                        @drop.prevent="handleDrop($event)"
                                                        :class="{ 'border-primary bg-primary-lt': isDragging }"
                                                        class="border border-2 border-dashed rounded p-4 text-center cursor-pointer"
                                                        style="cursor: pointer; transition: all 0.2s; min-height: 200px;">

                                                        <div class="mb-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                                            </svg>
                                                        </div>

                                                        <h4 class="mb-1">{{ __('muzibu::admin.song.drag_drop_audio') }}</h4>
                                                        <p class="mb-2">{{ __('muzibu::admin.song.or_click_browse') }}</p>

                                                        <small class="d-block">
                                                            {{ __('muzibu::admin.song.supported_formats') }}: MP3, WAV, FLAC, M4A, OGG
                                                            <span class="mx-1">•</span>
                                                            {{ __('muzibu::admin.song.max_size') }}: 100MB
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Current Song (appears only when file is uploaded) - Tam genişlik --}}
                                            @if($inputs['file_path'] ?? null)
                                                <div class="col-md-12" wire:key="audio-card-{{ $inputs['file_path'] }}">
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
                                                                    <small class="">
                                                                        {{ isset($inputs['duration']) && $inputs['duration'] > 0 ? gmdate('i:s', $inputs['duration']) : '00:00' }}
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            {{-- Audio Player --}}
                                                            <div>
                                                                <audio controls class="w-100" style="height: 35px;">
                                                                    <source src="{{ tenant_storage_url('muzibu/songs/' . $inputs['file_path']) }}?v={{ time() }}" type="audio/mpeg">
                                                                </audio>
                                                            </div>

                                                            {{-- HLS Status Badge --}}
                                                            @if(isset($inputs['hls_path']) && $inputs['hls_path'])
                                                                <div class="mt-2">
                                                                    <span class="badge bg-green-lt">
                                                                        <i class="fa fa-shield-alt me-1"></i>
                                                                        HLS Hazır
                                                                        @if(isset($inputs['is_encrypted']) && $inputs['is_encrypted'])
                                                                            - Şifreli
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @endif
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

                        {{-- RENK PALETİ - Minimal --}}
                        <div class="row mb-4" x-data="colorPaletteManager()">
                            <div class="col-12">
                                <label class="form-label text-muted small mb-2">Renk Paleti</label>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Gradient Önizleme --}}
                                    <div class="rounded-pill shadow-sm" id="colorPreview"
                                        :style="'width: 120px; height: 32px; background: linear-gradient(90deg, ' + color1 + ', ' + color2 + ', ' + color3 + ');'">
                                    </div>

                                    {{-- Renk Seçiciler --}}
                                    <div class="d-flex gap-2">
                                        <input type="color" x-model="color1" @change="updateColorHash()"
                                            class="form-control form-control-color p-0 border-0" style="width: 28px; height: 28px; cursor: pointer;">
                                        <input type="color" x-model="color2" @change="updateColorHash()"
                                            class="form-control form-control-color p-0 border-0" style="width: 28px; height: 28px; cursor: pointer;">
                                        <input type="color" x-model="color3" @change="updateColorHash()"
                                            class="form-control form-control-color p-0 border-0" style="width: 28px; height: 28px; cursor: pointer;">
                                    </div>

                                    {{-- Otomatik Oluştur --}}
                                    <button type="button" @click="generateFromTitle()" class="btn btn-ghost-secondary btn-sm px-2" title="Başlıktan otomatik oluştur">
                                        <i class="fas fa-redo"></i>
                                    </button>

                                    {{-- Hidden input for Livewire --}}
                                    <input type="hidden" x-model="colorHash" wire:model.defer="inputs.color_hash">
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
        {{-- HLS.js CDN --}}
        @if(isset($inputs['hls_path']) && $inputs['hls_path'])
            <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        @endif

        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $songId ?? 'null' }};
            window.currentModuleName = 'song';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔥 TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🎵 [SONG] DOMContentLoaded - registering Livewire event listeners');

                // 🖼️ MEDIA UPLOAD STATE - Dosya yüklenirken butonları kilitle
                Livewire.on('media-upload-started', () => {
                    console.log('📸 [SONG] Livewire event received: media-upload-started');
                    console.log('📸 [SONG] Dispatching window event: media-upload-started');
                    window.dispatchEvent(new CustomEvent('media-upload-started'));
                });

                Livewire.on('media-upload-completed', () => {
                    console.log('✅ [SONG] Livewire event received: media-upload-completed');
                    console.log('✅ [SONG] Dispatching window event: media-upload-completed');
                    window.dispatchEvent(new CustomEvent('media-upload-completed'));
                });

                @if(isset($inputs['hls_path']) && $inputs['hls_path'])
                    // 🎵 HLS Preview Player
                    const hlsPreview = document.getElementById('hlsPreview');
                    if (hlsPreview && typeof Hls !== 'undefined') {
                        const hlsUrl = '{{ url("/stream/play/" . basename(dirname($inputs["hls_path"])) . "/playlist.m3u8") }}';

                        if (Hls.isSupported()) {
                            const hls = new Hls({
                                enableWorker: true,
                                lowLatencyMode: false,
                            });
                            hls.loadSource(hlsUrl);
                            hls.attachMedia(hlsPreview);
                            console.log('✅ HLS Preview ready (cache\'li)');
                        } else if (hlsPreview.canPlayType('application/vnd.apple.mpegurl')) {
                            hlsPreview.src = hlsUrl;
                            console.log('✅ Native HLS support (Safari)');
                        }
                    }
                @endif

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

        {{-- 🎨 COLOR PALETTE MANAGER --}}
        <script>
            function colorPaletteManager() {
                return {
                    color1: '{{ $colorPicker1 ?? "#3498db" }}',
                    color2: '{{ $colorPicker2 ?? "#9b59b6" }}',
                    color3: '{{ $colorPicker3 ?? "#e74c3c" }}',
                    colorHash: '{{ $inputs["color_hash"] ?? "" }}',

                    init() {
                        // Mevcut color_hash varsa renkleri yükle
                        if (this.colorHash) {
                            this.loadColorsFromHash(this.colorHash);
                        }
                    },

                    // MD5 hash (basit implementasyon)
                    md5(string) {
                        function rotateLeft(lValue, iShiftBits) {
                            return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
                        }
                        function addUnsigned(lX, lY) {
                            var lX8 = (lX & 0x80000000);
                            var lY8 = (lY & 0x80000000);
                            var lX4 = (lX & 0x40000000);
                            var lY4 = (lY & 0x40000000);
                            var lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
                            if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
                            if (lX4 | lY4) {
                                if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                                else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                            } else return (lResult ^ lX8 ^ lY8);
                        }
                        function F(x, y, z) { return (x & y) | ((~x) & z); }
                        function G(x, y, z) { return (x & z) | (y & (~z)); }
                        function H(x, y, z) { return (x ^ y ^ z); }
                        function I(x, y, z) { return (y ^ (x | (~z))); }
                        function FF(a, b, c, d, x, s, ac) {
                            a = addUnsigned(a, addUnsigned(addUnsigned(F(b, c, d), x), ac));
                            return addUnsigned(rotateLeft(a, s), b);
                        }
                        function GG(a, b, c, d, x, s, ac) {
                            a = addUnsigned(a, addUnsigned(addUnsigned(G(b, c, d), x), ac));
                            return addUnsigned(rotateLeft(a, s), b);
                        }
                        function HH(a, b, c, d, x, s, ac) {
                            a = addUnsigned(a, addUnsigned(addUnsigned(H(b, c, d), x), ac));
                            return addUnsigned(rotateLeft(a, s), b);
                        }
                        function II(a, b, c, d, x, s, ac) {
                            a = addUnsigned(a, addUnsigned(addUnsigned(I(b, c, d), x), ac));
                            return addUnsigned(rotateLeft(a, s), b);
                        }
                        function convertToWordArray(string) {
                            var lWordCount;
                            var lMessageLength = string.length;
                            var lNumberOfWords_temp1 = lMessageLength + 8;
                            var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
                            var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
                            var lWordArray = Array(lNumberOfWords - 1);
                            var lBytePosition = 0;
                            var lByteCount = 0;
                            while (lByteCount < lMessageLength) {
                                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                                lBytePosition = (lByteCount % 4) * 8;
                                lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
                                lByteCount++;
                            }
                            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                            lBytePosition = (lByteCount % 4) * 8;
                            lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
                            lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
                            lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
                            return lWordArray;
                        }
                        function wordToHex(lValue) {
                            var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
                            for (lCount = 0; lCount <= 3; lCount++) {
                                lByte = (lValue >>> (lCount * 8)) & 255;
                                WordToHexValue_temp = "0" + lByte.toString(16);
                                WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
                            }
                            return WordToHexValue;
                        }
                        var x = convertToWordArray(string);
                        var a = 0x67452301, b = 0xEFCDAB89, c = 0x98BADCFE, d = 0x10325476;
                        var S11=7, S12=12, S13=17, S14=22, S21=5, S22=9, S23=14, S24=20;
                        var S31=4, S32=11, S33=16, S34=23, S41=6, S42=10, S43=15, S44=21;
                        for (var k = 0; k < x.length; k += 16) {
                            var AA = a, BB = b, CC = c, DD = d;
                            a = FF(a,b,c,d,x[k+0],S11,0xD76AA478); d = FF(d,a,b,c,x[k+1],S12,0xE8C7B756);
                            c = FF(c,d,a,b,x[k+2],S13,0x242070DB); b = FF(b,c,d,a,x[k+3],S14,0xC1BDCEEE);
                            a = FF(a,b,c,d,x[k+4],S11,0xF57C0FAF); d = FF(d,a,b,c,x[k+5],S12,0x4787C62A);
                            c = FF(c,d,a,b,x[k+6],S13,0xA8304613); b = FF(b,c,d,a,x[k+7],S14,0xFD469501);
                            a = FF(a,b,c,d,x[k+8],S11,0x698098D8); d = FF(d,a,b,c,x[k+9],S12,0x8B44F7AF);
                            c = FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1); b = FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
                            a = FF(a,b,c,d,x[k+12],S11,0x6B901122); d = FF(d,a,b,c,x[k+13],S12,0xFD987193);
                            c = FF(c,d,a,b,x[k+14],S13,0xA679438E); b = FF(b,c,d,a,x[k+15],S14,0x49B40821);
                            a = GG(a,b,c,d,x[k+1],S21,0xF61E2562); d = GG(d,a,b,c,x[k+6],S22,0xC040B340);
                            c = GG(c,d,a,b,x[k+11],S23,0x265E5A51); b = GG(b,c,d,a,x[k+0],S24,0xE9B6C7AA);
                            a = GG(a,b,c,d,x[k+5],S21,0xD62F105D); d = GG(d,a,b,c,x[k+10],S22,0x2441453);
                            c = GG(c,d,a,b,x[k+15],S23,0xD8A1E681); b = GG(b,c,d,a,x[k+4],S24,0xE7D3FBC8);
                            a = GG(a,b,c,d,x[k+9],S21,0x21E1CDE6); d = GG(d,a,b,c,x[k+14],S22,0xC33707D6);
                            c = GG(c,d,a,b,x[k+3],S23,0xF4D50D87); b = GG(b,c,d,a,x[k+8],S24,0x455A14ED);
                            a = GG(a,b,c,d,x[k+13],S21,0xA9E3E905); d = GG(d,a,b,c,x[k+2],S22,0xFCEFA3F8);
                            c = GG(c,d,a,b,x[k+7],S23,0x676F02D9); b = GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
                            a = HH(a,b,c,d,x[k+5],S31,0xFFFA3942); d = HH(d,a,b,c,x[k+8],S32,0x8771F681);
                            c = HH(c,d,a,b,x[k+11],S33,0x6D9D6122); b = HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
                            a = HH(a,b,c,d,x[k+1],S31,0xA4BEEA44); d = HH(d,a,b,c,x[k+4],S32,0x4BDECFA9);
                            c = HH(c,d,a,b,x[k+7],S33,0xF6BB4B60); b = HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
                            a = HH(a,b,c,d,x[k+13],S31,0x289B7EC6); d = HH(d,a,b,c,x[k+0],S32,0xEAA127FA);
                            c = HH(c,d,a,b,x[k+3],S33,0xD4EF3085); b = HH(b,c,d,a,x[k+6],S34,0x4881D05);
                            a = HH(a,b,c,d,x[k+9],S31,0xD9D4D039); d = HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
                            c = HH(c,d,a,b,x[k+15],S33,0x1FA27CF8); b = HH(b,c,d,a,x[k+2],S34,0xC4AC5665);
                            a = II(a,b,c,d,x[k+0],S41,0xF4292244); d = II(d,a,b,c,x[k+7],S42,0x432AFF97);
                            c = II(c,d,a,b,x[k+14],S43,0xAB9423A7); b = II(b,c,d,a,x[k+5],S44,0xFC93A039);
                            a = II(a,b,c,d,x[k+12],S41,0x655B59C3); d = II(d,a,b,c,x[k+3],S42,0x8F0CCC92);
                            c = II(c,d,a,b,x[k+10],S43,0xFFEFF47D); b = II(b,c,d,a,x[k+1],S44,0x85845DD1);
                            a = II(a,b,c,d,x[k+8],S41,0x6FA87E4F); d = II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
                            c = II(c,d,a,b,x[k+6],S43,0xA3014314); b = II(b,c,d,a,x[k+13],S44,0x4E0811A1);
                            a = II(a,b,c,d,x[k+4],S41,0xF7537E82); d = II(d,a,b,c,x[k+11],S42,0xBD3AF235);
                            c = II(c,d,a,b,x[k+2],S43,0x2AD7D2BB); b = II(b,c,d,a,x[k+9],S44,0xEB86D391);
                            a = addUnsigned(a, AA); b = addUnsigned(b, BB); c = addUnsigned(c, CC); d = addUnsigned(d, DD);
                        }
                        return (wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d)).toLowerCase();
                    },

                    // Title'dan renk üret (PHP algoritmasıyla aynı)
                    generateFromTitle() {
                        // Title input'unu bul
                        const titleInput = document.querySelector('input[wire\\:model="multiLangInputs.tr.title"]') ||
                                          document.querySelector('input[wire\\:model="multiLangInputs.en.title"]');
                        const title = titleInput ? titleInput.value.toLowerCase().trim() : 'untitled';

                        if (!title) {
                            alert('Lütfen önce şarkı başlığını girin.');
                            return;
                        }

                        const md5Hash = this.md5(title);

                        // 3 bağımsız hue, minimum 60° fark
                        const hues = [];
                        const minDistance = 60;

                        for (let i = 0; i < 3; i++) {
                            let attempts = 0;
                            let h;
                            do {
                                const offset = (i * 4 + attempts) % 28;
                                h = parseInt(md5Hash.substr(offset, 4), 16) % 360;
                                let tooClose = false;

                                for (const existingHue of hues) {
                                    let diff = Math.abs(h - existingHue);
                                    diff = Math.min(diff, 360 - diff);
                                    if (diff < minDistance) {
                                        tooClose = true;
                                        break;
                                    }
                                }
                                attempts++;
                                if (!tooClose) break;
                            } while (attempts < 10);

                            hues.push(h);
                        }

                        // HSL değerleri
                        const colors = [];
                        for (let i = 0; i < 3; i++) {
                            const h = hues[i];
                            const s = 60 + (parseInt(md5Hash.substr(12 + i, 1), 16) % 36);
                            const l = 40 + (parseInt(md5Hash.substr(16 + i, 1), 16) % 26);
                            colors.push({ h, s, l });
                        }

                        // HEX'e çevir
                        this.color1 = this.hslToHex(colors[0].h, colors[0].s, colors[0].l);
                        this.color2 = this.hslToHex(colors[1].h, colors[1].s, colors[1].l);
                        this.color3 = this.hslToHex(colors[2].h, colors[2].s, colors[2].l);

                        // Color hash güncelle
                        this.colorHash = `${colors[0].h},${colors[0].s},${colors[0].l},${colors[1].h},${colors[1].s},${colors[1].l},${colors[2].h},${colors[2].s},${colors[2].l}`;

                        // Livewire'a bildir
                        this.$wire.set('inputs.color_hash', this.colorHash);
                    },

                    // Color picker'lardan hash güncelle
                    updateColorHash() {
                        const hsl1 = this.hexToHsl(this.color1);
                        const hsl2 = this.hexToHsl(this.color2);
                        const hsl3 = this.hexToHsl(this.color3);

                        this.colorHash = `${hsl1.h},${hsl1.s},${hsl1.l},${hsl2.h},${hsl2.s},${hsl2.l},${hsl3.h},${hsl3.s},${hsl3.l}`;
                        this.$wire.set('inputs.color_hash', this.colorHash);
                    },

                    // Hash'ten renkleri yükle
                    loadColorsFromHash(hash) {
                        const parts = hash.split(',').map(Number);
                        if (parts.length === 9) {
                            this.color1 = this.hslToHex(parts[0], parts[1], parts[2]);
                            this.color2 = this.hslToHex(parts[3], parts[4], parts[5]);
                            this.color3 = this.hslToHex(parts[6], parts[7], parts[8]);
                        }
                    },

                    // HSL to HEX
                    hslToHex(h, s, l) {
                        s /= 100;
                        l /= 100;
                        const c = (1 - Math.abs(2 * l - 1)) * s;
                        const x = c * (1 - Math.abs((h / 60) % 2 - 1));
                        const m = l - c / 2;
                        let r = 0, g = 0, b = 0;

                        if (h < 60) { r = c; g = x; b = 0; }
                        else if (h < 120) { r = x; g = c; b = 0; }
                        else if (h < 180) { r = 0; g = c; b = x; }
                        else if (h < 240) { r = 0; g = x; b = c; }
                        else if (h < 300) { r = x; g = 0; b = c; }
                        else { r = c; g = 0; b = x; }

                        r = Math.round((r + m) * 255).toString(16).padStart(2, '0');
                        g = Math.round((g + m) * 255).toString(16).padStart(2, '0');
                        b = Math.round((b + m) * 255).toString(16).padStart(2, '0');

                        return `#${r}${g}${b}`;
                    },

                    // HEX to HSL
                    hexToHsl(hex) {
                        hex = hex.replace('#', '');
                        const r = parseInt(hex.substr(0, 2), 16) / 255;
                        const g = parseInt(hex.substr(2, 2), 16) / 255;
                        const b = parseInt(hex.substr(4, 2), 16) / 255;

                        const max = Math.max(r, g, b);
                        const min = Math.min(r, g, b);
                        let h, s, l = (max + min) / 2;

                        if (max === min) {
                            h = s = 0;
                        } else {
                            const d = max - min;
                            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                            switch (max) {
                                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                                case g: h = ((b - r) / d + 2) / 6; break;
                                case b: h = ((r - g) / d + 4) / 6; break;
                            }
                        }

                        return {
                            h: Math.round(h * 360),
                            s: Math.round(s * 100),
                            l: Math.round(l * 100)
                        };
                    }
                };
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
