# Muzibu Tema - Kalan İşler TODO

**Tarih:** 2025-11-24 22:59
**Tenant:** muzibu.com (ID: 1001)
**Tema:** Spotify-Style Müzik Platformu

---

## ✅ TAMAMLANAN

### UI/UX Tasarım
- [x] Spotify-style layout (sidebar + topbar + player)
- [x] Hero carousel (3 slide, otomatik kayma)
- [x] Horizontal scroll sections (Jump Back In, New Albums, Popular Playlists)
- [x] Context menus (sağ tık menüleri)
- [x] Favorite UI (kalp ikonları, localStorage ile)
- [x] Player UI (play/pause, prev/next, shuffle, repeat, progress bar, volume)
- [x] Guest vs Member content
- [x] Dark mode toggle

### Auth System
- [x] Theme-aware auth controllers
- [x] Muzibu auth pages (login, register, forgot-password, reset-password)
- [x] İxtif auth pages (login, register, forgot-password, reset-password)
- [x] Dark/Light mode toggle
- [x] Fallback system (default Laravel auth)

---

## 🔥 KRİTİK ÖNCELİK (Kullanıcı Söyledikçe Yapılacak)

### 1. SPA Navigation
- [ ] Client-side routing (window.history.pushState)
- [ ] AJAX ile içerik yükleme (sayfa reload yok)
- [ ] Player state persistence (müzik kesintisiz)
- [ ] Back/Forward browser butonları desteği

**Dosyalar:**
- `resources/views/themes/muzibu/layouts/app.blade.php` - Alpine.js routing ekle
- `public/themes/muzibu/js/spa-router.js` - Yeni dosya (router logic)

---

