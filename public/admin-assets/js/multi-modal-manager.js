/**
 * Multi Modal Manager - v2.0
 * Modal çakışmalarını önler ve temizlik yapar
 * Claude Code tarafından oluşturuldu
 */

class MultiModalManager {
    constructor() {
        this.activeModals = new Set();
        this.backdrop = null;
        this.init();
        
        console.log('🔧 Multi Modal Manager initialized');
    }

    init() {
        // Global modal event listeners
        document.addEventListener('DOMContentLoaded', () => {
            this.attachModalEvents();
        });

        // Escape key handler
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModals.size > 0) {
                this.closeTopModal();
            }
        });
    }

    attachModalEvents() {
        // Bootstrap modal events
        document.addEventListener('show.bs.modal', (e) => {
            this.onModalShow(e.target);
        });

        document.addEventListener('hidden.bs.modal', (e) => {
            this.onModalHide(e.target);
        });

        // Custom modal events (AI translation vs.)
        document.addEventListener('modal:open', (e) => {
            this.onModalShow(e.detail.modal);
        });

        document.addEventListener('modal:close', (e) => {
            this.onModalHide(e.detail.modal);
        });
    }

    onModalShow(modal) {
        const modalId = modal.id || `modal-${Date.now()}`;
        this.activeModals.add(modalId);
        
        console.log(`📂 Modal opened: ${modalId} (Active: ${this.activeModals.size})`);

        // Z-index management
        this.manageZIndex(modal);
        
        // Backdrop management
        this.manageBackdrop();
    }

    onModalHide(modal) {
        const modalId = modal.id || `modal-${Date.now()}`;
        this.activeModals.delete(modalId);
        
        console.log(`📂 Modal closed: ${modalId} (Active: ${this.activeModals.size})`);

        // Cleanup if no modals
        if (this.activeModals.size === 0) {
            this.cleanup();
        }
    }

    manageZIndex(modal) {
        const baseZIndex = 1050;
        const zIndex = baseZIndex + (this.activeModals.size * 10);
        
        modal.style.zIndex = zIndex;
        
        // Backdrop z-index
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.zIndex = zIndex - 1;
        }
    }

    manageBackdrop() {
        // Remove duplicate backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 1) {
            for (let i = 0; i < backdrops.length - 1; i++) {
                backdrops[i].remove();
            }
        }
    }

    closeTopModal() {
        const topModal = document.querySelector('.modal.show:last-of-type');
        if (topModal) {
            // Bootstrap güvenli kontrol
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const bsModal = bootstrap.Modal.getInstance(topModal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    // Custom modal close
                    topModal.style.display = 'none';
                }
            } else {
                // Bootstrap yüklenmemişse manuel kapat
                topModal.style.display = 'none';
                topModal.classList.remove('show');
            }
        }
    }

    closeAllModals() {
        console.log('🧹 Closing all modals...');

        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            // Bootstrap güvenli kontrol
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }
            } else {
                // Bootstrap yüklenmemişse manuel kapat
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
        });

        this.cleanup();
    }

    cleanup() {
        console.log('🧹 Modal cleanup started...');

        // Ultra güçlü backdrop temizleme
        const backdrops = document.querySelectorAll('.modal-backdrop, [class*="backdrop"], [id*="backdrop"]');
        backdrops.forEach(backdrop => {
            console.log('🗑️ Backdrop removing:', backdrop.className);
            backdrop.remove();
        });

        // Body state reset
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.style.marginRight = '';

        // Modal containers cleanup
        const openModals = document.querySelectorAll('.modal.show, .modal[style*="display: block"]');
        openModals.forEach(modal => {
            if (!modal.classList.contains('show')) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            }
        });

        // Clear active modals
        this.activeModals.clear();

        console.log('✅ Modal cleanup completed');
    }

    // Public methods
    getActiveModals() {
        return Array.from(this.activeModals);
    }

    hasActiveModals() {
        return this.activeModals.size > 0;
    }
}

// Global instance
window.MultiModalManager = new MultiModalManager();

console.log('✅ Multi Modal Manager loaded');