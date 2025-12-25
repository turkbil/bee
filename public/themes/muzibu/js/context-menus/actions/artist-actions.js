/**
 * 🎤 ARTIST ACTIONS HANDLER
 */
const ArtistActions = {
    async play(data) {
        // TODO: Artist için özel play implementasyonu gerekiyor
        if (window.playContent) await window.playContent('artist', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('artist', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('artist', data.id);
    },

    goToDetail(data) {
        if (data.id) window.location.href = `/artists/${data.id}`;
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.ArtistActions = ArtistActions;
