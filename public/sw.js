/**
 * Service Worker - PWA Support
 *
 * Minimal implementation for PWA installability
 * - Enables "Install App" button in browser
 * - Basic offline support
 * - Cache strategy for assets
 *
 * @version 1.0.0
 * @date 2025-10-29
 */

const CACHE_VERSION = 'v1.0.0';
const CACHE_NAME = `pwa-cache-${CACHE_VERSION}`;

// Assets to cache (minimal - only critical)
const ASSETS_TO_CACHE = [
    '/',
    '/manifest.json'
    // '/favicon.ico' - Removed: File might be missing or blocked
];

/**
 * Install Event - Cache critical assets
 */
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Caching critical assets');
                return cache.addAll(ASSETS_TO_CACHE);
            })
            .then(() => self.skipWaiting()) // Activate immediately
    );
});

/**
 * Activate Event - Clean old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName !== CACHE_NAME)
                    .map((cacheName) => {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    })
            );
        }).then(() => self.clients.claim()) // Take control immediately
    );
});

/**
 * Fetch Event - Network first, cache fallback
 *
 * Strategy: Network First
 * - Try network request first
 * - If network fails, serve from cache
 * - Update cache with fresh response
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

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Clone response (can only be consumed once)
                const responseToCache = response.clone();

                // Update cache with fresh response
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });

                return response;
            })
            .catch(() => {
                // Network failed, try cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        console.log('[Service Worker] Serving from cache:', event.request.url);
                        return cachedResponse;
                    }

                    // No cache, return offline page (optional)
                    return new Response('Offline - Network unavailable', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({
                            'Content-Type': 'text/plain'
                        })
                    });
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
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => caches.delete(cacheName))
                );
            })
        );
    }
});

console.log('[Service Worker] Loaded successfully');
