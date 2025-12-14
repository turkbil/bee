/**
 * Muzibu Alpine.js Global Store
 * Modüller arası paylaşılan state ve fonksiyonlar
 */

// 🛡️ Guard against duplicate loading in SPA navigation
if (typeof window._muzibuStoreInitialized !== 'undefined') {
    console.log('⚠️ muzibu-store.js already loaded, skipping...');
} else {
    window._muzibuStoreInitialized = true;

// 🛡️ Use global safeStorage from safe-storage.js (or fallback to localStorage)
// safe-storage.js must be loaded before this file
// Use window namespace to prevent redeclaration errors
window._safeStorage = window._safeStorage || window.safeStorage || {
    getItem: (key) => { try { return localStorage.getItem(key); } catch(e) { return null; } },
    setItem: (key, value) => { try { localStorage.setItem(key, value); } catch(e) {} },
    removeItem: (key) => { try { localStorage.removeItem(key); } catch(e) {} }
};

// Local reference for convenience
const _safeStorage = window._safeStorage;

document.addEventListener('alpine:init', () => {
    // Modern Toast Store - Minimal & Professional
    Alpine.store('toast', {
        show(message, type = 'info') {
            const toast = document.createElement('div');

            // Icon mapping
            const icons = {
                success: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };

            // Color mapping
            const colors = {
                success: 'text-emerald-400',
                error: 'text-red-400',
                warning: 'text-amber-400',
                info: 'text-blue-400'
            };

            toast.className = 'fixed bottom-24 right-6 z-[9999] transition-all duration-300 ease-out transform translate-x-0';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';

            toast.innerHTML = `
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border border-white/10"
                     style="background: rgba(24, 24, 27, 0.95); backdrop-filter: blur(12px);">
                    <div class="${colors[type] || colors.info}">
                        ${icons[type] || icons.info}
                    </div>
                    <span class="text-white text-sm font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Slide in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Slide out
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(400px)';
            }, 3000);

            // Remove
            setTimeout(() => toast.remove(), 3500);
        }
    });

    // Player State Store
    Alpine.store('player', {
        isPlaying: false,
        currentSong: null,
        isLoading: false, // SPA loading state için
        playContext: null, // Current play context (genre, album, playlist, etc.)

        showToast(message, type) {
            Alpine.store('toast').show(message, type);
        },

        /**
         * Set play context for infinite queue system
         * @param {Object} context - Context object
         * @param {string} context.type - Context type (genre, album, playlist, sector, radio, popular, recent, favorites, artist, search)
         * @param {number} context.id - Context ID
         * @param {string} context.name - Context name (for display)
         * @param {*} context.* - Additional context-specific data
         */
        setPlayContext(context) {
            // Validate context
            const validTypes = ['genre', 'album', 'playlist', 'user_playlist', 'sector', 'radio', 'popular', 'recent', 'favorites', 'artist', 'search'];
            if (!validTypes.includes(context.type)) {
                console.warn('⚠️ Invalid context type:', context.type);
                return;
            }

            // Save to localStorage (using safe wrapper)
            _safeStorage.setItem('muzibu_play_context', JSON.stringify(context));

            // Update Alpine store (reactive)
            this.playContext = context;

            console.log('🎯 Play Context Set:', context);
        },

        /**
         * Get current play context
         * @returns {Object|null} Context object or null
         */
        getPlayContext() {
            if (this.playContext) {
                return this.playContext;
            }

            const stored = _safeStorage.getItem('muzibu_play_context');
            if (stored) {
                this.playContext = JSON.parse(stored);
                return this.playContext;
            }

            return null;
        },

        /**
         * Update play context (for offset, etc.)
         * @param {Object} updates - Fields to update
         */
        updatePlayContext(updates) {
            const currentContext = this.getPlayContext();
            if (!currentContext) {
                console.warn('⚠️ No context to update');
                return;
            }

            const newContext = { ...currentContext, ...updates };
            this.setPlayContext(newContext);

            console.log('🔄 Play Context Updated:', newContext);
        },

        /**
         * Clear play context
         */
        clearPlayContext() {
            _safeStorage.removeItem('muzibu_play_context');
            this.playContext = null;
            console.log('🗑️ Play Context Cleared');
        },

        /**
         * Refill queue based on current context
         * @param {number} currentOffset - Current offset (how many songs played from this context)
         * @param {number} limit - How many songs to fetch (default: 15)
         * @returns {Promise<Array>} Array of songs
         */
        async refillQueue(currentOffset = 0, limit = 15) {
            const context = this.getPlayContext();

            if (!context) {
                console.warn('⚠️ No play context - cannot refill queue');
                return [];
            }

            try {
                console.log('🔄 Refilling queue...', { context, currentOffset, limit });

                const response = await fetch('/api/muzibu/queue/refill', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        type: context.type,
                        id: context.id,
                        offset: currentOffset,
                        limit: limit,
                        subType: context.subType,
                        source: context.source,
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // 🔄 CONTEXT TRANSITION: Backend suggested transition to Genre (infinite loop)
                if (data.transition) {
                    console.log('🔄 Context Transition:', data.transition);
                    console.log(`📢 ${data.transition.reason}`);

                    // Update play context to Genre (infinite music guaranteed)
                    this.updatePlayContext({
                        type: data.transition.type,
                        id: data.transition.id,
                        offset: 0, // Reset offset for new context
                        subType: null,
                        source: `transition_from_${context.type}`
                    });

                    // Show toast to user
                    this.showToast(`Müzik bitmesin! ${data.transition.name} çalıyor`, 'info');
                }

                if (data.success && data.songs && data.songs.length > 0) {
                    console.log(`✅ Queue refilled: ${data.songs.length} songs`, data.songs);

                    // Update context offset for next refill (unless transition happened)
                    if (!data.transition) {
                        this.updatePlayContext({ offset: currentOffset + data.songs.length });
                    }

                    return data.songs;
                } else {
                    console.warn('⚠️ No songs returned from API');
                    return [];
                }

            } catch (error) {
                console.error('❌ Queue refill error:', error);
                this.showToast('Şarkılar yüklenirken hata oluştu', 'error');
                return [];
            }
        }
    });

    // 🚀 Router Store - DEVRE DIŞI (wire:navigate kullanıyoruz)
    // Alpine.store('router', {
    //     currentRoute: '/',
    //     isLoading: false,
    //     navigateTo(url) {
    //         if (window.muzibuRouter) {
    //             window.muzibuRouter.navigateTo(url);
    //         }
    //     },
    //     clearCache() {
    //         if (window.muzibuRouter) {
    //             window.muzibuRouter.clearCache();
    //         }
    //     }
    // });

    // Context Menu Store
    Alpine.store('contextMenu', {
        visible: false,
        x: 0,
        y: 0,
        type: null, // song|album|playlist|genre|sector
        data: null,
        actions: [],

        // Debug flag
        _debug: true,

        // Modals
        ratingModal: {
            open: false,
            rating: 0,
            hoverRating: 0,
            comment: ''
        },

        playlistModal: {
            open: false
        },

        // User playlists (backend'den gelecek)
        userPlaylists: [],

        // Touch/Long Press
        touchTimer: null,
        touchStartPos: { x: 0, y: 0 },

        show(event, type, data) {
            return this.openContextMenu(event, type, data);
        },

        openContextMenu(event, type, data) {
            if (this._debug) {
                console.log('🎯 Context Menu Opened:', { type, data, event });
            }

            const menuWidth = 250;
            const menuHeight = 400;

            // position: fixed için clientX/clientY kullan
            const x = event.clientX || event.pageX;
            const y = event.clientY || event.pageY;

            // Viewport sınırlarını kontrol et
            this.x = (x + menuWidth > window.innerWidth)
                ? window.innerWidth - menuWidth - 10
                : x;

            this.y = (y + menuHeight > window.innerHeight)
                ? window.innerHeight - menuHeight - 10
                : y;

            this.type = type;
            this.data = data;
            this.actions = this.getActionsForType(type, data);
            this.visible = true;
        },

        getActionsForType(type, data) {
            const actions = {
                song: [
                    // Çal
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    // Sıraya Ekle
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' },
                    { divider: true },
                    // Favorilerime Ekle
                    { icon: 'fa-heart', label: data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle', action: 'toggleFavorite' },
                    { divider: true },
                    // Puan Ver
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    // Playliste Ekle
                    { icon: 'fa-list', label: 'Playliste Ekle', action: 'addToPlaylist', submenu: true },
                    // Albüme Git
                    { icon: 'fa-compact-disc', label: 'Albüme Git', action: 'goToAlbum' }
                ],
                album: [
                    // Çal
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    // Sıraya Ekle
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' },
                    { divider: true },
                    // Favorilerime Ekle
                    { icon: 'fa-heart', label: 'Favorilerime Ekle', action: 'toggleFavorite' },
                    { divider: true },
                    // Puan Ver
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    // Playliste Ekle (Tüm Şarkılar)
                    { icon: 'fa-list', label: 'Playliste Ekle (Tüm)', action: 'addToPlaylist' }
                ],
                playlist: [
                    // Çal
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    // Sıraya Ekle
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' },
                    { divider: true },
                    // Favorilerime Ekle
                    { icon: 'fa-heart', label: 'Favorilerime Ekle', action: 'toggleFavorite' },
                    { divider: true },
                    // Puan Ver
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    // Kopyala / Düzenle / Sil
                    ...(data.is_mine ? [
                        { icon: 'fa-edit', label: 'Düzenle', action: 'edit' },
                        { icon: 'fa-trash-alt', label: 'Sil', action: 'delete' }
                    ] : [
                        { icon: 'fa-copy', label: 'Playlisti Kopyala', action: 'copy' }
                    ])
                ]
            };

            return actions[type] || [];
        },

        executeAction(action) {
            const { type, data } = this;

            console.log(`🎯 Context Menu Action: ${action} | Type: ${type}`, data);

            switch(action) {
                case 'play':
                    if (window.playContent) {
                        window.playContent(type, data.id);
                    }
                    Alpine.store('toast').show(`▶️ Çalıyor: ${data.title}`, 'success');
                    break;
                case 'addToQueue':
                    if (window.addToQueue) {
                        window.addToQueue(type, data.id);
                    }
                    Alpine.store('toast').show(`➕ Sıraya eklendi: ${data.title}`, 'success');
                    break;
                case 'toggleFavorite':
                    if (window.toggleFavorite) {
                        window.toggleFavorite(type, data.id);
                    }
                    break;
                case 'rate':
                    this.ratingModal.open = true;
                    this.ratingModal.rating = 0;
                    this.ratingModal.comment = '';
                    break;
                case 'addToPlaylist':
                    this.fetchUserPlaylists();
                    this.playlistModal.open = true;
                    break;
                case 'goToAlbum':
                    if (data.album_id) {
                        window.location.href = `/albums/${data.album_id}`;
                    }
                    break;
                case 'edit':
                    window.location.href = `/my-playlists/${data.id}/edit`;
                    break;
                case 'delete':
                    if (confirm(`"${data.title}" playlistini silmek istediğinize emin misiniz?\n\nBu işlem geri alınamaz!`)) {
                        this.deletePlaylist(data.id);
                    }
                    break;
                case 'copy':
                    this.copyPlaylist(data.id);
                    break;
            }
        },

        submitRating() {
            const { rating, comment } = this.ratingModal;
            const { type, data } = this;

            fetch(`/api/muzibu/${type}s/${data.id}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ rating, comment })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    // Puanlama mesajı
                    let message = `⭐ ${rating} yıldız puanınız kaydedildi!`;

                    // 4-5 yıldız verdiyse ve otomatik favoriye eklendiyse
                    if (result.auto_favorited) {
                        message += ' ❤️ Favorilere otomatik eklendi!';
                    }

                    Alpine.store('toast').show(message, 'success');
                    this.ratingModal.open = false;
                } else {
                    Alpine.store('toast').show(result.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(err => {
                console.error('Rating error:', err);
                Alpine.store('toast').show('Bir hata oluştu', 'error');
            });
        },

        fetchUserPlaylists() {
            fetch('/api/muzibu/my-playlists')
                .then(res => res.json())
                .then(data => {
                    this.userPlaylists = data.data || [];
                })
                .catch(err => console.error('Fetch playlists error:', err));
        },

        createNewPlaylist() {
            // Mevcut modal'ı tetikle
            this.playlistModal.open = false;
            const event = new CustomEvent('open-create-playlist-modal');
            window.dispatchEvent(event);
        },

        addToPlaylist(playlist) {
            const { type, data } = this;

            fetch(`/api/muzibu/playlists/${playlist.id}/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type: type,
                    item_id: data.id
                })
            })
            .then(res => res.json())
            .then(result => {
                Alpine.store('toast').show(`📋 "${data.title}" playliste eklendi: ${playlist.title}`, 'success');
                this.playlistModal.open = false;
            })
            .catch(err => {
                console.error('Add to playlist error:', err);
                Alpine.store('toast').show('Bir hata oluştu', 'error');
            });
        },

        deletePlaylist(id) {
            fetch(`/api/muzibu/playlists/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(result => {
                Alpine.store('toast').show(`🗑️ Playlist silindi`, 'success');
                // Sayfayı yenile
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                console.error('Delete playlist error:', err);
                Alpine.store('toast').show('Bir hata oluştu', 'error');
            });
        },

        copyPlaylist(id) {
            const { data } = this;

            fetch(`/api/muzibu/playlists/${id}/copy`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    title: data.title + ' (Kopyam)'
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    Alpine.store('toast').show(`📋 "${data.title}" playlist'inize kopyalandı`, 'success');
                    // Kullanıcı playlist sayfasındaysa yenile
                    if (window.location.pathname.includes('/my-playlists')) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } else {
                    Alpine.store('toast').show(result.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(err => {
                console.error('Copy playlist error:', err);
                Alpine.store('toast').show('Bir hata oluştu', 'error');
            });
        }
    });

    // Playlist Select Store (Add to Playlist Modal)
    Alpine.store('playlistSelect', {
        open: false,
        songId: null,

        show(songId) {
            this.songId = songId;
            this.open = true;
        },

        hide() {
            this.open = false;
            this.songId = null;
        }
    });

    // 🎯 Sidebar Store - Dynamic sidebar content management
    Alpine.store('sidebar', {
        pageType: 'home', // 'home', 'playlist', 'album', 'genre', 'sector', 'artist', 'radio'
        tracks: [],
        entityInfo: null, // { title, cover, type, id }

        /**
         * Set sidebar content for a detail page
         * @param {string} type - Page type
         * @param {Array} tracks - Track list
         * @param {Object} info - Entity info (title, cover, etc.)
         */
        setContent(type, tracks = [], info = null) {
            this.pageType = type;
            this.tracks = tracks || [];
            this.entityInfo = info;
            console.log('🎯 Sidebar content set:', type, tracks?.length || 0, 'tracks');
        },

        /**
         * Reset sidebar to homepage state
         */
        reset() {
            this.pageType = 'home';
            this.tracks = [];
            this.entityInfo = null;
        },

        /**
         * Check if we're on a detail page
         */
        get isDetailPage() {
            return this.pageType !== 'home';
        },

        /**
         * Check if tracks are available
         */
        get hasTracks() {
            return this.tracks && this.tracks.length > 0;
        }
    });

    // 🔗 ALIAS: 'muzibu' store references 'player' store for backward compatibility
    // Some code uses Alpine.store('muzibu') instead of Alpine.store('player')
    Alpine.store('muzibu', Alpine.store('player'));
});

} // End of SPA guard - prevents duplicate store registration
