# 🏢 TENANT LİSTESİ & SİSTEM MİMARİSİ

## 🚨 ÖNEMLİ: SİSTEM MULTI-TENANT AWARE!

Bu sistem **multi-tenant** mimarisine sahiptir. Her tenant **tamamen bağımsız bir database** kullanır.

---

## 📊 TENANT YAPISI

### **Tenant 1 - tuufi.com (CENTRAL)**
- **ID:** `1`
- **Başlık:** tuufi.com
- **Domain:** `tuufi.com`
- **Database:** `tuufi_4ekim`
- **Durum:** Central Tenant (Ana Sistem)
- **Premium:** ✅ Evet
- **Tema:** Default (Theme ID: 1)
- **Sektör:** Central Management System

**Özellikler:**
- Tüm tenant'ları yöneten central sistem
- Kullanıcı yönetimi, faturalandırma, AI credits gibi global işlemler
- Diğer tenant'lar için ortak tablolar (users, roles, permissions vb.)
- `central: true` bayrağı ile tanımlanır

---

### **Tenant 2 - ixtif.com (VARSAYILAN)**
- **ID:** `2`
- **Başlık:** ixtif.com
- **Domain(ler):**
  - `ixtif.com` (Primary)
  - `ixtif.com.tr` (Alias)
- **Database:** `tenant_ixtif`
- **Durum:** Aktif
- **Premium:** ✅ Evet
- **Tema:** Ixtif (Theme ID: 2)
- **Sektör:** Endüstriyel Ekipman (Forklift, Transpalet, İş Makineleri)

**Özellikler:**
- Blog, Products, Categories, Brands
- Endüstriyel ekipman odaklı içerik
- SEO optimize edilmiş yapı
- Tailwind CSS: `tenant-2.config.js`

---

### **Tenant 1001 - muzibu.com**
- **ID:** `1001`
- **Başlık:** Muzibu
- **Domain(ler):**
  - `muzibu.com`
  - `www.muzibu.com`
- **Database:** `tenant_muzibu_1528d0`
- **Durum:** Aktif
- **Premium:** ❌ Hayır
- **Tema:** Muzibu (Theme ID: 7)
- **Sektör:** Müzik Platformu (Streaming, Playlist, Artist)

**Özellikler:**
- Song, Album, Artist, Playlist management
- Müzik streaming özellikleri
- Kullanıcı premium sistemi (günlük limit kontrolleri)
- Tailwind CSS: `tenant-1001.config.js`
- Spotify benzeri arayüz

---

## 🗄️ DATABASE MİMARİSİ

