@php
    // Tenant-specific translations for JavaScript
    $playerLang = tenant_lang('player');
    $frontLang = tenant_lang('front');
@endphp

<!-- Hidden Audio Element for HLS streams -->
<audio id="hlsAudio" x-ref="hlsAudio" style="display: none;"></audio>

<!-- Auth Modal -->
<template x-teleport="body">
    <div x-show="showAuthModal !== null"
         x-cloak
         class="auth-modal-overlay">

        <!-- Modal Box -->
        <div x-show="showAuthModal !== null"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="auth-modal-box"
             :class="showAuthModal === 'register' ? 'auth-modal-wide' : ''">

            <!-- Close Button -->
            <button @click="showAuthModal = null" class="auth-modal-close">
                <i class="fas fa-times"></i>
            </button>

            <!-- Login Header -->
            <div x-show="showAuthModal === 'login'" class="auth-modal-header">
                <div class="auth-modal-logo"><i class="fas fa-music"></i></div>
                <h2 class="auth-modal-title">{{ tenant_trans('player.login') }}</h2>
                <p class="auth-modal-subtitle">{{ tenant_trans('player.login_subtitle') }}</p>
            </div>

            <!-- Register Header -->
            <div x-show="showAuthModal === 'register'" class="auth-modal-header">
                <div class="auth-modal-logo"><i class="fas fa-music"></i></div>
                <h2 class="auth-modal-title">{{ tenant_trans('player.register') }}</h2>
                <p class="auth-modal-subtitle">{{ tenant_trans('player.register_subtitle') }}</p>
            </div>

            <!-- Forgot Header -->
            <div x-show="showAuthModal === 'forgot'" class="auth-modal-header">
                <div class="auth-modal-logo"><i class="fas fa-music"></i></div>
                <h2 class="auth-modal-title">{{ tenant_trans('player.forgot_password') }}</h2>
                <p class="auth-modal-subtitle">{{ tenant_trans('player.forgot_password_subtitle') }}</p>
            </div>

            <!-- Login Form -->
            <form x-show="showAuthModal === 'login'" @submit.prevent="handleLogin()" class="auth-form" autocomplete="on">
                <div class="auth-field">
                    <label>{{ tenant_trans('player.email') }}</label>
                    <input type="email" x-model="loginForm.email" placeholder="{{ tenant_trans('player.email_placeholder') }}" required autocomplete="email" name="email">
                </div>
                <div class="auth-field">
                    <label>{{ tenant_trans('player.password') }}</label>
                    <div class="auth-password-wrap">
                        <input :type="showLoginPassword ? 'text' : 'password'" x-model="loginForm.password" placeholder="••••••••" required autocomplete="current-password" name="password">
                        <button type="button" @click="showLoginPassword = !showLoginPassword" class="auth-eye-btn">
                            <i :class="showLoginPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <div class="auth-options">
                    <label class="auth-toggle">
                        <input type="checkbox" x-model="loginForm.remember" class="sr-only">
                        <span class="auth-toggle-track" :class="loginForm.remember ? 'active' : ''">
                            <span class="auth-toggle-thumb" :class="loginForm.remember ? 'active' : ''"></span>
                        </span>
                        <span class="auth-toggle-label">{{ tenant_trans('player.remember_me') }}</span>
                    </label>
                    <button type="button" @click="showAuthModal = 'forgot'" class="auth-link">{{ tenant_trans('player.forgot_password_link') }}</button>
                </div>
                <button type="submit" class="auth-submit-btn">
                    <span x-show="!isLoading">{{ tenant_trans('player.login_button') }}</span>
                    <i x-show="isLoading" class="fas fa-spinner fa-spin"></i>
                </button>
                <p class="auth-switch">{{ tenant_trans('player.no_account') }} <button type="button" @click="showAuthModal = 'register'">{{ tenant_trans('player.start_free') }}</button></p>
            </form>

            <!-- Forgot Password Form -->
            <form x-show="showAuthModal === 'forgot'" @submit.prevent="handleForgotPassword()" class="auth-form">
                <div class="auth-field">
                    <label>{{ tenant_trans('player.email') }}</label>
                    <input type="email" x-model="forgotForm.email" placeholder="{{ tenant_trans('player.email_placeholder') }}" required>
                </div>
                <button type="submit" class="auth-submit-btn">
                    <span x-show="!isLoading">{{ tenant_trans('player.reset_password_button') }}</span>
                    <i x-show="isLoading" class="fas fa-spinner fa-spin"></i>
                </button>
                <p class="auth-switch"><button type="button" @click="showAuthModal = 'login'"><i class="fas fa-arrow-left"></i> {{ tenant_trans('player.back_to_login') }}</button></p>
            </form>

            <!-- Register Form -->
            <form x-show="showAuthModal === 'register'" @submit.prevent="handleRegister()" class="auth-form">
                <!-- Ad / Soyad - 2 Kolon -->
                <div class="auth-grid">
                    <div class="auth-field">
                        <label>{{ tenant_trans('player.first_name') }}</label>
                        <input type="text" x-model="registerForm.firstName" @input="validateName()" placeholder="{{ tenant_trans('player.first_name_placeholder') }}" required>
                        <span x-show="registerForm.firstName.length > 0 && registerForm.firstName.length < 2" class="auth-error" x-text="lang.min_chars.replace(':count', 2)"></span>
                    </div>
                    <div class="auth-field">
                        <label>{{ tenant_trans('player.last_name') }}</label>
                        <input type="text" x-model="registerForm.lastName" @input="validateName()" placeholder="{{ tenant_trans('player.last_name_placeholder') }}" required>
                        <span x-show="registerForm.lastName.length > 0 && registerForm.lastName.length < 2" class="auth-error" x-text="lang.min_chars.replace(':count', 2)"></span>
                    </div>
                </div>

                <!-- E-posta / Telefon - 2 Kolon -->
                <div class="auth-grid">
                    <!-- Sol: E-posta -->
                    <div class="auth-field">
                        <label>{{ tenant_trans('player.email') }}</label>
                        <input type="email" x-model="registerForm.email" @input.debounce.500ms="validateEmail()" placeholder="{{ tenant_trans('player.email_placeholder') }}" required>
                        <span x-show="registerForm.email.length > 0 && !registerValidation.email.valid" class="auth-error" x-text="registerValidation.email.message"></span>
                    </div>

                    <!-- Sağ: Telefon -->
                    <div class="auth-field" x-show="tenantId === 1001">
                        <label>{{ tenant_trans('player.phone') }} <span style="color: #f87171;">*</span></label>
                        <div class="auth-phone-wrap">
                            <div class="auth-country-select" x-data="{ open: false }">
                                <button type="button" @click="open = !open" class="auth-country-btn">
                                    <span x-text="phoneCountry.flag"></span>
                                    <span x-text="phoneCountry.code"></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" class="auth-country-dropdown">
                                    <template x-for="country in phoneCountries" :key="country.code">
                                        <button type="button" @click="selectCountry(country); open = false" class="auth-country-option">
                                            <span x-text="country.flag"></span>
                                            <span x-text="country.name"></span>
                                            <span style="color: #6b7280;" x-text="country.code"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <input type="tel" x-model="registerForm.phone" @input="formatPhoneNumber()" :placeholder="phoneCountry.placeholder" maxlength="20">
                        </div>
                        <span x-show="registerForm.phone.length > 0 && !registerValidation.phone.valid" class="auth-error" x-text="registerValidation.phone.message"></span>
                    </div>
                </div>

                <!-- Şifre - Full Width -->
                <div class="auth-field">
                    <label>{{ tenant_trans('player.password') }}</label>
                    <div class="auth-password-wrap">
                        <input :type="showPassword ? 'text' : 'password'" x-model="registerForm.password" @input="validatePassword()" placeholder="{{ tenant_trans('player.password_placeholder') }}" required>
                        <button type="button" @click="showPassword = !showPassword" class="auth-eye-btn">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <!-- Şifre Kriterleri -->
                    <div x-show="registerForm.password.length > 0" class="auth-password-criteria">
                        <div class="auth-criteria-item" :class="registerValidation.password.checks.length ? 'valid' : 'invalid'">
                            <i :class="registerValidation.password.checks.length ? 'fas fa-check' : 'fas fa-times'"></i>
                            <span>{{ tenant_trans('player.password_length') }}</span>
                        </div>
                        <div class="auth-criteria-item" :class="registerValidation.password.checks.uppercase ? 'valid' : 'invalid'">
                            <i :class="registerValidation.password.checks.uppercase ? 'fas fa-check' : 'fas fa-times'"></i>
                            <span>{{ tenant_trans('player.password_uppercase') }}</span>
                        </div>
                        <div class="auth-criteria-item" :class="registerValidation.password.checks.lowercase ? 'valid' : 'invalid'">
                            <i :class="registerValidation.password.checks.lowercase ? 'fas fa-check' : 'fas fa-times'"></i>
                            <span>{{ tenant_trans('player.password_lowercase') }}</span>
                        </div>
                        <div class="auth-criteria-item" :class="registerValidation.password.checks.number ? 'valid' : 'invalid'">
                            <i :class="registerValidation.password.checks.number ? 'fas fa-check' : 'fas fa-times'"></i>
                            <span>{{ tenant_trans('player.password_number') }}</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn" :disabled="!isRegisterFormValid()">
                    <span x-show="!isLoading">{{ tenant_trans('player.register_button') }}</span>
                    <i x-show="isLoading" class="fas fa-spinner fa-spin"></i>
                </button>
                <p class="auth-switch">{{ tenant_trans('player.have_account') }} <button type="button" @click="showAuthModal = 'login'">{{ tenant_trans('player.login_link') }}</button></p>
            </form>
        </div>
    </div>
