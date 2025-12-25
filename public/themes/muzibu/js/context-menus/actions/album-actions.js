/**
 * 💿 ALBUM ACTIONS HANDLER
 *
 * ❌ goToArtist KALDIRILDI
 * ✅ addToPlaylist EKLENDİ - Albümün tüm şarkılarını playliste ekler
 */
const AlbumActions = {
    async play(data) {
        if (window.playAlbum) await window.playAlbum(data.id);
        else if (window.playContent) await window.playContent('album', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('album', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('album', data.id);
    },

    /**
     * 📝 Playliste ekle - Albümün TÜM şarkılarını ekler
     */
    addToPlaylist(data) {
        const playlistModal = Alpine.store('playlistModal');
        if (playlistModal) {
            playlistModal.showForAlbum(data.id, {
                title: data.title,
                artist: data.artist,
                cover_url: data.cover_url
            });
        }
    },

    // ❌ goToArtist KALDIRILDI

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.AlbumActions = AlbumActions;
