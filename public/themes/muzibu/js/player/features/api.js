/**
 * Muzibu API Helper
 * Handles authenticated fetch with 401 detection
 *
 * Dependencies: session.js (for handleSessionTerminated)
 */

// 🛡️ GUARD: Prevent redeclaration on SPA navigation
if (typeof MuzibuApi !== 'undefined') {
    console.log('⚠️ MuzibuApi already loaded, skipping...');
} else {

const MuzibuApi = {
    /**
     * 🔐 AUTHENTICATED FETCH: Tüm API çağrılarında 401 kontrolü yapar
     * 401 alırsa kullanıcıyı logout eder
     *
     * @param {string} url - API URL
     * @param {object} options - fetch options
     * @returns {Response|null} - Response or null if 401
     */
    async authenticatedFetch(url, options = {}) {
        const response = await fetch(url, options);

        // 🔴 401 Unauthorized = Session terminated, LOGOUT!
        if (response.status === 401) {
            try {
                const data = await response.json();
                if (data.force_logout || data.error === 'session_terminated') {
                    console.error('🔐 401 UNAUTHORIZED - Session terminated, forcing logout!');
                    // Call session handler from player context
                    if (this.handleSessionTerminated) {
                        this.handleSessionTerminated(data.message || 'Oturumunuz sonlandırıldı.');
                    } else if (window.MuzibuSession && window.MuzibuSession.handleSessionTerminated) {
                        window.MuzibuSession.handleSessionTerminated.call(this, data.message || 'Oturumunuz sonlandırıldı.');
                    }
                    return null;
                }
            } catch (e) {
                // JSON parse hatası olsa bile 401 = logout
                console.error('🔐 401 UNAUTHORIZED - Forcing logout!');
                if (this.handleSessionTerminated) {
                    this.handleSessionTerminated('Oturumunuz sonlandırıldı.');
                }
                return null;
            }
        }

        // 🔴 403 Device Limit = Show device selection modal
        if (response.status === 403) {
            try {
                const data = await response.clone().json();
                if (data.error === 'device_limit_exceeded' && data.show_device_modal) {
                    console.error('🔐 403 DEVICE LIMIT - Showing device selection modal');
                    if (this.handleDeviceLimitExceeded) {
                        this.handleDeviceLimitExceeded();
                    }
                    return null;
                }
            } catch (e) {
                // Continue with normal response
            }
        }

        return response;
    },

    /**
     * GET request with authentication
     * 🔥 FIX: Sanctum stateful auth için Referer header eklendi
     */
    async get(url, options = {}) {
        return this.authenticatedFetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Referer': window.location.origin + '/',  // Sanctum stateful için ZORUNLU
                ...options.headers
            },
            credentials: 'same-origin',
            referrerPolicy: 'strict-origin-when-cross-origin',
            ...options
        });
    },

    /**
     * POST request with authentication
     * 🔥 FIX: Sanctum stateful auth için Referer header eklendi
     */
    async post(url, data = {}, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        return this.authenticatedFetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Referer': window.location.origin + '/',  // Sanctum stateful için ZORUNLU
                ...options.headers
            },
            credentials: 'same-origin',
            referrerPolicy: 'strict-origin-when-cross-origin',
            body: JSON.stringify(data),
            ...options
        });
    }
};

// Export for use in player-core.js
window.MuzibuApi = MuzibuApi;

} // END GUARD
