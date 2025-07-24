/**
 * Tab Persistence Management
 * SOLID: Single Responsibility Principle - Only handles localStorage operations
 */
class TabPersistence {
    constructor(storageKey) {
        this.storageKey = storageKey;
        this.defaultTab = 'tabs-1';
    }
    
    /**
     * Save tab to localStorage
     * @param {string} tabId
     */
    save(tabId) {
        try {
            localStorage.setItem(this.storageKey, tabId);
            console.log(`💾 Tab persisted: ${tabId} → ${this.storageKey}`);
        } catch (error) {
            console.error('Tab persistence error:', error);
        }
    }
    
    /**
     * Load tab from localStorage
     * @returns {string}
     */
    load() {
        try {
            const savedTab = localStorage.getItem(this.storageKey);
            return savedTab || this.defaultTab;
        } catch (error) {
            console.error('Tab load error:', error);
            return this.defaultTab;
        }
    }
    
    /**
     * Clear persisted tab
     */
    clear() {
        try {
            localStorage.removeItem(this.storageKey);
        } catch (error) {
            console.error('Tab clear error:', error);
        }
    }
    
    /**
     * Check if tab exists in localStorage
     * @returns {boolean}
     */
    exists() {
        return localStorage.getItem(this.storageKey) !== null;
    }
}

// Export for module usage
if (typeof window !== 'undefined') {
    window.TabPersistence = TabPersistence;
}