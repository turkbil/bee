/**
 * 📻 RADIO ACTIONS HANDLER
 *
 * ⚠️ ÖZEL: Radio'da sıraya ekleme YOK!
 * Radio direkt çalar, queue sistemi kullanmaz.
 * ❌ addToQueue KALDIRILDI
 */
const RadioActions = {
    async play(data) {
        if (window.playRadio) await window.playRadio(data.id);
        else if (window.playContent) await window.playContent('radio', data.id);
    },

    // ❌ addToQueue KALDIRILDI - Radio direkt çalar

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('radio', data.id);
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.RadioActions = RadioActions;
