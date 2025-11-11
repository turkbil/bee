# 🎵 MUZIBU: Playlist Dual-List Şarkı Yönetim Sistemi

**Tarih:** 2025-11-11 18:30
**Modül:** Muzibu
**Özellik:** Playlist şarkı ekleme, çıkarma ve sıralama sistemi

---

## 📋 PROJE AÇIKLAMASI

Playlist'lere şarkı eklemek, çıkarmak ve sıralamak için **dual-list (iki kolonlu liste)** yönetim arayüzü geliştiriliyor.

---

## 🎯 KULLANICI İHTİYACI

**Sorun:**
- Playlist'lere şarkı ekleme mekanizması yok
- Şarkı sıralaması manuel yapılamıyor
- Toplu şarkı yönetimi zor

**Çözüm:**
- İki kolonlu liste arayüzü (Sol: Tüm şarkılar | Sağ: Playlist şarkıları)
- Real-time arama
- Drag & drop sıralama (SortableJS)
- AJAX ile anında güncelleme

---

## ✅ MEVCUT DURUM ANALİZİ

### Veritabanı Yapısı (Hazır)

**Pivot Table: `muzibu_playlist_song`**
```sql
CREATE TABLE muzibu_playlist_song (
    playlist_id BIGINT (FK → muzibu_playlists.playlist_id) CASCADE DELETE,
    song_id BIGINT (FK → muzibu_songs.song_id) CASCADE DELETE,
    position INT DEFAULT 0 COMMENT 'Sort order in playlist',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (playlist_id, song_id),
    INDEX (song_id),
    INDEX (position),
    INDEX (playlist_id, position)
);
```

### Model İlişkileri (Hazır)

**Playlist.php:119-130**
```php
public function songs()
{
    return $this->belongsToMany(
        Song::class,
        'muzibu_playlist_song',
        'playlist_id',
        'song_id',
        'playlist_id',
        'song_id'
    )->withPivot('position')->withTimestamps()->orderBy('muzibu_playlist_song.position');
}
```

**Song.php:141-151**
```php
public function playlists()
{
    return $this->belongsToMany(
        Playlist::class,
        'muzibu_playlist_song',
        'song_id',
        'playlist_id',
        'song_id',
        'playlist_id'
    )->withPivot('position')->withTimestamps();
}
```

### Frontend Kütüphaneler (Hazır)

- ✅ **SortableJS**: `/public/admin-assets/libs/sortable/sortable.min.js`
- ✅ **Alpine.js**: Zaten sistemde
- ✅ **Livewire**: Admin panelde aktif
- ✅ **Tabler.io**: Admin design system

---

## 🛠️ YAPILACAKLAR

### 1. Route Tanımı

**Dosya:** `Modules/Muzibu/routes/web.php`

```php
// Playlist şarkı yönetimi sayfası (GET)
Route::get('/playlist/{playlist_id}/songs', [PlaylistManageController::class, 'manageSongs'])
    ->name('admin.muzibu.playlist.songs');
```

**URL Örneği:** `/admin/muzibu/playlist/5/songs`

---

### 2. Livewire Component

**Dosya:** `Modules/Muzibu/app/Http/Livewire/Admin/PlaylistSongsManageComponent.php`

**Public Properties:**
```php
public int $playlistId;
public string $search = '';
public array $selectedSongIds = []; // Playlist'teki şarkılar (position order)
```

**Methods:**
```php
- mount($playlistId) // Initialize
- searchSongs() → Collection (computed) // Sol liste: Tüm şarkılar (arama filtreli)
- playlistSongs() → Collection (computed) // Sağ liste: Playlist'teki şarkılar (position order)
- addSong(int $songId) // Şarkı ekleme (position = max + 1)
- removeSong(int $songId) // Şarkı çıkarma
- reorderSongs(array $newOrder) // Sıralama güncelleme ([songId => position])
```

