/**
 * ▶️ PLAY HANDLER - Tüm content type'lar için ortak play işlemleri
 */
const PlayHandler = {
    /**
     * Content'i çal
     * @param {string} type - song, album, playlist, genre, sector, radio
     * @param {object} data - Content data (id, title, etc.)
     */
    async play(type, data) {
        console.log(`▶️ PlayHandler.play: type=${type}, id=${data.id}`);

        // Type'a göre doğru fonksiyonu çağır
        const playFunctions = {
            song: window.playContent ? () => window.playContent('song', data.id) : null,
            album: window.playAlbum || (() => window.playContent?.('album', data.id)),
            playlist: window.playPlaylist || (() => window.playContent?.('playlist', data.id)),
            genre: window.playGenres || (() => window.playContent?.('genre', data.id)),
            sector: window.playSector || (() => window.playContent?.('sector', data.id)),
            radio: window.playRadio || (() => window.playContent?.('radio', data.id)),
            artist: () => window.playContent?.('artist', data.id)
        };

        const playFn = playFunctions[type];
        if (playFn) {
            try {
                await playFn();
                Alpine.store('toast')?.show(`▶️ Çalıyor: ${data.title || 'İçerik'}`, 'success');
            } catch (error) {
                console.error('PlayHandler error:', error);
                Alpine.store('toast')?.show('Çalma hatası', 'error');
            }
        } else {
            console.error(`No play function for type: ${type}`);
        }
    }
};

window.PlayHandler = PlayHandler;
