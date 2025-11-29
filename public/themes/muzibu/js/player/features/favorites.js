/**
 * Muzibu Favorites Manager
 * Handles favorite/like functionality for songs, albums, playlists
 */

function muzibuFavorites() {
    return {
        favorites: [],

        /**
         * Toggle favorite status
         * @param {string} type - 'song', 'album', 'playlist'
         * @param {number} id - Entity ID
         */
        toggleFavorite(type, id) {
            const key = `${type}-${id}`;
            if (this.favorites.includes(key)) {
                this.favorites = this.favorites.filter(f => f !== key);
                this.showToast('Favorilerden kaldırıldı', 'info');
            } else {
                this.favorites.push(key);
                this.showToast('Favorilere eklendi', 'success');
            }
        },

        /**
         * Check if item is favorited
         * @param {string} type - 'song', 'album', 'playlist'
         * @param {number} id - Entity ID
         * @returns {boolean}
         */
        isFavorite(type, id) {
            return this.favorites.includes(`${type}-${id}`);
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
            this.toggleFavorite('song', songId);
        }
    };
}

// Make globally accessible
window.muzibuFavorites = muzibuFavorites;
