/**
 * Service Worker - PWA Support
 *
 * Minimal implementation for PWA installability
 * - Enables "Install App" button in browser
 * - Basic offline support
 * - Cache strategy for assets
 *
 * @version 1.0.2
 * @date 2025-12-10
 */

const CACHE_VERSION = 'v1.0.5'; // Fixed: AJAX pagination bypass
const CACHE_NAME = `pwa-cache-${CACHE_VERSION}`;

// Assets to cache (minimal - only critical)
const ASSETS_TO_CACHE = [
    '/',
    '/manifest.json'
];

/**
 * Install Event - Cache critical assets
 */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                return cache.addAll(ASSETS_TO_CACHE);
            })
            .catch((error) => {
                console.warn('[PWA] Cache failed, continuing anyway:', error);
            })
            .then(() => self.skipWaiting())
    );
});

/**
 * Activate Event - Clean old caches
 */
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName !== CACHE_NAME)
                        .map((cacheName) => caches.delete(cacheName))
                );
            })
            .catch(() => {
                // Silently ignore cache cleanup errors
            })
            .then(() => self.clients.claim())
    );
});

/**
 * Fetch Event - Network first, cache fallback
 */
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip external requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // 🎵 NEVER cache HLS/API streaming requests (they must be fresh!)
    // 🛒 NEVER cache AJAX pagination requests (they must be fresh!)
    const url = event.request.url;
    const shouldNotCache =
        url.includes('/stream/') ||
        url.includes('/api/muzibu/songs/') ||
        url.includes('.m3u8') ||
        url.includes('.ts') ||
        url.includes('/key') ||
        (url.includes('/shop') && url.includes('page=')) || // Shop pagination
        url.includes('?page=') || // Generic pagination
        event.request.headers.get('X-Requested-With') === 'XMLHttpRequest'; // All AJAX requests

    if (shouldNotCache) {
        // Network only - bypass cache completely, but still need Promise
        event.respondWith(
            fetch(event.request)
                .then(response => response)
                .catch(error => {
                    console.error('[SW] HLS fetch error:', error);
                    throw error;
                })
        );
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Clone response before caching
                const responseToCache = response.clone();

                // Cache in background (don't wait, silently ignore errors)
                caches.open(CACHE_NAME)
                    .then((cache) => {
                        cache.put(event.request, responseToCache).catch(() => {});
                    })
                    .catch(() => {});

                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(event.request)
                    .then((cachedResponse) => {
                        return cachedResponse || new Response('Offline', { status: 503 });
                    })
                    .catch(() => {
                        return new Response('Offline', { status: 503 });
                    });
            })
    );
});

/**
 * Message Event - Handle commands from main thread
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys()
                .then((cacheNames) => {
                    return Promise.all(
                        cacheNames.map((cacheName) => caches.delete(cacheName))
                    );
                })
                .catch(() => {})
        );
    }
});
