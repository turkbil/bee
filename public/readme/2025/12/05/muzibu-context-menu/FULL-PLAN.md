# 🎵 Muzibu Context Menu Sistemi - Detaylı Master Plan

**Tarih:** 05 Aralık 2025
**Tenant:** 1001 (muzibu.com.tr)
**Teknoloji:** Alpine.js + Tailwind CSS + Laravel

---

## 📊 Executive Summary

Muzibu.com.tr için **sağ tık (context menu) sistemi** geliştirilecek. Kullanıcılar şarkı, albüm, playlist, tür ve sektör içeriklerine sağ tıklayarak hızlı işlemler yapabilecek:

- ✅ **Favorilere Ekleme** (Mevcut Favorite modülü)
- ✅ **Puan Verme** (Mevcut ReviewSystem modülü)
- 🆕 **Playliste Ekleme** (Yeni özellik)
- 🆕 **Universal Context Menu** (6 içerik tipi desteği)

**Tahmini Süre:** 2-3 gün
**Adım Sayısı:** 9 adım (5 phase)

---

## 🎯 Phase 1: Altyapı

### Adım 1: Alpine Store Oluştur

**Dosya:** `public/themes/muzibu/js/context-menu-store.js`

```javascript
// Alpine Store - Global State Management
document.addEventListener('alpine:init', () => {
    Alpine.store('contextMenu', {
        // State
        visible: false,
        x: 0,
        y: 0,
        type: null,  // song | album | playlist | genre | sector | review
        data: null,  // Content object { id, title, ... }
        actions: [],

        // Methods
        show(type, data, mouseX, mouseY) {
            this.type = type;
            this.data = data;
            this.actions = this.getActionsForType(type, data);

            // Smart positioning (ekran sınır kontrolü)
            const menuWidth = 250;
            const menuHeight = this.actions.length * 45;

            this.x = (mouseX + menuWidth > window.innerWidth)
                ? window.innerWidth - menuWidth - 10
                : mouseX;

            this.y = (mouseY + menuHeight > window.innerHeight)
                ? window.innerHeight - menuHeight - 10
                : mouseY;

            this.visible = true;
        },

        hide() {
            this.visible = false;
            setTimeout(() => {
                this.type = null;
                this.data = null;
                this.actions = [];
            }, 200);
        },

        getActionsForType(type, data) {
            const actions = {
                song: [
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle', action: 'addToQueue' },
                    { icon: 'fa-heart', label: 'Favorilere Ekle', action: 'toggleFavorite' },
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    { divider: true },
                    { icon: 'fa-list-music', label: 'Playliste Ekle', action: 'addToPlaylist', submenu: true },
                    { divider: true },
                    { icon: 'fa-compact-disc', label: 'Albüme Git', action: 'goToAlbum' },
                    { icon: 'fa-user-music', label: 'Sanatçıya Git', action: 'goToArtist' },
                    { icon: 'fa-share-alt', label: 'Paylaş', action: 'share' }
                ],
                album: [
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle (Tüm)', action: 'addToQueue' },
                    { icon: 'fa-heart', label: 'Favorilere Ekle', action: 'toggleFavorite' },
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    { icon: 'fa-list-music', label: 'Playliste Ekle (Tüm)', action: 'addToPlaylist' },
                    { divider: true },
                    { icon: 'fa-user-music', label: 'Sanatçıya Git', action: 'goToArtist' },
                    { icon: 'fa-share-alt', label: 'Paylaş', action: 'share' }
                ],
                playlist: [
                    { icon: 'fa-play', label: 'Çal', action: 'play' },
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle (Tüm)', action: 'addToQueue' },
                    { icon: 'fa-heart', label: 'Favorilere Ekle', action: 'toggleFavorite' },
                    { icon: 'fa-star', label: 'Puan Ver', action: 'rate' },
                    { divider: true },
                    ...(data.is_mine ? [
                        { icon: 'fa-edit', label: 'Düzenle', action: 'edit' },
                        { icon: 'fa-trash-alt', label: 'Sil', action: 'delete' }
                    ] : []),
                    { icon: 'fa-share-alt', label: 'Paylaş', action: 'share' }
                ],
                genre: [
                    { icon: 'fa-play', label: 'Çal (Tüm)', action: 'play' },
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle (Tüm)', action: 'addToQueue' },
                    { icon: 'fa-heart', label: 'Favorilere Ekle', action: 'toggleFavorite' },
                    { icon: 'fa-list-music', label: 'Playliste Ekle (Tüm)', action: 'addToPlaylist' },
                    { icon: 'fa-share-alt', label: 'Paylaş', action: 'share' }
                ],
                sector: [
                    { icon: 'fa-play', label: 'Çal (Tüm)', action: 'play' },
                    { icon: 'fa-plus-circle', label: 'Sıraya Ekle (Tüm)', action: 'addToQueue' },
                    { icon: 'fa-heart', label: 'Favorilere Ekle', action: 'toggleFavorite' },
                    { icon: 'fa-list-music', label: 'Playliste Ekle (Tüm)', action: 'addToPlaylist' },
                    { icon: 'fa-share-alt', label: 'Paylaş', action: 'share' }
                ]
            };

            return actions[type] || [];
        }
    });
});
```