**AJAX Workflow:**
```php
// Ekleme
public function addSong(int $songId): void
{
    // 1. Validation: Song var mı?
    $song = Song::find($songId);
    if (!$song) {
        $this->dispatch('toast', ['type' => 'error', 'message' => 'Şarkı bulunamadı']);
        return;
    }

    // 2. Duplicate kontrolü
    $exists = $this->playlist->songs()->where('song_id', $songId)->exists();
    if ($exists) {
        $this->dispatch('toast', ['type' => 'warning', 'message' => 'Bu şarkı zaten playlist\'te']);
        return;
    }

    // 3. Position hesapla (max + 1)
    $maxPosition = $this->playlist->songs()->max('position') ?? 0;
    $newPosition = $maxPosition + 1;

    // 4. Pivot table'a ekle
    $this->playlist->songs()->attach($songId, ['position' => $newPosition]);

    // 5. Success feedback
    $this->dispatch('toast', ['type' => 'success', 'message' => 'Şarkı eklendi']);
}

// Çıkarma
public function removeSong(int $songId): void
{
    // 1. Pivot'tan sil
    $this->playlist->songs()->detach($songId);

    // 2. Sıralamayı yeniden düzenle (gap kalmasın)
    $this->reorderSequentially();

    // 3. Success feedback
    $this->dispatch('toast', ['type' => 'success', 'message' => 'Şarkı çıkarıldı']);
}

// Sıralama güncelleme
public function reorderSongs(array $newOrder): void
{
    // $newOrder = [songId => newPosition, ...]
    // Örnek: [12 => 0, 45 => 1, 23 => 2]

    DB::transaction(function () use ($newOrder) {
        foreach ($newOrder as $songId => $position) {
            DB::table('muzibu_playlist_song')
                ->where('playlist_id', $this->playlistId)
                ->where('song_id', $songId)
                ->update(['position' => $position]);
        }
    });

    $this->dispatch('toast', ['type' => 'success', 'message' => 'Sıralama güncellendi']);
}

// Helper: Sıralamayı düzelt (gap kalmasın)
private function reorderSequentially(): void
{
    $songs = $this->playlist->songs()->orderBy('position')->get();

    DB::transaction(function () use ($songs) {
        foreach ($songs as $index => $song) {
            DB::table('muzibu_playlist_song')
                ->where('playlist_id', $this->playlistId)
                ->where('song_id', $song->song_id)
                ->update(['position' => $index]);
        }
    });
}
```

---

### 3. View Dosyası (Dual-List Arayüz)

**Dosya:** `Modules/Muzibu/resources/views/admin/livewire/playlist-songs-manage-component.blade.php`

**Yapı:**
```html
<div class="container-xl py-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <h1>{{ $playlist->getTranslated('title') }} - Şarkı Yönetimi</h1>
        <a href="{{ route('admin.muzibu.playlist') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Geri
        </a>
    </div>

    <div class="row g-4">
        <!-- SOL KOLON: TÜM ŞARKILAR -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Şarkılar</h3>
                    <!-- Arama Input -->
                    <div class="ms-auto">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="form-control"
                               placeholder="Şarkı ara...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                        @forelse($this->searchSongs as $song)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong>{{ $song->getTranslated('title') }}</strong><br>
                                        <small class="text-muted">
                                            {{ $song->artist?->getTranslated('title') ?? 'Unknown' }}
                                            · {{ $song->getFormattedDuration() }}
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <button wire:click="addSong({{ $song->song_id }})"
                                                class="btn btn-sm btn-success"
                                                @if(in_array($song->song_id, $selectedSongIds)) disabled @endif>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Şarkı bulunamadı
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ KOLON: PLAYLIST ŞARKILARI -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Playlist Şarkıları
                        <span class="badge bg-blue ms-2">{{ count($this->playlistSongs) }}</span>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div id="sortable-playlist"
                         class="list-group list-group-flush"
                         style="max-height: 600px; overflow-y: auto;">
                        @forelse($this->playlistSongs as $index => $song)
                            <div class="list-group-item sortable-item"
                                 data-song-id="{{ $song->song_id }}">
                                <div class="row align-items-center">
                                    <!-- Drag Handle -->
                                    <div class="col-auto">
                                        <i class="fas fa-grip-vertical text-muted sortable-handle"
                                           style="cursor: grab;"></i>
                                    </div>
                                    <!-- Sıra Numarası -->
                                    <div class="col-auto">
                                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                    </div>
                                    <!-- Şarkı Bilgisi -->
                                    <div class="col">
                                        <strong>{{ $song->getTranslated('title') }}</strong><br>
                                        <small class="text-muted">
                                            {{ $song->artist?->getTranslated('title') ?? 'Unknown' }}
                                            · {{ $song->getFormattedDuration() }}
                                        </small>
                                    </div>
                                    <!-- Çıkar Butonu -->
                                    <div class="col-auto">
                                        <button wire:click="removeSong({{ $song->song_id }})"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Henüz şarkı eklenmedi
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableEl = document.getElementById('sortable-playlist');

    if (sortableEl) {
        Sortable.create(sortableEl, {
            handle: '.sortable-handle',
            animation: 150,
            onEnd: function(evt) {
                // Yeni sıralamayı topla
                const items = sortableEl.querySelectorAll('.sortable-item');
                const newOrder = {};

                items.forEach((item, index) => {
                    const songId = item.dataset.songId;
                    newOrder[songId] = index;
                });

                // Livewire'a gönder
                @this.reorderSongs(newOrder);
            }
        });
    }
});
</script>
@endpush
```

