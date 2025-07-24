/**
 * Tab State Management Interface
 * SOLID: Interface Segregation Principle
 */
class TabStateInterface {
    /**
     * Get current active tab
     * @returns {string}
     */
    getCurrentTab() {
        throw new Error('getCurrentTab must be implemented');
    }
    
    /**
     * Set active tab
     * @param {string} tabId
     */
    setCurrentTab(tabId) {
        throw new Error('setCurrentTab must be implemented');
    }
    
    /**
     * Persist tab state
     * @param {string} tabId
     */
    persistTab(tabId) {
        throw new Error('persistTab must be implemented');
    }
    
    /**
     * Restore tab state
     * @returns {string}
     */
    restoreTab() {
        throw new Error('restoreTab must be implemented');
    }
}

// Export for module usage
if (typeof window !== 'undefined') {
    window.TabStateInterface = TabStateInterface;
}