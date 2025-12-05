/**
 * Context Menu Initialization
 * SPA + instant.page uyumlu, native event listener yaklaşımı
 * Blade rendering sorunlarını bypass eder
 */

function initContextMenus() {
    // Playlist kartları
    const playlistCards = document.querySelectorAll('.playlist-card');

    playlistCards.forEach(el => {
        // Desktop: Right-click
        el.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const data = {
                id: parseInt(el.dataset.playlistId),
                title: el.dataset.playlistTitle,
                is_favorite: el.dataset.isFavorite === '1',
                is_mine: el.dataset.isMine === '1'
            };

            Alpine.store('contextMenu').openContextMenu(e, 'playlist', data);
        });
        
        // Mobile: Long-press (500ms)
        let touchTimer = null;
        let touchStartPos = { x: 0, y: 0 };
        
        el.addEventListener('touchstart', (e) => {
            touchStartPos = {
                x: e.touches[0].clientX,
                y: e.touches[0].clientY
            };
            
            touchTimer = setTimeout(() => {
                // Titreşim feedback
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }
                
                const data = {
                    id: parseInt(el.dataset.playlistId),
                    title: el.dataset.playlistTitle,
                    is_favorite: el.dataset.isFavorite === '1',
                    is_mine: el.dataset.isMine === '1'
                };

                Alpine.store('contextMenu').openContextMenu({
                    clientX: touchStartPos.x,
                    clientY: touchStartPos.y,
                    preventDefault: () => {}
                }, 'playlist', data);
            }, 500);
        });
        
        el.addEventListener('touchmove', (e) => {
            // Hareket ettiyse iptal et
            const deltaX = Math.abs(e.touches[0].clientX - touchStartPos.x);
            const deltaY = Math.abs(e.touches[0].clientY - touchStartPos.y);
            
            if (deltaX > 10 || deltaY > 10) {
                clearTimeout(touchTimer);
            }
        });
        
        el.addEventListener('touchend', () => {
            clearTimeout(touchTimer);
        });
    });
    
    // Album kartları
    const albumCards = document.querySelectorAll('.album-card');

    albumCards.forEach(el => {
        el.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const data = {
                id: parseInt(el.dataset.albumId),
                title: el.dataset.albumTitle,
                is_favorite: el.dataset.isFavorite === '1'
            };
            
            Alpine.store('contextMenu').openContextMenu(e, 'album', data);
        });
    });
    
    // Song kartları
    const songCards = document.querySelectorAll('.song-card');

    songCards.forEach(el => {
        el.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const data = {
                id: parseInt(el.dataset.songId),
                title: el.dataset.songTitle,
                is_favorite: el.dataset.isFavorite === '1'
            };
            
            Alpine.store('contextMenu').openContextMenu(e, 'song', data);
        });
    });
}

// DOM ready sonrası init
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContextMenus);
} else {
    // Zaten yüklenmişse direkt çalıştır
    initContextMenus();
}

// Livewire navigation sonrası re-init (sessiz)
document.addEventListener('livewire:navigated', initContextMenus);

// SPA route değişikliği sonrası re-init (sessiz)
window.addEventListener('route-changed', initContextMenus);
