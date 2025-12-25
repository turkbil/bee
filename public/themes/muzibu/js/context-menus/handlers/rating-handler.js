/**
 * ⭐ RATING HANDLER - Puan verme işlemleri
 */
const RatingHandler = {
    /**
     * Rating modal'ı aç
     * @param {string} type - song, album, playlist
     * @param {object} data - Content data
     */
    openRatingModal(type, data) {
        console.log(`⭐ RatingHandler.openRatingModal: type=${type}, id=${data.id}`);

        const contextMenu = Alpine.store('contextMenu');
        if (contextMenu?.ratingModal) {
            contextMenu.ratingModal.open = true;
            contextMenu.ratingModal.rating = 0;
            contextMenu.ratingModal.comment = '';
            contextMenu.ratingModal.contentType = type;
            contextMenu.ratingModal.contentId = data.id;
            contextMenu.ratingModal.contentTitle = data.title;
        }
    },

    /**
     * Rating kaydet
     * @param {string} type - Content type
     * @param {number} id - Content ID
     * @param {number} rating - 1-5 arası puan
     * @param {string} comment - Yorum (opsiyonel)
     */
    async submitRating(type, id, rating, comment = '') {
        console.log(`⭐ RatingHandler.submitRating: type=${type}, id=${id}, rating=${rating}`);

        try {
            const response = await fetch(`/api/muzibu/${type}s/${id}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ rating, comment })
            });

            if (response.ok) {
                Alpine.store('toast')?.show('Puanınız kaydedildi', 'success');
                return true;
            } else {
                Alpine.store('toast')?.show('Puan kaydedilemedi', 'error');
                return false;
            }
        } catch (error) {
            console.error('RatingHandler error:', error);
            Alpine.store('toast')?.show('Bir hata oluştu', 'error');
            return false;
        }
    }
};

window.RatingHandler = RatingHandler;
