/**
 * 📝 PLAYLIST HANDLER - Playlist işlemleri
 */
const PlaylistHandler = {
    /**
     * "Playliste Ekle" modal'ı aç
     * @param {string} type - song, album (hangi içerik ekleniyor)
     * @param {object} data - Content data
     */
    async openAddToPlaylistModal(type, data) {
        console.log(`📝 PlaylistHandler.openAddToPlaylistModal: type=${type}, id=${data.id}`);

        const contextMenu = Alpine.store('contextMenu');
        if (contextMenu) {
            // Kullanıcının playlist'lerini yükle
            await contextMenu.fetchUserPlaylists();

            contextMenu.playlistModal.open = true;
            contextMenu.playlistModal.selectedType = type;
            contextMenu.playlistModal.selectedId = data.id;
            contextMenu.playlistModal.selectedTitle = data.title;
        }
    },

    /**
     * İçeriği seçilen playlist'e ekle
     * @param {number} playlistId - Hedef playlist ID
     * @param {string} type - song, album
     * @param {number} contentId - Eklenecek içerik ID
     */
    async addToPlaylist(playlistId, type, contentId) {
        console.log(`📝 PlaylistHandler.addToPlaylist: playlist=${playlistId}, type=${type}, id=${contentId}`);

        try {
            const endpoint = type === 'song'
                ? `/api/muzibu/playlists/${playlistId}/songs`
                : `/api/muzibu/playlists/${playlistId}/bulk-add`;

            const body = type === 'song'
                ? { song_id: contentId }
                : { album_id: contentId };

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(body)
            });

            if (response.ok) {
                Alpine.store('toast')?.show('Playlist\'e eklendi', 'success');
                return true;
            } else {
                const error = await response.json();
                Alpine.store('toast')?.show(error.message || 'Eklenemedi', 'error');
                return false;
            }
        } catch (error) {
            console.error('PlaylistHandler error:', error);
            Alpine.store('toast')?.show('Bir hata oluştu', 'error');
            return false;
        }
    },

    /**
     * Yeni playlist oluştur
     * @param {string} name - Playlist adı
     * @param {boolean} isPublic - Herkese açık mı
     */
    async createPlaylist(name, isPublic = false) {
        console.log(`📝 PlaylistHandler.createPlaylist: name=${name}`);

        try {
            const response = await fetch('/api/muzibu/playlists/quick-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ title: name, is_public: isPublic })
            });

            if (response.ok) {
                const data = await response.json();
                Alpine.store('toast')?.show('Playlist oluşturuldu', 'success');
                return data.playlist;
            } else {
                Alpine.store('toast')?.show('Playlist oluşturulamadı', 'error');
                return null;
            }
        } catch (error) {
            console.error('PlaylistHandler error:', error);
            return null;
        }
    }
};

window.PlaylistHandler = PlaylistHandler;
