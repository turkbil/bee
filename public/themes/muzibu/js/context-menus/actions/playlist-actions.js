/**
 * 🎵 PLAYLIST ACTIONS HANDLER
 */
const PlaylistActions = {
    async play(data) {
        if (window.playPlaylist) await window.playPlaylist(data.id);
        else if (window.playContent) await window.playContent('playlist', data.id);
    },

    async addToQueue(data) {
        if (window.addContentToQueue) await window.addContentToQueue('playlist', data.id);
    },

    async toggleFavorite(data) {
        const store = Alpine.store('favorites');
        if (store) await store.toggle('playlist', data.id);
    },

    edit(data) {
        // Slug varsa slug kullan, yoksa id kullan
        const identifier = data.slug || data.id;
        if (identifier) window.location.href = `/muzibu/playlist/${identifier}/edit`;
    },

    delete(data) {
        // Alpine confirmModal store kullan
        const confirmModal = Alpine.store('confirmModal');
        if (confirmModal) {
            confirmModal.show({
                title: 'Playlist Sil',
                message: `"${data.title}" playlist'ini silmek istediğinizden emin misiniz?`,
                confirmText: 'Sil',
                cancelText: 'Vazgeç',
                type: 'danger',
                onConfirm: async () => {
                    // 🎯 Card elementi bul
                    const card = document.querySelector(`[data-playlist-id="${data.id}"][data-context-type="my-playlist"]`);

                    if (card) {
                        // 🔄 Loading state ekle
                        card.style.position = 'relative';
                        card.style.pointerEvents = 'none';
                        card.style.opacity = '0.6';

                        // 🌀 Loading spinner ekle
                        const spinner = document.createElement('div');
                        spinner.className = 'absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm rounded-lg z-50';
                        spinner.innerHTML = '<i class="fas fa-spinner fa-spin text-4xl text-muzibu-coral"></i>';
                        card.appendChild(spinner);
                    }

                    try {
                        const response = await fetch(`/api/muzibu/playlists/${data.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
                        });

                        if (response.ok) {
                            Alpine.store('toast')?.show('Playlist silindi', 'success');

                            // ✨ SPA: Card'ı DOM'dan kaldır (sayfa yenilenmeden)
                            if (card) {
                                // Fade out animasyonu
                                card.style.transition = 'all 0.3s ease-out';
                                card.style.transform = 'scale(0.8)';
                                card.style.opacity = '0';

                                setTimeout(() => {
                                    card.remove();

                                    // Grid'de hiç card kalmadıysa empty state göster
                                    const grid = document.querySelector('.grid');
                                    if (grid && grid.querySelectorAll('[data-context-type="my-playlist"]').length === 0) {
                                        window.location.reload(); // Empty state için reload
                                    }
                                }, 300);
                            }
                        } else {
                            // ❌ Hata: Loading'i kaldır
                            if (card) {
                                card.style.opacity = '1';
                                card.style.pointerEvents = 'auto';
                                const spinner = card.querySelector('.absolute.inset-0');
                                if (spinner) spinner.remove();
                            }
                            Alpine.store('toast')?.show('Playlist silinemedi', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        // ❌ Hata: Loading'i kaldır
                        if (card) {
                            card.style.opacity = '1';
                            card.style.pointerEvents = 'auto';
                            const spinner = card.querySelector('.absolute.inset-0');
                            if (spinner) spinner.remove();
                        }
                        Alpine.store('toast')?.show('Bir hata oluştu', 'error');
                    }
                }
            });
        }
    },

    async execute(action, data) {
        if (this[action]) await this[action](data);
    }
};
window.PlaylistActions = PlaylistActions;
