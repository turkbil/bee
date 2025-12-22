# Turkbil Bee - Multi-Tenant SaaS Platform

Laravel 12 tabanlı, modüler ve çok kiracılı (multi-tenancy) web platformu. Müzik streaming, e-ticaret ve kurumsal web sitesi çözümlerini tek çatı altında sunar.

---

## Son Güncellemeler

### v6.0.0 - Device/Session Limit System (22 Aralık 2025)

**Muzibu Session Yönetimi Tam Revizyonu**

- **Cookie-based Device Detection**: `mzb_login_token` ile tarayıcı bazlı cihaz tanıma
- **LIFO Mekanizması**: Yeni cihaz girişinde eski cihaz otomatik çıkış
- **Distributed Lock**: Race condition koruması (`Cache::lock`)
- **Atomic Termination**: DB + Redis + Cache senkron temizlik
- **Rate Limiting Fix**: Çift throttle sorunu çözüldü, 429 hataları giderildi

```
Commit: 🔐 Muzibu Device/Session Limit System Overhaul
Files: 33 changed, 1303 insertions(+), 718 deletions(-)
```

### v5.9.0 - Tailwind v4 Migration (21 Aralık 2025)

**Muzibu Frontend Modernizasyonu**

- Tailwind CSS v3 → v4.1.18 migration
- Tenant-aware CSS build sistemi (`npm run css:muzibu`)
- Homepage redesign - modern card layout
- Performance optimizasyonu

```
Commits:
🎉 Checkpoint 10: Tailwind v4 migration COMPLETE
🎯 Checkpoint 11: Homepage Redesign + Song Cover Fix
```

### v5.8.0 - AI & Security Updates (Aralık 2025)

- AI Chat dinamik context sistemi (ben/biz ayrımı)
- HLS streaming güvenlik güncellemeleri
- Console log cleanup (110+ gereksiz log silindi)
- Premium access & toast system düzeltmeleri

---

## Mimari

### Multi-Tenancy

```
Central Database (tuufi_4ekim)
├── tenants, domains
├── users, roles, permissions
├── subscriptions, invoices
└── migrations

Tenant Database (tenant_X)
├── pages, blogs, products
├── songs, albums, playlists (Muzibu)
├── media, settings
└── seo_meta
```

### Modül Sistemi

```
Modules/
├── AI/                 # AI Chat, Credits
├── Blog/               # Blog sistemi
├── Favorite/           # Favori sistemi
├── LanguageManagement/ # Çok dil desteği
├── MenuManagement/     # Dinamik menüler
├── Muzibu/             # Müzik streaming
├── Page/               # Sayfa yönetimi
├── Portfolio/          # Portfolyo
├── SEO/                # SEO meta yönetimi
├── SettingManagement/  # Ayarlar
├── Shop/               # E-ticaret
├── Subscription/       # Abonelik sistemi
├── TenantManagement/   # Tenant yönetimi
└── UserManagement/     # Kullanıcı yönetimi
```

---

## Kurulum

### Gereksinimler

- PHP 8.3+
- MySQL 8.0+ / MariaDB 10.6+
- Redis 7+
- Node.js 20+
- Composer 2.6+

### Hızlı Başlangıç

```bash
# Bağımlılıkları yükle
composer install
npm install

# Ortam dosyasını yapılandır
cp .env.example .env
php artisan key:generate

# Veritabanını hazırla
php artisan migrate
php artisan tenants:migrate

# Asset'leri derle
npm run prod

# Sunucuyu başlat
php artisan serve
```

### Build Komutları

```bash
npm run prod         # Tüm tenant CSS + app.css
npm run css:all      # Sadece tenant CSS'leri
npm run css:ixtif    # Tenant 2 CSS
npm run css:muzibu   # Tenant 1001 CSS
npm run mix-only     # Sadece Laravel Mix
```

---

## Güvenli Cache Temizleme

```bash
# Güvenli komutlar
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear
php artisan optimize:clear

# Nuclear clear (değişiklik yansımadığında)
php artisan cache:clear && php artisan config:clear && \
php artisan route:clear && php artisan view:clear && \
php artisan responsecache:clear && \
curl -s -k https://domain.com/opcache-reset.php
```

### YASAK Komutlar

```bash
# ASLA KULLANMA - Veri kaybı riski!
php artisan migrate:fresh
php artisan db:wipe
php artisan media-library:clear
rm -rf storage/
```

---

## Muzibu - Müzik Streaming

### Özellikler

- HLS encrypted streaming
- AI destekli playlist oluşturma
- Sektör bazlı müzik kategorileri
- Device limit sistemi (LIFO)
- Favori ve rating sistemi
- Infinite queue (otomatik şarkı ekleme)

### Session Yönetimi

