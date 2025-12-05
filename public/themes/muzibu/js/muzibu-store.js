/**
 * Muzibu Alpine.js Global Store
 * Modüller arası paylaşılan state ve fonksiyonlar
 */

document.addEventListener('alpine:init', () => {
    // Modern Toast Store - Minimal & Professional
    Alpine.store('toast', {
        show(message, type = 'info') {
            const toast = document.createElement('div');

            // Icon mapping
            const icons = {
                success: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };

            // Color mapping
            const colors = {
                success: 'text-emerald-400',
                error: 'text-red-400',
                warning: 'text-amber-400',
                info: 'text-blue-400'
            };

            toast.className = 'fixed bottom-24 right-6 z-[9999] transition-all duration-300 ease-out transform translate-x-0';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';

            toast.innerHTML = `
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border border-white/10"
                     style="background: rgba(24, 24, 27, 0.95); backdrop-filter: blur(12px);">
                    <div class="${colors[type] || colors.info}">
                        ${icons[type] || icons.info}
                    </div>
                    <span class="text-white text-sm font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Slide in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Slide out
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(400px)';
            }, 3000);

            // Remove
            setTimeout(() => toast.remove(), 3500);
        }
    });

    // Player State Store
    Alpine.store('player', {
        isPlaying: false,
        currentSong: null,
        isLoading: false, // SPA loading state için
        showToast(message, type) {
            Alpine.store('toast').show(message, type);
        }
    });

    // 🚀 Router Store (SPA Navigation)
    Alpine.store('router', {
        currentRoute: '/',
        isLoading: false,

        navigateTo(url) {
            if (window.muzibuRouter) {
                window.muzibuRouter.navigateTo(url);
            }
        },

        clearCache() {
            if (window.muzibuRouter) {
                window.muzibuRouter.clearCache();
            }
        }
    });
});
