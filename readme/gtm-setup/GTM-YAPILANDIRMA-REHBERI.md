# 🎯 GTM (Google Tag Manager) Yapılandırma Rehberi

**Container ID:** GTM-P8HKHCG9
**Son Güncelleme:** 2025-10-26

---

## ✅ TAMAMLANAN İŞLEMLER

### 1. GTM Kod Entegrasyonu ✅ (DYNAMIC - Setting-Based)

#### Frontend (ixtif.com)
- ✅ Layout dosyası: `resources/views/themes/ixtif/layouts/header.blade.php`
- ✅ Head script: Satır 151-162
- ✅ Body noscript: Satır 168-176
- ✅ **Dinamik GTM ID:** `setting('seo_google_tag_manager_id')`

#### Admin Panel
- ✅ Layout dosyası: `resources/views/admin/layout.blade.php`
- ✅ Head script: Satır 176-187
- ✅ Body noscript: Satır 213-221
- ✅ **Dinamik GTM ID:** `setting('seo_google_tag_manager_id')` ✨
- ✅ Tenant-aware: Her tenant kendi GTM ID'sini kullanır

#### Static HTML Sayfalar
- ✅ Klasör: `public/design/hakkimizda-alternatifler/`
- ✅ Güncellenen dosyalar: 11 adet (design-hakkimizda-*.html)
- ✅ **Dynamic injection script:** `readme/gtm-setup/add-gtm-to-static-html.php` ✨
- ✅ Tenant context'te `setting()` kullanır
- ✅ Force update desteği (mevcut GTM kodlarını günceller)

---

## 🔧 YAPILMASI GEREKEN AYARLAR

### 2. Cross-Domain Tracking Yapılandırması

**Amaç:** tuufi.com, ixtif.com ve ixtif.com.tr arasında kullanıcı takibi

#### Adım 1: GTM'de Değişken Oluştur

1. **GTM Dashboard** → **Değişkenler (Variables)** → **Yeni**
2. Değişken tipi: **Constant**
3. Değişken adı: `Cross Domain List`
4. Değer:
   ```
   tuufi.com,ixtif.com,ixtif.com.tr
   ```
5. **Kaydet**

#### Adım 2: Google Analytics 4 Tag'ini Güncelle

1. **GTM Dashboard** → **Etiketler (Tags)** → **GA4 Configuration Tag**
2. **Fields to Set** bölümüne ekle:
   - Field Name: `linker`
   - Value: `{"domains": ["tuufi.com", "ixtif.com", "ixtif.com.tr"]}`
3. **Kaydet**

#### Adım 3: Google Ads Conversion Linker Tetikleyici

**⚠️ ÖNEMLİ:** Ekran görüntünüzde "Google Ads - Conversion Linker" etiketi değiştirilmiş görünüyor.

1. **GTM Dashboard** → **Etiketler** → **Google Ads - Conversion Linker**
2. **Tetikleyici (Trigger)** kontrol et:
   - ✅ **All Pages** olmalı (tüm sayfalarda çalışsın)
   - ❌ Belirli sayfa sınırlaması olmamalı
3. **Linker Settings** (gelişmiş ayarlar):
   - ✅ **Accept Incoming Linker Parameters**: `true`
   - ✅ **Decorate Forms**: `true`
   - ✅ **URL Passthrough**: `false` (genellikle)
4. **Kaydet**

---

### 3. Etiket Kapsamı Genişletme

**Sorun:** Bazı sayfalar etiketlenmemiş (ekran görüntüsünde belirtilmiş)

#### Manuel Kontrol:

1. **GTM Dashboard** → **Tag Coverage** (Etiket Kapsamı)
2. Şu URL'leri ekleyip test et:
   - `ixtif.com/admin/shop` → ✅ Artık etiketli olmalı (layout güncelledik)
   - `ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html` → ✅ Artık etiketli olmalı

#### Tetikleyici Doğrulama:

1. **GTM Dashboard** → **Tetikleyiciler (Triggers)**
2. Tüm ana etiketlerin tetikleyicisi **"All Pages"** olmalı:
   - ✅ Google Analytics 4 Configuration
   - ✅ Google Ads Conversion Linker
   - ✅ Diğer tracking etiketleri

---

### 4. Alan Adı Yapılandırması

**GTM'de algılanan alan adları için:**

1. **GTM Dashboard** → **Admin** → **Container Settings**
2. **Allowed Domains** bölümünde şunları ekle:
   - `tuufi.com`
   - `ixtif.com`
   - `ixtif.com.tr`
   - `www.tuufi.com` (varsa)
   - `www.ixtif.com` (varsa)
   - `www.ixtif.com.tr` (varsa)

3. **Auto-Link Domains** (varsa):
   ```
   tuufi.com,ixtif.com,ixtif.com.tr
   ```

---

## 🧪 TEST ADIMLARI

### Manuel Test (Tarayıcı)

1. **Admin Sayfası Test:**
   - Login: https://ixtif.com/admin/shop
   - Tarayıcı Console aç (F12)
   - Network tab → `gtm.js` araması yap
   - ✅ `gtm.js?id=GTM-P8HKHCG9` yüklenmiş olmalı

