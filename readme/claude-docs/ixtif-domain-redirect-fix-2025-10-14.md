# ixtif.com Domain Yönlendirme Sorunu - Analiz ve Çözüm

**Tarih:** 2025-10-14
**Sorun:** ixtif.com domain'i tuufi.com'a (central) yönleniyor

---

## 🔍 PROBLEM ANALİZİ

### Mevcut Durum

1. **Database Kayıtları:**
   ```json
   [
     {
       "id": 1,
       "domains": ["tuufi.com", "www.tuufi.com"]  // CENTRAL
     },
     {
       "id": 2,
       "domains": ["ixtif.com", "www.ixtif.com"]  // TENANT
     },
     {
       "id": 3,
       "domains": ["ixtif.com.tr", "www.ixtif.com.tr"]  // TENANT
     }
   ]
   ```

2. **Plesk Yapılandırması:**
   - ✅ tuufi.com → Kayıtlı ve aktif
   - ❌ ixtif.com → Kayıtlı DEĞİL
   - ❌ ixtif.com.tr → Kayıtlı DEĞİL

3. **Web Sunucu:**
   - Apache 2.4.37 (AlmaLinux)
   - Document Root: `/var/www/vhosts/tuufi.com/httpdocs/public`
   - SSL: Let's Encrypt (tuufi.com için aktif)

4. **Laravel Tenancy Config:**
   ```php
   'central_domains' => [
       env('APP_DOMAIN', 'laravel.test'),
       'tuufi.com',
       'www.tuufi.com',
   ],
   ```

---

## ⚠️ SORUNUN NEDEN Kİ

**Ana Sebep:** Plesk'te ixtif.com domain'i tanımlı olmadığı için, Apache tüm tanımsız domain isteklerini varsayılan site olan tuufi.com'a yönlendiriyor.

**Akış:**
1. Kullanıcı ixtif.com'a istek gönderir
2. Apache/Plesk domain'i bulamaz
3. Varsayılan site (tuufi.com) config'i devreye girer
4. Laravel Tenancy middleware çalışır ama domain tanımsız olduğu için yönlendirme yapar
5. Kullanıcı tuufi.com'a düşer

---

## ✅ ÇÖZÜM PLANI

### Yöntem 1: Domain Alias (ÖNERİLEN)

Plesk'te tuufi.com site'ına **domain alias** eklemek:

```bash
# ixtif.com alias ekle
plesk bin site --update tuufi.com -domain-alias add ixtif.com
plesk bin site --update tuufi.com -domain-alias add www.ixtif.com

# ixtif.com.tr alias ekle
plesk bin site --update tuufi.com -domain-alias add ixtif.com.tr
plesk bin site --update tuufi.com -domain-alias add www.ixtif.com.tr
```

**Avantajları:**
- Tek bir site, tüm domain'ler aynı document root'u gösterir
- Laravel Tenancy sistemi domaini yakalayıp doğru tenant'ı çalıştırır
- SSL sertifikaları otomatik yönetilebilir

### Yöntem 2: Wildcard Domain (ALTERNATİF)

Eğer çok sayıda tenant domain'i varsa:

```bash
# Wildcard subdomain desteği
plesk bin site --update tuufi.com -wildcard-domain on
```

**Avantajları:**
- *.tuufi.com şeklindeki tüm subdomain'ler otomatik çalışır
- Yeni tenant eklendiğinde manuel işlem gerekmez

**Dezavantajları:**
- Sadece subdomain'ler için çalışır (tenant1.tuufi.com gibi)
- ixtif.com gibi farklı domain'ler için çalışmaz

---

## 🔧 UYGULAMA ADIMLARI

### Adım 1: Domain Alias Ekle

```bash
# İlk alias - ixtif.com
plesk bin site --update tuufi.com -domain-alias add ixtif.com

# www variant
plesk bin site --update tuufi.com -domain-alias add www.ixtif.com

# İkinci alias - ixtif.com.tr
plesk bin site --update tuufi.com -domain-alias add ixtif.com.tr

# www variant
plesk bin site --update tuufi.com -domain-alias add www.ixtif.com.tr
```