### 2. Gerçek Müzik Playback (HLS)
- [ ] HLS.js entegrasyonu (audio element'e bağla)
- [ ] Play/Pause fonksiyonlarını gerçek audio'ya bağla
- [ ] Şarkı değiştiğinde yeni stream yükleme
- [ ] Hata yönetimi (stream bulunamadı, network error)
- [ ] Loading durumu gösterme
- [ ] MP3 fallback (HLS yoksa direkt MP3)

**Dosyalar:**
- `resources/views/themes/muzibu/components/player.blade.php` - muzibuApp() güncelle
- `Modules/Muzibu/app/Models/MuzibuSong.php` - getStreamUrl() method ekle

---

### 3. Database Migrations
- [x] Mevcut migration'ları kontrol et ✅ YAPILDI
- [x] Tenant migrations çalıştır: `php artisan tenants:migrate` ✅ YAPILDI
- [x] Central migrations çalıştır: `php artisan migrate` ✅ YAPILDI
- [x] Seed data oluştur (demo müzikler) ✅ YAPILDI

**Demo Veri Özeti (Tenant 1001 - muzibu.com):**
- 6 Tür (Pop, Rock, Elektronik, Caz, Klasik, Türk Halk Müziği)
- 5 Sektör (Kafe & Restoran, Otel, AVM, Spor Salonu, Ofis)
- 10 Sanatçı
- 10 Albüm
- 14 Şarkı (MP3'ler: `/var/www/vhosts/tuufi.com/httpdocs/readme/1-muzibu-examples/mp3/`)
- 10 Playlist (Sabah Kahvesi, Gece Vakti, Kafe Ambiyansı, vb.)
- 3 Demo Kullanıcı (demo1@muzibu.com, demo2@muzibu.com, demo3@muzibu.com - şifre: `password`)
- 100 Dinleme Kaydı

**Dosyalar:**
- `Modules/Muzibu/database/migrations/*.php` - Kontrol
- `Modules/Muzibu/database/seeders/MuzibuSeeder.php` - Yeni (demo data)

**Komutlar:**
```bash
php artisan tenants:migrate
php artisan migrate
php artisan db:seed --class=Modules\\Muzibu\\database\\seeders\\MuzibuSeeder
```

---

### 4. Backend API Endpoints
- [ ] `GET /api/playlists` - Playlist listesi
- [ ] `GET /api/playlists/{id}` - Playlist detayı (şarkılarla)
- [ ] `GET /api/albums` - Albüm listesi
- [ ] `GET /api/albums/{id}` - Albüm detayı
- [ ] `GET /api/songs/recent` - Son dinlenenler
- [ ] `GET /api/genres` - Tür listesi
- [ ] `GET /api/sectors` - Sektör listesi
- [ ] Tenant-aware (her tenant kendi verisi)
- [ ] Pagination (20-50 kayıt/sayfa)

**Dosyalar:**
- `Modules/Muzibu/routes/api.php` - Route tanımları
- `Modules/Muzibu/app/Http/Controllers/Api/PlaylistController.php` - Yeni
- `Modules/Muzibu/app/Http/Controllers/Api/AlbumController.php` - Yeni
- `Modules/Muzibu/app/Http/Controllers/Api/SongController.php` - Yeni

---

### 5. Volume Normalization
- [ ] Web Audio API ile GainNode oluştur
- [ ] Her şarkının loudness değerini DB'de sakla
- [ ] Şarkı değiştiğinde GainNode ayarla
- [ ] Hedef: -14 LUFS (Spotify standardı)
- [ ] User volume + normalization birleşimi

**Dosyalar:**
- `resources/views/themes/muzibu/components/player.blade.php` - Web Audio API ekle
- `Modules/Muzibu/database/migrations/*_add_loudness_to_songs.php` - Yeni migration

---

## 🔧 YÜKSEK ÖNCELİK (Sonraki Aşamalar)

### 6. Playlist Detail Page
- [ ] Playlist header (cover, isim, açıklama)
- [ ] Şarkı listesi (sıra no, kapak, isim, artist, süre)
- [ ] Play butonu (playlist'i çalmaya başla)
- [ ] Her şarkı için context menu
- [ ] Hover efekti (play butonu)

**Dosyalar:**
- `resources/views/themes/muzibu/playlist/show.blade.php` - Yeni
- `Modules/Muzibu/routes/web.php` - Route ekle

---

### 7. Album Detail Page
- [ ] Album header (cover, isim, artist, yıl)
- [ ] Şarkı listesi (track numarası)
- [ ] Artist bilgisi (link)
- [ ] Play all butonu

**Dosyalar:**
- `resources/views/themes/muzibu/album/show.blade.php` - Yeni

---

### 8. Queue Management
- [ ] Sağ tarafta açılır Queue sidebar
- [ ] Şu an çalan şarkı vurgulu
- [ ] Sıradaki şarkılar listesi
- [ ] Drag & drop ile sıralama
- [ ] Şarkıyı sıradan çıkarma
- [ ] Queue'yu temizleme butonu

**Dosyalar:**
- `resources/views/themes/muzibu/components/queue-sidebar.blade.php` - Yeni
- `resources/views/themes/muzibu/layouts/app.blade.php` - Queue sidebar include

---

### 9. Search Functionality
- [ ] Meilisearch kurulum kontrolü
- [ ] Laravel Scout konfigürasyonu
- [ ] Song/Album/Playlist/Artist indexleme
- [ ] API endpoint: `GET /api/search?q=...`
- [ ] Dropdown ile gerçek zamanlı sonuçlar
- [ ] Kategori bazlı sonuçlar
- [ ] Klavye navigasyonu

**Dosyalar:**
- `Modules/Muzibu/app/Models/MuzibuSong.php` - Searchable trait
- `Modules/Muzibu/app/Http/Controllers/Api/SearchController.php` - Yeni
- `resources/views/themes/muzibu/layouts/app.blade.php` - Search dropdown güncelle

---

### 10. Favorites Backend
- [ ] Polymorphic relation: `favorites` tablosu
- [ ] `POST /api/favorites` - Favorilere ekle
- [ ] `DELETE /api/favorites/{id}` - Favorilerden çıkar
- [ ] `GET /api/favorites` - Kullanıcının favorileri
- [ ] Frontend'i backend'e bağla
- [ ] Favoriler sayfası

**Dosyalar:**
- `Modules/Favorite/database/migrations/tenant/*_create_favorites_table.php` - Mevcut (kontrol)
- `Modules/Favorite/app/Http/Controllers/Api/FavoriteController.php` - Yeni
- `Modules/Favorite/routes/api.php` - Route ekle

---

## ⚙️ ORTA ÖNCELİK

### 11. Recently Played Tracking
- [ ] `song_plays` tablosuna kayıt
- [ ] Şarkı %50 geçince "dinlendi" say
- [ ] "Jump Back In" bölümünde göster
- [ ] Duplicate kaydı önle

**Dosyalar:**
- `Modules/Muzibu/database/migrations/*_create_song_plays_table.php` - Mevcut (kontrol)
- `Modules/Muzibu/app/Services/PlaybackTrackingService.php` - Yeni

---

### 12. Genre & Sector Pages
- [ ] `/genres/{slug}` sayfası
- [ ] `/sectors/{slug}` sayfası
- [ ] Filtrelenmiş playlist/albüm listesi
- [ ] Sidebar'da aktif genre/sector vurgulama

**Dosyalar:**
- `resources/views/themes/muzibu/genre/show.blade.php` - Yeni
- `resources/views/themes/muzibu/sector/show.blade.php` - Yeni

---

### 13. Repeat Modes
- [ ] Repeat state: off, all, one
- [ ] Repeat butonu tıklayınca state değişsin
- [ ] Şarkı bitince repeat mode'a göre davran

---

### 14. Shuffle Mode
- [ ] Shuffle butonu queue'yu karıştır
- [ ] Fisher-Yates algoritması
- [ ] Şu an çalan şarkı yerinde kalsın
- [ ] Shuffle kapatınca orijinal sıraya dön

---

### 15. Device Limit Enforcement
- [ ] Device fingerprinting
- [ ] `active_sessions` tablosu
- [ ] Play'e bastığında cihaz sayısı kontrolü
- [ ] Limit aşıldıysa hata göster
- [ ] Aktif cihazları listeleme sayfası
- [ ] Cihazı deaktif etme butonu

**Dosyalar:**
- `database/migrations/tenant/*_create_active_sessions_table.php` - Yeni
- `app/Services/DeviceLimitService.php` - Yeni

---

## 🎨 DÜŞÜK ÖNCELİK

### 16. Artist Pages
- [ ] Artist detay sayfası
- [ ] Discography
- [ ] Bio
- [ ] Top songs

---

### 17. Playlist Creation
- [ ] Kullanıcılar kendi playlistlerini oluşturabilsin

---

### 18. Lyrics Display
- [ ] Şarkı sözlerini gösterme

---

### 19. Admin Panel
- [ ] Müzik yönetimi admin paneli

---

### 20. Analytics Dashboard
- [ ] Şirketler için dinleme istatistikleri

---

## 📋 NOTLAR

- **ÖNEMLİ:** Her adımda kullanıcı onayı bekle, otomatik devam etme!
- **Çalışma Mantığı:** Kullanıcı "X'i yap" dediğinde o maddeyi gerçekleştir
- **Permission:** Her dosya oluşturmadan sonra `sudo chown tuufi.com_:psaserv` + `sudo chmod 644`
- **Test:** Her değişiklikten sonra `curl -I https://muzibu.com/` ile test
- **Cache:** View değişikliğinden sonra `php artisan view:clear`

---

## 🎯 ÖNERİLEN SIRA

1. **Database + Seed** → Veri hazır olsun
2. **API Endpoints** → Backend hazır olsun
3. **Gerçek Playback** → Müzik çalsın
4. **Volume Normalization** → Ses dengeli olsun
5. **SPA Navigation** → Kesintisiz deneyim

**Kullanıcı hangisini isterse ona başla!**