</template>

<!-- Player Styles -->
<link rel="stylesheet" href="{{ asset('themes/muzibu/css/player/auth-modal.css') }}">


<!-- PLAYER BAR - Inline Styles (Spotify Inspired) with Blur Background -->
<template x-teleport="body">
<div id="muzibu-player-bar"
     style="position: fixed !important;
            bottom: 0 !important;
            left: 256px !important;
            right: 0 !important;
            background: #181818 !important;
            border-top: 1px solid rgba(255,255,255,0.1) !important;
            padding: 16px 24px !important;
            z-index: 99999 !important;
            display: block !important;
            visibility: visible !important;"
     :style="currentSong?.blur_background ?
        `background-image: url('${currentSong.blur_background}'); background-size: cover; background-position: center;` :
        ''">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <!-- Now Playing -->
        <div style="display: flex; align-items: center; gap: 12px; width: 25%;">
            <img :src="currentSong?.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=56&h=56&fit=crop'"
                 style="width: 56px; height: 56px; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 600; color: white; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 8px;">
                    <span x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></span>
                    <!-- Stream Type Icon -->
                    <span x-show="currentSong" style="font-size: 12px; opacity: 0.8;" :title="currentStreamType === 'hls' ? 'HLS Stream (Adaptive)' : 'MP3 Dosya'">
                        <i :class="currentStreamType === 'hls' ? 'fas fa-signal' : 'fas fa-file-audio'" :style="currentStreamType === 'hls' ? 'color: #3b82f6' : 'color: #10b981'"></i>
                    </span>
                </div>
                <div style="font-size: 12px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                     x-text="currentSong?.artist_title?.tr || ''"></div>
            </div>
            <button @click="toggleLike()"
                    title="Favorilere Ekle/Çıkar"
                    style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 8px; transition: color 0.2s;">
                <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'" :style="isLiked ? 'color: #1DB954' : ''"></i>
            </button>
        </div>

        <!-- Player Controls -->
        <div style="flex: 1; max-width: 720px; padding: 0 32px;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 8px;">
                <button @click="shuffle = !shuffle"
                        title="Karışık Çal"
                        style="background: none; border: none; cursor: pointer; padding: 4px; transition: all 0.2s;"
                        :style="shuffle ? 'color: #1DB954;' : 'color: #9ca3af;'">
                    <i class="fas fa-random" style="font-size: 14px;"></i>
                </button>
                <button @click="previousTrack()"
                        title="Önceki Şarkı"
                        style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; transition: color 0.2s;">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button @click="togglePlayPause()"
                        :title="isPlaying ? 'Duraklat' : 'Çal'"
                        style="width: 40px; height: 40px; background: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                    <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'" style="color: black; margin-left: 2px;"></i>
                </button>
                <button @click="nextTrack()"
                        title="Sonraki Şarkı"
                        style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px; transition: color 0.2s;">
                    <i class="fas fa-step-forward"></i>
                </button>
                <button @click="cycleRepeat()"
                        :title="repeatMode === 'off' ? '{{ tenant_trans('player.repeat_off') }}' : (repeatMode === 'one' ? '{{ tenant_trans('player.repeat_one') }}' : '{{ tenant_trans('player.repeat_all') }}')"
                        class="player-repeat-btn"
                        :class="{ 'active': repeatMode !== 'off' }">
                    <i class="fas fa-redo" style="font-size: 14px;"></i>
                    <span x-show="repeatMode === 'one'" class="repeat-one-badge">1</span>
                </button>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 12px; color: #9ca3af; min-width: 40px; text-align: right;" x-text="formatTime(currentTime)">0:00</span>
                <div @click="seekTo($event)" style="flex: 1; height: 8px; background: #374151; border-radius: 4px; cursor: pointer; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #1DB954, #1ed760); border-radius: 4px; transition: width 0.1s linear; box-shadow: 0 0 4px rgba(29, 185, 84, 0.5);" :style="'width: ' + progressPercent + '%'"></div>
                </div>
                <span style="font-size: 12px; color: #9ca3af; min-width: 40px;" x-text="formatTime(duration)">0:00</span>
            </div>
        </div>

        <!-- Volume -->
        <div style="display: flex; align-items: center; gap: 12px; width: 25%; justify-content: flex-end;">
            <button @click="showQueue = !showQueue"
                    title="Çalma Sırası"
                    style="background: none; border: none; cursor: pointer; padding: 4px; font-size: 14px; transition: all 0.2s;"
                    :style="showQueue ? 'color: #1DB954;' : 'color: #9ca3af;'">
                <i class="fas fa-list"></i>
            </button>
            <button @click="toggleMute()"
                    :title="isMuted ? 'Sesi Aç' : 'Sessiz'"
                    style="background: none; border: none; cursor: pointer; padding: 4px; font-size: 14px; transition: all 0.2s;"
                    :style="isMuted ? 'color: #ef4444;' : 'color: #9ca3af;'">
                <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : (volume > 0 ? 'fas fa-volume-down' : 'fas fa-volume-off'))"></i>
            </button>
            <div class="player-volume-bar" @click="setVolume($event)" title="Ses Seviyesi">
                <div class="player-volume-track">
                    <div class="player-volume-fill" :style="'width: ' + volume + '%'"></div>
                    <div class="player-volume-thumb" :style="'left: ' + volume + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<!-- Queue Panel (Sağ Tarafta) -->
