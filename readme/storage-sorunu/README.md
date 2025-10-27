# 🚨 Storage Görselleri 403 Forbidden Sorunu

**Tarih:** 2025-10-24
**Etkilenen Tenant'lar:** ixtif.com (tenant2), tenant1, tenant3
**Durum:** ✅ GEÇİCİ ÇÖZÜM UYGULANMIŞ - KALıCı ÇÖZÜM BEKLİYOR

---

## 📋 SORUN ÖZETI

### ❌ Belirtiler
- ixtif.com sitesinde tüm görseller görünmüyor
- Tarayıcıda `403 Forbidden` hatası
- Görseller fiziksel olarak mevcut ama web'den erişilemiyor

### 🔍 Tespit Edilen Kök Sebep

**Apache Symlink İzin Sorunu**

```
Apache Error Log:
AH00037: Symbolic link not allowed or link target not accessible:
/var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2
```

#### Teknik Detaylar:

1. **Apache Config Problemi**
   - Dosya: `/var/www/vhosts/system/tuufi.com/conf/httpd.conf:129`
   - Mevcut ayar: `Options -FollowSymLinks` ❌
   - Gerekli: `Options +FollowSymLinks` ✅

2. **Symlink Sahiplik Uyumsuzluğu**
   ```bash
   # Symlink bilgisi
   lrwxrwxrwx root root tenant2 -> /storage/tenant2/app/public

   # Hedef dosya sahipliği
   -rw-r--r-- tuufi.com_ psaserv görsel.png
   ```

   `SymLinksIfOwnerMatch` direktifi çalışmıyor çünkü:
   - Symlink sahibi: `root`
   - Hedef dosya sahibi: `tuufi.com_`
   - **Sahipler uyuşmuyor!**

3. **Storage Yapısı**
   ```
   YAPILMASI GEREKEN:
   public/storage/tenant2 (symlink) -> storage/tenant2/app/public/

   APACHE İZİN VERMEDİ:
   - Options -FollowSymLinks aktif
   - Symlink farklı sahipte (root vs tuufi.com_)
   - Apache symlink'i reddetti → 403 Forbidden
   ```

---

## ✅ UYGULANAN GEÇİCİ ÇÖZÜM

### Yapılan İşlem
Symlink yerine **fiziksel klasör kopyalama**

```bash
# 1. Symlink'i sil
rm -rf /var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2

# 2. Fiziksel klasör oluştur
mkdir /var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2

# 3. Görselleri kopyala
cp -r /var/www/vhosts/tuufi.com/httpdocs/storage/tenant2/app/public/* \
     /var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2/

# 4. İzinleri düzelt
chown -R tuufi.com_:psaserv public/storage/tenant2
chmod -R 755 public/storage/tenant2
```

### ✅ Sonuç
- **HTTP 200 OK** - Görseller artık erişilebilir
- Tüm tenant'lar (tenant1, tenant2, tenant3) düzeltildi
- ixtif.com sitesi normal çalışıyor

---

## ⚠️ GEÇİCİ ÇÖZÜMÜN RİSKLERİ

### 1. **Depolama Alanı 2x Kullanım**
- Görseller hem `/storage/tenant2/app/public/` hem de `/public/storage/tenant2/` içinde
- **Disk kullanımı iki katına çıktı!**

### 2. **Yeni Upload'larda Sorun**
Laravel Media Library yeni görselleri şuraya kaydeder:
```
storage/tenant2/app/public/58/yeni-görsel.png
```

Ama web'den erişim için burası gerekli:
```
public/storage/tenant2/58/yeni-görsel.png
```

**Sonuç:** Yeni upload edilen görseller görünmeyecek!

### 3. **Thumbmaker Cache Sorunu**
Thumbmaker cache de symlink'e güveniyor:
```
storage/tenant2/app/public/thumbmaker-cache/
```

Fiziksel kopyada bu cache'ler eksik olabilir → performans kaybı

---

## 🛠️ KALıCı ÇÖZÜM SEÇENEKLERİ

### **✅ Seçenek 1: Apache Config Düzeltme (ÖNERİLEN)**

**Avantajları:**
- Kalıcı çözüm
- Disk alanı tasarrufu (symlink tekrar aktif)
- Yeni upload'lar otomatik çalışır
- Laravel'in tasarladığı mimariyle uyumlu

**İşlem:**

1. **httpd.conf düzelt**
   ```bash
   vim /var/www/vhosts/system/tuufi.com/conf/httpd.conf

   # Satır 129'da değiştir:
   - Options -FollowSymLinks
   + Options +FollowSymLinks
   ```

2. **Plesk repair ve reload**
   ```bash
   plesk repair web tuufi.com -y
   systemctl reload httpd
   ```

