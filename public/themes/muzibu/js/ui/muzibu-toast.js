/**
 * Muzibu Toast Notification System
 * Lightweight toast notifications using Alpine.js store
 */

// Toast is handled by Alpine.store('toast')
// Usage: Alpine.store('toast').show('Message', 'success|error|warning|info')

// Helper function for non-Alpine contexts
window.showToast = function(message, type = 'info') {
    if (window.Alpine && Alpine.store('toast')) {
        Alpine.store('toast').show(message, type);
    } else {
        console.warn('Alpine.js not loaded, showing console message:', message);
    }
};