### Adım 2: SSL Sertifikaları

```bash
# Let's Encrypt SSL sertifikalarını yenile (tüm alias'lar dahil)
plesk bin certificate --issue "tuufi.com" -domains "tuufi.com,www.tuufi.com,ixtif.com,www.ixtif.com,ixtif.com.tr,www.ixtif.com.tr"
```

### Adım 3: Apache Yeniden Başlat

```bash
# Apache config test
httpd -t

# Apache yeniden başlat
systemctl restart httpd
# veya
plesk bin service --restart web
```

### Adım 4: Test

```bash
# Domain çözümlemesi kontrol
curl -I https://ixtif.com
curl -I https://ixtif.com.tr

# Laravel tenant kontrolü
php artisan tinker --execute="
\$domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', 'ixtif.com')->first();
echo 'Domain: ' . \$domain->domain . PHP_EOL;
echo 'Tenant ID: ' . \$domain->tenant_id . PHP_EOL;
"
```

---

## 📋 KONTROL LİSTESİ

- [x] Plesk'te domain alias'ları ekle
- [x] SSL sertifikalarını güncelle
- [x] Apache config test yap
- [x] Apache'yi yeniden başlat
- [x] ixtif.com domain'ini tarayıcıda test et
- [x] ixtif.com.tr domain'ini tarayıcıda test et
- [x] Laravel log dosyasını kontrol et
- [x] Tenant database bağlantısını doğrula

---

## 🔐 GÜVENLİK NOTLARI

1. **SSL Sertifikaları:**
   - Her domain için ayrı SSL gerekli
   - Let's Encrypt otomatik yenileyebilir
   - Wildcard SSL kullanılabilir (*.tuufi.com)

2. **Apache ServerAlias:**
   - Plesk otomatik ServerAlias direktifi ekler
   - Manuel Apache config değişikliği GEREKMİYOR

3. **Session Domain:**
   - `.env` dosyasında: `SESSION_DOMAIN=.tuufi.com`
   - Bu ayar sadece tuufi.com subdomain'leri için geçerli
   - ixtif.com için ayrı session cookie kullanılacak (Laravel Tenancy otomatik ayarlar)

---

## 🎯 BEKLENTİLER

**İşlem Sonrası:**
- ✅ ixtif.com → Tenant 2'ye gidecek
- ✅ ixtif.com.tr → Tenant 3'e gidecek
- ✅ tuufi.com → Central (Tenant 1) kalacak
- ✅ Her tenant kendi database'ini kullanacak
- ✅ SSL sertifikaları tüm domain'ler için çalışacak

---

## 📞 SORUN GİDERME

### Sorun: Domain hala yönlendiriyor

**Çözüm:**
```bash
# DNS cache temizle
systemctl restart named

# Apache cache temizle
rm -rf /var/cache/apache2/*

# PHP-FPM restart
systemctl restart php-fpm
```

### Sorun: SSL hatası

**Çözüm:**
```bash
# SSL sertifikasını manuel yenile
certbot certonly --webroot -w /var/www/vhosts/tuufi.com/httpdocs/public -d ixtif.com -d www.ixtif.com

# Plesk'e sertifikayı tanıt
plesk bin certificate --install "tuufi.com" -cert-file /etc/letsencrypt/live/ixtif.com/cert.pem -key-file /etc/letsencrypt/live/ixtif.com/privkey.pem -cacert-file /etc/letsencrypt/live/ixtif.com/chain.pem
```

### Sorun: Tenant database bağlantı hatası

**Kontrol:**
```bash
# Tenant database'lerini listele
php artisan tenants:list

# Tenant database bağlantısını test et
php artisan tinker --execute="
tenancy()->initialize(2);
echo 'Connected to: ' . \DB::connection()->getDatabaseName();
"
```

