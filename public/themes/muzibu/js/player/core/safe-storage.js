/**
 * Safe Storage Wrapper
 * Prevents "Access to storage is not allowed" errors in privacy-restricted contexts
 */

// Guard against duplicate loading in SPA navigation
if (typeof window.safeStorage !== 'undefined') {
    console.log('⚠️ safeStorage already loaded, skipping...');
} else {

window.safeStorage = {
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

} // End of else block - SPA guard
