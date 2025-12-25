/**
 * ❤️ FAVORITE HANDLER - Favori işlemleri
 */
const FavoriteHandler = {
    /**
     * Favorilere ekle/çıkar
     * @param {string} type - song, album, playlist, genre, sector, radio
     * @param {object} data - Content data
     */
    async toggleFavorite(type, data) {
        console.log(`❤️ FavoriteHandler.toggleFavorite: type=${type}, id=${data.id}`);

        const store = Alpine.store('favorites');
        if (store) {
            try {
                await store.toggle(type, data.id);
                // Toast message is handled in favorites store
            } catch (error) {
                console.error('FavoriteHandler error:', error);
                Alpine.store('toast')?.show('Favori işlemi başarısız', 'error');
            }
        } else {
            console.error('Favorites store not found');
        }
    },

    /**
     * Favori mi kontrol et
     */
    isFavorite(type, id) {
        const store = Alpine.store('favorites');
        return store?.isFavorite(type, id) || false;
    }
};

window.FavoriteHandler = FavoriteHandler;