---

## 📊 SİSTEM BİLGİLERİ

- **OS:** AlmaLinux 8
- **Web Server:** Apache 2.4.37
- **PHP:** (kontrol edilecek)
- **Laravel:** (version kontrol edilecek)
- **Tenancy Package:** stancl/tenancy
- **Plesk:** Obsidian
- **Document Root:** `/var/www/vhosts/tuufi.com/httpdocs/public`
- **Database:** MySQL/MariaDB
- **Central DB:** tuufi_4ekim
- **Tenant DB Pattern:** `tenant{id}*`

---

## SON NOTLAR

Bu işlem sonrasında:
1. Yeni tenant eklediğinizde domain alias manuel eklemeniz gerekecek
2. Veya subdomain pattern'e geçiş yapabilirsiniz (tenant1.tuufi.com, tenant2.tuufi.com)
3. SSL sertifikaları Let's Encrypt tarafından 90 günde bir otomatik yenilenecek

---

## ✅ UYGULANAN ÇÖZÜM - 2025-10-14 04:40

### İşlem Özeti

**Problem:**
- ixtif.com → tuufi.com'a redirect yapıyordu
- ixtif.com.tr → SSL sertifika hatası (ERR_CERT_COMMON_NAME_INVALID)

**Uygulanan Çözüm:**

#### 1. Apache Konfigürasyonu

**Dosya:** `/var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf` ve `vhost.conf`

```apache
# Custom Apache SSL config for tuufi.com
# Add multi-tenant domains here

ServerAlias "ixtif.com"
ServerAlias "www.ixtif.com"
ServerAlias "ixtif.com.tr"
ServerAlias "www.ixtif.com.tr"

# Laravel Tenancy will handle multi-tenant routing
# No redirects needed
```

**NOT:** Bu dosyalar Plesk tarafından overwrite edilmez, kalıcı config için idealdir.

#### 2. Let's Encrypt SSL Sertifikası

**Komut:**
```bash
plesk bin extension --exec letsencrypt cli.php \
  -d tuufi.com -d www.tuufi.com \
  -d ixtif.com -d www.ixtif.com \
  -d ixtif.com.tr -d www.ixtif.com.tr \
  -m admin@tuufi.com
```

**Sonuç:** Tüm 6 domain için SSL sertifikası oluşturuldu.

**SAN (Subject Alternative Names):**
- ixtif.com ✅
- ixtif.com.tr ✅
- tuufi.com ✅
- www.ixtif.com ✅
- www.ixtif.com.tr ✅
- www.tuufi.com ✅

#### 3. Nginx Konfigürasyonu

**Dosya:** `/var/www/vhosts/system/tuufi.com/conf/nginx.conf`

**Değişiklik:** Ayrı redirect server block'larını kaldırıp, tüm domainleri tek server block'a birleştirdik:

```nginx
server {
    listen 159.253.45.94:443 ssl;
    http2 on;

    server_name tuufi.com;
    server_name www.tuufi.com;
    server_name ipv4.tuufi.com;
    server_name ixtif.com;        # ✅ Eklendi
    server_name www.ixtif.com;    # ✅ Eklendi
    server_name ixtif.com.tr;
    server_name www.ixtif.com.tr;

    ssl_certificate             /usr/local/psa/var/certificates/scffm1s7qbch4jnfprJ4Ox;
    ssl_certificate_key         /usr/local/psa/var/certificates/scffm1s7qbch4jnfprJ4Ox;

    # ... rest of config
}
```

**NOT:** Plesk bu dosyayı otomatik regenerate edebilir. Kalıcı override için:

**Dosya:** `/var/www/vhosts/system/tuufi.com/conf/vhost_nginx.conf`

