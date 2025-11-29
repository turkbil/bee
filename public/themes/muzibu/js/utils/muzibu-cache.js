/**
 * Muzibu Cache Management
 * Handles all cache clearing operations
 */

function muzibuCache() {
    return {
        async clearAll() {
            console.log('🧹 Tüm cache temizleniyor...');

            // 1. Clear localStorage
            try {
                localStorage.clear();
                console.log('✅ LocalStorage temizlendi');
            } catch (e) {
                console.warn('⚠️ LocalStorage temizlenemedi:', e);
            }

            // 2. Clear sessionStorage
            try {
                sessionStorage.clear();
                console.log('✅ SessionStorage temizlendi');
            } catch (e) {
                console.warn('⚠️ SessionStorage temizlenemedi:', e);
            }

            // 3. Clear Cache API (modern browsers)
            if ('caches' in window) {
                try {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    console.log('✅ Cache API temizlendi');
                } catch (e) {
                    console.warn('⚠️ Cache API temizlenemedi:', e);
                }
            }

            // 4. Clear Service Worker cache
            if ('serviceWorker' in navigator) {
                try {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    for (let registration of registrations) {
                        await registration.unregister();
                    }
                    console.log('✅ Service Worker temizlendi');
                } catch (e) {
                    console.warn('⚠️ Service Worker temizlenemedi:', e);
                }
            }

            // 5. Show notification (using Alpine store)
            const player = Alpine.store('player');
            if (player && player.showToast) {
                player.showToast('Tüm cache temizlendi! Sayfa yenileniyor...', 'success');
            }

            // 6. Hard reload with cache bypass
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
}