3. **Symlink'leri geri al**
   ```bash
   # Fiziksel klasörleri sil
   rm -rf public/storage/tenant1
   rm -rf public/storage/tenant2
   rm -rf public/storage/tenant3

   # Symlink'leri yeniden oluştur
   ln -s ../../storage/tenant1/app/public public/storage/tenant1
   ln -s ../../storage/tenant2/app/public public/storage/tenant2
   ln -s ../../storage/tenant3/app/public public/storage/tenant3

   # İzinleri düzelt
   chown -h tuufi.com_:psaserv public/storage/tenant*
   ```

4. **Test et**
   ```bash
   curl -I https://ixtif.com/storage/tenant2/19/0ufxpkujohzrh8nahnm9valr5jg8jgxoaqlwfzaj.png
   # Beklenen: HTTP/2 200
   ```

**Riskler:**
- ❌ Plesk update/upgrade sırasında config geri dönebilir
- ✅ Çözüm: `vhost.conf`'a yedek direktif ekle

---

### **⚠️ Seçenek 2: Symlink Sahipliğini Değiştir**

```bash
chown -h tuufi.com_:psaserv public/storage/tenant*
```

**Avantajları:**
- Hızlı
- `SymLinksIfOwnerMatch` çalışabilir

**Dezavantajları:**
- ❌ `Options -FollowSymLinks` hala aktif
- ❌ Plesk her repair'de root'a geri döndürebilir
- ❌ %100 çalışma garantisi yok

**VERDİKT:** Denemeye değmez, Seçenek 1 daha güvenli.

---

### **❌ Seçenek 3: .htaccess Override**

public/storage/ içine `.htaccess` ekle:
```apache
Options +FollowSymLinks
```

**Neden Çalışmaz:**
- httpd.conf'da `AllowOverride Options` kapalı olabilir
- Plesk'te genelde override'a izin yok
- Test edilmeden güvenilmez

**VERDİKT:** Zaman kaybı, direk Apache config düzelt.

---

## ✅ **ÇÖZÜLDÜ! (2025-10-24 05:20 UTC)**

### 🎉 UYGULANAN ÇÖZÜM: SYMLINK OWNERSHIP DEĞİŞİKLİĞİ

**Sorun:** Apache `SymLinksIfOwnerMatch` kullanıyor ama symlink'ler root:root sahipliğinde!

**Çözüm:**
```bash
chown -h tuufi.com_:psaserv /var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant*
```

**Sonuç:**
- ✅ **HTTP 200 OK** - Görseller erişilebilir!
- ✅ Symlink'ler korundu (disk alanı tasarrufu)
- ✅ Laravel tenant sistemi normal çalışıyor
- ✅ Yeni upload'lar otomatik çalışacak

---

## 📊 ŞU ANKİ DURUM

### ✅ Çalışan
- [x] ixtif.com görselleri görünüyor (HTTP 200)
- [x] Mevcut tüm görseller erişilebilir
- [x] tenant1, tenant2, tenant3 tümü düzeltildi
- [x] Symlink'ler aktif (disk tasarrufu)
- [x] Laravel Storage bootstrapper normal çalışıyor

### 🔧 Uygulanan Düzeltmeler
1. **Symlink Ownership:** root → tuufi.com_:psaserv
2. **vhost.conf:** LocationMatch eklendi (yedek çözüm)
3. **Dokümantasyon:** readme/storage-sorunu/ oluşturuldu

---

## 🔗 İLGİLİ DOSYALAR

- **Apache Config:** `/var/www/vhosts/system/tuufi.com/conf/httpd.conf:129`
- **Nginx Config:** `/var/www/vhosts/system/tuufi.com/conf/vhost_nginx.conf`
- **Storage Path:** `/var/www/vhosts/tuufi.com/httpdocs/storage/tenant2/app/public/`
- **Public Path:** `/var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2/`
- **Apache Error Log:** `/var/www/vhosts/system/tuufi.com/logs/error_log`

---

## 📚 KAYNAKLAR

### Apache Symlink Dokümantasyonu
- `FollowSymLinks` vs `SymLinksIfOwnerMatch`: https://httpd.apache.org/docs/2.4/mod/core.html#options
- AH00037 Error: https://wiki.apache.org/httpd/SymlinkNotAllowed

### Laravel Media Library
- Storage Disks: https://spatie.be/docs/laravel-medialibrary/v11/advanced-usage/using-a-custom-directory-structure
- Symlink Setup: https://laravel.com/docs/11.x/filesystem#the-public-disk

### Plesk KB
- Apache Directives: https://support.plesk.com/hc/en-us/articles/115000147154
- Vhost Management: https://docs.plesk.com/en-US/obsidian/administrator-guide/

---

**Son Güncelleme:** 2025-10-24 04:50 UTC
**Güncelleyen:** Claude Code
**Git Checkpoint:** `717bd3e0`
