/**
 * 🌐 GLOBAL HELPER FUNCTIONS
 *
 * These functions provide backward compatibility for older Blade components
 * that call global functions instead of Alpine.js stores.
 */

/**
 * 🎵 Add song to queue (legacy wrapper for song-actions-menu component)
 * @param {number} songId - Song ID
 */
window.addToQueue = async function(songId) {
    console.log(`➕ Global addToQueue called: songId=${songId}`);

    // Call the universal addContentToQueue from play-helpers.js
    if (window.addContentToQueue) {
        return window.addContentToQueue('song', songId);
    }

    console.error('addContentToQueue function not found');
};

/**
 * ❤️ Toggle favorite (legacy wrapper)
 * @param {number} id - Content ID
 * @param {string} type - Content type (song, album, playlist, etc.)
 */
window.toggleFavorite = async function(id, type) {
    console.log(`❤️ Global toggleFavorite called: id=${id}, type=${type}`);

    const favoritesStore = Alpine.store('favorites');
    if (!favoritesStore) {
        console.error('Favorites store not found');
        return;
    }

    // Call the store's toggle method
    await favoritesStore.toggle(type, id);
};