---

### 4. Playlist Listesine Link Ekle

**Dosya:** `Modules/Muzibu/resources/views/admin/livewire/playlist-component.blade.php:184-201`

**Değişiklik:** Dropdown menüye yeni item ekle:

```blade
<div class="dropdown-menu dropdown-menu-end">
    <!-- YENİ: Şarkıları Düzenle Linki -->
    <a href="{{ route('admin.muzibu.playlist.songs', $playlist->playlist_id) }}"
       class="dropdown-item">
        <i class="fas fa-music me-2"></i> Şarkıları Düzenle
    </a>

    <!-- Mevcut: Sil -->
    <a href="javascript:void(0);"
        wire:click="$dispatch('showDeleteModal', {...})"
        class="dropdown-item link-danger">
        {{ __('admin.delete') }}
    </a>
</div>
```

---

## 📊 KULLANICI AKIŞI

### Senaryo 1: Şarkı Ekleme

1. **Kullanıcı:** Playlist listesinde "İşlemler → Şarkıları Düzenle" tıklar
2. **Sistem:** `/admin/muzibu/playlist/5/songs` sayfasına yönlendirir
3. **Kullanıcı:** Sol listede şarkı arar ("Black Heart")
4. **Kullanıcı:** Şarkının yanındaki "+" butonuna tıklar
5. **Sistem:** AJAX ile `addSong(12)` çalıştırır
6. **Sistem:** Pivot table'a ekler: `(playlist_id: 5, song_id: 12, position: 3)`
7. **Sistem:** Sağ listeyi günceller (Livewire refresh)
8. **Kullanıcı:** Toast notification görür: "Şarkı eklendi"

### Senaryo 2: Şarkı Çıkarma

1. **Kullanıcı:** Sağ listede şarkının yanındaki "×" butonuna tıklar
2. **Sistem:** AJAX ile `removeSong(12)` çalıştırır
3. **Sistem:** Pivot table'dan siler
4. **Sistem:** Sıralamayı yeniden düzenler (gap kalmasın)
5. **Kullanıcı:** Toast notification görür: "Şarkı çıkarıldı"

### Senaryo 3: Sıralama Değiştirme (Drag & Drop)

1. **Kullanıcı:** Sağ listede şarkıyı sürükler (⋮⋮ handle ile)
2. **Kullanıcı:** Yeni konuma bırakır (örn: 3. → 1.)
3. **Sistem:** SortableJS `onEnd` event tetiklenir
4. **Sistem:** Yeni sıralama hesaplanır: `{12: 0, 45: 1, 23: 2}`
5. **Sistem:** AJAX ile `reorderSongs(...)` çalıştırır
6. **Sistem:** Pivot table'daki `position` alanları güncellenir
7. **Kullanıcı:** Toast notification görür: "Sıralama güncellendi"

---

## 🎨 UI/UX DETAYLARİ

### Tabler.io Design Standartları

- **Kartlar:** `.card` + `.card-header` + `.card-body`
- **Liste:** `.list-group` + `.list-group-item`
- **Butonlar:** `.btn-sm` + `.btn-success` (ekle), `.btn-outline-danger` (çıkar)
- **Badge:** `.badge.bg-blue` (şarkı sayısı)
- **Loading:** `wire:loading` ile spinner göster

### Responsive

- **Desktop:** İki kolon yan yana
- **Mobile:** Kolonlar alt alta geçer (Bootstrap grid)

### Accessibility

- **Keyboard:** Tab ile navigasyon
- **Screen Reader:** ARIA labels ekle
- **Contrast:** WCAG AA standardı

---

## 🔒 GÜVENLİK

### Authorization

```php
// PlaylistSongsManageComponent.php
public function mount($playlistId): void
{
    // Playlist var mı?
    $this->playlist = Playlist::findOrFail($playlistId);

    // Kullanıcı yetkili mi?
    if (!auth()->user()->can('update', $this->playlist)) {
        abort(403, 'Yetkiniz yok');
    }
}
```

### Validation

```php
// addSong method
public function addSong(int $songId): void
{
    // Song var mı?
    $song = Song::active()->find($songId);
    if (!$song) {
        throw new \Exception('Geçersiz şarkı ID');
    }

    // Duplicate?
    if ($this->playlist->songs()->where('song_id', $songId)->exists()) {
        $this->dispatch('toast', ['type' => 'warning', 'message' => 'Bu şarkı zaten eklendi']);
        return;
    }

    // ...
}
```

