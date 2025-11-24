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
     class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/80 backdrop-blur-sm"
     style="display: none;">

    <!-- Modal Content -->
    <div class="relative bg-spotify-dark rounded-2xl shadow-2xl max-w-md w-full p-8 border border-white/10">
        <!-- Close Button -->
        <button @click="showAuthModal = null" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-all">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-spotify-green to-green-600 rounded-full flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-music text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2" x-text="showAuthModal === 'login' ? 'Giriş Yap' : 'Ücretsiz Başla'"></h2>
            <p class="text-gray-400 text-sm" x-text="showAuthModal === 'login' ? 'Hesabınıza giriş yapın' : '7 gün ücretsiz deneme - Kredi kartı gerekmez'"></p>
        </div>

        <!-- Login Form -->
        <form x-show="showAuthModal === 'login'" @submit.prevent="handleLogin()" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                <input type="email" x-model="loginForm.email" @input="validateLoginEmail()" required class="w-full px-4 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="loginValidation.email.valid ? 'ring-green-500' : (loginForm.email.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="ornek@email.com">
                <div class="h-5 mt-1">
                    <div x-show="!loginValidation.email.valid && loginForm.email.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="loginValidation.email.message"></span>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Şifre</label>
                <div class="relative">
                    <input :type="showLoginPassword ? 'text' : 'password'" x-model="loginForm.password" @input="validateLoginPassword()" required class="w-full px-4 py-3 pr-12 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="loginValidation.password.valid ? 'ring-green-500' : (loginForm.password.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="••••••••">
                    <button type="button" @click="showLoginPassword = !showLoginPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-all">
                        <i :class="showLoginPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                <div class="h-5 mt-1">
                    <div x-show="!loginValidation.password.valid && loginForm.password.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="loginValidation.password.message"></span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" @click="loginForm.remember = !loginForm.remember" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-spotify-green focus:ring-offset-2 focus:ring-offset-spotify-dark" :class="loginForm.remember ? 'bg-spotify-green' : 'bg-gray-600'">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="loginForm.remember ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                    <label class="text-sm text-gray-300 cursor-pointer" @click="loginForm.remember = !loginForm.remember">Beni Hatırla</label>
                </div>
                <button type="button" @click="showAuthModal = 'forgot'" class="text-sm text-gray-400 hover:text-spotify-green transition-all">
                    Şifremi Unuttum
                </button>
            </div>
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold rounded-full transition-all shadow-lg">
                Giriş Yap
            </button>
            <div class="text-center">
                <button type="button" @click="showAuthModal = 'register'" class="text-sm text-gray-400 hover:text-white transition-all">
                    Hesabınız yok mu? <span class="text-spotify-green font-semibold">Ücretsiz Başlayın</span>
                </button>
            </div>
        </form>

        <!-- Forgot Password Form -->
        <form x-show="showAuthModal === 'forgot'" @submit.prevent="handleForgotPassword()" class="space-y-4">
            <div class="text-center mb-4">
                <p class="text-gray-400 text-sm">Şifre sıfırlama linki e-posta adresinize gönderilecektir.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                <input type="email" x-model="forgotForm.email" @input="validateForgotEmail()" required class="w-full px-4 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="forgotValidation.email.valid ? 'ring-green-500' : (forgotForm.email.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="ornek@email.com">
                <div class="h-5 mt-1">
                    <div x-show="!forgotValidation.email.valid && forgotForm.email.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="forgotValidation.email.message"></span>
                    </div>
                    <div x-show="forgotValidation.email.valid" class="text-xs text-green-400 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i>
                        <span>Geçerli e-posta adresi</span>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold rounded-full transition-all shadow-lg">
                Sıfırlama Linki Gönder
            </button>
            <div class="text-center">
                <button type="button" @click="showAuthModal = 'login'" class="text-sm text-gray-400 hover:text-white transition-all">
                    <i class="fas fa-arrow-left mr-1"></i> Giriş Sayfasına Dön
                </button>
            </div>
        </form>

        <!-- Register Form -->
        <form x-show="showAuthModal === 'register'" @submit.prevent="handleRegister()" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Ad Soyad</label>
                <input type="text" x-model="registerForm.name" @input="validateName()" required class="w-full px-4 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="registerValidation.name.valid ? 'ring-green-500' : (registerForm.name.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="Adınız Soyadınız">
                <div class="h-5 mt-1">
                    <div x-show="!registerValidation.name.valid && registerForm.name.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="registerValidation.name.message"></span>
                    </div>
                    <div x-show="registerValidation.name.valid" class="text-xs text-green-400 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i>
                        <span>Geçerli ad soyad</span>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">E-posta</label>
                <input type="email" x-model="registerForm.email" @input="validateEmail()" required class="w-full px-4 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="registerValidation.email.valid ? 'ring-green-500' : (registerForm.email.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="ornek@email.com">
                <div class="h-5 mt-1">
                    <div x-show="!registerValidation.email.valid && registerForm.email.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="registerValidation.email.message"></span>
                    </div>
                    <div x-show="registerValidation.email.valid" class="text-xs text-green-400 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i>
                        <span>Geçerli e-posta adresi</span>
                    </div>
                </div>
            </div>

            <!-- Telefon (Tenant 1001 için zorunlu) -->
            <div x-show="tenantId === 1001">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Telefon
                    <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <div class="flex gap-2">
                        <!-- Country Code Selector -->
                        <div class="relative" x-data="{ countryOpen: false }">
                            <button type="button" @click="countryOpen = !countryOpen" class="px-3 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-spotify-green transition-all flex items-center gap-2 min-w-[100px]">
                                <span x-text="phoneCountry.flag">🇹🇷</span>
                                <span x-text="phoneCountry.code" class="text-sm">+90</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="countryOpen" @click.away="countryOpen = false" x-transition class="absolute top-full left-0 mt-2 w-64 bg-spotify-gray rounded-lg shadow-2xl overflow-hidden z-50 max-h-64 overflow-y-auto">
                                <template x-for="country in phoneCountries" :key="country.code">
                                    <button type="button" @click="selectCountry(country); countryOpen = false" class="w-full flex items-center gap-3 px-3 py-2 hover:bg-white/10 transition-all text-left">
                                        <span x-text="country.flag" class="text-xl"></span>
                                        <div class="flex-1">
                                            <div class="text-white text-sm" x-text="country.name"></div>
                                            <div class="text-gray-400 text-xs" x-text="country.code"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Phone Number Input -->
                        <input type="tel" x-model="registerForm.phone" @input="formatPhoneNumber()" :required="tenantId === 1001" class="flex-1 px-4 py-3 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="registerValidation.phone.valid ? 'ring-green-500' : (registerForm.phone.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" :placeholder="phoneCountry.placeholder" maxlength="20">
                    </div>
                    <!-- Validation message with fixed height -->
                    <div class="h-5 mt-1">
                        <div x-show="!registerValidation.phone.valid && registerForm.phone.length > 0" class="text-xs text-red-400 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <span x-text="registerValidation.phone.message"></span>
                        </div>
                        <div x-show="registerValidation.phone.valid" class="text-xs text-green-400 flex items-center gap-1">
                            <i class="fas fa-check-circle"></i>
                            <span>Geçerli telefon numarası</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Şifre -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Şifre</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" x-model="registerForm.password" @input="validatePassword()" required class="w-full px-4 py-3 pr-12 bg-spotify-gray text-white rounded-lg focus:outline-none focus:ring-2 transition-all" :class="registerValidation.password.valid ? 'ring-green-500' : (registerForm.password.length > 0 ? 'ring-red-500' : 'focus:ring-spotify-green')" placeholder="En az 8 karakter">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-all">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>

                <!-- Şifre Güvenlik Göstergesi -->
                <div x-show="registerForm.password.length > 0" class="mt-2">
                    <div class="flex gap-1 mb-1">
                        <div class="h-1 flex-1 rounded-full transition-all" :class="registerValidation.password.strength >= 1 ? 'bg-red-500' : 'bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-all" :class="registerValidation.password.strength >= 2 ? 'bg-orange-500' : 'bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-all" :class="registerValidation.password.strength >= 3 ? 'bg-yellow-500' : 'bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-all" :class="registerValidation.password.strength >= 4 ? 'bg-green-500' : 'bg-gray-600'"></div>
                    </div>
                    <div class="text-xs flex items-center gap-2">
                        <span :class="registerValidation.password.strength === 1 ? 'text-red-400' : registerValidation.password.strength === 2 ? 'text-orange-400' : registerValidation.password.strength === 3 ? 'text-yellow-400' : 'text-green-400'" x-text="registerValidation.password.strengthText"></span>
                    </div>
                    <ul class="mt-2 space-y-1 text-xs">
                        <li :class="registerValidation.password.checks.length ? 'text-green-400' : 'text-gray-500'" class="flex items-center gap-1">
                            <i :class="registerValidation.password.checks.length ? 'fas fa-check-circle' : 'far fa-circle'"></i>
                            En az 8 karakter
                        </li>
                        <li :class="registerValidation.password.checks.uppercase ? 'text-green-400' : 'text-gray-500'" class="flex items-center gap-1">
                            <i :class="registerValidation.password.checks.uppercase ? 'fas fa-check-circle' : 'far fa-circle'"></i>
                            Büyük harf (A-Z)
                        </li>
                        <li :class="registerValidation.password.checks.lowercase ? 'text-green-400' : 'text-gray-500'" class="flex items-center gap-1">
                            <i :class="registerValidation.password.checks.lowercase ? 'fas fa-check-circle' : 'far fa-circle'"></i>
                            Küçük harf (a-z)
                        </li>
                        <li :class="registerValidation.password.checks.number ? 'text-green-400' : 'text-gray-500'" class="flex items-center gap-1">
                            <i :class="registerValidation.password.checks.number ? 'fas fa-check-circle' : 'far fa-circle'"></i>
                            Rakam (0-9)
                        </li>
                    </ul>
                </div>
            </div>

            <button type="submit" :disabled="!isRegisterFormValid()" class="w-full py-3 bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold rounded-full transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                7 Gün Ücretsiz Başla
            </button>
            <div class="text-center">
                <button type="button" @click="showAuthModal = 'login'" class="text-sm text-gray-400 hover:text-white transition-all">
                    Zaten hesabınız var mı? <span class="text-spotify-green font-semibold">Giriş Yapın</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="fixed bottom-0 left-64 right-0 bg-spotify-dark border-t border-white/10 px-6 py-4 z-40">
    <div class="flex items-center justify-between">
        <!-- Now Playing -->
        <div class="flex items-center gap-3 w-1/4">
            <img :src="currentSong?.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=56&h=56&fit=crop'" class="w-14 h-14 rounded shadow-md">
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-white text-sm truncate" x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></div>
                <div class="text-xs text-gray-400 truncate" x-text="currentSong?.artist_title?.tr || ''"></div>
            </div>
            <button @click="toggleLike()" class="text-gray-400 hover:text-spotify-green transition-all">
                <i :class="isLiked ? 'fas fa-heart text-spotify-green' : 'far fa-heart'"></i>
            </button>
        </div>

        <!-- Player Controls -->
        <div class="flex-1 max-w-2xl px-8">
            <div class="flex items-center justify-center gap-4 mb-2">
                <button @click="shuffle = !shuffle" class="text-gray-400 hover:text-white transition-all" :class="{ 'text-spotify-green': shuffle }">
                    <i class="fas fa-random text-sm"></i>
                </button>
                <button @click="previousTrack()" class="text-gray-400 hover:text-white transition-all">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button @click="togglePlayPause()" class="w-10 h-10 bg-white rounded-full flex items-center justify-center hover:scale-105 transition-all shadow-lg">
                    <i :class="isPlaying ? 'fas fa-pause text-black' : 'fas fa-play text-black ml-0.5'"></i>
                </button>
                <button @click="nextTrack()" class="text-gray-400 hover:text-white transition-all">
                    <i class="fas fa-step-forward"></i>
                </button>
                <button @click="cycleRepeat()" class="text-gray-400 hover:text-white transition-all" :class="{ 'text-spotify-green': repeatMode !== 'off' }">
                    <i :class="repeatMode === 'one' ? 'fas fa-repeat-1 text-sm' : 'fas fa-redo text-sm'"></i>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400" x-text="formatTime(currentTime)">0:00</span>
                <div @click="seekTo($event)" class="flex-1 h-1 bg-gray-600 rounded-full overflow-hidden cursor-pointer relative group">
                    <div class="h-full bg-white rounded-full transition-all" :style="`width: ${progressPercent}%`">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
                <span class="text-xs text-gray-400" x-text="formatTime(duration)">0:00</span>
            </div>
        </div>

        <!-- Volume -->
        <div class="flex items-center gap-3 w-1/4 justify-end">
            <button @click="showQueue = !showQueue" class="text-gray-400 hover:text-white transition-all text-sm" :class="{'text-spotify-green': showQueue}">
                <i class="fas fa-list"></i>
            </button>
            <button @click="toggleMute()" class="text-gray-400 hover:text-white transition-all text-sm">
                <i :class="isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up'"></i>
            </button>
            <div @click="setVolume($event)" class="w-24 h-1 bg-gray-600 rounded-full overflow-hidden cursor-pointer relative group">
                <div class="h-full bg-white rounded-full transition-all" :style="`width: ${volume}%`">
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Queue Panel (Sağ Tarafta) -->
<div x-show="showQueue"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     class="fixed right-0 bottom-28 top-0 w-96 bg-spotify-dark border-l border-white/10 z-30 overflow-y-auto"
     style="display: none;">

    <div class="sticky top-0 bg-spotify-dark border-b border-white/10 px-6 py-4 flex items-center justify-between">
        <h3 class="text-xl font-bold text-white">Çalma Sırası</h3>
        <button @click="showQueue = false" class="text-gray-400 hover:text-white transition-all">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <div class="p-6">
        <!-- Şu An Çalan -->
        <div x-show="currentSong" class="mb-6">
            <div class="text-sm text-gray-400 font-semibold mb-2">ŞU AN ÇALIYOR</div>
            <div class="flex items-center gap-3 p-3 bg-spotify-gray rounded-lg">
                <img :src="currentSong?.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=56&h=56&fit=crop'" class="w-12 h-12 rounded shadow-md">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-white text-sm truncate" x-text="currentSong?.song_title?.tr || 'Şarkı seçilmedi'"></div>
                    <div class="text-xs text-gray-400 truncate" x-text="currentSong?.artist_title?.tr || ''"></div>
                </div>
                <div class="pulse-play">
                    <i class="fas fa-volume-up text-spotify-green"></i>
                </div>
            </div>
        </div>

        <!-- Sıradaki Şarkılar -->
        <div x-show="queue.length > 0">
            <div class="text-sm text-gray-400 font-semibold mb-2 flex items-center justify-between">
                <span>SIRADAKİ</span>
                <span x-text="`${queue.length} şarkı`" class="text-xs"></span>
            </div>
            <div class="space-y-1">
                <template x-for="(song, index) in queue" :key="song.song_id">
                    <div draggable="true"
                         @dragstart="dragStart(index, $event)"
                         @dragover.prevent="dragOver(index)"
                         @drop="drop(index)"
                         @dragend="dragEnd()"
                         @click="playSongFromQueue(index)"
                         class="flex items-center gap-3 p-3 rounded-lg hover:bg-spotify-gray transition-all cursor-move group"
                         :class="{'bg-spotify-gray/50': index === queueIndex, 'opacity-50': draggedIndex === index, 'border-t-2 border-spotify-green': dropTargetIndex === index}">
                        <div class="w-8 text-center cursor-grab active:cursor-grabbing">
                            <i class="fas fa-grip-vertical text-gray-600 text-sm group-hover:text-gray-400"></i>
                        </div>
                        <div class="w-8 text-center">
                            <span x-show="index !== queueIndex" class="text-gray-400 text-sm" x-text="index + 1"></span>
                            <i x-show="index === queueIndex" class="fas fa-play text-spotify-green text-xs"></i>
                        </div>
                        <img :src="song.album_cover || 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=40&h=40&fit=crop'" class="w-10 h-10 rounded shadow-md">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-white text-sm truncate" x-text="song.song_title?.tr || 'Untitled'"></div>
                            <div class="text-xs text-gray-400 truncate" x-text="song.artist_title?.tr || 'Unknown'"></div>
                        </div>
                        <button @click.stop="removeFromQueue(index)" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-white transition-all">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Boş Queue Durumu -->
        <div x-show="queue.length === 0" class="text-center py-12">
            <i class="fas fa-music text-4xl text-gray-600 mb-3"></i>
            <p class="text-gray-400 text-sm">Çalma sırası boş</p>
            <p class="text-gray-500 text-xs mt-1">Bir şarkı veya playlist çalmaya başlayın</p>
        </div>
    </div>
</div>

<script>
function muzibuApp() {
    return {
        isLoggedIn: false,
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

            // Check authentication status
            this.checkAuth();

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

        toggleLike() {
            this.isLiked = !this.isLiked;
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

            try {
                const response = await fetch(`/api/muzibu/songs/${song.song_id}/stream`);
                const data = await response.json();
                await this.loadAndPlaySong(data.stream_url);
            } catch (error) {
                console.error('Failed to load song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
            }
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

        async checkAuth() {
            try {
                const response = await fetch('/api/auth/me', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.authenticated) {
                    this.isLoggedIn = true;
                    console.log('User authenticated:', data.user);
                }
            } catch (error) {
                console.error('Auth check failed:', error);
            }
        },

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

                if (response.ok) {
                    this.isLoggedIn = true;
                    this.showAuthModal = null;
                    this.loginForm = { email: '', password: '', remember: false };
                    this.showToast('Başarıyla giriş yapıldı!', 'success');
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

                if (response.ok) {
                    this.isLoggedIn = true;
                    this.showAuthModal = null;
                    this.registerForm = { name: '', email: '', password: '', company: '' };
                    this.showToast('Hesabınız oluşturuldu! 7 günlük deneme başladı.', 'success');
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
                    this.showAuthModal = null;
                    this.showToast('Çıkış yapıldı', 'info');
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Still logout on frontend
                this.isLoggedIn = false;
            }
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.isDarkMode);
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

        validateEmail() {
            const email = this.registerForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
            } else if (email.includes('..')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresinde ardışık nokta olamaz';
            } else if (email.startsWith('.') || email.endsWith('.')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresi nokta ile başlayamaz veya bitemez';
            } else {
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
