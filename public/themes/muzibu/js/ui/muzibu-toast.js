/**
 * Muzibu Toast Notification System
 * Lightweight toast notifications using Alpine.js store
 */

// Alpine Store for Toast
document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        visible: false,
        type: 'info',
        message: '',
        timer: null,

        show(message, type = 'info') {
            // Önceki toast varsa temizle
            if (this.timer) {
                clearTimeout(this.timer);
            }

            this.message = message;
            this.type = type;
            this.visible = true;

            console.log('🔔 Toast shown:', message, type);

            // 3 saniye sonra otomatik kapat
            this.timer = setTimeout(() => {
                this.hide();
            }, 3000);
        },

        hide() {
            this.visible = false;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
        }
    });
});

// Helper function for non-Alpine contexts
window.showToast = function(message, type = 'info') {
    if (window.Alpine && Alpine.store('toast')) {
        Alpine.store('toast').show(message, type);
    } else {
        console.warn('Alpine.js not loaded, showing console message:', message);
    }
};