**Beklenen Sonuç:**
✅ Global Alpine Store hazır
✅ Smart positioning çalışıyor
✅ İçerik tipine göre action listesi dinamik

---

### Adım 2: Context Menu Component

**Dosya:** `resources/views/components/muzibu/context-menu.blade.php`

```blade
{{-- Universal Context Menu Component --}}
<div x-data
     x-show="$store.contextMenu.visible"
     x-cloak
     @click.outside="$store.contextMenu.hide()"
     @keydown.escape.window="$store.contextMenu.hide()"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     :style="`position: fixed; left: ${$store.contextMenu.x}px; top: ${$store.contextMenu.y}px; z-index: 9999;`"
     class="bg-zinc-800 rounded-lg shadow-2xl border border-white/10 py-2 min-w-[250px]">

    <template x-for="(action, index) in $store.contextMenu.actions" :key="index">
        <div>
            {{-- Divider --}}
            <div x-show="action.divider" class="border-t border-white/10 my-2"></div>

            {{-- Action Item --}}
            <button x-show="!action.divider"
                    @click="executeAction(action.action); $store.contextMenu.hide()"
                    class="w-full px-4 py-2.5 text-left hover:bg-white/10 transition-colors flex items-center gap-3 text-white text-sm">
                <i :class="`fas ${action.icon} w-5`"></i>
                <span x-text="action.label"></span>
                <i x-show="action.submenu" class="fas fa-chevron-right ml-auto text-xs"></i>
            </button>
        </div>
    </template>
</div>

<script>
function executeAction(action) {
    const { type, data } = Alpine.store('contextMenu');

    switch(action) {
        case 'play':
            playSong(data.id);
            break;
        case 'addToQueue':
            addToQueue(data.id);
            break;
        case 'toggleFavorite':
            toggleFavorite(type, data.id);
            break;
        case 'rate':
            openRatingModal(type, data.id);
            break;
        case 'addToPlaylist':
            openPlaylistSelectModal(type, data.id);
            break;
        case 'share':
            shareContent(type, data);
            break;
        case 'goToAlbum':
            window.location.href = data.album_url;
            break;
        case 'goToArtist':
            window.location.href = data.artist_url;
            break;
        case 'edit':
            window.location.href = data.edit_url;
            break;
        case 'delete':
            if(confirm('Silmek istediğinize emin misiniz?')) {
                deleteContent(type, data.id);
            }
            break;
    }
}
</script>
```

**Beklenen Sonuç:**
✅ Context menu görsel olarak çalışıyor
✅ Smooth transitions
✅ Action execution routing

---

### Adım 3: Global Context Menu Handler

**Konum:** `resources/views/themes/muzibu/layouts/app.blade.php`

