/**
 * 🎵 SONG ACTIONS HANDLER
 *
 * Song context menu action'ları için handler
 * ❌ goToArtist KALDIRILDI
 * ✅ addToPlaylist: $store.playlistModal kullanır
 */

const SongActions = {
    /**
     * ▶️ Şarkıyı çal
     */
    async play(data) {
        if (window.playContent) {
            await window.playContent('song', data.id);
        }
    },

    /**
     * ➕ Sıraya Ekle (çalan şarkının hemen ardına)
     */
    async playNext(data) {
        if (window.addContentToQueueNext) {
            await window.addContentToQueueNext('song', data.id);
        }
    },

    /**
     * ➕ Sıraya ekle (kuyruğun sonuna)
     */
    async addToQueue(data) {
        if (window.addContentToQueue) {
            await window.addContentToQueue('song', data.id);
        }
    },

    /**
     * ❤️ Favorilere ekle/çıkar
     */
    async toggleFavorite(data) {
        const favoritesStore = Alpine.store('favorites');
        if (favoritesStore) {
            await favoritesStore.toggle('song', data.id);
        }
    },

    /**
     * ⭐ Puan ver
     */
    rate(data) {
        const contextMenu = Alpine.store('contextMenu');
        if (contextMenu && contextMenu.ratingModal) {
            contextMenu.ratingModal.open = true;
            contextMenu.ratingModal.rating = 0;
            contextMenu.ratingModal.comment = '';
            contextMenu.ratingModal.songId = data.id;
        }
    },

    /**
     * 📝 Playliste ekle - Global playlistModal store kullanır
     */
    addToPlaylist(data) {
        const playlistModal = Alpine.store('playlistModal');
        if (playlistModal) {
            playlistModal.showForSong(data.id, {
                title: data.title,
                artist: data.artist,
                cover_url: data.cover_url
            });
        }
    },

    /**
     * 💿 Albüme git
     * Desktop: Sidebar preview aç
     * Mobile: Ana sayfada aç
     */
    goToAlbum(data) {
        const isDesktop = window.innerWidth >= 1024;

        if (isDesktop && data.album_id) {
            // Desktop: Sidebar preview aç
            const sidebar = Alpine.store('sidebar');
            if (sidebar) {
                sidebar.showPreview('album', data.album_id, {
                    type: 'Album',
                    id: data.album_id,
                    title: data.album_title || '',
                    cover: data.album_cover || '',
                    is_favorite: false
                });
            }
        } else {
            // Mobile: Ana sayfada aç
            if (data.album_slug) {
                window.location.href = `/albums/${data.album_slug}`;
            } else if (data.album_id) {
                // Fallback: API'den slug çek
                fetch(`/api/muzibu/albums/${data.album_id}`)
                    .then(res => res.json())
                    .then(album => {
                        if (album.slug) {
                            window.location.href = `/albums/${album.slug}`;
                        }
                    })
                    .catch(err => console.error('Album fetch error:', err));
            }
        }
    },

    // ❌ goToArtist KALDIRILDI

    /**
     * 🎯 Action executor
     */
    async execute(action, data) {
        const method = this[action];
        if (method) {
            await method.call(this, data);
        } else {
            console.error(`Unknown song action: ${action}`);
        }
    }
};

// Global export
window.SongActions = SongActions;
