# 🏢 Tenant Yönetimi - Eksiksiz Kılavuz

**Tarih:** 2025-10-14 15:22-15:45
**Sistem:** Laravel Multi-Tenant + Plesk + Nginx
**Durum:** ✅ Test Edildi - Çalışıyor

---

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#-genel-bakış)
2. [Yeni Domain Ekleme](#-yeni-domain-ekleme-süreci)
3. [Sorun Giderme](#-sorun-giderme)
4. [Gerçek Vaka: ixtif.com Sorunu](#-gerçek-vaka-ixtifcom-redirect-sorunu)
5. [Hızlı Komutlar](#-hizli-komutlar)
6. [Kontrol Listesi](#-kontrol-listesi)

---

## 📋 GENEL BAKIŞ

Bu kılavuz, sistemde **yeni bir tenant domain** eklemek, sorun gidermek ve sistem yapısını anlamak için her şeyi içerir.

### Mevcut Tenant Yapısı
- **tuufi.com**: Central domain (Ana Laravel app)
- **ixtif.com**: Tenant ID: 2 ✅
- **ixtif.com.tr**: Tenant ID: 3 ✅

### Nasıl Çalışır?
1. **Plesk**: Domain alias olarak tuufi.com'a bağlı
2. **Nginx/Apache**: Tüm domain'leri aynı Laravel app'e yönlendirir
3. **Laravel Tenancy**: Domain'e göre tenant database'e bağlanır
4. **Her tenant**: Kendi database + storage klasörü

---

## 🎯 YENİ DOMAIN EKLEME SÜRECİ

### Adım 1: Plesk'te Domain Alias Oluşturma

1. **Plesk Panel**'e giriş yap:
   ```bash
   plesk login
   ```

2. **Domains → tuufi.com** → **Add Alias** tıkla

3. **Alias adını gir**: örnek: `yenisite.com`

4. ✅ **Web**, ✅ **DNS** seçili olsun

5. ❌ **SEO-safe redirect** KAPALI olmalı (önemli!)

6. **OK** ile kaydet

---

### Adım 2: Plesk Database'de SEO Redirect Kontrolü

**SEO redirect aktifse** domain ana domain'e yönlenecektir. Kontrol ve düzeltme:

```bash
# 1. Kontrol et
plesk db "SELECT da.id, da.name, da.seoRedirect, d.name as parent_domain
FROM domain_aliases da
LEFT JOIN domains d ON da.dom_id = d.id
WHERE da.name = 'yenisite.com'"

# 2. Eğer seoRedirect = true ise kapat
plesk db "UPDATE domain_aliases SET seoRedirect = 'false' WHERE name = 'yenisite.com'"
```

---

### Adım 3: Laravel Tenant Oluşturma

```bash
cd /var/www/vhosts/tuufi.com/httpdocs

php artisan tinker
```

**Tinker içinde:**
```php
// Yeni tenant oluştur
$tenant = \App\Models\Tenant::create([
    'id' => 'tenant4', // veya otomatik ID
]);

// Domain ekle
$tenant->domains()->create([
    'domain' => 'yenisite.com'
]);

$tenant->domains()->create([
    'domain' => 'www.yenisite.com'
]);

// Tenant database'i oluştur
$tenant->run(function () {
    // Seeder çalıştır (opsiyonel)
    \Artisan::call('db:seed', ['--class' => 'TenantDatabaseSeeder']);
});

exit
```

---

### Adım 4: Web Server Config Güncelleme

```bash
# Plesk web server yapılandırmasını yeniden oluştur
plesk repair web tuufi.com -y

# Nginx reload
systemctl reload nginx

# Apache reload
systemctl reload httpd
```

---

### Adım 5: Test

```bash
# HTTPS testi
curl -I https://yenisite.com/

# Beklenen sonuç:
# HTTP/2 200 OK

# HTTP → HTTPS redirect testi
curl -I http://yenisite.com/

# Beklenen sonuç:
# HTTP/1.1 301 Moved Permanently
# Location: https://yenisite.com/
```

---

## 🔧 SORUN GİDERME

### Sorun 1: Domain tuufi.com'a Redirect Yapıyor

**Neden:** Plesk'te SEO redirect aktif

**Çözüm:**
```bash
# Database'de kontrol et
plesk db "SELECT name, seoRedirect FROM domain_aliases WHERE name = 'domain.com'"

# SEO redirect'i kapat
plesk db "UPDATE domain_aliases SET seoRedirect = 'false' WHERE name = 'domain.com'"

# Config yenile
plesk repair web tuufi.com -y && systemctl reload nginx
```

---

### Sorun 2: SSL Sertifika Hatası

**Neden:** Domain için SSL sertifikası yok

**Çözüm:**
```bash
# Let's Encrypt sertifika al
plesk bin certificate --issue -domain yenisite.com -admin-email admin@tuufi.com
```

---

### Sorun 3: Nginx "421 Misdirected Request"

**Neden:** Domain Nginx config'te yok

**Çözüm:**
```bash
# Nginx config yenile
plesk repair web tuufi.com -y

# Kontrol et
grep "yenisite.com" /var/www/vhosts/system/tuufi.com/conf/nginx.conf

# Reload
systemctl reload nginx
```

---

## 📁 ÖNEMLİ DOSYALAR

### Nginx Config
```
/var/www/vhosts/system/tuufi.com/conf/nginx.conf
/etc/nginx/plesk.conf.d/vhosts/tuufi.com.conf (symlink)
```

### Apache Config
```
/var/www/vhosts/system/tuufi.com/conf/httpd.conf
/var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf (custom)
```

### Laravel Tenant Config
```
/var/www/vhosts/tuufi.com/httpdocs/config/tenancy.php
```

---

## ✅ KONTROL LİSTESİ

Yeni domain eklerken şunları kontrol et:

- [ ] Plesk'te alias olarak eklendi
- [ ] SEO redirect KAPALI
- [ ] Laravel'de tenant oluşturuldu
- [ ] Domain tenant'a bağlandı
- [ ] Web server config güncellendi
- [ ] Nginx reload yapıldı
- [ ] HTTPS testi başarılı
- [ ] Tenant database seeded
- [ ] Storage klasörleri oluştu

---

## 🚀 HIZLI KOMUTLAR

```bash
# Tüm tenant'ları listele
php artisan tinker --execute="
\App\Models\Tenant::with('domains')->get()->each(function(\$t) {
    echo 'Tenant: ' . \$t->id . PHP_EOL;
    \$t->domains->each(fn(\$d) => echo '  - ' . \$d->domain . PHP_EOL);
});
"

# Domain alias kontrol
plesk db "SELECT da.name, da.seoRedirect, d.name as parent
FROM domain_aliases da
LEFT JOIN domains d ON da.dom_id = d.id"

# Nginx config test
nginx -t

# Son access log
tail -20 /var/www/vhosts/system/tuufi.com/logs/proxy_access_ssl_log
```

---

## 📌 NOTLAR

1. **Her yeni domain için SSL sertifikası otomatik oluşmaz**, manuel Let's Encrypt çalıştır
2. **SEO redirect** varsayılan olarak açık gelebilir, mutlaka kapat
3. **Plesk GUI değişiklikleri** config dosyalarını otomatik regenerate eder
4. **Custom Nginx direktifleri** için `vhost_nginx.conf` kullan
5. **Tenant database** otomatik oluşur ama seed manuel çalıştır

---

## 🔍 GERÇEK VAKA: ixtif.com Redirect Sorunu

**Tarih:** 2025-10-14 15:22-15:45
**Problem:** ixtif.com → tuufi.com'a 301 redirect yapıyordu

### 🎯 Sorun

```bash
curl -I https://ixtif.com/
# HTTP/2 301
# location: https://tuufi.com/
```

**Beklenen:** ixtif.com kendi tenant'ı olarak çalışmalıydı (Tenant ID: 2)

### 🔍 Analiz Süreci

#### 1. Nginx Config Kontrolü ❌
- ixtif.com redirect blokları vardı
- Blokları kaldırdık, server_name'e ekledik
- **Sonuç:** Redirect hala devam etti

#### 2. Apache Config Kontrolü ✅
- ixtif.com zaten ServerAlias olarak ekliydi
- **Sonuç:** Apache'de sorun yok

#### 3. Laravel Tenant Kontrolü ✅
```bash
php artisan tinker --execute="..."
# Tenant ID: 2 → ixtif.com ✅
```

#### 4. DNS Kontrolü ✅
```bash
dig ixtif.com +short
# 159.253.45.94 (doğru IP)
```

#### 5. ⭐ KÖK NEDEN BULUNDU!

**Plesk Database:**
```sql
SELECT da.id, da.name, da.seoRedirect, d.name as parent_domain
FROM domain_aliases da
LEFT JOIN domains d ON da.dom_id = d.id
WHERE da.name = 'ixtif.com';

-- SONUÇ:
-- seoRedirect = true ❌
```

**Plesk SEO Redirect aktifti!** Domain alias oluşturulurken varsayılan açık gelmişti.

### ✅ Çözüm

```bash
# 1. Database'de SEO redirect'i kapat
plesk db "UPDATE domain_aliases SET seoRedirect = 'false' WHERE name = 'ixtif.com'"

# 2. Web server config yenile
plesk repair web tuufi.com -y

# 3. Nginx reload
systemctl reload nginx

# 4. Test
curl -I https://ixtif.com/
# HTTP/2 200 ✅ BAŞARILI!
```

### 📊 Sonuç

**Öncesi:**
- ❌ ixtif.com → 301 redirect to tuufi.com

**Sonrası:**
- ✅ ixtif.com → 200 OK (Tenant ID: 2)
- ✅ ixtif.com.tr → 200 OK (Tenant ID: 3)
- ✅ tuufi.com → 200 OK (Central)

### 🎓 Öğrenilenler

1. **Plesk alias'larda SEO redirect varsayılan açık gelebilir!**
2. **Domain eklendikten sonra mutlaka database'de kontrol et:**
   ```bash
   plesk db "SELECT name, seoRedirect FROM domain_aliases"
   ```
3. **Nginx config'i elle değiştirmek yetersiz** → Plesk database'de ayar değişmeli
4. **Her değişiklikten sonra:** `plesk repair web tuufi.com -y`

---

## 🔗 İLGİLİ DÖKÜMANLAR

- [Laravel Tenancy Docs](https://tenancyforlaravel.com)
- [Plesk CLI Docs](https://docs.plesk.com)
- Sistem: `CLAUDE.md` → Tenant Yönetimi bölümü

---

## 📝 ÖZET

**Bu döküman 3 ana işlevi görür:**
1. ✅ **Yeni tenant ekleme** → Adım adım kılavuz
2. 🔧 **Sorun giderme** → Yaygın problemler + çözümler
3. 📖 **Gerçek vaka çalışması** → ixtif.com redirect sorunu

**İşlem Süresi:** ~25 dakika (analiz + çözüm)
**Test Edilen:** ixtif.com ✅
**Son Güncelleme:** 2025-10-14