```blade
<script>
// Global Context Menu Handler
document.addEventListener('DOMContentLoaded', function() {
    // Prevent browser context menu (except inputs)
    document.addEventListener('contextmenu', function(e) {
        // Allow browser menu for inputs (paste)
        if (e.target.matches('input, textarea, [contenteditable="true"]')) {
            return true;
        }

        // Check if element has data-contextmenu attribute
        const target = e.target.closest('[data-contextmenu]');
        if (target) {
            e.preventDefault();

            const type = target.dataset.contextmenu;
            const data = JSON.parse(target.dataset.contextmenuData || '{}');

            Alpine.store('contextMenu').show(type, data, e.pageX, e.pageY);
        }
    });
});
</script>
```

**Beklenen Sonuç:**
✅ Tarayıcı context menu engellenmiş
✅ data-contextmenu attribute ile tetiklenme
✅ Input/textarea için paste hala çalışıyor

---

## 🎯 Phase 2: Modals

### Adım 4: Rating Modal Component

**Dosya:** `resources/views/components/muzibu/rating-modal.blade.php`

```blade
{{-- Rating Modal - 5 Yıldız + Yorum --}}
<div x-data="{
    open: false,
    type: null,
    id: null,
    rating: 0,
    comment: '',
    loading: false,

    show(type, id, currentRating = 0) {
        this.type = type;
        this.id = id;
        this.rating = currentRating;
        this.comment = '';
        this.open = true;
    },

    async submitRating() {
        if (this.rating === 0) {
            alert('Lütfen puan verin');
            return;
        }

        this.loading = true;

        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reviewable_type: this.type,
                    reviewable_id: this.id,
                    rating_value: this.rating,
                    review_body: this.comment
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast('✅ Puanınız kaydedildi', 'success');
                this.open = false;
                window.location.reload();  // Refresh to show new rating
            } else {
                showToast('❌ ' + (data.message || 'Bir hata oluştu'), 'error');
            }
        } catch (error) {
            showToast('❌ Bağlantı hatası', 'error');
        } finally {
            this.loading = false;
        }
    }
}"
     @open-rating-modal.window="show($event.detail.type, $event.detail.id, $event.detail.currentRating)"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div @click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div class="relative bg-zinc-800 rounded-lg shadow-2xl border border-white/10 max-w-md w-full p-6">
        <h3 class="text-2xl font-bold text-white mb-6">⭐ Puan Ver</h3>

        {{-- Star Rating --}}
        <div class="flex items-center justify-center gap-2 mb-6">
            <template x-for="star in [1,2,3,4,5]" :key="star">
                <button @click="rating = star"
                        @mouseover="rating = star"
                        type="button"
                        class="text-4xl transition-all duration-200 hover:scale-110">
                    <i :class="star <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-500'"></i>
                </button>
            </template>
        </div>

        <p class="text-center text-slate-300 mb-6" x-show="rating > 0">
            <span x-text="rating"></span> yıldız seçtiniz
        </p>

        {{-- Comment (Optional) --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Yorumunuz (opsiyonel)
            </label>
            <textarea x-model="comment"
                      rows="4"
                      class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-muzibu-coral transition-colors"
                      placeholder="Bu içerik hakkında düşünceleriniz..."></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button @click="open = false"
                    type="button"
                    class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                İptal
            </button>
            <button @click="submitRating()"
                    :disabled="loading || rating === 0"
                    :class="loading || rating === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    type="button"
                    class="flex-1 px-4 py-3 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-lg transition-colors font-semibold">
                <span x-show="!loading">Puanı Gönder</span>
                <span x-show="loading">Gönderiliyor...</span>
            </button>
        </div>
    </div>
</div>
```

**Beklenen Sonuç:**
✅ 5 yıldız interactive UI
✅ Opsiyonel yorum
✅ API ile entegrasyon
✅ Toast notification

---

### Adım 5: Playlist Select Modal

**Dosya:** `resources/views/components/muzibu/playlist-select-modal.blade.php`