```php
// Device = Tarayıcı instance
// Chrome + Firefox = 2 cihaz
// Aynı tarayıcıda re-login = 1 cihaz

// Cookie: mzb_login_token
// Lifetime: auth_session_lifetime setting (varsayılan 43200 dakika)
// LIFO: Yeni cihaz girişinde eski cihaz otomatik logout
```

### API Endpoints

```
POST /api/auth/login          # Giriş + session oluşturma
GET  /api/auth/check-session  # Session geçerlilik kontrolü
GET  /api/auth/active-devices # Aktif cihaz listesi
POST /api/auth/terminate-device # Cihaz sonlandırma

GET  /api/muzibu/songs/{id}/stream  # HLS stream
GET  /api/muzibu/playlists          # Playlist listesi
POST /api/ai/v1/assistant/chat      # AI asistan
```

---

## İxtif - Endüstriyel Ekipman

### Özellikler

- Forklift ve transpalet kataloğu
- Teknik özellik karşılaştırma
- Teklif talep sistemi
- SEO optimizasyonu

---

## Geliştirme Standartları

### Admin Panel

- **Framework**: Tabler.io + Bootstrap 5
- **Components**: Livewire 3.5+
- **Icons**: FontAwesome (`fas`, `far`, `fab`)

### Frontend

- **CSS**: Tailwind CSS v4
- **JS**: Alpine.js 3.x
- **Build**: Vite + PostCSS

### Dosya İzinleri

```bash
# Dosya oluşturduktan sonra
sudo chown tuufi.com_:psaserv /path/to/file
sudo chmod 644 /path/to/file  # Dosya
sudo chmod 755 /path/to/dir/  # Klasör
```

---

## Commit Geçmişi (Son 30)

```
dc34c1e1a 📄 Session/Device Limit Analysis Reports
a630a4cd9 🔐 Muzibu Device/Session Limit System Overhaul
7ea5d0155 🎯 Checkpoint 11: Muzibu Homepage Redesign + Song Cover Fix
b1be47ccb 🎉 Checkpoint 10: Muzibu Tailwind v4 migration COMPLETE
0ffa6d4cc ⚡ Checkpoint 9: Performance test - SIZE INCREASE DETECTED
bfab7275a 🌐 Checkpoint 6: Visual test - ISSUES DETECTED
4b3226fa6 🔨 Checkpoint 5: First successful build with Tailwind v4
df4462f8d 🎨 Checkpoint 4: Muzibu custom colors migrated to @theme
05495b4e1 📝 Checkpoint 3: CSS import syntax updated to Tailwind v4
30b667ffa ⚙️ Checkpoint 2: PostCSS config updated for Tailwind v4
9a7adcce1 📦 Checkpoint 1: Tailwind v4.1.18 packages installed
a6cc45fe4 ✅ Muzibu current state (before Tailwind v4 migration)
b54f283e5 ♻️ Muzibu: Quick Access component refactor
dc9e6afec 🎨 Muzibu: CDN'den tenant_css()'e geçiş
2e076809d ✨ Add Favorite Buttons & Responsive Icons to Homepage
ec01d03b5 📱 Make Card Icons Responsive (Album, Playlist)
eac772756 ✨ Add Favorite Buttons to All Muzibu Cards
9607094e5 🎨 Muzibu Component System Implementation
12a3c2c3e 🎨 Muzibu Component Design System - Infinite Queue
35e989e9f 🔧 System Updates: AI, Mail, Auth, Frontend & Favorites
914d10cb7 🚀 Feature Updates: Component Analysis, AI Enhancements
6caf91d91 ✨ System Improvements: Mail, HLS Streaming, Auth
c7dd990e7 🔧 CHECKPOINT: Before loading performance optimization
95758da34 🔒 Critical Security & UX Fixes - Premium Access
fda65e550 🔧 SEO Fix: Homepage redirect + Schema generation
4b31c221a 🔇 Debug logs: Only show when debug panel active
08613d25e 🧹 Console log cleanup - Phase 2 COMPLETE
e1b72642c 🧹 Console log cleanup - Phase 1 (110+ logs removed)
f21c9084f 🎯 Muzibu AI: ACTION Button Post-Processing System
9fe46058d 🎲 Fix: Queue refill random & SQL issues
```

---

## Dokümantasyon

### Raporlar

- [Session/Device Limit Analizi](https://muzibu.com.tr/readme/2025/12/21/session-device-limit-analysis/)
- [Tüm Raporlar - İxtif](https://ixtif.com/readme/)
- [Tüm Raporlar - Muzibu](https://muzibu.com.tr/readme/)

### Geliştirici Rehberleri

- `CLAUDE.md` - AI geliştirme kuralları
- `TENANT_LIST.md` - Tenant detayları
- `readme/claude-docs/` - Teknik dökümanlar

---

## Lisans

Proprietary - Türk Bilişim

## İletişim

- **Geliştirici**: Nurullah Okatan
- **E-posta**: nurullah@nurullah.net
