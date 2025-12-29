/**
 * Muzibu Favorites Manager
 * Handles favorite/like functionality for songs, albums, playlists, radios
 */

// Alpine.js Store (global reactive state)
document.addEventListener('alpine:init', () => {
    Alpine.store('favorites', {
        favorites: [],
        loading: false,

        /**
         * Toggle favorite status with API call
         * @param {string} type - 'song', 'album', 'playlist', 'radio'
         * @param {number} id - Entity ID
         */
        async toggle(type, id) {
            if (this.loading) return;

            const key = `${type}-${id}`;
            const wasLiked = this.favorites.includes(key);

            // Optimistic update
            if (wasLiked) {
                this.favorites = this.favorites.filter(f => f !== key);
            } else {
                this.favorites.push(key);
            }

            this.loading = true;

            try {
                const response = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        model_class: this.getModelClass(type),
                        model_id: id
                    })
                });

                // 401 Unauthorized → Guest kullanıcı, login'e yönlendir
                if (response.status === 401) {
                    this.loading = false;
                    // Revert optimistic update
                    if (wasLiked) {
                        this.favorites.push(key);
                    } else {
                        this.favorites = this.favorites.filter(f => f !== key);
                    }
                    // Pending favorite kaydet ve login'e yönlendir
                    if (window.savePendingFavorite) {
                        await window.savePendingFavorite(this.getModelClass(type), id, window.location.href);
                    } else {
                        // Fallback: direkt login'e yönlendir
                        window.location.href = '/login';
                    }
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    // Update based on API response
                    if (data.data.is_favorited && !this.favorites.includes(key)) {
                        this.favorites.push(key);
                    } else if (!data.data.is_favorited && this.favorites.includes(key)) {
                        this.favorites = this.favorites.filter(f => f !== key);
                    }

                    // 🧹 Clear SPA cache for dynamic pages (favorites page will show fresh data)
                    if (window.MuzibuSpaRouter?.clearDynamicCache) {
                        window.MuzibuSpaRouter.clearDynamicCache();
                    }

                    // Show toast
                    if (window.Alpine?.store('toast')?.show) {
                        window.Alpine.store('toast').show(
                            data.data.is_favorited ? 'Favorilere eklendi' : 'Favorilerden kaldırıldı',
                            data.data.is_favorited ? 'success' : 'info'
                        );
                    }
                } else {
                    // Revert on failure
                    if (wasLiked) {
                        this.favorites.push(key);
                    } else {
                        this.favorites = this.favorites.filter(f => f !== key);
                    }
                    console.warn('Favorite toggle failed:', data.message);
                }
            } catch (error) {
                // Revert on error
                if (wasLiked) {
                    this.favorites.push(key);
                } else {
                    this.favorites = this.favorites.filter(f => f !== key);
                }
                console.error('Favorite error:', error);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Check if item is favorited
         * @param {string} type - 'song', 'album', 'playlist', 'radio'
         * @param {number} id - Entity ID
         * @returns {boolean}
         */
        isFavorite(type, id) {
            return this.favorites.includes(`${type}-${id}`);
        },

        /**
         * Toggle favorite (alias for backward compatibility)
         */
        toggleFavorite(type, id) {
            return this.toggle(type, id);
        },

        /**
         * Check if song is liked (alias for player bar compatibility)
         * @param {number} songId - Song ID
         * @returns {boolean}
         */
        isLiked(songId) {
            return this.favorites.includes(`song-${songId}`);
        },

        /**
         * Toggle song like (alias for blade templates compatibility)
         * @param {number} songId - Song ID
         */
        toggleLike(songId) {
            return this.toggle('song', songId);
        },

        /**
         * Get model class for API
         * @param {string} type - Entity type
         * @returns {string} - Full model class name
         */
        getModelClass(type) {
            const modelMap = {
                'song': 'Modules\\Muzibu\\app\\Models\\Song',
                'album': 'Modules\\Muzibu\\app\\Models\\Album',
                'playlist': 'Modules\\Muzibu\\app\\Models\\Playlist',
                'radio': 'Modules\\Muzibu\\app\\Models\\Radio',
                'artist': 'Modules\\Muzibu\\app\\Models\\Artist',
                'genre': 'Modules\\Muzibu\\app\\Models\\Genre',
                'sector': 'Modules\\Muzibu\\app\\Models\\Sector'
            };
            return modelMap[type] || '';
        },

        /**
         * Load favorites from server (called on init)
         */
        async loadFavorites() {
            try {
                const response = await fetch('/api/favorites/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && Array.isArray(data.data)) {
                        this.favorites = data.data;
                    }
                }
            } catch (error) {
                console.error('[Favorites] ❌ Failed to load favorites:', error);
            }
        }
    });
});

// Legacy function for backward compatibility
function muzibuFavorites() {
    return {
        favorites: [],

        toggleFavorite(type, id) {
            const key = `${type}-${id}`;
            if (this.favorites.includes(key)) {
                this.favorites = this.favorites.filter(f => f !== key);
            } else {
                this.favorites.push(key);
            }
        },

        isFavorite(type, id) {
            return this.favorites.includes(`${type}-${id}`);
        },

        isLiked(songId) {
            return this.favorites.includes(`song-${songId}`);
        },

        toggleLike(songId) {
            this.toggleFavorite('song', songId);
        }
    };
}

// Make globally accessible (legacy)
window.muzibuFavorites = muzibuFavorites;

// 🎯 AUTO-LOAD FAVORITES: Initialize favorites list when Alpine is ready
document.addEventListener('alpine:initialized', () => {
    const favoritesStore = Alpine.store('favorites');

    // Only load if user is authenticated
    if (window.muzibuPlayerConfig?.isLoggedIn) {
        favoritesStore.loadFavorites();
    }
});