```blade
{{-- Playlist Select Modal --}}
<div x-data="{
    open: false,
    type: null,
    id: null,
    playlists: [],
    loading: false,

    async show(type, id) {
        this.type = type;
        this.id = id;
        this.open = true;
        await this.loadPlaylists();
    },

    async loadPlaylists() {
        this.loading = true;
        try {
            const response = await fetch('/api/muzibu/my-playlists', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.playlists = data.data || [];
        } catch (error) {
            showToast('❌ Playlistler yüklenemedi', 'error');
        } finally {
            this.loading = false;
        }
    },

    async addToPlaylist(playlistId) {
        try {
            const response = await fetch(\`/api/muzibu/playlists/\${playlistId}/songs\`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ song_id: this.id })
            });

            const data = await response.json();

            if (data.success) {
                showToast('✅ Playliste eklendi', 'success');
                this.open = false;
            } else {
                showToast('❌ ' + (data.message || 'Bir hata oluştu'), 'error');
            }
        } catch (error) {
            showToast('❌ Bağlantı hatası', 'error');
        }
    }
}"
     @open-playlist-select-modal.window="show($event.detail.type, $event.detail.id)"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div @click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div class="relative bg-zinc-800 rounded-lg shadow-2xl border border-white/10 max-w-2xl w-full p-6">
        <h3 class="text-2xl font-bold text-white mb-6">🎵 Playliste Ekle</h3>

        {{-- Create New Playlist Button --}}
        <button @click="$dispatch('open-create-playlist-modal'); open = false"
                class="w-full mb-4 px-4 py-3 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-lg transition-colors font-semibold flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i>
            Yeni Playlist Oluştur
        </button>

        {{-- Loading State --}}
        <div x-show="loading" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-muzibu-coral"></i>
            <p class="text-slate-400 mt-4">Playlistler yükleniyor...</p>
        </div>

        {{-- Playlists Grid --}}
        <div x-show="!loading && playlists.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
            <template x-for="playlist in playlists" :key="playlist.playlist_id">
                <button @click="addToPlaylist(playlist.playlist_id)"
                        class="group bg-zinc-900 hover:bg-zinc-700 rounded-lg p-4 transition-all duration-300 text-left">
                    <div class="aspect-square bg-gradient-to-br from-muzibu-coral via-purple-600 to-blue-600 rounded-lg mb-3 flex items-center justify-center">
                        <i class="fas fa-list-music text-white text-2xl opacity-50"></i>
                    </div>
                    <h4 class="text-white font-semibold truncate" x-text="playlist.title"></h4>
                    <p class="text-xs text-slate-400" x-text="playlist.songs_count + ' şarkı'"></p>
                </button>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && playlists.length === 0" class="text-center py-8">
            <i class="fas fa-list-music text-4xl text-gray-600 mb-4"></i>
            <p class="text-slate-400 mb-4">Henüz playlist oluşturmadınız</p>
            <button @click="$dispatch('open-create-playlist-modal'); open = false"
                    class="px-6 py-2 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-lg transition-colors">
                İlk Playlist'inizi Oluşturun
            </button>
        </div>

        {{-- Close Button --}}
        <button @click="open = false"
                class="mt-6 w-full px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
            Kapat
        </button>
    </div>
</div>
```

**Beklenen Sonuç:**
✅ Kullanıcının playlistleri gösteriliyor
✅ Playliste ekleme çalışıyor
✅ "Yeni Playlist Oluştur" butonu
✅ Empty state handling

---

## 🎯 Devamı için tam HTML raporuna bakınız

Bu Markdown dosyası implementation detaylarını içerir.
**Tam görsel rapor:** https://ixtif.com/readme/2025/12/05/muzibu-context-menu/

---

**Sonraki Adımlar:**
- Adım 6: Action Handlers JS
- Adım 7: View Entegrasyonları
- Adım 8: Layout Güncellemesi
- Adım 9: Test & Optimization

**API Endpoints**, **Örnek Senaryolar**, **Güvenlik**, **Mobil Uyumluluk** ve **Future Roadmap** bölümleri HTML raporunda detaylı olarak yer almaktadır.
