/**
 * 🎯 CONTENT ACTIONS HANDLER (Album, Playlist, Genre, Sector, Radio)
 *
 * Song dışındaki tüm content type'lar için ortak action handler
 *
 * ❌ goToArtist KALDIRILDI
 * ✅ addToPlaylist EKLENDİ (album için)
 */

const ContentActions = {
    /**
     * ▶️ İçeriği çal
     */
    async play(type, data) {
        if (window.playContent) {
            await window.playContent(type, data.id);
        }
    },

    /**
     * ➕ Sıraya ekle
     * ⚠️ NOT: Radio için çağrılmamalı (menu'den kaldırıldı)
     */
    async addToQueue(type, data) {
        // Radio için sıraya ekleme yok
        if (type === 'radio') {
            console.warn('Radio cannot be added to queue');
            return;
        }

        if (window.addContentToQueue) {
            await window.addContentToQueue(type, data.id);
        }
    },

    /**
     * ❤️ Favorilere ekle/çıkar
     */
    async toggleFavorite(type, data) {
        const favoritesStore = Alpine.store('favorites');
        if (favoritesStore) {
            await favoritesStore.toggle(type, data.id);
        }
    },

    /**
     * 📝 Playliste ekle (album için)
     */
    addToPlaylist(type, data) {
        if (type === 'album') {
            const playlistModal = Alpine.store('playlistModal');
            if (playlistModal) {
                playlistModal.showForAlbum(data.id, {
                    title: data.title,
                    artist: data.artist,
                    cover_url: data.cover_url
                });
            }
        }
    },

    /**
     * 📝 Playlist düzenle
     */
    edit(type, data) {
        if (type === 'playlist' && data.id) {
            window.location.href = `/my-playlists/${data.id}/edit`;
        }
    },

    /**
     * 🗑️ Playlist sil
     */
    async delete(type, data) {
        if (type === 'playlist' && data.id) {
            if (confirm(`"${data.title}" playlist'ini silmek istediğinizden emin misiniz?`)) {
                try {
                    const response = await fetch(`/api/muzibu/playlists/${data.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        Alpine.store('toast').show('Playlist silindi', 'success');
                        // Refresh page or remove from DOM
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        Alpine.store('toast').show('Playlist silinemedi', 'error');
                    }
                } catch (error) {
                    console.error('Delete playlist error:', error);
                    Alpine.store('toast').show('Bir hata oluştu', 'error');
                }
            }
        }
    },

    // ❌ goToArtist KALDIRILDI

    /**
     * 🎯 Action executor
     */
    async execute(type, action, data) {
        const method = this[action];
        if (method) {
            await method.call(this, type, data);
        } else {
            console.error(`Unknown content action: ${action}`);
        }
    }
};

// Global export
window.ContentActions = ContentActions;