2. **Static HTML Test:**
   - Ziyaret: https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html
   - View Source (Ctrl+U)
   - ✅ `GTM-P8HKHCG9` kodunu ara, bulmalısın

3. **Frontend Test:**
   - Ziyaret: https://ixtif.com
   - Tarayıcı Console → `dataLayer` yaz
   - ✅ Array döner, `gtm.start` eventi olmalı

### GTM Preview Mode (Önerilen)

1. **GTM Dashboard** → **Preview** butonuna tıkla
2. URL gir: `https://ixtif.com`
3. **Tag Assistant** açılır
4. Tüm sayfalarda gezin:
   - Admin panel
   - Static HTML sayfalar
   - Frontend sayfalar
5. ✅ **Tags Fired** bölümünde etiketlerin çalıştığını gör

---

## 📊 KONTROL LİSTESİ

### Kod Entegrasyonu
- [x] Frontend layout'a GTM eklendi
- [x] Admin layout'a GTM eklendi
- [x] Static HTML dosyalarına GTM eklendi
- [x] Cache temizlendi

### GTM Yapılandırması (Manuel)
- [ ] Cross-Domain Tracking değişkeni oluşturuldu
- [ ] GA4 Tag'ine linker parametresi eklendi
- [ ] Google Ads Conversion Linker tetikleyicisi "All Pages" olarak ayarlandı
- [ ] Allowed Domains listesi güncellendi
- [ ] Preview mode ile test edildi

### Doğrulama
- [ ] Admin panelde GTM çalışıyor (tarayıcı console kontrolü)
- [ ] Static HTML'lerde GTM çalışıyor
- [ ] Frontend'de GTM çalışıyor
- [ ] Cross-domain tracking çalışıyor (tenant'lar arası geçiş)

---

## 🚨 SORUN GİDERME

### "Etiketlenmemiş sayfalar" Hatası

**Çözüm:**
1. Cache temizlendi mi? → `php artisan view:clear`
2. GTM Preview mode'da sayfa görünüyor mu?
3. Tetikleyici "All Pages" mi?

### "Ek alan adları algılandı" Hatası

**Çözüm:**
1. GTM → Container Settings → Allowed Domains
2. Tüm tenant domain'lerini ekle
3. Cross-Domain Tracking ayarını yap

### Google Ads Conversion Linker Çalışmıyor

**Çözüm:**
1. Tetikleyici: **All Pages** olmalı
2. Linker Settings → Accept Incoming: **true**
3. Preview mode'da test et

---

## 📝 NOTLAR

### Setting Yönetimi (DYNAMIC SYSTEM)

**✅ TÜM ENTEGRASYON DİNAMİK!**

Artık tüm GTM kodları `setting('seo_google_tag_manager_id')` ile dinamik olarak çalışıyor:
- ✅ **Frontend:** Setting'den alır
- ✅ **Admin Panel:** Setting'den alır
- ✅ **Static HTML:** Script çalıştırıldığında setting'den alır

**GTM ID Değiştirme:**

1. **Admin Panelden** (Önerilen):
   - `/admin/settingmanagement` → SEO Ayarları
   - "Google Tag Manager Container ID" alanını güncelle
   - Admin ve frontend otomatik güncellenir
   - Static HTML için script tekrar çalıştırılmalı

2. **Tinker ile:**
   ```bash
   php artisan tinker
   tenancy()->initialize(2); # ixtif.com
   setting_update('seo_google_tag_manager_id', 'GTM-YENI-ID');
   ```

3. **Static HTML güncelleme:**
   ```bash
   php readme/gtm-setup/add-gtm-to-static-html.php --force
   ```

**Tenant-Specific GTM:**

Her tenant kendi GTM ID'sine sahip olabilir:
- Tenant 2 (ixtif.com): GTM-P8HKHCG9
- Tenant 3 (ixtif.com.tr): Farklı GTM ID kullanabilir

```bash
# Tenant 3 için farklı GTM ekle
php artisan tinker
tenancy()->initialize(3);
setting_update('seo_google_tag_manager_id', 'GTM-TENANT3-ID');

# Static HTML'leri güncelle
php readme/gtm-setup/add-gtm-to-static-html.php --tenant=3 --force
```

**Static HTML Dosyaları:**

- Yeni static HTML eklenirse: `php readme/gtm-setup/add-gtm-to-static-html.php`
- GTM ID değişirse: `php readme/gtm-setup/add-gtm-to-static-html.php --force`
- Farklı tenant: `php readme/gtm-setup/add-gtm-to-static-html.php --tenant=3`

**ixtif-designs Klasörü:** Script'te tanımlı ama dosya bulunamadı. Gerekirse klasör yolunu kontrol et.

---

## 🎯 SONRAKI ADIMLAR

1. ✅ Kod entegrasyonu tamamlandı
2. ⏳ GTM Dashboard'da manuel yapılandırma yap (yukarıdaki adımlar)
3. ⏳ Preview mode ile test et
4. ⏳ Container'ı **Publish** et
5. ⏳ 24-48 saat sonra Tag Coverage'ı tekrar kontrol et

---

**Hazırlayan:** Claude AI
**Script Dosyası:** `readme/gtm-setup/add-gtm-to-static-html.php`
**Tarih:** 2025-10-26