<div x-show="showQueue"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform translate-x-full"
     x-transition:enter-end="transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="transform translate-x-0"
     x-transition:leave-end="transform translate-x-full"
     style="display: none;"
     class="queue-panel">

    <div class="queue-panel-header">
        <h3>{{ tenant_trans('player.queue_title') }}</h3>
        <button @click="showQueue = false" class="queue-close-btn">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="queue-scroll-area">
        <!-- Şu An Çalan -->
        <div x-show="currentSong" class="now-playing-section">
            <div class="now-playing-label">{{ tenant_trans('player.now_playing') }}</div>
            <div class="now-playing-card">
                <img :src="currentSong?.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=48&h=48&fit=crop'"
                     class="now-playing-cover">
                <div class="now-playing-info">
                    <div class="now-playing-title" style="display: flex; align-items: center; gap: 6px;">
                        <span x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></span>
                        <!-- Stream Type Icon -->
                        <span x-show="currentSong" style="font-size: 11px; opacity: 0.8;" :title="currentStreamType === 'hls' ? 'HLS Stream (Adaptive)' : 'MP3 Dosya'">
                            <i :class="currentStreamType === 'hls' ? 'fas fa-signal' : 'fas fa-file-audio'" :style="currentStreamType === 'hls' ? 'color: #3b82f6' : 'color: #10b981'"></i>
                        </span>
                    </div>
                    <div class="now-playing-artist" x-text="currentSong?.artist_title?.tr || ''"></div>
                </div>
                <div class="now-playing-indicator">
                    <i class="fas fa-volume-up"></i>
                </div>
            </div>
        </div>

        <!-- Sıradaki Şarkılar -->
        <div x-show="queue.length > 0">
            <div class="queue-header">
                <span>{{ tenant_trans('player.up_next') }}</span>
                <span x-text="queue.length + ' ' + lang.songs_count.replace(':count ', '')" class="queue-count"></span>
            </div>
            <div class="queue-list">
                <template x-for="(song, index) in queue" :key="song.song_id">
                    <div draggable="true"
                         @dragstart="dragStart(index, $event)"
                         @dragover.prevent="dragOver(index)"
                         @drop="drop(index)"
                         @dragend="dragEnd()"
                         @click="playSongFromQueue(index)"
                         class="queue-item"
                         :class="{
                             'queue-item-active': index === queueIndex,
                             'queue-item-dragging': draggedIndex === index,
                             'queue-item-drop-target': dropTargetIndex === index
                         }">
                        <!-- Drag Handle -->
                        <div class="queue-drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <!-- Index / Play Icon -->
                        <div class="queue-index">
                            <span x-show="index !== queueIndex" x-text="index + 1"></span>
                            <i x-show="index === queueIndex" class="fas fa-play queue-playing-icon"></i>
                        </div>
                        <!-- Album Cover -->
                        <img :src="song.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=40&h=40&fit=crop'"
                             class="queue-cover"
                             :class="{ 'queue-cover-active': index === queueIndex }">
                        <!-- Song Info -->
                        <div class="queue-info">
                            <div class="queue-title" :class="{ 'queue-title-active': index === queueIndex }"
                                 x-text="song.song_title?.tr || 'Untitled'"></div>
                            <div class="queue-artist" x-text="song.artist_title?.tr || 'Unknown'"></div>
                        </div>
                        <!-- Remove Button -->
                        <button @click.stop="removeFromQueue(index)" class="queue-remove-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Boş Queue Durumu -->
        <div x-show="queue.length === 0" style="text-align: center; padding: 48px 0;">
            <i class="fas fa-music" style="font-size: 40px; color: #4b5563; margin-bottom: 12px; display: block;"></i>
            <p style="color: #9ca3af; font-size: 14px; margin: 0;">{{ tenant_trans('player.queue_empty') }}</p>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px;">{{ tenant_trans('player.queue_empty_hint') }}</p>
        </div>
    </div>
</div>

<!-- Responsive: Player mobilde sidebar olmadan -->

<link rel="stylesheet" href="{{ asset('themes/muzibu/css/player/player-queue.css') }}">

<!-- Player Script -->
<script>
    // Blade variables for JavaScript
    window.muzibuPlayerConfig = {
        lang: @json($playerLang ?? []),
        frontLang: @json($frontLang ?? []),
        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        currentUser: @json(auth()->check() ? ['id' => auth()->user()->id, 'name' => auth()->user()->name, 'email' => auth()->user()->email] : null),
        tenantId: {{ tenant('id') ?? 2 }},
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('themes/muzibu/js/player/muzibu-player-v2.js') }}?v={{ time() }}"></script>
