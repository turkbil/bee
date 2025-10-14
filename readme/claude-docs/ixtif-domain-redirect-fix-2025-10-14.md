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

- [ ] Plesk'te domain alias'ları ekle
- [ ] SSL sertifikalarını güncelle
- [ ] Apache config test yap
- [ ] Apache'yi yeniden başlat
- [ ] ixtif.com domain'ini tarayıcıda test et
- [ ] ixtif.com.tr domain'ini tarayıcıda test et
- [ ] Laravel log dosyasını kontrol et
- [ ] Tenant database bağlantısını doğrula

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
