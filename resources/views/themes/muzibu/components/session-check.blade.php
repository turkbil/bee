{{-- Session Check Component - DEVRE DIŞI (2025-12-22) --}}
{{-- SEBEP: session.js ile çakışma - login/logout döngüsü oluşturuyordu --}}
{{-- session.js zaten tüm session kontrolünü yapıyor (player entegrasyonlu) --}}
@if(false) {{-- DEVRE DIŞI: tenant() && tenant()->id == 1001 && auth()->check() && !request()->is('admin/*') --}}

<script>
// 🔥 FIX: Alpine.js init event'ini bekle (defer loading için)
document.addEventListener('alpine:init', () => {
    // Alpine.data ile component'i kaydet
    Alpine.data('sessionCheckComponent', () => ({
        sessionCheckInterval: null,
        sessionTerminatedModal: false,

        // init() Alpine.js tarafından otomatik çağrılır
        init() {
            console.log('🔐 Session check component initialized');
            this.startSessionCheck();
        },

        startSessionCheck() {
            console.log('🔐 Starting session check polling (5 seconds interval)');
            // Her 5 saniyede bir session kontrol et (TEST MODE)
            this.sessionCheckInterval = setInterval(() => {
                this.checkSession();
            }, 5000); // 5 saniye = test mode
        },

        async checkSession() {
            try {
                const response = await fetch('/api/session/check', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                // 🔥 FIX: 429 Too Many Requests durumunda logout YAPMA!
                if (response.status === 429) {
                    console.warn('⚠️ Session check rate limited, will retry');
                    return; // Hiçbir şey yapma, polling devam edecek
                }

                // Eğer 401 (Unauthorized) dönerse session kesilmiş demektir
                if (response.status === 401) {
                    console.log('🔐 Session terminated - showing modal');
                    this.onSessionTerminated();
                }
            } catch (error) {
                // Network hatalarını logla (401 değil)
                if (error.message !== 'Failed to fetch') {
                    console.warn('Session check network error:', error.message);
                }
            }
        },

        onSessionTerminated() {
            // Session check interval'ı durdur
            if (this.sessionCheckInterval) {
                clearInterval(this.sessionCheckInterval);
            }

            // Müziği durdur (player var mı kontrol et)
            try {
                if (window.Alpine && window.Alpine.store('player')) {
                    const player = window.Alpine.store('player');
                    // Player'da pause() fonksiyonu var
                    if (typeof player.pause === 'function') {
                        player.pause();
                    }
                }
            } catch (e) {
                console.warn('Could not pause player:', e.message);
            }

            // Modal göster
            this.sessionTerminatedModal = true;
        },

        async logout() {
            try {
                // Fresh CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                // 419 (Page Expired) veya herhangi bir hata -> Anasayfaya
                if (response.status === 419 || !response.ok) {
                    window.location.href = '/';
                } else {
                    // Başarılı logout -> Login sayfası
                    window.location.href = '/login';
                }
            } catch (error) {
                // Network hatası -> Anasayfaya
                console.error('Logout error:', error);
                window.location.href = '/';
            }
        }
    }));
});
</script>

<div x-data="sessionCheckComponent">
    <!-- Session Terminated Modal - Spotify-Like Modern Design -->
    <div
        x-show="sessionTerminatedModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/90 backdrop-blur-md z-50"
    ></div>

    <div
        x-show="sessionTerminatedModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="bg-muzibu-dark border border-white/10 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <!-- Icon & Title -->
            <div class="px-8 pt-8 pb-4 text-center">
                <div class="w-16 h-16 bg-muzibu-coral/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-3xl text-muzibu-coral"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Oturumunuz Sonlandırıldı</h3>
                <p class="text-gray-400 text-sm">Başka bir cihazdan giriş yapıldı</p>
            </div>

            <!-- Content -->
            <div class="px-8 py-4">
                <div class="bg-muzibu-gray/50 rounded-xl p-4 mb-4">
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Hesabınıza başka bir cihazdan giriş yapıldığı için bu cihazdaki oturumunuz otomatik olarak sonlandırıldı.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    <span>Müzik çalma durduruldu</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-6 bg-muzibu-black/50">
                <button
                    @click="logout()"
                    class="w-full px-6 py-3.5 bg-muzibu-coral hover:bg-muzibu-coral-light text-black font-bold rounded-full transition-all hover:scale-105"
                >
                    Yeniden Giriş Yap
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endif
