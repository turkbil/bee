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

<style>
/* Auth Modal Styles */
.auth-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
}
.auth-modal-box {
    position: relative;
    width: 100%;
    max-width: 400px;
    max-height: 90vh;
    overflow-y: auto;
    background: #181818;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.1);
    padding: 32px;
}
.auth-modal-wide { max-width: 680px; }
.auth-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 700px) { .auth-grid { grid-template-columns: 1fr; } .auth-modal-wide { max-width: 400px; } }
.auth-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 50%;
    color: #9ca3af;
    cursor: pointer;
    transition: all 0.2s;
}
.auth-modal-close:hover { background: rgba(255,255,255,0.2); color: white; }
.auth-modal-header { text-align: center; margin-bottom: 24px; }
.auth-modal-logo {
    width: 56px;
    height: 56px;
    margin: 0 auto 16px;
    background: linear-gradient(135deg, #1DB954, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}
.auth-modal-title { font-size: 24px; font-weight: 700; color: white; margin: 0 0 8px; }
.auth-modal-subtitle { font-size: 14px; color: #9ca3af; margin: 0; }
.auth-form { display: flex; flex-direction: column; gap: 16px; }
.auth-field { display: flex; flex-direction: column; gap: 6px; }
.auth-field label { font-size: 13px; font-weight: 500; color: #d1d5db; }
.auth-field input {
    width: 100%;
    padding: 12px 14px;
    background: #282828;
    border: 1px solid #3f3f46;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}
.auth-field input:focus { border-color: #1DB954; }
.auth-field input::placeholder { color: #6b7280; }
.auth-error { font-size: 12px; color: #f87171; }
.auth-password-wrap { position: relative; }
.auth-password-wrap input { padding-right: 44px; }
.auth-eye-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-eye-btn:hover { color: #9ca3af; transform: translateY(-50%); }
.auth-eye-btn i { font-size: 14px; }
.auth-options { display: flex; align-items: center; justify-content: space-between; }
.auth-toggle { display: flex; align-items: center; gap: 10px; cursor: pointer; }
.auth-toggle-track {
    position: relative;
    width: 40px;
    height: 22px;
    background: #3f3f46;
    border-radius: 11px;
    transition: background 0.2s;
}
.auth-toggle-track.active { background: #1DB954; }
.auth-toggle-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
}
.auth-toggle-thumb.active { transform: translateX(18px); }
.auth-toggle-label { font-size: 13px; color: #d1d5db; }
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }
.auth-link { background: none; border: none; color: #9ca3af; font-size: 13px; cursor: pointer; }
.auth-link:hover { color: #1DB954; }
.auth-submit-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #1DB954, #16a34a);
    border: none;
    border-radius: 9999px;
    color: white;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.auth-submit-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(29,185,84,0.4); }
.auth-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.auth-switch { text-align: center; font-size: 13px; color: #9ca3af; margin: 0; }
.auth-switch button { background: none; border: none; color: #1DB954; font-weight: 600; cursor: pointer; }
.auth-switch button:hover { text-decoration: underline; }
.auth-phone-wrap { display: flex; gap: 8px; }
.auth-phone-wrap input { flex: 1; }
.auth-country-select { position: relative; }
.auth-country-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px;
    background: #282828;
    border: 1px solid #3f3f46;
    border-radius: 8px;
    color: white;
    font-size: 13px;
    cursor: pointer;
}
.auth-country-btn i { font-size: 10px; color: #6b7280; }
.auth-country-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 4px;
    width: 220px;
    max-height: 200px;
    overflow-y: auto;
    background: #282828;
    border: 1px solid #3f3f46;
    border-radius: 8px;
    z-index: 50;
}
.auth-country-option {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 10px 12px;
    background: none;
    border: none;
    color: white;
    font-size: 13px;
    text-align: left;
    cursor: pointer;
}
.auth-country-option:hover { background: rgba(255,255,255,0.1); }
.auth-strength { margin-top: 8px; }
.auth-strength-bars { display: flex; gap: 4px; margin-bottom: 4px; }
.auth-strength-bars div { height: 3px; flex: 1; border-radius: 2px; transition: background 0.2s; }
.auth-strength-text { font-size: 11px; color: #9ca3af; }
.auth-password-criteria { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 16px; margin-top: 10px; }
.auth-criteria-item { display: flex; align-items: center; gap: 6px; font-size: 12px; }
.auth-criteria-item.valid { color: #10b981; }
.auth-criteria-item.invalid { color: #6b7280; }
.auth-criteria-item i { font-size: 10px; width: 12px; }
[x-cloak] { display: none !important; }
</style>

<!-- PLAYER BAR - Inline Styles (Spotify Inspired) -->
<div style="position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: #181818 !important;
            border-top: 1px solid rgba(255,255,255,0.1) !important;
            padding: 16px 24px !important;
            z-index: 99999 !important;">
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
<style>
    @media (max-width: 768px) {
        /* Player mobilde full width */
        div[style*="position: fixed"][style*="bottom: 0"] {
            left: 0 !important;
            padding: 12px 16px !important;
        }

        /* Queue mobilde full width */
        div[style*="width: 384px"] {
            width: 100% !important;
        }
    }

    /* Desktop: Sidebar genişliği kadar sol boşluk */
    @media (min-width: 769px) {
        div[style*="position: fixed"][style*="bottom: 0"] {
            left: 256px !important;
        }
    }

    /* Pulse animation for playing indicator */
    @keyframes pulse-slow {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }

    /* Play button pulse animation */
    @keyframes pulse-play {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.9); }
    }

    /* ============================================
       PLAYER PROGRESS & VOLUME BARS
       ============================================ */

    /* Progress Bar */
    .player-progress-bar {
        flex: 1;
        height: 20px;
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 6px 0;
    }

    .player-progress-track {
        position: relative;
        width: 100%;
        height: 6px;
        background: #4b5563;
        border-radius: 3px;
        overflow: visible;
    }

    .player-progress-bar:hover .player-progress-track {
        height: 8px;
    }

    .player-progress-fill {
        height: 100%;
        background: #1DB954;
        border-radius: 3px;
        transition: width 0.05s linear;
    }

    .player-progress-bar:hover .player-progress-fill {
        background: #22c55e;
    }

    .player-progress-thumb {
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 14px;
        height: 14px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.15s ease;
        pointer-events: none;
    }

    .player-progress-bar:hover .player-progress-thumb {
        opacity: 1;
    }

    /* Volume Bar */
    .player-volume-bar {
        width: 100px;
        height: 20px;
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 6px 0;
    }

    .player-volume-track {
        position: relative;
        width: 100%;
        height: 6px;
        background: #4b5563;
        border-radius: 3px;
        overflow: visible;
    }

    .player-volume-bar:hover .player-volume-track {
        height: 8px;
    }

    .player-volume-fill {
        height: 100%;
        background: #1DB954;
        border-radius: 3px;
        transition: width 0.05s linear;
    }

    .player-volume-bar:hover .player-volume-fill {
        background: #22c55e;
    }

    .player-volume-thumb {
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 14px;
        height: 14px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.15s ease;
        pointer-events: none;
    }

    .player-volume-bar:hover .player-volume-thumb {
        opacity: 1;
    }

    /* Repeat Button */
    .player-repeat-btn {
        position: relative;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #9ca3af;
        transition: color 0.2s ease;
    }

    .player-repeat-btn.active {
        color: #1DB954;
    }

    .player-repeat-btn:hover {
        color: white;
    }

    .player-repeat-btn.active:hover {
        color: #22c55e;
    }

    .repeat-one-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: #1DB954;
        color: white;
        font-size: 8px;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    /* ============================================
       QUEUE PANEL STYLES - Clean Professional Design
       ============================================ */

    /* Queue Panel Container */
    .queue-panel {
        position: fixed !important;
        right: 0 !important;
        bottom: 88px !important;
        top: 0 !important;
        width: 380px !important;
        background: #181818 !important;
        border-left: 1px solid rgba(255,255,255,0.1) !important;
        z-index: 30 !important;
        flex-direction: column !important;
    }

    /* Alpine.js x-show ile birlikte çalışması için */
    .queue-panel:not([style*="display: none"]) {
        display: flex !important;
    }

    /* Queue Panel Header */
    .queue-panel-header {
        position: sticky !important;
        top: 0 !important;
        background: #181818 !important;
        border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        padding: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        z-index: 10 !important;
        flex-shrink: 0 !important;
    }

    .queue-panel-header h3 {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: white !important;
        margin: 0 !important;
    }

    .queue-close-btn {
        background: none !important;
        border: none !important;
        color: #9ca3af !important;
        cursor: pointer !important;
        padding: 8px !important;
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .queue-close-btn:hover {
        background: rgba(255,255,255,0.1) !important;
        color: white !important;
    }

    .queue-close-btn i {
        font-size: 16px !important;
    }

    /* Queue Scroll Area */
    .queue-scroll-area {
        flex: 1 !important;
        overflow-y: auto !important;
        padding: 16px !important;
        padding-bottom: 120px !important;
    }

    /* Queue Section Header */
    .queue-header {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        font-size: 12px;
        color: #9ca3af;
        font-weight: 700;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }

    .queue-count {
        font-size: 11px;
        color: #6b7280;
    }

    /* Queue List Container */
    .queue-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
    }

    /* Queue Item - MAIN FLEXBOX - Compact & Professional */
    .queue-item {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 8px !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        transition: background 0.15s ease !important;
        background: transparent !important;
        position: relative !important;
    }

    .queue-item:hover {
        background: rgba(255,255,255,0.05) !important;
    }

    .queue-item:hover .queue-remove-btn {
        opacity: 1 !important;
    }

    /* Active (Currently Playing) Item */
    .queue-item-active {
        background: rgba(29,185,84,0.1) !important;
        border-left: 2px solid #1DB954 !important;
        padding-left: 6px !important;
    }

    .queue-item-active:hover {
        background: rgba(29,185,84,0.15) !important;
    }

    /* Dragging State */
    .queue-item-dragging {
        opacity: 0.5 !important;
    }

    /* Drop Target State */
    .queue-item-drop-target {
        border-top: 2px solid #1DB954 !important;
    }

    /* Drag Handle */
    .queue-drag-handle {
        width: 20px !important;
        min-width: 20px !important;
        text-align: center !important;
        cursor: grab !important;
        flex-shrink: 0 !important;
    }

    .queue-drag-handle i {
        font-size: 12px !important;
        color: #4b5563 !important;
    }

    .queue-item:hover .queue-drag-handle i {
        color: #6b7280 !important;
    }

    /* Queue Index Number */
    .queue-index {
        width: 24px !important;
        min-width: 24px !important;
        text-align: center !important;
        font-weight: 500 !important;
        color: #6b7280 !important;
        font-size: 12px !important;
        flex-shrink: 0 !important;
    }

    .queue-playing-icon {
        color: #1DB954 !important;
        font-size: 10px !important;
    }

    /* Album Cover - Smaller */
    .queue-cover {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        border-radius: 4px !important;
        flex-shrink: 0 !important;
        object-fit: cover !important;
    }

    .queue-cover-active {
        box-shadow: 0 0 0 1px #1DB954 !important;
    }

    /* Song Info Container */
    .queue-info {
        flex: 1 !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    .queue-title {
        font-weight: 500 !important;
        color: white !important;
        font-size: 13px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .queue-title-active {
        color: #1DB954 !important;
    }

    .queue-artist {
        font-size: 11px !important;
        color: #9ca3af !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        margin-top: 1px !important;
    }

    /* Remove Button */
    .queue-remove-btn {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        background: transparent !important;
        border: none !important;
        color: #6b7280 !important;
        cursor: pointer !important;
        padding: 4px !important;
        opacity: 0 !important;
        transition: opacity 0.15s ease, color 0.15s ease !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }

    .queue-remove-btn:hover {
        color: #ef4444 !important;
    }

    .queue-remove-btn i {
        font-size: 10px !important;
    }

    /* Now Playing Section */
    .now-playing-section {
        margin-bottom: 20px !important;
    }

    .now-playing-label {
        font-size: 11px !important;
        color: #9ca3af !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
        letter-spacing: 0.5px !important;
    }

    .now-playing-card {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 10px !important;
        background: #282828 !important;
        border-radius: 6px !important;
    }

    .now-playing-cover {
        width: 44px !important;
        height: 44px !important;
        border-radius: 4px !important;
        flex-shrink: 0 !important;
        object-fit: cover !important;
    }

    .now-playing-info {
        flex: 1 !important;
        min-width: 0 !important;
    }

    .now-playing-title {
        font-weight: 500 !important;
        color: white !important;
        font-size: 13px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .now-playing-artist {
        font-size: 11px !important;
        color: #9ca3af !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        margin-top: 1px !important;
    }

    .now-playing-indicator {
        color: #1DB954 !important;
        font-size: 14px !important;
        flex-shrink: 0 !important;
    }

    /* ============================================
       END QUEUE STYLES
       ============================================ */

    /* Scrollbar styling for queue */
    .queue-scroll-area::-webkit-scrollbar {
        width: 6px;
    }

    .queue-scroll-area::-webkit-scrollbar-track {
        background: transparent;
    }

    .queue-scroll-area::-webkit-scrollbar-thumb {
        background: #3f3f46;
        border-radius: 3px;
    }

    .queue-scroll-area::-webkit-scrollbar-thumb:hover {
        background: #52525b;
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .queue-panel {
            width: 100% !important;
        }
    }
</style>

<script>
// 🔒 Safe Storage Wrapper - Prevents "Access to storage is not allowed" errors
const safeStorage = {
    getItem(key) {
        try {
            return localStorage.getItem(key);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
            return null;
        }
    },
    setItem(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
        }
    },
    removeItem(key) {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
        }
    }
};

function muzibuApp() {
    return {
        // Tenant-specific translations
        lang: @json($playerLang ?? []),
        frontLang: @json($frontLang ?? []),

        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        currentUser: @json(auth()->check() ? ['id' => auth()->user()->id, 'name' => auth()->user()->name, 'email' => auth()->user()->email] : null),
        showAuthModal: null,
        showQueue: false,
        progressPercent: 0,
        loginForm: {
            email: safeStorage.getItem('remembered_email') || '',
            password: '',
            remember: safeStorage.getItem('remembered_email') ? true : false
        },
        registerForm: { firstName: '', lastName: '', name: '', email: '', password: '', phone: '' },
        forgotForm: { email: '' },
        showPassword: false,
        showLoginPassword: false,
        tenantId: {{ tenant('id') ?? 2 }},
        registerValidation: {
            name: { valid: false, message: '' },
            email: { valid: false, message: '' },
            phone: { valid: false, message: '' },
            password: {
                valid: false,
                strength: 0,
                strengthText: '',
                checks: { length: false, uppercase: false, lowercase: false, number: false }
            }
        },
        loginValidation: {
            email: { valid: false, message: '' },
            password: { valid: false, message: '' }
        },
        forgotValidation: {
            email: { valid: false, message: '' }
        },
        phoneCountry: {
            code: '+90',
            flag: '🇹🇷',
            name: 'Türkiye',
            placeholder: '5__ ___ __ __',
            format: 'XXX XXX XX XX'
        },
        phoneCountries: [
            { code: '+90', flag: '🇹🇷', name: 'Türkiye', placeholder: '5__ ___ __ __', format: 'XXX XXX XX XX' },
            { code: '+1', flag: '🇺🇸', name: 'Amerika', placeholder: '(___) ___-____', format: '(XXX) XXX-XXXX' },
            { code: '+44', flag: '🇬🇧', name: 'İngiltere', placeholder: '____ ______', format: 'XXXX XXXXXX' },
            { code: '+49', flag: '🇩🇪', name: 'Almanya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+33', flag: '🇫🇷', name: 'Fransa', placeholder: '_ __ __ __ __', format: 'X XX XX XX XX' },
            { code: '+39', flag: '🇮🇹', name: 'İtalya', placeholder: '___ _______', format: 'XXX XXXXXXX' },
            { code: '+34', flag: '🇪🇸', name: 'İspanya', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+31', flag: '🇳🇱', name: 'Hollanda', placeholder: '_ ________', format: 'X XXXXXXXX' },
            { code: '+32', flag: '🇧🇪', name: 'Belçika', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+41', flag: '🇨🇭', name: 'İsviçre', placeholder: '__ ___ __ __', format: 'XX XXX XX XX' },
            { code: '+43', flag: '🇦🇹', name: 'Avusturya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+7', flag: '🇷🇺', name: 'Rusya', placeholder: '(___) ___-__-__', format: '(XXX) XXX-XX-XX' },
            { code: '+86', flag: '🇨🇳', name: 'Çin', placeholder: '___ ____ ____', format: 'XXX XXXX XXXX' },
            { code: '+81', flag: '🇯🇵', name: 'Japonya', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+82', flag: '🇰🇷', name: 'Güney Kore', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+971', flag: '🇦🇪', name: 'BAE', placeholder: '__ ___ ____', format: 'XX XXX XXXX' },
            { code: '+966', flag: '🇸🇦', name: 'Suudi Arabistan', placeholder: '__ ___ ____', format: 'XX XXX XXXX' }
        ],
        favorites: [],
        isPlaying: false,
        isLiked: false,
        shuffle: false,
        repeatMode: 'off',
        currentTime: 0,
        duration: 240,
        volume: 70,
        isMuted: false,
        currentSong: null,
        queue: [],
        queueIndex: 0,
        isLoading: false,
        isLoggingOut: false,
        currentPath: window.location.pathname,
        _initialized: false,
        isDarkMode: safeStorage.getItem('theme') === 'light' ? false : true,
        draggedIndex: null,
        dropTargetIndex: null,

        // Crossfade settings (using Howler.js + HLS.js)
        crossfadeEnabled: true,
        crossfadeDuration: 6000, // 6 seconds for automatic song transitions
        fadeOutDuration: 1000, // 1 second for pause/play/manual change fade
        isCrossfading: false,
        howl: null, // Current Howler instance (for MP3)
        howlNext: null, // Next song Howler instance for crossfade
        hls: null, // Current HLS.js instance
        hlsNext: null, // Next HLS.js instance for crossfade
        isHlsStream: false, // Whether current stream is HLS
        activeHlsAudioId: 'hlsAudio', // Which HLS audio element is active ('hlsAudio' or 'hlsAudioNext')
        progressInterval: null, // Interval for updating progress
        _fadeAnimation: null, // For requestAnimationFrame fade

        // Computed: Current stream type
        get currentStreamType() {
            return this.isHlsStream ? 'hls' : 'mp3';
        },

        // Get the currently active HLS audio element
        getActiveHlsAudio() {
            if (this.activeHlsAudioId === 'hlsAudioNext') {
                return document.getElementById('hlsAudioNext');
            }
            return this.$refs.hlsAudio;
        },

        init() {
            // Prevent double initialization
            if (this._initialized) {
                console.log('Muzibu already initialized, skipping...');
                return;
            }
            this._initialized = true;

            console.log('Muzibu initialized with Howler.js');

            // User already loaded from Laravel backend (no need for API check)
            console.log('Muzibu initialized', { isLoggedIn: this.isLoggedIn, user: this.currentUser });

            // Load featured playlists on init
            this.loadFeaturedPlaylists();

            // SPA Navigation: Handle browser back/forward
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.url) {
                    this.loadPage(e.state.url, false);
                }
            });

            // SPA Navigation: Intercept all internal links
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');

                // Skip if no href, hash link, external link, or has download/target attribute
                if (!href ||
                    href.startsWith('#') ||
                    href.startsWith('http') ||
                    href.startsWith('//') ||
                    link.hasAttribute('download') ||
                    link.hasAttribute('target')) {
                    return;
                }

                // Internal link - use SPA navigation
                e.preventDefault();
                this.navigateTo(href);
            });
        },

        async loadFeaturedPlaylists() {
            try {
                const response = await fetch('/api/muzibu/playlists/featured');
                const playlists = await response.json();
                console.log('Featured playlists loaded:', playlists.length);
            } catch (error) {
                console.error('Failed to load playlists:', error);
            }
        },

        toggleFavorite(type, id) {
            const key = `${type}-${id}`;
            if (this.favorites.includes(key)) {
                this.favorites = this.favorites.filter(f => f !== key);
                this.showToast('Favorilerden kaldırıldı', 'info');
            } else {
                this.favorites.push(key);
                this.showToast('Favorilere eklendi', 'success');
            }
        },

        isFavorite(type, id) {
            return this.favorites.includes(`${type}-${id}`);
        },

        async togglePlayPause() {
            // Eğer queue boşsa, rastgele şarkılar yükle
            if (this.queue.length === 0 || !this.currentSong) {
                await this.playRandomSongs();
                return;
            }

            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            if (this.isPlaying) {
                // Fade out then pause
                if (this.howl) {
                    const currentVolume = this.howl.volume();
                    this.howl.fade(currentVolume, 0, this.fadeOutDuration);
                    this.howl.once('fade', () => {
                        this.howl.pause();
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    });
                } else if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        await this.fadeAudioElement(audio, audio.volume, 0, this.fadeOutDuration);
                        audio.pause();
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    }
                }
            } else {
                // Fade in then play
                if (this.howl) {
                    this.howl.volume(0);
                    this.howl.play();
                    this.howl.fade(0, targetVolume, this.fadeOutDuration);
                    this.isPlaying = true;
                } else if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.volume = 0;
                        await audio.play();
                        this.fadeAudioElement(audio, 0, targetVolume, this.fadeOutDuration);
                        this.isPlaying = true;
                    }
                }
            }
        },

        async playRandomSongs() {
            try {
                this.isLoading = true;

                // Popüler şarkılardan rastgele 50 şarkı al
                const response = await fetch('/api/muzibu/songs/popular?limit=50');
                const songs = await response.json();

                if (songs.length > 0) {
                    // Shuffle songs
                    const shuffled = songs.sort(() => Math.random() - 0.5);

                    this.queue = shuffled;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast('Rastgele çalma başladı!', 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Failed to play random songs:', error);
                this.showToast('Şarkılar yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async previousTrack() {
            if (this.queueIndex > 0) {
                this.queueIndex--;
                await this.playSongFromQueue(this.queueIndex);
            }
        },

        async nextTrack() {
            if (this.queueIndex < this.queue.length - 1) {
                this.queueIndex++;
                await this.playSongFromQueue(this.queueIndex);
            } else if (this.repeatMode === 'all') {
                this.queueIndex = 0;
                await this.playSongFromQueue(this.queueIndex);
            } else {
                this.isPlaying = false;
            }
        },

        cycleRepeat() {
            const modes = ['off', 'all', 'one'];
            const idx = modes.indexOf(this.repeatMode);
            this.repeatMode = modes[(idx + 1) % modes.length];
        },

        async toggleLike() {
            if (!this.currentSong) return;

            const songId = this.currentSong.song_id;
            const previousState = this.isLiked;

            // Optimistic UI update
            this.isLiked = !this.isLiked;

            try {
                const response = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        model_class: 'Modules\\Muzibu\\App\\Models\\Song',
                        model_id: songId
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    // Başarısız ise eski haline döndür
                    this.isLiked = previousState;

                    // Eğer unauthorized ise login modali göster
                    if (response.status === 401) {
                        this.showAuthModal = 'login';
                    }
                }
            } catch (error) {
                console.error('Favorite toggle error:', error);
                // Hata durumunda eski haline döndür
                this.isLiked = previousState;
            }
        },

        toggleMute() {
            this.isMuted = !this.isMuted;
            if (this.howl) {
                this.howl.mute(this.isMuted);
            }
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio) {
                    audio.muted = this.isMuted;
                }
            }
        },

        // Progress tracking is handled by Howler.js in loadAndPlaySong()

        // Get index of next song (considering repeat and shuffle)
        getNextSongIndex() {
            if (this.repeatMode === 'one') {
                return this.queueIndex; // Same song
            }

            if (this.queueIndex < this.queue.length - 1) {
                return this.queueIndex + 1;
            } else if (this.repeatMode === 'all') {
                return 0; // Loop back
            }

            return -1; // No next song
        },

        // Start crossfade transition (using Howler.js)
        async startCrossfade() {
            if (this.isCrossfading) return;

            // Check if any player is active (Howler OR HLS)
            const hasActiveHowler = this.howl && this.howl.playing();
            const audio = this.$refs.hlsAudio;
            const hasActiveHls = this.hls && audio && !audio.paused;

            if (!hasActiveHowler && !hasActiveHls) return;

            const nextIndex = this.getNextSongIndex();
            if (nextIndex === -1) return;

            const nextSong = this.queue[nextIndex];
            if (!nextSong) return;

            this.isCrossfading = true;
            console.log('Starting crossfade...');

            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // Get next song URL and type
            try {
                const response = await fetch(`/api/muzibu/songs/${nextSong.song_id}/stream`);
                const data = await response.json();

                if (!data.stream_url) {
                    this.isCrossfading = false;
                    return;
                }

                const nextStreamType = data.stream_type || 'mp3';
                const nextIsHls = nextStreamType === 'hls';

                console.log('Next song type:', nextStreamType, 'URL:', data.stream_url);

                // Create next player based on stream type
                if (nextIsHls) {
                    // Create HLS player for next song
                    await this.createNextHlsPlayer(data.stream_url, targetVolume);
                } else {
                    // Create Howler for next song (MP3)
                    this.createNextHowlerPlayer(data.stream_url, targetVolume);
                }

                // Fade out current player (Howler or HLS)
                if (hasActiveHowler) {
                    this.howl.fade(targetVolume, 0, this.crossfadeDuration);
                } else if (hasActiveHls) {
                    this.fadeAudioElement(audio, audio.volume, 0, this.crossfadeDuration);
                }

                // After crossfade duration, complete the transition
                setTimeout(() => {
                    this.completeCrossfade(nextIndex, nextIsHls);
                }, this.crossfadeDuration);

            } catch (error) {
                console.error('Crossfade error:', error);
                this.isCrossfading = false;
            }
        },

        // Create next Howler player for crossfade
        createNextHowlerPlayer(url, targetVolume) {
            const self = this;

            // Determine format from URL
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];

            this.howlNext = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: 0,
                onplay: function() {
                    // Fade in next song
                    self.howlNext.fade(0, targetVolume, self.crossfadeDuration);
                },
                onloaderror: function(id, error) {
                    console.error('Howler load error (crossfade):', error);
                }
            });

            // Start playing next
            this.howlNext.play();
        },

        // Create next HLS player for crossfade
        async createNextHlsPlayer(url, targetVolume) {
            const self = this;

            // Create a second audio element for crossfade
            let nextAudio = document.getElementById('hlsAudioNext');
            if (!nextAudio) {
                nextAudio = document.createElement('audio');
                nextAudio.id = 'hlsAudioNext';
                nextAudio.style.display = 'none';
                document.body.appendChild(nextAudio);
            }

            return new Promise((resolve, reject) => {
                if (Hls.isSupported()) {
                    this.hlsNext = new Hls({
                        enableWorker: true,
                        lowLatencyMode: false
                    });

                    this.hlsNext.loadSource(url);
                    this.hlsNext.attachMedia(nextAudio);

                    this.hlsNext.on(Hls.Events.MANIFEST_PARSED, function() {
                        nextAudio.volume = 0;
                        nextAudio.play().then(() => {
                            // Fade in next HLS stream
                            self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
                            resolve();
                        }).catch(e => {
                            console.error('HLS crossfade play error:', e);
                            reject(e);
                        });
                    });

                    this.hlsNext.on(Hls.Events.ERROR, function(event, data) {
                        if (data.fatal) {
                            console.error('HLS crossfade fatal error:', data);
                            reject(data);
                        }
                    });
                } else if (nextAudio.canPlayType('application/vnd.apple.mpegurl')) {
                    // Native HLS support (Safari)
                    nextAudio.src = url;
                    nextAudio.volume = 0;
                    nextAudio.play().then(() => {
                        self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
                        resolve();
                    }).catch(reject);
                } else {
                    console.error('HLS not supported for crossfade');
                    reject(new Error('HLS not supported'));
                }
            });
        },

        // Complete the crossfade transition
        completeCrossfade(nextIndex, nextIsHls = false) {
            // Stop and unload old Howler
            if (this.howl) {
                this.howl.stop();
                this.howl.unload();
                this.howl = null;
            }

            // Stop and unload old HLS
            if (this.hls) {
                const oldAudio = this.$refs.hlsAudio;
                if (oldAudio) {
                    oldAudio.pause();
                    oldAudio.src = '';
                }
                this.hls.destroy();
                this.hls = null;
            }

            // Clear old progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }

            // Swap next player to current based on type
            if (nextIsHls) {
                // HLS crossfade - swap hlsNext to hls
                this.hls = this.hlsNext;
                this.hlsNext = null;
                this.isHlsStream = true;

                // Mark hlsAudioNext as the active audio element
                this.activeHlsAudioId = 'hlsAudioNext';

                // Get reference to the next audio element (now becomes main)
                const nextAudio = document.getElementById('hlsAudioNext');
                if (nextAudio) {
                    this.duration = nextAudio.duration || 0;

                    // Set up ended handler for the new audio
                    const self = this;
                    nextAudio.onended = function() {
                        if (!self.isCrossfading) {
                            self.onTrackEnded();
                        }
                    };
                }

                // Start progress tracking with next audio element
                this.startProgressTrackingWithElement(nextAudio);

            } else {
                // MP3 crossfade - swap howlNext to howl
                this.howl = this.howlNext;
                this.howlNext = null;
                this.isHlsStream = false;

                // Get duration and start tracking
                if (this.howl) {
                    this.duration = this.howl.duration();
                }
                this.startProgressTracking('howler');
            }

            // Update queue index and current song
            this.queueIndex = nextIndex;
            this.currentSong = this.queue[nextIndex];

            // Reset crossfade state
            this.isCrossfading = false;

            console.log('Crossfade complete, now playing:', this.currentSong?.song_title?.tr);
        },

        seekTo(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            const newTime = this.duration * percent;

            if (this.howl && this.duration) {
                this.howl.seek(newTime);
            }
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && this.duration) {
                    audio.currentTime = newTime;
                }
            }

            this.currentTime = newTime;
            this.progressPercent = percent * 100;
        },

        setVolume(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.volume = Math.max(0, Math.min(100, percent * 100));

            const volumeValue = this.volume / 100;

            if (this.howl) {
                this.howl.volume(volumeValue);
            }
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio) {
                    audio.volume = volumeValue;
                }
            }

            if (this.isMuted && this.volume > 0) {
                this.isMuted = false;
                if (this.howl) {
                    this.howl.mute(false);
                }
                if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.muted = false;
                    }
                }
            }
        },

        // Metadata is handled by Howler.js onload callback

        onTrackEnded() {
            // Dispatch stop event (track ended naturally)
            window.dispatchEvent(new CustomEvent('player:stop'));

            if (this.repeatMode === 'one') {
                // Repeat current song
                if (this.howl) {
                    this.howl.seek(0);
                    this.howl.play();
                }
                if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            } else {
                this.nextTrack();
            }
        },

        formatTime(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return `${m}:${s.toString().padStart(2, '0')}`;
        },

        async playAlbum(id) {
            try {
                this.isLoading = true;
                const response = await fetch(`/api/muzibu/albums/${id}`);
                const album = await response.json();

                if (album.songs && album.songs.length > 0) {
                    this.queue = album.songs;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast(`${album.album_title.tr} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play album:', error);
                this.showToast('Albüm yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playPlaylist(id) {
            try {
                this.isLoading = true;
                const response = await fetch(`/api/muzibu/playlists/${id}`);
                const playlist = await response.json();

                if (playlist.songs && playlist.songs.length > 0) {
                    this.queue = playlist.songs;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast(`${playlist.title.tr} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play playlist:', error);
                this.showToast('Playlist yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSong(id) {
            try {
                this.isLoading = true;

                // Get song details first
                const songResponse = await fetch(`/api/muzibu/songs/popular?limit=100`);
                const songs = await songResponse.json();

                // Find the specific song by ID
                const song = songs.find(s => s.song_id == id);

                if (song) {
                    // Create queue with just this song
                    this.queue = [song];
                    this.queueIndex = 0;
                    this.currentSong = song;

                    // Get stream URL
                    const streamResponse = await fetch(`/api/muzibu/songs/${id}/stream`);

                    // 🔐 403/401 Check: Backend auth/limit hatası
                    if (!streamResponse.ok) {
                        // Play limits component'ine bildir (modal aç!)
                        const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                        if (playLimitsComponent) {
                            playLimitsComponent.limitExceeded = true;
                            playLimitsComponent.showLimitModal = true;
                            playLimitsComponent.remainingPlays = 0;
                        }

                        this.showToast('Günlük limit doldu', 'error');
                        return; // Şarkıyı çalma!
                    }

                    const streamData = await streamResponse.json();

                    // 🔐 LIMIT CHECK: Üye limit aştıysa çalma!
                    if (streamData.status === 'limit_exceeded') {
                        // Play limits component'ine bildir
                        const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                        if (playLimitsComponent) {
                            playLimitsComponent.limitExceeded = true;
                            playLimitsComponent.showLimitModal = true;
                            playLimitsComponent.remainingPlays = 0;
                        }

                        this.showToast('Günlük limit doldu', 'error');
                        return; // Şarkıyı çalma!
                    }

                    await this.loadAndPlaySong(streamData.stream_url);
                    this.showToast('Şarkı çalınıyor', 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Failed to play song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSongFromQueue(index) {
            if (index < 0 || index >= this.queue.length) return;

            const song = this.queue[index];
            this.currentSong = song;
            this.queueIndex = index;

            // Check if song is favorited
            this.checkFavoriteStatus(song.song_id);

            try {
                const response = await fetch(`/api/muzibu/songs/${song.song_id}/stream`);

                // 🔐 403/401 Check: Backend auth/limit hatası
                if (!response.ok) {
                    // Play limits component'ine bildir (modal aç!)
                    const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                    if (playLimitsComponent) {
                        playLimitsComponent.limitExceeded = true;
                        playLimitsComponent.showLimitModal = true;
                        playLimitsComponent.remainingPlays = 0;
                    }

                    this.showToast('Günlük limit doldu', 'error');
                    return; // Şarkıyı çalma!
                }

                const data = await response.json();

                // 🔐 LIMIT CHECK: Üye limit aştıysa çalma!
                if (data.status === 'limit_exceeded') {
                    // Play limits component'ine bildir
                    const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                    if (playLimitsComponent) {
                        playLimitsComponent.limitExceeded = true;
                        playLimitsComponent.showLimitModal = true;
                        playLimitsComponent.remainingPlays = 0;
                    }

                    this.showToast('Günlük limit doldu', 'error');
                    return; // Şarkıyı çalma!
                }

                // Pass stream type from API response ('hls' or 'mp3')
                const streamType = data.stream_type || 'mp3';
                console.log('Playing song:', data.stream_url, 'Type:', streamType);
                await this.loadAndPlaySong(data.stream_url, streamType);

                // Prefetch HLS for next songs in queue (background)
                this.prefetchHlsForQueue(index);
            } catch (error) {
                console.error('Failed to load song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
            }
        },

        // Prefetch HLS conversion for upcoming songs in queue
        prefetchHlsForQueue(currentIndex) {
            // Prefetch next 3 songs (or remaining songs if less)
            const prefetchCount = 3;
            const startIndex = currentIndex + 1;
            const endIndex = Math.min(startIndex + prefetchCount, this.queue.length);

            for (let i = startIndex; i < endIndex; i++) {
                const song = this.queue[i];
                if (song && song.song_id) {
                    // Fire and forget - just trigger the API to start HLS conversion
                    fetch(`/api/muzibu/songs/${song.song_id}/stream`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.hls_converting) {
                                console.log(`HLS prefetch started for: ${song.song_title?.tr || song.song_id}`);
                            }
                        })
                        .catch(() => {}); // Ignore errors for prefetch
                }
            }
        },

        async checkFavoriteStatus(songId) {
            // Reset to false while checking
            this.isLiked = false;

            // Only check if user is logged in
            @auth
            try {
                const response = await fetch(`/api/favorites/check?model_class=Modules\\Muzibu\\App\\Models\\Song&model_id=${songId}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.isLiked = data.is_favorited || false;
                }
            } catch (error) {
                console.error('Failed to check favorite status:', error);
            }
            @endauth
        },

        async loadAndPlaySong(url, streamType = null) {
            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // Stop and fade out current playback
            await this.stopCurrentPlayback();

            // Clear progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }

            // Use stream type from API if provided, otherwise detect from URL
            let useHls = false;
            if (streamType) {
                useHls = streamType === 'hls';
            } else {
                // Fallback: detect from URL
                const isDirectAudio = url.match(/\.(mp3|ogg|wav|webm|aac|m4a)(\?|$)/i);
                const isHlsUrl = url.includes('.m3u8') || url.includes('m3u8') || url.includes('/hls/');
                useHls = isHlsUrl || !isDirectAudio;
            }

            console.log('loadAndPlaySong:', { url, streamType, useHls });

            if (useHls) {
                this.isHlsStream = true;
                await this.playHlsStream(url, targetVolume);
            } else {
                this.isHlsStream = false;
                await this.playWithHowler(url, targetVolume);
            }
        },

        // Stop current playback with fade out
        async stopCurrentPlayback() {
            const targetVolume = this.volume / 100;
            let wasStopped = false;

            // Stop Howler if playing
            if (this.howl) {
                if (this.howl.playing()) {
                    wasStopped = true;
                    await new Promise(resolve => {
                        const currentVolume = this.howl.volume();
                        this.howl.fade(currentVolume, 0, this.fadeOutDuration);
                        this.howl.once('fade', () => {
                            this.howl.stop();
                            this.howl.unload();
                            this.howl = null;
                            resolve();
                        });
                    });
                } else {
                    this.howl.unload();
                    this.howl = null;
                }
            }

            // Stop HLS if playing (check both audio elements)
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && !audio.paused) {
                    wasStopped = true;
                    await this.fadeAudioElement(audio, audio.volume, 0, this.fadeOutDuration);
                    audio.pause();
                }
                this.hls.destroy();
                this.hls = null;
            }

            // Also clean up hlsAudioNext if exists
            const nextAudio = document.getElementById('hlsAudioNext');
            if (nextAudio) {
                nextAudio.pause();
                nextAudio.src = '';
            }

            // Reset active HLS audio to default
            this.activeHlsAudioId = 'hlsAudio';

            // Dispatch stop event if something was actually stopped
            if (wasStopped) {
                window.dispatchEvent(new CustomEvent('player:stop'));
            }
        },

        // Play using Howler.js (for MP3, etc.)
        async playWithHowler(url, targetVolume) {
            const self = this;

            // Determine format from URL or default to mp3
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];

            this.howl = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: 0,
                onload: function() {
                    self.duration = self.howl.duration();
                    console.log('Howler loaded, duration:', self.duration);
                },
                onplay: function() {
                    self.isPlaying = true;
                    self.startProgressTracking('howler');

                    // Dispatch event for play-limits
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));
                },
                onend: function() {
                    if (!self.isCrossfading) {
                        self.onTrackEnded();
                    }
                },
                onloaderror: function(id, error) {
                    console.error('Howler load error:', error);
                    console.error('❌ MP3 playback failed, cannot fallback (already in fallback mode)');
                    self.showToast('Şarkı yüklenemedi', 'error');
                    self.isPlaying = false;

                    // Bir sonraki şarkıya geç
                    setTimeout(() => {
                        self.nextTrack();
                    }, 1500);
                },
                onplayerror: function(id, error) {
                    console.error('Howler play error:', error);
                    self.showToast('Çalma hatası', 'error');
                    self.isPlaying = false;
                }
            });

            this.howl.play();
            this.howl.fade(0, targetVolume, this.fadeOutDuration);
            this.isPlaying = true;
        },

        // Play using HLS.js (for HLS streams)
        async playHlsStream(url, targetVolume) {
            const self = this;
            const audio = this.$refs.hlsAudio;

            if (!audio) {
                console.error('HLS audio element not found');
                return;
            }

            // Check HLS.js support
            if (Hls.isSupported()) {
                this.hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: false
                });

                this.hls.loadSource(url);
                this.hls.attachMedia(audio);

                this.hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    audio.volume = 0;
                    audio.play().then(() => {
                        self.isPlaying = true;
                        self.fadeAudioElement(audio, 0, targetVolume, self.fadeOutDuration);
                        self.startProgressTracking('hls');

                        // Dispatch event for play-limits (HLS)
                        window.dispatchEvent(new CustomEvent('player:play', {
                            detail: {
                                songId: self.currentSong?.song_id,
                                isLoggedIn: self.isLoggedIn
                            }
                        }));
                    }).catch(e => {
                        console.error('HLS play error:', e);
                        self.showToast('Çalma hatası', 'error');
                    });
                });

                this.hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        console.error('HLS fatal error:', data);

                        // HLS yüklenemezse MP3'e fallback
                        if (data.type === Hls.ErrorTypes.NETWORK_ERROR && self.currentSong) {
                            console.log('🔄 HLS failed, falling back to MP3...');
                            const mp3Url = `/api/muzibu/songs/${self.currentSong.song_id}/serve`;

                            // Cleanup HLS
                            if (self.hls) {
                                self.hls.destroy();
                                self.hls = null;
                            }

                            // Queue'ye MP3 conversion job ekle (background)
                            self.showToast('MP3 ile çalıyor, HLS hazırlanıyor...', 'info');

                            // MP3 ile çal
                            self.playWithHowler(mp3Url, targetVolume);
                        } else {
                            self.showToast('Şarkı yüklenemedi', 'error');
                            self.isPlaying = false;
                        }
                    }
                });

                // Handle track end
                audio.onended = function() {
                    if (!self.isCrossfading) {
                        self.onTrackEnded();
                    }
                };

                // Get duration when available
                audio.onloadedmetadata = function() {
                    self.duration = audio.duration;
                    console.log('HLS loaded, duration:', self.duration);
                };
            } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
                // Native HLS support (Safari)
                audio.src = url;
                audio.volume = 0;
                audio.play().then(() => {
                    self.isPlaying = true;
                    self.fadeAudioElement(audio, 0, targetVolume, self.fadeOutDuration);
                    self.startProgressTracking('hls');

                    // Dispatch event for play-limits (Safari native HLS)
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));
                });
            } else {
                console.error('HLS not supported');
                this.showToast('HLS desteklenmiyor', 'error');
            }
        },

        // Fade audio element volume using requestAnimationFrame
        fadeAudioElement(audio, fromVolume, toVolume, duration) {
            return new Promise(resolve => {
                if (this._fadeAnimation) cancelAnimationFrame(this._fadeAnimation);

                const startTime = performance.now();
                const volumeDiff = toVolume - fromVolume;

                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    audio.volume = fromVolume + (volumeDiff * progress);

                    if (progress < 1) {
                        this._fadeAnimation = requestAnimationFrame(animate);
                    } else {
                        audio.volume = toVolume;
                        resolve();
                    }
                };

                this._fadeAnimation = requestAnimationFrame(animate);
            });
        },

        // Start progress tracking for either Howler or HLS
        startProgressTracking(type) {
            const self = this;

            this.progressInterval = setInterval(() => {
                let currentTime = 0;
                let isCurrentlyPlaying = false;

                if (type === 'howler' && this.howl) {
                    currentTime = this.howl.seek();
                    isCurrentlyPlaying = this.howl.playing();
                } else if (type === 'hls') {
                    const audio = this.$refs.hlsAudio;
                    if (audio) {
                        currentTime = audio.currentTime;
                        isCurrentlyPlaying = !audio.paused;
                    }
                }

                if (isCurrentlyPlaying && this.duration > 0) {
                    this.currentTime = currentTime;
                    this.progressPercent = (currentTime / this.duration) * 100;

                    // Dispatch time update event for play-limits (every second, not every 100ms)
                    if (Math.floor(currentTime) !== self._lastDispatchedSecond) {
                        self._lastDispatchedSecond = Math.floor(currentTime);
                        window.dispatchEvent(new CustomEvent('player:timeupdate', {
                            detail: {
                                currentTime: Math.floor(currentTime),
                                isLoggedIn: self.isLoggedIn
                            }
                        }));
                    }

                    // Check for crossfade at end of song
                    const timeRemaining = this.duration - currentTime;
                    if (this.crossfadeEnabled && timeRemaining <= (this.crossfadeDuration / 1000) && timeRemaining > 0 && !this.isCrossfading) {
                        this.startCrossfade();
                    }
                }
            }, 100);
        },

        // Start progress tracking with a specific audio element (for HLS crossfade)
        startProgressTrackingWithElement(audioElement) {
            const self = this;

            if (!audioElement) return;

            this.progressInterval = setInterval(() => {
                if (!audioElement.paused && this.duration > 0) {
                    this.currentTime = audioElement.currentTime;
                    this.progressPercent = (audioElement.currentTime / this.duration) * 100;

                    // Check for crossfade at end of song
                    const timeRemaining = this.duration - this.currentTime;
                    if (this.crossfadeEnabled && timeRemaining <= (this.crossfadeDuration / 1000) && timeRemaining > 0 && !this.isCrossfading) {
                        this.startCrossfade();
                    }
                }
            }, 100);
        },

        // SPA Navigation: Navigate to URL
        async navigateTo(url) {
            history.pushState({ url: url }, '', url);
            await this.loadPage(url, true);
        },

        // SPA Navigation: Load page content
        async loadPage(url, addToHistory = true) {
            try {
                // Show loading indicator
                this.isLoading = true;

                // Fetch page content
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const html = await response.text();

                // Parse HTML and extract main content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('main');

                if (newContent) {
                    // Replace main content
                    const currentMain = document.querySelector('main');
                    if (currentMain) {
                        currentMain.innerHTML = newContent.innerHTML;

                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });

                        // Update page title
                        const newTitle = doc.querySelector('title');
                        if (newTitle) {
                            document.title = newTitle.textContent;
                        }

                        // Update current path for active link tracking
                        this.currentPath = url;

                        console.log('Page loaded:', url);
                    }
                } else {
                    console.error('Main content not found in response');
                }

                this.isLoading = false;
            } catch (error) {
                console.error('Failed to load page:', error);
                this.showToast('Sayfa yüklenemedi', 'error');
                this.isLoading = false;

                // Fallback to full page reload on error
                window.location.href = url;
            }
        },

        shareContent(type, id) {
            console.log('Sharing:', type, id);
            this.showToast('Paylaşım linki kopyalandı', 'success');
        },

        addToQueue(type, id) {
            console.log('Adding to queue:', type, id);
            this.showToast('Kuyruğa eklendi', 'success');
        },

        removeFromQueue(index) {
            if (index < 0 || index >= this.queue.length) return;

            // If removing current song, stop playback
            if (index === this.queueIndex) {
                this.isPlaying = false;
                if (this.howl) {
                    this.howl.stop();
                }
            }

            // Remove song from queue
            this.queue.splice(index, 1);

            // Adjust queue index if needed
            if (index < this.queueIndex) {
                this.queueIndex--;
            } else if (index === this.queueIndex && this.queue.length > 0) {
                // If removed current song, play next one
                if (this.queueIndex >= this.queue.length) {
                    this.queueIndex = this.queue.length - 1;
                }
                this.playSongFromQueue(this.queueIndex);
            }

            this.showToast('Şarkı kuyruktan kaldırıldı', 'info');
        },

        goToArtist(id) {
            console.log('Going to artist:', id);
        },

        showToast(message, type = 'info') {
            console.log(`Toast [${type}]:`, message);
        },

        // checkAuth() removed - user data now loaded directly from Laravel backend on page load

        async handleLogin() {
            try {
                this.isLoading = true;
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.loginForm)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Beni Hatırla - email'i kaydet veya sil
                    if (this.loginForm.remember) {
                        safeStorage.setItem('remembered_email', this.loginForm.email);
                    } else {
                        safeStorage.removeItem('remembered_email');
                    }

                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.showToast('Başarıyla giriş yapıldı!', 'success');
                    location.reload();
                } else {
                    this.showToast(data.message || 'Giriş başarısız', 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                this.showToast('Giriş hatası', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async handleRegister() {
            try {
                this.isLoading = true;
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.registerForm)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.registerForm = { firstName: '', lastName: '', name: '', email: '', password: '', phone: '' };
                    this.showToast('Hesabınız oluşturuldu! 7 günlük deneme başladı.', 'success');
                    location.reload(); // Reload to update sidebar
                } else {
                    this.showToast(data.message || 'Kayıt başarısız', 'error');
                }
            } catch (error) {
                console.error('Register error:', error);
                this.showToast('Kayıt hatası', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async handleForgotPassword() {
            try {
                this.isLoading = true;
                const response = await fetch('/api/auth/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.forgotForm)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showToast('Şifre sıfırlama linki e-postanıza gönderildi!', 'success');
                    this.forgotForm = { email: '' };
                    // 3 saniye sonra login modalına dön
                    setTimeout(() => {
                        this.showAuthModal = 'login';
                    }, 3000);
                } else {
                    this.showToast(data.message || 'E-posta gönderilemedi', 'error');
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                this.showToast('Bir hata oluştu', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            // Çift tıklamayı engelle
            if (this.isLoggingOut) return;

            // Hemen UI'ı güncelle
            this.isLoggingOut = true;
            this.isLoggedIn = false;
            this.currentUser = null;
            this.showAuthModal = null;

            try {
                // Logout isteğini BEKLE
                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                // Kısa bekle ve sayfayı yenile (cache'siz)
                setTimeout(() => {
                    window.location.reload(true);
                }, 100);
            } catch (error) {
                console.error('Logout error:', error);
                window.location.reload(true);
            }
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            safeStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            this.showToast(this.isDarkMode ? 'Koyu tema aktif' : 'Açık tema aktif', 'success');
        },

        dragStart(index, event) {
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target);
        },

        dragOver(index) {
            if (this.draggedIndex !== null && this.draggedIndex !== index) {
                this.dropTargetIndex = index;
            }
        },

        drop(dropIndex) {
            if (this.draggedIndex === null || this.draggedIndex === dropIndex) {
                this.draggedIndex = null;
                this.dropTargetIndex = null;
                return;
            }

            // Reorder queue
            const draggedSong = this.queue[this.draggedIndex];
            const newQueue = [...this.queue];

            // Remove dragged item
            newQueue.splice(this.draggedIndex, 1);

            // Insert at drop position
            newQueue.splice(dropIndex, 0, draggedSong);

            // Update queueIndex if needed
            if (this.queueIndex === this.draggedIndex) {
                // Currently playing song was moved
                this.queueIndex = dropIndex;
            } else if (this.draggedIndex < this.queueIndex && dropIndex >= this.queueIndex) {
                // Moved from before to after current
                this.queueIndex--;
            } else if (this.draggedIndex > this.queueIndex && dropIndex <= this.queueIndex) {
                // Moved from after to before current
                this.queueIndex++;
            }

            this.queue = newQueue;
            this.draggedIndex = null;
            this.dropTargetIndex = null;
            this.showToast('Sıra güncellendi', 'success');
        },

        dragEnd() {
            this.draggedIndex = null;
            this.dropTargetIndex = null;
        },

        validateName() {
            const firstName = this.registerForm.firstName.trim();
            const lastName = this.registerForm.lastName.trim();

            // Birleşik name'i güncelle (API için)
            this.registerForm.name = (firstName + ' ' + lastName).trim();

            // Validation
            if (firstName.length >= 2 && lastName.length >= 2 &&
                /^[a-zA-ZğüşöçıİĞÜŞÖÇ]+$/.test(firstName) &&
                /^[a-zA-ZğüşöçıİĞÜŞÖÇ]+$/.test(lastName)) {
                this.registerValidation.name.valid = true;
                this.registerValidation.name.message = '';
            } else {
                this.registerValidation.name.valid = false;
                this.registerValidation.name.message = 'Ad ve soyad en az 2 karakter olmalıdır';
            }
        },

        async validateEmail() {
            const email = this.registerForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
                return;
            }

            if (email.includes('..')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresinde ardışık nokta olamaz';
                return;
            }

            if (email.startsWith('.') || email.endsWith('.')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresi nokta ile başlayamaz veya bitemez';
                return;
            }

            // Format valid, check availability via API
            try {
                const response = await fetch('/api/auth/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (data.exists) {
                    this.registerValidation.email.valid = false;
                    this.registerValidation.email.message = 'Bu e-posta adresi zaten kullanılıyor';
                } else {
                    this.registerValidation.email.valid = true;
                    this.registerValidation.email.message = '';
                }
            } catch (error) {
                console.error('Email check failed:', error);
                // Format is valid, just can't check availability
                this.registerValidation.email.valid = true;
                this.registerValidation.email.message = '';
            }
        },

        selectCountry(country) {
            this.phoneCountry = country;
            this.registerForm.phone = '';
            this.registerValidation.phone.valid = false;
        },

        formatPhoneNumber() {
            let phone = this.registerForm.phone.replace(/\D/g, '');

            // Turkey specific formatting
            if (this.phoneCountry.code === '+90') {
                if (phone.length > 0) {
                    if (phone.length <= 3) {
                        this.registerForm.phone = phone;
                    } else if (phone.length <= 6) {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3);
                    } else if (phone.length <= 8) {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6);
                    } else {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6, 8) + ' ' + phone.substring(8, 10);
                        phone = phone.substring(0, 10);
                    }
                }

                // Validate Turkey phone
                if (phone.length === 0) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası gereklidir';
                } else if (!phone.startsWith('5')) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Cep telefonu 5 ile başlamalıdır';
                } else if (phone.length !== 10) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası 10 haneli olmalıdır';
                } else if (!['50', '51', '52', '53', '54', '55', '56', '58', '59'].includes(phone.substring(0, 2))) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Geçersiz operatör kodu';
                } else {
                    this.registerValidation.phone.valid = true;
                    this.registerValidation.phone.message = '';
                }
            } else {
                // Generic international validation
                this.registerForm.phone = phone;

                if (phone.length === 0) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası gereklidir';
                } else if (phone.length < 7) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası çok kısa';
                } else if (phone.length > 15) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası çok uzun';
                } else {
                    this.registerValidation.phone.valid = true;
                    this.registerValidation.phone.message = '';
                }
            }
        },

        validatePassword() {
            const password = this.registerForm.password;

            // Check individual requirements
            this.registerValidation.password.checks.length = password.length >= 8;
            this.registerValidation.password.checks.uppercase = /[A-Z]/.test(password);
            this.registerValidation.password.checks.lowercase = /[a-z]/.test(password);
            this.registerValidation.password.checks.number = /[0-9]/.test(password);

            // Calculate strength
            let strength = 0;
            if (this.registerValidation.password.checks.length) strength++;
            if (this.registerValidation.password.checks.uppercase) strength++;
            if (this.registerValidation.password.checks.lowercase) strength++;
            if (this.registerValidation.password.checks.number) strength++;
            if (password.length >= 12) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++; // Special char bonus

            // Normalize to 1-4 scale
            this.registerValidation.password.strength = Math.min(4, Math.ceil(strength / 1.5));

            // Set strength text
            const strengthTexts = {
                1: 'Çok Zayıf',
                2: 'Zayıf',
                3: 'Orta',
                4: 'Güçlü'
            };
            this.registerValidation.password.strengthText = strengthTexts[this.registerValidation.password.strength] || '';

            // Password is valid if all basic checks pass
            this.registerValidation.password.valid =
                this.registerValidation.password.checks.length &&
                this.registerValidation.password.checks.uppercase &&
                this.registerValidation.password.checks.lowercase &&
                this.registerValidation.password.checks.number;
        },

        isRegisterFormValid() {
            const basicValid = this.registerValidation.name.valid &&
                             this.registerValidation.email.valid &&
                             this.registerValidation.password.valid;

            // If tenant 1001, phone is also required
            if (this.tenantId === 1001) {
                return basicValid && this.registerValidation.phone.valid;
            }

            return basicValid;
        },

        validateLoginEmail() {
            const email = this.loginForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.loginValidation.email.valid = false;
                this.loginValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
            } else {
                this.loginValidation.email.valid = true;
                this.loginValidation.email.message = '';
            }
        },

        validateLoginPassword() {
            const password = this.loginForm.password;

            if (password.length < 1) {
                this.loginValidation.password.valid = false;
                this.loginValidation.password.message = 'Şifre gereklidir';
            } else {
                this.loginValidation.password.valid = true;
                this.loginValidation.password.message = '';
            }
        },

        validateForgotEmail() {
            const email = this.forgotForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.forgotValidation.email.valid = false;
                this.forgotValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
            } else {
                this.forgotValidation.email.valid = true;
                this.forgotValidation.email.message = '';
            }
        }
    }
}
</script>
