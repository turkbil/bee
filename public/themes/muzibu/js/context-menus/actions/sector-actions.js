/**
 * 🏢 SECTOR ACTIONS HANDLER
 */
const SectorActions = {
    async play(data) {
        if (window.playSector) await window.playSector(data.id);
        else if (window.playContent) await window.playContent('sector', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('sector', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('sector', data.id);
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.SectorActions = SectorActions;
