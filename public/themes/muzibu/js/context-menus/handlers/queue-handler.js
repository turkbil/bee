/**
 * ➕ QUEUE HANDLER - Sıraya ekleme işlemleri
 */
const QueueHandler = {
    /**
     * Content'i sıraya ekle
     * @param {string} type - song, album, playlist, genre, sector, radio
     * @param {object} data - Content data
     */
    async addToQueue(type, data) {
        console.log(`➕ QueueHandler.addToQueue: type=${type}, id=${data.id}`);

        if (window.addContentToQueue) {
            try {
                await window.addContentToQueue(type, data.id);
                // Toast message is handled in addContentToQueue
            } catch (error) {
                console.error('QueueHandler error:', error);
                Alpine.store('toast')?.show('Sıraya eklenemedi', 'error');
            }
        } else {
            console.error('addContentToQueue function not found');
        }
    },

    /**
     * Queue'yu temizle
     */
    clearQueue() {
        const player = Alpine.store('player');
        if (player) {
            player.queue = [];
            player.queueIndex = 0;
            Alpine.store('toast')?.show('Sıra temizlendi', 'info');
        }
    }
};

window.QueueHandler = QueueHandler;
