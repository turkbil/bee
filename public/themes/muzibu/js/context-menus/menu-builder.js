/**
 * 🎯 DYNAMIC MENU BUILDER
 *
 * Context menu'lar için dynamic olarak menu item'ları oluşturur.
 * Her content type (song, album, playlist, etc.) için farklı menu gösterir.
 *
 * 📋 KURALLAR:
 * - Song: Çal, Sıraya Ekle, Favori, Puan Ver, Playliste Ekle, Albüme Git
 * - Album: Çal, Sıraya Ekle, Favori, Playliste Ekle (tüm şarkılar)
 * - Playlist: Çal, Sıraya Ekle, Favori, Düzenle/Sil (sahibiyse)
 * - Genre/Sector: Çal, Sıraya Ekle, Favori
 * - Radio: SADECE Çal ve Favori (sıraya ekleme YOK - direkt çalar)
 * - Artist: Çal, Sıraya Ekle, Favori
 *
 * ❌ KALDIRILDI: goToArtist (tüm menülerden)
 */

const MenuBuilder = {
    /**
     * 🎵 Song için menu itemları
     */
    getSongMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' }
        ];

        // Sıraya Ekle (çalan şarkının hemen ardına)
        items.push({ icon: 'fa-step-forward', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'playNext' });

        // Favorilere Ekle/Çıkar (dynamic icon based on is_favorite)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        // Divider
        items.push({ divider: true });

        // Puan Ver
        items.push({ icon: 'fa-star', iconPrefix: 'fas', label: 'Puan Ver', action: 'rate' });

        // Playliste Ekle
        items.push({ icon: 'fa-list', iconPrefix: 'fas', label: 'Playliste Ekle', action: 'addToPlaylist' });

        // Divider
        items.push({ divider: true });

        // Albüme Git (sadece album varsa)
        if (data.album_id) {
            items.push({ icon: 'fa-compact-disc', iconPrefix: 'fas', label: 'Albüme Git', action: 'goToAlbum' });
        }

        // ❌ goToArtist KALDIRILDI

        return items;
    },

    /**
     * 💿 Album için menu itemları
     */
    getAlbumMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' },
            { icon: 'fa-plus-circle', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'addToQueue' }
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        // Divider
        items.push({ divider: true });

        // ✅ Playliste Ekle (Tüm Şarkılar) - YENİ EKLENDİ
        items.push({ icon: 'fa-list', iconPrefix: 'fas', label: 'Playliste Ekle', action: 'addToPlaylist' });

        // ❌ goToArtist KALDIRILDI

        return items;
    },

    /**
     * 🎵 Playlist için menu itemları
     */
    getPlaylistMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' },
            { icon: 'fa-plus-circle', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'addToQueue' }
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ divider: true });
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        // Kullanıcının kendi playlist'iyse: Düzenle/Sil (is_owner veya is_mine)
        if (data.is_owner || data.is_mine) {
            items.push({ divider: true });
            items.push({ icon: 'fa-edit', iconPrefix: 'fas', label: 'Düzenle', action: 'edit' });
            items.push({ icon: 'fa-trash', iconPrefix: 'fas', label: 'Sil', action: 'delete' });
        }

        return items;
    },

    /**
     * 🎵 My Playlist için menu itemları (My-Playlists sayfası için)
     * Her zaman edit/delete gösterir çünkü kullanıcının kendi playlist'i
     */
    getMyPlaylistMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' },
            { icon: 'fa-plus-circle', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'addToQueue' }
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ divider: true });
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        // Her zaman düzenle/sil göster (my-playlists sayfası kullanıcının kendi playlist'leri)
        items.push({ divider: true });
        items.push({ icon: 'fa-edit', iconPrefix: 'fas', label: 'Düzenle', action: 'edit' });
        items.push({ icon: 'fa-trash', iconPrefix: 'fas', label: 'Sil', action: 'delete' });

        return items;
    },

    /**
     * 🎸 Genre için menu itemları
     */
    getGenreMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' },
            { icon: 'fa-plus-circle', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'addToQueue' }
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ divider: true });
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        return items;
    },

    /**
     * 🏢 Sector için menu itemları
     * ⚠️ SADECE Favorilere Ekle - Çal ve Sıraya Ekle YOK
     */
    getSectorMenuItems(data) {
        const items = [];

        // Favorilere Ekle/Çıkar (dynamic icon) - SADECE BU SEÇENEK
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        return items;
    },

    /**
     * 📻 Radio için menu itemları
     * ⚠️ ÖZEL: Radio'da sıraya ekle YOK - direkt çalar!
     */
    getRadioMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Şimdi Dinle', action: 'play' }
            // ❌ addToQueue KALDIRILDI - Radio direkt çalar
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ divider: true });
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        return items;
    },

    /**
     * 🎤 Artist için menu itemları
     */
    getArtistMenuItems(data) {
        const items = [
            { icon: 'fa-play', iconPrefix: 'fas', label: 'Çal', action: 'play' },
            { icon: 'fa-plus-circle', iconPrefix: 'fas', label: 'Sıraya Ekle', action: 'addToQueue' }
        ];

        // Favorilere Ekle/Çıkar (dynamic icon)
        const favoriteLabel = data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle';
        const favoriteIconPrefix = data.is_favorite ? 'fas' : 'far';
        items.push({ divider: true });
        items.push({ icon: 'fa-heart', iconPrefix: favoriteIconPrefix, label: favoriteLabel, action: 'toggleFavorite' });

        return items;
    },

    /**
     * 🎯 MAIN: Type'a göre menu itemları döndür
     */
    getMenuItems(type, data) {
        const methodMap = {
            'song': this.getSongMenuItems,
            'album': this.getAlbumMenuItems,
            'playlist': this.getPlaylistMenuItems,
            'my-playlist': this.getMyPlaylistMenuItems,
            'genre': this.getGenreMenuItems,
            'sector': this.getSectorMenuItems,
            'radio': this.getRadioMenuItems,
            'artist': this.getArtistMenuItems
        };

        const method = methodMap[type];
        if (!method) {
            console.error(`Unknown content type: ${type}`);
            return [];
        }

        return method.call(this, data);
    }
};

// Global export
window.MenuBuilder = MenuBuilder;
