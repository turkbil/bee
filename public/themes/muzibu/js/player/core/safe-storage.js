/**
 * Safe Storage Wrapper
 * Prevents "Access to storage is not allowed" errors in privacy-restricted contexts
 */

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

// Make globally accessible
window.safeStorage = safeStorage;
