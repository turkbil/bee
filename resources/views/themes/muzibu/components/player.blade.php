<!-- Hidden Audio Element -->
<audio x-ref="audio"
       @timeupdate="updateProgress()"
       @loadedmetadata="onMetadataLoaded()"
       @ended="onTrackEnded()"
       preload="metadata"></audio>

<!-- Auth Modal -->
<div x-show="showAuthModal !== null"
     x-transition
     @click.self="showAuthModal = null"
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 120px; z-index: 50; display: none; align-items: center; justify-content: center; padding: 0 16px; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);">

    <!-- Modal Content -->
    <div style="position: relative; background: #181818; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); width: 100%; border: 1px solid rgba(255,255,255,0.1); max-height: 90vh; display: flex; flex-direction: column;"
         :style="showAuthModal === 'register' ? 'max-width: 42rem' : 'max-width: 28rem'">
        <!-- Close Button -->
        <button @click="showAuthModal = null" style="position: absolute; top: 16px; right: 16px; color: #9ca3af; background: none; border: none; cursor: pointer; z-index: 10; transition: color 0.2s;">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Scrollable Content -->
        <div style="overflow-y: auto; padding: 32px;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #1DB954, #16a34a); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(29,185,84,0.3); margin: 0 auto 16px;">
                <i class="fas fa-music" style="color: white; font-size: 24px;"></i>
            </div>
            <h2 style="font-size: 30px; font-weight: 700; color: white; margin-bottom: 8px;" x-text="showAuthModal === 'login' ? 'Giriş Yap' : 'Ücretsiz Başla'"></h2>
            <p style="color: #9ca3af; font-size: 14px;" x-text="showAuthModal === 'login' ? 'Hesabınıza giriş yapın' : '7 gün ücretsiz deneme - Kredi kartı gerekmez'"></p>
        </div>

        <!-- Login Form -->
        <form x-show="showAuthModal === 'login'" @submit.prevent="handleLogin()" style="display: flex; flex-direction: column; gap: 16px;">
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">E-posta</label>
                <input type="email" x-model="loginForm.email" @input="validateLoginEmail()" required
                       style="width: 100%; padding: 12px 16px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                       :style="loginValidation.email.valid ? 'border-color: #10b981' : (loginForm.email.length > 0 ? 'border-color: #ef4444' : '')"
                       placeholder="ornek@email.com">
                <div style="height: 20px; margin-top: 4px;">
                    <div x-show="!loginValidation.email.valid && loginForm.email.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="loginValidation.email.message"></span>
                    </div>
                </div>
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">Şifre</label>
                <div style="position: relative;">
                    <input :type="showLoginPassword ? 'text' : 'password'" x-model="loginForm.password" @input="validateLoginPassword()" required
                           style="width: 100%; padding: 12px 16px; padding-right: 48px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                           :style="loginValidation.password.valid ? 'border-color: #10b981' : (loginForm.password.length > 0 ? 'border-color: #ef4444' : '')"
                           placeholder="••••••••">
                    <button type="button" @click="showLoginPassword = !showLoginPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                        <i :class="showLoginPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                <div style="height: 20px; margin-top: 4px;">
                    <div x-show="!loginValidation.password.valid && loginForm.password.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="loginValidation.password.message"></span>
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" @click="loginForm.remember = !loginForm.remember"
                            style="position: relative; display: inline-flex; height: 24px; width: 44px; align-items: center; border-radius: 12px; transition: all 0.2s; outline: none; border: none; cursor: pointer;"
                            :style="loginForm.remember ? 'background: #1DB954' : 'background: #4b5563'">
                        <span style="display: inline-block; height: 16px; width: 16px; border-radius: 50%; background: white; transition: transform 0.2s;"
                              :style="loginForm.remember ? 'transform: translateX(24px)' : 'transform: translateX(4px)'"></span>
                    </button>
                    <label style="font-size: 14px; color: #d1d5db; cursor: pointer;" @click="loginForm.remember = !loginForm.remember">Beni Hatırla</label>
                </div>
                <button type="button" @click="showAuthModal = 'forgot'" style="font-size: 14px; color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                    Şifremi Unuttum
                </button>
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(90deg, #1DB954, #16a34a); color: white; font-weight: 700; border-radius: 9999px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(29,185,84,0.4);">
                Giriş Yap
            </button>
            <div style="text-align: center;">
                <button type="button" @click="showAuthModal = 'register'" style="font-size: 14px; color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                    Hesabınız yok mu? <span style="color: #1DB954; font-weight: 600;">Ücretsiz Başlayın</span>
                </button>
            </div>
        </form>

        <!-- Forgot Password Form -->
        <form x-show="showAuthModal === 'forgot'" @submit.prevent="handleForgotPassword()" style="display: flex; flex-direction: column; gap: 16px;">
            <div style="text-align: center; margin-bottom: 16px;">
                <p style="color: #9ca3af; font-size: 14px;">Şifre sıfırlama linki e-posta adresinize gönderilecektir.</p>
            </div>
            <div>
                <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">E-posta</label>
                <input type="email" x-model="forgotForm.email" @input="validateForgotEmail()" required
                       style="width: 100%; padding: 12px 16px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                       :style="forgotValidation.email.valid ? 'border-color: #10b981' : (forgotForm.email.length > 0 ? 'border-color: #ef4444' : '')"
                       placeholder="ornek@email.com">
                <div style="height: 20px; margin-top: 4px;">
                    <div x-show="!forgotValidation.email.valid && forgotForm.email.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="forgotValidation.email.message"></span>
                    </div>
                    <div x-show="forgotValidation.email.valid" style="font-size: 12px; color: #10b981; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-check-circle"></i>
                        <span>Geçerli e-posta adresi</span>
                    </div>
                </div>
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(90deg, #1DB954, #16a34a); color: white; font-weight: 700; border-radius: 9999px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(29,185,84,0.4);">
                Sıfırlama Linki Gönder
            </button>
            <div style="text-align: center;">
                <button type="button" @click="showAuthModal = 'login'" style="font-size: 14px; color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                    <i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Giriş Sayfasına Dön
                </button>
            </div>
        </form>

        <!-- Register Form -->
        <form x-show="showAuthModal === 'register'" @submit.prevent="handleRegister()" style="display: flex; flex-direction: column; gap: 16px;">
            <!-- 2 Kolon Grid -->
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <!-- Sol Kolon: Ad + Email -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">Ad Soyad</label>
                        <input type="text" x-model="registerForm.name" @input="validateName()" required
                               style="width: 100%; padding: 12px 16px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                               :style="registerValidation.name.valid ? 'border-color: #10b981' : (registerForm.name.length > 0 ? 'border-color: #ef4444' : '')"
                               placeholder="Adınız Soyadınız">
                        <div style="height: 20px; margin-top: 4px;">
                            <div x-show="!registerValidation.name.valid && registerForm.name.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span x-text="registerValidation.name.message"></span>
                            </div>
                            <div x-show="registerValidation.name.valid" style="font-size: 12px; color: #10b981; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-check-circle"></i>
                                <span>Geçerli</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">E-posta</label>
                        <input type="email" x-model="registerForm.email" @input.debounce.500ms="validateEmail()" required
                               style="width: 100%; padding: 12px 16px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                               :style="registerValidation.email.valid ? 'border-color: #10b981' : (registerForm.email.length > 0 ? 'border-color: #ef4444' : '')"
                               placeholder="ornek@email.com">
                        <div style="height: 20px; margin-top: 4px;">
                            <div x-show="!registerValidation.email.valid && registerForm.email.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span x-text="registerValidation.email.message"></span>
                            </div>
                            <div x-show="registerValidation.email.valid" style="font-size: 12px; color: #10b981; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-check-circle"></i>
                                <span>Geçerli</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Telefon + Şifre -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Telefon (Tenant 1001 için zorunlu) -->
                    <div x-show="tenantId === 1001">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">
                    Telefon
                    <span style="color: #f87171;">*</span>
                </label>
                <div style="position: relative;">
                    <div style="display: flex; gap: 8px;">
                        <!-- Country Code Selector -->
                        <div style="position: relative;" x-data="{ countryOpen: false }">
                            <button type="button" @click="countryOpen = !countryOpen"
                                    style="padding: 12px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px; min-width: 100px; cursor: pointer;">
                                <span x-text="phoneCountry.flag">🇹🇷</span>
                                <span x-text="phoneCountry.code" style="font-size: 14px;">+90</span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </button>
                            <div x-show="countryOpen" @click.away="countryOpen = false" x-transition
                                 style="position: absolute; top: 100%; left: 0; margin-top: 8px; width: 256px; background: #282828; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); overflow: hidden; z-index: 50; max-height: 256px; overflow-y: auto;">
                                <template x-for="country in phoneCountries" :key="country.code">
                                    <button type="button" @click="selectCountry(country); countryOpen = false"
                                            style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 12px; background: none; border: none; cursor: pointer; transition: all 0.2s; text-align: left; color: white;"
                                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                                            onmouseout="this.style.background='none'">
                                        <span x-text="country.flag" style="font-size: 20px;"></span>
                                        <div style="flex: 1;">
                                            <div style="color: white; font-size: 14px;" x-text="country.name"></div>
                                            <div style="color: #9ca3af; font-size: 12px;" x-text="country.code"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Phone Number Input -->
                        <input type="tel" x-model="registerForm.phone" @input="formatPhoneNumber()" :required="tenantId === 1001"
                               style="flex: 1; padding: 12px 16px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                               :style="registerValidation.phone.valid ? 'border-color: #10b981' : (registerForm.phone.length > 0 ? 'border-color: #ef4444' : '')"
                               :placeholder="phoneCountry.placeholder" maxlength="20">
                    </div>
                    <!-- Validation message with fixed height -->
                    <div style="height: 20px; margin-top: 4px;">
                        <div x-show="!registerValidation.phone.valid && registerForm.phone.length > 0" style="font-size: 12px; color: #f87171; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span x-text="registerValidation.phone.message"></span>
                        </div>
                        <div x-show="registerValidation.phone.valid" style="font-size: 12px; color: #10b981; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-check-circle"></i>
                            <span>Geçerli telefon numarası</span>
                        </div>
                    </div>
                </div>
                    </div>

                    <!-- Şifre -->
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #d1d5db; margin-bottom: 8px;">Şifre</label>
                        <div style="position: relative;">
                            <input :type="showPassword ? 'text' : 'password'" x-model="registerForm.password" @input="validatePassword()" required
                                   style="width: 100%; padding: 12px 16px; padding-right: 48px; background: #282828; color: white; border-radius: 8px; border: 2px solid transparent; outline: none; transition: all 0.2s;"
                                   :style="registerValidation.password.valid ? 'border-color: #10b981' : (registerForm.password.length > 0 ? 'border-color: #ef4444' : '')"
                                   placeholder="En az 8 karakter">
                            <button type="button" @click="showPassword = !showPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>

                        <!-- Şifre Güvenlik Göstergesi (Kompakt) -->
                        <div x-show="registerForm.password.length > 0" style="margin-top: 8px;">
                            <div style="display: flex; gap: 4px; margin-bottom: 4px;">
                                <div style="height: 4px; flex: 1; border-radius: 2px; background: #4b5563; transition: all 0.2s;" :style="registerValidation.password.strength >= 1 ? 'background: #ef4444' : ''"></div>
                                <div style="height: 4px; flex: 1; border-radius: 2px; background: #4b5563; transition: all 0.2s;" :style="registerValidation.password.strength >= 2 ? 'background: #f97316' : ''"></div>
                                <div style="height: 4px; flex: 1; border-radius: 2px; background: #4b5563; transition: all 0.2s;" :style="registerValidation.password.strength >= 3 ? 'background: #facc15' : ''"></div>
                                <div style="height: 4px; flex: 1; border-radius: 2px; background: #4b5563; transition: all 0.2s;" :style="registerValidation.password.strength >= 4 ? 'background: #10b981' : ''"></div>
                            </div>
                            <div style="font-size: 10px; display: flex; align-items: center; justify-content: space-between;">
                                <span :style="registerValidation.password.strength === 1 ? 'color: #f87171' : registerValidation.password.strength === 2 ? 'color: #fb923c' : registerValidation.password.strength === 3 ? 'color: #fde047' : 'color: #10b981'" x-text="registerValidation.password.strengthText"></span>
                                <!-- Kompakt Checkler -->
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <i :class="registerValidation.password.checks.length ? 'fas fa-check-circle' : 'far fa-circle'" :style="registerValidation.password.checks.length ? 'color: #10b981' : 'color: #6b7280'" style="font-size: 10px;" title="8+ karakter"></i>
                                    <i :class="registerValidation.password.checks.uppercase ? 'fas fa-check-circle' : 'far fa-circle'" :style="registerValidation.password.checks.uppercase ? 'color: #10b981' : 'color: #6b7280'" style="font-size: 10px;" title="Büyük harf"></i>
                                    <i :class="registerValidation.password.checks.lowercase ? 'fas fa-check-circle' : 'far fa-circle'" :style="registerValidation.password.checks.lowercase ? 'color: #10b981' : 'color: #6b7280'" style="font-size: 10px;" title="Küçük harf"></i>
                                    <i :class="registerValidation.password.checks.number ? 'fas fa-check-circle' : 'far fa-circle'" :style="registerValidation.password.checks.number ? 'color: #10b981' : 'color: #6b7280'" style="font-size: 10px;" title="Rakam"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" :disabled="!isRegisterFormValid()"
                    style="width: 100%; padding: 12px; background: linear-gradient(90deg, #1DB954, #16a34a); color: white; font-weight: 700; border-radius: 9999px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(29,185,84,0.4);"
                    :style="!isRegisterFormValid() ? 'opacity: 0.5; cursor: not-allowed' : ''">
                7 Gün Ücretsiz Başla
            </button>
            <div style="text-align: center;">
                <button type="button" @click="showAuthModal = 'login'" style="font-size: 14px; color: #9ca3af; background: none; border: none; cursor: pointer; transition: color 0.2s;">
                    Zaten hesabınız var mı? <span style="color: #1DB954; font-weight: 600;">Giriş Yapın</span>
                </button>
            </div>
        </form>
    </div>
</div>
</div>

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
                <div style="font-weight: 600; color: white; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                     x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></div>
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
                        :title="repeatMode === 'off' ? 'Tekrar: Kapalı' : (repeatMode === 'one' ? 'Tekrar: Tek Şarkı' : 'Tekrar: Tümü')"
                        style="background: none; border: none; cursor: pointer; padding: 4px; transition: all 0.2s;"
                        :style="repeatMode !== 'off' ? 'color: #1DB954;' : 'color: #9ca3af;'">
                    <i :class="repeatMode === 'one' ? 'fas fa-repeat-1' : 'fas fa-redo'" style="font-size: 14px;"></i>
                </button>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 12px; color: #9ca3af;" x-text="formatTime(currentTime)">0:00</span>
                <div @click="seekTo($event)"
                     style="flex: 1; height: 4px; background: #4b5563; border-radius: 2px; overflow: hidden; cursor: pointer; position: relative;"
                     onmouseover="this.style.height='6px'" onmouseout="this.style.height='4px'">
                    <div :style="'width: ' + progressPercent + '%; height: 100%; background: #1DB954; border-radius: 2px; transition: width 0.1s;'">
                        <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 12px; height: 12px; background: #1DB954; border-radius: 50%; box-shadow: 0 0 4px rgba(29,185,84,0.6); opacity: 0; transition: opacity 0.2s;"
                             onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'"></div>
                    </div>
                </div>
                <span style="font-size: 12px; color: #9ca3af;" x-text="formatTime(duration)">0:00</span>
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
                <i :class="isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up'"></i>
            </button>
            <div @click="setVolume($event)"
                 title="Ses Seviyesi"
                 style="width: 96px; height: 4px; background: #4b5563; border-radius: 2px; overflow: hidden; cursor: pointer; position: relative;"
                 onmouseover="this.style.height='6px'" onmouseout="this.style.height='4px'">
                <div :style="'width: ' + volume + '%; height: 100%; background: #1DB954; border-radius: 2px; transition: width 0.1s;'">
                    <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 12px; height: 12px; background: #1DB954; border-radius: 50%; box-shadow: 0 0 4px rgba(29,185,84,0.6); opacity: 0; transition: opacity 0.2s;"
                         onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'"></div>
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
     style="position: fixed; right: 0; bottom: 100px; top: 0; width: 400px; background: #181818; border-left: 1px solid rgba(255,255,255,0.1); z-index: 30; display: flex; flex-direction: column;">

    <div style="position: sticky; top: 0; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; z-index: 10;">
        <h3 style="font-size: 22px; font-weight: 700; color: white; margin: 0;">Çalma Sırası</h3>
        <button @click="showQueue = false" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 8px; transition: all 0.2s; border-radius: 50%;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white';" onmouseout="this.style.background='none'; this.style.color='#9ca3af';">
            <i class="fas fa-times" style="font-size: 20px;"></i>
        </button>
    </div>

    <div style="flex: 1; overflow-y: auto; padding: 24px; padding-bottom: 40px;">
        <!-- Şu An Çalan -->
        <div x-show="currentSong" style="margin-bottom: 24px;">
            <div style="font-size: 14px; color: #9ca3af; font-weight: 600; margin-bottom: 8px;">ŞU AN ÇALIYOR</div>
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #282828; border-radius: 8px;">
                <img :src="currentSong?.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=56&h=56&fit=crop'"
                     style="width: 48px; height: 48px; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: white; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                         x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></div>
                    <div style="font-size: 12px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                         x-text="currentSong?.artist_title?.tr || ''"></div>
                </div>
                <div style="color: #1DB954; animation: pulse-slow 2s ease-in-out infinite;">
                    <i class="fas fa-volume-up"></i>
                </div>
            </div>
        </div>

        <!-- Sıradaki Şarkılar -->
        <div x-show="queue.length > 0">
            <div style="font-size: 13px; color: #9ca3af; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; letter-spacing: 0.5px;">
                <span>SIRADAKİ</span>
                <span x-text="queue.length + ' şarkı'" style="font-size: 11px; color: #6b7280;"></span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <template x-for="(song, index) in queue" :key="song.song_id">
                    <div draggable="true"
                         @dragstart="dragStart(index, $event)"
                         @dragover.prevent="dragOver(index)"
                         @drop="drop(index)"
                         @dragend="dragEnd()"
                         @click="playSongFromQueue(index)"
                         class="queue-item"
                         style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 6px; cursor: grab; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; margin-bottom: 2px;"
                         :style="index === queueIndex ? 'background: linear-gradient(90deg, rgba(29,185,84,0.15), rgba(29,185,84,0.05)); border-left: 3px solid #1DB954; padding-left: 9px;' : (draggedIndex === index ? 'opacity: 0.4; transform: scale(0.95);' : (dropTargetIndex === index ? 'border-top: 3px solid #1DB954; padding-top: 13px; margin-top: 6px;' : 'background: transparent;'))"
                         onmouseover="if(!this.style.background.includes('linear-gradient')) { this.style.background='rgba(255,255,255,0.05)'; this.style.transform='translateX(4px)'; }"
                         onmouseout="if(!this.style.background.includes('linear-gradient')) { this.style.background='transparent'; this.style.transform='translateX(0)'; }">
                        <div style="width: 24px; text-align: center; cursor: grab; transition: all 0.2s;"
                             :style="draggedIndex === index ? 'cursor: grabbing;' : ''"
                             onmouseover="this.style.transform='scale(1.2)'"
                             onmouseout="this.style.transform='scale(1)'">
                            <i class="fas fa-grip-vertical" style="font-size: 13px; color: #4b5563; transition: color 0.2s;"
                               onmouseover="this.style.color='#1DB954'"
                               onmouseout="this.style.color='#4b5563'"></i>
                        </div>
                        <div style="width: 28px; text-align: center; font-weight: 600;">
                            <span x-show="index !== queueIndex"
                                  style="color: #6b7280; font-size: 13px; transition: all 0.2s;"
                                  x-text="index + 1"
                                  :style="draggedIndex === index ? 'opacity: 0.5;' : ''"></span>
                            <i x-show="index === queueIndex"
                               class="fas fa-play"
                               style="color: #1DB954; font-size: 11px; animation: pulse-play 1.5s ease-in-out infinite;"></i>
                        </div>
                        <img :src="song.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=40&h=40&fit=crop'"
                             style="width: 44px; height: 44px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.4); transition: all 0.3s;"
                             :style="index === queueIndex ? 'box-shadow: 0 4px 16px rgba(29,185,84,0.4);' : ''"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; color: white; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: color 0.2s;"
                                 :style="index === queueIndex ? 'color: #1DB954;' : ''"
                                 x-text="song.song_title?.tr || 'Untitled'"></div>
                            <div style="font-size: 12px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;"
                                 x-text="song.artist_title?.tr || 'Unknown'"></div>
                        </div>
                        <button @click.stop="removeFromQueue(index)"
                                style="background: rgba(239,68,68,0.1); border: none; color: #ef4444; cursor: pointer; padding: 6px; opacity: 0; transition: all 0.3s; border-radius: 4px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;"
                                onmouseover="this.style.opacity='1'; this.style.background='rgba(239,68,68,0.2)'; this.style.transform='scale(1.1) rotate(90deg)';"
                                onmouseout="this.style.opacity='0'; this.style.background='rgba(239,68,68,0.1)'; this.style.transform='scale(1) rotate(0deg)';">
                            <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Boş Queue Durumu -->
        <div x-show="queue.length === 0" style="text-align: center; padding: 48px 0;">
            <i class="fas fa-music" style="font-size: 40px; color: #4b5563; margin-bottom: 12px; display: block;"></i>
            <p style="color: #9ca3af; font-size: 14px; margin: 0;">Çalma sırası boş</p>
            <p style="color: #6b7280; font-size: 12px; margin-top: 4px;">Bir şarkı veya playlist çalmaya başlayın</p>
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
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(0.9);
        }
    }

    /* Queue item hover effect - smooth slide */
    .queue-item {
        position: relative;
        overflow: hidden;
    }

    .queue-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(29,185,84,0.1), transparent);
        transition: left 0.6s ease;
    }

    .queue-item:hover::before {
        left: 100%;
    }

    /* Drag animation - smooth scale */
    .queue-item[draggable="true"]:active {
        cursor: grabbing !important;
        transform: scale(0.98) rotate(2deg);
        box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }

    /* Drop target indicator - glowing effect */
    .queue-item[style*="border-top: 3px solid"] {
        animation: glow-border 1s ease-in-out infinite;
    }

    @keyframes glow-border {
        0%, 100% {
            box-shadow: 0 -3px 12px rgba(29,185,84,0.3);
        }
        50% {
            box-shadow: 0 -3px 20px rgba(29,185,84,0.6);
        }
    }

    /* Smooth entrance animation for new items */
    @keyframes slideInFromRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Hover effects - buttons */
    button:hover {
        transform: scale(1.05);
    }

    button:active {
        transform: scale(0.95);
    }

    /* Scrollbar styling for queue */
    div[style*="overflow-y: auto"]::-webkit-scrollbar {
        width: 8px;
    }

    div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
        background: #181818;
    }

    div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
        background: #282828;
        border-radius: 4px;
        transition: background 0.3s;
    }

    div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
        background: #3e3e3e;
    }