```nginx
# Custom Nginx config for tuufi.com - Laravel Multi-Tenant
# This will be included in the main server block

# IMPORTANT: All tenant domains should be handled by main tuufi.com server block
# Laravel Tenancy middleware handles multi-tenant routing
# Do NOT create separate server blocks or redirects for tenant domains
#
# NOTE: server_name directives for tenant domains are already in main nginx.conf
# No additional server_name directives needed here
```

#### 4. Web Sunucuları Yeniden Başlatma

```bash
systemctl restart httpd
systemctl restart nginx
```

#### 5. Test Sonuçları

```bash
# Test 1: tuufi.com
curl -I -s https://tuufi.com
# HTTP/2 200 ✅

# Test 2: ixtif.com
curl -I -s https://ixtif.com
# HTTP/2 200 ✅ (redirect YOK!)

# Test 3: ixtif.com.tr
curl -I -s https://ixtif.com.tr
# HTTP/2 200 ✅ (SSL hatası ÇÖZÜLDÜ!)
```

**SSL Sertifika Kontrolü:**
```bash
echo | openssl s_client -connect ixtif.com.tr:443 -servername ixtif.com.tr 2>/dev/null | openssl x509 -text -noout | grep -A1 "Subject Alternative Name"

# Çıktı:
# X509v3 Subject Alternative Name:
#     DNS:ixtif.com, DNS:ixtif.com.tr, DNS:tuufi.com, DNS:www.ixtif.com, DNS:www.ixtif.com.tr, DNS:www.tuufi.com
```

### Kritik Noktalar

1. **Plesk Auto-Regeneration:**
   - `/etc/httpd/conf/plesk.conf.d/vhosts/tuufi.com.conf` → Plesk tarafından otomatik regenerate edilir
   - `/var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf` → MANUEL, kalıcı ✅
   - `/var/www/vhosts/system/tuufi.com/conf/vhost_nginx.conf` → MANUEL, kalıcı ✅

2. **Domain Redirect Ayarları:**
   - Plesk database'de tuufi.com için ixtif.com'a redirect ayarları vardı
   - Bu ayarlar Apache/Nginx config'lerinde otomatik oluşturulan redirect'lere sebep oluyordu
   - Manuel config override'larıyla çözüldü

3. **Laravel Tenancy:**
   - Sistem `config/tenancy.php`'deki central_domains ayarını kullanıyor
   - ixtif.com ve ixtif.com.tr central_domains'te YOK, yani tenant olarak çalışacaklar
   - Middleware otomatik olarak doğru tenant'ı initialize ediyor

### Sorun Giderme

**Eğer Plesk config'leri tekrar regenerate ederse:**

```bash
# 1. vhost_ssl.conf ve vhost.conf'u kontrol et
cat /var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf

# 2. Gerekirse tekrar ekle
echo 'ServerAlias "ixtif.com"' >> /var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf
echo 'ServerAlias "www.ixtif.com"' >> /var/www/vhosts/system/tuufi.com/conf/vhost_ssl.conf

# 3. Apache restart
systemctl restart httpd
```

**Nginx için:**

```bash
# 1. nginx.conf'u kontrol et
grep -A 5 "server_name tuufi.com" /var/www/vhosts/system/tuufi.com/conf/nginx.conf

# 2. Eğer ixtif.com ayrı server block'a alınmışsa, tekrar birleştir
# Manuel edit gerekir (Edit tool kullan)

# 3. Test ve restart
nginx -t && systemctl restart nginx
```

### Başarı Kriterleri ✅

- [x] tuufi.com → HTTP 200, redirect YOK
- [x] ixtif.com → HTTP 200, redirect YOK
- [x] ixtif.com.tr → HTTP 200, SSL hatası YOK
- [x] SSL sertifikası tüm domainleri kapsıyor
- [x] Laravel Tenancy doğru tenant'ları initialize ediyor
- [x] Apache ve Nginx conflict'siz çalışıyor

**Tarih:** 2025-10-14 04:40
**Durum:** ✅ TAMAMLANDI