### **Central Database: `tuufi_4ekim`**
Tüm tenant'lar için ortak:
- `tenants` - Tenant bilgileri
- `domains` - Tenant domain'leri
- `users` - Kullanıcılar (tüm tenant'lar)
- `roles`, `permissions` - Yetki sistemi
- `migrations` - Central migration kayıtları
- `settings` (bazı global ayarlar)
- `ai_credits` - AI kredi yönetimi
- `subscriptions`, `invoices` - Faturalandırma

### **Tenant Database: `tenant_[name]`**
Her tenant'a özel:
- `pages` - Sayfa içerikleri
- `blogs`, `blog_categories` - Blog sistemi
- `products`, `categories`, `brands` - Ürün yönetimi
- `media` - Medya dosyaları (tenant'a özel)
- `seo_meta` - SEO bilgileri
- `settings` - Tenant özel ayarlar
- **Muzibu için:** `songs`, `albums`, `artists`, `playlists`, `genres`, `sectors`
- **İxtif için:** `products`, `brands`, `categories` (endüstriyel ekipman)

### **Database Bağlantıları (`config/database.php`):**
- `mysql` - Central database (varsayılan)
- `central` - Central database (alias)
- `tenant` - Tenant database (dinamik, runtime'da belirlenir)

---

## 🔄 TENANT AWARE ÇALIŞMA MANTIĞI

### **1. Domain Tanıma Sistemi**
Sistem gelen HTTP isteğindeki domain'e göre tenant'ı belirler:

```
https://ixtif.com → Tenant 2 → tenant_ixtif database
https://muzibu.com → Tenant 1001 → tenant_muzibu_1528d0 database
https://tuufi.com → Tenant 1 (Central) → tuufi_4ekim database
```

### **2. Otomatik Database Switching**
Tenant belirlendikten sonra Laravel otomatik olarak database bağlantısını değiştirir:

- `DB::connection('tenant')` - Aktif tenant'ın database'i
- `DB::connection('central')` - Her zaman central database

### **3. Model'ler ve Tenant Awareness**

**Tenant Database Kullanan Modeller:**
```php
// Otomatik olarak tenant database'i kullanır
Page, Blog, Product, Category, Brand, Song, Album, Artist, Playlist
```

**Central Database Kullanan Modeller:**
```php
// Her zaman central database'i kullanır
use Illuminate\Database\Eloquent\Model;
class User extends Model {
    protected $connection = 'central'; // ✅ Zorunlu!
}
```

### **4. Bazı Tablolar Hem Central Hem Tenant'da:**
- `settings` - Ortak ayarlar central'da, özel ayarlar tenant'da
- `media` - Global medya central'da, tenant medyası tenant'da
- `languages` - Dil tanımları central'da, çeviriler tenant'da

---

## 🚨 KRİTİK KURALLAR

### **❌ YAPMA:**
1. ❌ Tenant'a özel içeriği global kodlara ekleme!
   - Forklift/Transpalet sadece Tenant 2'ye ait!
   - Müzik/Song/Album sadece Tenant 1001'e ait!

2. ❌ Central database'e tenant verisi yazma!
   - Blog, Product, Page → Tenant database'e yazılmalı!

3. ❌ Tenant database'e user bilgisi yazma!
   - User, Role, Permission → Central database'de!

### **✅ YAP:**
1. ✅ Tenant'ı kontrol et:
   ```php
   if (tenant()->id === 2) {
       // Sadece İxtif için
   }

   if (tenant()->id === 1001) {
       // Sadece Muzibu için
   }
   ```

2. ✅ Migration oluştururken İKİ YERDE oluştur:
   ```bash
   # Central
   database/migrations/YYYY_MM_DD_create_table.php

   # Tenant
   database/migrations/tenant/YYYY_MM_DD_create_table.php
   ```

3. ✅ Database bağlantısını doğru kullan:
   ```php
   // Tenant verisi
   Page::all(); // Otomatik tenant DB

   // Central verisi
   User::all(); // Zorunlu $connection = 'central'
   ```

---

## 📝 YENİ TENANT EKLEME

**Detaylı kılavuz:** `readme/tenant-olusturma.md`

**Kısa özet:**
```bash
# 1. Plesk'te domain alias ekle (SEO redirect KAPALI!)

# 2. Laravel'de tenant oluştur
php artisan tinker
$tenant = Tenant::create([
    'id' => 3,
    'title' => 'New Tenant',
    'tenancy_db_name' => 'tenant_new',
]);
$tenant->domains()->create(['domain' => 'newdomain.com']);

# 3. Nginx config yenile
plesk repair web tuufi.com -y

# 4. Test
curl -I https://newdomain.com/
```

---

## 🎨 TEMA & CSS SİSTEMİ

Her tenant'ın kendi Tailwind CSS config'i var:

```bash
# Tailwind config konumları
tailwind/tenants/tenant-1.config.js    # tuufi.com
tailwind/tenants/tenant-2.config.js    # ixtif.com
tailwind/tenants/tenant-1001.config.js # muzibu.com

# Build komutları
npm run css:all      # Tüm tenant'lar
npm run css:ixtif    # Sadece tenant-2
npm run css:muzibu   # Sadece tenant-1001

# Output
public/css/tenant-1.css
public/css/tenant-2.css
public/css/tenant-1001.css
```

**Blade'de kullanım:**
```blade
{{ tenant_css() }} <!-- Otomatik olarak aktif tenant'ın CSS'ini yükler -->
```

---

## 📊 TENANT İSTATİSTİKLERİ

| Tenant | DB Boyutu | Aktif Kullanıcı | Premium | Sektör |
|--------|-----------|-----------------|---------|--------|
| Tenant 1 (tuufi.com) | - | - | ✅ | Central |
| Tenant 2 (ixtif.com) | - | - | ✅ | Endüstriyel |
| Tenant 1001 (muzibu.com) | - | - | ❌ | Müzik |

---

## 🔍 TENANT DEBUG

**Aktif tenant'ı öğren:**
```php
tenant(); // Tenant instance
tenant()->id; // Tenant ID
tenant()->title; // Tenant başlığı
tenant('id'); // Kısa yol
```

**Database kontrolü:**
```php
// Hangi database bağlısın?
DB::connection()->getDatabaseName();

// Tenant database
DB::connection('tenant')->getDatabaseName();

// Central database
DB::connection('central')->getDatabaseName();
```

**Domain kontrolü:**
```php
// Aktif domain
request()->getHost();

// Tenant'ın tüm domain'leri
tenant()->domains()->pluck('domain');
```

---

## 📚 BAĞLANTILAR

- **Tenant Oluşturma:** `readme/tenant-olusturma.md`
- **Tenancy Config:** `config/tenancy.php`
- **Database Config:** `config/database.php`
- **Ana Kılavuz:** `CLAUDE.md`

---

**Son Güncelleme:** 2025-11-28
**Tenant Sayısı:** 3 (1 Central + 2 Alt Tenant)