</style>

<script>
function muzibuApp() {
    return {
        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        currentUser: @json(auth()->check() ? ['id' => auth()->user()->id, 'name' => auth()->user()->name, 'email' => auth()->user()->email] : null),
        showAuthModal: null,
        showQueue: false,
        loginForm: { email: '', password: '', remember: false },
        registerForm: { name: '', email: '', password: '', phone: '' },
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
        currentPath: window.location.pathname,
        _initialized: false,
        isDarkMode: localStorage.getItem('theme') === 'light' ? false : true,
        draggedIndex: null,
        dropTargetIndex: null,

        init() {
            // Prevent double initialization
            if (this._initialized) {
                console.log('Muzibu already initialized, skipping...');
                return;
            }
            this._initialized = true;

            console.log('Muzibu initialized');
            if (this.$refs.audio) {
                this.$refs.audio.volume = this.volume / 100;
            }

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

            this.isPlaying = !this.isPlaying;
            const audio = this.$refs.audio;
            if (audio) {
                if (this.isPlaying) {
                    audio.play();
                } else {
                    audio.pause();
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
            if (this.$refs.audio) {
                this.$refs.audio.muted = this.isMuted;
            }
        },

        updateProgress() {
            const audio = this.$refs.audio;
            if (audio) {
                this.currentTime = audio.currentTime;
                this.duration = audio.duration || 240;
            }
        },

        seekTo(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            const audio = this.$refs.audio;
            if (audio && audio.duration) {
                audio.currentTime = audio.duration * percent;
            }
        },

        setVolume(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.volume = Math.max(0, Math.min(100, percent * 100));
            if (this.$refs.audio) {
                this.$refs.audio.volume = this.volume / 100;
            }
            if (this.isMuted && this.volume > 0) {
                this.isMuted = false;
                if (this.$refs.audio) {
                    this.$refs.audio.muted = false;
                }
            }
        },

        onMetadataLoaded() {
            if (this.$refs.audio) {
                this.duration = this.$refs.audio.duration;
            }
        },

        onTrackEnded() {
            if (this.repeatMode === 'one') {
                if (this.$refs.audio) {
                    this.$refs.audio.play();
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
                    const streamData = await streamResponse.json();

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
                const data = await response.json();
                await this.loadAndPlaySong(data.stream_url);
            } catch (error) {
                console.error('Failed to load song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
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

        async loadAndPlaySong(url) {
            const audio = this.$refs.audio;
            if (!audio) return;

            // Stop current playback first
            if (!audio.paused) {
                audio.pause();
            }

            // Reset audio element
            audio.src = url;
            audio.load();

            try {
                // Wait a bit for load to complete
                await new Promise(resolve => setTimeout(resolve, 100));
                await audio.play();
                this.isPlaying = true;
            } catch (error) {
                // Ignore AbortError (happens when rapidly clicking play buttons)
                if (error.name !== 'AbortError') {
                    console.error('Playback error:', error);
                    this.isPlaying = false;
                    this.showToast('Çalma hatası', 'error');
                }
            }
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
                if (this.$refs.audio) {
                    this.$refs.audio.pause();
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
                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.loginForm = { email: '', password: '', remember: false };
                    this.showToast('Başarıyla giriş yapıldı!', 'success');
                    location.reload(); // Reload to update sidebar
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
                    this.registerForm = { name: '', email: '', password: '', company: '' };
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
            try {
                const response = await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    this.isLoggedIn = false;
                    this.currentUser = null;
                    this.showAuthModal = null;
                    this.showToast('Çıkış yapıldı', 'info');
                    location.reload(); // Reload to clear user data
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Still logout on frontend
                this.isLoggedIn = false;
                this.currentUser = null;
                location.reload();
            }
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
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
            const name = this.registerForm.name.trim();
            if (name.length < 2) {
                this.registerValidation.name.valid = false;
                this.registerValidation.name.message = 'Ad soyad en az 2 karakter olmalıdır';
            } else if (!/^[a-zA-ZğüşöçıİĞÜŞÖÇ\s]+$/.test(name)) {
                this.registerValidation.name.valid = false;
                this.registerValidation.name.message = 'Sadece harf ve boşluk kullanılabilir';
            } else if (name.split(' ').length < 2) {
                this.registerValidation.name.valid = false;
                this.registerValidation.name.message = 'Lütfen ad ve soyadınızı giriniz';
            } else {
                this.registerValidation.name.valid = true;
                this.registerValidation.name.message = '';
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
        },

        get progressPercent() {
            return this.duration ? (this.currentTime / this.duration) * 100 : 0;
        }
    }
}
</script>
