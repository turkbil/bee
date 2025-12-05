/**
 * Muzibu Alpine.js Global Store
 * Modüller arası paylaşılan state ve fonksiyonlar
 */

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
        showToast(message, type) {
            Alpine.store('toast').show(message, type);
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
                    // Row 1: Çal + Sıraya Ekle
                    { row: true, buttons: [
                        { icon: 'fa-play', label: 'Çal', action: 'play' },
                        { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' }
                    ]},
                    // Row 2: Favori + Puan Ver
                    { row: true, buttons: [
                        { icon: 'fa-heart', label: data.is_favorite ? 'Çıkar' : 'Favori', action: 'toggleFavorite' },
                        { icon: 'fa-star', label: 'Puan Ver', action: 'rate' }
                    ]},
                    { divider: true },
                    { icon: 'fa-list', label: 'Playliste Ekle', action: 'addToPlaylist', submenu: true },
                    { icon: 'fa-compact-disc', label: 'Albüme Git', action: 'goToAlbum' }
                ],
                album: [
                    // Row 1: Çal + Sıraya Ekle
                    { row: true, buttons: [
                        { icon: 'fa-play', label: 'Çal', action: 'play' },
                        { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' }
                    ]},
                    // Row 2: Favori + Puan Ver
                    { row: true, buttons: [
                        { icon: 'fa-heart', label: 'Favori', action: 'toggleFavorite' },
                        { icon: 'fa-star', label: 'Puan Ver', action: 'rate' }
                    ]},
                    { divider: true },
                    { icon: 'fa-list', label: 'Playliste Ekle (Tüm)', action: 'addToPlaylist' }
                ],
                playlist: [
                    // Row 1: Çal + Sıraya Ekle
                    { row: true, buttons: [
                        { icon: 'fa-play', label: 'Çal', action: 'play' },
                        { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' }
                    ]},
                    // Row 2: Favori + Puan Ver
                    { row: true, buttons: [
                        { icon: 'fa-heart', label: 'Favori', action: 'toggleFavorite' },
                        { icon: 'fa-star', label: 'Puan Ver', action: 'rate' }
                    ]},
                    { divider: true },
                    ...(data.is_mine ? [
                        // Kullanıcının playlistinde: Düzenle + Sil
                        { icon: 'fa-edit', label: 'Düzenle', action: 'edit' },
                        { icon: 'fa-trash-alt', label: 'Sil', action: 'delete' }
                    ] : [
                        // Sistem playlistinde: Kopyala
                        { icon: 'fa-copy', label: 'Playlistimi Kopyala', action: 'copy' }
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
});
