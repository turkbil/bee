/**
 * 🎵 PLAYLIST ACTIONS HANDLER
 */
const PlaylistActions = {
    async play(data) {
        if (window.playPlaylist) await window.playPlaylist(data.id);
        else if (window.playContent) await window.playContent('playlist', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('playlist', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('playlist', data.id);
    },

    edit(data) {
        if (data.id) window.location.href = `/my-playlists/${data.id}/edit`;
    },

    async delete(data) {
        if (!confirm(`"${data.title}" playlist'ini silmek istediğinizden emin misiniz?`)) return;
        try {
            const response = await fetch(`/api/muzibu/playlists/${data.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
            });
            if (response.ok) {
                Alpine.store('toast')?.show('Playlist silindi', 'success');
                setTimeout(() => window.location.reload(), 1000);
            }
        } catch (e) { console.error(e); }
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.PlaylistActions = PlaylistActions;
