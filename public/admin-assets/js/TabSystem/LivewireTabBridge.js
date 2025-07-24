/**
 * Livewire Tab Bridge
 * SOLID: Single Responsibility Principle - Only handles Livewire integration
 * SOLID: Open/Closed Principle - Extensible for different Livewire events
 */
class LivewireTabBridge {
    constructor(tabStateManager) {
        this.tabStateManager = tabStateManager;
        this.initialized = false;
        this.hooks = new Map();
        
        console.log('🔗 LivewireTabBridge initialized');
    }
    
    /**
     * Initialize Livewire hooks
     */
    initialize() {
        if (this.initialized || typeof Livewire === 'undefined') {
            return false;
        }
        
        this.setupPreSubmitHook();
        this.setupPostProcessHook();
        this.initialized = true;
        
        console.log('✅ Livewire hooks attached');
        return true;
    }
    
    /**
     * Setup pre-submit hook to capture tab state
     */
    setupPreSubmitHook() {
        const hook = () => {
            const currentTab = this.tabStateManager.getCurrentTab();
            this.tabStateManager.persistTab(currentTab);
            
            console.log(`📤 Pre-submit: Tab ${currentTab} persisted`);
        };
        
        Livewire.hook('message.sent', hook);
        this.hooks.set('message.sent', hook);
    }
    
    /**
     * Setup post-process hook to restore tab state
     */
    setupPostProcessHook() {
        const hook = () => {
            // Short delay to ensure DOM is ready
            setTimeout(() => {
                const targetTab = this.tabStateManager.getCurrentTab();
                this.restoreTabInDOM(targetTab);
                
                console.log(`📥 Post-process: Tab ${targetTab} restored`);
            }, 10);
        };
        
        Livewire.hook('message.processed', hook);
        this.hooks.set('message.processed', hook);
    }
    
    /**
     * Restore tab in DOM
     * @param {string} tabId
     */
    restoreTabInDOM(tabId) {
        try {
            // Deactivate all tabs
            this.deactivateAllTabs();
            
            // Activate target tab
            const navLink = document.querySelector(`.nav-link[href="#${tabId}"]`);
            const tabPane = document.getElementById(tabId);
            
            if (navLink && tabPane) {
                navLink.classList.add('active');
                tabPane.classList.add('active', 'show');
                
                console.log(`✅ DOM restored: ${tabId}`);
                return true;
            } else {
                console.warn(`❌ Tab elements not found: ${tabId}`);
                return false;
            }
        } catch (error) {
            console.error('Tab restore error:', error);
            return false;
        }
    }
    
    /**
     * Deactivate all tabs in DOM
     */
    deactivateAllTabs() {
        document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(link => {
            link.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active', 'show');
        });
    }
    
    /**
     * Add custom Livewire hook
     * @param {string} eventName
     * @param {Function} callback
     */
    addCustomHook(eventName, callback) {
        if (typeof Livewire !== 'undefined' && typeof callback === 'function') {
            Livewire.hook(eventName, callback);
            this.hooks.set(eventName, callback);
            
            console.log(`🎣 Custom hook added: ${eventName}`);
        }
    }
    
    /**
     * Remove hook
     * @param {string} eventName
     */
    removeHook(eventName) {
        // Livewire doesn't provide hook removal, but we can track them
        this.hooks.delete(eventName);
    }
    
    /**
     * Get bridge statistics
     * @returns {Object}
     */
    getStats() {
        return {
            initialized: this.initialized,
            livewireAvailable: typeof Livewire !== 'undefined',
            hookCount: this.hooks.size,
            hooks: Array.from(this.hooks.keys())
        };
    }
}

// Export for module usage
if (typeof window !== 'undefined') {
    window.LivewireTabBridge = LivewireTabBridge;
}