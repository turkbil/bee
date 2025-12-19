{{--
    PWA Service Worker Registration Component
    Auto-registers Service Worker for all themes
    Usage: <x-pwa-registration />
--}}
<script>
    // Register Service Worker for PWA installability
    if ('serviceWorker' in navigator && document.readyState === 'complete') {
        try {
            navigator.serviceWorker.register('/sw.js')
                .catch((error) => {
                    // Suppress error from console (likely ad blocker)
                    if (error.name !== 'InvalidStateError') {
                        console.error('[PWA] SW registration failed:', error);
                    }
                });
        } catch (e) {
            // Suppress InvalidStateError silently
        }
    } else if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            try {
                navigator.serviceWorker.register('/sw.js')
                    .catch((error) => {
                        if (error.name !== 'InvalidStateError') {
                            console.error('[PWA] SW registration failed:', error);
                        }
                    });
            } catch (e) {
                // Suppress InvalidStateError silently
            }
        });
    }
</script>
