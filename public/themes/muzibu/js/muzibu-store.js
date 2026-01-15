/**
 * Muzibu Alpine.js Global Store
 * Modüller arası paylaşılan state ve fonksiyonlar
 */

// 🛡️ Guard against duplicate loading in SPA navigation
if (typeof window._muzibuStoreInitialized !== 'undefined') {
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
        toasts: [], // Active toasts array
        toastHeight: 68, // Toast height + gap (60px toast + 8px gap)
        enabled: false, // 🔴 Tooltip/toast disabled (set true to re-enable)

        show(message, type = 'info') {
            // 🔴 Skip if toasts are disabled
            if (!this.enabled) return;

            const toast = document.createElement('div');
            const toastId = Date.now() + Math.random(); // Unique ID

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

            // 🔼 Push existing toasts upward
            this.toasts.forEach((existingToast, index) => {
                const newBottom = (index + 1) * this.toastHeight + 96; // 96px = bottom-24 (6rem)
                existingToast.element.style.bottom = `${newBottom}px`;
            });

            // Create new toast at bottom
            toast.className = 'fixed right-6 z-[9999] transition-all duration-300 ease-out';
            toast.style.bottom = '96px'; // bottom-24 = 6rem = 96px
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';
            toast.dataset.toastId = toastId;

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

            // Add to active toasts array
            this.toasts.unshift({ id: toastId, element: toast });

            // Slide in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                this.dismiss(toastId);
            }, 3000);
        },

        dismiss(toastId) {
            const toastIndex = this.toasts.findIndex(t => t.id === toastId);
            if (toastIndex === -1) return;

            const toast = this.toasts[toastIndex];

            // Slide out
            toast.element.style.opacity = '0';
            toast.element.style.transform = 'translateX(400px)';

            // Remove from array
            this.toasts.splice(toastIndex, 1);

            // 🔽 Move remaining toasts down to fill the gap
            this.toasts.forEach((remainingToast, index) => {
                const newBottom = index * this.toastHeight + 96;
                remainingToast.element.style.bottom = `${newBottom}px`;
            });

            // Remove element from DOM
            setTimeout(() => {
                if (toast.element.parentNode) {
                    toast.element.remove();
                }
            }, 300);
        }
    });

    // Player State Store
    Alpine.store('player', {
        isPlaying: false,
        currentSong: null,
        isLoading: false, // SPA loading state için
        playContext: null, // Current play context (genre, album, playlist, etc.)
        recentlyPlayed: [], // 🎯 Son çalınan şarkılar (son 300 şarkı)
        maxRecentlyPlayed: 300, // Son kaç şarkı saklanacak (performans dengesi)

        showToast(message, type) {
            Alpine.store('toast').show(message, type);
        },

        /**
         * 🎵 Play a song by ID - delegates to Alpine component
         * @param {number} songId - Song ID to play
         * @param {number|null} albumId - Optional album ID for context
         * @param {number|null} genreId - Optional genre ID for context
         */
        playSong(songId, albumId = null, genreId = null) {
            // Find the Alpine component (html element has x-data="muzibuApp()")
            const htmlEl = document.documentElement;
            if (htmlEl && htmlEl._x_dataStack && htmlEl._x_dataStack[0]) {
                const app = htmlEl._x_dataStack[0];
                if (typeof app.playSong === 'function') {
                    app.playSong(songId);
                } else {
                    console.error('❌ playSong method not found on component');
                }
            } else {
                console.error('❌ Alpine component not found on html element');
            }
        },

        /**
         * 🎯 Add song to recently played list (exclude mekanizması)
         * @param {number} songId - Song ID to add
         */
        addToRecentlyPlayed(songId) {
            if (!songId) return;

            // Eğer zaten varsa çıkar (en başa ekleyeceğiz)
            this.recentlyPlayed = this.recentlyPlayed.filter(id => id !== songId);

            // Başa ekle
            this.recentlyPlayed.unshift(songId);

            // Max limit aşarsa sondan sil
            if (this.recentlyPlayed.length > this.maxRecentlyPlayed) {
                this.recentlyPlayed = this.recentlyPlayed.slice(0, this.maxRecentlyPlayed);
            }

            // localStorage'a kaydet
            _safeStorage.setItem('muzibu_recently_played', JSON.stringify(this.recentlyPlayed));

        },

        /**
         * 🎯 Get recently played song IDs
         * @returns {Array<number>} Recently played song IDs
         */
        getRecentlyPlayed() {
            // Bellekten dön
            if (this.recentlyPlayed.length > 0) {
                return this.recentlyPlayed;
            }

            // localStorage'dan yükle
            const stored = _safeStorage.getItem('muzibu_recently_played');
            if (stored) {
                this.recentlyPlayed = JSON.parse(stored);
                return this.recentlyPlayed;
            }

            return [];
        },

        /**
         * 🎯 Clear recently played list
         */
        clearRecentlyPlayed() {
            this.recentlyPlayed = [];
            _safeStorage.removeItem('muzibu_recently_played');
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
            const validTypes = ['genre', 'album', 'playlist', 'user_playlist', 'sector', 'radio', 'popular', 'recent', 'favorites', 'artist', 'search', 'song'];
            if (!validTypes.includes(context.type)) {
                console.warn('⚠️ Invalid context type:', context.type);
                return;
            }

            // Save to localStorage (using safe wrapper)
            _safeStorage.setItem('muzibu_play_context', JSON.stringify(context));

            // Update Alpine store (reactive)
            this.playContext = context;

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
                // Sessizce çık - context yokken update çağrılması normal bir durum
                return;
            }

            const newContext = { ...currentContext, ...updates };
            this.setPlayContext(newContext);

        },

        /**
         * Clear play context
         */
        clearPlayContext() {
            _safeStorage.removeItem('muzibu_play_context');
            this.playContext = null;
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

            // 🎯 Son çalınan şarkıları al (exclude için)
            const excludeSongIds = this.getRecentlyPlayed();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            try {

                const response = await fetch('/api/muzibu/queue/refill', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                    body: JSON.stringify({
                        type: context.type,
                        id: context.id,
                        offset: currentOffset,
                        limit: limit,
                        subType: context.subType,
                        source: context.source,
                        exclude_song_ids: excludeSongIds, // 🎯 Son çalınan şarkıları gönder
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // 🔄 CONTEXT TRANSITION: Backend suggested transition to Genre (infinite loop)
                if (data.transition) {

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

                    // 🧪 DEBUG: Şarkı seçim açıklaması (backend'den gelen)
                    if (data.explanation) {
                        window.debugLog?.('info', `📋 ${data.explanation.algoritma}`, {
                            kaynak: data.explanation.kaynak,
                            toplam: `${data.explanation.toplam_sarki} şarkı`,
                            alinan: `${data.explanation.baslangic}-${data.explanation.baslangic + data.explanation.alinan - 1}. sıra`,
                            secim_mantigi: data.explanation.algoritma
                        });
                    }

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
                this.showToast(window.muzibuPlayerConfig?.frontLang?.messages?.songs_loading_failed || 'Failed to load songs', 'error');
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

        // Playlist search & filtering
        playlistSearchQuery: '',
        songExistsInPlaylists: [], // Playlist IDs where current song already exists

        // Computed: filtered playlists based on search
        get filteredPlaylists() {
            if (!this.playlistSearchQuery || this.playlistSearchQuery.trim() === '') {
                return this.userPlaylists;
            }
            const query = this.playlistSearchQuery.toLowerCase().trim();
            return this.userPlaylists.filter(p =>
                p.title && p.title.toLowerCase().includes(query)
            );
        },

        // Touch/Long Press
        touchTimer: null,
        touchStartPos: { x: 0, y: 0 },

        show(event, type, data) {
            return this.openContextMenu(event, type, data);
        },

        openContextMenu(event, type, data) {
            if (this._debug) {
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

            // ✅ Favorites store'dan gerçek is_favorite durumunu al
            const favoritesStore = Alpine.store('favorites');
            if (favoritesStore && data && data.id) {
                data.is_favorite = favoritesStore.isFavorite(type, data.id);
            }

            this.data = data;
            this.actions = this.getActionsForType(type, data);
            this.visible = true;
        },

        getActionsForType(type, data) {
            // 🎯 Use MenuBuilder for dynamic menu generation
            if (window.MenuBuilder) {
                return window.MenuBuilder.getMenuItems(type, data);
            }

            // Fallback to old static menus (if MenuBuilder not loaded yet)
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
                    { icon: 'fa-heart', label: data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle', action: 'toggleFavorite' },
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
                ],
                genre: [
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
                    // Detaya Git
                    { icon: 'fa-music', label: 'Türe Git', action: 'goToDetail' }
                ],
                sector: [
                    // Favorilerime Ekle (Sadece bu seçenek)
                    { icon: 'fa-heart', label: data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle', action: 'toggleFavorite' }
                ],
                radio: [
                    // Çal
                    { icon: 'fa-play', label: 'Şimdi Dinle', action: 'play' },
                    { divider: true },
                    // Favorilerime Ekle
                    { icon: 'fa-heart', label: data.is_favorite ? 'Favorilerimden Çıkar' : 'Favorilerime Ekle', action: 'toggleFavorite' },
                    { divider: true },
                    // Puan Ver
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' }
                ],
                artist: [
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
                    // Sanatçıya Git
                    { icon: 'fa-user', label: 'Sanatçıya Git', action: 'goToDetail' }
                ]
            };

            return actions[type] || [];
        },

        async executeAction(action) {
            const { type, data } = this;

            // 🎯 Use ActionExecutor (Hybrid Approach - Centralized)
            if (window.ActionExecutor) {
                await window.ActionExecutor.execute(type, action, data);
                this.visible = false;
                return;
            }

            // Fallback: Type-specific handlers
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
            if (handler && typeof handler.execute === 'function') {
                await handler.execute(action, data);
                this.visible = false;
                return;
            }

            // Last fallback: Legacy switch (for backwards compatibility)
            switch(action) {
                case 'play':
                    if (window.playContent) window.playContent(type, data.id);
                    break;
                case 'addToQueue':
                    if (window.addContentToQueue) window.addContentToQueue(type, data.id);
                    break;
                case 'toggleFavorite':
                    Alpine.store('favorites')?.toggle(type, data.id);
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
                    if (data.album_id) window.location.href = `/albums/${data.album_id}`;
                    break;
                case 'goToDetail':
                    const urlMap = { genre: '/genres/', sector: '/sectors/', artist: '/artists/', playlist: '/playlists/', album: '/albums/' };
                    if (urlMap[type]) window.location.href = urlMap[type] + (data.slug || data.id);
                    break;
                case 'edit':
                    window.location.href = `/my-playlists/${data.id}/edit`;
                    break;
                case 'delete':
                    if (confirm(`"${data.title}" playlistini silmek istediğinize emin misiniz?`)) this.deletePlaylist(data.id);
                    break;
                case 'copy':
                    this.copyPlaylist(data.id);
                    break;
            }
            this.visible = false;
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
            // Reset search query when fetching
            this.playlistSearchQuery = '';
            this.songExistsInPlaylists = [];

            const songId = this.data?.id;

            // Fetch user playlists with credentials
            fetch('/api/muzibu/playlists/my-playlists', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => {
                    if (!res.ok) {
                        console.error('[Playlists] API error:', res.status, res.statusText);
                        throw new Error(`HTTP ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    const rawPlaylists = data.data || data || [];

                    // Get current locale from HTML lang attribute
                    const currentLocale = document.documentElement.lang || 'tr';

                    // Parse JSON title fields
                    this.userPlaylists = rawPlaylists.map(playlist => {
                        let parsedTitle = playlist.title;

                        // If title is JSON string, parse it
                        if (typeof parsedTitle === 'string' && parsedTitle.startsWith('{')) {
                            try {
                                const titleObj = JSON.parse(parsedTitle);
                                parsedTitle = titleObj[currentLocale] || titleObj.tr || titleObj.en || parsedTitle;
                            } catch (e) {
                                // Keep original if parse fails
                            }
                        }
                        // If title is already object
                        else if (typeof parsedTitle === 'object' && parsedTitle !== null) {
                            parsedTitle = parsedTitle[currentLocale] || parsedTitle.tr || parsedTitle.en || 'Playlist';
                        }

                        return {
                            ...playlist,
                            title: parsedTitle
                        };
                    });

                    // If we have a song ID, check which playlists contain it
                    if (songId && this.type === 'song') {
                        this.checkSongInPlaylists(songId);
                    }
                })
                .catch(err => {
                    console.error('[Playlists] Fetch error:', err);
                    this.userPlaylists = [];
                });
        },

        // Check which playlists contain the current song
        async checkSongInPlaylists(songId) {
            try {
                const response = await fetch(`/api/muzibu/songs/${songId}/playlists`);
                const data = await response.json();
                this.songExistsInPlaylists = data.playlist_ids || [];
            } catch (err) {
                console.error('Check song in playlists error:', err);
                this.songExistsInPlaylists = [];
            }
        },

        // Add song to multiple playlists at once
        async addToMultiplePlaylists(playlistIds) {
            const { type, data } = this;

            if (!playlistIds || playlistIds.length === 0) return;

            const promises = playlistIds.map(playlistId =>
                fetch(`/api/muzibu/playlists/${playlistId}/add-song`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        song_id: data.id
                    })
                }).then(res => {
                    if (!res.ok) {
                        console.error(`[ContextMenu] Add song failed (${res.status}):`, playlistId);
                        return { success: false, error: res.statusText };
                    }
                    return res.json();
                })
            );

            try {
                const results = await Promise.all(promises);
                const successCount = results.filter(r => r.success).length;

                if (successCount > 0) {
                    Alpine.store('toast').show(
                        `📋 "${data.title}" ${successCount} playlist'e eklendi`,
                        'success'
                    );

                    // Update songExistsInPlaylists
                    this.songExistsInPlaylists = [
                        ...this.songExistsInPlaylists,
                        ...playlistIds
                    ];
                } else {
                    Alpine.store('toast').show('❌ Şarkı playlist\'e eklenemedi', 'error');
                }

                this.playlistModal.open = false;
            } catch (err) {
                console.error('Add to multiple playlists error:', err);
                Alpine.store('toast').show('Bir hata oluştu', 'error');
            }
        },

        createNewPlaylist() {
            // Mevcut modal'ı tetikle
            this.playlistModal.open = false;
            const event = new CustomEvent('open-create-playlist-modal');
            window.dispatchEvent(event);
        },

        addToPlaylist(playlist) {
            const { type, data } = this;

            fetch(`/api/muzibu/playlists/${playlist.id}/add-song`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    song_id: data.id
                })
            })
            .then(res => {
                if (!res.ok) {
                    console.error(`[ContextMenu] Add to playlist failed (${res.status})`);
                    throw new Error(res.statusText);
                }
                return res.json();
            })
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

    // 🎵 Playlist Modal Store - Global, SPA-safe playlist ekleme modal'ı
    // contextMenu'dan bağımsız, her yerden erişilebilir
    Alpine.store('playlistModal', {
        open: false,

        // Content bilgisi (song veya album)
        contentType: null, // 'song' veya 'album'
        contentId: null,
        contentData: null, // { title, artist, cover_url, ... }

        // Playlists
        userPlaylists: [],
        searchQuery: '',
        existsInPlaylists: [], // Bu içeriğin zaten bulunduğu playlist ID'leri

        // Loading states
        loading: false,
        adding: false,

        // Selected playlists (checkbox multi-select)
        selectedPlaylists: [],

        /**
         * 🎵 Modal'ı şarkı için aç
         * @param {number} songId - Song ID
         * @param {Object} songData - Song data (title, artist, cover_url)
         */
        showForSong(songId, songData = {}) {
            this.contentType = 'song';
            this.contentId = songId;
            this.contentData = songData;
            this.selectedPlaylists = [];
            this.searchQuery = '';
            this.open = true;
            this.fetchPlaylists();
        },

        /**
         * 💿 Modal'ı albüm için aç (tüm şarkıları ekle)
         * @param {number} albumId - Album ID
         * @param {Object} albumData - Album data (title, artist, cover_url)
         */
        showForAlbum(albumId, albumData = {}) {
            this.contentType = 'album';
            this.contentId = albumId;
            this.contentData = albumData;
            this.selectedPlaylists = [];
            this.searchQuery = '';
            this.open = true;
            this.fetchPlaylists();
        },

        /**
         * Modal'ı kapat
         */
        hide() {
            this.open = false;
            this.selectedPlaylists = [];
        },

        /**
         * Kullanıcının playlistlerini çek
         */
        async fetchPlaylists() {
            this.loading = true;
            this.existsInPlaylists = [];

            try {
                const response = await fetch('/api/muzibu/playlists/my-playlists', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                const rawPlaylists = data.data || data || [];

                // Get current locale from HTML lang attribute
                const currentLocale = document.documentElement.lang || 'tr';

                // Parse JSON title fields
                this.userPlaylists = rawPlaylists.map(playlist => {
                    let parsedTitle = playlist.title;

                    // If title is JSON string, parse it
                    if (typeof parsedTitle === 'string' && parsedTitle.startsWith('{')) {
                        try {
                            const titleObj = JSON.parse(parsedTitle);
                            parsedTitle = titleObj[currentLocale] || titleObj.tr || titleObj.en || parsedTitle;
                        } catch (e) {
                            // Keep original if parse fails
                        }
                    }
                    // If title is already object
                    else if (typeof parsedTitle === 'object' && parsedTitle !== null) {
                        parsedTitle = parsedTitle[currentLocale] || parsedTitle.tr || parsedTitle.en || 'Playlist';
                    }

                    return {
                        ...playlist,
                        title: parsedTitle
                    };
                });

                // Şarkı için hangi playlistlerde var kontrol et
                if (this.contentType === 'song' && this.contentId) {
                    await this.checkExistingPlaylists();
                }
            } catch (err) {
                console.error('[PlaylistModal] Fetch error:', err);
                this.userPlaylists = [];
            } finally {
                this.loading = false;
            }
        },

        /**
         * Bu şarkı hangi playlistlerde var kontrol et
         */
        async checkExistingPlaylists() {
            if (this.contentType !== 'song' || !this.contentId) return;

            try {
                const response = await fetch(`/api/muzibu/songs/${this.contentId}/playlists`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.existsInPlaylists = data.playlist_ids || [];
            } catch (err) {
                console.error('[PlaylistModal] Check existing error:', err);
                this.existsInPlaylists = [];
            }
        },

        /**
         * Playlist ID zaten içeriyor mu?
         */
        isInPlaylist(playlistId) {
            return this.existsInPlaylists.includes(playlistId);
        },

        /**
         * Playlist seçili mi?
         */
        isSelected(playlistId) {
            return this.selectedPlaylists.includes(playlistId);
        },

        /**
         * Playlist seçimini toggle et (eski yöntem - buton ile kullanım için)
         */
        toggleSelection(playlistId) {
            if (this.isInPlaylist(playlistId)) return; // Zaten varsa toggle yapma

            const idx = this.selectedPlaylists.indexOf(playlistId);
            if (idx > -1) {
                this.selectedPlaylists.splice(idx, 1);
            } else {
                this.selectedPlaylists.push(playlistId);
            }
        },

        /**
         * 🔥 INSTANT TOGGLE: Playlist'e ekle/çıkar (direkt API çağrısı)
         */
        async toggleInstant(playlistId) {
            const isInPlaylist = this.isInPlaylist(playlistId);

            if (this.contentType === 'song') {
                if (isInPlaylist) {
                    // Çıkar
                    await this.removeSongFromPlaylist(playlistId);
                } else {
                    // Ekle
                    await this.addSongToPlaylist(playlistId);
                }
            } else if (this.contentType === 'album') {
                if (isInPlaylist) {
                    // Album'ü playlist'ten çıkarmak yok, sadece eklemek var
                    return;
                } else {
                    await this.addAlbumToPlaylist(playlistId);
                }
            }
        },

        /**
         * Tek playlist'e şarkı ekle
         */
        async addSongToPlaylist(playlistId) {
            try {
                const response = await fetch(`/api/muzibu/playlists/${playlistId}/add-song`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ song_id: this.contentId })
                });

                const data = await response.json();

                if (data.success) {
                    this.existsInPlaylists.push(playlistId);
                    const playlist = this.userPlaylists.find(p => p.playlist_id === playlistId);
                    Alpine.store('toast').show(
                        `✅ "${playlist?.title || 'Playlist'}" listesine eklendi`,
                        'success'
                    );
                } else {
                    Alpine.store('toast').show(data.message || 'Ekleme başarısız', 'error');
                }
            } catch (error) {
                console.error('Add song error:', error);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
            }
        },

        /**
         * Playlist'ten şarkı çıkar
         */
        async removeSongFromPlaylist(playlistId) {
            try {
                const response = await fetch(`/api/muzibu/playlists/${playlistId}/remove-song/${this.contentId}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const idx = this.existsInPlaylists.indexOf(playlistId);
                    if (idx > -1) this.existsInPlaylists.splice(idx, 1);
                    const playlist = this.userPlaylists.find(p => p.playlist_id === playlistId);
                    Alpine.store('toast').show(
                        `🗑️ "${playlist?.title || 'Playlist'}" listesinden çıkarıldı`,
                        'warning'
                    );
                } else {
                    Alpine.store('toast').show(data.message || 'Çıkarma başarısız', 'error');
                }
            } catch (error) {
                console.error('Remove song error:', error);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
            }
        },

        /**
         * Tek playlist'e albüm ekle
         */
        async addAlbumToPlaylist(playlistId) {
            try {
                const response = await fetch(`/api/muzibu/playlists/${playlistId}/add-album`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ album_id: this.contentId })
                });

                const data = await response.json();

                if (data.success) {
                    this.existsInPlaylists.push(playlistId);
                    const playlist = this.userPlaylists.find(p => p.playlist_id === playlistId);
                    Alpine.store('toast').show(
                        `✅ Albüm "${playlist?.title || 'Playlist'}" listesine eklendi (${data.added_count} şarkı)`,
                        'success'
                    );
                } else {
                    Alpine.store('toast').show(data.message || 'Ekleme başarısız', 'error');
                }
            } catch (error) {
                console.error('Add album error:', error);
                Alpine.store('toast').show('Bağlantı hatası', 'error');
            }
        },

        /**
         * Filtered playlists (search ile)
         */
        get filteredPlaylists() {
            if (!this.searchQuery || this.searchQuery.trim() === '') {
                return this.userPlaylists;
            }
            const query = this.searchQuery.toLowerCase().trim();
            return this.userPlaylists.filter(p =>
                p.title && p.title.toLowerCase().includes(query)
            );
        },

        /**
         * Seçili playlist var mı?
         */
        get hasSelection() {
            return this.selectedPlaylists.length > 0;
        },

        /**
         * Seçili playlistlere içerik ekle
         */
        async addToSelected() {
            if (!this.hasSelection) return;

            this.adding = true;

            try {
                if (this.contentType === 'song') {
                    await this.addSongToPlaylists();
                } else if (this.contentType === 'album') {
                    await this.addAlbumToPlaylists();
                }
            } finally {
                this.adding = false;
            }
        },

        /**
         * Şarkıyı seçili playlistlere ekle
         */
        async addSongToPlaylists() {
            const promises = this.selectedPlaylists.map(playlistId =>
                fetch(`/api/muzibu/playlists/${playlistId}/add-song`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ song_id: this.contentId })
                }).then(async res => {
                    const data = await res.json().catch(() => ({ error: 'Parse failed' }));
                    if (!res.ok) {
                        console.error(`[PlaylistModal] Add song failed (${res.status}):`, {
                            playlist_id: playlistId,
                            response: data
                        });
                        return { success: false, error: data.message || res.statusText };
                    }
                    return data;
                })
            );

            const results = await Promise.all(promises);
            const successCount = results.filter(r => r.success).length;

            if (successCount > 0) {
                Alpine.store('toast').show(
                    `📋 "${this.contentData?.title || 'Şarkı'}" ${successCount} playlist'e eklendi`,
                    'success'
                );

                // Update existsInPlaylists
                this.existsInPlaylists = [...this.existsInPlaylists, ...this.selectedPlaylists];
            } else {
                Alpine.store('toast').show(
                    `❌ Şarkı playlist'e eklenemedi`,
                    'error'
                );
            }

            this.hide();
        },

        /**
         * Albümün tüm şarkılarını seçili playlistlere ekle
         */
        async addAlbumToPlaylists() {
            const promises = this.selectedPlaylists.map(playlistId =>
                fetch(`/api/muzibu/playlists/${playlistId}/add-album`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ album_id: this.contentId })
                }).then(res => {
                    if (!res.ok) {
                        console.error(`[PlaylistModal] Add album failed (${res.status}):`, playlistId);
                        return { success: false, error: res.statusText };
                    }
                    return res.json();
                })
            );

            const results = await Promise.all(promises);
            const successCount = results.filter(r => r.success).length;

            if (successCount > 0) {
                const totalSongs = results.reduce((sum, r) => sum + (r.added_count || 0), 0);
                Alpine.store('toast').show(
                    `📋 "${this.contentData?.title || 'Albüm'}" - ${totalSongs} şarkı ${successCount} playlist'e eklendi`,
                    'success'
                );
            } else {
                Alpine.store('toast').show(
                    `❌ Albüm playlist'e eklenemedi`,
                    'error'
                );
            }

            this.hide();
        },

        /**
         * Yeni playlist oluştur modal'ını aç
         * Context'i sakla ki sonra geri dönebilsin
         */
        createNewPlaylist() {
            // 🎯 Context'i sakla (playlist oluşturulduktan sonra modal tekrar açılacak)
            window._playlistModalPendingContext = {
                contentType: this.contentType,
                contentId: this.contentId,
                contentData: this.contentData
            };

            this.hide();
            window.dispatchEvent(new CustomEvent('open-create-playlist-modal'));
        }
    });

    // 🎯 Sidebar Store - Dynamic sidebar content management
    Alpine.store('sidebar', {
        pageType: 'home', // 'home', 'playlist', 'album', 'genre', 'sector', 'artist', 'radio'
        tracks: [],
        entityInfo: null, // { title, cover, type, id }

        // 🚀 Right sidebar visibility (dynamic based on route)
        // Initial value calculated from current path
        // 2 SÜTUN (sidebar YOK): /dashboard, /corporate, /profile, /subscription, /my-certificate, /cart
        // 3 SÜTUN (sidebar VAR): Müzik sayfaları (songs, albums, playlists, genres, sectors, radios, search, favorites, vb.)
        rightSidebarVisible: (() => {
            const path = window.location.pathname;
            const routes = [
                '/', '/home',
                '/songs', '/albums', '/artists', '/playlists',
                '/genres', '/sectors', '/radios', '/search',
                '/muzibu/favorites',
                '/muzibu/my-playlists',
                '/muzibu/corporate-playlists',
                '/muzibu/listening-history'
            ];
            return routes.some(route => {
                if (route === '/') return path === '/';
                return path === route || path.startsWith(route + '/');
            });
        })(),

        // Routes where right sidebar should be visible (3 sütun layout)
        // 2 SÜTUN SAYFALAR (bu listede OLMAYAN): /dashboard, /corporate, /profile, /subscription, /my-certificate, /cart
        _rightSidebarRoutes: [
            '/', '/home',
            '/songs', '/albums', '/artists', '/playlists',
            '/genres', '/sectors', '/radios', '/search',
            '/muzibu/favorites',
            '/muzibu/my-playlists',
            '/muzibu/corporate-playlists',
            '/muzibu/listening-history'
        ],

        /**
         * 🚀 Update right sidebar visibility based on current URL
         */
        updateRightSidebarVisibility() {
            const path = window.location.pathname;

            // Check if path matches any of the routes or starts with them (for detail pages)
            const shouldShow = this._rightSidebarRoutes.some(route => {
                if (route === '/') return path === '/';
                return path === route || path.startsWith(route + '/');
            });

            this.rightSidebarVisible = shouldShow;

            // 🔧 FIX: Update grid class to prevent class conflicts after SPA navigation
            this._updateGridClass(shouldShow);
        },

        /**
         * 🔧 Update grid class manually to fix class conflicts
         * PHP adds initial grid class, but SPA navigation doesn't remove it
         * This causes Tailwind class conflicts where both 2-col and 3-col classes exist
         */
        _updateGridClass(showSidebar) {
            const mainGrid = document.querySelector('#main-app-grid');
            if (!mainGrid) return;

            // Grid class definitions (must match app.blade.php layout)
            const gridWithSidebar = [
                'md:grid-cols-[1fr_280px]',
                'lg:grid-cols-[220px_1fr_280px]',
                'xl:grid-cols-[220px_1fr_320px]',
                '2xl:grid-cols-[220px_1fr_360px]'
            ];
            const gridNoSidebar = [
                'lg:grid-cols-[220px_1fr]',
                'xl:grid-cols-[220px_1fr]',
                '2xl:grid-cols-[220px_1fr]'
            ];

            // Remove all grid column classes first
            mainGrid.classList.remove(...gridWithSidebar, ...gridNoSidebar);

            // Add correct classes based on sidebar visibility
            if (showSidebar) {
                mainGrid.classList.add(...gridWithSidebar);
            } else {
                mainGrid.classList.add(...gridNoSidebar);
            }
        },

        // Preview mode (for list page hover)
        previewMode: false,
        previewTracks: [],          // Currently displayed tracks (20, 40, 60, 80, 100)
        previewAllTracks: [],        // All tracks from API (full list)
        previewDisplayCount: 20,     // How many tracks currently displayed
        previewCurrentPage: 1,       // Pagination page (for 100+)
        previewTotalCount: 0,        // Total track count
        previewInfo: null,
        previewLoading: false,
        previewCache: {}, // Cache for fetched tracks

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
            this.previewMode = false; // Exit preview mode on detail page
        },

        /**
         * Reset sidebar to homepage state
         */
        reset() {
            this.pageType = 'home';
            this.tracks = [];
            this.entityInfo = null;
            this.previewMode = false;
            this.previewTracks = [];
            this.previewInfo = null;
        },

        /**
         * 🚀 PREFETCH: Silently fetch and cache tracks on hover (no loading spinner)
         * @param {string} type - Item type (playlist, album, genre, sector)
         * @param {number} id - Item ID
         */
        async prefetch(type, id) {
            const cacheKey = `${type}_${id}`;

            // Already cached or currently fetching? Skip
            if (this.previewCache[cacheKey] || this._prefetchingIds?.has(cacheKey)) {
                return;
            }

            // Track what we're fetching to avoid duplicates
            if (!this._prefetchingIds) this._prefetchingIds = new Set();
            this._prefetchingIds.add(cacheKey);

            // Build API URL based on type
            let apiUrl;
            switch (type) {
                case 'playlist':
                    apiUrl = `/api/muzibu/playlists/${id}`;
                    break;
                case 'album':
                    apiUrl = `/api/muzibu/albums/${id}`;
                    break;
                case 'genre':
                    apiUrl = `/api/muzibu/genres/${id}/songs`;
                    break;
                case 'sector':
                    apiUrl = `/api/muzibu/sectors/${id}/songs`;
                    break;
                default:
                    this._prefetchingIds.delete(cacheKey);
                    return;
            }

            try {
                const response = await fetch(apiUrl);
                if (response.ok) {
                    const data = await response.json();

                    // Extract tracks based on response format
                    let tracks = [];
                    if (type === 'genre' || type === 'sector') {
                        // Genre/Sector API returns: { genre/sector: {...}, songs: [...] }
                        tracks = data.songs || (Array.isArray(data) ? data : (data.data || []));
                    } else if (type === 'playlist') {
                        // Playlist API returns: { playlist: {..., songs: [...]} }
                        tracks = data.playlist?.songs || data.songs || [];
                    } else if (type === 'album') {
                        // Album API returns: { album: {..., songs: [...]} }
                        tracks = data.album?.songs || data.songs || [];
                    } else {
                        tracks = data.songs || [];
                    }

                    // Transform and cache ALL tracks (no limit)
                    this.previewCache[cacheKey] = tracks.map(song => ({
                        id: song.song_id,
                        title: this.getLocalizedTitle(song.song_title || song.title),
                        artist: this.getLocalizedTitle(song.artist_title || song.artist?.title || ''),
                        duration: this.formatDuration(song.duration),
                        cover: song.cover_url || song.album_cover || null,
                        album_id: song.album_id || null,
                        album_slug: song.album_slug || null,
                        album_title: this.getLocalizedTitle(song.album_title || ''),
                        album_cover: song.album_cover || null,
                        is_favorite: song.is_favorite || false
                    }));
                }
            } catch (e) {
                // Silent fail - prefetch is optional
            } finally {
                this._prefetchingIds.delete(cacheKey);
            }
        },

        /**
         * Show preview for a list item (on click - instant from cache)
         * @param {string} type - Item type (playlist, album, genre, sector)
         * @param {number} id - Item ID
         * @param {Object} info - Item info (title, cover)
         */
        async showPreview(type, id, info) {
            // 🎯 RESPONSIVE: 768px altında preview değil, detay sayfaya git
            if (window.innerWidth < 768) {
                const urlMap = {
                    playlist: '/playlists/',
                    album: '/albums/',
                    genre: '/genres/',
                    sector: '/sectors/',
                    radio: '/radios/',
                    artist: '/artists/'
                };
                if (urlMap[type]) {
                    window.location.href = urlMap[type] + (info?.slug || id);
                    return;
                }
            }

            // ✅ 768px+ show preview in right sidebar
            this.previewMode = true;
            this.previewInfo = info;

            // Check cache first (prefetch should have filled this)
            const cacheKey = `${type}_${id}`;
            if (this.previewCache[cacheKey]) {
                this.previewAllTracks = this.previewCache[cacheKey];
                this.previewTotalCount = this.previewAllTracks.length;
                this.previewDisplayCount = 20;
                this.previewCurrentPage = 1;
                this.previewTracks = this.previewAllTracks.slice(0, 20);
                return;
            }

            // Fallback: fetch if not cached (shouldn't happen with prefetch)
            // Build API URL based on type
            let apiUrl;
            switch (type) {
                case 'playlist':
                    apiUrl = `/api/muzibu/playlists/${id}`;
                    break;
                case 'album':
                    apiUrl = `/api/muzibu/albums/${id}`;
                    break;
                case 'genre':
                    apiUrl = `/api/muzibu/genres/${id}/songs`;
                    break;
                case 'sector':
                    apiUrl = `/api/muzibu/sectors/${id}/songs`;
                    break;
                default:
                    return;
            }

            // Fetch tracks from API (with loading since not prefetched)
            this.previewLoading = true;
            try {
                const response = await fetch(apiUrl);
                if (response.ok) {
                    const data = await response.json();

                    // Extract tracks based on response format
                    let tracks = [];
                    if (type === 'genre' || type === 'sector') {
                        // Genre/Sector API returns: { genre/sector: {...}, songs: [...] }
                        tracks = data.songs || (Array.isArray(data) ? data : (data.data || []));
                    } else if (type === 'playlist') {
                        // Playlist API returns: { playlist: {..., songs: [...]} }
                        tracks = data.playlist?.songs || data.songs || [];
                    } else if (type === 'album') {
                        // Album API returns: { album: {..., songs: [...]} }
                        tracks = data.album?.songs || data.songs || [];
                    } else {
                        tracks = data.songs || [];
                    }

                    // Transform ALL tracks to sidebar format
                    this.previewAllTracks = tracks.map(song => ({
                        id: song.song_id,
                        title: this.getLocalizedTitle(song.song_title || song.title),
                        artist: this.getLocalizedTitle(song.artist_title || song.artist?.title || ''),
                        duration: this.formatDuration(song.duration),
                        cover: song.cover_url || song.album_cover || null,
                        album_id: song.album_id || null,
                        album_slug: song.album_slug || null,
                        album_title: this.getLocalizedTitle(song.album_title || ''),
                        album_cover: song.album_cover || null,
                        is_favorite: song.is_favorite || false
                    }));

                    // Set pagination state
                    this.previewTotalCount = this.previewAllTracks.length;
                    this.previewDisplayCount = 20;
                    this.previewCurrentPage = 1;
                    this.previewTracks = this.previewAllTracks.slice(0, 20);

                    // Cache ALL tracks
                    this.previewCache[cacheKey] = this.previewAllTracks;
                }
            } catch (e) {
                console.error('Preview fetch error:', e);
                this.previewTracks = [];
            } finally {
                this.previewLoading = false;
            }
        },

        /**
         * 🔄 Refresh current preview (re-fetch from API)
         * Used after delete/reorder operations
         */
        async refreshPreview() {
            if (!this.previewMode || !this.previewInfo) return;

            const { type, id } = this.previewInfo;
            const cacheKey = `${type}_${id}`;

            // Clear cache for this item
            delete this.previewCache[cacheKey];

            // Re-fetch using showPreview
            await this.showPreview(type, id, this.previewInfo);
        },

        /**
         * Load more preview tracks (infinite scroll)
         * Loads 20 more tracks, max 100 total
         */
        loadMorePreviewTracks() {
            if (this.previewDisplayCount >= 100) {
                return; // Already showing max for infinite scroll
            }

            const newCount = Math.min(this.previewDisplayCount + 20, 100);
            this.previewDisplayCount = newCount;

            // ✅ Calculate offset based on current page (for pagination)
            const pageOffset = (this.previewCurrentPage - 1) * 100;
            this.previewTracks = this.previewAllTracks.slice(pageOffset, pageOffset + newCount);
        },

        /**
         * Load specific page (pagination for 100+)
         * @param {number} page - Page number (1-based)
         */
        loadPreviewPage(page) {
            if (!this.previewAllTracks.length) return;

            this.previewCurrentPage = page;
            const startIndex = (page - 1) * 100;
            const endIndex = startIndex + 100;

            this.previewTracks = this.previewAllTracks.slice(startIndex, endIndex);
            this.previewDisplayCount = Math.min(20, this.previewTracks.length);

            // Scroll to top when changing page
            setTimeout(() => {
                const scrollContainer = document.querySelector('[x-ref="previewScrollContainer"]');
                if (scrollContainer) {
                    scrollContainer.scrollTop = 0;
                }
            }, 50);
        },

        /**
         * Get total pages for pagination
         */
        get previewTotalPages() {
            return Math.ceil(this.previewTotalCount / 100);
        },

        /**
         * Check if more tracks can be loaded (for infinite scroll)
         */
        get canLoadMorePreviewTracks() {
            return this.previewDisplayCount < 100 && this.previewDisplayCount < this.previewTotalCount;
        },

        /**
         * Check if pagination should be shown
         */
        get showPreviewPagination() {
            return this.previewTotalCount > 100;
        },

        /**
         * Get total duration of all preview tracks
         * Returns formatted string like "2sa 30dk" or "45dk 12sn"
         */
        get previewTotalDuration() {
            if (!this.previewAllTracks.length) return '0dk';

            // Sum all track durations (format is "mm:ss")
            let totalSeconds = 0;
            this.previewAllTracks.forEach(track => {
                if (track.duration) {
                    const [mins, secs] = track.duration.split(':').map(Number);
                    totalSeconds += (mins * 60) + secs;
                }
            });

            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            if (hours > 0) {
                return `${hours}sa ${minutes}dk`;
            } else if (minutes > 0) {
                return seconds > 0 ? `${minutes}dk ${seconds}sn` : `${minutes}dk`;
            } else {
                return `${seconds}sn`;
            }
        },

        /**
         * Get localized title from JSON or string
         */
        getLocalizedTitle(title) {
            if (!title) return '';
            if (typeof title === 'string') return title;
            // JSON object with locale keys
            const locale = document.documentElement.lang || 'tr';
            return title[locale] || title.tr || title.en || Object.values(title)[0] || '';
        },

        /**
         * Format duration from seconds to mm:ss
         */
        formatDuration(seconds) {
            if (!seconds) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        /**
         * Hide preview (on mouse leave)
         */
        hidePreview() {
            this.previewMode = false;
            this.previewTracks = [];
            this.previewAllTracks = [];
            this.previewDisplayCount = 20;
            this.previewCurrentPage = 1;
            this.previewTotalCount = 0;
            this.previewInfo = null;
        },

        /**
         * 🎙️ Show radio preview (PC/Tablet only - for live radio broadcast)
         * Displays radio information in right sidebar when radio is played
         * @param {Object} radioData - Radio information from API
         */
        showRadioPreview(radioData) {
            // 🎯 SADECE PC/Tablet (768px+) - Mobilde preview GÖSTERME!
            if (window.innerWidth < 768) {
                return;
            }

            // ✅ 768px+ show radio preview in right sidebar
            this.previewMode = true;
            this.rightSidebarVisible = true;

            this.previewInfo = {
                type: 'Radio',
                id: radioData.radio_id,
                title: this.getLocalizedTitle(radioData.title),
                cover: radioData.cover_url || radioData.cover || null,
                description: this.getLocalizedTitle(radioData.description) || null,
                subtitle: null, // Radio için subtitle yok
                is_favorite: radioData.is_favorite || false
            };

            // Radio preview için track listesi yok (canlı yayın)
            this.previewTracks = [];
            this.previewAllTracks = [];
            this.previewTotalCount = 0;
            this.previewDisplayCount = 0;
            this.previewCurrentPage = 1;
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
        },

        /**
         * Check if preview has tracks
         */
        get hasPreviewTracks() {
            return this.previewTracks && this.previewTracks.length > 0;
        }
    });

    // 🔴 Confirmation Modal Store - Global confirmation dialog
    Alpine.store('confirmModal', {
        visible: false,
        title: '',
        message: '',
        confirmText: 'Onayla',
        cancelText: 'Vazgeç',
        type: 'info', // 'info' | 'danger'
        onConfirm: null,

        show(options) {
            this.title = options.title || 'Emin misiniz?';
            this.message = options.message || '';
            this.confirmText = options.confirmText || 'Onayla';
            this.cancelText = options.cancelText || 'Vazgeç';
            this.type = options.type || 'info';
            this.onConfirm = options.onConfirm || null;
            this.visible = true;
        },

        hide() {
            this.visible = false;
            // Reset after animation
            setTimeout(() => {
                this.title = '';
                this.message = '';
                this.confirmText = 'Onayla';
                this.cancelText = 'Vazgeç';
                this.type = 'info';
                this.onConfirm = null;
            }, 200);
        },

        confirm() {
            if (this.onConfirm && typeof this.onConfirm === 'function') {
                this.onConfirm();
            }
            this.hide();
        }
    });

    // 🔗 ALIAS: 'muzibu' store references 'player' store for backward compatibility
    // Some code uses Alpine.store('muzibu') instead of Alpine.store('player')
    Alpine.store('muzibu', Alpine.store('player'));
});

} // End of SPA guard - prevents duplicate store registration
