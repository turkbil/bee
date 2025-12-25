/**
 * 🎯 ACTION EXECUTOR - Merkezi action yönetimi
 *
 * Tüm content type'lar için action'ları yönlendirir.
 * Type'a göre doğru handler'ı çağırır.
 */
const ActionExecutor = {
    /**
     * Action'ı çalıştır
     * @param {string} type - Content type (song, album, playlist, etc.)
     * @param {string} action - Action name (play, addToQueue, toggleFavorite, etc.)
     * @param {object} data - Content data
     */
    async execute(type, action, data) {
        console.log(`🎯 ActionExecutor: type=${type}, action=${action}, id=${data.id}`);

        // Type'a göre action handler'ı seç
        const actionHandlers = {
            song: window.SongActions,
            album: window.AlbumActions,
            playlist: window.PlaylistActions,
            genre: window.GenreActions,
            sector: window.SectorActions,
            radio: window.RadioActions,
            artist: window.ArtistActions
        };

        const handler = actionHandlers[type];

        // Önce type-specific handler'ı dene
        if (handler && typeof handler.execute === 'function') {
            await handler.execute(action, data);
            return;
        }

        // Fallback: Generic handlers kullan
        await this.executeGeneric(type, action, data);
    },

    /**
     * Generic action executor (fallback)
     */
    async executeGeneric(type, action, data) {
        switch (action) {
            case 'play':
                if (window.PlayHandler) {
                    await window.PlayHandler.play(type, data);
                }
                break;

            case 'addToQueue':
                if (window.QueueHandler) {
                    await window.QueueHandler.addToQueue(type, data);
                }
                break;

            case 'toggleFavorite':
                if (window.FavoriteHandler) {
                    await window.FavoriteHandler.toggleFavorite(type, data);
                }
                break;

            case 'rate':
                if (window.RatingHandler) {
                    window.RatingHandler.openRatingModal(type, data);
                }
                break;

            case 'addToPlaylist':
                if (window.PlaylistHandler) {
                    await window.PlaylistHandler.openAddToPlaylistModal(type, data);
                }
                break;

            case 'goToAlbum':
                const isDesktop = window.innerWidth >= 1024;
                if (isDesktop && data.album_id) {
                    // Desktop: Sidebar preview aç
                    const sidebar = Alpine.store('sidebar');
                    if (sidebar) {
                        sidebar.showPreview('album', data.album_id, {
                            type: 'Album',
                            id: data.album_id,
                            title: data.album_title || '',
                            cover: data.album_cover || '',
                            is_favorite: false
                        });
                    }
                } else {
                    // Mobile: Ana sayfada aç
                    if (data.album_slug) {
                        window.location.href = `/albums/${data.album_slug}`;
                    } else if (data.album_id) {
                        window.location.href = `/albums/${data.album_id}`;
                    }
                }
                break;

            case 'goToArtist':
                if (data.artist_id) {
                    window.location.href = `/artists/${data.artist_id}`;
                }
                break;

            case 'goToDetail':
                const urlMap = {
                    genre: '/genres/',
                    sector: '/sectors/',
                    radio: '/radios/',
                    playlist: '/playlists/',
                    album: '/albums/',
                    artist: '/artists/'
                };
                const baseUrl = urlMap[type];
                if (baseUrl && data.slug) {
                    window.location.href = baseUrl + data.slug;
                } else if (baseUrl && data.id) {
                    window.location.href = baseUrl + data.id;
                }
                break;

            case 'edit':
                if (type === 'playlist' && data.id) {
                    window.location.href = `/my-playlists/${data.id}/edit`;
                }
                break;

            case 'delete':
                if (type === 'playlist' && window.PlaylistActions) {
                    await window.PlaylistActions.delete(data);
                }
                break;

            default:
                console.warn(`Unknown action: ${action} for type: ${type}`);
        }
    }
};

window.ActionExecutor = ActionExecutor;
