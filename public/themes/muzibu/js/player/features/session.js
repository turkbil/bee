/**
 * Muzibu Session Manager
 * Handles session polling, device limit, session termination
 *
 * Dependencies: player-core.js (for this.* context)
 */

const MuzibuSession = {
    // State
    sessionPollInterval: null,
    _sessionTerminatedHandling: false,
    sessionCheckFailCount: 0,

    /**
     * 🔐 SESSION POLLING: Start polling for session validity (device limit check)
     * Polls /api/auth/check-session every 30 seconds
     */
    startSessionPolling() {
        // Clear any existing interval
        if (this.sessionPollInterval) {
            clearInterval(this.sessionPollInterval);
        }

        // 🔧 LOGIN SONRASI: Session DB'ye kaydedilmesi için 2 saniye bekle
        // Race condition önleme: Backend registerSession() işlemi tamamlansın
        setTimeout(() => {
            this.checkSessionValidity();
        }, 2000);

        // 🔧 PERFORMANS AYARI:
        // TEST: 5 saniye (5000ms) - hızlı geri bildirim
        // CANLI: 5 dakika (300000ms) - 10.000 kullanıcıda 33 req/s
        // @see https://ixtif.com/readme/2025/12/10/muzibu-session-auth-system/
        const SESSION_POLL_INTERVAL = 5000; // 🧪 TEST: 5 saniye | 🚀 CANLI: 300000 (5 dk)

        this.sessionPollInterval = setInterval(() => {
            this.checkSessionValidity();
        }, SESSION_POLL_INTERVAL);

        console.log(`🔐 Session polling started (${SESSION_POLL_INTERVAL/1000}s interval, initial check in 2s)`);
    },

    /**
     * 🔐 STOP SESSION POLLING: Clear the polling interval
     */
    stopSessionPolling() {
        if (this.sessionPollInterval) {
            clearInterval(this.sessionPollInterval);
            this.sessionPollInterval = null;
            console.log('🔐 Session polling stopped');
        }
    },

    /**
     * 🔐 CHECK SESSION: Verify session is still valid
     * Backend checks if session exists in DB (device limit enforcement)
     */
    async checkSessionValidity() {
        try {
            // 🔥 FIX: Sanctum stateful authentication için Referer header ZORUNLU!
            // EnsureFrontendRequestsAreStateful middleware Referer/Origin header'a bakıyor
            const response = await fetch('/api/auth/check-session', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Referer': window.location.origin + '/'  // Sanctum stateful için ZORUNLU
                },
                credentials: 'same-origin',
                referrerPolicy: 'strict-origin-when-cross-origin'  // Browser'ın Referer göndermesini sağla
            });

            const data = await response.json();

            // Session invalid - user was logged out
            if (!data.valid) {
                console.warn('⚠️ Session invalid:', data.reason);

                // Stop polling
                this.stopSessionPolling();

                // Handle based on reason
                if (data.reason === 'device_limit_exceeded') {
                    console.log('🚨 Device limit exceeded - showing modal');
                    this.handleDeviceLimitExceeded();
                } else if (data.reason === 'session_terminated') {
                    // 🔐 SESSION TERMINATED: Başka cihazdan giriş yapıldı (LIFO)
                    if (!this._sessionTerminatedHandling) {
                        console.log('🔐 Session terminated - another device logged in');
                        this.handleSessionTerminated(data.message);
                    }
                } else if (data.reason === 'not_authenticated') {
                    // Sayfa renderda auth vardı ama API'de yok
                    // Session sync sorunu - agresif logout YAPMA
                    console.log('🔐 Not authenticated - waiting for session sync');
                    this.isLoggedIn = false;
                    this.stopSessionPolling();
                } else {
                    // Silent logout (session expired veya diğer nedenler)
                    this.handleSilentLogout();
                }
            } else {
                // ✅ Session valid - reset fail counter
                this.sessionCheckFailCount = 0;
            }
        } catch (error) {
            console.error('Session check failed:', error);
            // Don't logout on network error - keep trying
        }
    },

    /**
     * 🔐 DEVICE LIMIT EXCEEDED: Show modal to select which device to terminate
     */
    handleDeviceLimitExceeded() {
        console.log('🔐 Device limit exceeded - checking terminable devices...');

        this.deviceLimitExceeded = true;
        this.stopCurrentPlayback();
        this.isPlaying = false;

        // Fetch devices and show modal if terminable devices exist
        this.fetchActiveDevices().then(() => {
            const terminableDevices = this.activeDevices.filter(d => !d.is_current);

            if (terminableDevices.length > 0) {
                console.log('🔐 Found', terminableDevices.length, 'terminable devices - showing modal');
                this.showDeviceSelectionModal = true;
            } else {
                console.log('🔐 No terminable devices - showing logout prompt');
                this.showToast('Cihaz limitine ulaştınız. Müzik dinlemek için bu cihazdan çıkış yapıp tekrar giriş yapabilirsiniz.', 'warning', 8000);
                this.deviceLimitExceeded = false;
            }
        });
    },

    /**
     * 🔐 SILENT LOGOUT: Logout without modal (session expired)
     */
    handleSilentLogout() {
        console.log('🔐 Session expired - silent logout');
        this.forceLogout();
    },

    /**
     * 🔐 SESSION TERMINATED: Başka cihazdan giriş yapıldı
     * HEMEN logout yap ve login'e yönlendir
     */
    handleSessionTerminated(message) {
        // Sonsuz döngü önleme
        if (this._sessionTerminatedHandling) {
            console.log('🔐 Session terminated already being handled, skipping...');
            return;
        }
        this._sessionTerminatedHandling = true;

        console.log('🔐 Session terminated - IMMEDIATE LOGOUT');

        // HER ŞEYİ DURDUR
        try {
            this.stopCurrentPlayback();
            this.isPlaying = false;
            this.isLoggedIn = false;
            this.stopSessionPolling();
            this.clearAllBrowserStorage();
        } catch(e) {}

        // API LOGOUT + HARD REDIRECT
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        console.log('🔐 Calling logout API...');

        fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(() => {
            console.log('🔐 Logout API success, redirecting to login...');
        })
        .catch((err) => {
            console.log('🔐 Logout API error (ignored):', err.message);
        })
        .finally(() => {
            // HARD REDIRECT - Livewire/SPA INTERCEPT EDEMEZ!
            console.log('🔐 HARD REDIRECT to login page NOW!');
            window.location.href = '/login?session_terminated=1';
        });
    },

    /**
     * 🔥 SESSION TERMINATED MODAL
     */
    showSessionTerminatedModal(message) {
        const defaultMessage = 'Başka bir cihazdan giriş yapıldı. Bu oturum sonlandırıldı.';
        const displayMessage = message || defaultMessage;

        const existingModal = document.getElementById('session-terminated-modal');
        if (existingModal) {
            existingModal.remove();
        }

        const modalHtml = `
            <div id="session-terminated-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm">
                <div class="bg-slate-900 border border-slate-700 rounded-2xl p-8 max-w-md mx-4 shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-orange-500/20 flex items-center justify-center">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Oturum Sonlandırıldı</h3>
                        <p class="text-slate-300 mb-6">${displayMessage}</p>
                        <button id="session-terminated-btn" class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-red-600 transition-all duration-200">
                            Tamam
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        document.getElementById('session-terminated-btn').addEventListener('click', () => {
            this.performFullLogout();
        });
    },

    /**
     * 🔥 TAM ÇIKIŞ - Form POST ile logout yap
     */
    async performFullLogout() {
        const btn = document.getElementById('session-terminated-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Çıkış yapılıyor...</span>';
        }

        console.log('🔐 Performing full logout via form POST...');

        this.clearAllBrowserStorage();
        this.clearCacheAPI();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';
        form.style.display = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                          document.querySelector('input[name="_token"]')?.value || '';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        form.appendChild(tokenInput);

        const redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect';
        redirectInput.value = '/login?session_terminated=1';
        form.appendChild(redirectInput);

        document.body.appendChild(form);
        form.submit();
    },

    /**
     * 🔥 TÜM COOKIE'LERİ TEMİZLE
     */
    clearAllCookies() {
        console.log('🍪 Clearing all cookies...');
        const cookies = document.cookie.split(';');

        for (let cookie of cookies) {
            const eqPos = cookie.indexOf('=');
            const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();

            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + window.location.hostname;
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=.' + window.location.hostname;
        }

        console.log('🍪 Cookies cleared');
    },

    /**
     * 🔥 CACHE API TEMİZLE
     */
    async clearCacheAPI() {
        if ('caches' in window) {
            try {
                const cacheNames = await caches.keys();
                await Promise.all(cacheNames.map(name => caches.delete(name)));
                console.log('🗄️ Cache API cleared');
            } catch (e) {
                console.log('🗄️ Cache API clear error:', e.message);
            }
        }
    },

    /**
     * 🔥 BROWSER STORAGE TEMİZLE
     */
    clearAllBrowserStorage() {
        console.log('🧹 Clearing browser storage...');

        try {
            localStorage.removeItem('muzibu_player_state');
            localStorage.removeItem('muzibu_queue');
            localStorage.removeItem('muzibu_favorites');
            localStorage.removeItem('muzibu_play_context');
            localStorage.removeItem('muzibu_volume');
        } catch (e) {
            console.log('🧹 localStorage clear error:', e.message);
        }

        try {
            sessionStorage.clear();
        } catch (e) {
            console.log('🧹 sessionStorage clear error:', e.message);
        }

        console.log('🧹 Browser storage cleared');
    },

    /**
     * 🔐 FORCE LOGOUT: Clear state and reload page
     */
    forceLogout() {
        this.isLoggedIn = false;
        this.currentUser = null;
        this.favorites = [];
        window.location.reload();
    }
};

// Export for use in player-core.js
window.MuzibuSession = MuzibuSession;
