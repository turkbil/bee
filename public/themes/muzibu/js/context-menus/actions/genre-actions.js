/**
 * 🎸 GENRE ACTIONS HANDLER
 */
const GenreActions = {
    async play(data) {
        if (window.playGenres) await window.playGenres(data.id);
        else if (window.playContent) await window.playContent('genre', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('genre', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('genre', data.id);
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.GenreActions = GenreActions;