### Transaction

```php
// Sıralama güncellemesi atomic olmalı
DB::transaction(function () use ($newOrder) {
    foreach ($newOrder as $songId => $position) {
        // Update pivot
    }
});
```

---

## ✅ TEST SENARYOLARI

### 1. Şarkı Ekleme
- [ ] Arama çalışıyor mu? (real-time filter)
- [ ] Şarkı pivot table'a ekleniyor mu?
- [ ] Position doğru hesaplanıyor mu? (max + 1)
- [ ] Duplicate kontrolü çalışıyor mu?
- [ ] Toast notification gösteriliyor mu?

### 2. Şarkı Çıkarma
- [ ] Pivot table'dan siliniyor mu?
- [ ] Sıralama otomatik düzenleniyor mu?
- [ ] Toast notification gösteriliyor mu?

### 3. Sıralama
- [ ] Drag & drop çalışıyor mu?
- [ ] Position alanları güncelleniyor mu?
- [ ] Sayfa yenilenince sıralama korunuyor mu?
- [ ] Transaction rollback çalışıyor mu? (hata durumunda)

### 4. Edge Cases
- [ ] Boş playlist (şarkı yok)
- [ ] Çok fazla şarkı (1000+) - scroll çalışıyor mu?
- [ ] Eşzamanlı işlem (2 kullanıcı aynı anda)
- [ ] Network hatası (AJAX timeout)

---

## 📈 PERFORMANS

### Optimizasyon

```php
// Eager loading (N+1 sorunu çözümü)
public function playlistSongs()
{
    return $this->playlist
        ->songs()
        ->with(['artist', 'album', 'genre']) // Eager load
        ->orderBy('muzibu_playlist_song.position')
        ->get();
}

// Pagination (çok şarkı varsa)
public function searchSongs()
{
    return Song::active()
        ->with(['artist', 'album'])
        ->when($this->search, function($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('artist', fn($q) =>
                      $q->where('title', 'like', '%' . $this->search . '%')
                  );
        })
        ->limit(100) // Max 100 şarkı göster
        ->get();
}
```

### Caching

```php
// Playlist şarkılarını cache'le
public function playlistSongs()
{
    return Cache::remember(
        "playlist_{$this->playlistId}_songs",
        3600,
        fn() => $this->playlist->songs()->with('artist')->get()
    );
}

// Cache invalidation
public function addSong(int $songId): void
{
    $this->playlist->songs()->attach($songId, ['position' => $newPosition]);

    // Cache'i temizle
    Cache::forget("playlist_{$this->playlistId}_songs");
}
```

---

## 🚀 DEPLOYMENT

### Checklist

- [ ] Migration çalıştırıldı mı? (pivot table zaten var)
- [ ] Route tanımlandı mı?
- [ ] Livewire component oluşturuldu mu?
- [ ] View dosyası oluşturuldu mu?
- [ ] SortableJS script eklendi mi?
- [ ] Playlist listesine link eklendi mi?
- [ ] Cache temizlendi mi? (`php artisan view:clear`)
- [ ] Build compile edildi mi? (`npm run prod`)
- [ ] Test edildi mi? (canlı sistemde)

---

## 📝 DOSYA YAPISI

```
Modules/Muzibu/
├── app/
│   └── Http/
│       └── Livewire/
│           └── Admin/
│               └── PlaylistSongsManageComponent.php ← YENİ
├── resources/
│   └── views/
│       └── admin/
│           └── livewire/
│               ├── playlist-component.blade.php ← GÜNCELLE (link ekle)
│               └── playlist-songs-manage-component.blade.php ← YENİ
├── routes/
│   └── web.php ← GÜNCELLE (route ekle)
└── database/
    └── migrations/
        └── tenant/
            └── 2025_11_09_000008_create_muzibu_playlist_song_table.php ← MEVCUT
```

---

## 🎯 ÖZET

**Yapılacak İşler:**
1. ✅ Route tanımlama
2. ✅ Livewire Component yazma
3. ✅ View oluşturma (dual-list)
4. ✅ SortableJS entegrasyonu
5. ✅ Playlist listesine link ekleme
6. ✅ Cache clear + Build
7. ✅ Test

**Tahmini Süre:** 2-3 saat

**Risk:** Düşük (veritabanı yapısı hazır, kütüphaneler mevcut)

---

**SON GÜNCELLEME:** 2025-11-11 18:30
**DURUM:** Plan hazır, onay bekleniyor 🚀
