/**
 * Tab State Manager
 * SOLID: Single Responsibility Principle - Only manages tab state
 * SOLID: Dependency Inversion Principle - Depends on abstractions (TabPersistence)
 */
class TabStateManager extends TabStateInterface {
    constructor(persistence, moduleContext = 'default') {
        super();
        this.persistence = persistence;
        this.moduleContext = moduleContext;
        this.currentTab = null;
        this.observers = [];
        
        console.log(`🎯 TabStateManager initialized for: ${moduleContext}`);
    }
    
    /**
     * Get current active tab
     * @returns {string}
     */
    getCurrentTab() {
        if (!this.currentTab) {
            this.currentTab = this.persistence.load();
        }
        return this.currentTab;
    }
    
    /**
     * Set active tab with validation
     * @param {string} tabId
     */
    setCurrentTab(tabId) {
        if (!this.isValidTab(tabId)) {
            console.warn(`Invalid tab ID: ${tabId}`);
            return false;
        }
        
        const previousTab = this.currentTab;
        this.currentTab = tabId;
        
        // Notify observers (Observer Pattern)
        this.notifyObservers(previousTab, tabId);
        
        console.log(`🔄 Tab changed: ${previousTab} → ${tabId}`);
        return true;
    }
    
    /**
     * Persist current tab
     * @param {string} tabId
     */
    persistTab(tabId = null) {
        const targetTab = tabId || this.currentTab;
        if (targetTab) {
            this.persistence.save(targetTab);
        }
    }
    
    /**
     * Restore tab from persistence
     * @returns {string}
     */
    restoreTab() {
        const restoredTab = this.persistence.load();
        this.currentTab = restoredTab;
        console.log(`🔄 Tab restored: ${restoredTab}`);
        return restoredTab;
    }
    
    /**
     * Validate tab ID format
     * @param {string} tabId
     * @returns {boolean}
     */
    isValidTab(tabId) {
        return typeof tabId === 'string' && 
               tabId.startsWith('tabs-') && 
               !isNaN(parseInt(tabId.replace('tabs-', '')));
    }
    
    /**
     * Add observer for tab changes
     * @param {Function} callback
     */
    addObserver(callback) {
        if (typeof callback === 'function') {
            this.observers.push(callback);
        }
    }
    
    /**
     * Notify all observers of tab change
     * @param {string} previousTab
     * @param {string} newTab
     */
    notifyObservers(previousTab, newTab) {
        this.observers.forEach(callback => {
            try {
                callback(previousTab, newTab, this.moduleContext);
            } catch (error) {
                console.error('Observer callback error:', error);
            }
        });
    }
    
    /**
     * Get tab statistics
     * @returns {Object}
     */
    getStats() {
        return {
            currentTab: this.currentTab,
            moduleContext: this.moduleContext,
            persistenceKey: this.persistence.storageKey,
            observerCount: this.observers.length,
            hasPersistentData: this.persistence.exists()
        };
    }
}

// Export for module usage
if (typeof window !== 'undefined') {
    window.TabStateManager = TabStateManager;
}